<?php
require 'func.php';
//print_r($_POST);
if ($_GET['mode']=='get'){
	$r=db_manager('get');
}
elseif($_POST['mode']=='put'){
	$exten=$_POST['exten'];
	if ($_POST['cf'] ) {
		$r['cf']=db_manager('put',$exten,'CF',$_POST['cf']);
	}else{
		$r['cf']=db_manager('del',$exten,'CF');
	} 
	if ($_POST['cfu'] ) {
		$r['cfu']=db_manager('put',$exten,'CFU',$_POST['cfu']);
	}else{
		$r['cfu']=db_manager('del',$exten,'CFU');
	} 
	if ($_POST['cfb'] ){
		$r['cfb']=db_manager('put',$exten,'CFB',$_POST['cfb']);
	}else{
		$r['cfb']=db_manager('del',$exten,'CFB');
	}
	if ($_POST['cw']) {
		$r['cw']=db_manager('put',$exten,'CW','ENABLED');
	}else{
		$r['cw']=db_manager('del',$exten,'CW');
	}
	if ($_POST['dnd']) {
		$r['dnd']=db_manager('put',$exten,'DND','YES');
	}else{
		$r['dnd']=db_manager('del',$exten,'DND');
	}
 }elseif($_POST['mode']=='custom'){
	$exten=$_POST['exten'];
	$r['custom']=db_manager('custom',$exten);
 }
echo ($r) ? json_encode($r) : 'no';

function db_manager($type,$exten=false, $fwd=false, $no=false){
	if($type=='put'){
		exec('asterisk -x "database put '.$fwd.' '.$exten.' '.$no.' " ',$r);
	}elseif($type=='del'){
		exec('asterisk -x "database del '.$fwd.' '.$exten.' " ',$r);
	}elseif($type=='get'){
		exec('asterisk -x "database show" | egrep "(/CF.?/)|CW|DND" ',$r);
	}elseif($type=='custom') {
		//echo 'asterisk -x "database show" | egrep "DEVICE/'.$exten.'/dial';
		exec('asterisk -x "database show" | egrep "DEVICE/'.$exten.'/dial"',$n);
		if($n){
			$parts=explode(' : ',$n[0]);
			$val=explode('/',$parts[1]);
			$c=count($val);
			unset($val[$c-1]); 
			foreach($val as $el){
			$newval.=$el.'/';
			}
		    $newval.=$_POST['dial'];
			//$newval;
			exec('asterisk -x "database put DEVICE '.$exten.'/dial '.$newval.' " ',$r);
			require 'db_conn.php';
			echo $asteriskdb->query("UPDATE devices SET dial='{$newval}' WHERE id='{$exten}' ;");
		}
		
	}
	return $r;
}
?>