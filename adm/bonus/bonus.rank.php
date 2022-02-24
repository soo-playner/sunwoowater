<?php

$sub_menu = "600499";
include_once('./_common.php');
// $debug = 1;
include_once(G5_THEME_PATH.'/_include/wallet.php');
include_once('./bonus_inc.php');
include_once(G5_PATH.'/util/brecommend.php');

auth_check($auth[$sub_menu], 'r');

$category ='rank';
// 해당차수
$bonus_layer = $_GET['exc_layer'];

if (!$debug) {
    $dupl_check_sql = "select mb_id from rank where count={$bonus_layer} category = 0";
    $get_today = sql_fetch($dupl_check_sql);

    if ($get_today['mb_id']) {
        alert($bonus_layer . " 차수 승급이 이미 완료 되었습니다.");
        die;
    }
}


// 직급 승급
$grade_cnt = 4;
$levelup_result = bonus_pick($code);


// 본인구매조건
$lvlimit_pv = $levelup_result['limited'];

// 본인구매조건
$lvlimit_grade = $levelup_result['bonus_condition'];


// 본인해쉬파워기준
// $lvlimit_sales_level = explode(',', $levelup_result['rate']);


// 추천하부구매볼누적
$lvlimit_recom = explode(',', $levelup_result['layer']);
$lvlimit_recom_val = 1;


//회원 리스트를 읽어 온다.
$sql_common = " FROM g5_member ";
$search_condition = " and mb_level > 0 ";
$sql_search = " WHERE mb_level > 0 and mb_level <9 ";
$sql_mgroup = " GROUP BY mb_level ORDER BY mb_level asc ";

$pre_sql = "select mb_level, count(*) as cnt
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
echo " [ ".$bonus_layer."대 ]";
echo "</strong> <br>";

/* 승급기준 로그 출력 */
echo "<br><code>회원직급 승급 조건   |   기준조건 :" . $pre_condition . "<br>";
for ($i = 0; $i < $grade_cnt; $i++) {
    echo "<br>" .$member_level_array[$i+2];
    echo  " -  [ 승급기준 ] 본인 : " .shift_auto($lvlimit_pv)."(S".$lvlimit_grade.") 이상";
    echo  " / 추천하부(본인포함) : ".shift_auto($lvlimit_recom[$i] * $lvlimit_recom_val)."B 이상 <br>";
}
echo "</code><br><br>";

echo "<strong>현재 직급 기준 대상자</strong> : ";

if($pre_count > 0){
    while ($cnt_row = sql_fetch_array($pre_result)) {
        $level = $cnt_row['mb_level'];
        echo "<br><strong>" . $member_level_array[$level] . " : <span class='red'>" . $cnt_row['cnt'] . '</span> 명</strong>';
    }
}else{
    echo "<span class='red'>대상자없음</span>";
}

