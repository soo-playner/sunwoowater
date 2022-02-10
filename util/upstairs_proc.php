<?php
include_once('./_common.php');
include_once(G5_THEME_PATH.'/_include/wallet.php');
include_once(G5_PATH.'/util/purchase_proc.php');

// $debug = 1;
$now_datetime = date('Y-m-d H:i:s');
$now_date = date('Y-m-d');

if($func == "admin"){
	$od_status = '패키지구매(관리자)';
	$mb_id = $_POST['mb_id'];
	$mb_no = $_POST['mb_no'];
	$mb_rank = $_POST['rank'];
}else{
	$od_status =  '패키지구매';
	$mb_id = $member['mb_id'];
	$mb_no = $member['mb_no'];
	$mb_rank = $member['rank'];
}


$coin_val = PURCHASE_CURENCY;
$func = $_POST['func'];
$input_val= $_POST['input_val']; // 결제금액 
$output_val = $_POST['output_val']; // 구매금액 
$it_point = $_POST['it_point']; // DSP
$pack_name= $_POST['select_pack_name'];
$pack_id = $_POST['select_pack_id']; 
$pack_maker = $_POST['select_maker']; // 시스템 상품명
$it_supply_point = $_POST['it_supply_point']; // 수량
$recharge = $_POST['recharge']; // 재기부
$schedule = $_POST['schedule']; // 지급스케쥴


if($debug){
	$mb_id = 'test11';
	$mb_no = 12;
	$mb_rank = 0;
	$func = 'new';
	$input_val ='0'; 
	$output_val ='30000'; 
	$pack_name = 'S1'; 
	$pack_maker = 'P1'; 
	$pack_id = 2021111641;
	$it_point = 150; 
	$it_supply_point = 1;
	$recharge = 4;
	$schedule = 1;
}

$val = substr($pack_maker,1,1);

$target = "mb_deposit_calc";
$orderid = trim(date("mdHis",time()).'0'.substr(trim($pack_id),-2,2).'9'.$mb_no);

// samwoo
$od_schedule_1 = build_schedule($output_val,$recharge);
$od_schedule_2 = build_schedule2($output_val,$recharge);

if($schedule == 1){
	$schedule_endcount = count($od_schedule_1);
}else{
	$schedule_endcount = count($od_schedule_2);
}

$sql = "insert g5_shop_order set
	od_id				= '{$orderid}'
	, mb_no             = '{$mb_no}'
	, mb_id             = '{$mb_id}'
	, od_cart_price     = '{$output_val}'
	, od_cash    		= '{$it_point}'
	, od_name           = '{$pack_name}'
	, od_tno            = '{$pack_id}'
	, od_receipt_time   = '{$now_datetime}'
	, od_time           = '{$now_datetime}'
	, od_date           = '{$now_date}'
	, od_settle_case    = '{$coin_val}'
	, od_status         = '{$od_status}'
	, upstair    		= {$output_val}
	, od_rate			= {$it_supply_point}
	, od_schedule1		= '".json_encode($od_schedule_1)."'
	, od_schedule2		= '".json_encode($od_schedule_2)."'
	, od_layer			= '{$total_layer}'
	, od_recharge		= '{$recharge}'
	, od_select			= {$schedule}
	, pay_end			= {$schedule_endcount}
	";
if($debug){
	$rst = 1;
	echo "구매내역 Invoice 생성<br>";
	echo $sql."<br><br>";
}else{
	$rst = sql_query($sql);
}


$logic = purchase_package($mb_id,$pack_id,0,$schedule);
$calc_value = conv_number($output_val);

if($rst && $logic){
	$mb = sql_fetch("SELECT * from g5_member WHERE mb_id ='{$mb_id}' ");
	$mb_rank = $mb['rank'];
	$update_point = " UPDATE g5_member set $target = ($target - $calc_value) ";

	if($mb['mb_level'] == 0){
		$update_point .= ", mb_level = 1 " ;
	}

	if($mb_rank >= $val){
		$update_rank = $mb_rank;
	}else{
		$update_rank = $val;
	}
	
	// $update_point .= ", mb_rate = ( mb_rate + {$od_rate}) ";
	// 마이닝보류
	$update_point .= ", mb_pv = ( mb_pv + {$it_supply_point}) ";
	$update_point .= ", mb_rate = ( mb_rate + {$it_supply_point}) ";
	$update_point .= ", mb_save_point = ( mb_save_point + {$calc_value}) ";
	$update_point .= ", cash_point = cash_point + {$it_point} ";
	$update_point .= ", rank = '{$update_rank}', rank_note = '{$pack_name}', sales_day = '{$now_datetime}' ";
	$update_point .= " where mb_id ='".$mb_id."'";

	if($debug){
		echo "회원 금액 반영<br>";
		echo $update_point."<br>";
	}else{
		sql_query($update_point);
		ob_end_clean();
		echo (json_encode(array("result" => "success",  "code" => "0000", "sql" => $save_hist)));
	}
}else{
	ob_end_clean();
	echo (json_encode(array("result" => "failed",  "code" => "0001", "sql" => $save_hist)));
}

?>

<?if($debug){?>
<style>
    .red{color:red;font-size:16px;font-weight:900}
    .blue{color:blue;font-size:16px;font-weight:900}
    .title {font-weight:900}
    code{text-decoration: italic;color:green;display:block}
    .box{background:#f5f5f5;border:1px solid #ddd;padding:20px;}
</style>
<?}?>
