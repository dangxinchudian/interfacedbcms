<?php

$watch_id = $matches[1];

$serverModel = model('server');
$watch = $serverModel->selectWatch($watch_id);
if(empty($watch)) json(false, '监控不存在');
if($watch['remove'] > 0) json(false, '监控已经被移除');
$server = $serverModel->get($watch['server_id']);
if(empty($server)) json(false, 'server不存在');
if($server['remove'] > 0) json(false, 'server已经被移除');

//判断间隔
if($watch['last_watch_time'] + $server['period'] > time()) json(false, '间隔时间过短');

$db = $serverModel->db();
$snmp = model('snmpCatch');
$snmp->ip = $server['ip'];
if($server['snmp_version'] == 2) $snmp->community = jdecode($server['snmp_token']);
else json(false, 'can\'t support v3');

//device 
$item = $serverModel->item($watch['server_item_id']);
$table = "moserver_{$watch['server_id']}.{$item['table_name']}";
$device = $serverModel->getDevice($item['server_hardware_id'], 'hardware_id');
$deviceList = array();
foreach ($device as $key => $value) {
	$deviceList[$value['hash']] = $value;
}
switch ($item['table_name']) {
	case 'disk_log':
		$result = $snmp->disk();
		if(!$result) json(false, 'snmp error!');
		$snmpDevice = array();
		foreach ($result as $key => $value) {
			$snmpDevice[$serverModel->device_hash($watch['server_id'], $item['server_hardware_id'], $value['name'])] = $value;
		}
		$snmpDevice = array_intersect_key($snmpDevice, $deviceList);
		foreach ($snmpDevice as $key => $value) $snmpDevice[$key]['device_id'] = $deviceList[$key]['server_device_id'];
		$sql = "INSERT INTO {$table} (id, used_amount, total_amount, device_id, time) VALUES ";
		$sqlArray = array();
		$time = date('Y-m-d H:i:s');
		foreach ($snmpDevice as $key => $value) {
			$sqlArray[] = "(uuid(), '{$value['used']}', '{$value['total']}', '{$value['device_id']}', '{$time}')";
		}
		$sql .= implode(',', $sqlArray);

		break;

	case 'network_log':
		# code...
		break;

	case 'cpu_log':
		# code...
		break;

	case 'memory_log':
		# code...
		break;

	case 'processcount_log':
		break;

	default:
		json(false,'undefined mode!');
		break;
}

$db->query($sql, 'exec');
$update = array('last_watch_time' => time());
$db->update('server_watch', $update, "server_watch_id = '{$watch_id}'");
json(true, 'snmp catch finish!');

?>