<?php 
include 'func.php';
require 'db_conn.php';
$num = isset ( $_GET['num'] ) ? $_GET['num'] : '';
$ext   = isset ( $_GET['ext'] )   ? $_GET['ext'] : '';

if ( $num  and  $ext ){
$row = $cdrdb->query("SELECT id FROM directcalls WHERE num=${num}");
    if ( mysqli_fetch_assoc($row) ) {
	$query = "UPDATE directcalls SET `num` = ${num},`ext` = ${ext} WHERE num=${num}";
    }
    else {
	$query = "INSERT IGNORE INTO `directcalls` SET `num` = ${num},`ext` = ${ext}";
    }	
}
elseif ( $num and !$ext ){
    $query="DELETE FROM `directcalls` WHERE `num` = ${num};";
}
if ( isset( $query )) $cdrdb->query( $query ) ;
$cdrdb->close();
?>