<?
$menubar = 1;
$email_auth = 1;
$phone_auth = 0;


include_once(G5_THEME_PATH.'/_include/head.php');
include_once(G5_THEME_PATH.'/_include/gnb.php');
include_once(G5_THEME_PATH.'/_include/lang.php');
include_once(G5_THEME_PATH.'/_include/wallet.php');


$service_term = get_write("g5_write_agreement", 1);
$private_term = get_write("g5_write_agreement", 2);

// 추천인링크 타고 넘어온경우
if ($_GET['recom_referral']){
	$recom_sql = "select mb_id from g5_member where mb_no = '{$_GET['recom_referral']}'";
	$recom_result = sql_fetch($recom_sql);
	$mb_recommend = $recom_result['mb_id'];
}
?>
<style>
	.gflag{display:none !important;}
</style>
<link href="<?=G5_THEME_URL ?>/css/scss/enroll.css" rel="stylesheet">
<script src="https://use.fontawesome.com/releases/v5.2.0/js/all.js"></script>
<script src="//t1.daumcdn.net/mapjsapi/bundle/postcode/prod/postcode.v2.js"></script>
<link href="<?=G5_THEME_URL ?>/css/dd.css" rel="stylesheet">
<script src="<?=G5_THEME_URL?>/_common/js/jquery.dd.min.js"></script>

