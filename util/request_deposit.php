<?php
include_once('./_common.php');
include_once(G5_LIB_PATH.'/Telegram/telegram_api.php');

/*현재시간*/
$now_datetime = date('Y-m-d H:i:s');
$now_date = date('Y-m-d');

$mb_id = $_POST['mb_id'];
$txhash = $_POST['hash'];
$coin = $_POST['coin'];
$d_price = $_POST['d_price'];

/*기존건 확인*/
$pre_result = sql_fetch("SELECT count(*) as cnt from wallet_deposit_request WHERE mb_id ='{$mb_id}' AND create_d = '{$now_date}' AND in_amt = {$d_price} ");

if($pre_result['cnt'] < 1){
  $sql = "INSERT INTO wallet_deposit_request(mb_id, txhash, create_dt,create_d,status,coin,amt,in_amt) VALUES('$mb_id','$txhash','$now_datetime','$now_date',0,'$coin', '$d_price','$d_price')";
  $result = sql_query($sql);

  curl_tele_sent('[KHAN][입금요청] '.$mb_id.'('.$txhash.') 님의 '.Number_format($d_price).'입금요청이 있습니다.');

  if($result){
    echo json_encode(array("response"=>"OK", "data"=>$sql));
  }else{
    echo json_encode(array("response"=>"FAIL", "data"=>"<p>ERROR<br>Please try later</p>"));
  }
}else{
  echo json_encode(array("response"=>"FAIL", "data"=>"Request already exists"));
}


?>