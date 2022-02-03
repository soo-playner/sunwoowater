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

?>

<style>
    .schedule_table{background:ghostwhite;padding:30px 20px;border-bottom:1px solid #111}
    .schedule_table li{ list-style: none;display:inline-block}
    .input1{width:150px;padding:5px;font-weight:600;font-size:15px;color:blue}
    .submit_btn{background:black;border:1px solid black;width:120px;color:white;height:30px;margin-left:10px;}
    .red{color:red;font-weight:600;}
    .green{color:green;font-weight:600;}
    .f_blue{color:blue;font-weight:600;margin-right:5px;font-size:16px;}
    
    .schedule{width:100%;padding:30px 0;height:100%;}
    .schedule p {text-align:left;}
    .daul{width:50%;display:inline-block;float: left;padding-bottom:30px;}
    .schedule li{list-style: none;line-height:26px;border:1px solid #ccc;border-bottom:none; width:90%;text-align:right;font-weight: 600;display:flex}
    .schedule li dt{display:inline-block;min-width:35px;border-right:1px solid #ccc;font-size:11px;padding-right:3px;color:#555}
    .schedule li dt{background:ghostwhite}
    .schedule li dt:first-child{background:#f9ebeb}
    
    .schedule li dd{display:inline-block;width:100%;padding:0 10px;}

    .schedule li.li-footer{background:#f1f1f1;color:red;border-bottom:1px solid #ccc;display:inherit;padding:0 10px;text-align: right;}

    #dialogModal .modal-dialog{transform:translate(0,10%)}
    .modal-body{max-height:600px;}
</style>

<?
/* if($option1){
    echo "<strong> 예상 지급 </strong>";
    echo "<br><br>";
    echo " 기부금액 : <span class='f_blue'>".Number_format($option1)."</span>";
    echo " | 재구매횟수 : <span class='red'>".Number_format($option2)."</span>";
    echo "<br><br>";
} */


?>



<div class='schedule'>
    <div class='daul'>
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
        echo "<li class='li-footer'>";
        echo Number_format($total_value);
        echo "</li>";
        ?>
    </div>

    <div class='daul'>
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

        echo "<li class='li-footer'>";
        echo Number_format($total_values);
        echo "</li>";
        ?>
    </div>
</div>
