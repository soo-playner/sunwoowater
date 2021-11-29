<?php

$sub_menu = "600299";
include_once('./_common.php');

// $debug = 1;
$category = 'mining';
include_once('./bonus_inc.php');

auth_check($auth[$sub_menu], 'r');



// 승급수당

$avata = explode(',', $bonus_row['limited']);
$bonus_token = explode(',', $bonus_row['layer']);

$group_rule = ['S1+S2','S2+S3','S3+S4','S4+S5','S5'];
$group_rule_num = ['1,2','2,3','3,4','4,5','5'];

// 보름(15일) 단위 산출
$day = date('d', $timestr);
$lastday = date('t', $timestr);

if($day > 14 && $day <= 24){
    $half = '1/2';
    $half_frdate    = date('Y-m-1', $timestr); // 매월 1 시작일자
    $half_todate    = date('Y-m-15', $timestr); // 매월 15
}else{
    $half = '2/2';
    $half_frdate    = date('Y-m-16', $timestr); // 매월 15
    $half_todate    = date('Y-m-'.$lastday, $timestr); // 매월 말일
}


//보름간 마이닝 합계 
$total_order_query = "SELECT truncate(sum(mining),6) AS hap FROM soodang_mining WHERE day BETWEEN '{$half_frdate}' AND '{$half_todate}' ";
$total_order_reult = sql_fetch($total_order_query);
$total_order = $total_order_reult['hap'];
$grade_order = ($total_order * $bonus_rate);

ob_start();

// 설정로그 
echo "<br><strong> 현재일 : " . $bonus_day . " |  " . $half . "월 (지급산정기준) : <span class='red'>" . $half_frdate . "~" . $half_todate . "</span><br>";
echo "<br>- 해당기간 마이닝 지급총량 : <span class='red'>" . point_number($total_order).' '.$minings[0] ." </span>";
echo "<br>- 승급수당 마이닝 할당 : <span class='red'> (".$bonus_row['rate']."%)  " . point_number($grade_order).' '.$minings[0]." </span>";

/* for ($i = 0; $i < count($bonus_layer); $i++) {
    echo "<br>- <strong>S" . ($i+1) . "  : ";
    echo "승급수당 : <span class='red'>" . Number_format($bonus_layer[$i]) . " % </span>";
    echo "</strong>";
} */
echo "</span><br><br>";
echo "<div class='btn' onclick=bonus_url('".$category."')>돌아가기</div>";
?>

<html>

<body>
    <header>상품승급시작</header>
    <div>

        <?
        for ($z = count($bonus_layer); $z > 0; $z--) {
            rankup($z);
        }

        function rankup($val)
        {
            global $g5, $debug, $bonus_day, $code;
            global $bonus_rate,$bonus_layer, $grade_order,$group_rule,$group_rule_num,$minings,$mining_target,$mining_hash;

            $num = $val - 1;

            $rank_member_sql = "SELECT * from `g5_member` WHERE grade = {$val} ";
            $rank_member_result = sql_query($rank_member_sql);
            $rank_member_cnt = sql_num_rows($rank_member_result);

            $rank_group_sql = "SELECT count(*) as cnt from g5_member WHERE grade in ({$group_rule_num[$num]}) ";
            $rank_group_cnt = sql_fetch($rank_group_sql)['cnt'];

            $rank_bonus = $grade_order*$bonus_layer[$num];

            echo "<br><br><div class='block'>";
            echo "S{$val} ";




            echo " : ".$bonus_layer[$num]."% ";
            echo " | Bonus ::".$rank_bonus.' '.$minings[0];
            echo " | ".$group_rule[$num]." = 1 / ".$rank_group_cnt;
            echo "</div>";

            if($rank_member_cnt > 0){
                while($row = sql_fetch_array($rank_member_result)){
                    $mb_id = $row['mb_id'];
                    $benefit = point_number($rank_bonus/$rank_group_cnt);

                    echo "<br><span class='title box'> ".$mb_id."  - 승급수당 : <span class='blue'>".point_number($benefit).' '.$minings[0]."</span></span>";
                    

                    if($benefit > 0){
                        $rec='Rank Bonus By promote to S'.$val.' :: '.$benefit.' '.$minings[0];
                        $rec_adm = $rec;

                        echo "<span class=blue> ▶▶ 마이닝 지급 : ".$benefit.' '.$minings[0]."</span><br>";

                        $record_result = mining_record($mb_id, $code, $benefit,$bonus_rate,$minings[0], $rec, $rec_adm, $bonus_day);

                        if($record_result){
                            $balance_up = "update g5_member set {$mining_target} = {$mining_target} + {$benefit}  where mb_id = '{$mb_id}' ";

                            // 디버그 로그
                            if($debug){
                                echo "<code>";
                                print_R($balance_up);
                                echo "</code>";
                            }else{
                                sql_query($balance_up);
                            }
                        }
                    }else{
                        echo "<span class=blue> ▶▶ 수당 지급 : ".$benefit.' '.$minings[0]."</span><br>";
                    }
                }
            }else{
                echo "<span class='red'>지급 대상 없음</span>";
            }
        } // rankup
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