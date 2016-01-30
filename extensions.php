
<?php
//ini_set('display_errors',1);
//ini_set('display_startup_errors',1);
//error_reporting(-1);
if($_GET['type']=='get_w_reg'){
print(get_w_reg());
}
   
	function get_w_reg(){
		require 'func.php';
		require 'db_conn.php';
		$out=array();
		//select * from devices;
		if ($result = $asteriskdb->query("SELECT user,dial FROM devices ;")) {
			exec('asterisk -x "sip show peers"',$sipshowpeers);
			while   ($userdata = mysqli_fetch_assoc($result)) {
				//print_r($userdata);
				$u=$userdata['user'];
				$el=explode('/',$userdata['dial']);
				$c=count($el);
				$dial =($c>2 ? $el[2] : 'sip');
				$key=array_search((int)$u,$sipshowpeers);
				if($key){
					if(strstr($sipshowpeers[$key],'OK')) {
						$stat='OK';
					}else{
							$stat='UNKNOWN';
					}
				}else{
				$stat="NO";
				}
				$out[$u]= array($u,$dial,$stat);
				unset($u);
				unset($stat);
			}
			
			
		  return json_encode($out);
		}
	}



?>