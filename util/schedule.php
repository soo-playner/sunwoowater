<?php
include_once("../common.php");
include_once(G5_THEME_PATH."/_include/wallet.php");

$option1 =0;
$option2 =0;

if($_POST){
    $option1 = conv_number($_POST['pre_price']);
    $option2 = $_POST['pre_recharge'];
    $option3 = $_POST["pre_schedule"];
}

if($option2 == 0){
    $class = '';
}else{
    $class ='duel';
}
?>


<?
/* if($option1){
    echo "<strong> 예상 지급 </strong>";
    echo "<br><br>";
    echo " 기부금액 : <span class='f_blue'>".Number_format($option1)."</span>";
    echo " | 재구매횟수 : <span class='red'>".Number_format($option2)."</span>";
    echo "<br><br>";
} */
?>

<link rel="stylesheet" href="<?=G5_THEME_URL?>/css/scss/include/schedule.css">


<div class='schedule'>
    <div class='<?=$class?>'>
        <p> 1차 수익률 지급스케쥴  </p>
        <?
        $price = build_schedule($option1,$option2);
        $total_value =0;
        foreach ($price as $key => $value) {
            
            $total_value += $value;

            echo "<li>";
            echo "<dt>".array_month($key)."월</dt>";
            echo "<dt>".($current_layer + $key)."대</dt>";
            echo "<dd>";
            echo Number_format($value); 
            echo "</dd>";   
            echo "</li>";
        }
        echo "<li class='li-footer'><dd>";
        echo Number_format($total_value);
        echo "</dd></li>";
        ?>
    </div>

    <?if($option3 != 3){?>
    <div class='<?=$class?>'>
        <p> 2차 수익률 지급스케쥴  </p>
        <?

        $prices = build_schedule2($option1,$option2);
        $total_values = 0;

        foreach ($prices as $key => $value) {
            $total_values += round($value);
            echo "<li>";
            echo "<dt>".array_month($key)."월</dt>";
            echo "<dt>".($current_layer + $key)."대</dt>";
            echo "<dd>";
            echo Number_format($value);
            echo "</dd>";    
            echo "</li>";
        }

        echo "<li class='li-footer'><dd>";
        echo Number_format($total_values);
        echo "</dd></li>";
        ?>
    </div>
    <?}?>
</div>
