<?php
include_once("connect.php");
$order = $_POST['order'];
//echo $order;
$itemid = trim($_POST['id']);
if (!empty ($itemid)) {
	if ($order != $itemid) {
		$query = mysql_query("update sortlist set sort='$itemid' where id=1");
		if ($query) {
			echo $itemid;
		} else {
			echo "none";
		}
	}
}
//$new_arr=explode(",",$itemid);
//$len=count($new_arr);
//for($i=0;$i<$len;$i++){
//	mysql_query("update sortlist set sort='$new_arr[$i]' where id=($i+1)");
//}
//print_r($len);
?>