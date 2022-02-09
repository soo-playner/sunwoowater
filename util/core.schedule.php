<?
// 현재 누적 볼카운트
$current_count = sql_fetch("SELECT SUM(od_rate) as count FROM g5_shop_order")['count'];
$extra = sql_fetch("SELECT rate,layer,bonus_condition from wallet_bonus_config WHERE code = 'extra' ");
$extra_count = $extra['rate'];
$extra_recharge = $extra['bonus_condition'];
// $extra_layer = $extra['layer'];


// 대수계산
$layer_array = [];
for($i=1; $i <= 35; $i++){
    array_push($layer_array,pow(2, $i));
}

$total_ball_count = $current_count  + $extra_count;
$total_layer = between_layer($total_ball_count);
$next_layer_remain = $layer_array[$total_layer-1] - $total_ball_count;


$month = date("m");
$current_layer = $total_layer;

$value_array = [];
$price_array = [];
$price2_array = [];


function between_layer($val){
    global $layer_array;
    for($i=0; $i < count($layer_array); $i++){
        
        if($layer_array[$i-1] < $val &&  $val <= $layer_array[$i]  ){
            $layer =  $i+1;
            return $layer;
        }
    }
}


function array_month($val,$month_value = 0){
	global $month;
	if($month_value != 0){
		$month = $month_value;
	}
	return ((($val+($month-1))%12)+1);
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
        array_push($price_array, Round($bonus));
    }
    if ($option2 == 1) {
        array_unshift($price_array, $option1);
    }
	$schedule_array = array_merge([0,0,0],$price_array);
    return $schedule_array;
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
        $value = Round($calculate + $bonus);
        array_push($price2_array, $value);
    }
    if ($option2 == 1) {
        array_push($price2_array, $option1 / 3 / 3);
    }
	$schedule2_array = array_merge([0,0,0,0,0,0],$price2_array);

    return $schedule2_array;
}
?>