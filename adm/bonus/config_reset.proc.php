<?php
include_once('./_common.php');
// include_once('./bonus_inc.php');
include_once('../../util/purchase_proc.php');

$today = date("Y-m-d H:i:s",time());
$todate = date("Y-m-d",time());
// $debug=1;
// $_POST['nw_member_reset'] = 'on';

if($_GET['debug']) $debug = 1;

if($_POST['nw_soodang_reset'] == 'on'){
    $trunc1 = sql_query(" TRUNCATE TABLE `soodang_pay` ");
    $trunc16 = sql_query(" TRUNCATE TABLE `soodang_extra` ");

    $member_update_sql = " UPDATE g5_member set  mb_balance = 0 WHERE mb_level < 9 ";
    sql_query($member_update_sql);
    

    if($trunc16){
        $result = 1;
    }
}

if($_POST['nw_member_reset'] == 'on'){

    $trunc5 = sql_query(" TRUNCATE TABLE `g5_shop_order` ");
    $trunc6 = sql_query(" TRUNCATE TABLE `package_log`; ");
    
    $pack_cnt = sql_fetch("SELECT count(it_id) as cnt from g5_shop_item WHERE it_use > 0")['cnt'];
    $pack_name_sql = sql_fetch("SELECT it_maker from g5_shop_item WHERE it_use > 0 limit 0,1 ")['it_maker'];
    $pack_name = strtolower(substr($pack_name_sql,0,1));
    
    for($i=0;$i<=$pack_cnt;$i++){
        $pack_where = "package_".$pack_name.$i;
        $empty = " TRUNCATE TABLE {$pack_where};";
        
        sql_query($empty);
    }
    $trunc15 = sql_query(" TRUNCATE TABLE `rank` ");

    $member_update_sql = " UPDATE g5_member set  
        mb_deposit_point = 0, 
        mb_deposit_calc=0, 
        mb_save_point=0, 
        mb_shift_amt =0, 
        mb_rate=0,
        mb_pv = 0,
        cash_point=0, 
        sales_day='0000-00-00', rank_note='',rank='',
        mb_1 ='',mb_2='',mb_3='',mb_4 ='',mb_5='',mb_6='',mb_7='',mb_8='',
        recom_sales= 0 
        WHERE mb_level < 9 ";
    sql_query($member_update_sql);

    /* $sql_member_reset2 = " UPDATE g5_member set grade = 0, mb_level = 0 WHERE mb_no > 1 ";
    sql_query($sql_member_reset2); */
    
    if($member_update_sql){
        $result = 1;
    }
}

if($_POST['nw_asset_reset'] == 'on'){

    $trunc2 = sql_query(" TRUNCATE TABLE `{$g5['withdrawal']}` ");
    $trunc3 = sql_query(" TRUNCATE TABLE `{$g5['deposit']}` ");

    if($trunc3){
        $result = 1;
    }
}

if($_POST['nw_mining_reset'] == 'on'){

    /* $trunc2 = sql_query(" TRUNCATE TABLE `{$g5['mining']}` ");
    $update_member = sql_query("update g5_member set {$mining_target} = 0, {$mining_amt_target} = 0 WHERE mb_no > 1 ");

    if($update_member){
        $result = 1;
    } */

    $sql_member_reset2 = " UPDATE g5_member set grade = 0, mb_level = 0,center_use =0 WHERE mb_level < 8 ";
    $result = sql_query($sql_member_reset2);
}

if($_POST['nw_brecommend_reset'] == 'on'){
    $trunc2 = sql_query(" TRUNCATE TABLE `g5_member_bclass` ");
    $trunc2 = sql_query(" TRUNCATE TABLE `g5_member_bclass_chk` ");

    $trunc2 = sql_query(" TRUNCATE TABLE `g5_member_class` ");
    $trunc2 = sql_query(" TRUNCATE TABLE `g5_member_class_chk` ");

    // $result = sql_query("UPDATE g5_member SET mb_memo ='', mb_bre_time ='', mb_brecommend ='', mb_brecommend_type ='', mb_lr = 3,mb_child=0, mb_b_child=0 WHERE mb_no > 1");
    $result = sql_query("UPDATE g5_member SET mb_center='', mb_jijum ='', mb_jisa ='', mb_bonbu ='', mb_child=0, mb_b_child=0 WHERE mb_no > 1");
}

if($_POST['nw_data_test'] == 'on'){
    
    $mb_deposit_point = 50000000;
    $member_update_sql = " UPDATE g5_member set mb_deposit_point = {$mb_deposit_point}, mb_deposit_calc = 0 WHERE mb_no > 1 ";
    $result = sql_query($member_update_sql);
   
   /* if($update_member){

    
    for($i=0; $i <= 10 ; $i++){
        $orderid = date("YmdHis",time()).mt_rand(0000,9999);
        $member_id = 'test'.($i+20);
        $logic = purchase_package($member_id,2021011491,1);
        $insert_order_sql_arry .= " ({$orderid}, '{$member_id}', 0, 246, 1.05688, 232.76, 'M1', 2021011491, 1230, '{$today}', '{$todate}', 'eth', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', NULL, 0, 0, 0, 0, 0, 0, 0, 0, 0, '', '{$today}', 0, 0, NULL, NULL, '패키지구매', '0000-00-00', 0, 0, '', '', 0, '', 0, 0, 0, 0, '0', '', '0000-00-00 00:00:00', NULL, NULL, '', ''),";
    }

    $result_insert_sql = substr($insert_order_sql.$insert_order_sql_arry, 0, -1);

    if($debug){
        print_R($result_insert_sql);
        $result = 1;
    }else{
        $result = sql_query($result_insert_sql);
    }
   } */
}



if($_POST['nw_data_del'] == 'on'){
    
    $del_member = " DELETE from `g5_member` WHERE mb_no > 1; ";
    
    if($debug){
        print_R($del_member);
        $del_result = 1;
    }else{ 
        $del_result = sql_query($del_member);
    }


    if($del_result){
        $alter_table_query = " ALTER TABLE `g5_member` set AUTO_INCREMENT = 2; ";

        if($debug){
            print_R($alter_table_query);
        }else{ 
            sql_query($alter_table_query);
        }
        
    }
    
}

if($debug){}else{
    if($result){
        alert('정상 처리되었습니다.');
        goto_url('./config_reset.php');
    }
}
?>