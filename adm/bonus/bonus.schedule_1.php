<?php
$sub_menu = "600299";
include_once('./_common.php');
// $debug = 1;
include_once('./bonus_inc.php');

auth_check($auth[$sub_menu], 'r');
$bonus_layer = $_GET['exc_layer'];

//회원 리스트를 읽어 온다.
$sql_common = " FROM g5_shop_order ";
$sql_search=" WHERE od_test = 0 AND od_select =1 ";
$sql_mgroup=' ORDER BY od_date asc';

$pre_sql = "select count(mb_id) as cnt 
            {$sql_common}
            {$sql_search}
            {$sql_mgroup}";


if($debug){
    echo "<code>";
    print_r($pre_sql);
    echo "</code><br>";
}

$result_cnt = sql_fetch($pre_sql)['cnt'];
$group_cnt = sql_fetch("SELECT count(mb_id) AS cnt FROM (SELECT * from g5_shop_order {$sql_search} GROUP BY mb_id ) A")['cnt'];

ob_start();

// 설정로그 
echo "<span class ='title' style='font-size:20px;'>".$bonus_layer."대 : ". $bonus_row['name'] ." 수당 지급</span><br>";
echo "<strong>".strtoupper($code)." 수당 지급비율 : schedule   </strong> |    지급조건 -".$pre_condition.' | '.$bonus_source_tx." | ".$bonus_layer_tx." | ".$bonus_limit_tx."<br>";
echo "<strong>".$bonus_day."</strong><br>";
echo "<br><span class='red'> 기준대상건 (대상회원) : ".$result_cnt."건 ( $group_cnt 명) </span><br><br>";
echo "<div class='btn' onclick='bonus_url();'>돌아가기</div>";

?>

<html><body>
<header>정산시작</header>    
<div>

<?
$sql = "SELECT *
            {$sql_common}
            {$sql_search}
            {$sql_mgroup}";
$result = sql_query($sql);

// 디버그 로그 
if($debug){
	echo "<code>";
    print_r($sql);
	echo "</code><br>";
}

excute();


function  excute(){

    global $result;
    global $g5, $bonus_day, $bonus_condition,$bonus_source, $code, $bonus_rates, $bonus_rate,$pre_condition_in,$bonus_limit,$bonus_layer ;
    global $debug;

    for ($i=0; $row=sql_fetch_array($result); $i++) {   

        $mb_id = $row['mb_id'];
        $it_id = $row['od_tno'];
        $od_id = $row['od_id'];
        $it_bonus = $row['upstair'];
        $it_name = $row['od_name'];

        $pay_count = $row['pay_count'];
        $pay_acc = $row['pay_acc'];
        $od_schedule = json_decode($row['od_schedule1'],true);
        $od_bonus = $od_schedule[$pay_count];

        $od_select = $row['od_select'];

        echo "<br><br><span class='title block' style='font-size:30px;'>".$mb_id."</span><br>";
        
        /* echo "<code>";
        print_R($row);
        echo "</code>"; */


        echo "<div class='item_title'>";
        echo "상품 : ".$it_name;
        echo " | 수량 : ".$row['od_rate'];
        echo " | 구매차수 : ".$row['od_layer']." 대";
        echo " | 구매일 : ".$row['od_date'];
        echo "</div> ";

        echo "▶지급진행횟수 : ".$pay_count ;
        echo " / 누적지급금액 : ".$pay_acc ;
        echo "<br><br>";

        echo "▶▶지급계산액 : <span class='blue'>".Number_format($od_bonus)."</span>";

        
        echo "<br><span class=blue> ▶▶▶ 수당 지급 : ".Number_format($od_bonus)."</span>";

        $benefit = $od_bonus;
        $benefit_limit = $od_bonus;
        $rec = $bonus_layer." 대 기부수당 지급 : ".Number_format($od_bonus);
        $rec_adm = "스케쥴1 수당지급 (".$pay_count."/".count($od_schedule).") - ".Number_format($od_bonus);

        if($benefit > -1 && $benefit_limit > -1){

            $record_result = soodang_record($mb_id, $code, $benefit_limit,$rec,$rec_adm,$bonus_day,$bonus_layer,$od_select);
            $order_update_result = order_update($od_id,$benefit_limit);

            if($record_result && $order_update_result){
                $balance_up = "update g5_member set mb_balance = mb_balance + {$benefit_limit}  where mb_id = '".$mb_id."'";

                // 디버그 로그
                if($debug){
                    echo "<code>";
                    print_R($balance_up);
                    echo "</code>";
                }else{
                    sql_query($balance_up);
                }
            }
        }

    } // for
}
?>

<?include_once('./bonus_footer.php');?>

<?
if($debug){}else{
    $html = ob_get_contents();
    //ob_end_flush();
    $logfile = G5_PATH.'/data/log/'.$code.'/'.$code.'_'.$bonus_day.'.html';
    fopen($logfile, "w");
    file_put_contents($logfile, ob_get_contents());
}
?>