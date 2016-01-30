<?php
/*
файл  отдает список звонков формируемый  get зпросом
*/
$debug=0;
require 'func.php';
require 'db_conn.php';
require 'get_ext_list.php';
error_reporting('ALL');
//print_r($_GET);

$out=array();
// определяем режим работы скрипта
// limit
if($_GET['limit']){
	$limit=$_GET['limit'];
}
else{
	//без разбивки по страницам
	if($_GET['export_xlsx'] or $_GET['export_csv'] or $_GET['stat'] ){
		$export=true;
	}else{
		$limit='0,25';
	}
}
//для формирования архива с записями
if($_GET['saverec'])$export=$saverec=true;

$where=" dcontext != 'from-sip-external'";
//echo count($_GET);
if(isset($_GET['startdate'])and isset($_GET['enddate'])){
    $where.=' AND (calldate between "'.$_GET['startdate'].' 00:00:00" and "'.$_GET['enddate'].' 23:59:59")';
}
if(isset($_GET['durtype'])and (isset($_GET['durtime']))){

	switch($_GET['durtype']){
		case 'mo':
		$durtype='>';
		break;
		case 'le':
		$durtype='<';
		break;
		case 'is':
		$durtype='=';
		break;


	}
	if($durtype=='='){
	    $min =$_GET['durtime']-59;
		if($_GET['durtime']=='zero'){
			$where.=' AND (billsec  ="0")';
		}else{
			$where.=' AND (billsec between '.$min.' and '.$_GET['durtime'].')';
		}
	}else{
		if($_GET['durtime']=='zero'){
			$where.=' AND (billsec '.$durtype.' "0")';
		}else{
			$where.=' AND (billsec '.$durtype.' '.$_GET['durtime'].')';
		}
	}
	// echo $where;
	 /*$log=fopen('query','a');
    fwrite($log,"\r\n");
    fwrite($log,date('d/m H:i:s'."   "));
    fwrite($log,$where);
    fclose($log);
	*/
}
if(isset($_GET['anstype'])){
	switch($_GET['anstype']){
		case 'ans':
		$anstype='ANSWERED';
		break;
		case 'noans':
		$anstype='NO ANSWER';
		break;
		case 'fail':
		$anstype='FAILED';
		break;
		case 'busy':
		$anstype='BUSY';
		break;
		case 'unk':
		$anstype='UNKNOWN';
		break;
	}
	$where.=' AND disposition = "'.$anstype.'"';
}


if(isset($_GET['exten'])){
    if(isset($_GET['incom']) and isset($_GET['outcom'])){
		$where.='AND (src = "'.$_GET['exten'].'" OR dstchannel like "SIP/'.$_GET['exten'].'%" OR dst="'.$_GET['exten'].'") ';
    }elseif(isset($_GET['incom'])){
		$where.='AND (dstchannel like "SIP/'.$_GET['exten'].'%" OR dst="'.$_GET['exten'].'") ';
    }elseif(isset($_GET['outcom'])){
		$where.='AND  (src = "'.$_GET['exten'].'") ';
    }else{
		$where.='AND (src = "'.$_GET['exten'].'" OR dstchannel like "SIP/'.$_GET['exten'].'%" OR dst="'.$_GET['exten'].'")  ';
    }
    
}else{
	if(isset($_GET['incom']) and isset($_GET['outcom'])){
	 //не добавляем ничего
    }elseif(isset($_GET['incom'])){
//	$where.='AND channel like "SIP/peer%" OR channel like "SIP/eng%" ';
		$where.='AND (channel REGEXP "SIP/[a-zA-Z]+" OR channel like "%from-queue%") ';
    }elseif(isset($_GET['outcom'])){
		$where.="AND src in ($extension_list)  ";
    }
	
	
}
if(isset($_GET['recyes']) and isset($_GET['recno'])){
	 //не добавляем ничего
    }elseif(isset($_GET['recyes'])){
	$where.='AND recordingfile >"" ';
    }elseif(isset($_GET['recno'])){
	$where.="AND recordingfile =''";
    }
    
//поля  поиска 
// искомое
if(isset($_GET['find'])){
    $having=' HAVING ';
    
    if(isset($_GET['FindByName'])){
	$fbn=true;
	$nums=explode(',',$_GET['FindByName']);
    }else{
	$fbn=false;
        $nums[]=$_GET['find'];
    }
    
    //$where.= ' AND ';
    foreach($nums as $find){
	if(!$find)continue;
	$res.=$find.',';
    }
    $find=substr($res,0,-1);
    //позиция в тексте
    if($fbn!=true){
	if(isset($_GET['findplace'])){
	    $place=" like '%$find%'";
		// начало строки
	    if($_GET['findplace']=='end'){
		$place=" like '%$find'";
    	    }
	    // конец строки
	    if($_GET['findplace']=='begin'){
		$place=" like '$find%'";
	    }
	    // в любом месте строки
	    if($_GET['findplace']=='incl'){
		$place=" like '%$find%'";
	    }
	    // точное совпадение
	    if($_GET['findplace']=='equal'){
		$place=" = '$find'";
	    }
	}
        else{
    	    $place=" like '%$find%'";
	}
    }
    else{
	$place=" in ($find)";
    }
    // столбец
    if(isset($_GET['findfield'])){
	//поиск по источнику
	if($_GET['findfield']=='src'){
	    $having.=" (src $place)";
	}
	//поиск по назначению
	if($_GET['findfield']=='dst'){
	    $having.=" (dst $place)";
	}
	//поиск по обоим полям
	if($_GET['findfield']=='both'){
	    $having.=" (src $place or dst $place)";
	}
	
    }
    else{
	$having.=" (src $place or dst $place)";
    }
    
    
    
  //  }
    
}

    
//echo "SELECT * FROM cdr WHERE $where   order by calldate desc limit $limit";
    $q="SELECT count(*)as kolvo FROM (SELECT * FROM cdr WHERE $where $having   order by calldate desc) as t1 ";
    if($debug){
    $log=fopen('./query','a');
    fwrite($log,"\r\n");
    fwrite($log,date('d/m H:i:s'."   "));
    fwrite($log,$q);
    fclose($log);
}
if ($result = $cdrdb->query($q)) {
    $ud=mysqli_fetch_assoc($result);
    $out['total']= $ud['kolvo'];
}


