<?
	include_once('./_common.php');
	
	include_once(G5_THEME_PATH.'/_include/gnb.php'); 
    include_once(G5_THEME_PATH.'/_include/wallet.php'); 
	include_once(G5_THEME_PATH.'/_include/shop.php');
	

	//login_check($member['mb_id']);

    //print_r($_POST);
    //echo "<br><br>";
    //print_r($member);
    
    $now_date = date('Y-m-d H:i:s');

    if(!$_POST['b_it_id'] && !$_POST['q_it_id']){
        alert("error :: no_item");
        return;
    }

    if($_POST['b_it_id']){
        $order_B = get_shop_item($_POST['b_it_id']); // B팩

        /*B팩 구매내역 있다면*/
        if($_POST['expire_date_b'] && $_POST['b_it_name'] == $member['it_pool1'] ){
            $order_date_b = $_POST['expire_date_b'];
        }else{
            $order_date_b = G5_TIME_YMDHIS;
        }

        $expire_date_b = date("Y-m-d", strtotime($order_date_b."+1 month"));

        $B_sql =  ",it_pool1 =  '$order_B[it_name]' ,
                   it_pool1_profit =  '".$expire_date_b."',
                   b_autopack = '$_POST[b_it_auto]' ";
    }

    if($_POST['q_it_id']){
        $order_Q = get_shop_item($_POST['q_it_id']); // Q팩
         /*Q팩 구매내역 있다면*/
        if($_POST['expire_date_q'] && $_POST['q_it_name'] == $member['it_pool2']){
            $order_date_q = $_POST['expire_date_q'];
        }else{
            $order_date_q = G5_TIME_YMDHIS;
        }
        $expire_date_q = date("Y-m-d", strtotime($order_date_q."+1 month"));

        $Q_sql =  ",it_pool2 =  '$order_Q[it_name]' ,
                   it_pool2_profit =  '".$expire_date_q."',
                   q_autopack = '$_POST[q_it_auto]' ";
    }
    
    $order_price = $_POST['order_price']; // 주문 총금액
	
    /*멤버 btc차감 | 팩구매기록 */
    if($btc_account > $order_price){

        $sql = "UPDATE g5_member set 
                mb_btc_calc = round((mb_btc_calc - '$order_price'),8)"; 

                if($B_sql){
                    $sql.=   $B_sql;  
                }
                if($Q_sql){
                    $sql.=   $Q_sql;  
                }
               
        $sql .= "where mb_id = '$member['mb_id']' ";
        $mem_result = sql_query($sql, false);
        //echo "<br>".$sql."<br><br>";
    }


    /*주문로그*/
    $cnt= $_POST['cnt'];
    $orderid = date("YmdHis",time()).$cnt.'1';
    $mb_id = $member['mb_id'];
    $coin = $_POST['coin'];
    
    if($B_sql){
        $cp_price = calc_price($order_B['it_price'], $btc_cost,'btc');
        $sql = "insert g5_shop_cart set
		od_id				= '".$orderid."'
		, mb_id             = '".$mb_id."'
		, it_id     = '{$order_B['it_id']}'
        , it_name     = '{$order_B['it_name']}'
        , it_sc_type    ='10'
        , it_sc_method    ='".$coin."'
        , ct_status         = '구매'
        , ct_price         = '".$order_price."'
        , ct_point         = '".$cp_price."'
        , cp_price         = '".$cp_price."'
        , io_price         = '{$order_B['it_price']}'
        , ct_qty         = '".$cnt."'
        , ct_option         = '{$_POST['b_it_auto']}'
        , ct_time         = '".$order_date_b."'
        , ct_ip         = '{$_SERVER['REMOTE_ADDR']}'
        , ct_select_time         = '".$expire_date_b."' ";
        
        $order_result = sql_query($sql, false);
        //echo $sql."<br><br>";
    }

    if($Q_sql){
        $cp_price = calc_price($order_Q['it_price'], $btc_cost,'btc');
        $sql = "insert g5_shop_cart set
		od_id				= '".$orderid."'
		, mb_id             = '".$mb_id."'
		, it_id     = '{$order_Q['it_id']}'
        , it_name     = '{$order_Q['it_name']}'
        , it_sc_type    ='20'
        , it_sc_method    ='".$coin."'
        , ct_status         = '구매'
        , ct_price         = '".$order_price."'
        , ct_point         = '".$cp_price."'
        , cp_price         = '".$cp_price."'
        , io_price         = '{$order_Q['it_price']}'
        , ct_qty         = '".$cnt."'
        , ct_option         = '{$_POST['q_it_auto']}'
        , ct_time         = '".$order_date_q."'
        , ct_ip         = '{$_SERVER['REMOTE_ADDR']}'
        , ct_select_time         = '".$expire_date_q."' ";
            
        $order_result = sql_query($sql, false);
        //echo $sql."<br><br>";
    }
?>


	<script>
		$(function() {
			$(".top_title h3").html("<img src='<?=G5_THEME_URL?>/_images/top_purchase.png' alt='아이콘'> <span data-i18n='title.팩상품구매하기'> Purchase Packs </span>");
			

        });
        
        $( document ).ready(function(){
            //console.log('sendmail');
            var mem_result = "<?= $mem_result?>";
            var order_result = "<?= $order_result?>";

            if(mem_result && order_result){
                dimShow();
                purchaseModal('Confirm Payment','<p>Your payment completed successfully</p>','success');
                $('#purchaseModal #modal_return_url').on('click', function () {
					//console.log('ok');
					document.location.href = "<?=G5_URL?>/page.php?id=purchase_order_end";
                });

            }else{
                dimShow();
                purchaseModal('ERROR','<p>Plese Try Again</p>','failed');
                $('#purchaseModal #modal_return_back').on('click', function () {
					//console.log('ok');
					history.back();
                });
            }
        });
	</script>


<? include_once(G5_THEME_PATH.'/_include/tail.php'); ?>
