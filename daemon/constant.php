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
        '0',
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
    $sql = "SELECT site_id,domain,path,port FROM site WHERE last_watch_time + period < $time";
    $result = $db->query($sql, 'array');
    $urls = array();
    foreach ($result as $key => $value) {
        $urls[$value['domain']] = array(
            'url' =>"http://{$value['domain']}{$value['path']}", 
            'port' => $value['port'],
            'site_id' =>$value['site_id']
        );
    }

    $count = rolling_curl($urls, $callback, false);

    echo "local {$count}\n";
    sleep(20);
}

//print_r($a);


?>