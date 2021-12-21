
<?php
	include_once('./_common.php');
	include_once(G5_THEME_PATH.'/_include/wallet.php');
	include_once(G5_THEME_PATH.'/_include/gnb.php');

	login_check($member['mb_id']);
	
	$title = 'profile';


	/* 추천 링크 */
	/* $refferal_sql = "select short_code from url_shorten where mb_no ='{$member['mb_no']}'";
	$refferal_result = sql_fetch($refferal_sql);
	$ref_url = G5_URL."/go/".$refferal_result['short_code']; */


	// 임의의수까지 숫자 랜덤
	function generate_code($length = 6) {
		$numbers  = "0123456789";
		$svcTxSeqno = date("YmdHis");
		$nmr_loops = 6;
		while ($nmr_loops--) {
			$svcTxSeqno .= $numbers[mt_rand(0, strlen($numbers))];
		}
		return $svcTxSeqno;
	}



	$security_code= generateRandomString(6);
	$security_num_code = generate_code(8);

	function format_phone($phone){ $phone = preg_replace("/[^0-9]/", "", $phone); $length = strlen($phone); switch($length){ case 11 : return preg_replace("/([0-9]{3})([0-9]{4})([0-9]{4})/", "$1-$2-$3", $phone); break; case 10: return preg_replace("/([0-9]{3})([0-9]{3})([0-9]{4})/", "$1-$2-$3", $phone); break; default : return $phone; break; } }


// 자동 팩 구매
	// $pack_sql = "select * from g5_shop_cart where mb_id = '{$member['mb_id']}' order by ct_time desc limit 0,1";
	// $pack_res = sql_fetch($pack_sql);
	// $pack_name = substr($pack_res['it_name'],-1,1);

	// $pack_auto = $pack_res['ct_option'];
	// if($pack_auto != ''){
	// 	$pack_check =  'checked';
	// }


	//kyc 인증
	// $kyc_sql = "select * from g5_write_kyc where mb_id = '{$member['mb_id']}' order by wr_last desc limit 0,1";
	// $kyc_res = sql_fetch($kyc_sql);
	// $kyc_cert = $kyc_res['wr_2'];




	//길이만큼 영문숫자 랜덤코드 /lib/common.php
	/*
	function generateRandomString($length = 10) {
		$characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
		$charactersLength = strlen($characters);
		$randomString = '';

		for ($i = 0; $i < $length; $i++) {
			$randomString .= $characters[rand(0, $charactersLength - 1)];
		}
		return $randomString;
	}
	*/

