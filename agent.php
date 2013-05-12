<?php

/*用于派发至监测点*/

function httpHeader($url, $port = 80){

	$curl = curl_init();
	$timeOut = 30;
	$userAgent = 'Mozilla/5.0 (Windows; U; Windows NT 6.1; en-US; rv:1.9.1.2) Gecko/20090729 Firefox/3.5.2 GTB5';

	curl_setopt($curl, CURLOPT_URL, $url);
	curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
	curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
	curl_setopt($curl, CURLOPT_PORT, $port);
	curl_setopt($curl, CURLOPT_TIMEOUT, $timeOut);
	curl_setopt($curl, CURLOPT_USERAGENT, $userAgent);
	//curl_setopt($curl, CURLOPT_NOBODY, true);

	$a = curl_exec($curl);
	//echo $a;

	$curlinfo = curl_getinfo($curl);
	//print_r($curlinfo);

	$hc = array();
	# Informational 1xx
	$hc['0'] = 'Unable to access'; 
	$hc['100'] = 'Continue'; 
	$hc['101'] = 'Switching Protocols';
	# Successful 2xx 
	$hc['200'] = 'OK';
	$hc['201'] = 'Created';
	$hc['202'] = 'Accepted';
	$hc['203'] = 'Non-Authoritative Information';
	$hc['204'] = 'No Content';
	$hc['205'] = 'Reset Content';
	$hc['206'] = 'Partial Content';
	# Redirection 3xx 
	$hc['300'] = 'Multiple Choices';
	$hc['301'] = 'Moved Permanently';
	$hc['302'] = 'Moved Temporarily';
	$hc['303'] = 'See Other';
	$hc['304'] = 'Not Modified';
	$hc['305'] = 'Use Proxy';
	$hc['306'] = '(Unused)';
	$hc['307'] = 'Temporary Redirect';
	# Client Error 4xx 
	$hc['400'] = 'Bad Request';
	$hc['401'] = 'Unauthorized';
	$hc['402'] = 'Payment Required';
	$hc['403'] = 'Forbidden';
	$hc['404'] = 'Not Found';
	$hc['405'] = 'Method Not Allowed';
	$hc['406'] = 'Not Acceptable';
	$hc['407'] = 'Proxy Authentication Required';
	$hc['408'] = 'Request Timeout';
	$hc['409'] = 'Conflict';
	$hc['410'] = 'Gone';
	$hc['411'] = 'Length Required';
	$hc['412'] = 'Precondition Failed';
	$hc['413'] = 'Request Entity Too Large';
	$hc['414'] = 'Request-URI Too Long';
	$hc['415'] = 'Unsupported Media Type';
	$hc['416'] = 'Requested Range Not Satisfiable';
	$hc['417'] = 'Expectation Failed';
	# Server Error 5xx
	$hc['500'] = 'Internal Server Error';
	$hc['501'] = 'Not Implemented';
	$hc['502'] = 'Bad Gateway';
	$hc['503'] = 'Service Unavailable';
	$hc['504'] = 'Gateway Timeout';
	$hc['505'] = 'HTTP Version Not Supported';

	$result = array(
		'code' => $curlinfo['http_code'],
		'status' => $hc[$curlinfo['http_code']],
		'time' => $curlinfo['total_time']
	);
	return $result;
}

$result = httpHeader($_GET['url'], $_GET['port']);

echo json_encode($result);

?>