<style>
	/* 센터 닉네임 사용 추가 0720  by arcthan */
	.dd{border:2px solid #006df3}
	.dd .ddTitle{height:40px;line-height:27px;}
	.dd .ddTitle .ddTitleText img{padding:0;margin-right:5px;box-shadow:0 1px 1px rgb(0 0 0 / 50%)}

	.dd .ddlabel{vertical-align: middle;}

	.ddcommon .ddArrow{right:10px;}

	.id_set input:not(#id_check){border-left:3px solid #999;}
	.phone_set input[type='text'],.phone_set input[type='email']{border-left:3px solid #dc3545;}
	.addr_set input{border-left:3px solid royalblue;}
	.bank_info_set input{border-left:3px solid gold;}

	hr.light_line{margin-top:10px;margin-bottom:-10px;border-top:1px solid rgba(0,0,0,0.1)}
</style>


<script type="text/javascript">
	var captcha;
	var key;
	var verify = false;
	var recommned = "<?= $mb_recommend ?>";
	var recommend_search = false;

	if (recommned) {
		recommend_search = true;
	}
	//console.log(recommend_search);

	$(function() {

		$("#nation_number").msDropDown();

		/* $("#nation_number").on('change',function(){
			var select_nation = $("#nation_number option:selected").val();
			console.log(select_nation);
		}); */

		onlyNumber('reg_mb_hp');
		onlyNumber('bank_account');

		// 공통함수로 이전 common.js
		/* $('.cabinet').on('click',function(){
			$(this).next().css('display','contents');
		});

		$('.cabinet').on('mouseout',function(){
			$(this).next().css('display','none');
		}); */


		/*초기설정*/
		//$('.agreement_ly').hide();
		$('#verify_txt').hide();


		/* 핸드폰 SMS 문자인증 사용 */
		$('#nation_number').on('change', function(e) {
			// $('#reg_mb_hp').val($(this).val());
		});

		var phone_auth = "<?= $phone_auth ?>";
		if (phone_auth > 0) {
			$('.verify_phone').hide();

			//SMS발송
			$('#sendSms').on('click', function(e) {
				if (!$('#reg_mb_hp').val()) {
					commonModal('Mobile authentication', '<p>Please enter your Mobile Number</p>', 80);
					return;
				}
				var reg_mb_hp = +($('#reg_mb_hp').val().replace(/-/gi, ''));
				$.ajax({
					url: '/bbs/register.sms.verify.php',
					type: 'post',
					async: false,
					data: {
						"nation_no": $('#nation_number').val(),
						"mb_hp": reg_mb_hp
					},
					dataType: 'json',
					success: function(result) {
						// console.log(result);
						smsKey = result.key;
						commonModal('SMS authentication', '<p>Sent a authentication code to your Mobile.</p>', 80);
					},
					error: function(e) {
						console.log(e);
					}
				});
			});
		}
	
	/* 메일발송 로더 */
	/* var loading = $('<div id="loading" class="loading"></div><img id="loading_img" src="/img/Spinner-1s-200px2.gif" />');
	loading.appendTo(document.body).hide(); */
 	

		/*이메일 체크*/
		$('#EmailChcek').on('click',function(){
			var email = $('#reg_mb_email').val();
			var re = /^([\w-]+(?:\.[\w-]+)*)@((?:[\w-]+\.)*\w[\w-]{0,66})\.([a-z]{2,6}(?:\.[a-z]{2})?)$/i;


			if (email == '' || !re.test(email)) {
				commonModal("check email address", "please put email correctly", 80)
				return false;
			}

			// loading.show();

			$.ajax({
				type: "POST",
				url: "/mail/send_mail.php",
				dataType: "json",
				data: {
					user_email: email
				},
				complete: function() {
					// loading.hide();
					dialogModal("인증메일발송", "인증메일이 발송되었습니다.<br>메일인증확인후 돌아와 완료해주세요", 'success');
				}

			});

		});

		/* 로더 테스트
		$(document).ajaxStart(function() {
			// loading.show();
			console.log('loading-start');

		}).ajaxStop(function() {
			console.log('loading-end');
			// loading.hide();
		}); */


		/* 사전인증 메일발송 - 사용안함 */
		/* $('#sendMail').on('click', function(e) {
			//console.log('sendmail');
			if (!$('#reg_mb_email').val()) {
				commonModal('Mail authentication', '<p>Please enter your mail</p>', 80);
				return;
			}
			$.ajax({
				url: '/bbs/register.mail.verify.php',
				type: 'GET',
				async: false,
				data: {
					"mb_email": $('#reg_mb_email').val()
				},
				dataType: 'json',
				success: function(result) {
					console.log(result);
					key = result.key;

					commonModal('Mail authentication', '<p>Sent a authentication code to your mail.</p>', 80);
				},
				error: function(e) {
					console.log(e);
				}
			});
		}); */



		// 메일 인증 코드 성공
		$('#vCode').on('change', function(e) {
			console.log($('#vCode').val().trim());
			if (key == sha256($('#vCode').val().trim())) {

				console.log("verify OK");
				verify = true;
				$('#verify_txt').show();
				$('#reg_mb_email').css('background-color', '#ccc').prop('readonly', true);;

			} else {
				commonModal('Do not match', '<p>Email verification code is incorrect. Please enter the correct code</p>', 80);
			}
		});

		// 핀번호 (오직 숫자만)
		document.getElementById('reg_tr_password').oninput = function() {
			// if empty
			if (!this.value) return;

			// if non numeric
			let isNum = this.value[this.value.length - 1].match(/[0-9]/g);
			if (!isNum) this.value = this.value.substring(0, this.value.length - 1);

			chkPwd_2($('#reg_tr_password').val(), $('#reg_tr_password_re').val());
		}

		document.getElementById('reg_tr_password_re').oninput = function() {
			// if empty
			if (!this.value) return;

			// if non numeric
			let isNum = this.value[this.value.length - 1].match(/[0-9]/g);
			if (!isNum) this.value = this.value.substring(0, this.value.length - 1);

			chkPwd_2($('#reg_tr_password').val(), $('#reg_tr_password_re').val());
		}

		check_id = 0;
		check_wallet = 0;
		check_email = 0;
		wallet = "";

		// 아이디 중복 체크
		$('#id_check').click(function() {
			

			var registerId = $('#reg_mb_id').val();

			var idReg = /^[a-z]+[a-z0-9]{5,19}$/g;
			if( !idReg.test( registerId ) ) {
				alert("아이디는 영문자로 시작하는 6~20자 영문자 또는 숫자이어야 합니다.");
				return;
			}

			if (registerId.length < 5) {
				dialogModal("ID CHECK", "Please put 5 letters or more", "failed");
			} else {
				$.ajax({
					type: "POST",
					dataType: "json",
					url: "/bbs/register_check_id.php",
					data: {
						"registerId": registerId,
						check: "id"
					},
					success: function(res) {
						if (res.code == '000') {
							check_id = 0;
							dialogModal("ID CHECK", res.response, 'failed');
						} else {
							check_id = 1;
							dialogModal("ID CHECK", '해당아이디는 사용가능합니다.', 'success');
						}
					}
				});
			}
		});

		$("#reg_mb_id").bind("keyup", function() {
			re = /[~!@\#$%^&*\()\-=+_']/gi;
			var temp = $("#reg_mb_id").val();
			if (re.test(temp)) { //특수문자가 포함되면 삭제하여 값으로 다시셋팅
				$("#reg_mb_id").val(temp.replace(re, ""));
			}
			check_id = 0;
		});

		/*이용약관동의*/
		/* $('.agreement_ly').click(function() {
			if ($("#agree").is(":checked") == true) {
				$('#agree').prop("checked", false);
				$('.agreement_ly > span').css('text-decoration', 'none');
			} else {
				$('#agree').prop("checked", true);
				$('.agreement_ly > span').css('text-decoration', 'underline');
			}
		}); */


		$('#reg_mb_password').on('keyup', function(e) {
			chkPwd_1($('#reg_mb_password').val(), $('#reg_mb_password_re').val());
		});
		$('#reg_mb_password_re').on('keyup', function(e) {
			chkPwd_1($('#reg_mb_password').val(), $('#reg_mb_password_re').val());
		});

		/* 
		지갑주소 입력
		$('#wallet_addr_check').click(function() {
			var wallet_addr_len = $('#wallet_addr').val().length;
			console.log(wallet_addr_len);
			if (wallet_addr_len <= 40) {
				dialogModal("wallet check", "Please check wallet address again ", 'failed');
			} else {

				$.ajax({
					type: "POST",
					dataType: "json",
					url: "/bbs/register_check_id.php",
					data: {
						"registerId": $('#wallet_addr').val(),
						check: "wallet"
					},
					success: function(res) {

						if (res.code == '000') {
							check_wallet = 0;
							wallet = "";
							dialogModal("WALLET CHECK", res.response, 'failed');
						} else {
							check_wallet = 1;
							wallet = res.wallet;
							dialogModal("WALLET CHECK", res.response, 'success');
						}
					}
				});


			}
		}); */

	});

	/* 패스워드 확인*/
	function chkPwd_1(str, str2) {
		var pw = str;
		var pw_rule = 0;
		var num = pw.search(/[0-9]/g);
		var eng = pw.search(/[a-z][A-Z]/ig);
		//var eng_large = pw.search(/[A-Z]/ig);
		var spe = pw.search(/[`~!@@#$%^&*|₩₩₩'₩";:₩/?]/gi);

		var pattern = /^(?!((?:[0-9]+)|(?:[a-zA-Z]+)|(?:[\[\]\^\$\.\|\?\*\+\(\)\\~`\!@#%&\-_+={}'""<>:;,\n]+))$)(.){4,}$/;

		if (pw.length < 4) {
			$("#pm_1").attr('class', 'x_li');
		} else {
			$("#pm_1").attr('class', 'o_li');
			pw_rule += 1;
		}

		// if(eng < 0 || num < 0){
		// 	$("#pm_3").attr('class','x_li');
		// }else{
		// 	$("#pm_3").attr('class','o_li');
		// 	pw_rule += 1;
		// }

		if (!pattern.test(pw)) {
			$("#pm_3").attr('class', 'x_li');
		} else {
			$("#pm_3").attr('class', 'o_li');
			pw_rule += 1;
		}


		// if(spe < 0 ){
		// 	$("#pm_3").attr('class','x_li');
		// }else{
		// 	$("#pm_3").attr('class','o_li');
		// 	pw_rule += 1;
		// }


		if (pw_rule == 2 && str == str2) {
			$("#pm_5").attr('class', 'o_li');
			pw_rule += 1;
		} else {
			$("#pm_5").attr('class', 'x_li');
		}

		if (pw_rule == 3) {
			return true;
		} else {
			return false;
		}
	}

	function chkPwd_2(str, str2) {
		var pw_rule = 0;

		if (str.length < 6) {
			$("#pt_1").attr('class', 'x_li');
		} else {
			$("#pt_1").attr('class', 'o_li');
			pw_rule += 1;
		}

		if (str == str2) {
			$("#pt_2").attr('class', 'o_li');
			pw_rule += 1;
		} else {
			$("#pt_2").attr('class', 'x_li');
		}

		if (isNaN(str) && isNaN(str2)) {
			$("#pt_3").attr('class', 'x_li');
		} else {
			$("#pt_3").attr('class', 'o_li');
			pw_rule += 1;
		}

		if (pw_rule >= 3) {
			return true;
		} else {
			return false;
		}
	}


	/*추천인, 센터멤버 등록*/
	function getUser(etarget, type) {
		var target = etarget;
		if (type == 1) {
			var target_type = "#referral";
		}else if(type == 2) {
			var target_type = "#center";
		}else {
			var target_type = "#director";
		}
		console.log(target + ' === ' + type);

		$.ajax({
			type: 'POST',
			url: '/util/ajax.recommend.user.php',
			data: {
				mb_id: $(target).val(),
				type: type
			},
			success: function(data) {
				var list = JSON.parse(data);

				if (list.length > 0) {
					$(target_type).modal('show');
					var vHtml = $('<div>');

					$.each(list, function(index, obj) {
						// vHtml.append($("<div>").addClass('user').html(obj.mb_id));
						
						if(type == 2){
							if(obj.mb_level > 0){
								vHtml.append($("<div style='text-indent:-999px'>").addClass('user').html(obj.mb_id));
								vHtml.append($("<label>").addClass('mb_nick').html(obj.mb_nick));
							}else{
								vHtml.append($("<div style='color:red;text-indent:-999px'>").addClass('non_user').html(obj.mb_id));
								vHtml.append($("<label style='color:red'>").addClass('mb_nick').html(obj.mb_nick));
							}
						}else{

							if(obj.mb_level > 0){
								vHtml.append($("<div>").addClass('user').html(obj.mb_id));
							}else{
								vHtml.append($("<div style='color:red;'>").addClass('non_user').html(obj.mb_id));
							}
						}
						
						/* 
						if (obj.mb_level > 0) {
							

							if(type == 2){
								vHtml.append($("<label>").addClass('mb_nick').html(obj.mb_nick));
							}

						} else {
							if(type == 2){
								
							}else{
								vHtml.append($("<div style='color:red;>").addClass('non_user').html(obj.mb_id));
							}
						} */
					});
			
					$(target_type + ' .modal-body').html(vHtml.html());
					first_select();


					/* 첫번째 선택되어있게 */
					function first_select() {
						$(target_type + ' .modal-body .user:nth-child(1)').addClass('selected');

						if(type == 2){
							$('#reg_mb_center_nick').val($(target_type + ' .modal-body .user.selected').html())
							$(target).val($(target_type + ' .modal-body .user.selected + .mb_nick').html());
						}else{
							$(target).val($(target_type + ' .modal-body .user.selected').html());
						}
					}


					$(target_type + ' .modal-body .user').click(function() {
						// console.log('user click');
						$(target_type + ' .modal-body .user').removeClass('selected');
						$(target + ' .modal-body .user').removeClass('selected');
						$(this).addClass('selected');
					});


					$(target_type + ' .modal-footer #btnSave').click(function() {
						recommend_search = true;
						if(type == 2){
							$('#reg_mb_center_nick').val($(target_type + ' .modal-body .user.selected').html())
							$(target).val($(target_type + ' .modal-body .user.selected + .mb_nick').html());
						}else{
							$(target).val($(target_type + ' .modal-body .user.selected').html());
						}
						$(target_type).modal('hide');
					});

				} else {

					commonModal('Notice', 'MEMBER NOT FOUND', 80);
				}
			}
		});

	} ///*추천인등록*/



	// submit 최종 폼체크
	function fregisterform_submit() {
		var f = $('#fregisterform')[0];
		//console.log(recommend_search);
		/*
		if(key != sha256($('#vCode').val())){
		 	commonModal('Do not match','<p>Please enter the correct code</p>',80);
		 	return false;
		}
		*/

		/* 국가선택 검사*/
		var select_nation = $("#nation_number option:selected").val();

		if(select_nation == "" ){
			commonModal('country check', '<strong>please select country.</strong>', 80);
			return false;
		}

		/* 이사멤버 검사
		if (f.mb_director.value == '' || f.mb_director.value == 'undefined') {
			commonModal('recommend check', '<strong>please check recommend search Button and choose recommend.</strong>', 80);
			return false;
		}
		*/

		//추천인 검사
		if (f.mb_recommend.value == '' || f.mb_recommend.value == 'undefined') {
			commonModal('recommend check', '<strong>please check recommend search Button and choose recommend.</strong>', 80);
			return false;
		}
		if (!recommend_search) {
			commonModal('recommend check', '<strong>please check recommend search Button and choose recommend.</strong>', 80);
			return false;
		}

		//센터멤버 검사
		// if (f.mb_center.value == '' || f.mb_center.value == 'undefined') {
		// 	commonModal('recommend check', '<strong>please check recommend search Button and choose recommend.</strong>', 80);
		// 	return false;
		// }
		
		//추천인이 본인인지 확인
		if (f.mb_id.value == f.mb_recommend.value) {
			commonModal('recommend check', '<strong> can not recommend self. </strong>', 80);
			f.mb_recommend.focus();
			return false;
		}

		// 이름
		if (f.mb_name.value == '' || f.mb_name.value == 'undefined') {
			commonModal('이름입력확인', '<strong>이름을 확인해주세요.</strong>', 80);
			return false;
		}
		
		//아이디 중복체크
		if (check_id == 0) {
			commonModal('ID 중복확인', '<strong>아이디 중복확인을 해주세요. </strong>', 80);
			return false;
		}

		// 연락처
		if (f.mb_hp.value == '' || f.mb_hp.value == 'undefined') {
			commonModal('휴대폰번호확인', '<strong>휴대폰 번호가 잘못되거나 누락되었습니다. </strong>', 80);
			return false;
		}

		// 주소
		const mb_addr1 = $("#mb_addr1").val();
		const mb_addr2 = $("#mb_addr2").val();
		if(!mb_addr1 || !mb_addr2) {
			commonModal('주소확인', '<strong>주소가 잘못되거나 누락되었습니다. </strong>', 80);
			return false;
		}

		// 계좌번호
		const bank_name = $("#bank_name").val();
		const account_name = $("#account_name").val();
		const bank_account = $("#bank_account").val();
		if(!bank_name || !account_name || !bank_account) {
			commonModal('계좌번호확인', '<strong>계좌정보가 잘못되거나 누락되었습니다. </strong>', 80);
			return false;
		}

		// 패스워드
		if (!chkPwd_1($('#reg_mb_password').val(), $('#reg_mb_password_re').val())) {
			commonModal('password rule check', '<strong> login Password does not match password Rule.</strong>', 80);
			return false;
		}

		// // 핀코드
		if (!chkPwd_2($('#reg_tr_password').val(), $('#reg_tr_password_re').val())) {
			commonModal('Check password Rule', '<strong> Transaction Password does not match password Rule.</strong>', 80);
			return false;
		}

		/*이용약관 체크*/
		for (var i = 0; i < $("input[name=term]:checkbox").length; i++) {
			if ($("input[name=term]:checkbox")[i].checked == false) {
				commonModal('check the policy agreement', '<strong>이용약관에 동의해주세요.</strong>', 80);
				return false;
			}

		}
	
		// 메일인증 체크
		$.ajax({
			type: "POST",
			url: "/mail/check_mail_for_register.php",
			cache: false,
			async: false,
			dataType: "json",
			data: {
				user_email: $('#reg_mb_email').val()
			},
			success: function(res) {
				if (res.result == "OK") {
					mail_check = 1;
					f.submit();
				} else {
					mail_check = 0;
					dialogModal("Email Auth", res.res, 'failed');

				}

			},
			error: function(e) {
				console.log(e)
			}
		});


	}
</script>

<div class="v_center">

	<div class="enroll_wrap">
		<form id="fregisterform" name="fregisterform" action="/bbs/register_form_update.php" method="post" enctype="multipart/form-data" autocomplete="off">

		<p class="check_appear_title mt10"><span >국가선택</span></p>
			<div class="mb20">
				<select id="nation_number" name="nation_number" required >
					<option value="" >거주국가를 선택해주세요</option>
					<option value="82" title="<?=national_flag('82')?>">Korea</option>
					<option value="1" title="<?=national_flag('1')?>"> USA</option>
					<option value="81" title="<?=national_flag('81')?>">Japan</option>
					<option value="84" title="<?=national_flag('84')?>">Vietnam</option>
					<option value="86" title="<?=national_flag('86')?>">China</option>
					<option value="66" title="<?=national_flag('66')?>">Thailand</option>
				</select>
			</div>


			<!-- 추천인 정보 -->
			<p class="check_appear_title mt10"><span >추천인 등록</span></p>
			<section class='referzone'>
				<div class="btn_input_wrap">
					<input type="text" name="mb_recommend" id="reg_mb_recommend" value="<?= $mb_recommend ?>" required placeholder="추천인 아이디" />
					<div class='in_btn_ly2'>
						<button type='button' class="btn_round check " onclick="getUser('#reg_mb_recommend',1);" style=""><span data-i18n="signUp.검색">Search</span></button>
					</div>
					
				</div>
			</section>

			<!-- 센터 정보 -->
			<!-- <p class="check_appear_title mt20"><span data-i18n="signUp.센터정보">Referrer's Information</span></p>
			<section class='referzone'>
				<div class="btn_input_wrap">
					<input type="hidden" name="mb_center_nick" id="reg_mb_center_nick" value=""  required  />
					<input type="text" name="mb_center" id="reg_mb_center" value="" placeholder="센터명 또는 센터아이디" required  />

					<div class='in_btn_ly2'>
						<button type='button' class="btn_round check " onclick="getUser('#reg_mb_center',2);"
						><span data-i18n="signUp.검색">Search</span></button>
					</div>
				</div>
			</section> -->


			<!-- <p class="check_appear_title mt40"><span data-i18n='signUp.일반정보'>General Information</span></p> -->
			<p class="check_appear_title mt40"><span data-i18n='signUp.개인 정보 & 인증'>Personal Information & Authentication </span></p>
			<div>
				<div class='id_set mt20'>
					<input type="text" minlength="5" maxlength="20" name="mb_name" style='padding:15px' id="reg_mb_name" required placeholder="이름"  />
					<input type="text" minlength="5" maxlength="20" name="mb_id" class='cabinet' style='padding:15px' id="reg_mb_id" required placeholder="아이디"/>
					<span class='cabinet_inner' style=''>※영문+숫자조합 6자리 이상 입력해주세요</span>
					<div class='in_btn_ly'><input type="button" id='id_check' class='btn_round check' value="ID Check" data-i18n='[value]signUp.중복확인'></div>
				</div>


				<div class='phone_set mt20'>
					<input type="email"  id="reg_mb_email" name="mb_email" class='cabinet' style='padding:15px'placeholder="Email address" required data-i18n='[placeholder]signUp.이메일 주소' />
					<span class='cabinet_inner' style=''>※수신가능한 이메일주소를 직접 입력해주세요</span>
					<div class='in_btn_ly'><input type="button" id='EmailChcek' class='btn_round check' value="Eamil" data-i18n='[value]signUp.이메일 전송'></div>
					<input type="text" name="mb_hp"  id="reg_mb_hp" class='cabinet'  pattern="[0-9]*" style='padding:15px' required  placeholder="휴대폰번호"/>
					<span class='cabinet_inner' style=''>※'-'를 제외한 숫자만 입력해주세요</span>
				</div>

				
				
				<div class='addr_set mt20'>
					<input type="text" name="mb_addr1"  id="mb_addr1" style='padding:15px' required  placeholder="주소" readonly />

					<div id="map_wrap" class="map_wrap"></div>

					<input type="text" name="mb_addr2"  id="mb_addr2" style='padding:15px' required  placeholder="상세주소" maxlength="150" autocomplete="off" />
				</div>


				<hr class="light_line">

				<div class="bank_info_set mt20 ">
					<div class="row ">
						<div class="col-6">
							<input type="text" name="bank_name" id="bank_name" class="" style='padding:15px' required placeholder="은행명" maxlength="30" autocomplete="off" />
						</div>
						<div class="col-6">
							<input type="text" name="account_name" id="account_name" class="" style='padding:15px' required placeholder="예금주" maxlength="30" autocomplete="off" />
						</div>
					</div>

					<input type="text" name="bank_account" id="bank_account" pattern="[0-9]*" class='cabinet' style='padding:15px' required placeholder="계좌번호" maxlength="50" autocomplete="off" />
					<span class='cabinet_inner' style=''>※'-'를 제외한 숫자만 입력해주세요</span>
				</div>
			</div>


			<ul class="clear_fix pw_ul mt20">
				<li>
					<input type="password" name="mb_password" id="reg_mb_password" minlength="4" placeholder="Login Password" data-i18n='[placeholder]signUp.로그인 비밀번호' />
					<input type="password" name="mb_password_re" id="reg_mb_password_re" minlength="4" placeholder="Confirm login password" data-i18n='[placeholder]signUp.로그인 비밀번호 확인' />

					<strong><span class='mb10' style='display:block;font-size:13px;' data-i18n='signUp.강도 높은 비밀번호 설정 조건'>Your password must contain</span></strong>
					<ul>
						<li class="x_li" id="pm_1" data-i18n='signUp.4자 이상 20자 이하'>4 characters or more</li>
						<li class="x_li" id="pm_3" data-i18n='signUp.숫자+영문'>Digits + Characters</li>
						<li class="x_li" id="pm_5" data-i18n='signUp.비밀번호 비교'>Compare Password</li>
					</ul>
				</li>
				<li style='margin-left:5px'>
					<input type="password" minlength="6" maxlength="6" id="reg_tr_password" name="reg_tr_password" placeholder="Pin-Code" data-i18n='[placeholder]signUp.핀코드' />
					<input type="password" minlength="6" maxlength="6" id="reg_tr_password_re" name="reg_tr_password_re" placeholder="confirm Pin-Code" data-i18n='[placeholder]signUp.핀코드확인' />

					<strong><span class='mb10' style='display:block;font-size:13px;' data-i18n='signUp.강도 높은 핀코드 설정 조건'>Your Pin-code must contain</span></strong>
					<ul>
						<li class="x_li" id="pt_1" data-i18n='signUp.6 자리'>6 digits</li>
						<li class="x_li" id="pt_3" >숫자</li>
						<li class="x_li" id="pt_2" data-i18n='signUp.핀코드 비교'>Compare Pin-code</li>
					</ul>
				</li>
			</ul>


			<!-- 폰인증
			<section id="personal">
				<div>
					<span style='display:block;margin-left:10px;' class='' data-i18n='signUp.핸드폰 번호'> Phone number</span>
					<input type="text" name="mb_hp"  id="reg_mb_hp"  pattern="[09]*" placeholder="Phone number" value='' data-i18n='[placeholder]signUp.핸드폰 번호'/>
					<label class='phone_num'><i class="ri-smartphone-line"></i></label>
				</div>
					<?if($phone_auth > 1){?>
					<div class="clear_fix ecode_div">
					<div class="verify_phone">
						<input type="text" placeholder="Enter Phone Authtication Code"/>
						<a href="javascript:void(0)" class=""  id="sendSms">
							<img src="<?= G5_THEME_URL ?>/_images/email_send_icon.gif" alt="이메일코드">
							Enter Phone Authtication Code
						</a>
						</div>
					</div>
					<?}?>
			</section>
			-->

			<!--
			<hr>
			<div class="agreement_btn"> <button type="button" class="agreeement_show btn"><span data-i18n='register.회원가입 약관보기'>Read Terms and Conditions</span></button></div>
			-->

			<p class="check_appear_title mt40"><span >회원가입 약관동의 </span></p>
			<div class="mt20">
				<div class="term_space">
					<input type="checkbox" id="service_checkbox" class="term_none" name="term" >
					<label for="service_checkbox" style="width:25px;height:25px;">
						<span style='margin-left:10px;line-height:30px;'><?= $service_term['wr_subject'] ?> 및 서약서 동의</span>
						<a id="service" href="javascript:collapse('#service');"  style="width:25px;height:25px;position:absolute;right:25px;"><i class="fas fa-angle-down" style="width:25px;height:25px;"></i></a>
					</label>
				</div>
				
				<textarea id="service_term" class="term_textarea term_none"><?= $service_term['wr_content'] ?></textarea>

				<div class="term_space">
					<input type="checkbox" id="private_checkbox" class="term_none" name="term" >
					<label for="private_checkbox" style="width:25px;height:25px;">
						<span style='margin-left:10px;line-height:30px;'><?= $private_term['wr_subject'] ?> 동의</span>
						<a id="private" href="javascript:collapse('#private');"  style="width:25px;height:25px;position:absolute;right:25px;"><i class="fas fa-angle-down" style="width:25px;height:25px;"></i></a>
					</label>
				</div>
				<textarea id="private_term" class="term_textarea term_none"><?= $private_term['wr_content'] ?></textarea>
			</div>
			

			<div class="btn2_wrap mb40" style='width:100%;height:60px'>
				<input class="btn btn_double enroll_cancel_pop_open btn_cancle pop_open" type="button" value="Cancel" data-i18n='[value]signUp.취소'>
				<input class="btn btn_double btn_primary" type="button" onclick="fregisterform_submit();" value="Enroll new member" data-i18n='[value]signUp.신규 회원 등록하기'>
			</div>
		</form>
	</div>

</div>
</section>

<div class="gnb_dim"></div>


<script>
	$(function() {
		$(".top_title h3").html("<span data-i18n='title.신규 회원등록' style='font-size:16px;margin-left:20px'>Create a new account</span>");

		const map_wrap = document.getElementById('map_wrap');
		const mb_addr1 = document.getElementById("mb_addr1");
		const mb_addr2 = document.getElementById("mb_addr2");
		
		const hidden_map = () => map_wrap.style.display = 'none';

		hidden_map();

		mb_addr1.addEventListener('click', function() {
			mb_addr1.style.display = 'none';

			select_addr();
		});

		const select_addr = () => {
			let current_scroll = Math.max(document.body.scrollTop, document.documentElement.scrollTop);

			new daum.Postcode({
				oncomplete: function(data) {
					mb_addr1.value = data.address;
					mb_addr1.style.display = 'block';
					mb_addr2.focus();

					map_wrap.style.display = 'none';

					// 우편번호 찾기 화면이 보이기 이전으로 scroll 위치를 되돌린다.
					document.body.scrollTop = current_scroll;
				},
				// iframe을 넣은 element의 높이값을 조정
				onresize : function(size) {
					map_wrap.style.height = size.height+'px';
				},
				width : '100%',
				height : '100%'
			}).embed(map_wrap);

			map_wrap.style.display = 'block';
		}
	
	});

	function collapse(id) {
			if ($(id + "_term").css("display") == "none") {
				$(id + "_term").css("display", "block");
				$(id + "_term").animate({
					height: "150px"
				}, 100, function() {
					$(id + ' .svg-inline--fa').css('transform', "rotate(180deg)");
				});
			} else {
				$(id + "_term").animate({
					height: "0px"
				}, 100, function() {
					$(id + "_term").css("display", "none");
					$(id + ' .svg-inline--fa').css('transform', "rotate(360deg)");
				});
			}
		}
</script>
