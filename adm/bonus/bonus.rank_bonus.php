<?php

$sub_menu = "600299";
include_once('./_common.php');

// $debug = 1;
$category = 'rank_bonus';
include_once('./bonus_inc.php');

auth_check($auth[$sub_menu], 'r');

// 직급수당
$bonus_layer = $_GET['exc_layer'];
$bonus_rates = explode(',', $bonus_row['rate']);

ob_start();
$pre_extra_sql = "SELECT COUNT(NO) AS cnt, sum(bonus) AS total from soodang_extra WHERE COUNT =  {$bonus_layer} ";
$pre_extra = sql_fetch($pre_extra_sql);
$pre_extra_cnt = $pre_extra['cnt'];
$pre_extra_total = $pre_extra['total'];

$member_level = ['센터','지점','지사','본부'];
$member_level_target = ['center','jijum','jisa','bonbu'];

// 설정로그 
echo "<br><strong> 지급일 : " . $bonus_day . " |  지급차수 : <span class='red'>" . $bonus_layer . "차</span><br>";
echo "직급수당 산정건수 : <span class='red'>".$pre_extra_cnt." 건</span>";
echo " | 직급총 수당금액 : <span class='red'>".shift_auto($pre_extra_total);
echo "</span><br><br>";

echo "직급별 지급총액 : ";
for($i=0; $i < count($bonus_rates); $i++){
    echo $member_level[$i]." : <span class='blue'>". shift_auto($pre_extra_total * ($bonus_rates[$i]*0.01))."</span>  |  ";
}

echo "<br><br>";
echo "<div class='btn' onclick=bonus_url('".$category."')>돌아가기</div>";
?>

<html>

<body>
    <header>직급수당지급</header>
    <div>

        <?
        for ($z = count($bonus_rates); $z > 0; $z--) {
            rankbonus($z);
        }

        function rankbonus($val)
        {
            global $g5, $debug, $bonus_day, $code,$bonus_layer;
            global $bonus_rates,$bonus_layer, $member_level,$pre_extra_total,$member_level_target;

            $num = $val-1;
            $bonus_rate = ($bonus_rates[$num]*0.01);
            $rank = $member_level_target[$num];
            $total = $pre_extra_total * $bonus_rate;
            $exc_bonus_total = 0;
            
            echo "<br><br><div class='block'>";
            echo $member_level[$num];
            echo " : ".$bonus_rates[$num]."%  | ";
            echo shift_auto($total);
            echo "</div>";
            
            $rank_member_sql = "SELECT {$rank},sum(bonus) as benefit from soodang_extra WHERE COUNT = {$bonus_layer} AND {$rank} != ''  Group by {$rank} ";
            $rank_member = sql_query($rank_member_sql);

            if($rank_member){
                while($row = sql_fetch_array($rank_member)){
                    $mb_id = $row[$rank];
                    $benefit = $row['benefit']*$bonus_rate;

                    echo "<br><span class='title box'> ".$mb_id."  - 직급수당 : <span class='blue'>".shift_auto($benefit)."</span></span>";
                    
                    $member_log_sql = "SELECT mb_id,bonus from soodang_extra WHERE COUNT = {$bonus_layer} AND {$rank} = '{$mb_id}' ";
                    $member_log = sql_query($member_log_sql);

                    echo "<code>";
                    while($mem = sql_fetch_array($member_log)){
                        echo "<br>".$mem['mb_id']." | ".$mem['bonus']." * ".$bonus_rate." = ".shift_auto($mem['bonus']*$bonus_rate);
                    }
                    echo "</code>";

                    $rec='Rank Bonus By '.$member_level[$num].' :: '.shift_auto($benefit);
                    $rec_adm = shift_auto($row['benefit'])." * ".$bonus_rate." = ".shift_auto($benefit);
                    
                    $record_result = soodang_record($mb_id, $code, $benefit,$rec,$rec_adm,$bonus_day,$bonus_layer,1);

                    if($benefit > 0){
                        echo "<span class=blue> ▶▶ 직급 수당 지급 : ".shift_auto($benefit)."</span><br><br>";
                        $balance_up = "update g5_member set mb_balance = mb_balance + {$benefit}  where mb_id = '".$mb_id."'";

                        // 디버그 로그
                        if($debug){
                            echo "<code>";
                            print_R($balance_up);
                            echo "</code>";
                        }else{
                            sql_query($balance_up);
                        }

                        $exc_bonus_total += $benefit;
                        
                    }else{
                        echo "<span class=red> ▶▶ 지급수당금액 없음 </span><br>";
                    }


                }
            }else{
                echo "<span class='red'>지급 대상 없음</span>";
            }

            if($total > $exc_bonus_total){
                echo "<br><br>미지급금액 : <span class='red'>";
                echo shift_auto($total - $exc_bonus_total);
                echo "</span>";
            }
        } // rankup
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