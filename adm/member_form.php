<?php
$sub_menu = "200100";
include_once('./_common.php');
include_once(G5_PATH.'/util/package.php');


auth_check($auth[$sub_menu], 'w');

/* admin 슈퍼관리자 수정불가 코드*/
if($_GET['mb_id'] == 'admin' && $member['mb_id'] != 'admin'){
	alert('슈퍼관리자는 수정할수없습니다.','/adm/member_list.php');
}

function bonus_pick($val){    
    global $g5;
    $pick_sql = "select * from {$g5['bonus_config']} where code = '{$val}' ";
    $list = sql_fetch($pick_sql);
    return $list;
}

$week_bonus_result = bonus_pick('weekend')['rate'];
$week_bonus = explode(',',$week_bonus_result);

if ($w == '')
{
	$required_mb_id = 'required';
	$required_mb_id_class = 'required alnum_';
	$required_mb_password = 'required';
	$sound_only = '<strong class="sound_only">필수</strong>';

	$mb['mb_mailling'] = 1;
	$mb['mb_open'] = 1;
	$mb['mb_level'] = $config['cf_register_level'];
	$html_title = '추가';

}else if ($w == 'u'){

	$mb = get_member($mb_id);
	if (!$mb['mb_id'])
		alert('존재하지 않는 회원자료입니다.');

	if ($is_admin != 'super' && $mb['mb_level'] >= $member['mb_level'])
		alert('자신보다 권한이 높거나 같은 회원은 수정할 수 없습니다.');

	$required_mb_id = 'readonly';
	$required_mb_password = '';
	$html_title = '수정';

	$mb['mb_name'] = get_text($mb['mb_name']);
	$mb['mb_nick'] = get_text($mb['mb_nick']);
	$mb['mb_email'] = get_text($mb['mb_email']);
	$mb['mb_homepage'] = get_text($mb['mb_homepage']);
	$mb['mb_birth'] = get_text($mb['mb_birth']);
	$mb['mb_tel'] = get_text($mb['mb_tel']);
	$mb['mb_hp'] = get_text($mb['mb_hp']);
	$mb['mb_addr1'] = get_text($mb['mb_addr1']);
	$mb['mb_addr2'] = get_text($mb['mb_addr2']);
	$mb['mb_addr3'] = get_text($mb['mb_addr3']);
	$mb['mb_signature'] = get_text($mb['mb_signature']);
	$mb['mb_recommend'] = get_text($mb['mb_recommend']);
	$mb['mb_brecommend'] = get_text($mb['mb_brecommend']);
	$mb['mb_profile'] = get_text($mb['mb_profile']);
	$mb['mb_1'] = get_text($mb['mb_1']);
	$mb['mb_2'] = get_text($mb['mb_2']);
	$mb['mb_3'] = get_text($mb['mb_3']);
	$mb['mb_4'] = get_text($mb['mb_4']);
	$mb['mb_5'] = get_text($mb['mb_5']);
	$mb['mb_6'] = get_text($mb['mb_6']);
	$mb['mb_7'] = get_text($mb['mb_7']);
	$mb['mb_8'] = get_text($mb['mb_8']);
	$mb['mb_9'] = get_text($mb['mb_9']);
	$mb['mb_10'] = get_text($mb['mb_10']);
	$mb['grade'] = get_text($mb['grade']);
}
else
	alert('제대로 된 값이 넘어오지 않았습니다.');

// 본인확인방법
switch($mb['mb_certify']) {
	case 'hp':
		$mb_certify_case = '휴대폰';
		$mb_certify_val = 'hp';
		break;
	case 'ipin':
		$mb_certify_case = '아이핀';
		$mb_certify_val = 'ipin';
		break;
	case 'admin':
		$mb_certify_case = '관리자 수정';
		$mb_certify_val = 'admin';
		break;
	default:
		$mb_certify_case = '';
		$mb_certify_val = 'admin';
		break;
}

// 본인확인
$mb_certify_yes  =  $mb['mb_certify'] ? 'checked="checked"' : '';
$mb_certify_no   = !$mb['mb_certify'] ? 'checked="checked"' : '';

// 성인인증
$mb_adult_yes       =  $mb['mb_adult']      ? 'checked="checked"' : '';
$mb_adult_no        = !$mb['mb_adult']      ? 'checked="checked"' : '';

//메일수신
$mb_mailling_yes    =  $mb['mb_mailling']   ? 'checked="checked"' : '';
$mb_mailling_no     = !$mb['mb_mailling']   ? 'checked="checked"' : '';

// SMS 수신
$mb_sms_yes         =  $mb['mb_sms']        ? 'checked="checked"' : '';
$mb_sms_no          = !$mb['mb_sms']        ? 'checked="checked"' : '';

// 정보 공개
$mb_open_yes        =  $mb['mb_open']       ? 'checked="checked"' : '';
$mb_open_no         = !$mb['mb_open']       ? 'checked="checked"' : '';

// 지급차단
if($mb['mb_block']){
	$mb_block_yes = 'checked="checked"';
}else{
	$mb_block_no = 'checked="checked"';
}

if (isset($mb['mb_certify'])) {
	// 날짜시간형이라면 drop 시킴
	if (preg_match("/-/", $mb['mb_certify'])) {
		sql_query(" ALTER TABLE `{$g5['member_table']}` DROP `mb_certify` ", false);
	}
} else {
	sql_query(" ALTER TABLE `{$g5['member_table']}` ADD `mb_certify` TINYINT(4) NOT NULL DEFAULT '0' AFTER `mb_hp` ", false);
}

if(isset($mb['mb_adult'])) {
	sql_query(" ALTER TABLE `{$g5['member_table']}` CHANGE `mb_adult` `mb_adult` TINYINT(4) NOT NULL DEFAULT '0' ", false);
} else {
	sql_query(" ALTER TABLE `{$g5['member_table']}` ADD `mb_adult` TINYINT NOT NULL DEFAULT '0' AFTER `mb_certify` ", false);
}

// 지번주소 필드추가
if(!isset($mb['mb_addr_jibeon'])) {
	sql_query(" ALTER TABLE {$g5['member_table']} ADD `mb_addr_jibeon` varchar(255) NOT NULL DEFAULT '' AFTER `mb_addr2` ", false);
}