?>
	
    <main>
        <div class='container profile nomargin nopadding'>
			<section class="profile_wrap content-box6">
				<div class="col-sm-12 col-12 profile-box">
					<h3 class='title b_line'>
						<i><img src="<?=G5_THEME_URL?>/img/personl_information.png" alt=""></i>
						<span data-i18n="profile.개인정보">Profile</span>
					</h3>

					<ul class='row person_info'>
						<li class='col-12 inline' style='background:ghostwhite;padding:10px;text-align:center'>
							<span class='userid user_level inline' ><?=$user_icon?></span>
							<h4 class='myid bold inline'>
								
								<span class='nation_flag'><img src="<?=national_flag($member['nation_number'])?>"></span>

								<span style='font-size:18px;'><?=$member['mb_name']?>님</span>
								<span style='font-size:14px;line-height:15px;'>[ <?=$member['mb_id']?> ]</span>
							</h4>
							<h4 class='mygrade badge inline'><?=$user_level?></h4>
						</li>
					</ul>

					<ul class='row person_info'>
						<li class='col-12 mt20 '>
							<label>모바일</label>  
							<p class='mb20'><?=format_phone($member['mb_hp'])?></p>
							
						</li>

						<li class='col-12 mt20'>
							<label data-i18n="profile.이메일">Email</label>  
							<p><?=$member['mb_email']?></p>
							<!-- <?if($member['mb_email_certify'] != ''){?>
								<img src="<?=G5_THEME_URL?>/_images/okay_icon.gif" alt="인증됨" style="width:15px;">
							<?}else{?>
								<img src="<?=G5_THEME_URL?>/_images/x_icon.gif" alt="인증안됨" style="width:15px;">
							<?}?> -->
						</li>
						<!-- 
							<li class='col-sm-3 col-4 text-right grid'>
								<input type="button" value="Change" class="btn inline white email_pop_open pop_open" data-i18n="[value]profile.변경">
							</li>
						 -->
					</ul>
					<?php if($member['bank_name'] && $member['bank_account'] && $member['account_name']){?>
					<ul class='row person_info'>
						<li class='col-12'>
							<label>출금정보</label>
							<p><?=$member['bank_name']?> : <?=$member['bank_account']?> (<?=$member['account_name']?>)</p>	
						</li>
					</ul>
					<?php } ?>
					<ul class='row person_info'>
						<li class='col-12'>
							<label>주소</label>
							<p><?=$member['mb_addr1']?> <?=$member['mb_addr2']?></p>		
						</li>
					</ul>
				</div>
				<div class='col-sm-12 col-12 profile-box'>
					<h3 class='title b_line'>
						<i><img src="<?=G5_THEME_URL?>/img/security_setting.png" alt=""></i>
						<span data-i18n="profile.보안설정">Setting</span>
					</h3>
					<ul class='row'>
						<li class='col-sm-9 col-8'><span data-i18n="profile.로그인 비밀번호 변경">Change login password</span></li>
						<li class='col-sm-3 col-4 text-right grid'>
							<i class="ri-arrow-drop-right-line ch_pw_open pop_open"></i>
						</li>
					</ul>

					<ul class='row'>
						<li class='col-sm-9 col-8'><span data-i18n="profile.출금 비밀번호 변경">Change Pin-Code</span></li>
						<li class='col-sm-3 col-4 text-right grid'>
							<i class="ri-arrow-drop-right-line ch_tpw_open pop_open"></i>
						</li>
					</ul>
				</div>
				<div class='col-sm-12 col-12 profile-box'>
					<h3 class='title b_line'>
						<i><img src="<?=G5_THEME_URL?>/img/recommendation_information.png" alt=""></i>
						<span data-i18n="profile.추천인 정보">Referral</<i>
					</h3>
					<ul class='row'>
						<li class='col-sm-12 col-12'>
							<label data-i18n="profile.나의 추천">My Referral</label>
							<p ><?=$member['mb_recommend']?></p>
						</li>
					</ul>

					<!-- <ul class='row'>
						<li class='col-sm-12 col-12'>
							<label data-i18n="profile.나의 센터">My Center</label>
							<p ><?=get_name($member['mb_center'])?></p>
						</li>
					</ul> -->

					<!-- <div class="google-auth-top-qr" id="qrcode"></div> -->
				</div>
				<!-- <div class='col-sm-12 col-12 profile-box'>
					<h3 class='title b_line'>
						<i><img src="<?=G5_THEME_URL?>/img/alert_setting.png" alt=""></i>
						<span data-i18n="profile.알림설정">알림설정</<i>
					</h3>
					<ul class='row'>
						<li class='col-sm-9 col-8'>
							<p data-i18n="profile.알림">알림</p>
						</li>
						<li class='col-sm-3 col-4 text-right grid'>
							<div class='switch_box'>
								<label class="switch">
									<input type="checkbox" <?if($member['mb_sms']>0) echo 'checked';?> class='noti_check'>
									<span class="slider round"></span>
								</label>
								<p data-i18n="profile.끄기">OFF</p><p style="display:none;" data-i18n="profile.켜기">ON</p>
							</div>
						</li>
					</ul>
				</div> -->
			</section>
    	</div>
	</main>
	<?php include_once(G5_THEME_PATH.'/_include/tail.php'); ?>
	<div class="gnb_dim"></div>
</section>

<script>
	$(function(){
		$(".top_title h3").html("<span data-i18n=''>개인정보&보안설정</span>")
	});
