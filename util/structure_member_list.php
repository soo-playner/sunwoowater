<?php
include_once('./_common.php');
include_once(G5_THEME_PATH.'/_include/wallet.php');
include_once(G5_PATH.'/util/recommend.php');

$query = "select mb_id,mb_level from g5_member where mb_id = '{$_REQUEST['mb_no']}'";
$srow = sql_fetch($query);
$mb_id = $srow['mb_id'];


if($srow['mb_level'] < 2){
	$depth_limit = 2;
}else{
	$depth_limit = 2;
}

$result  = return_down_manager($mb_id,$depth_limit);
echo json_encode($result);
?>