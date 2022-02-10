<?php

include_once('./_common.php');
include_once('./bonus_inc.php');
include_once(G5_PATH.'/util/recommend.php');

$all_member_result = sql_query("SELECT * from g5_member WHERE mb_level > 0 AND mb_level < 9 ");

while($row = sql_fetch_array($all_member_result)){
    $mb_id = $row['mb_id'];

    $recommed_list = return_down_tree($mb_id,$cnt=0);
    $recommed_pv_total = array_int_sum($recommed_list, 'mb_pv');
    
    if($recommed_pv_total >0){
        $update_recom = "UPDATE g5_member set recom_sales = {$recommed_pv_total} WHERE mb_id  = '{$mb_id}' ";
    }
    $mem_list = [];
};

/*추천 하부라인 */
function return_down_tree($mb_id,$cnt=0){
    global $config,$g5,$mem_list;

    $mb_result = sql_fetch("SELECT mb_id,mb_level,grade,mb_rate,mb_save_point,rank,recom_sales,mb_pv from g5_member WHERE mb_id = '{$mb_id}' ");
    $result = recommend_downtrees($mb_result['mb_id'],0,$cnt);
    return $result;
}


function recommend_downtrees($mb_id,$count=0,$cnt = 0){
    global $mem_list;

    if($cnt == 0 || ($cnt !=0 && $count < $cnt)){
        
        $recommend_tree_result = sql_query("SELECT mb_id,mb_level,grade,mb_rate,mb_save_point,rank,recom_sales,mb_pv from g5_member WHERE mb_recommend = '{$mb_id}' ");
        $recommend_tree_cnt = sql_num_rows($recommend_tree_result);

        if($recommend_tree_cnt > 0 ){
            ++$count;
            while($row = sql_fetch_array($recommend_tree_result)){
                
                array_push($mem_list,$row);
                recommend_downtrees($row['mb_id'],$count,$cnt);
            }
        }
    }
    return $mem_list;
}