</script>

		<!-- 트랜잭션 비밀번호 변경 -->
		<div class="pop_wrap chage_tpw_pop">
			<p class="pop_title font_red">경고</p>
			<div>
				본인의 비밀번호는 블록체인에 공유되거나 서버에 저장되지 않습니다. 즉, 우리는 회원의 비밀번호를 알 수도 없고 초기화 시킬 수도 없습니다. 회원의 지갑을 복구하기 위한 유일한 방법은 백업 구절을 통한 방법입니다. 비밀번호 분실시 지갑을 복구할 수 있는 유일한 방법인 백업 구절을 꼭 안전한 장소에 보관하시기 바랍니다.
			</div>
			<div class="pop_close_wrap">
				<a href="javascript:void(0);" class="go_tpw1">계속</a>
			</div>
		</div>

		<div class="pop_wrap chage_tpw_pop1 input_pop_css">
			<form action="">
				<label for="" data-i18n='popup.사용중인 거래 비밀번호'>Current Pin-Code</label>
				<input type="password" id="current_tpw" maxlength="6">
				<hr class="hr_dash">

				<label for="" data-i18n='popup.새로운 거래 비밀번호'>New Pin-Code</label>
				<input type="password" id="new_tpw" maxlength="6">
				<label for="" data-i18n='popup.새로운 거래 비밀번호 확인'>Confirm new Pin-Code</label>
				<input type="password" id="new_tpw_re" maxlength="6">
				<label for="" data-i18n='popup.로그인 비밀번호'>Login password for change</label>
				<input type="password" id="auth_pwd" minlength='4' maxlength="20">
				<!--
				<div>
					<label for="" data-i18n='popup.보안코드 입력'>Enter the security code</label>
					<p class="code_btn code_btn_tpw"><img src="<?=G5_THEME_URL?>/_images/email_send_icon.gif" alt="이미지" data-i18n='popup.코드요청'>Request code</p>
				</div>
				<input type="text" style="margin-bottom:25px;">
				-->

				<div class="btn2_btm_wrap">
					<input type="button" value="Close" class="btn btn_double default_btn cancel pop_close" >
					<input type="button" value="Save" class="btn btn_double blue save go_tpw3">
				</div>
			</form>
		</div>

		<div class="pop_wrap chage_tpw_pop2 notice_img_pop">
			<p class="pop_title" data-i18n='popup.인증번호 전송'>Email verification</p>
			<img src="<?=G5_THEME_URL?>/_images/comform_chk.gif" alt="체크">
			<div data-i18n='popup.인증번호가 이메일로 전송되었습니다'>Security code sent to your email.</div>
			<a href="javascript:void(0);" class="back_tpw1 gray_close f_right" data-i18n='popup.창닫기'>Close</a>
		</div>

		<div class="pop_wrap chage_tpw_pop3 notice_img_pop">
			<p class="pop_title" data-i18n='popup.거래 비밀번호 변경'>Change Pin-Code</p>
			<div>
				<img src="<?=G5_THEME_URL?>/_images/comform_chk.gif" alt="이미지">
				<p data-i18n='popup.변경이 성공적으로 완료되었습니다'>Change successfully completed.
			</div>
			<div class="pop_close_wrap">
				<a href="javascript:void(0);" id="pin_close" data-i18n='popup.창닫기'>Close</a>
			</div>
		</div>
<script>

