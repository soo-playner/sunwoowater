<?php
include_once('./_common.php');
include_once(G5_THEME_PATH.'/_include/wallet.php');
include_once(G5_PATH.'/util/recommend.php');

$mb_id = $_REQUEST['mb_id'];
$category = $_REQUEST['category'];
$category_value = $_REQUEST['category_value'];

$search_member = return_org_member($mb_id,$category_value);

if($search_member != ''){
    echo (json_encode(array("result" => "success",  "code" => "0000", "data" =>$search_member),JSON_UNESCAPED_UNICODE));
}else{
    echo (json_encode(array("result" => "failed",  "code" => "0001", "data" =>'해당회원이 없거나 찾을 수없습니다.'),JSON_UNESCAPED_UNICODE));
}

?>