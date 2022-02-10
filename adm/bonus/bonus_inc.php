<?php
include_once('./_common.php');

/*수당설정 로드*/
define('ASSETS_CURENCY','원');
$now_datetime = date('Y-m-d H:i:s');
$now_date = date('Y-m-d');

if($_GET['debug']){
    $debug = 1;
}
 
$bonus_sql = "select * from {$g5['bonus_config']} WHERE used > 0 order by no asc";
$list = sql_query($bonus_sql);

$pre_setting = sql_fetch($bonus_sql);
$pre_condition ='';
$admin_condition = " and "." mb_level < 10 ";


// 이미지급받은경우
$file_name = explode(".",basename($_SERVER['PHP_SELF']));
$code=$file_name[1];
$bonus_day = $_GET['to_date'];
$bonus_exc_layer = $_GET['exc_layer'];

if(!$debug && $sub_menu == "600299"){
    if($category == 'mining'){
        $check_target = $g5['mining'];
    }else{
        $check_target = $g5['bonus'];
    }

    // $dupl_check_sql = "select mb_id from {$check_target} where day='".$bonus_day."' and count ={$bonus_exc_layer} and allowance_name = '{$code}' ";
    $dupl_check_sql = "select mb_id from {$check_target} where count ={$bonus_exc_layer} and allowance_name = '{$code}' ";
    $get_today = sql_fetch( $dupl_check_sql);

    if($get_today['mb_id']){
        alert($bonus_day.' '.$code." 수당은 이미 지급되었습니다.");
        die;
    }
    
}

/*수당지급조건*/
if($pre_setting['layer'] != ''){
    $pre_condition = ' and '.$pre_setting['layer'];
    $pre_condition_in = $pre_setting['layer'];
}else{
    $pre_condition_in = ' mb_level < 9 and mb_rate > 0';
}


// 지난주 날짜 구하기 
$today=$bonus_day;
$timestr        = strtotime($today);

$week           = date('w', strtotime($today));
$weekfr         = $timestr - ($week * 86400);
$weekla         = $weekfr + (6 * 86400);

$week_frdate    = date('Y-m-d', $weekfr - (86400 * 6)); // 지난주 시작일자
$week_todate    = date('Y-m-d', $weekla - (86400 * 6)); // 지난주 종료일자




function bonus_pick($val,$category = ''){    
    global $g5;
    $pick_sql = "select * from {$g5['bonus_config']} where code = '{$val}' ";
    $list = sql_fetch($pick_sql);

    if($category == 'name'){
        return $list['name'];
    }else{
        return $list;
    }
}

function bonus_source_tx($bonus_source){
    if($bonus_source == 1){
        $bonus_source_tx = '추천 계보';
    }else if($bonus_source == 2){
        $bonus_source_tx = '후원(바이너리) 계보';
    }else{
        $bonus_source_tx='';
    }
    return $bonus_source_tx;
}

function bonus_layer_tx($bonus_layer){
    if($bonus_layer == '' || $bonus_layer == '0'){
        $bonus_layer_tx = '전체지급';
    }else{
        $bonus_layer_tx = $bonus_layer.'단계까지 지급';
    }
    return $bonus_layer_tx;
}

function bonus_limit_tx($bonus_limit){
    if($bonus_limit == '' || $bonus_limit == 0){
        $bonus_limit_tx = '상한제한없음';
    }else{
        $bonus_limit_tx = (Number_format($bonus_limit*100)).'% 까지 지급';
    }
    return $bonus_limit_tx;
}


