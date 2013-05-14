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