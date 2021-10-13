<?php include '_include/head.php'; ?>
<?php include '_include/gnb.php'; ?>

		<section class="v_center purchase1_wrap wrap">

			<h5>구매하는 팩 상품</h5>
			<table>
				<tbody>
				  <colgroup>
					<col style="width:96px;">
					<col style="width:40px;">
					<col style="width: *;">
					<col style="width:30px;">
				  </colgroup>
					<tr>
						<td>바이너리 팩</td>
						<td>B1</td>
						<td>&#36;600</td>
						<td>X</td>
					</tr>
					<tr>
						<td>큐 팩</td>
						<td>Q1</td>
						<td>&#36;1,000</td>
						<td>X</td>
					</tr>
				</tbody>
				<tfoot>
					<td>합계:</td>
					<td></td>
					<td>&#36;1,600</td>
					<td></td>
				</tfoot>

			</table>
			<h5>지불 방식을 선택하세요</h5>
			<div class="payment_div">
				<p>지갑 잔고로 지불</p>
				<ul class="pay_coin bit_color">
					<li><img src="_images/bit_round.gif" alt="비트코인"></li>
					<li>
						<div>
							<span>잔고</span>
							<b>1.5215BTC ($12,000.52)</b>
						</div>
						<div class="coin_color">
							<span>지불할 코인 갯수:</span>
							<b>0.15425BTC</b>
						</div>
					</li>
					<li>
						<input type="button" value="결제" onclick="location.href='purchase_3.php'">
					</li>
				</ul>
				<ul class="pay_coin eth_color">
					<li><img src="_images/eth_round.gif" alt="이더리움"></li>
					<li>
						<div>
							<span>잔고</span>
							<b>1.5215BTC ($12,000.52)</b>
						</div>
						<div class="coin_color">
							<span>지불할 코인 갯수:</span>
							<b>0.15425BTC</b>
						</div>
					</li>
					<li>
						<input type="button" value="결제" onclick="location.href='purchase_3.php'">
					</li>
				</ul>
				<ul class="pay_coin rock_color">
					<li><img src="_images/rock_round.gif" alt="락우드"></li>
					<li>
						<div>
							<span>잔고</span>
							<b>1.5215BTC ($12,000.52)</b>
						</div>
						<div class="coin_color">
							<span>지불할 코인 갯수:</span>
							<b>0.15425BTC</b>
						</div>
					</li>
					<li>
						<input type="button" value="결제" onclick="location.href='purchase_3.php'">
					</li>
				</ul>
			</div>
			<hr>
			<div class="payment_div">
				<p>외부 지갑에서 지불</p>
				<ul class="pay_coin bit_color">
					<li><img src="_images/bit_round.gif" alt="비트코인"></li>
					<li>
						<div class="coin_color">
							<span>지불할 코인 갯수:</span>
							<b>0.15425BTC</b>
						</div>
					</li>
					<li>
						<input type="button" value="인보이스 생성" onclick="location.href='purchase_3_ext.php'">
					</li>
				</ul>
				<ul class="pay_coin eth_color">
					<li><img src="_images/eth_round.gif" alt="이더리움"></li>
					<li>
						<div class="coin_color">
							<span>지불할 코인 갯수:</span>
							<b>0.15425BTC</b>
						</div>
					</li>
					<li>
						<input type="button" value="인보이스 생성" onclick="location.href='purchase_3_ext.php'">
					</li>
				</ul>
				<ul class="pay_coin rock_color">
					<li><img src="_images/rock_round.gif" alt="락우드"></li>
					<li>
						<div class="coin_color">
							<span>지불할 코인 갯수:</span>
							<b>0.15425BTC</b>
						</div>
					</li>
					<li>
						<input type="button" value="인보이스 생성" onclick="location.href='purchase_3_ext.php'">
					</li>
				</ul>
			</div>


			<div>
				<input type="button" value="구매 취소" class="btn_basic_block" onclick="history.back();">
			</div>

		</section>

		<?php include '_include/popup.php'; ?>
		<div class="gnb_dim"></div>

	</section>



	<script>
		$(function() {
			$(".top_title h3").html("<img src='_images/top_purchase.png' alt='아이콘'> 팩 상품 구매하기");
			

		});
	</script>



</body></html>