/* 수당초과 계산 */
function bonus_limit_check($mb_id,$bonus,$direct_LR = 0){
    global $bonus_limit,$config;

    if($bonus_limit == 0){
        $bonus_limit = 100;
    }

    // $mem_sql="SELECT mb_balance, mb_rate,(SELECT SUM(benefit) FROM soodang_pay WHERE mb_id ='{$mb_id}' AND DAY = '{$bonus_day}') AS b_total FROM g5_member WHERE mb_id ='{$mb_id}' ";
    $mem_sql="SELECT mb_balance, mb_rate, mb_pv, mb_save_point, mb_3 FROM g5_member WHERE mb_id ='{$mb_id}' ";
    $mem_result = sql_fetch($mem_sql);

    $mb_balance = $mem_result['mb_balance'];
    // echo "<br>";
    // echo "수당한계값 : ".$bonus_limit;
    // echo "<br>";

    $mb_pv = $mem_result['mb_pv'] * $bonus_limit;
    
    if($mb_pv > 0 ){
        if( ($mb_balance + $bonus) < $mb_pv){
            
            $mb_limit = $bonus;
        }else{
            $mb_limit = $mb_pv - $mb_balance;
            if($mb_limit < 0){
                $mb_limit = 0;
            }
        }
    }else{
        $mb_limit = 0;
    }
    
    if($direct_LR == 1){
        return array($mb_balance,$mb_pv,$mb_limit,$mem_result['mb_pv'],$mem_result['mb_3']);
    }
    return array($mb_balance,$mb_pv,$mb_limit,$mem_result['mb_pv']);
}



function it_item_return($it_id,$func){
    $sql = " SELECT * from g5_shop_item WHERE it_id = '{$it_id}' ";
    $result = sql_fetch($sql);

    if($result){
        return $result['it_'.$func];
    }else{
        return 0;
    }
}

function soodang_record($mb_id, $code, $bonus_val,$rec,$rec_adm,$bonus_day,$exc_count=0,$od_select = 0){
    global $g5,$debug,$now_datetime;

    $soodang_sql = " insert `{$g5['bonus']}` set day='".$bonus_day."'";
    $soodang_sql .= " ,mb_id			= '".$mb_id."'";
    $soodang_sql .= " ,allowance_name	= '".$code."'";
    $soodang_sql .= " ,benefit		=  ".$bonus_val;	
    $soodang_sql .= " ,rec			= '".$rec."'";
    $soodang_sql .= " ,rec_adm		= '".$rec_adm."'";
    $soodang_sql .= " ,count		= '".$exc_count."'";
    $soodang_sql .= " ,schedule		= '".$od_select."'";
    $soodang_sql .= " ,datetime		= '".$now_datetime."'";

    

    // 수당 푸시 메시지 설정
    /* $mb_push_data = sql_fetch("SELECT fcm_token,mb_sms from g5_member WHERE mb_id = '{$mb_id}' ");
    $push_agree = $mb_push_data['mb_sms'];
    $push_token = $mb_push_data['fcm_token'];

    $push_images = G5_URL.'/img/marker.png';
    if($push_token != '' && $push_agree == 1){
        setPushData("[DFINE] - ".$mb_id." 수당 지급 ", $code.' =  +'.$bonus_val.' ETH', $push_token,$push_images);
    } */
    
    if($debug){
        echo "<code>";
        print_r($soodang_sql);
        echo "</code>";
        return true;
    }else{
        return sql_query($soodang_sql);
    }
}

function soodang_extra($mb_id, $code, $bonus_val,$rec,$rec_adm,$bonus_day){
    global $g5,$debug,$now_datetime;

    $soodang_sql = " insert `soodang_extra` set day='".$bonus_day."'";
    $soodang_sql .= " ,mb_id			= '".$mb_id."'";
    $soodang_sql .= " ,allowance_name	= '".$code."'";
    $soodang_sql .= " ,benefit		=  ".$bonus_val;	
    $soodang_sql .= " ,rec			= '".$rec."'";
    $soodang_sql .= " ,rec_adm		= '".$rec_adm."'";
    $soodang_sql .= " ,datetime		= '".$now_datetime."'";
    sql_query($soodang_sql);
}


$bonus_row = bonus_pick($code);

if($bonus_row['limited'] > 0){
    $bonus_limit = $bonus_row['limited']/100;
}else{
    $bonus_limit = $bonus_row['limited'];
}
$bonus_limit_tx = bonus_limit_tx($bonus_limit);


if(strpos($bonus_row['rate'],',')>0){
    $bonus_rates = explode(',',$bonus_row['rate']);
}else{
    $bonus_rate = $bonus_row['rate']*0.01;
}

$bonus_source = $bonus_row['source'];
$bonus_source_tx = bonus_source_tx($bonus_source);


if(strpos($bonus_row['layer'],',')>0){
    $bonus_layer = explode(',',$bonus_row['layer']);
}else{
    $bonus_layer = $bonus_row['layer'];
}
$bonus_layer_tx = bonus_layer_tx($bonus_layer);



