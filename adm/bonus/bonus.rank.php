<?php

$sub_menu = "600200";
include_once('./_common.php');
// $debug = 1;
include_once('./bonus_inc.php');

auth_check($auth[$sub_menu], 'r');



if (!$debug) {
    $dupl_check_sql = "select mb_id from rank where rank_day='" . $bonus_day . "'";
    $get_today = sql_fetch($dupl_check_sql);

    if ($get_today['mb_id']) {
        alert($bonus_day . " 해당일 승급이 이미 완료 되었습니다.");
        die;
    }
}

// 직급 승급
$grade_cnt = 6;
$levelup_result = bonus_pick($code);

// 직추천 회원수 
$lvlimit_cnt = explode(',', $levelup_result['limited']);


// 구매등급기준
/* $lvlimit_sales_level = explode(',', $levelup_result['rate']);
$lvlimit_sales_level_val = 6000000; */


// 추천산하매출기준
$lvlimit_recom = explode(',', $levelup_result['layer']);
$lvlimit_recom_val = 1;


//회원 리스트를 읽어 온다.
$sql_common = " FROM g5_member ";
// $sql_search=" WHERE o.mb_id=m.mb_id AND DATE_FORMAT(o.od_time,'%Y-%m-%d')='".$bonus_day."'";
$search_condition = " and mb_level > 0 and rank > 3 ";
$sql_search = " WHERE grade < {$grade_cnt} {$search_condition} " . $pre_condition . $admin_condition;
$sql_mgroup = " GROUP BY grade ORDER BY grade asc ";

$pre_sql = "select grade, count(*) as cnt
                {$sql_common}
                {$sql_search}
                {$sql_mgroup}";

$pre_result = sql_query($pre_sql);

// 디버그 로그 
if ($debug) {
    echo "대상회원 - <code>";
    print_r($pre_sql);
    echo "</code><br>";
}
$pre_count = sql_num_rows($pre_result);
ob_start();

// 설정로그 
echo "<strong> 현재일 : " . $bonus_day;
// echo " | 지난주(week) : <span class='red'>".$week_frdate."~".$week_todate."</span>";
echo "</strong> <br>";

function grade_name($val)
{
    global $grade_cnt;
    if($val == 6){
        $grade_name = $val . " STAR[KHAN]";
    }else{
        $grade_name = $val . " STAR ";
    }
    return $grade_name;
}

/* 승급기준 로그 출력 */
echo "<br><code>회원직급 승급 조건   |   기준조건 :" . $pre_condition . "<br>";
for ($i = 0; $i < $grade_cnt; $i++) {
    echo "<br>" . grade_name($i + 1);
    echo  " -  [ 승급기준]  추천인수 합: " . ($lvlimit_cnt[$i]) . " 이상 / 추천산하매출(PV) " . Number_format($lvlimit_recom[$i] * $lvlimit_recom_val) . " 이상<br>";
}
echo "</code><br><br><br>";

echo "<strong>현재 직급 기준 대상자</strong> : ";

if($pre_count > 0){
    while ($cnt_row = sql_fetch_array($pre_result)) {
        echo "<br><strong>" . $cnt_row['grade'] . " STAR : <span class='red'>" . $cnt_row['cnt'] . '</span> 명</strong>';
    }
}else{
    echo "<span class='red'>대상자없음</span>";
}

echo "</span><br><br>";
echo "<div class='btn' onclick='bonus_url();'>돌아가기</div>";

?>

<html>

