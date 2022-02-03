<?php
include_once("../common.php");

$option1 =0;
$option2 =0;

if($_POST){
    $option1 = conv_number($_POST['input_price']);
    $option2 = $_POST['auto_charge'];
}

$value_array = [];
$price_array = [];
$price2_array = [];

function conv_number($val) {
    $number = (int)str_replace(',', '', $val);
    return $number;
}

function build_schedule($option1, $option2) {
    global $value_array, $price_array;
    $arr = [];
    for ($j = 0;$j <= $option2;$j++) {
        $value = floor(1 + $j / 2);
        array_push($arr, $value);
    }
    for ($k = $option2 - 1;$k >= 0;$k--) {
        $value = floor(1 + $k / 2);
        array_push($arr, $value);
    }
    foreach ($arr as $key => $value) {
        $bonus = $value * ($option1 / 3);
        if ($key == $option2 - 2) {
            $bonus = $bonus + $option1;
        }
        array_push($value_array, $value);
        array_push($price_array, $bonus);
    }
    if ($option2 == 1) {
        array_unshift($price_array, $option1);
    }
    return array($value_array, $price_array);
}

function build_schedule2($option1, $option2) {
    global $price2_array;
    global $value_array, $price_array;

    if ($option2 == 1) {
        array_unshift($price2_array, $option1);
    }
    for ($i = 0;$i <= ($option2 * 4);$i++) {
        $calculate = 0;
        $bonus = 0;
        for ($j = $i;$j >= 0;$j--) {
            $k = $i - $j;
            if ($price_array[$k]) {
                $calculate += ($price_array[$k] / 3) * $value_array[$j];
            }
        }
        $bonus_cnt = $option2 - 2 + count($price_array);
        if ($i >= $option2 - 2 && $i < $bonus_cnt) {
            $bonus+= $price_array[$i - ($option2 - 2) ];
        } else {
            $bonus = 0;
        }
        $value = $calculate + $bonus;
        array_push($price2_array, $value);
    }
    if ($option2 == 1) {
        array_push($price2_array, $option1 / 3 / 3);
    }
    return $price2_array;
} ?>

<style>
    .schedule_table{background:ghostwhite;padding:30px 20px;border-bottom:1px solid #111}
    .schedule_table li{ list-style: none;display:inline-block}
    .input1{width:150px;padding:5px;font-weight:600;font-size:15px;color:blue}
    .submit_btn{background:black;border:1px solid black;width:120px;color:white;height:30px;margin-left:10px;}
    .red{color:red;font-weight:600;}
    .green{color:green;font-weight:600;}
    .blue{color:blue;font-weight:600;margin-right:5px;font-size:16px;}
    
    .container{width:100%;padding:30px 0;height:100%;}
    .daul{width:50%;display:inline-block;float: left;}
    .container li{list-style: none;line-height:22px;border:1px solid #ccc;border-bottom:none; width:150px;text-align:right;padding:3px 10px;font-weight: 600;}
    .container li:last-child{border-bottom:1px solid #ccc}
    .li-footer{background:#f1f1f1;color:red}
</style>

<form name="bonus_schedule_cal" action='./index.php' id="bonus_schedule_cal" class="schedule_table" method="POST">
    <li>
    <label>기부금액</label>
    <input type='text' class='input1' name='input_price' value='' inputmode="numeric"></li>
    <li>&nbsp&nbsp | &nbsp&nbsp
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


<div class='container'>
    <div class='daul'>
        <p> 1차 수익률 지급스케쥴  </p>
        <?
        
        list($valuelist,$price) = build_schedule($option1,$option2);
        $total_value =0;
        foreach ($price as $key => $value) {
            
            $total_value += $value;

            echo "<li>";
            echo Number_format($value);    
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
            echo Number_format($value); 
            echo "</li>";
        }

        echo "<li class='li-footer'>";
        echo Number_format($total_values);
        echo "</li>";

        ?>
    </div>
</div>