if(strpos($bonus_row['bonus_condition'],',')>0){
    $bonus_condition = explode(',',$bonus_row['bonus_condition']);
}else{
    $bonus_condition = $bonus_row['bonus_condition'];
}





function get_shop_item($table=null){
	$array = array();
	$sql = "SELECT * FROM g5_shop_item";
	$sql .= " WHERE it_use > 0 ORDER BY it_order";

	if($table != null){
		$table = strtoupper($table);
		$sql .= " WHERE it_use > 0 AND it_name='{$table}'";
	}
	
	$result = sql_query($sql);

	while($row = sql_fetch_array($result)){
		array_push($array,$row);
	}

	return $array;
}

function ordered_items($mb_id, $table=null){

	$item = get_shop_item($table);

	$upgrade_array = array();
	for($i = 0; $i < count($item); $i++){

		if($table != null){
			$name_lower = $table;
		}else{
			$name_lower = strtolower($item[$i]['it_name']);
		}
	
		$sql = "SELECT * FROM package_".$name_lower." WHERE mb_id = '{$mb_id}' AND promote = 0";
		$result = sql_query($sql);

		for($j = 0; $j < sql_num_rows($result); $j++){
			$row = sql_fetch_array($result);

			$order_sql = "SELECT * FROM g5_shop_order WHERE od_id = '{$row['od_id']}'";
			$order_row = sql_fetch($order_sql);

			array_push($upgrade_array, array(
				"it_id" => $item[$i]['it_id'],
				"it_name" => $item[$i]['it_name'],
				"it_price" => $item[$i]['it_price'],
                "it_cust_price" => $item[$i]['it_cust_price'],
                "it_point" => $item[$i]['it_point'],
				"it_maker" => $item[$i]['it_maker'],
				"it_supply_point" => $item[$i]['it_supply_point'],
				"it_option_subject" => $item[$i]['it_option_subject'],
				"it_supply_subject" => $item[$i]['it_supply_subject'],
				"od_cart_price" => $order_row['od_cart_price'],
				"upstair" => $order_row['upstair'],
				"pv" => $order_row['pv'],
				"od_time" => $order_row['od_time'],
				"od_settle_case" => $order_row['od_settle_case'],
				"row" => $row
				));

		}
	}

	return $upgrade_array;
}


// 배열키찾기 
function array_key($list,$code,$column){
	$key = array_search($code,array_column($list,$column));
	return $key;
}

if(!sql_query("SELECT * from `soodang_mining`")){
$mining_table = sql_query("CREATE table if not exists `soodang_mining`(
    `no` int(11) NOT NULL AUTO_INCREMENT,
    `day` date DEFAULT NULL COMMENT '지급일',
    `allowance_name` varchar(30) CHARACTER SET utf8 DEFAULT NULL COMMENT '수당명',
    `mb_id` varchar(30) CHARACTER SET utf8 DEFAULT NULL COMMENT '회원아이디',
    `mining` double DEFAULT NULL COMMENT '지급받은수당',
    `rate` double DEFAULT NULL COMMENT '지급률',
    `rec` text CHARACTER SET utf8 DEFAULT NULL COMMENT '메모',
    `rec_adm` text DEFAULT NULL COMMENT '관리자메모',
    `datetime` datetime NOT NULL COMMENT '지급실행일',
    PRIMARY KEY (`no`),
    KEY `mb_id` (`mb_id`)
  )");
}

$mining_data = bonus_pick('mining');
$mining_rate = $mining_data['rate'];

// 마이닝 컬럼 확인 
$pre_sql = sql_fetch("SHOW COLUMNS FROM g5_member WHERE `Field`= '{$mining_target}' ");

if(!$pre_sql && $minings[0] != ''){
    $sql = "ALTER TABLE g5_member ADD COLUMN `{$mining_target}` DOUBLE NULL DEFAULT '0' AFTER `mb_shift_amt`,
    ADD COLUMN `{$mining_amt_target}` DOUBLE NULL DEFAULT '0'  AFTER `{$mining_target}` ";
    echo $sql;
    sql_query($sql);
}