$(function() {
	//  트랜잭션 비밀번호변경
	$('.ch_tpw_open').click(function(){
		//$('.chage_tpw_pop').css("display","block");
		$('.chage_tpw_pop1').css("display","block");
	});

	onlyNumber('current_tpw');
	onlyNumber('new_tpw');
	onlyNumber('new_tpw_re');

	$('.chage_tpw_pop1 .save').click(function(){
		// console.log('tpw');

		var current_tpw = $('.chage_tpw_pop1 #current_tpw').val();
		var new_tpw = $('.chage_tpw_pop1 #new_tpw').val();
		var new_tpw_re = $('.chage_tpw_pop1 #new_tpw_re').val();


		if(new_tpw.length < 6){
			dialogModal('Please check','<strong> New Pin-Code must contain 6 digits.</strong>','failed');
			return false;
		}

		if(new_tpw != new_tpw_re){
			dialogModal('Please check','<strong> New Pin-Code does not matched.</strong>','failed');
			return false;
		}

		// console.log(current_tpw + '/' + new_tpw + '/' + new_tpw_re);

		$.ajax({
				type: "POST",
				url: "/util/profile_proc.php",
				dataType: "json",
				data:  {
					"current_tpw" : current_tpw,
					"new_tpw" : new_tpw,
					"new_tpw_re" : new_tpw_re,
					"auth_pwd" : $('.chage_tpw_pop1 #auth_pwd').val(),
					"category" : "tpw"
				},
				success: function(data) {
					if(data.result =='success'){
						$('.chage_tpw_pop1').css("display","none");
						$('.chage_tpw_pop3').css("display","block");
						$('#pin_close').click(function(){
							window.location.reload();
						})
					}else{
						dialogModal('Error!','<strong> '+ data.sql+'</strong>','failed');
					}
				},
				error:function(e){
					dialogModal('Error!','<strong> Please check retry.</strong>','failed');
				}
			});

	});
});

</script>




<!--  비밀번호 변경 -->

	<div class="pop_wrap chage_pw_pop">
		<p class="pop_title font_red">경고</p>
		<div>
			본인의 비밀번호는 블록체인에 공유되거나 서버에 저장되지 않습니다. 즉, 우리는 회원의 비밀번호를 알 수도 없고 초기화 시킬 수도 없습니다. 회원의 지갑을 복구하기 위한 유일한 방법은 백업 구절을 통한 방법입니다. 비밀번호 분실시 지갑을 복구할 수 있는 유일한 방법인 백업 구절을 꼭 안전한 장소에 보관하시기 바랍니다.
		</div>
		<div class="pop_close_wrap">
			<a href="javascript:void(0);" class="go_ch_pw1">계속</a>
		</div>
	</div>

	<div class="pop_wrap chage_pw_pop1 input_pop_css">
		<form action="">
			<label for="" data-i18n='popup.사용중인 비밀번호'>Current login password</label>
			<input type="password" id="current_pw" minlength='4' maxlength="20">

			<hr class="hr_dash">

			<label for="" data-i18n='popup.새로운 비밀번호'>New login password</label>
			<input type="password" id="new_pw" minlength='4' maxlength="20">
			<label for="" data-i18n='popup.새로운 비밀번호 확인'>Confirm new login password</label>
			<input type="password" id="new_pw_re" minlength='4' maxlength="20">
			<!--
			<div>
				<label for="" data-i18n='popup.보안코드 입력'>Enter the security code</label>
				<p class="code_btn code_btn_pw"><img src="<?=G5_THEME_URL?>/_images/email_send_icon.gif" alt="이미지" data-i18n='popup.코드요청'>Request code</p>
			</div>

			<input type="text" style="margin-bottom:25px;">
			-->
			<div class="btn2_btm_wrap">
				<input type="button" value="Close" class="btn btn_double default_btn cancel pop_close" >
				<input type="button" value="Save" class="btn btn_double default_btn blue save go_ch_pw3">
			</div>
		</form>
	</div>

	<div class="pop_wrap chage_pw_pop3 notice_img_pop">
		<p class="pop_title" data-i18n="비밀번호 변경">Change Password</p>
		<div>
			<img src="<?=G5_THEME_URL?>/_images/comform_chk.gif" alt="이미지">
			<span data-i18n="비밀번호가 성공적으로 변경되었습니다">Password successfully changed
		</div>
		<div class="pop_close_wrap">
			<a href="javascript:void(0);" id="pass_close" class="pop_close">Close</a>
		</div>
	</div>

<script>



