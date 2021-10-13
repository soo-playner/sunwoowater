<?php include '_include/head.php'; ?>
<?php include '_include/gnb.php'; ?>

		<section class="v_center wrap purchase2_wrap">

			<dl>
				<dt class="bit_color">
					<p>
						<img src="_images/bit_round.gif" alt="이더리움">
						Amount to Pay
					</p>
					<span>0.5565BTC</span>
				</dt>
				<dd>
					<img src="_images/qr_img.gif" alt="">
				</dd>
				
				<dt class="eth_color">
					<p>
						<img src="_images/eth_round.gif" alt="이더리움">
						Amount to Pay
					</p>
					<span>0.5565BTC</span>
				</dt>
				<dd>
					<img src="_images/qr_img.gif" alt="">
				</dd>
				
				<dt class="rock_color">
					<p>
						<img src="_images/rock_round.gif" alt="이더리움">
						Amount to Pay
					</p>
					<span>0.5565BTC</span>
				</dt>
				<dd>
					<img src="_images/qr_img.gif" alt="">
				</dd>
			</dl>


			<div class="btn_block_btm_wrap">
				<input type="button" value="Cancel" class="btn_basic_block">
			</div>

		</section>

		<?php include '_include/popup.php'; ?>
		<div class="gnb_dim"></div>

	</section>



	<script>
		$(function() {
			$(".top_title h3").html("<img src='_images/top_purchase.png' alt='아이콘'> 팩 상품 구매하기");
			

			$('dt').click(function() {
				$(this).next().stop().slideToggle();
			});
		});
	</script>



</body></html>