<body>
    <header>승급시작</header>
    <div>

        <?
        $mem_list = array();
        if($pre_count > 0){
            excute();
        }

        //직하부매출
        /* function recom_sales($mb_id){
            $mem_recom_sql = "SELECT * FROM g5_member where mb_recommend = '{$mb_id}' ";
            $mem_recom_result = sql_query($mem_recom_sql);
            $recom_sales = [];
            
            while($row = sql_fetch_array($mem_recom_result)){
                $recom = $row['mb_id'];
                $sql = "SELECT mb_id, mb_save_point FROM g5_member where mb_id ='{$recom}' ";
                $result = sql_fetch($sql);

                if($result){
                    $recom_sale = $result['mb_save_point'];

                    echo $mb_id."<br>";
                    echo $recom_sale."<br>";

                    if(!$recom_sale){
                        $recom_sale = 0;
                    }
                    array_push($recom_sales,$recom_sale);
                }else{
                    array_push($recom_sales,0);
                }
            }
            return $recom_sales;
        } */

    

    /*추천 하부라인 */
    function return_down_manager($mb_id,$cnt=0){
        global $config,$g5,$mem_list;

        $mb_result = sql_fetch("SELECT mb_id,mb_rate,mb_pv,mb_save_point from g5_member WHERE mb_id = '{$mb_id}' ");
        
        // 본인포함
       /*  $list = [];
        $list['mb_id'] = $mb_result['mb_id'];
        $list['mb_rate'] = $mb_result['mb_rate'];
        $list['mb_pv'] = $mb_result['mb_pv'];
        $list['mb_save_point'] = $mb_result['mb_save_point'];
        $mem_list = [$list]; */

        $result = recommend_downtree($mb_result['mb_id'],0,$cnt);
        return $result;
    }

    
    function recommend_downtree($mb_id,$count=0,$cnt = 0){
        global $mem_list;

        if($cnt == 0 || ($cnt !=0 && $count < $cnt)){
            
            $recommend_tree_result = sql_query("SELECT mb_id,mb_rate,mb_pv,mb_save_point from g5_member WHERE mb_recommend = '{$mb_id}' ");
            $recommend_tree_cnt = sql_num_rows($recommend_tree_result);
    
            if($recommend_tree_cnt > 0 ){
                ++$count;
                while($row = sql_fetch_array($recommend_tree_result)){
                    // $list['mb_rate'] = $row['mb_rate'];
                    $list['mb_id'] = $row['mb_id'];
                    $list['mb_pv'] = $row['mb_pv'];
                    $list['mb_save_point'] = $row['mb_save_point'];
                    $list['depth'] = $count;
                    
                    array_push($mem_list,$list);
                    recommend_downtree($row['mb_id'],$count,$cnt,$mem_list);
                }
            }
        }
        return $mem_list;
    }
    /* 결과 합계 */
    function array_index_sum($list, $key,$category)
    {
        $sum = null;
        $count = 0;
        $a = array_count_values(array_column($list, $key));

        foreach ($a as $key => $value) {
            if($category == 'int'){
                $sum +=$key; 
            }else if ($category == 'text'){
                $sum .=$key.' | '; 
            }
        }
        return $sum;
    }

    /*추천 상부라인 */
    /* function return_up_manager($mb_id,$cnt=0){
    global $config;
    $origin = $mb_id;
    $manager_list = [];
    $i = 0;
        if($mb_id != 'admin' && $mb_id != $config['cf_admin']){
            
            if($cnt == 0){
                do{
                    $manager = recommend_uptree($mb_id);
                    $mb_id = $manager;
                    array_push($manager_list,$manager);
                }while( 
                    $manager != 'dfine'
                );
            
                if(count($manager_list) < 2){
                    return $origin;
                }else{
                    return $manager_list[count($manager_list)-2];
                }
            }else{
                do{
                    $i++;
                    $manager = recommend_uptree($mb_id);
                    $mb_id = $manager;
                    array_push($manager_list,$manager);
                }while( $i < $cnt );

                return $manager_list[$cnt-1];
            }
        }else{
            return $mb_id;
        } 
    }

    function recommend_uptree($mb_id){
        $result = sql_fetch("SELECT mb_recommend,mb_level from g5_member WHERE mb_id = '{$mb_id}' ");
        return $result['mb_recommend'];
    }
    */




    // brecom_grade('test2');

    $brcomm_arr = [];

    function brecom_grade($mb_id, $grade_condition = 1, $sales_condition = 0)
    {
        global $config, $brcomm_arr, $debug;
        $origin = $mb_id;

        // 후원 하부 L,R 구분
        list($leg_list, $cnt) = brecommend_direct($mb_id);

        if ($cnt == 2) {

            $L_member = $leg_list[0]['mb_id'];
            $R_member = $leg_list[1]['mb_id'];


            $brcomm_arr = [];
            array_push($brcomm_arr, $leg_list[0]);
            $manager_list_L = brecommend_array($L_member, 0);
            /* echo "<br><br> L ::<br>";
    print_R($manager_list_L); */

            $brcomm_arr = [];
            array_push($brcomm_arr, $leg_list[1]);
            $manager_list_R = brecommend_array($R_member, 0);
            /* echo "<br><br> R ::<br>";
    print_R($manager_list_R); */

            /* 하부등급 확인 */
            // echo "<br><span> 직급 :: ";
            list($L_grade_array, $L_grade_count) = array_index_sort($manager_list_L, 'grade', $grade_condition);
            list($R_grade_array, $R_grade_count) = array_index_sort($manager_list_R, 'grade', $grade_condition);
            // if($debug){echo "<code>";print_R($L_grade_array);echo "<br>";print_R($R_grade_array);echo "</code>";}
            // echo "L : ".$L_grade_count.' / R :'.$R_grade_count;
            echo "</span>";

            /* 하부매출 확인 */
            if ($sales_condition > 0) {
                // echo "<br><span> 매출 :: ";
                list($L_sales_array, $L_sales_count) = array_index_sort($manager_list_L, 'mb_rate', $sales_condition);
                list($R_sales_array, $R_sales_count) = array_index_sort($manager_list_R, 'mb_rate', $sales_condition);
                // if($debug){echo "<code>";print_R($L_sales_array);echo "<br>";print_R($R_sales_array);echo "</code>";}
                // echo "L : ".$L_sales_count.' / R :'.$R_sales_count;
                echo "</span>";
                return array($L_member, $L_grade_array, $L_grade_count, $R_member, $R_grade_array, $R_grade_count, $L_sales_count, $R_sales_count);
            } else {
                return array($L_member, $L_grade_array, $L_grade_count, $R_member, $R_grade_array, $R_grade_count);
            }
        } else if ($cnt < 2) {

            echo "<span>후원 L,R 라인 없음</span>";
        } else {

            echo "<span>후원인 초과</span>";
        }
    }



    // 후원인 하부 회원 
    /* function brecommend_array($brecom_id, $count)
    {
        global $brcomm_arr;

        // $new_arr = array();
        $b_recom_sql = "SELECT mb_id,grade,mb_rate,mb_save_point,mb_brecommend_type from g5_member WHERE mb_brecommend='{$brecom_id}' ";
        $b_recom_result = sql_query($b_recom_sql);
        $cnt = sql_num_rows($b_recom_result);

        if ($cnt < 1) {
            // 마지막
        } else {
            ++$count;
            while ($row = sql_fetch_array($b_recom_result)) {
                brecommend_array($row['mb_id'], $count);
                // print_R($count.' :: '.$row['mb_id'].' | type ::'.$row['grade']);
                // $brcomm_arr[$count]['id'] = $brecom_id;
                array_push($brcomm_arr, $row);
            }
        }
        return $brcomm_arr;
    } */

    /* 
    function brecommend_direct($mb_id)
    {

        $down_leg = array();
        $sql = "SELECT mb_id,grade,mb_rate,mb_save_point,mb_brecommend_type FROM g5_member where mb_brecommend = '{$mb_id}' AND mb_brecommend != '' ORDER BY mb_brecommend_type ASC ";
        $sql_result = sql_query($sql);
        $cnt = sql_num_rows($sql_result);

        while ($result = sql_fetch_array($sql_result)) {
            array_push($down_leg, $result);
        }
        return array($down_leg, $cnt);
    } */



    // 배열정렬 + 지정값 이상 카운팅
    /* function array_index_sort($list, $key, $average)
    {
        $count = 0;
        $a = array_count_values(array_column($list, $key));

        foreach ($a as $key => $value) {

            if ($key >= $average) {
                $count += intval($value);
            }
        }
        return array($a, $count);
    } */

        function  excute()
        {

            global $g5, $search_condition, $admin_condition, $pre_condition;
            global $bonus_day, $week_frdate, $week_todate, $grade_cnt, $code, $lvlimit_cnt,$bonus_rates, $lvlimit_sales_level, $lvlimit_recom, $lvlimit_recom_val, $lvlimit_pv;
            global $debug,$mem_list;

            for ($i = $grade_cnt - 1; $i > -1; $i--) {
                $cnt_sql = "SELECT count(*) as cnt From {$g5['member_table']} WHERE grade = {$i} {$search_condition}" . $admin_condition . $pre_condition . " ORDER BY mb_no";
                $cnt_result = sql_fetch($cnt_sql);

                $sql = "SELECT * FROM {$g5['member_table']} WHERE grade = {$i} {$search_condition}" . $admin_condition . $pre_condition . " ORDER BY mb_no ";
                $result = sql_query($sql);

                $member_count  = $cnt_result['cnt'];

                echo "<br><br><span class='title block'>" . ($i+1) . " STAR (" . $member_count . ")</span><br>";
                echo  " -  [ 승급기준 ] 추천인수 합 : " . ($lvlimit_cnt[$i]) . " 이상 | 추천산하매출(PV) : " . Number_format($lvlimit_recom[$i]*$lvlimit_recom_val) . " 이상 ";

                // 1STAR 예외
                /* $lvlimit_recom_pv = 0;
                if ($i == 0) {
                    echo "| 하부 PV L,R 500 만원 이상| 본인 PV : 500 만원 이상";
                    $lvlimit_recom_pv = $lvlimit_pv;
                } */

                $bonus_rate = $bonus_rates[$i];

                // 디버그 로그 
                if ($debug) {
                    echo "<code>";
                    echo ($sql);
                    echo "</code><br>";
                }

                while ($row = sql_fetch_array($result)) {

                    $mb_no = $row['mb_no'];
                    $mb_id = $row['mb_id'];
                    $mb_name = $row['mb_name'];
                    $mb_level = $row['mb_level'];
                    $mb_deposit = $row['mb_deposit_point'];
                    $mb_balance = $row['mb_balance'];
                    $mb_save_point = $row['mb_save_point'];
                    $mb_rate = $row['mb_rate'];
                    $grade = $row['grade'];
                    $item_rank = $row['rank'];

                    // $star_rate = $bonus_rate[$i-1]*0.01;

                    $rank_option1 = 0;
                    $rank_option2 = 0;
                    $rank_option3 = 0;
                    $rank_grade = '';
                    $rank_cnt = 0;
                    echo "<br><span class='title' >[ " . $row['mb_id'] . " ] </span>";

                    // 관리자 제외
                    if ($mb_level > 9) {
                        break;
                    }

                    if ($member_count != 0) {

                        // 직추천자수 
                        $mem_cnt_sql = "SELECT count(*) as cnt FROM g5_member where mb_recommend = '{$mb_id}' ";
                        $mem_cnt_result = sql_fetch($mem_cnt_sql);
                        $mem_cnt = $mem_cnt_result['cnt'];

                        echo "<br>직추천인수 : <span class='blue'>" . $mem_cnt . "</span>";
                        if ($mem_cnt >= $lvlimit_cnt[$i]) {
                            $rank_cnt += 1;
                            $rank_option1 = 1;
                            echo "<span class='red'> == OK </span>";
                        }


                        /* // 내 매출 
                        $mem_pv = $mb_rate;
                        echo "<br>본인 PV : <span class='blue'>" . Number_format($mem_pv) . "</span>";
                        if ($mem_pv >= $lvlimit_pv) {
                            $rank_cnt += 1;
                            $rank_option2 = 1;
                            echo "<span class='red'> == OK </span>";
                        } */


                        // 하부 지난주 매출 - 사용안함
                        // $recom_week_sales = recom_sales($mb_id);
                        // echo "<br>지난주 하부 매출 - "; */
                        //print_R($recom_week_sales);
                        /* if($recom_week_sales){
                            $sum_sale = array_sum($recom_week_sales);
                            $max_sale = max($recom_week_sales);
                            
                            echo  "하부매출(". $sum_sale .") - 대실적(". $max_sale .") = 계산실적( <span class='blue'>".($sum_sale - $max_sale)."</span> )";
                            // if($mem_sales >= $lvlimit_recom[$i]*$lvlimit_recom_val){$rank_cnt += 1;}
                            if($mem_sales >= $lvlimit_recom[$i]*1){$rank_cnt += 1; echo "<span class='red'> == OK </span>";}
                            if($debug)  echo "<code>"; print_R($recom_week_sales);echo "</code>";
                        }
                        */


                        // 하부 직급 확인
                        /* echo "<br>후원하부 : ";
                        list($L_member, $L_grade_array, $L_grade_count, $R_member, $R_grade_array, $R_grade_count, $L_sales_count, $R_sales_count) = brecom_grade($mb_id, $lvlimit_sales_level[$i], $lvlimit_recom_pv);
                        if ($L_member) {
                            echo "L - <strong>" . $L_member . "</strong>";
                        }
                        if ($R_member) {
                            echo " | R - <strong>" . $R_member . "</strong>";
                        }
                        echo "<br>";


                        if ($i == 0) {
                            // 1STAR 매출
                            if ($L_sales_count >= 1 && $R_sales_count >= 1) {
                                echo "└ 매출기준 : <span class=blue>L : " . $L_sales_count . '명 / R :' . $R_sales_count . "명</span>";
                                $rank_cnt += 1;
                                $rank_option3 = 1;
                                echo "<span class='red'> == OK </span>";
                            } else {
                                if ($L_sales_count == '') {
                                    $L_sales_count = 0;
                                }
                                if ($R_sales_count == '') {
                                    $R_sales_count = 0;
                                }
                                echo "└ 매출기준 : L : " . $L_sales_count . '명 / R :' . $R_sales_count . "명";
                                // echo "<span class='red'> == X </span>";
                            }
                            $rank_grade = $L_sales_count . ',' . $R_sales_count;
                        } else {
                            // 1STAR 직급
                            if ($L_member) {
                                if ($L_grade_count >= $lvlimit_recom[$i] && $R_grade_count >= $lvlimit_recom[$i]) {
                                    echo "└ 직급기준 : <span class=blue>L : " . $L_grade_count . '명 / R :' . $R_grade_count . "명</span>";
                                    $rank_cnt += 1;
                                    $rank_option3 = 1;
                                    echo "<span class='red'> == OK </span>";
                                } else {
                                    echo "└ 직급기준 :  L : " . $L_grade_count . '명 / R :' . $R_grade_count . "명";

                                    // echo "<span class='red'> == X </span>";
                                }
                            }
                            $rank_grade = $L_grade_count . ',' . $R_grade_count;
                        } */

                        // 내 구매등급  
                        /* echo "<br>본인 아이템등급 : <span class='blue'>P" . Number_format($item_rank) . "</span>";
                        if ($item_rank >= $lvlimit_sales_level[$i]) {
                            $rank_cnt += 1;
                            $rank_option1 = 1;
                            echo "<span class='red'> == OK </span>";
                        } */

                        // 산하 추천  매출 -  mb_pv 기준
                        $mem_result = return_down_manager($mb_id);
                        $recom_sales = array_index_sum($mem_result,'mb_pv','int');
                        $recom_id = array_index_sum($mem_result,'mb_id','text');
                        $recom_sales_value = Number_format($recom_sales);
                        
                        echo "<br>산하추천(PV)매출 : <span class='blue'>" .$recom_sales_value. "</span>";
                        if( $recom_sales >= $lvlimit_recom[$i]*$lvlimit_recom_val){
                            $rank_cnt += 1;
                            $rank_option2 = 1;
                            echo "<span class='red'> == OK </span>";
                        }
                        /* echo "<br><span class='desc'>└ 추천하부 : ";
                        echo ($recom_id);
                        echo "</span>"; */
                        
                        
                        /* if($recom_week_sales){
                            $sum_sale = array_sum($recom_week_sales);
                            $max_sale = max($recom_week_sales);
                            
                            echo  "하부매출(". $sum_sale .") - 대실적(". $max_sale .") = 계산실적( <span class='blue'>".($sum_sale - $max_sale)."</span> )";
                            // if($mem_sales >= $lvlimit_recom[$i]*$lvlimit_recom_val){$rank_cnt += 1;}
                            if($mem_sales >= $lvlimit_recom[$i]*1){$rank_cnt += 1; echo "<span class='red'> == OK </span>";}
                            if($debug)  echo "<code>"; print_R($recom_week_sales);echo "</code>";
                        }
                        /* if ($item_rank >= $lvlimit_sales_level[$i]) {
                            $rank_cnt += 1;
                            $rank_option2 = 1;
                            echo "<span class='red'> == OK </span>";
                        } */


                        // 디버그 로그
                        if ($debug) {
                            echo "<code> Total Rank count :: ";
                            echo $rank_cnt;
                            echo "</code><br>";
                        }

                        // 승급조건 기록

                        /* $rank_record_sql = "INSERT INTO (mb_id,rank,option1,option1_result,option2,option2_result,option3,option3_result) VALUE ";
                        $rank_record_mem_sql .= "('{$row['mb_id']}',{$i},'{$mem_cnt}',{$rank_option1},'{$mem_pv}',{$rank_option2},'{$rank_grade}',{$rank_option3})"; */

                        $update_mem_rank = "UPDATE g5_member SET ";
                        $update_mem_rank .= "mb_4 = '{$mem_cnt}',mb_5= '{$rank_option1}' ";
                        $update_mem_rank .= ",mb_6 = '{$recom_sales}',mb_7= '{$rank_option2}' ";
                        $update_mem_rank .= "WHERE mb_id = '{$row['mb_id']}' ";

                        if ($debug) {
                            print_R($update_mem_rank);
                            echo "<br>";
                            // sql_query($update_mem_rank);
                        } else {
                            sql_query($update_mem_rank);
                        }

                        // 승급로그
                        if ($rank_cnt >= 2) {
                            $upgrade = ($grade + 1);
                            echo "<br><span class='red'> ▶▶ 직급 승급 => " . $upgrade . " STAR </span><br> ";
                            
                            $benefit=$bonus_rate;
                            $rec = $code . ' Promote to ' . ($grade + 1) . ' STAR IN ' . $bonus_day;
                            $rec_adm= $rec;

                            // 승급기록 
                            $bonus_sql = " insert rank set rank_day='{$bonus_day}'";
                            $bonus_sql .= " ,mb_id			= '{$mb_id}' ";
                            $bonus_sql .= " ,old_level		= '{$grade}' ";
                            $bonus_sql .= " ,rank      = {$upgrade}";
                            $bonus_sql .= " ,rank_note	= '{$rec}'";

                            if ($debug) {
                                echo "<br><code>";
                                print_R($bonus_sql);
                                echo "</code>";
                            } else {
                                sql_query($bonus_sql);
                            }

                            // 승급 수당지급
                            echo "<span class=blue> ▶▶ 수당 지급 : ".Number_format($benefit)."</span><br>";

                            $record_result = soodang_record($mb_id, $code, $benefit,$rec,$rec_adm,$bonus_day);

                            if($record_result){
                                
                                $balance_up = "update g5_member set mb_balance = mb_balance + {$benefit},mb_balance_ignore = mb_balance_ignore + {$benefit}, grade = {$upgrade}  where mb_id = '".$mb_id."'";

                                // 디버그 로그
                                if($debug){
                                    echo "<code>";
                                    print_R($balance_up);
                                    echo "</code>";
                                }else{
                                    sql_query($balance_up);
                                }
                                
                            }

                        } // if $rank_cnt

                        $mem_list = array();
                    } // if else
                } //while

                $rec = '';
                
            } //for
        } //function
        ?>

        <? include_once('./bonus_footer.php'); ?>

        <?
        if ($debug) {
        } else {
            $html = ob_get_contents();
            //ob_end_flush();
            $logfile = G5_PATH . '/data/log/' . $code . '/' . $code . '_' . $bonus_day . '.html';
            fopen($logfile, "w");
            file_put_contents($logfile, ob_get_contents());
        }
        ?>