// 마이닝
function mining_record($mb_id, $code, $bonus_val,$bonus_rate,$currency, $rec,$rec_adm,$bonus_day){
    global $g5,$debug,$now_datetime,$mining_rate;

    $soodang_sql = " insert `soodang_mining` set day='".$bonus_day."'";
    $soodang_sql .= " ,mb_id			= '{$mb_id}' ";
    $soodang_sql .= " ,allowance_name	= '{$code}' ";
    $soodang_sql .= " ,mining		=  {$bonus_val} ";
    $soodang_sql .= " ,currency		=  '{$currency}' ";
    $soodang_sql .= " ,rate		=  {$bonus_rate} ";	
    $soodang_sql .= " ,global_rate		=  {$mining_rate} ";	
    $soodang_sql .= " ,rec			= '{$rec}' ";
    $soodang_sql .= " ,rec_adm		= '{$rec_adm}' ";
    $soodang_sql .= " ,datetime		= '{$now_datetime}' ";
    
    if($debug){
        echo "<code>";
        print_r($soodang_sql);
        echo "</code>";
        return true;
    }else{
        return sql_query($soodang_sql);
    }
}



/* 마이닝 수당초과 계산 */
function mining_limit_check($mb_id,$bonus){
    global $bonus_limit,$config,$mining_target,$today;

    if($bonus_limit == 0){
        $bonus_limit = 100;
    }
    
    $mem_sql="SELECT mb_balance, mb_rate, mb_save_point, {$mining_target},
    (SELECT SUM(mining) from soodang_mining AS B  WHERE B.mb_id = A.mb_id AND day='{$today}') AS daily_mining 
    FROM g5_member AS A WHERE mb_id ='{$mb_id}' ";
    
    $mem_result = sql_fetch($mem_sql);


    $mb_mining = $mem_result[$mining_target];
    $mb_pv = $mem_result['daily_mining'] * $bonus_limit;
    
    /* if($mb_id == 'admin' || $mb_id == $config['cf_admin']){
        $mb_pv = 100000000000;
        $admin_cash = 1;
    } */

    if($mb_pv > 0 ){
        if( ($mb_mining + $bonus) < $mb_pv){
            
            $mb_limit = $bonus;
        }else{
            
            $mb_limit = $mb_pv - $mb_mining;
            if($mb_limit < 0){
                $mb_limit = 0;
            }
        }
    }else{
        $mb_limit = 0;
    }
    
    return array($mb_mining,$mb_pv,$mb_limit,$admin_cash);
}

// 주문 내역 업데이트  
function order_update($od_id,$benefit_limit){
    global $debug;

    $origin_order = sql_fetch("SELECT * FROM g5_shop_order WHERE od_id = {$od_id}");
    $item_no = $origin_order['no'];

    if($origin_order){
        $update_order = "UPDATE g5_shop_order set pay_count = pay_count + 1, pay_acc = pay_acc + {$benefit_limit} WHERE no = {$item_no} ";
        
        if($debug){
            echo "<code>";
            print_r($update_order);
            echo "</code>";
            return true;
        }else{
            return sql_query($update_order);
        }
        
    }else{
        return false;
    }

}

// 원 표시
function shift_kor($val){
	return Number_format($val, 0);
}

// 달러 표시
function shift_doller($val){
	return Number_format($val, 2);
}

// 코인 표시
function shift_coin($val){
	return Number_format($val, COIN_NUMBER_POINT);
}

// 소수점 지수-상수 변환표시 
function point_number($val){
	return sprintf('%f',$val);
}

// 달러 , ETH 코인 표시
function shift_auto($val,$coin = '원'){
	if($coin == '$'){
		return shift_doller($val);
	}else if($coin == '원'){
		return shift_kor($val);
	}else{
		return shift_coin($val);
	}
}



// Not zero express
function zero_value($val){
    if($val != 0){
        return 'strong f_blue';
    }
}


if( !function_exists( 'array_column' ) ):
    
    function array_column( array $input, $column_key, $index_key = null ) {
    
        $result = array();
        foreach( $input as $k => $v )
            $result[ $index_key ? $v[ $index_key ] : $k ] = $v[ $column_key ];
        
        return $result;
    }
endif;

include_once(G5_PATH."/util/core.schedule.php");
?>