echo "</span><br><br>";
echo "<div class='btn' onclick=bonus_url('".$category."');>돌아가기</div>";

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

        function return_down_manager($mb_id,$cnt=0){
            global $config,$g5,$mem_list;
        
            $mb_result = sql_fetch("SELECT mb_id,mb_rate from g5_member WHERE mb_id = '{$mb_id}' ");
            array_unshift($mem_list,$mb_result);
            $result = recommend_downtree($mb_result['mb_id'],0,$cnt);
            return $result;
        }
        
        
        function recommend_downtree($mb_id,$count=0,$cnt = 0){
            global $mem_list;
        
            if($cnt == 0 || ($cnt !=0 && $count < $cnt)){
                
                $recommend_tree_result = sql_query("SELECT mb_id,mb_rate from g5_member WHERE mb_recommend = '{$mb_id}' ");
                $recommend_tree_cnt = sql_num_rows($recommend_tree_result);
        
                if($recommend_tree_cnt > 0 ){
                    ++$count;
                    while($row = sql_fetch_array($recommend_tree_result)){
                        $list['mb_id'] = $row['mb_id'];
                        $list['mb_rate'] = $row['mb_rate'];
                        $list['depth'] = $count;
                        
                        array_push($mem_list,$list);
                        recommend_downtree($row['mb_id'],$count,$cnt);
                    }
                }
            }
            return $mem_list;
        }
        
        /* 결과 합계 중복제거*/
        function array_index_sum($list, $key,$category)
        {
            $sum = null;
            $count = 0;
            $a = array_count_values(array_column($list, $key));
            

            foreach ($a as $key => $value) {
                
                if($category == 'int'){
                    // echo $key." ";
                    $sum += $key; 
                    // echo "= ".$sum."<br>";
                }else if ($category == 'text'){
                    $sum .= $key.' | '; 
                }
            }
            return $sum;
        }

        /* 결과 합계 */
        function array_int_sum($list, $key){
            return array_sum(array_column($list, $key));
        }
        

        function  excute()
        {
            global $g5, $search_condition, $admin_condition, $pre_condition;
            global $bonus_day, $grade_cnt, $code, $lvlimit_pv,$bonus_rates, $lvlimit_grade,$lvlimit_recom,$lvlimit_recom_val;
            global $debug,$mem_list,$member_level_array,$bonus_layer;

            for ($i = $grade_cnt; $i > 0; $i--) {
                $cnt_sql = "SELECT count(*) as cnt From {$g5['member_table']} WHERE mb_level = {$i} {$search_condition}" . $admin_condition . $pre_condition . " ORDER BY mb_no";
                $cnt_result = sql_fetch($cnt_sql);
                $member_count  = $cnt_result['cnt'];

                echo "<br><br><span class='title block'>" .$member_level_array[$i+1]. " 승급대상 (" . $member_count . ")</span><br>";
                echo  " -  [ 승급기준 ] 본인 : " . shift_auto($lvlimit_pv)." (S".$lvlimit_grade.") 이상";
                echo  " / 추천하부(본인포함) : ".shift_auto($lvlimit_recom[$i-1] * $lvlimit_recom_val)."B 이상 <br>";
                echo "<br><br>";

                $sql = "SELECT * FROM {$g5['member_table']} WHERE mb_level = {$i} {$search_condition}" . $admin_condition . $pre_condition . " ORDER BY mb_no ";
                $result = sql_query($sql);
               
                // $bonus_rate = $bonus_rates[$i];

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

                    $grade = $row['grade'];
                    $mb_level = $row['mb_level'];

                    $mb_deposit = $row['mb_deposit_point'];
                    $mb_balance = $row['mb_balance'];
                    
                    $mb_rate = $row['mb_rate'];
                    $item_rank = $row['rank'];
                    $mb_save_point = $row['mb_save_point'];

                    // $star_rate = $bonus_rate[$i-1]*0.01;

                    $rank_option1 = 0;
                    $rank_option2 = 0;
                    $rank_option3 = 0;

                    $rank_grade = '';
                    $rank_cnt = 0;

                    echo "<br><span class='title box' >[ " . $row['mb_id'] . " ] </span>";

                   

                    if ($member_count > 0) {


                        // 내 매출 
                        echo "<br>본인 PV : <span class='blue'>" . Number_format($mb_save_point) . "</span>";
                        echo " | 본인 매출등급 : <span class='blue'> S" . Number_format($item_rank) . "</span>";
                        if ($mb_save_point >= $lvlimit_pv || $item_rank > $lvlimit_grade) {
                            $rank_cnt += 1;
                            $rank_option1 = 1;
                            echo "<span class='red'> == OK </span>";
                        }


                        // 산하 추천  매출 -  mb_pv 기준
                        $mem_result = return_down_manager($mb_id);
                        $recom_sales = array_int_sum($mem_result,'mb_rate','int');
                        $recom_id = array_index_sum($mem_result,'mb_id','text');
                        
                        $recom_sales_value = Number_format($recom_sales);
                        
                        echo "<br>추천 산하 볼(B) : <span class='blue'>" .$recom_sales_value. "</span>";
                        
                        if( $recom_sales >= $lvlimit_recom[$i-1] * $lvlimit_recom_val){
                            $rank_cnt += 1;
                            $rank_option2 = 1;
                            echo "<span class='red'> == OK </span>";
                        }
                        

                        echo "<br><span class='desc'>└ 추천하부 : ";
                        echo ($recom_id);
                        echo "</span>";
                        

                        // 디버그 로그
                        if ($debug) {
                            echo "<code> Total Rank count :: ";
                            echo $rank_cnt;
                            echo "</code><br>";
                        }

                        // 승급조건 기록

                        /* $rank_record_sql = "INSERT INTO (mb_id,rank,option1,option1_result,option2,option2_result,option3,option3_result) VALUE ";
                        $rank_record_mem_sql .= "('{$row['mb_id']}',{$i},'{$mem_cnt}',{$rank_option1},'{$mb_rate}',{$rank_option2},'{$rank_grade}',{$rank_option3})"; */

                        $update_mem_rank = "UPDATE g5_member SET ";
                        $update_mem_rank .= "mb_4 = '{$mb_save_point}',mb_5= '{$rank_option1}' ";
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
                            $upgrade = ($mb_level + 1);
                            echo "<br><span class='red'> ▶▶ 직급 승급 => " . $member_level_array[$upgrade] . " </span><br> ";
                            
                            $benefit=$bonus_rate;
                            $rec = $code." Promote to {$upgrade} Lv (".$member_level_array[$upgrade].") IN " . $bonus_day."[".$bonus_layer."차수]";
                            $rec_adm= $rec;

                            // 승급기록 
                            $bonus_sql = " insert rank set rank_day='{$bonus_day}'";
                            $bonus_sql .= " ,mb_id			= '{$mb_id}' ";
                            $bonus_sql .= " ,old_level		= '{$mb_level}' ";
                            $bonus_sql .= " ,rank           = {$upgrade}";
                            $bonus_sql .= " ,rank_note	    = '{$rec}' ";
                            $bonus_sql .= " ,category	    = 0 ";
                            $bonus_sql .= " ,count	        = '{$bonus_layer}' ";
                            $bonus_sql .= " ,upgrade_clue	= '{$recom_sales}' ";
                            

                            if ($debug) {
                                echo "<br><code>";
                                print_R($bonus_sql);
                                echo "</code>";
                            } else {
                                sql_query($bonus_sql);
                            }


                            $record_result = 1;

                            if($record_result){
                                
                                $balance_up = "update g5_member set mb_level = {$upgrade}  where mb_id = '".$mb_id."'";

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
if($debug){}else{
    $html = ob_get_contents();
    //ob_end_flush();
    $logfile = G5_PATH.'/data/log/'.$code.'/'.$code.'_'.$bonus_layer.'.html';
    fopen($logfile, "w");
    file_put_contents($logfile, ob_get_contents());
}
?>