$(function() {

	//console.log(email_sendcode);
	$('.ch_pw_open').click(function(){
			//$('.chage_pw_pop').css("display","block");
		$('.chage_pw_pop1').css("display","block");
	});


	$('.chage_pw_pop1 .save').click(function(){

		var current_pw = $('.chage_pw_pop1 #current_pw').val();
		var new_pw = $('.chage_pw_pop1 #new_pw').val();
		var check_new_pw = CheckPass(document.getElementById('new_pw').value);
		var new_pw_re = $('.chage_pw_pop1 #new_pw_re').val();

		if(check_new_pw == false){
			dialogModal('Please check','<strong> New password required <br />1. Must be 4 to 12 digits<br />2. English and numbers must be written together</strong>','failed',false);
			return false;
		}

		if(new_pw != new_pw_re){
			dialogModal('Please check','<strong> New login password does not matched.</strong>','failed',false);
			return false;
		}

		// console.log(current_pw + '/' + new_pw + '/' + new_pw_re);

		$.ajax({
			type: "POST",
			url: "/util/profile_proc.php",
			dataType: "json",
			data:  {
				"current_pw" : current_pw,
				"new_pw" : new_pw,
				"new_pw_re" : new_pw_re,
				"category" : "pw"
			},
			success: function(data) {
				if(data.result =='success'){
					$('.chage_pw_pop1').css("display","none");
					$('.chage_pw_pop3').css("display","block");
					$('#pass_close').click(function(){
						window.location.reload();
					})
				}else{
					dialogModal('Error!','<strong> '+ data.sql+'</strong>','failed');
				}
			},
			error:function(e){
				dialogModal('Error!','<strong> Please check retry.</strong>','failed');
			}
		});

	});
});

</script>



<!-- 이메일 주소 변경 -->
	<div class="pop_wrap chage_email_pop input_pop_css">
		<form>
			<label for="" data-i18n='popup.사용중인 이메일 주소'>Current email</label>
			<div class='current'><?=$member['mb_email']?></div>

			<label for="" data-i18n='popup.새로운 이메일 주소'>New email</label>
			<input type="text"  name="email_new" id="email_new" value="" onchange="validateEmail(this.value);">

			<label for="" data-i18n='popup.새로운 이메일 주소 확인'>Confirm new email</label>
			<input type="text"  name="email_new_re" id="email_new_re" value="">

			<label for="" data-i18n='popup.로그인 비밀번호'>Login password for change</label>
			<input type="password" id="auth_pwd" minlength='4' maxlength="20">

			<div class="btn2_btm_wrap">
				<input type="button" value="Close" class="btn btn_double deault_btn cancel pop_close" >
				<input type="button" value="Save" class="btn btn_double blue save">
			</div>
		</form>
	</div>


	<div class="pop_wrap chage_email_pop1 notice_img_pop">
		<p class="pop_title" data-i18n='popup.이메일 변경'>Change Email address</p>
		<div>
			<img src="<?=G5_THEME_URL?>/_images/comform_chk.gif" alt="이미지">
		<p data-i18n='popup.변경이 성공적으로 완료되었습니다'>	Change successfully saved</p>
		</div>
		<div class="pop_close_wrap">
			<a href="javascript:parent.location.reload();" class="btn inline wd pop_close" data-i18n='popup.창닫기'>Close</a>
		</div>
	</div>


	<script>


	$(function() {

		$('.email_pop_open').click(function(){
			$('.chage_email_pop').css("display","block");

		validateEmail = function (email) {
		var email = email;
		var re = /^([\w-]+(?:\.[\w-]+)*)@((?:[\w-]+\.)*\w[\w-]{0,66})\.([a-z]{2,6}(?:\.[a-z]{2})?)$/i;

		if (email == '' || !re.test(email)) {
			alert("wrong E-mail type! please check e-mail");
			return false;
		}
	}
		});

		$('.go_ch_em1').click(function(){
			$('.chage_email_pop').css("display","none");
			$('.chage_email_pop1').css("display","block");
		});



		$('.chage_email_pop .save').click(function(){
			var email2 = $('.chage_email_pop #email_new').val();
			var email3 = $('.chage_email_pop #email_new_re').val();

			if( email2 != email3){
				dialogModal('Please check','<strong> New email does not matched confirm new mail.</strong>','failed');
				return false;
			}

			$.ajax({
					type: "POST",
					url: "/util/profile_proc.php",
					dataType: "json",
					data:  {
						"email2" : email2,
						"email3" : email3,
						// "confirm" : email_vaild_code,
						"auth_pwd" : $('.chage_email_pop #auth_pwd').val(),
						"category" : "email"

					},
					success: function(data) {
						if(data.result =='success'){
							$('.chage_email_pop2').css("display","none");
							$('.chage_email_pop1').css("display","block");
						}else{
							dialogModal('Error!','<strong> '+ data.sql+'</strong>','failed');
						}
					},
					error:function(e){
						dialogModal('Error!','<strong> Please check retry.</strong>','failed');
					}
				});

		});
	});
	</script>
