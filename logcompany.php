<?
define('CONFIG_TITLE','THE KHAN MINE');
define('CONFIG_SUB_TITLE','THE KHAN MINE');

// 메일설정
define('CONFIG_MAIL_ACCOUNT','willsoftkr');
define('CONFIG_MAIL_PW','willsoft0780');
define('CONFIG_MAIL_ADDR','willsoftkr@gmail.com');

// 기준통화설정
define('DEPOSIT_CURENCY','원');
define('ASSETS_CURENCY','$');
define('PURCHASE_CURENCY','$');
define('BALANCE_CURENCY','$');
define('WITHDRAW_CURENCY','원');


$minings = ['eth'];
$mining_hash = ['hp/s'];
$mining_target = 'mb_mining_1';
$mining_amt_target = 'mb_mining_1'.'_amt';

define('DEPOSIT_NUMBER_POINT',0); // 입금단위
define('ASSETS_NUMBER_POINT',2); // 정산 단위
define('BONUS_NUMBER_POINT',2); // 수당계산,정산기준단위
define('COIN_NUMBER_POINT',8); // 코인 단위


// 이더사용 및 회사지갑 설정
// False 설정시 현금사용
define('USE_WALLET',TRUE);
define('eth_ADDRESS','0x123456789123456789564513');
define('ltc_ADDRESS','0xabcd1234567895612356789');


define('NATION_USE',TRUE);

//영카트 로그인체크 주소
if(strpos($_SERVER['HTTP_HOST'],"localhost") !== false){
    $port_number = "";
    define('SHOP_URL',"http://localhost:{$port_number}/bbs/login_check.php");
}else{
    define('SHOP_URL',"http://khanshop.willsoft.kr/bbs/login_check.php");
}

?>
