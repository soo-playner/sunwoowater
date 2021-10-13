<?
	include_once('./_common.php');
	include_once(G5_THEME_PATH.'/_include/gnb.php'); 
	include_once(G5_THEME_PATH.'/_include/wallet.php'); 
	//print_r($member);

	login_check($member['mb_id']);

	print_r($_POST);

	//$product =$_POST['b_rd'];
	//$product_auto = $_POST['q_chk'];
	//$price = shift_btc(conv_number($product)/conv_number($btc_cost);

?>
		<section class="v_center purchase_1wrap wrap">

			<ul class="p1_ul clear_fix">
				<li>
					<p>구매자 아이디</p>
					<strong>Coolrunning</strong>
				</li>
				<li>
					<p>시세 유효기간</p>
					<strong>13:39</strong>
				</li>
			</ul>
			<hr>	
			<table>
				<tbody>
					<tr>
						<th>구매 상품</th>
						<th>가격</th>
						<th>유효기간</th>
					</tr>
					<tr>
						<td>Q1</td>
						<td>&#36;1,000</td>
						<td>7/13/2019</td>
					</tr>
					<tr>
						<td>B1</td>
						<td>&#36;600</td>
						<td>7/13/2019</td>
					</tr>
				</tbody>
				<tfoot>
					<td class="bor_line">합계:</td>
					<td class="bor_line">&#36;1,600</td>
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
				<p class="font_gray">지불할 금액</p>
				<p>3.44521565 ETH</p>
			</div>


			<div class="btn_block_btm_wrap">
				<input type="button" value="취소" class="btn_basic_block noti_pop_open pop_open">
			</div>

		</section>


		<div class="pop_wrap noti_pop_wrap">
			<p class="pop_title">주의</p>	
			<div>
				지금 이 페이지를 벗어나면 구매가 즉각 종료됩니다. "구매가 완료되었습니다"라는 메시지가 뜰때까지 기다리십시오.
			</div>
			<div class="pop_close_wrap">
				<a href="javascript:void(0);" class="pop_close gray_close">Close</a>
			</div>
		</div>

	<div class="gnb_dim"></div>

</section>



<script>
	$(function() {
		$(".top_title h3").html("<img src='<?=G5_THEME_URL?>/_images/top_purchase.png' alt='아이콘'> <span data-i18n='title.팩상품구매하기'> Purchase Packs </span>");
		

		$('.noti_pop_open').click(function(){
				$('.noti_pop_wrap').css("display","block");
		});
	});
</script>



<? include_once(G5_THEME_PATH.'/_include/tail.php'); ?>
