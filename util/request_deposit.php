<?php
include_once('./_common.php');
include_once(G5_THEME_PATH.'/_include/wallet.php');
include_once(G5_LIB_PATH.'/Telegram/telegram_api.php');

login_check($member['mb_id']);

// 입금처리 PROCESS
//  $debug = 1;

/*현재시간*/
$now_datetime = date('Y-m-d H:i:s');
$now_date = date('Y-m-d');

$mb_id = $_POST['mb_id'];
$txhash = $_POST['hash'];
$coin = $_POST['coin'];
$d_price = $_POST['d_price'];
   
// if($debug){
//   $mb_id ='admin';
//   $coin ='FIL';
//   $txhash ='bafy2bzacedadg6yufns42ibfuxm6qcqzsb4u2ykqbv5br2y7d2zbbkcwtyipq';
//   $d_price ='0';
// }

if(strpos($txhash_1,'/')){
  $char_count = array();
  $char_count = explode('/',$txhash_1);
  $ary_cnt = count($char_count);
  $txhash =  $char_count[$ary_cnt-1];
}

if(DEPOSIT_CURENCY != ASSETS_CURENCY){
  $in_price = shift_price($d_price,$coin,ASSETS_CURENCY);
}

/*기존건 확인*/
$pre_result = sql_fetch("SELECT count(*) as cnt from `{$g5['deposit']}` 
WHERE mb_id ='{$mb_id}' AND create_d = '{$now_date}' AND in_amt = {$d_price} ");

if($pre_result['cnt'] < 1){
  $sql = "INSERT INTO `{$g5['deposit']}`(mb_id, txhash, create_dt,create_d,status,coin,amt,in_amt) 
  VALUES('$mb_id','$txhash','$now_datetime','$now_date',0,'$coin', '$d_price','$in_price')";
  
  if($debug){
    echo "<br><br>";
    print_R($sql);
    $result = 1;
  }else{
    $result = sql_query($sql);
  }

  // 입금알림 텔레그램 API
  // curl_tele_sent('[SAMWOO][입금요청] '.$mb_id.'('.$txhash.') 님의 '.Number_format($d_price).'입금요청이 있습니다.');

  if($result){
    echo json_encode(array("response"=>"OK", "data"=>'complete'));
  }else{
    echo json_encode(array("response"=>"FAIL", "data"=>"<p>ERROR<br>Please try later</p>"));
  }
}else{
  echo json_encode(array("response"=>"FAIL", "data"=>"Request already exists"));
}


?>
