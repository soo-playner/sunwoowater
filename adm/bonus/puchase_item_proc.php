<?php
include_once('./_common.php');

$pack_sql = "SELECT it_id, it_name,it_price,it_point,it_supply_point,it_use, ca_id,it_maker FROM g5_shop_item WHERE it_use > 0 order by it_id asc ";
$pack_result = sql_query($pack_sql);

print_R($pack_result);

$mb_no = $_POST['mb_no'];
$mb_id = $_POST['mb_id'];
$item = $_POST['item'];


/* 구매내역 Invoice 생성*/
// $target = "mb_".$coin_val."_calc";
// $target_price = coin_prices($coin_val);
/* 
$orderid = date("YmdHis",time()).$mb_no;

$sql = "insert g5_shop_order set
	od_id				= '".$orderid."'
	, mb_no             = '".$mb_no."'
	, mb_id             = '".$mb_id."'
	, od_cart_price     = ".$output_val."
	, od_cash    		= ".$target_price."
	, od_receipt_time   = '".$now_datetime."'
	, od_time           = '".$now_datetime."'
	, od_name           = '".$pack_name."'
	, od_tno           = '".$pack_id."'
	, od_date           = '".$now_date."'
	, od_settle_case    = '".$coin_val."'
	, od_status         = '패키지 구매'
	, upstair    		= ".$input_val."
	, pv				= ".$input_val." ";

if($is_debug){
	$rst = 1;
	echo "구매내역 Invoice 생성<br>";
	echo $sql."<br><br>";
}else{
	$rst = sql_query($sql);
}


$pack_sql = "SELECT * FROM g5_shop_item WHERE it_use > 0 AND it_id = {$pack_id} ";
$pack_result = sql_fetch($pack_sql);
$pack_purpose = $pack_result['it_maker'];
$pack_target = explode('+',$pack_purpose);

if(count($pack_target) > 0){
	for($i=0; $i < count($pack_target); $i++){

		$pack_table = "package_".strtolower(trim($pack_target[$i]));

		$count_colum_sql = "SELECT count(*) as cnt FROM {$pack_table}";
		$count_colum = sql_fetch($count_colum_sql);
		$count_colum_cnt = $count_colum['cnt']+1;

		$count_item_sql = "SELECT count(*) as cnt FROM {$pack_table} where mb_id = '{$mb_id}' ";
		$count_item = sql_fetch($count_item_sql);
		$count_item_cnt = $count_item['cnt']+1;
		
		$it_name = $i."_".$mb_id."_".$count_item_cnt;

		$pack_reult_sql = "INSERT {$pack_table} SET mb_id = '{$mb_id}', idx= {$count_colum_cnt},it_name='{$it_name}', nth = {$count_item_cnt}, cdate = '{$now_date}', cdatetime = '{$now_datetime}', od_id = {$orderid} ";
	
		if($is_debug){
			$pack_insert = $pack_reult_sql;
			echo "구매내역 Invoice 생성<br>";
			echo $pack_insert."<br><br>";
		}else{
			$pack_insert = sql_query($pack_reult_sql);
		}
		// $pack_insert = sql_query($pack_reult_sql);
	}
}
 */

?>