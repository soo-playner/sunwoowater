<?php
include_once('./_common.php');

include_once(G5_THEME_PATH.'/_include/wallet.php');
include_once('./bonus_inc.php');


for($i = 0 ; $i < count($_POST['chk']); $i ++){
    $k = $_POST['chk'][$i];
    $idx = $_POST['no'][$k];

    $mb = $_POST['mb_id'][$k];
    
    $center = $_POST['center'][$k];
    $jijum = $_POST['jijum'][$k];
    $jisa = $_POST['jisa'][$k];
    $bonbu = $_POST['bonbu'][$k];

    $update_extra = "UPDATE soodang_extra set 
    center = '{$center}',
    jijum = '{$jijum}',
    jisa = '{$jisa}',
    bonbu = '{$bonbu}',
    datetime = '{$now_datetime}'
    WHERE no = {$idx} ";
    // print_R($update_extra);
    $result1 = sql_query($update_extra);

    if($result1){
        $member_update_sql = "UPDATE g5_member set 
        mb_center = '{$center}',
        mb_jijum = '{$jijum}',
        mb_jisa = '{$jisa}',
        mb_bonbu = '{$bonbu}'
        WHERE mb_id = '{$mb}'
        ";
        // print_R($member_update_sql);

        $result2 = sql_query($member_update_sql);
    }

    if($result1 && $result2){
        alert('변경되었습니다.');
        goto_url('./bonus.level_org.php'.$_SERVER["QUERY_STRING"]);
    }
} 

?>