<!-- 이메일 주소 변경 -->












<!-- 전화번호 변경 -->
	<div class="pop_wrap num_pop_wrap input_pop_css">
		<form action="">
			<label for="" data-i18n="popup.사용중인 전화번호">Current phone number</label>
			<div class="num_pop_div clear_fix" >
				<!-- <input type="input" id="nation_num" value='' placeholder="Country" maxlength="3" readonly>
				<input type="input" id="hp_num" value='<?=substr(str_replace('-','',$member['mb_hp']), 1);?>' placeholder="Phone Number(Number only)" readonly>
					-->
				<div class='current'><?=" + ".$member['nation_number']." ".$member['mb_hp']?></div>
			</div>


			<label for="" data-i18n="popup.새로운 전화번호">New phone number</label>
			<div class="num_pop_div clear_fix">
				<input type="text" id="new_nation_num" value="<?=$member['nation_number']?>" placeholder="Country" maxlength="3" data-i18n='[placeholder]popup.국가번호' readonly>
				<input type="text" id="new_hp_num" value="" placeholder="Phone Number(Number only)" data-i18n='[placeholder]popup.전화번호(숫자만)'>
			</div>


			<label for="" data-i18n='popup.로그인 비밀번호'>Login password for change</label>
			<input type="password" id="auth_pwd" minlength='4' maxlength="20">


			<div class="btn2_btm_wrap" >
				<input type="button" value="Close" class="btn btn_double default_btn cancel pop_close" >
				<input type="button" value="Save" class="btn btn_double blue save proceed">
			</div>

		</form>
	</div>

	<!-- 변경완료 -->
	<div class="pop_wrap num2_pop_wrap notice_img_pop">
		<p class="pop_title" data-i18n="popup.전화번호 변경">Change Phone Number</p>
		<div>
			<img src="<?=G5_THEME_URL?>/_images/comform_chk.gif" alt="이미지">
		<p data-i18n="popup.변경이 성공적으로 완료되었습니다">Change successfully completed</p>
		</div>
		<div class="pop_close_wrap">
			<a href="javascript:void(0);" class="btn inline wd pop_close" data-i18n="popup.창닫기">Close</a>
		</div>
	</div>

	<script>
		$(function() {
			$('.num_pop_open').click(function(){
				$('.num_pop_wrap').css("display","block");
			});

			$('.num1_pop_close').click(function(){
				$('.num_pop_wrap').css("display","block");
				$('.num1_pop_wrap').css("display","none");
			});

			$('.proceed').click(function(){
				var new_hp_num = $('.num_pop_wrap #new_hp_num').val();

				$.ajax({
					type: "POST",
					url: "/util/profile_proc.php",
					dataType: "json",
					data:  {
						// "hp_num" : hp_num,
						// "new_nation_num" : new_nation_num,
						"new_hp_num" : new_hp_num,
						"auth_pwd" : $('.num_pop_wrap #auth_pwd').val(),
						"category" : "phone"

					},
					success: function(data) {
						if(data.result =='success'){
							$('.num_pop_wrap').css("display","none");
							$('.num2_pop_wrap').css("display","block");
							$('.num2_pop_wrap .pop_close').click(function(){
								parent.location.reload();
							});
						}else{
							dialogModal('Please check','<strong>'+data.sql+'</strong>','failed');
						}
					},
					error:function(e){
						dialogModal('Error!','<strong> Please check retry.</strong>','failed');
					}
				});
			});
		});
	</script>
