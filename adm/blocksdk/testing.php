<?php
header("Content-Type:application/json");

$sub_menu = '100610';
include_once('./_common.php');

include_once(G5_LIB_PATH.'/blocksdk.lib.php');
include_once(G5_LIB_PATH.'/crypto.lib.php');


auth_check($auth[$sub_menu], "w");

	
$blocksdk_token = 'BsokFsOPhUSbtMVnmgTJYSNyP9VdrkoynMEb7ag9';
$blocksdk_conf  = Crypto::GetConfig();

$blockSDK   = new BlockSDK($blocksdk_token);
print_R(Crypto::Encrypt($blocksdk_token));

$sql = "
	UPDATE 
	blocksdk_conf SET 
	blocksdk_token = '" . Crypto::Encrypt($blocksdk_token) . "'
";

// sql_query($sql);
	
?>
