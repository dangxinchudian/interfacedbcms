<?php

class snmpCatch {

	public $ip = false;
	public $community = 'public';
	public $version = 2;
	public $user = '';
	public $pass = '';

	public function os(){
		$result = $this->snmp('system.sysDescr.0', true);
		if($result === false) return false;
		//echo $result;
		if(stristr($result, 'linux')) return 'linux';
		if(stristr($result, 'unix')) return 'linux';
		if(stristr($result, 'windows')) return 'windows';
		return 'unkown';
	}

	public function sys_descr(){
		return $this->snmp('system.sysDescr.0', true);
	}

	public function sys_uptime(){
		return $this->snmp('system.sysUpTime.0', true);
	}

	public function sys_name(){
		return $this->snmp('system.sysName.0', true);
	}

	public function sys_time(){
		return $this->snmp('HOST-RESOURCES-MIB::hrSystemDate.0', true);
	}

	public function disk(){
		$disk = array();
		$result = $this->snmp('1.3.6.1.2.1.25.2');
		if(!$result) return false;
		foreach($result as $key => $value){
			if($label = strstr($key , 'hrStorageDescr')){
				$label = explode('.', $label);
				$label = $label[1];
				if(($name = strstr($value, '/')) || strstr($value, '\\')){
					if($name === false) $name = $this->format($value);
					if(($size = $this->format($result["HOST-RESOURCES-MIB::hrStorageSize.{$label}"])) != 0){
						$disk[] = array(
							//'name' =>  iconv('GB2312', 'UTF-8', $name),
							'name' =>  $name,
							'total' => $size,
							'used' => $this->format($result["HOST-RESOURCES-MIB::hrStorageUsed.{$label}"]) 
						);
					}
				}
			}
		}
		return $disk;
	}

	public function network(){
		$network = array();
		$result = $this->snmp('1.3.6.1.2.1.2.2.1');
		if(!$result) return false;
		foreach($result as $key => $value){
			if($label = strstr($key , 'ifIndex')){
				$index = (int)$this->format($value);
				$network[] = array(
					'descr' => $this->format($result["IF-MIB::ifDescr.{$index}"]),
					'type' => $this->format($result["IF-MIB::ifType.{$index}"]),
					'mtu' => $this->format($result["IF-MIB::ifMtu.{$index}"]),
					'speed' => $this->format($result["IF-MIB::ifSpeed.{$index}"]),
					'physAddress' => $this->format($result["IF-MIB::ifPhysAddress.{$index}"]),
					'adminStatus' => $this->format($result["IF-MIB::ifAdminStatus.{$index}"]),
					'operStatus' => $this->format($result["IF-MIB::ifOperStatus.{$index}"]),
					'inOctets' => $this->format($result["IF-MIB::ifInOctets.{$index}"]),
					'inUcastPkts' => $this->format($result["IF-MIB::ifInUcastPkts.{$index}"]),
					'inNUcastPkts' => $this->format($result["IF-MIB::ifInNUcastPkts.{$index}"]),
					'inErrors' => $this->format($result["IF-MIB::ifInErrors.{$index}"]),
					'inUnknownProtos' => $this->format($result["IF-MIB::ifInUnknownProtos.{$index}"]),
					'outOctets' => $this->format($result["IF-MIB::ifOutOctets.{$index}"]),
					'outUcastPkts' => $this->format($result["IF-MIB::ifOutUcastPkts.{$index}"]),
					'outNUcastPkts' => $this->format($result["IF-MIB::ifOutNUcastPkts.{$index}"]),
					'outErrors' => $this->format($result["IF-MIB::ifOutErrors.{$index}"]),
					'outQLen' => $this->format($result["IF-MIB::ifOutQLen.{$index}"]),
				);
			}
		}
		return $network;	
	}

	public function cpu(){
		$cpu = array();
		$result = $this->snmp('1.3.6.1.2.1.25.3.3.1.2');
		if(!$result) return false;
		foreach($result as $value){
			$cpu[] = $this->format($value);
		}
		return $cpu;	
	}

	public function process(){
		$run = array();
		$result = $this->snmp('1.3.6.1.2.1.25.4');
		if(!$result) return false;
		$performance = $this->snmp('1.3.6.1.2.1.25.5');
		if(!$performance) return false;
		if(isset($result['HOST-RESOURCES-MIB::hrSWOSIndex.0'])) unset($result['HOST-RESOURCES-MIB::hrSWOSIndex.0']);
		foreach($result as $key => $value){
			if(!strstr($key, 'hrSWRunIndex')) break;
			$id = $this->format($value);
			$run[] = array(
				'name' => $this->format($result["HOST-RESOURCES-MIB::hrSWRunName.{$id}"]),
				'path' => $this->format($result["HOST-RESOURCES-MIB::hrSWRunPath.{$id}"]),
				'parameter' => $this->format($result["HOST-RESOURCES-MIB::hrSWRunParameters.{$id}"]),
				'type' => $this->format($result["HOST-RESOURCES-MIB::hrSWRunType.{$id}"]),
				'status' => $this->format($result["HOST-RESOURCES-MIB::hrSWRunStatus.{$id}"]),
				'cpu' => $this->format($performance["HOST-RESOURCES-MIB::hrSWRunPerfCPU.{$id}"]),
				'memory' => $this->format($performance["HOST-RESOURCES-MIB::hrSWRunPerfMem.{$id}"])
			);
		}
		return $run;
	}

	public function memory_total(){
		return array_shift($this->snmp('1.3.6.1.2.1.25.2.2.0'));
	}

	private function snmp($value, $format = false){
		if($format) return @$this->format(snmprealwalk($this->ip, $this->community, $value));
		return @snmprealwalk($this->ip, $this->community, $value);
	}

	private function format($result){
		if(!$result) return false;
		if(is_array($result)) $result = array_shift($result);
		$result = str_replace(array('Timeticks: ', 'STRING: ','INTEGER: ','Counter32: ','Gauge32: '),'', $result);
		$result = preg_replace('/^"(.*)"$/', '$1', $result);
		return $result;
	}

}

?>