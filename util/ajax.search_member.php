<?php
include_once('./_common.php');
include_once(G5_THEME_PATH.'/_include/wallet.php');
include_once(G5_PATH.'/util/recommend.php');

$mb_id = $_REQUEST['mb_id'];
$search_member = trim($_REQUEST['search_member']);

$search_sql = "SELECT mb_id,mb_nick,mb_name,mb_level FROM g5_member WHERE ( mb_id like '%{$search_member}%' or mb_nick like '%{$search_member}%') and mb_level > 0 AND mb_id != '{$mb_id}' order by mb_id ";

$search_result = sql_query($search_sql);
$member_list = [];

while($row = sql_fetch_array($search_result)){
   
    array_push($member_list,$row);
}

if(count($member_list) >0){
    echo (json_encode(array("result" => "success",  "code" => "0000", "data" =>$member_list),JSON_UNESCAPED_UNICODE));
}else{
    echo (json_encode(array("result" => "failed",  "code" => "0001", "data" =>'해당회원이 없거나 찾을 수없습니다.'),JSON_UNESCAPED_UNICODE));
}

?>