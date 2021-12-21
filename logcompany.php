<?
define('CONFIG_TITLE','SAMWOO SYSTEM');
define('CONFIG_SUB_TITLE','SAMWOO SYSTEM');

// 메일설정
define('CONFIG_MAIL_ACCOUNT','willsoftkr');
define('CONFIG_MAIL_PW','willsoft0780');
define('CONFIG_MAIL_ADDR','willsoftkr@gmail.com');

// 기준통화설정
define('DEPOSIT_CURENCY','원');
define('ASSETS_CURENCY','원');
define('PURCHASE_CURENCY','원');
define('BALANCE_CURENCY','원');
define('WITHDRAW_CURENCY','원');


$minings = ['DSP'];
$mining_hash = ['T'];
$mining_target = 'mb_mining_1';
$mining_amt_target = 'mb_mining_1'.'_amt';

define('DEPOSIT_NUMBER_POINT',0); // 입금단위
define('ASSETS_NUMBER_POINT',2); // 정산 단위
define('BONUS_NUMBER_POINT',2); // 수당계산,정산기준단위
define('COIN_NUMBER_POINT',4); // 코인 단위

$deposit_method = ['원'];

// 이더사용 및 회사지갑 설정
// False 설정시 현금사용
define('USE_WALLET',false);
$wallet_code = ['FIL'];
$wallet_create_code = "1 TERA MINE TEST";
$deposit_wallet_address = "1teramin_filecoin_address";


define('NATION_USE',TRUE);

//영카트 로그인체크 주소
if(strpos($_SERVER['HTTP_HOST'],"localhost") !== false){
    $port_number = "";
    define('SHOP_URL',"http://localhost:{$port_number}/bbs/login_check.php");
}else{
    define('SHOP_URL',"http://khanshop.willsoft.kr/bbs/login_check.php");
}

?>
