<?php
require 'func.php';
require 'db_conn.php';
require 'conf.inc.php';
$dir='/var/lib/asterisk/sounds/custom';


if($_GET['act']=='show'){
	$cont=scandir($dir, 1);
	foreach($cont as $k =>$v){
		if($v=='..' or $v =='.'){
			unset($cont[$k]);
		}
	}	
	print(json_encode($cont));
}


if($_GET['act']=='get'){
	if($_GET['fnameA'] and $_GET['fnameB']) {
		$fnA=$_GET['fnameA'];
		$fnB=$_GET['fnameB'];
		//echo 'fname ok';
	}else {
	//	echo 'fname error';
	die;
	} ;
	
	
	 $content = file_get_contents("http://".$config['domain']."/files/".$fnA);
	// file_put_contents('/tmp/'.$fnA, $content);
	if(file_put_contents('/tmp/'.$fnA, $content)){
		/* backup file */
		if(!copy($dir.'/'.$fnB,$dir.'/'.$fnB.'.bak' )){
			echo 'ERR backup';
		}else{
		//echo 'OK';
			if(!copy('/tmp/'.$fnA,$dir.'/'.$fnB)){
				echo 'ERR copy';
			}else{
				 unlink('/tmp/'.$fnA);
			echo 'OK';
			}
		}
	}
}

?>