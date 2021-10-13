<?php include '_include/head.php'; ?>
<?php include '_include/gnb.php'; ?>

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
				<p><span class="font_gray">인보이스 번호 : </span>201906142155220</p>
				<ul class="pay_coin_ul clear_fix">
					<li><img src="_images/bit_round.gif" alt="비트코인"> BTC로 지불</li>
					<li><img src="_images/eth_round.gif" alt="비트코인"> ETH로 지불</li>
					<li><img src="_images/rock_round.gif" alt="비트코인"> RWD로 지불</li>
				</ul>
				<p class="font_gray">지불할 금액</p>
				<p>3.44521565 ETH</p>
			</div>
			<hr>
			<div class="p1_qr_div">
				<img src="_images/otp_qr.gif" alt="큐알">
				<p>QR 코드를 스캔하거나<br>3.79133527 ETH 를 아래 주소로 송금하십시오.</p>
				<p class="font_blue">0x61276DaA9ef80a4Cef2AB9d7ef9e10DbA71B9597</p>
			</div>


			<div class="btn_block_btm_wrap">
				<input type="button" value="Cancel" class="btn_basic_block noti_pop_open pop_open">
			</div>

		</section>

<style>
	.dim{display: block;}
	.pop_wrap{display: block;}
</style>

		<div class="pop_wrap pay_pop_wrap">
			<p class="pop_title">지불확인</p>	
			<hr>
			<div>
				구매가 정상적으로 처리되었습니다.<br>
				감사합니다.
			</div>
			<div class="pop_close_wrap">
				<a href="javascript:void(0);" class="pop_close">OK</a>
			</div>
		</div>

		<div class="dim"></div>
		<div class="gnb_dim"></div>

	</section>



	<script>
		$(function() {
			$(".top_title h3").html("<img src='_images/top_purchase.png' alt='아이콘'> 팩 상품 구매하기");
			

			$('.noti_pop_open').click(function(){
				$('.noti_pop_wrap').css("display","block");
			});

		});
	</script>



</body></html>
