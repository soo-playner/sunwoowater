<?php
include_once('./_common.php');
include_once(G5_CAPTCHA_PATH.'/captcha.lib.php');
include_once(G5_LIB_PATH.'/register.lib.php');
include_once(G5_LIB_PATH.'/mailer.lib.php');

include_once(G5_THEME_PATH.'/_include/wallet.php');


?>

<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/2.2.4/jquery.min.js"></script>
<?
include_once(G5_THEME_PATH.'/_include/head.php');
include_once(G5_THEME_PATH.'/_include/gnb.php');
include_once(G5_THEME_PATH.'/_include/popup.php');
include_once(G5_PATH.'/util/recommend.php');


// 테스팅 
/* $_POST['nation_number'] = '81';
$_POST['mb_recommend'] = 'arcthan';
$_POST['mb_name'] = '한은수4';
$_POST['mb_id'] = 'arcthan4';
$_POST['mb_email'] = 'arcthan@naver.com';
$_POST['mb_hp'] = '01090009000';
$_POST['mb_addr1'] = '서울 강남구 테헤란로5길 25';
$_POST['mb_addr2'] = '602호 (코너스톤)';
$_POST['bank_name'] = '국민은행';
$_POST['account_name'] = '한은수';
$_POST['bank_account'] = '123456789123456';
$_POST['mb_password'] = 'zx235689';
$_POST['mb_password_re'] = 'zx235689';
$_POST['reg_tr_password'] = '235689';
$_POST['reg_tr_password_re'] = '235689';
$_POST['term'] = 'on';
$_POST['term'] = 'on'; */

$mb_id = 'arcthan3';
	
    // 메일인증
    /* $recent_sql = "SELECT id FROM auth_email WHERE email='{$mb_email}' ORDER BY id DESC LIMIT 0,1";
    $row = sql_fetch($recent_sql);

    $update_sql = "UPDATE auth_email set auth_check = '2' WHERE email = '{$mb_email}' AND id = {$row['id']}";
    sql_query($update_sql); */
    // $mb_center = return_org_member($mb_id,2);
    // 센터찾기 
    $mb_center = return_org_member($mb_id,2);
    
    // 지점찾기 
    $mb_jijum = return_org_member($mb_id,3);
    
    // 지사찾기 
    $mb_jisa = return_org_member($mb_id,4);
    
    // 본부찾기 
    $mb_bonbu = return_org_member($mb_id,5);
    
    $update_org_sql = "UPDATE g5_member set mb_center = '{$mb_center}', mb_jijum = '{$mb_jijum}', mb_jisa = '{$mb_jisa}', mb_bonbu = '{$mb_bonbu}' WHERE mb_id = '{$mb_id}' ";
    
    echo "<br><br>";
    print_R($update_org_sql);

?>
