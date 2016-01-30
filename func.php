<?php
if (!function_exists('chk_func')) {
	 function chk_func($content) {
	 return true;
	 }
}

if (!function_exists('json_decode')) {
    function json_decode($content, $assoc=false) {
		require_once 'JSON.php';
		if ($assoc) {
			$json = new Services_JSON(SERVICES_JSON_LOOSE_TYPE);
		}
		else {
			$json = new Services_JSON;
		}
		return $json->decode($content);
    }
}
if (!function_exists('json_encode')) {
    function json_encode($content) {
        require_once 'JSON.php';
		$json = new Services_JSON;
	    return $json->encode($content);
	}
}
// конфиг астериска
if (!function_exists('amp')) {
	function amp(){
		$f=file('/etc/amportal.conf');
		foreach($f as $k=>$s){
			if(strlen($s)>1){
				if($s[0]!='#'){
					$vv=explode("=",$s);
					$amportal[$vv[0]]=trim($vv[1]);
				}
			}
		}
		return $amportal;
	}
}

if (!function_exists('get_param')) {
	function get_param($ext,$key='context'){
	    $file=parse_ini_file('/etc/asterisk/sip_additional.conf',true);
	    return $file[$ext][$key];
	}
}
?>