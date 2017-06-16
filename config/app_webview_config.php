<?php
require_once("dbconfig.php");
$path="gjj/backend/web/";
$ip=$GLOBALS['db']['server'];
$url="https://www.jajahome.com/";
$conn = mysqli_connect($GLOBALS['db']['server'], $GLOBALS['db']['username'],$GLOBALS['db']['password'],$GLOBALS['db']['database']);
mysqli_set_charset($conn,"utf8");
function verify($user_id,$user_token)
{
	global $conn;
	$user_id=intval($user_id);
	$user_token=addslashes($user_token);

	$res=mysqli_query($conn,"select count(*) from `sys_session` where uid=".$user_id." AND token='".$user_token."'");
	$row=mysqli_fetch_array($res);
	return $row[0]==1?true:false;
}
?>