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
        $remote = json_decode($data, true);
        if(!$remote) return false;
        $time = $remote['time'];
        $info = array_merge($info, $remote);
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

    if($self['node_id'] != 0){
        $sql = "UPDATE monitor.site SET last_watch_time = '{$time}' WHERE site.site_id = '{$self['site_id']}'"; 
    }else{
        //距离上次访问时间超过2倍的间隔时间就算中间停止过，重新计算，否则进行粘滞计算(time()-last_watch_time)
        $middle_time = $time - $self['last_watch_time'];
        if($middle_time > 2 * $self['period']) $middle_time = 0;

        $sql = "UPDATE monitor.site SET last_watch_time = '{$time}',keep_watch_time = keep_watch_time + {$middle_time} WHERE site.site_id = '{$self['site_id']}'";
    }
    $db->query($sql, 'exec');
	//echo "{$self['site_id']} : {$info['url']}   {$info['total_time']}\n";


    if($self['node_id'] == 0){
        // ----------------------------------------------------------
        // -----------------------fault-----------------------------
        // ----------------------------------------------------------

        //检查未闭合的故障
        $sql = "SELECT * FROM constant_fault WHERE site_id = '{$self['site_id']}' AND status = 'unslove' ORDER BY time DESC";
        $result = $db->query($sql, 'array');
        $fault = array();
        if(!empty($result)) $fault = array_shift($result);

        //正常情况下最多只会有一个unslove
        if(count($result) > 0){     //如果出现多个。除了第一个其他全部闭合
            $fault_array = array();
            foreach ($result as $key => $value) $fault_array[] = $value['id'];
            $fault_where = implode(',', $fault_array);
            $updateArray = array('status' => 'slove');
            $db->update('constant_fault', $updateArray, "id in ({$fault_where})");
            echo '闭合修复\n';
        }

        //$fault_id
        if($info['http_code'] != 200){      //开启故障，持续故障
            if(empty($fault)){      //开启故障
                $insertArray = array(
                    'time' => date('Y-m-d H:i:s'),
                    'keep_time' => $self['period'],
                    'user_id' => $self['user_id'],
                    'site_id' => $self['site_id'],
                    'http_code' => $info['http_code']
                );
                $result = $db->insert('constant_fault', $insertArray);
            }else{      //持续故障时间累加
                $sql = "UPDATE constant_fault SET keep_time = keep_time + {$self['period']} WHERE id = '{$fault['id']}'";
                $db->query($sql, 'exec');
            }
        }else{   //闭合故障
            if(!empty($fault)){
                $updateArray = array('status' => 'slove');
                $db->update('constant_fault', $updateArray, "id = '{$fault['id']}'");
            }
        }

        // ----------------------------------------------------------
        // -----------------------alarm-----------------------------
        // ----------------------------------------------------------

    }
};

function alarm($site_id, $http_code){
    
}

for(;;){
    $time = time();
    //node-list
    $sql = "SELECT constant_node_id,url,name FROM monitor.constant_node";
    $node = $db->query($sql, 'array');

    //url-list
    $sql = "SELECT user_id,site_id,domain,path,port,last_watch_time,period FROM monitor.site WHERE last_watch_time + period < $time  AND remove = '0' AND constant_status = '1'";
    $result = $db->query($sql, 'array');

    $urls = array();
    foreach ($result as $key => $value) {
        $urls[$value['domain']] = array(
            'url' =>"http://{$value['domain']}{$value['path']}", 
            'port' => $value['port'],
            'site_id' => $value['site_id'],
            'node_id' => 0,
            'last_watch_time' => $value['last_watch_time'],
            'period' => $value['period'],
            'user_id' => $value['user_id']
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
                'node_id' => $nodevalue['constant_node_id'],
                'last_watch_time' => $value['last_watch_time'],
                'period' => $value['period'],
                'user_id' => $value['user_id']
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