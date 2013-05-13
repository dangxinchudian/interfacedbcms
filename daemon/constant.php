<?php

date_default_timezone_set('PRC');

require('../database.php');
$db = new database;

require('../common.php');       //common function
/*for(;;){

}*/
$callback = function($data, $info, $self){
    global $db;
    $database = "mosite_{$self['site_id']}";
    $time = date('Y-m-d H:i:s');
    if($self['node_id'] != 0){
        $info = json_decode($data, true);
        if(!$info) return false;
        $time = $info['time'];
    }
    $sql = "INSERT INTO {$database}.constant_log 
    (
        id, 
        starttransfer_time, 
        pretransfer_time, 
        total_time, 
        namelookup_time, 
        connect_time, 
        redirect_time, 
        status,
        constant_node_id, 
        time
    ) VALUES (
        uuid(), 
        '{$info['starttransfer_time']}', 
        '{$info['pretransfer_time']}',
        '{$info['total_time']}',
        '{$info['namelookup_time']}',
        '{$info['connect_time']}',
        '{$info['redirect_time']}',
        '{$info['http_code']}',
        '{$self['node_id']}',
        '{$time}'
    )";
    $db->query($sql, 'exec');
    $time = time();
    $sql = "UPDATE monitor.site SET last_watch_time = '{$time}' WHERE site.site_id = '{$self['site_id']}'";
    $db->query($sql, 'exec');
	//echo "{$self['site_id']} : {$info['url']}   {$info['total_time']}\n";
};

for(;;){
    $time = time();
    //node-list
    $sql = "SELECT constant_node_id,url,name FROM monitor.constant_node";
    $node = $db->query($sql, 'array');

    //url-list
    $sql = "SELECT site_id,domain,path,port FROM monitor.site WHERE last_watch_time + period < $time  AND remove = '0' AND constant_status = '1'";
    $result = $db->query($sql, 'array');

    $urls = array();
    foreach ($result as $key => $value) {
        $urls[$value['domain']] = array(
            'url' =>"http://{$value['domain']}{$value['path']}", 
            'port' => $value['port'],
            'site_id' =>$value['site_id'],
            'node_id' => 0
        );
    }
    $local = rolling_curl($urls, $callback, false);

    foreach ($node as $nodekey => $nodevalue) {
        $urls = array();
        foreach ($result as $key => $value) {
            $encode_url = urlencode("http://{$value['domain']}{$value['path']}");
            $urls[$value['domain']] = array(
                'url' =>"{$nodevalue['url']}?url={$encode_url}&port={$value['port']}", 
                'port' => 80,
                'site_id' => $value['site_id'],
                'node_id' => $nodevalue['constant_node_id']
            );
        }
        $node[$nodekey]['count'] = rolling_curl($urls, $callback, true);
    }

    //$count = rolling_curl($urls, $callback, false);
    $print = date('Y-m-d H:i:s')." 本地[{$local}]";
    foreach($node as $key => $value) $print .= " {$value['name']}[{$value['count']}]";

    echo "{$print}\n";
    sleep(20);
}

//print_r($a);


?>