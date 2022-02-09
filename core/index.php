<?php
include_once("./_common.php");
include_once(G5_THEME_PATH.'/_include/wallet.php');

$option1 =0;
$option2 =0;

if($_POST){
    $option1 = conv_number($_POST['input_price']);
    $option2 = $_POST['auto_charge'];
}
?>


<link rel="stylesheet" href="<?=G5_THEME_URL?>/css/scss/include/schedule.css">
<style>
    .schedule_table{background:ghostwhite;padding:30px 20px;border-bottom:1px solid #111}
    .schedule_table li{ list-style: none;display:inline-block}
    .input1{width:120px;padding:5px;font-weight:600;font-size:15px;color:blue}
    .submit_btn{background:black;border:1px solid black;width:120px;color:white;height:30px;margin-left:10px;}
    label{display:block}
    .red{color:red;font-weight:600;}
    .green{color:green;font-weight:600;}
    .blue{color:blue;font-weight:600;margin-right:5px;font-size:16px;}
    .li-footer{background:#f1f1f1;color:red;display:flex !important}
</style>

<form name="bonus_schedule_cal" action='./index.php' id="bonus_schedule_cal" class="schedule_table" method="POST">
    <li>
    <label>기부금액</label>
    <input type='text' class='input1' name='input_price' value='' inputmode="numeric"></li>
    <li>
    <label>재구매횟수</label>
    <input type='text' class='input1' name='auto_charge' value=''></li>
    <li> 
    <li>
        <input type='submit' class='submit_btn' name='' value='실행'>
    </li>
</form>

<?

if($option1){
    echo "<strong> 투자 스케쥴링 결과 </strong>";
    echo "<br><br>";
    echo " 기부금액 : <span class='blue'>".Number_format($option1)."</span>";
    echo " | 재구매횟수 : <span class='red'>".Number_format($option2)."</span>";
    echo "<br><br>";
    
}
?>
<script src="https://code.jquery.com/jquery-3.6.0.min.js" integrity="sha256-/xUj+3OJU5yExlq6GSYGSHk7tPXikynS7ogEvDej/m4=" crossorigin="anonymous"></script>
<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.min.js" integrity="sha256-VazP97ZCwtekAsvgPBSUwPFKdrwD3unUfSGVYrahUqU=" crossorigin="anonymous"></script>
<script>

//  
$(document).on('keyup','input[inputmode=numeric]',function(event){
	this.value = this.value.replace(/[^0-9]/g,'');   //    
	this.value = this.value.replace(/,/g,'');          // , 
	this.value = this.value.replace(/\B(?=(\d{3})+(?!\d))/g, ","); //   3  ,  	
}); 

</script>


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
        echo "<li class='li-footer'><dd>";
        echo Number_format($total_value);
        echo "</dd></li>";
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

        echo "<li class='li-footer'><dd>";
        echo Number_format($total_values);
        echo "</dd></li>";
        ?>
    </div>
</div>
