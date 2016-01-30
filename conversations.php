<?php
require 'func.php';
$act=$_GET['act'];
if($act=='get'){
	//получаем список каналов
	exec('asterisk -x "core show channels"',$c);

	// разбиваем, убираем пустые значения
	foreach($c as $k=>$call){
		$ca=explode(' ',$call);
		foreach($ca as $str){
			if($str) $flt[$k][]=$str;
		}
	}
	// перебираем каналы, пропускаем неинтересные
	foreach($flt as $k=>$v){
		
		// шапка таблицы
		if($k==0)continue;
		
		// если нет 2 плеча звонка
		if($v[1]=='(None)')continue;
		
		// последние стоки со статистикой
		if($v[1]=='active' or  $v[1]=='calls')continue;
		
		//берем канал оригинатор
		$out[$k]['channel']=$v[0];
		
		//получаем по данному каналу поробную информацию
		exec('asterisk -x "core show channel '.$v[0].'"',$podr);
		
		// берем из результата интересующие значения, заодно убираем имена и пробелы
		$out[$k]['cid']=trim(str_replace('Caller ID:','',$podr[5]));
		
		$out[$k]['clid']=trim(str_replace('Connected Line ID:','',$podr[7]));
		
		//$out[$k]['did']=trim(str_replace('DNID Digits:','',$podr[11]));
		
		$out[$k]['state']=trim(str_replace('State:','',$podr[13]));
		
		$out[$k]['time']=trim(str_replace('Elapsed Time:','',$podr[24]));
		
		$out[$k]['app']=trim(str_replace('Application:','',$podr[33]));
		
		unset($podr);
	}
	// конвертим массив в json и выводим
	echo json_encode($out);
}elseif($act=='drop'){
	$c=$_GET['ch'];
	if($c){
		//echo 
		exec('asterisk -x "channel request hangup '.$c.' "',$res);
		echo $res[0];
	}
}
?>