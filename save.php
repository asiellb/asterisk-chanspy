<?php
if ($_GET['cdr'] and $_GET['date'] and $_GET['fn']){
	$fn=$_GET['fn'];
	$dir='/var/spool/asterisk/monitor/';
	$d['full']=explode('-',$_GET['date']);
	$d['y']=$d['full'][0]; //year
	$d['m']=$d['full'][1];//month
	$d['d']=$d['full'][2]; //day
 	$fullpath=$dir.$d['y'].'/'.$d['m'].'/'.$d['d'].'/'.$fn;
	if(!copy($fullpath,'files/'.$fn )){
		echo 'ERR copy';
	}
	else{
		echo (file_exists('files/'.$fn)) ? $fn : 'ERR exist';
	}	
	//print_r($d);
	
}else{

	$dir='/var/lib/asterisk/sounds/custom/';
	if ($_GET['fn']){
		$fn=$_GET['fn'];
		if(!copy($dir.$fn,'files/'.$fn )){
				echo 'ERR copy';
			}else{
				if (file_exists('files/'.$fn)) {
					echo $fn;
				}else{
					echo 'ERR exist';
				}
		}	
	}
}
?>