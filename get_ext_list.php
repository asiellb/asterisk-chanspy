<?php
/*
 в переменной $out['extensions']= 101,102,103,104
 
 используется для формирования запросов со списком абоентов
*/
require 'db_conn.php';
require 'func.php';
$out=array();
//select * from devices;
if ($result = $asteriskdb->query("SELECT * FROM devices ;")) {
    while   ($userdata = mysqli_fetch_assoc($result)) {
	 $out['extensions'].= $userdata['user'].',';
    }
    $extension_list=substr($out['extensions'],0,-1);
}

                                	    