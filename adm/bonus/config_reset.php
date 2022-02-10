<?php
$sub_menu = "900120";
include_once('./_common.php');

$g5['title'] = "데이터 초기화 설정";

include_once(G5_ADMIN_PATH . '/admin.head.php');
?>

<link rel="stylesheet" href="/adm/css/switch.css">
<style>
	th{font-size:18px;font-family: 'Nanum-Gothic';letter-spacing: 0px;}
	.desc{font-size:12px;font-weight:500;display:block;margin:5px 0;}
	.red{color:red}
</style>
<div class="local_desc01 local_desc">
	<p>
		- 수당 초기화는 설정을 제외한 수당지급 로그,기록 관련 데이터등을 초기화<br>
		- 회원및구매내역 초기화는 회원 보유 잔고,포인트,직급 등을 초기화하고 구매내역,상품등을 초기화합니다.<span class='red'> ※관리자제외</span><br>
		- 입출금 초기화는 설정을 제외한 입금내역,출금내역 등을 초기화합니다.<br>
		<br>
		<!-- - 테스트환경생성은 전회원 10이더,10MBM 를 지급 하고, 당일기준 test20~test30까지의 m1 상품을 구매처리합니다.<span class='red'> ※관리자제외</span> -->
	</p>
</div>

<form name="frmnewwin" action="./config_reset.proc.php" onsubmit="return frmnewwin_check(this);" method="post">
	<input type="hidden" name="w" value="<?php echo $w; ?>">
	<div class="tbl_frm01 tbl_wrap">
		<table >
			<caption><?php echo $g5['title']; ?></caption>
			<tbody>
				<tr>
					<th scope="row"><label for="nw_soodang_reset"> 수당 초기화</label>
					<span class='desc' >※ 회원수당보너스 및 수당지급내역</span>
					</th>
					<td>
						<p style="padding:0;"><input type="checkbox" id="nw_soodang_reset" name="nw_soodang_reset" <?if($nw['nw_soodang_reset']=='Y' ) {echo "checked" ;}?>/><label for="nw_soodang_reset"><span class="ui"></span><span class="nw_soodang_reset_txt">사용 설정</span></label></p>
					</td>

					<th scope="row"><label for="nw_member_reset"> 회원포인트내역 및 구매내역 초기화</label></th>
					<td>
						<p style="padding:0;"><input type="checkbox" id="nw_member_reset" name="nw_member_reset" <?if($nw['nw_member_reset']=='Y' ) {echo "checked" ;}?>/><label for="nw_member_reset"><span class="ui"></span><span class="nw_member_reset_txt">사용 설정</span></label></p>
					</td>
				</tr>
				<tr>
					<th scope="row"><label for="nw_asset_reset"> 입출금 내역 초기화</label>
						<span class='desc' >※ 입출금 내역</span>
					</th>
					<td>
						<p style="padding:0;"><input type="checkbox" id="nw_asset_reset" name="nw_asset_reset" <?if($nw['nw_asset_reset']=='Y' ) {echo "checked" ;}?>/><label for="nw_asset_reset"><span class="ui"></span><span class="nw_asset_reset_txt">사용 설정</span></label></p>
					</td>
					

					<th scope="row">
						<label for="nw_mining_reset"> 회원 직급 및 등급 초기화</label>
						<span class='desc red' >※ 관리자 제외 전원 일반회원직급으로 </span>
					</th>
					<td>
						<p style="padding:0;"><input type="checkbox" id="nw_mining_reset" name="nw_mining_reset" <?if($nw['nw_mining_reset']=='Y' ) {echo "checked" ;}?>/><label for="nw_mining_reset"><span class="ui"></span><span class="nw_mining_reset_txt">사용 설정</span></label></p>
					</td>
				</tr>
				<tr><td style="height:30px;"></td></tr>
				<tr>
					<th scope="row" ><label for="nw_brecommend_reset"> 조직구성 초기화</label>
						<span class='desc red' >※ 등록된 추천인 제외 센터,지점,지사,본부 등록 정보 초기화</span>
					</th>
					<td>
						<p style="padding:0;"><input type="checkbox" id="nw_brecommend_reset" name="nw_brecommend_reset" <?if($nw['nw_brecommend_reset']=='Y' ) {echo "checked" ;}?>/><label for="nw_brecommend_reset"><span class="ui"></span><span class="nw_brecommend_reset_txt">사용 설정</span></label></p>
					</td>

					<th scope="row"><label for="nw_data_test"> 테스트환경 생성 </label>
						<span class='desc red' >※ 관리자 제외 전체 잔고 50,000,000 지급</span>
					</th>
					<td>
						<p style="padding:0;"><input type="checkbox" id="nw_data_test" name="nw_data_test" <?if($nw['nw_data_test']=='Y' ) {echo "checked" ;}?>/><label for="nw_data_test"><span class="ui"></span><span class="nw_data_test_txt">사용 설정</span></label></p>
					</td>
				</tr>

			</tbody>
		</table>
		
	</div>

	<div class="btn_confirm01 btn_confirm" style="margin-top:30px;">
		<input type="submit" value="확인" class="btn_submit" accesskey="s">
	</div>
</form>

<script>
	$(document).ready(function() {

		$('#nw_soodang_reset').on('click', function() {
			if ($('#nw_soodang_reset').is(":checked")) {
				$('.nw_soodang_reset_txt').html('사용함');
			} else {
				$('.nw_soodang_reset_txt').html('사용안함');
			}
		});

		$('#nw_member_reset').on('click', function() {
			if ($('#nw_member_reset').is(":checked")) {
				$('.nw_member_reset_txt').html('사용함');
			} else {
				$('.nw_member_reset_txt').html('사용안함');
			}
		});

		$('#nw_asset_reset').on('click', function() {
			if ($('#nw_asset_reset').is(":checked")) {
				$('.nw_asset_reset_txt').html('사용함');
			} else {
				$('.nw_asset_reset_txt').html('사용안함');
			}
		});

		$('#nw_data_del').on('click', function() {
			if ($('#nw_data_del').is(":checked")) {
				$('.nw_data_del_txt').html('사용함');
			} else {
				$('.nw_data_del_txt').html('사용안함');
			}
		});

		$('#nw_mining_del').on('click', function() {
			if ($('#nw_mining_del').is(":checked")) {
				$('.nw_mining_del_txt').html('사용함');
			} else {
				$('.nw_mining_del_txt').html('사용안함');
			}
		});


		$('#nw_brecommend_reset').on('click', function() {
			if ($('#nw_brecommend_reset').is(":checked")) {
				$('.nw_brecommend_reset_txt').html('사용함');
			} else {
				$('.nw_brecommend_reset_txt').html('사용안함');
			}
		});
		$('#nw_data_test').on('click', function() {
			if ($('#nw_data_test').is(":checked")) {
				$('.nw_data_test_txt').html('사용함');
			} else {
				$('.nw_data_test_txt').html('사용안함');
			}
		});
	});


	function frmnewwin_check(f) {
		// console.log($('#nw_data_reset').is(":checked") + ' / ' + $('#nw_data_test').is(":checked"));

		if ($('#nw_brecommend_reset').is(":checked")) {
			if (confirm('조직구성정보를 정말 초기화하겠습니까?')) {} else {
				return false;
			}
		}
/* 
		if ($('#nw_data_test').is(":checked")) {
			if (confirm('테스트 데이터를 생성 하시겠습니까?')) {} else {
				return false;
			}
		} */
	}
</script>

<?php
include_once(G5_ADMIN_PATH . '/admin.tail.php');
?>