// 건물명필드추가
if(!isset($mb['mb_addr3'])) {
	sql_query(" ALTER TABLE {$g5['member_table']} ADD `mb_addr3` varchar(255) NOT NULL DEFAULT '' AFTER `mb_addr2` ", false);
}

// 중복가입 확인필드 추가
if(!isset($mb['mb_dupinfo'])) {
	sql_query(" ALTER TABLE {$g5['member_table']} ADD `mb_dupinfo` varchar(255) NOT NULL DEFAULT '' AFTER `mb_adult` ", false);
}

// 이메일인증 체크 필드추가
if(!isset($mb['mb_email_certify2'])) {
	sql_query(" ALTER TABLE {$g5['member_table']} ADD `mb_email_certify2` varchar(255) NOT NULL DEFAULT '' AFTER `mb_email_certify` ", false);
}


$bonus_per = bonus_per($mb['mb_id'],$mb['mb_balance'],$mb['mb_pv']);

if ($mb['mb_intercept_date']) $g5['title'] = "차단된 ";
else $g5['title'] .= "";
$g5['title'] .= '회원 '.$html_title;
include_once('./admin.head.php');

$rank_sql = "select * from rank where mb_id = '{$mb['mb_id']}' and rank = '{$mb['mb_level']}' ";
	$rank_result = sql_fetch($rank_sql);

// add_javascript('js 구문', 출력순서); 숫자가 작을 수록 먼저 출력됨
// add_javascript(G5_POSTCODE_JS, 0);    //다음 주소 js
?>

<link rel="stylesheet" href="<?=G5_THEME_URL?>/css/scss/custom.css">
<link rel="stylesheet" href="./css/scss/admin_custom.css">
<script src="//t1.daumcdn.net/mapjsapi/bundle/postcode/prod/postcode.v2.js"></script>
<script src="<?=G5_THEME_URL?>/_common/js/common.js" crossorigin="anonymous"></script>


