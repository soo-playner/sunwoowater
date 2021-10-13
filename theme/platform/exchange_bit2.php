<?
include_once(G5_THEME_PATH.'/_include/head.php'); 
include_once(G5_THEME_PATH.'/_include/gnb.php'); 

include_once(G5_THEME_PATH.'/_include/wallet.php'); 

/*
$order_sql = "select * from g5_shop_order where mb_id = '".$member['mb_id']."'";
$order_result = sql_query($order_sql);
*/
//$order_list = sql_fetch($order_sql);
?>
		<section class="v_center1 exchange_wrap">
			<div class="color_block v7_block">
				<div class="clear_fix">
					<strong class="f_left">V7</strong>
					<b class="f_right"><?=$v7_rate?> USD</b>
				</div>
				<div class="clear_fix">
					<span class="f_left">=<?=$v7_cost?> USD</span>
					<p class="f_right"><?=$v7_account?> V7</p>
				</div>
			</div>
			<img src="<?=G5_THEME_URL?>/_images/exchange_arrow.gif" alt="변환">
			<div class="color_block bit_block">
				<div class="clear_fix">
					<strong class="f_left">Bitcoin</strong>
					<b class="f_right"><?=$btc_rate?> USD</b>
				</div>
				<div class="clear_fix">
					<span class="f_left">=<?=$btc_cost?> USD</span>
					<p class="f_right"><?=$btc_account?> BTC</p>
				</div>
			</div>


			<form action="">
				<ul class="money_chage clear_fix mc_bit">
					<li>
						<p data-i18n="wallet.환전할 금액">Amount to exchange</p>
						<input type="number" id="amount" placeholder="<?=$v7_account?>" min="0" max="<?=$v7_account_num?>"  >
						<span>V7</span>
					</li>
					<li>=</li>
					<li>
						<p class="font_red" data-i18n="wallet.전부 환전하기">Exchange ALL</p>
						<input type="number" id="exchange_amount" placeholder="0" disabled >
						<span>BTC</span>
					</li>
				</ul>

				<ul class="send_con">
					<li class="clear_fix">
						<span class="send_title" data-i18n="wallet.환전 수수료">Exchange fee</span>
						<p class="f_right"><span class="font_gray" id="exchange_fee">0</span> BTC</p>
					</li>
					<li class="clear_fix">
						<span class="send_title" data-i18n="wallet.합계">Total</span>
						<p class="f_right"><span id="exchange_total">0</span> BTC</p>
					</li>
				</ul>

			</form>

		</section>

		<div class="btn_block_btm_wrap">
			<input type="button" value="Exchange" class="btn_basic" id="exchange" data-i18n="[value]wallet.환전하기">
			<!--<input type="button" value="Exchange" class="btn_basic exchange_cancel_pop_open pop_open" data-i18n="[value]wallet.환전하기">-->
			<p data-i18n="wallet.warning.A">Warning: This order can't be undone.</p>
		</div>


		<div class="gnb_dim"></div>
	</section>



	<script>
		$(function() {
			$(".top_title h3").html("<img src='<?=G5_THEME_URL?>/_images/top_exchange.png' alt='아이콘'> 비트코인으로 환전하기");
		});
	</script>

	
<script>
			
			var v7_account = Number("<?=$v7_account_num?>");
			var v7_cost = "<?=$v7_cost?>";
			var btc_cost = Number("<?=$btc_cost_num?>");
			var btc_cost_plus = 
			//var btc_cost_1 = 10000.00;
			
			function exchage_result(val) {
				//var shiftbtc = Number( v7usd / btc_cost );
				//$exchage_cost = 100+(100*3/100);
				//return Number($exchage_cost*val).toFixed(2);
				return val;
			}

			$('#amount').on('change',function(){
				
				console.log(this.value +" / "+ v7_account);

				if(this.value > v7_account){
					commonModal('check input amount','<strong> out of the maximum amount. </strong>',80);
					return false;
				}

				var rate = this.value;
				var v7usd = rate * v7_cost; //달러 환산

				var shifted = Number( v7usd / btc_cost).toFixed(8); //total
				var shift_left = Number( (v7usd /btc_cost)*0.97).toFixed(8); //exchage
				var shift_fee = Number( (v7usd /btc_cost)*0.03).toFixed(8); // fee

				//console.log(v7usd+" | "+ shifted);

				$('#exchange_amount').val(shift_left );
				$('#exchange_fee').html(shift_fee);
				$('#exchange_total').html(shifted);
			});

			
			$('#exchange').on('click', function(){
				var amount = $('#amount').val();
				var exchange_amount = $('#exchange_amount').val();
				var exchange_fee = $('#exchange_fee').text();
				var exchange_total = $('#exchange_total').text();
				var mb_id = "<?=$member['mb_id']?>";

				console.log(amount +" / "+ exchange_amount);

				if(amount > v7_account){
					commonModal('check input amount','<strong> out of the maximum amount. </strong>',80);
					return false;
				}
				else{
					
					$.ajax({
						type: "POST",
						url: "/util/exchange_proc.php",
						cache: false,
						async: false,
						dataType: "json",
						data:  {
							"mb_id" : mb_id,
							"account" : v7_account,
							"amount" : amount,
							"exchange": exchange_amount,
							"fee": exchange_fee,
							"exchange_total": exchange_total,
							"coin_cost" : btc_cost,
							"source" : "v7",
							"coin" : "btc",
							"type" : "exchage"
						},
						success: function(data) {
							
							commonModal('Congratulation! Complete Deposit','<strong> Congratulation! Complete Deposit.</strong>',80);	
							$('#closeModal').on('click', function(){
								location.reload();
							});
						},
						error:function(e){
							commonModal('Error!','<strong> Please check retry.</strong>',80);	
						}
					});
					
				}
			});
		


		$(function() {
			$(".top_title h3").html("<img src='<?=G5_THEME_URL?>/_images/top_deposit.png' alt='아이콘'><span data-i18n='title.Deposit with Bitcoin'> 비트코인 입금</span>");
			
		});
	</script>
	
	

<? include_once(G5_THEME_PATH.'/_include/tail.php'); ?>