<?php
include 'func.php';
if (isset($_GET['a']) and isset($_GET['b']) and !isset($_GET['spy'])){
	$a=$_GET['a'];
	$b=$_GET['b'];
	include('phpagi/phpagi.php');
	$manager = new AGI_AsteriskManager();
	$manager->connect();
	//if(strlen($b)==6){
	//	$b='78452'.$b;
	//}
	$context=get_param($a);
	$manager->Originate(
		'SIP/'.$a,
		$b,
		$context,
		'1',
		'',
		'',
		'20000',
		'SIP/'.$b,
		'tTr',
		'',
		'Async',
		''
		);
	$manager->disconnect(); 
}
if(isset($_GET['spy']) and isset($_GET['a']) and isset($_GET['b']) and isset($_GET['type']) ){
	print_r ($_GET);
	$a= $_GET['a'];
	$b= $_GET['b'];
	echo $type= $_GET['type'];
	//$a= ����� ���������
	//$b= ����� ������� ����� �������
	include('phpagi/phpagi.php');
	$manager = new AGI_AsteriskManager();
	$manager->connect();
	//if(strlen($b)==6){
	//	$b='78452'.$b;
	//}
	//$context=get_param($a);
	/*
	$manager->Originate(
		'SIP/'.$a,
		'',
		'',
		'1',
		'ChanSpy',
		$b.',qx',
		'',
		'',
		'',
		'',
		'Async',
		''
		);
		*/
	$r= $manager->Originate ('SIP/'.$a, $a, 'from-internal','1', 'ChanSpy', $b.','.$type, '',  $b, '', '', 'Async','' );
	echo $r['Response'].' mode='.$type;
	$manager->disconnect(); 
	//echo 'ok';
	/*
	'����� ��� ������, �������� SIP/1001',
'�������� ��� ���������',
'�������� ���������',
'��������� ��������� ���������',
'��� ���������� ��������� ��� �������, �������� playback',
'��������� ����������, �������� ���� � ����������',
'�������',
'����� �������� �� �������� ���� ����� ��� ���',
'���������� ��� ���������',
'account - ������ �����, �� ����������� ���',
'���������� ��� ����������� ������ (���� ��� �� ���� ������ � ��������� �������)',
'actionid - ���� ���� �� �����������'
*/
}
?>