<style>
	.ly_up{height:60px;}
	.ly_up .ups{background:linen;}
	.ly_up.padding-box{height:60px;}
	.account_box{padding:0px;height:60px;}
	.account_box th,.account_box td{border:0;height:100%;padding-left:10px;}

	.hidden{display:none;}

	select{width:auto;min-width:150px;height:36px;}
	option{line-height:36px;}

	.kyc_btn{background:ROYALBLUE;padding:10px 20px;color:white;}
	.kyc_btn i {vertical-align:sub}

	a.btn,span.btn {display:inline-block;*display:inline;*zoom:1;padding:0 10px;height:24px;line-height:24px;vertical-align:middle;color:#fff;cursor:pointer;}
	.btn.flexible{height:34px;line-height:34px;width:60px;text-align:center;border-radius:0;opacity:0.7}
	.btn.flexible{background:rgb(76,100,127);}
	.btn.flexible.btn2{background:rgb(139 86 175);}
	.btn.flexible:hover{opacity:1}
	.wallet_addr{display:inline-block;}
	.badge{padding:10px 10px;font-weight:700;}
	.copybutton{margin-left:10px; background:rgba(76,100,127,1);color:white;padding:5px 20px;border:0;box-shadow:0;border-radius:20px;}

	#mb_nick_regist{display:none;}
	#mb_nick_regist.active{display:inline;}
	.frm_input{padding:5px;min-width:150px;}
	.fund {line-height:38px;}
	.fund input{vertical-align: middle;line-height:26px;padding:1px;}
	.fund .be_to{font-size:15px;margin-left:10px;border:0;box-shadow:none;background:transparent;width:80px;}

	.math_btn{width:26px;height:26px;border:1px solid #ccc; padding:1px;font-size:20px;cursor:pointer}
	.math_btn.plus.active{background:blue;border:1px solid blue;color:white}
	.math_btn.minus.active{background:red;border:1px solid red;color:white}

	#field_upstair{padding-left:10px;font-size:13px;font-weight: 900;}

	.strong{font-weight:900;font-size:13px;}

	.bonus{color:#0072d1;}
	.mining{color:green;}
	.soodang{color:orangered;}
	.mining_soodang{color:#4c124b;}
	.amt {color:red}
	.divide-top th,.divide-top td{border-top:2px solid #333;padding:3px 0;text-align:center;font-size:11px}
	.divide-bottom th,.divide-bottom td{border-bottom:2px solid #333;padding-bottom:30px;}
	
	.purchase_btn{display:inline-grid;height:40px;padding:0;}
	.purchase_btn.pack{width:150px;margin-left:20px;color:white}			
	.pack_title{font-weight:600;padding:1px 10px;color:#fff;min-width:50px;display:grid;border-top-left-radius:5px;border-top-right-radius:5px}
	
	.pack_have{font-size:16px;font-weight:600;padding:5px;color:red}


	.search_container {
		position: absolute;
		left: 50px;
		top: 0;
		width: 450px;
		height: 300px;
		background-color:ghostwhite;
		box-shadow: 0 3px 3px rgba(0,0,0,0.5);
		text-align: center;
		padding: 10px;
		transition: all 2s;
		overflow: hidden;
		display:none;
	}
	.search_result{
		max-height:270px;
		overflow-y:scroll;
	}
	.search_container.active{
		display:block;
	}
	.search_container ul{margin:0;padding:0;width:100%;height:100%;}
	.search_container li{list-style:none;display:flex;line-height:30px;margin:5px 0;width:100%;background:white;cursor: pointer;}
	.search_container .header li{border-bottom:1px solid #333;font-weight:600;background:ghostwhite;line-height:18px;}
	.search_container li span{padding:0 5px;width:20%;}
	.search_container li .level{}
	.search_container li .id{font-weight:900;font-size:13px;}
	.search_container li .name{}
	.search_container li .nick{}

	.search_container li:hover{background:#fee500}

	.search_container .result_btn{
		position: absolute;
		bottom: 5px;
		display: table;
		width: inherit;
		height: 30px;
		background: #555;
		color:white;
		font-weight:600;
		line-height: 30px;
		cursor: pointer;
	}
</style>

<form name="fmember" id="fmember" action="./member_form_update.php" onsubmit="return fmember_submit(this);" method="post" enctype="multipart/form-data">
<input type="hidden" name="w" value="<?php echo $w ?>">
<input type="hidden" name="sfl" value="<?php echo $sfl ?>">
<input type="hidden" name="stx" value="<?php echo $stx ?>">
<input type="hidden" name="sst" value="<?php echo $sst ?>">
<input type="hidden" name="sod" value="<?php echo $sod ?>">
<input type="hidden" name="page" value="<?php echo $page ?>">
<input type="hidden" name="rank" value="<?=$mb['rank'] ?>">
<input type="hidden" name="token" value="">

<div class="local_desc01 local_desc">
    <p>
		- 최초 닉네임지정시 체크시 <strong>닉네임 등록</strong> : (닉네임으로 센터,지사,지점,본부 동일 검색사용)<br>
		- 센터/지사/지점/본부 회원은 직접검색-등록하거나(정회원이상) <strong>자동(상위에 해당직급이 있는경우)으로 자동검색</strong> 가능 <br>
		<!-- - <strong>마케팅-승급처리시</strong> 센터/지사/지점/본부 회원은 자동 업데이트  -->
	</p>
</div>

<div class="tbl_frm01 tbl_wrap">
	<table>
	<caption><?php echo $g5['title']; ?></caption>
	<colgroup>
		<col class="grid_4">
		<col>
		<col class="grid_4">
		<col>
	</colgroup>
	<tbody>
	<input type="hidden" name='mb_no' value="<?=$mb['mb_no']?>">
	<tr class='divide-top'>
		<td colspan="4" style="background:aliceblue;">회원기본정보</td>
	<tr>
	<tr>
		<th scope="row"><label for="mb_id">아이디<?php echo $sound_only ?></label></th>
		<td>
			<? if ($w == "u") { ?>
			<input type="hidden" name="mb_id" id="mb_id" value="<?=$mb['mb_id']?>" />
			<?=$mb['mb_id']?>

			<? } else { ?>
			<input type="text" name="mb_id" value="<?php echo $mb['mb_id'] ?>" id="mb_id" <?php echo $required_mb_id ?> class="frm_input <?php echo $required_mb_id_class ?>" size="15" minlength="3" maxlength="20">
			<?php if ($w=='u'){ ?><a href="./boardgroupmember_form.php?mb_id=<?php echo $mb['mb_id'] ?>">접근가능그룹보기</a><?php } ?>
			<? } ?>

		</td>

		<th scope="row"><label for="mb_id">이름<?php echo $sound_only ?></label></th>
		<td>
			<? if ($w == "u") { ?>
			<input type="text" name="mb_name" id="mb_name" class='frm_input ' value="<?=$mb['mb_name']?>" />
			<? }?>
		</td>
		
  </tr>

	<tr>
	<th scope="row"><label for="mb_password">비밀번호<?php echo $sound_only ?></label></th>
		<td><input type="password" name="mb_password" id="mb_password" <?php echo $required_mb_password ?> class="frm_input <?php echo $required_mb_password ?>" size="15" maxlength="20"></td>
		<th scope="row"><label for="reg_tr_password">핀번호</label></th>
		<td><input type="password" name="reg_tr_password" id="reg_tr_password" class="frm_input " size="15" maxlength="6"></td>
	</tr>

	<tr>
		<th scope="row"><label for="mb_email">E-mail<strong class="sound_only">필수</strong></label></th>
		<td><input type="text" name="mb_email" value="<?php echo $mb['mb_email'] ?>" id="mb_email" maxlength="100" class="frm_input email " size="30">
		<!--
		<?if($member['mb_email_certify'] != ''){?>
				<img src="<?=G5_THEME_URL?>/_images/okay_icon.gif" alt="인증됨" style="width:15px;"> 인증됨
			<?}else{?>
				<img src="<?=G5_THEME_URL?>/_images/x_icon.gif" alt="인증안됨" style="width:15px;"> 인증안됨
		<?}?>
		-->
		</td>
		<th scope="row"><label for="mb_hp">휴대폰번호</label></th>
		<td>
			<!-- <input type="text" name="nation_number" value="<?php echo $mb['nation_number'] ?>" id="nation_number" class="frm_input" style="height:36px;text-align:center" size="5" maxlength="50"> -->
			<input type="text" name="mb_hp" value="<?php echo $mb['mb_hp'] ?>" id="mb_hp" class="frm_input  " size="15" maxlength="20">
			
			<!-- <?if($member['mb_certify'] == 1){?>
				<img src="<?=G5_THEME_URL?>/_images/okay_icon.gif" alt="인증됨" style="width:15px;"> 인증됨
			<?}else{?>
				<img src="<?=G5_THEME_URL?>/_images/x_icon.gif" alt="인증안됨" style="width:15px;"> 인증안됨
			<?}?> -->
			
		</td>
		<!-- <th scope="row"><label for="mb_hp">휴대폰번호</label></th>
		<td>
			<input type="text" name="nation_number" value="<?php echo $mb['nation_number'] ?>" id="nation_number" class="frm_input" style="height:36px;text-align:center" size="5" maxlength="50">
			<input type="text" name="mb_hp" value="<?php echo $mb['mb_hp'] ?>" id="mb_hp" class="frm_input  " size="15" maxlength="20">
			
			<?if($member['mb_certify'] == 1){?>
				<img src="<?=G5_THEME_URL?>/_images/okay_icon.gif" alt="인증됨" style="width:15px;"> 인증됨
			<?}else{?>
				<img src="<?=G5_THEME_URL?>/_images/x_icon.gif" alt="인증안됨" style="width:15px;"> 인증안됨
			<?}?>
			
		</td> -->
	</tr>

	<tr>
		<th scope="row">
			<label for="mb_addr1">주소</label>
		</th>

		<td>
			<input type="text" name="mb_addr1" value="<?php echo $mb['mb_addr1']; ?>" id="mb_addr1" class="frm_input mb_addr1 " style="width: 80%;" readonly />
		</td>


		<th scope="row">
			<label for="mb_add2">상세주소</label>
		</th>

		<td>
			<input type="text" name="mb_addr2" value="<?php echo $mb['mb_addr2']; ?>" id="mb_addr2" maxlength="150" class="frm_input mb_addr2 " style="width: 80%;" autocomplete="off" />
		</td>
	</tr>

	<tr>
		<th scope="row"><label for="grade">회원 등급</label></th>
		<td ><?echo "<img src='/img/".$mb['grade'].".png' style='width:40px;height:40px;'>";?><?php echo get_grade_select('grade', 0, $member['grade'], $mb['grade']) ?><?=$rank_result['rank_day']?></div></td>


		<th scope="row"><label for="mb_level">회원 레벨</label></th>
		<td ><?php echo get_member_level_select('mb_level', 0, $member['mb_level'], $mb['mb_level']) ?> <div> </td>
	</tr>


	<tr class="hidden">
		<th scope="row"><label for="mb_homepage">홈페이지</label></th>
		<td><input type="text" name="mb_homepage" value="<?php echo $mb['mb_homepage'] ?>" id="mb_homepage" class="frm_input" maxlength="255" size="15"></td>
		<th scope="row"><label for="mb_tel">전화번호</label></th>
		<td><input type="text" name="mb_tel" value="<?php echo $mb['mb_tel'] ?>" id="mb_tel" class="frm_input" size="15" maxlength="20"></td>
	</tr>

	

	<?php if ($config['cf_use_recommend']) { // 추천인 사용 ?>
	<tr class='divide-top'>
		<td colspan="4" style="background:paleturquoise;">회원추천및 관계설정</td>
	<tr>
	<tr >
		<th scope="row">추천인</th>
		<td colspan="1">
			<!-- <input type="text" name="mb_recommend" id="mb_recommend" value="<?=$mb['mb_recommend']?>" class="frm_input " />
			<span id="ajax_rcm_search" class="btn flexible">검색</span>
			<span class='exp_nick'><?=get_name($mb['mb_recommend'])?></span> -->

			<input type="text" name="mb_recommend" id="mb_recommend" value="<?=$mb['mb_recommend']?>" class="frm_input " />
			<span class="btn flexible member_search" data-category="recommend">검색</span>
			<span class='exp_nick'><?=get_name($mb['mb_recommend'])?></span>
		</td>
		
		
		<th scope="row">닉네임지정</th>
		<td colspan="1">
			<input type="checkbox" style='width:24px;height:24px;text-align:left' name="center_use" id="center_use" value=" <?=$mb['center_use']?> " class="" <? if($mb['center_use'] == '1') {echo "checked";}?> />
			<?if($mb['center_use']>0){ $center_regist_class = 'active';}else{$center_regist = '';}?>
			
			<div id='mb_nick_regist' class="<?if($mb['center_use']>0){echo 'active';}?>">
			| 닉네임 : 
			<input type="text" name="mb_nick" id="mb_nick_field" value="<?=$mb['mb_nick']?>" class="frm_input" />
			</div>
			
		</td>
	</tr>
	<?php } ?>

	<tr>

		<th scope="row">센터회원</th>
		<td colspan="1">
			<input type="text" name="mb_center" id="mb_center" value="<?=$mb['mb_center']?>" class="frm_input " />
			<span class="btn flexible member_search" data-category="center">검색</span>
			<span class="btn flexible btn2 member_auto_regist" data-category="center" data-category_value="2">자동등록</span>
			<span class='exp_nick'><?=get_name($mb['mb_center'])?></span>
		</td>

		<th scope="row">지점회원</th>
		<td colspan="1">
			<input type="text" name="mb_jijum" id="mb_jijum" value="<?=$mb['mb_jijum']?>" class="frm_input " />
			<span class="btn flexible member_search" data-category="jijum">검색</span>
			<span class="btn flexible btn2 member_auto_regist" data-category="jijum" data-category_value="3">자동등록</span>
			<span class='exp_nick'><?=get_name($mb['mb_jijum'])?></span>
		</td>
		
	</tr>

	<tr>
		<th scope="row">지사회원</th>
		<td colspan="1">
			<input type="text" name="mb_jisa" id="mb_jisa" value="<?=$mb['mb_jisa']?>" class="frm_input " />
			<span class="btn flexible member_search" data-category="jisa">검색</span>
			<span class="btn flexible btn2 member_auto_regist" data-category="jisa" data-category_value="4">자동등록</span>
			<span class='exp_nick'><?=get_name($mb['mb_jisa'])?></span>
		</td>
	
		<th scope="row">본부회원</th>
		<td colspan="1">
			<input type="text" name="mb_bonbu" id="mb_bonbu" value="<?=$mb['mb_bonbu']?>" class="frm_input " />
			<span class="btn flexible member_search" data-category="bonbu">검색</span>
			<span class="btn flexible btn2 member_auto_regist" data-category="bonbu" data-category_value="5">자동등록</span>
			<span class='exp_nick'><?=get_name($mb['mb_bonbu'])?></span>
		</td>

	</tr>

	
	

	<tr class='divide-top'>
		<td colspan="4" style="background:#fff1ab;">회원자산 및 상품현황</td>
	<tr>

	<tr class="ly_up padding-box fund">
		<th scope="row">보유 잔고 (<?=ASSETS_CURENCY?>)</th>
		
		<td colspan="1">
			<strong><?=shift_auto_zero($mb['mb_deposit_point']+$mb['mb_deposit_calc'])?></strong> <?=ASSETS_CURENCY?> &nbsp&nbsp (총 입금액 : <?=Number_format($mb['mb_deposit_point'])?> <?=ASSETS_CURENCY?>)
		</td>

		<th>수동입금 (<?=ASSETS_CURENCY?>)</th>
		<td>
			<input type="hidden" name="mb_deposit_point_math" id="math_code" value="">
			<input type="button" value="+"  class='math_btn plus'>
			<input type="button" value="-"  class='math_btn minus'>
			<input type="text" name="mb_deposit_point_add" value="" id="field_upstair" class="frm_input " size="15" style="max-width:60%" inputmode=price>
		</td>

	</tr>

	<tr class="ly_up padding-box fund">

		<th scope="row">누적 매출 합계 (PV)</th>
		<td colspan="1">
			<span class='strong soodang'><?=shift_auto($mb['mb_save_point'],BALANCE_CURENCY)?> <?=ASSETS_CURENCY?></span>
			&nbsp&nbsp( PV : <?=$mb['mb_pv']?> )
		</td>

		<th scope="row">총 받은보너스(수당)</th>
		<td colspan="1">
			<input type='hidden' name='mb_balance' value ='<?=$mb['mb_balance']?>'/>
			<span class='strong bonus'><?=shift_auto($mb['mb_balance'],BALANCE_CURENCY)?> <?=ASSETS_CURENCY?></span> 
			&nbsp&nbsp( per : <?=percent_express($bonus_per)?> )
		</td>

	</tr>

	<!-- <tr class="ly_up padding-box fund">
		<th scope="row" style='line-height:20px;'>보유마이닝해쉬<br>(추천산하)(후원산하)</th>
		<td colspan="1">
			<span class='strong mining'><?=Number_format($mb['mb_rate'])?> <?=$mining_hash[0]?></span>
			&nbsp&nbsp(<?=$mb['recom_mining']?> <?=$mining_hash[0]?> / <?=$mb['brecom_mining']?> <?=$mining_hash[0]?>)
		</td>

		<th scope="row">총 받은마이닝보너스</th>
		<td colspan="1"><span class='strong mining_soodang'><?=shift_auto($mb[$mining_target],$minings[0])?> <?=strtoupper($minings[0])?></span> </td>
	</tr> -->

	<tr class="ly_up padding-box fund">
		<th scope="row">출금총액</th>
		<td colspan="1"><span class='strong amt'><?=shift_auto($mb['mb_shift_amt'])?> <?=ASSETS_CURENCY?> </span>  
		<!-- &nbsp&nbsp( <?=shift_auto(($mb['mb_shift_amt']),WITHDRAW_CURENCY)?> <?=WITHDRAW_CURENCY?> ) -->
	</td>

		<!-- <th scope="row">마이닝출금액</th>
		<td colspan="1"><span class='strong amt'><?=shift_auto($mb[$mining_amt_target],$minings[0])?> <?=strtoupper($minings[0])?></span></td> -->
	</tr>

	<tr class="ly_up padding-box">
		<th scope="row">패키지 보유</th>

		<td colspan="3">
			최고보유 패키지 :
			<!-- <span class='badge t_white color<?= max_item_level_array($mb['mb_id'], 'number') ?>' style='padding:15px;'><?= max_item_level_array($mb['mb_id']) ?></span> -->
			<span class='badge t_white <?=rank_return($mb['rank'],'color')?>' style='padding:15px;'><?=rank_return($mb['rank'])?></span>
			<span class='divide' style="margin-right:20px;">|</span>
			재기부 설정 : <input type="text" id='recharge' class='frm_input' style="width:40px;min-width:40px;" value="<?=$extra_recharge?>">
			<span class='divide' style="margin:0 20px;">|</span>
			<?php
			
			function rank_return($val,$color=''){
				global $mb;

				if($mb['rank_note'] == ''){
					if($color ==''){
						return '-';
					}else{
						return '';
					}
				}else{
					if($color ==''){
						return 'S'.$val;
					}else{
						return 'color'.$val;
					}
					
				}
			}

			/**/
			$shop_item = get_shop_item(null,1);
			$shop_item_cnt = count($shop_item);
			$pack_array = package_have_return($mb['mb_id'],0);
			
			for ($i = 0; $i < count($pack_array); $i++) {?>
				<button type='button' class='btn purchase_btn' value='' data-row='<?=json_encode($shop_item[$i],JSON_FORCE_OBJECT)?>' data-schedule="1">
					<span class='pack_title color<?=$i?>'><?= $shop_item[$i]['it_name'] ?>-1</span>
					<div class='pack_have'><span><?= $pack_array[$i] ?>
				</button>
			<?}?>
			<span class='divide' style="margin:0 20px;">|</span>
			<?$pack_array2 = package_have_return($mb['mb_id'],1);
			
			for ($i = 0; $i < count($pack_array2); $i++) {?>
				<button type='button' class='btn purchase_btn' value='' data-row='<?=json_encode($shop_item[$i],JSON_FORCE_OBJECT)?>' data-schedule="2">
					<span class='pack_title color<?=$i?>'><?= $shop_item[$i]['it_name'] ?>-2</span>
					<div class='pack_have'><span><?= $pack_array2[$i] ?>
				</button>
			<?}?>
		</td>

	</tr>

	
	</tr>

	<script>
		$(function(){

		//수동입금 입금-차감
		$('.math_btn').click(function(){
			var value = $(this).val();
			$('.math_btn').removeClass('active');
			$(this).addClass('active');
			$('#math_code').val(value);
		});

		var total_fund = '<?=$mb['mb_deposit_point']+$mb['mb_deposit_calc']?>';
		var mb_grade = '<?=$mb['grade']?>';
		var fil_price = Number(<?=$fil_price?>);

		//패키지구매처리
		$('.purchase_btn').on('click',function(){

			var func ='admin';
			var item = $(this).data('row');
			var mb_item_rank = '<?=$mb['rank_note']?>';
			var item_num = item.it_maker.substr(1,1);

			var pre_schedule = $(this).data("schedule");
			var pre_recharge = $("#recharge").val();

			
			console.log(`fil_price:${Number(fil_price)}\nit_cust_price:${Number(item.it_cust_price)}`);
			console.log(`total:${total_fund}\nprice:${item.it_price}`);

			/* var extra_price = Number(item.it_extra*fil_price);
			var it_price = Number(item.it_price + extra_price);
			var coin_price = ((it_price/fil_price)*10000)/10000;
			*/
			
			/*멤버쉽 구매조건 있는경우*/
			/* if(mb_item_rank == '' && item_num > 0){
				alert("MEMBERSHIP 팩을 보유하지 않았습니다.");
				return false;
			} */

			if (confirm("해당 회원에게 \n"+item.it_name+" : "+item.it_option_subject+" ( "+ pre_schedule +" 지급스케쥴 ) 패키지를 지급하시겠습니까?\n회원 잔고에서 "+Price(item.it_price)+" 원이 차감됩니다.")) {
			} else {
				return false;
			}
			
			if(Number(total_fund) < Number(item.it_price) ){
				alert("회원 잔고가 부족합니다.\n잔고지급후 사용해주세요.");
				return false;
			}

			$.ajax({
				type: "POST",
				url: "/util/upstairs_proc.php",
				cache: false,
				async: false,
				dataType: "json",
				data:  {
					"mb_id" : '<?=$mb['mb_id']?>',
					"mb_no" : '<?=$mb['mb_no']?>',
					"rank" : '<?=$mb['rank']?>',
					"func" : func,
					"input_val" : item.it_cust_price,
					"output_val" : item.it_price,
					"select_pack_name" : item.it_name,
					"select_pack_id" : item.it_id,
					"select_maker" : item.it_maker,
					"it_point" : item.it_point,
					"it_supply_point" : item.it_supply_point,
					"recharge" : pre_recharge,
					"schedule" : pre_schedule
				},
				success: function(data) {
				
					if(data.code == 0000){
						alert('구매처리되었습니다.');
						location.reload();
						
					}else{
						alert('처리되지않았습니다.'); 
						location.reload();
						
					}
				},
				error:function(e){
					alert('처리에러'); 
				}
			});
		});
	});
	</script>
	<tr class='divide-bottom'>
		<th scope="row">출금계좌정보</th>			
		<td colspan="3"> 
		은행 :<input type="text" name="bank_name" value="<?php echo $mb['bank_name'] ?>" id="bank_name" class="frm_input " size="15" style="";>
		&nbsp 계좌번호 : &nbsp<input type="text" name="bank_account" value="<?php echo str_replace('-', '', $mb['bank_account']); ?>" id="bank_account" class="frm_input " size="15" style="width:300px;";>
		&nbsp 예금주 : &nbsp<input type="text" name="account_name" value="<?php echo $mb['account_name'] ?>" id="account_name" class="frm_input " size="15" style="";>
		</td>
	</tr>
	
	<!-- <tr>
		<th scope="row">본인확인방법</th>
		<td >
			<input type="radio" name="mb_certify_case" value="ipin" id="mb_certify_ipin" <?php if($mb['mb_certify'] == 'ipin') echo 'checked="checked"'; ?>>
			<label for="mb_certify_ipin">아이핀</label>
			<input type="radio" name="mb_certify_case" value="hp" id="mb_certify_hp" <?php if($mb['mb_certify'] == 'hp') echo 'checked="checked"'; ?>>
			<label for="mb_certify_hp">휴대폰</label>
		</td>

	</tr>
 -->
	<tr class="hidden">
		<th scope="row">본인확인</th>
		<td>
			<input type="radio" name="mb_certify" value="1" id="mb_certify_yes" <?php echo $mb_certify_yes; ?>>
			<label for="mb_certify_yes">예</label>
			<input type="radio" name="mb_certify" value="" id="mb_certify_no" <?php echo $mb_certify_no; ?>>
			<label for="mb_certify_no">아니오</label>
		</td>
		<th scope="row"><label for="mb_adult">성인인증</label></th>
		<td>
			<input type="radio" name="mb_adult" value="1" id="mb_adult_yes" <?php echo $mb_adult_yes; ?>>
			<label for="mb_adult_yes">예</label>
			<input type="radio" name="mb_adult" value="0" id="mb_adult_no" <?php echo $mb_adult_no; ?>>
			<label for="mb_adult_no">아니오</label>
		</td>
	</tr>
		<script>
		function copyAddress(param){
			commonModal("Address copy",'Your Wallet address is copied!',100);
			var $temp = $("<input>");
			$("body").append($temp);

			$temp.val($('#'+param).text()).select();
				document.execCommand("copy");
			$temp.remove();
		}
	</script>
	<!--
	<tr >
		<th scope="row">지갑주소</th>
		<td>
			<div class="wallet_addr">
				<span class="btc_color badge">BTC ADDRESS</span>
				<span id="btc_addr"><?=$mb['mb_wallet']?></span>
				<button type="button" class="copybutton" onclick="copyAddress('#btc_addr')">copyAdress</button>
			</div>
		</td>
		<td>

		</td>
		<td>

		</td>
	</tr>
	-->


	<!-- <tr class="hidden">
		<th scope="row">주소</th>
		<td colspan="3" class="td_addr_line">
			<label for="mb_zip" class="sound_only">우편번호</label>
			<input type="text" name="mb_zip" value="<?php echo $mb['mb_zip1'].$mb['mb_zip2']; ?>" id="mb_zip" class="frm_input readonly" size="5" maxlength="6">
			<button type="button" class="btn_frmline" onclick="win_zip('fmember', 'mb_zip', 'mb_addr1', 'mb_addr2', 'mb_addr3', 'mb_addr_jibeon');">주소 검색</button><br>
			<input type="text" name="mb_addr1" value="<?php echo $mb['mb_addr1'] ?>" id="mb_addr1" class="frm_input readonly" size="60">
			<label for="mb_addr1">기본주소</label><br>
			<input type="text" name="mb_addr2" value="<?php echo $mb['mb_addr2'] ?>" id="mb_addr2" class="frm_input" size="60">
			<label for="mb_addr2">상세주소</label>
			<br>
			<input type="text" name="mb_addr3" value="<?php echo $mb['mb_addr3'] ?>" id="mb_addr3" class="frm_input" size="60">
			<label for="mb_addr3">참고항목</label>
			<input type="hidden" name="mb_addr_jibeon" value="<?php echo $mb['mb_addr_jibeon']; ?>"><br>
		</td>
	</tr> -->

	<tr class="hidden">
		<th scope="row"><label for="mb_icon">회원아이콘</label></th>
		<td colspan="3">
			<?php echo help('이미지 크기는 <strong>넓이 '.$config['cf_member_icon_width'].'픽셀 높이 '.$config['cf_member_icon_height'].'픽셀</strong>로 해주세요.') ?>
			<input type="file" name="mb_icon" id="mb_icon">
			<?php
			$mb_dir = substr($mb['mb_id'],0,2);
			$icon_file = G5_DATA_PATH.'/member/'.$mb_dir.'/'.$mb['mb_id'].'.gif';
			if (file_exists($icon_file)) {
				$icon_url = G5_DATA_URL.'/member/'.$mb_dir.'/'.$mb['mb_id'].'.gif';
				echo '<img src="'.$icon_url.'" alt="">';
				echo '<input type="checkbox" id="del_mb_icon" name="del_mb_icon" value="1">삭제';
			}
			?>
		</td>
	</tr>
	<tr class="hidden">
		<th scope="row">메일 수신</th>
		<td>
			<input type="radio" name="mb_mailling" value="1" id="mb_mailling_yes" <?php echo $mb_mailling_yes; ?>>
			<label for="mb_mailling_yes">예</label>
			<input type="radio" name="mb_mailling" value="0" id="mb_mailling_no" <?php echo $mb_mailling_no; ?>>
			<label for="mb_mailling_no">아니오</label>
		</td>
		<th scope="row"><label for="mb_sms_yes">SMS 수신</label></th>
		<td>
			<input type="radio" name="mb_sms" value="1" id="mb_sms_yes" <?php echo $mb_sms_yes; ?>>
			<label for="mb_sms_yes">예</label>
			<input type="radio" name="mb_sms" value="0" id="mb_sms_no" <?php echo $mb_sms_no; ?>>
			<label for="mb_sms_no">아니오</label>
		</td>
	</tr>
	<tr class="hidden">
		<th scope="row"><label for="mb_open">정보 공개</label></th>
		<td colspan="3">
			<input type="radio" name="mb_open" value="1" id="mb_open_yes" <?php echo $mb_open_yes; ?>>
			<label for="mb_open_yes">예</label>
			<input type="radio" name="mb_open" value="0" id="mb_open_no" <?php echo $mb_open_no; ?>>
			<label for="mb_open_no">아니오</label>
		</td>
	</tr>
	<tr class="hidden">
		<th scope="row"><label for="mb_signature">서명</label></th>
		<td colspan="3"><textarea  name="mb_signature" id="mb_signature"><?php echo $mb['mb_signature'] ?></textarea></td>
	</tr>
	<tr class="hidden">
		<th scope="row"><label for="mb_profile">자기 소개</label></th>
		<td colspan="3"><textarea name="mb_profile" id="mb_profile"><?php echo $mb['mb_profile'] ?></textarea></td>
	</tr>


	<?php if ($w == 'u') { ?>
	<tr>
		<th scope="row">회원가입일</th>
		<td><?php echo $mb['mb_datetime'] ?></td>
		<th scope="row">최근접속일</th>
		<td><?php echo $mb['mb_today_login'] ?></td>
	</tr>
	<tr  class="hidden">
		<th scope="row">IP</th>
		<td ><?php echo $mb['mb_ip'] ?></td>

		<th scope="row">지급차단</th>
		<td>
			<label for="mb_block_yes">
				<input type="radio" name="mb_block" value="1" id="mb_block_yes" <?php echo $mb_block_yes; ?> >예
			</label>
			<label for="mb_block_no">
				<input type="radio" name="mb_block" value="0" id="mb_block_no" <?php echo $mb_block_no; ?> >아니오
			</label>
	</tr>
	<?php if ($config['cf_use_email_certify']) { ?>
	<tr>
		<th scope="row">인증일시</th>
		<td colspan="3">
			<?php if ($mb['mb_email_certify'] == '0000-00-00 00:00:00') { ?>
			<?php echo help('회원님이 메일을 수신할 수 없는 경우 등에 직접 인증처리를 하실 수 있습니다.') ?>
			<input type="checkbox" name="passive_certify" id="passive_certify">
			<label for="passive_certify">수동인증</label>
			<?php } else { ?>
			<?php echo $mb['mb_email_certify'] ?>
			<?php } ?>
		</td>
	</tr>
	<?php } ?>
	<?php } ?>



	<tr>
		<th scope="row"><label for="mb_leave_date">탈퇴일자</label></th>
		<td>
			<input type="text" name="mb_leave_date" value="<?php echo $mb['mb_leave_date'] ?>" id="mb_leave_date" class="frm_input" maxlength="8">
			<input type="checkbox" value="<?php echo date("Ymd"); ?>" id="mb_leave_date_set_today" onclick="if (this.form.mb_leave_date.value==this.form.mb_leave_date.defaultValue) {
this.form.mb_leave_date.value=this.value; } else { this.form.mb_leave_date.value=this.form.mb_leave_date.defaultValue; }">
			<label for="mb_leave_date_set_today">탈퇴일을 오늘로 지정</label>
		</td>
		<th scope="row">접근차단일자</th>
		<td>
			<input type="text" name="mb_intercept_date" value="<?php echo $mb['mb_intercept_date'] ?>" id="mb_intercept_date" class="frm_input" maxlength="8">
			<input type="checkbox" value="<?php echo date("Ymd"); ?>" id="mb_intercept_date_set_today" onclick="if
(this.form.mb_intercept_date.value==this.form.mb_intercept_date.defaultValue) { this.form.mb_intercept_date.value=this.value; } else {
this.form.mb_intercept_date.value=this.form.mb_intercept_date.defaultValue; }">
			<label for="mb_intercept_date_set_today">접근차단일을 오늘로 지정</label>
		</td>
	</tr>

	<tr>
		<th scope="row"><label for="mb_memo">메모</label></th>
		<td colspan="3"><textarea name="mb_memo" id="mb_memo" style="height:30px;" ><?php echo $mb['mb_memo'] ?></textarea></td>
	</tr>

	<?php for ($i=1; $i<=10; $i++) { ?>
	<tr class="hidden">
		<th scope="row"><label for="mb_<?php echo $i ?>">여분 필드 <?php echo $i ?></label></th>
		<td colspan="3"><input type="text" name="mb_<?php echo $i ?>" value="<?php echo $mb['mb_'.$i] ?>" id="mb_<?php echo $i ?>" class="frm_input " size="30" maxlength="255"></td>
	</tr>
	<?php } ?>

	</tbody>
	</table>
</div>

<div class="search_container">
	<div class="search_result" id="search_result" style='overflow:scroll'>
	<ul class="header">
		<li >
			<span class="level">직급</span>
			<span class="id">아이디</span>
			<span class="name">이름</span>
			<span class="nick">닉네임(검색)</span>
		</li>
	</ul>
	</div>
	<div class="result_btn">Close</div>
</div>

<div class="btn_confirm01 btn_confirm">
	<input type="submit" value="확인" class="btn_submit" accesskey='s'>
	<a href="./member_list.php?<?php echo $qstr ?>">목록</a>
</div>
</form>

<script>

function fmember_submit(f)
{
	/*## ##################################*/
	var $rcm_id = $('#mb_recommend').val();
	var $mbs_id = $('#mb_id').val();
	var $break = "ok";

	if ($rcm_id == $mbs_id) {
		alert("회원아이디와 추천인 아이디가 같을 수 없습니다.");
		$('#mb_recommend').focus();
		return false;
	} else {
		$.ajax({
			type: "POST",
			url: "<?=G5_SHOP_URL?>/ajax.id.php",
			data: {
				"rcm_id":$rcm_id,
				"mbs_id":$mbs_id
			},
			cache: false,
			async: false,
				error : function (request, status, error) { // error
							alert("code : " + request.status + "\r\nmessage : " + request.responseText);
						},
				success: function(data) {
					if (data == "break") {
						$break = "break";
					}
			}
		});
	}
	
	if ($('#center_use').is(":checked")) {
		$('#center_use').val('1');
	} else {
		$('#center_use').val('0');
		$('#mb_nick_field').val('');
	}


	if ($break == "break") {
		alert("추천인 아이디를 다시 한번 확인해주세요!");
		$('#mb_recommend').focus();
		return false;
	}
	/*## ##################################*/

	if (!f.mb_icon.value.match(/\.gif$/i) && f.mb_icon.value) {
		alert('아이콘은 gif 파일만 가능합니다.');
		return false;
	}

	if(f.mb_deposit_point_add.value != ''){
		var origin_deposit_point = <?=$mb['mb_deposit_point']?>;

		// console.log( f.mb_deposit_point_math.value );
		
		if($('#math_code').val() == ''){
			$('.math_btn.plus').focus();
			alert('수동입금 기호를 선택해주세요');
			
			return false;
		}

		if(origin_deposit_point == 0){
			alert("최초입금처리시에는 바로처리되지 않으며,\n입금 요청 내역에서 승인처리하여야 정상입금처리됩니다. ");
		}
	}

	// return true;
}


$(function() {
		onlyNumber('bank_account');

		// 주소 찾기
		const mb_addr1 = document.getElementById("mb_addr1");

		mb_addr1.addEventListener('click', () => {
			new daum.Postcode({
				oncomplete: function(data) {
					// address : 기본주소, roadAddress : 도로명 주소, jibunAddress : 지번 주소
					mb_addr1.value = data.address;
				}
			}).open();
		});

		$('#center_use').click(function(){
			var checked = $(this).is(":checked");

			if(checked){
				$('#mb_nick_regist').addClass('active');
			}else{
				$('#mb_nick_regist').removeClass('active');
			}
		});
		
		
		$('#field_upstair').on('change', function() {
			var after = $(this).val().replace(/,/g,'');
			var before ="<?=$mb['mb_deposit_point']?>";
			console.log(after +'/'+ before);

			// var calc = (after - conv_number(before));
			// $('#be_to').val(Price(calc));
		});

		$(".member_auto_regist").on('click',function(){
			var mb_id = "<?=$mb['mb_id']?>";
			var category = $(this).data("category");
			var category_value = $(this).data("category_value");
			var target = $("#mb_"+category);

			$.ajax({
				type: "POST",
				url: "/util/ajax.member_auto_org.php",
				dataType: 'json',
				async: false,
				data: {
					"mb_id" :mb_id ,
					"category" : category,
					"category_value" : category_value
				},
				success: function(result) {
					if(result.code == 0000){
						console.log(result.data);
						target.val(result.data);
					}else{
						alert("해당되는 등급 회원이 없거나 찾을수없습니다.");
					}
				},
				error: function(e) {
					alert(e);
				}
			});
		});


		$(".member_search").on('click',function(){
			var mb_id = "<?=$mb['mb_id']?>";
			var category = $(this).data("category");
			var target = $("#mb_"+category);
			var search_member = target.val();
			
			var divX = $(this).offset().left;
			var divY = $(this).offset().top;

			console.log(divX);
			console.log(divY);

			$.ajax({
				type: "POST",
				url: "/util/ajax.search_member.php",
				dataType: 'json',
				async: false,
				data: {
					"search_member":search_member,
					"mb_id": mb_id
				},
				success: function(result) {
					if(result.code == 0000){
						$('.search_container').addClass("active");
						$('.search_container').css('left',(divX - 170)).css('top',(divY - 500));

							var cnt = result.data.length;
							var html = "<ul class='content'>";

							for (var i = 0; i < cnt; i++) {
								html += "<li data-id='"+result.data[i].mb_id+"'>";
								html += "<span class='level'>"+result.data[i].mb_level+"</span>";
								html += "<span class='id'>"+result.data[i].mb_id+"</span>";
								html += "<span class='name'>"+result.data[i].mb_name+"</span>";
								html += "<span class='nick'>"+result.data[i].mb_nick+"</span>";
								html += "</li>";
							}
							html += "</ul>";

						$('.dim').css("display","block");
						$("#search_result").append(html);

						$(".result_btn").on('click',function(){
							$('.search_container').removeClass("active");
							$("#search_result .content").remove();
						});

						$("#search_result li").on('click',function(){
							target.val($(this).data('id'));
							$('.search_container').removeClass("active");
							$("#search_result .content").remove();
						});

					}else{
						alert("해당되는 회원이 없거나 찾을수없습니다.");
					}
				},
				error: function(e) {
					// alert(e);
				}
			});

		});

		function copyAddress(param){
			//commonModal("Address copy",'Your Wallet address is copied!',100);

			console.log($(param).text());
			var $temp = $("<input>");
				$("body").append($temp);
			$temp.val($(param).text()).select();
				document.execCommand("copy");
			$temp.remove();

			alert('주소가 복사되었습니다.');
		}
	});
</script>

<?php
include_once('./admin.tail.php');
?>
