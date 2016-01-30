<?php
/* 
ВЫводим список регистраций или 1 логин с 5.17 для запроса баланса
*/
//если нет параметра завершаем работу скрипта
 error_reporting(E_ALL & ~(E_STRICT|E_NOTICE));
 //echo 0;
if(!$_GET['type']) exit;
//echo 1;
if($_GET['type']=='one') one();
//echo 2;
if($_GET['type']=='all')  all();
//echo 3;
exit;

function one(){
//	echo 'one';
	exec('asterisk -x "sip show registry" | grep 91.196.5.17',&$result);
	$newarr=array();
	foreach( $result as $str ){
		//echo '<pre>'.$str.'</pre>';
		if(strstr($str,'91.196.5.17')){
		   
			$elem=explode(" ",$str);
			//print_r($elem);
				foreach($elem as $word){
					if($word) $newarr[]=$word;
					
				}
		    break;
		}
		
	}
	echo  $newarr[2];
}
function all(){
//	echo 'all';
	$result = array();
	exec('asterisk -x "sip show registry" ',&$result);
	foreach( $result as $str ){
		if(strstr($str,'91.196.5.17'))echo $str.'</br>';
	}
	
}