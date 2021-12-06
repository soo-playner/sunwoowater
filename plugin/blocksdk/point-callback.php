<?php
include_once("./_common.php");
include_once(G5_THEME_PATH.'/_include/wallet.php');

// $debug =1;

$json_text = get_text(json_encode($_REQUEST));
$sql = "insert into blocksdk_callback_log(`json`) values('{$json_text}')";
if(!$debug){
	sql_query($sql);
}


$coin      = $_REQUEST["category"];
$api_token = $_REQUEST["api_token"];
$event 	   = $_REQUEST["event"];
$tx_hash   = get_text($_REQUEST["tx_hash"]);
$inaddr    = get_text($_REQUEST["address"]);

$now_datetime = date('Y-m-d H:i:s');
$now_date = date('Y-m-d');

/* if($debug){
	$coin = 'eth';
	$api_token = 'BsokFsOPhUSbtMVnmgTJYSNyP9VdrkoynMEb7ag9';
	$event = 'confirmed';
	$tx_hash = '0x79acdefb928f2054320512200e56c91360aec089a0e99d877ad2dba3b1eb1e6b';
	$inaddr = '0xbb51cebac915a0f3df1ce73f653d2b2c2fd781c9';
} */



if($api_token != Crypto::GetToken()){
	//틀린 토큰값
	echo "token !=";
	exit_response(array(
		"code" => 400,
		"data" => array()
	));
}else if($coin != 'btc' && $coin != 'bch' && $coin != 'ltc' && $coin != 'eth' && $coin != 'dash'){
	//지원하지않는 코인
	echo "coin !=";
	exit_response(array(
		"code" => 400,
		"data" => array()
	));
}else if(empty(Crypto::IsReceiveTx($tx_hash)) == false){
	//이미 처리된 거래
	echo "tx !=";
	exit_response(array(
		"code" => 400,
		"data" => array()
	));
}else if($event != "confirmed"){
	//언컨펌 거래
	echo "confirmed !=";
	exit_response(array(
		"code" => 400,
		"data" => array()
	));
}


sleep(30);

if($coin == 'btc')
	$where_sql = "mb_10='{$inaddr}'";
else if($coin == 'bch')
	$where_sql = "mb_10='{$inaddr}'";
else if($coin == 'ltc')
	$where_sql = "ltc_wallet='{$inaddr}'";
else if($coin == 'eth')
	$where_sql = "eth_wallet='{$inaddr}'";
else if($coin == 'dash')
	$where_sql = "mb_10='{$inaddr}'";

$sql = "SELECT * FROM {$g5['member_table']} WHERE {$where_sql}";
$member_data = sql_fetch($sql);



if(empty($member_data) == true){
	echo "member !=";
	exit_response(array(
		"code" => 400,
		"data" => array()
	));
}

$blocksdk_conf = Crypto::GetConfig();
$receiving_address = Crypto::GetReceivingAddress();

$client = Crypto::GetClient($coin);


$rawTx_payload = $client->getTransaction([
	"hash" => $tx_hash
]);
$rawTx = $rawTx_payload['payload'];



if($coin == 'eth'){
	if($rawTx['from'] == $inaddr){
		exit_response(array(
			"code" => 400,
			"data" => array()
		));
	}
	
	$ethinfo = Crypto::GetMemberEthAddress($inaddr);
	$balance = $client->getAddressBalance([
		"address" => $ethinfo['address']
	]);

	
	if($balance['payload']['balance'] < 0.005){
		exit_response(array(
			"code" => 400,
			"data" => array()
		));
	}
	
	
	$eth = $client->getBlockChain();
	
	
	
	if($eth['payload']['high_gwei'] < 240){
		$gwei = $eth['payload']['high_gwei'];
	}

	if($eth['payload']['high_gwei'] >= 240){
		$gwei = $eth['payload']['medium_gwei'];
	}

	if($gwei = $eth['payload']['medium_gwei'] > 220){
		$gwei = $eth['payload']['low_gwei'];
	}

	/* if($gwei = $eth['payload']['low_gwei'] > 150){
		$gwei = 150;
	} */



	$price = $gwei * 0.000000001;
	$deposit_fee = $price * 21000;
	$amount = $balance['payload']['balance'] - $deposit_fee;

	if($debug){
		$balance['payload']['balance'] = 1;
		echo "<br><br>";
		echo "from :".$ethinfo['address'];
		echo "<br>";
		echo "to :".$receiving_address[$coin];
		echo "<br>";
		echo "amount :".($amount);
		echo "<br>";
		echo "private_key :".$ethinfo['private_key'];
		echo "<br>"."gwei : ".$gwei;
		echo "<br><br>";
	}else{
		$tx = $client->sendToAddress2([
			"from" => $ethinfo['address'],
			"to" => $receiving_address[$coin],
			"amount" => $amount,
			"private_key" => $ethinfo['private_key'],
			"gwei" => $gwei,
			"gas_limit" => 21000
		]); 
		print_R($tx);

		if($tx){
			$insert_tx = "INSERT wallet_income_transfer set mb_id = '{$member_data['mb_id']}', wallet = '{$ethinfo['address']}', txid = '{$tx['payload'][hash]}',coin='{eth}',fee={$deposit_fee}, trnsfertx = '{$tx_hash}', value = {$amount}, createdAt = '{$now_datetime}' ";
			sql_query($insert_tx);
		}

		// $send_result = $client->sendTransaction(["hex" => $tx]);
	}

	// $amount = $rawTx['value'];
	$amount = $balance['payload']['balance'];

}


// $point = Crypto::GetCoinToPrice($coin, 2);

$point = $fil_price * $amount;

if($debug){
	echo "<br><br>";
	echo "amount_point :: ".$point;
	echo "<br><br>";
}

insert_point($member_data['mb_id'], $point, $coin.'-'.$tx_hash, '@passive', 'admin', $member_data['mb_id'].'-'.uniqid(''));



Crypto::InsertReceiveTx([
	"mb_no" => $member_data['mb_no'],
	"tx_hash" => $tx_hash,
	"symbol" => $coin,
	"address" => $inaddr,
	"value" => $amount,
	"create_at" => $now_datetime
]);


if($event == "confirmed"){
	$event_code = 1;
}else{
	$event_code = 2;
}

if(!$tx['error']['message']){
	$update_member_asset_sql = "UPDATE g5_member set mb_deposit_point = mb_deposit_point + {$point} WHERE mb_id = '{$member_data['mb_id']}' ";
	$update_result = sql_query($update_member_asset_sql);
}else{
	$event_code = 3;
}

$deposit_sql = "INSERT INTO `{$g5['deposit']}`(mb_id, txhash, create_dt,create_d,status,coin,amt,in_amt)";
$deposit_sql .= " VALUES('{$member_data['mb_id']}','$tx_hash','$now_datetime','$now_date','$event_code','$coin', '$amount','$point')";

if($debug){
	print_R($deposit_sql);
}else{
	sql_query($deposit_sql);
}

?>