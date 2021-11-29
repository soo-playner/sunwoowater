<?
include_once('./_common.php');
include_once(G5_THEME_PATH.'/_include/wallet.php');
include_once(G5_LIB_PATH.'/api/filecoin/filecoin.infura.php');

$no = $_POST['uid'];

if(!$no){
    $deposit_sql = "SELECT * from wallet_deposit_request WHERE coin = 'fil' AND amt = 0 ";
    $result_deposit = sql_query($deposit_sql);
    $now_datetime = date('Y-m-d H:i:s');

    while ($row = sql_fetch_array($result_deposit)) {
        
        $tx_hash = trim($row['txhash']);
        
        if(strlen($row['txhash']) > 10){
            $fil_result = get_filecoin_message($tx_hash);
            $fil_value = 0.0001 * substr($fil_result['result']['Value'],0,4);
            $fil_amt_val = shift_auto_zero($fil_value * $fil_price,'$');

            if($fil_value > 0){
                $update_sql = "UPDATE wallet_deposit_request set amt = {$fil_value}, in_amt = {$fil_amt_val}, update_dt = '{$now_datetime}' WHERE uid = {$row['uid']} ";
                sql_query($update_sql);
            }
        }
    }

    alert('입금정보가 업데이트 되었습니다.',0);
    goto_url("/adm/adm.deposit_request.php");
}
?>