<?
	include_once('./_common.php');
	include_once(G5_THEME_PATH.'/_include/gnb.php'); 
    include_once(G5_THEME_PATH.'/_include/wallet.php'); 
	include_once(G5_THEME_PATH.'/_include/shop.php');
	

	login_check($member['mb_id']);

    //print_r($_POST);
    //echo "<br><br>";
    //print_r($member);
    
	$shop_list = shop_history($member['mb_id']);
	$category = $_GET['stx'];

?>

	<section class="v_center purchase_1wrap wrap">

			<ul class="p1_ul clear_fix">
				<li style="width:100%;">
					<p>Purchase details</p>
					<strong><?=$member['mb_id']?></strong>
                </li>
                <!--
				<li>
					<p>시세 유효기간</p>
					<strong>13:39</strong>
                </li>
                -->
			</ul>
			<hr>	
			<table>
				<tbody>
					<tr>
						<th>PACKS</th>
						<th>PRICE</th>
                        <th>EXPIRE DATE</th>
                        <th>Auto Repurchase</th>
                    </tr>
                    <?while( $row = sql_fetch_array($shop_list) ){
                        $total += $row['io_price'];
                        $order_total = $row['ct_price'];

                    ?>
					<tr>
                        <td><?=$row['it_name']?></td>
						<td>&#36; <?=$row['io_price']?></td>
                        <td><?=$row['ct_select_time']?></td>
                        <td><?=autoYn($row['ct_option'])?></td>
                    </tr>
                    <?}?>
                </tbody>
               
				<tfoot style="border-top:1px solid #ddd;">
					<td >TOTAL:</td>
					<td >&#36;<?=$total?></td>
					<td></td>
				</tfoot>
			</table>
			<hr>	

			<div class="invoice_div">
				<!--<p><span class="font_gray">인보이스 번호 : </span>201906142155220</p>
				<ul class="pay_coin_ul clear_fix">
					<li><img src="_images/bit_round.gif" alt="비트코인"> BTC로 지불</li>
					<li><img src="_images/eth_round.gif" alt="비트코인"> ETH로 지불</li>
					<li><img src="_images/rock_round.gif" alt="비트코인"> RWD로 지불</li>
				</ul>
				-->
				<p class="font_gray">PAYMENT AMOUNT</p>
				<p><?= $order_total?> <span class="upper"><?=$coin?></span></p>
			</div>


			<div class="btn_block_btm_wrap">
				<input type="button" value="OK" class="btn_basic_block" onclick="location.href='/';">
            </div>
            
            
		</section>


        <div class="gnb_dim"></div>

    </section>

	<script>
		$(function() {
			$(".top_title h3").html("<img src='<?=G5_THEME_URL?>/_images/top_purchase.png' alt='아이콘'> <span data-i18n='title.팩상품구매하기'> Purchase Packs </span>");
			

        });
	</script>



<? include_once(G5_THEME_PATH.'/_include/tail.php'); ?>