<!-- 전화번호 변경 -->





<!-- 이름변경 -->
	<div class="pop_wrap chage_name_pop1 input_pop_css">
	<form action="">

		<label for="" data-i18n='popup.현재 성함'>Current name</label>
			<!-- <input type="text" id="current_name" value='<?=$member['first_name']." ".$member['last_name']?>' readonly> -->
			<div class='current'><?=$member['first_name']." ".$member['last_name']?></div>

		<label for="" data-i18n='popup.변경하실 성'>New last name</label>
			<input type="text" id="new_last_name">

		<label for="" data-i18n='popup.변경하실 이름'>New first name</label>
			<input type="text" id="new_first_name">

		<label for="" data-i18n='popup.로그인 비밀번호'>Login password for change</label>
			<input type="password" id="auth_pwd" minlength='4' maxlength="20">


		<div class="btn2_btm_wrap">
			<input type="button" value="Close" class="btn btn_double default_btn cancel pop_close" >
			<input type="button" value="Save" class="btn btn_double blue save go_ch_name">
		</div>
	</form>
	</div>


	<div class="pop_wrap chage_name_pop3 notice_img_pop">
		<p class="pop_title" data-i18n="성함 변경">Change name</p>
		<div>
			<img src="<?=G5_THEME_URL?>/_images/comform_chk.gif" alt="이미지">
			<span data-i18n="사용자님 성함이 성공적으로 변경되었습니다">Name successfully changed
		</div>
		<div class="pop_close_wrap">
			<a href="javascript:void(0);" class="btn inline wd pop_close">Close</a>
		</div>
	</div>

	<script>
	$(function() {
		var noti_check = <?=$member['mb_sms']?>;
		if(noti_check > 0){
			$('.switch_box p').toggle();
		}

		$("input[type='checkbox']").on('click',function(){
			$(this).parent().parent().find('p').toggle();
			
			if($(this).is(":checked")){
				var check_value = 1;
			}else{
				var check_value = 0;
			}
			$.ajax({
				type: "POST",
				url: "/util/profile_proc.php",
				dataType: "json",
				data:  {
					"check_value" : check_value,
					"category" : "notification"
				},
				success: function(data) {
					if(data.result =='success'){
						
					}else{
						dialogModal('Error!','<strong> '+ data.sql+'</strong>','failed');
					}
				},
				error:function(e){
					dialogModal('Error!','<strong> Please check retry.</strong>','failed');
				}
			});
		});
	

		$('.ch_name_open').click(function(){
			$('.chage_name_pop1').css("display","block");
		});

		$('.chage_name_pop1 .save').click(function(){
			var new_last_name = $('.chage_name_pop1 #new_last_name').val();
			var new_first_name = $('.chage_name_pop1 #new_first_name').val();
			var auth_pwd = $('.chage_name_pop1 #auth_pwd').val();

			$.ajax({
					type: "POST",
					url: "/util/profile_proc.php",
					dataType: "json",
					data:  {
						"new_last_name" : new_last_name,
						"new_first_name" : new_first_name,
						"auth_pwd" : auth_pwd,
						"category" : "name"
					},
					success: function(data) {
						if(data.result =='success'){
							$('.chage_name_pop1').css("display","none");
							$('.chage_name_pop3').css("display","block");
						}else{
							dialogModal('Error!','<strong> '+ data.sql+'</strong>','failed');
						}
					},
					error:function(e){
						dialogModal('Error!','<strong> Please check retry.</strong>','failed');
					}
				});
		});
	});
	</script>
<!-- 이름변경 -->