/*
флаг экспорт не делит вывод по страницам (не ставит limit в запросе)
*/
if($export){
	$q="SELECT * FROM cdr WHERE $where  $having order by calldate desc ";
	$result = $cdrdb->query($q);
}else{
	$q="SELECT * FROM cdr WHERE $where  $having  order by calldate desc limit $limit";
	$result = $cdrdb->query($q);
}

//  query logging
if($debug){
    $log=fopen('./query','a');
    fwrite($log,"\r\n");
    fwrite($log,date('d/m H:i:s'."   "));
    fwrite($log,$q);
    fclose($log);
}

if ($result) {
    
//    printf("Select вернул %d строк.\n", $result->num_rows);
                
    $i=0;
    while   ($userdata = mysqli_fetch_assoc($result)) : 
	    $out[$i]['calldate']= $userdata['calldate'];
	    $out[$i]['src']=$userdata['src'];
	    $out[$i]['dst']=$userdata['dst'];
	    $out[$i]['account']=$userdata['accountcode'];
	    $out[$i]['dcontexts']= $userdata['dcontext'];
	    $out[$i]['channel']= $userdata['channel'];
	    $out[$i]['dstchannel']= $userdata['dstchannel'];
	    $out[$i]['billsec']= $userdata['billsec'];
	    $out[$i]['disposition']=$userdata['disposition'];
	    $out[$i]['uniqueid']=$userdata['uniqueid'];
	    $out[$i]['recordingfile']=$userdata['recordingfile'];
	 
	
	    $i++;	     
#	   
    endwhile;
   
    $result->close();
	// удаляем  все строки которые попали в очередь
	foreach($out as $k=>$str){
				if(strstr($str['dstchannel'],'from-queue') ){
					$sub=substr($str['dstchannel'],0,-1);
					$one[$sub][]=$sub;
					$one[$sub][]=$str['account'];
					$one[$sub][]=$str['channel'];
				}
			}
			// выбираем все строки которые вышли из очереди, подставляем в них информацию из строк пападающих
	foreach($out as $k=>$cnl){
		if(strstr($cnl['channel'],'from-queue')){
			 $sub=substr($cnl['channel'],0,-1);
	   		if($sub==$one[$sub][0]){
				$out[$k]['account']=$one[$sub][1];
				$out[$k]['channel']=$one[$sub][2];
			}
		};
	}
	if($saverec){
		//перебираем все записи и получаем названия файлов
		$subdir=(int) microtime(TRUE);
		if(!mkdir('files/'.$subdir))echo 'failed to mkdir';
		//chmod('/tmp/'.$subdir, 0777);
		
		foreach($out as $str){
			//print_r($str);
			
			if(strlen($str['recordingfile'])>1){
				
				$dir='/var/spool/asterisk/monitor/';
				$ymd=explode(' ',$str['calldate']);
				$d['full']=explode('-',$ymd[0]);
				$d['y']=$d['full'][0]; //year
				$d['m']=$d['full'][1];//month
				$d['d']=$d['full'][2]; //day
				$fullpath=$dir.$d['y'].'/'.$d['m'].'/'.$d['d'].'/'.$str['recordingfile'];
				
				if(!copy($fullpath,'files/'.$subdir.'/'.$str['recordingfile'] )){
					//echo 'ERR copy '.$str['recordingfile'].'<br>';
				}
				else{
					$newfile='files/'.$subdir.'/'.$str['recordingfile'];
					if (file_exists($newfile)) {
						//echo $str['recordingfile'] ;
					}
					else{ 
						//echo 'ERR exist';
					};
		
				}	
			}
			
		}
		//архивируем файлы 
		exec("zip -j /var/www/html/cabinet/files/{$subdir}.zip /var/www/html/cabinet/files/{$subdir}/*");
		// если архив сущесвтует
	//	if (file_exists("files/{$subdir}.zip")) {
			//удаляем папку с записями
		exec("rm -fr /var/www/html/cabinet/files/{$subdir}");
		echo $subdir.'.zip';
		//}
		
	//	echo phpinfo();
	}else{
		print(json_encode($out));	
	}		
			
    
	     
}
$cdrdb->close();
?>