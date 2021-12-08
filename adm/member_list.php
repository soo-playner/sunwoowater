<?php
$sub_menu = "200100";
include_once('./_common.php');
include_once(G5_THEME_PATH.'/_include/wallet.php');
include_once(G5_PATH.'/util/package.php');

auth_check($auth[$sub_menu], 'r');

$g5['title'] = '회원관리';
include_once('./admin.head.php');

$get_shop_item = get_shop_item();

$sub_sql = "";
if($_GET['sst'] == "eth"){
	$sub_sql = " , (mb_eth_point+mb_eth_calc) as eth";
}

if($_GET['sst'] == "total_fund"){
	$sub_sql = " , (mb_deposit_point + mb_deposit_calc + mb_balance) as total_fund";
}

if($_GET['sst'] == "deposit_point"){
	$sub_sql = " , (mb_deposit_point) as deposit_point";
}

if($_GET['sst'] == "deposit_calc"){
	$sub_sql = " , (mb_deposit_calc) as deposit_calc";
}

$sql_common = " {$sub_sql} from {$g5['member_table']} ";

$sql_search = " where (1) ";

if ($stx) {
	$sql_search .= " and ( ";
	switch ($sfl) {
		case 'mb_point' :
			$sql_search .= " ({$sfl} >= '{$stx}') ";
			break;
		case 'mb_level' :
			$sql_search .= " ({$sfl} = '{$stx}') ";
			break;
		case 'mb_tel' :
		case 'mb_hp' :
			$sql_search .= " ({$sfl} like '%{$stx}') ";
			break;

		default :
			$sql_search .= " ({$sfl} like '{$stx}%') ";
			break;
	}
	$sql_search .= " ) ";
}

if($_GET['level']){
	$sql_search .= " and mb_level = ".$_GET['level'];
}

if($member['mb_id'] != 'admin'){
	$sql_search .= " and mb_id  != 'admin' ";
}

if($_GET['nation']){
	$sql_search .= " and nation_number = ".$_GET['nation'];
}

if($_GET['block']){
	$sql_search .= " and mb_block = 1 ";
}

if (!$sst) {
	$sst = "mb_datetime, mb_no";
	$sod = "desc";
}

if($_GET['grade'] && $_GET['grade'] != ''){
	$sql_search .= " and grade = ".$_GET['grade'];
}

$sql_order = " order by {$sst} {$sod}";
$sql = " select count(*) as cnt {$sql_common} {$sql_search} {$sql_order} ";

$row = sql_fetch($sql);
$total_count = $row['cnt'];

$rows = $config['cf_page_rows'];
$rows =100;

$total_page  = ceil($total_count / $rows);  // 전체 페이지 계산
if ($page < 1) $page = 1; // 페이지가 없으면 첫 페이지 (1 페이지)
$from_record = ($page - 1) * $rows; // 시작 열을 구함


// 탈퇴회원수
$sql = " select count(*) as cnt {$sql_common} {$sql_search} and mb_leave_date <> '' {$sql_order} ";
$row = sql_fetch($sql);
$leave_count = $row['cnt'];


// 차단회원수
$sql = " select count(*) as cnt {$sql_common} {$sql_search} and mb_intercept_date <> '' {$sql_order} ";
$row = sql_fetch($sql);
$intercept_count = $row['cnt'];

$listall = '<a href="'.$_SERVER['SCRIPT_NAME'].'" class="ov_listall">전체목록</a>';

$sql = " select * {$sql_common} {$sql_search} {$sql_order} limit {$from_record}, {$rows} ";
//echo $sql; exit;
$result = sql_query($sql);
$colspan = 11;

/* 레벨 */
$grade = "SELECT grade, count( grade ) as cnt FROM g5_member GROUP BY grade order by grade";
$get_lc = sql_query($grade);



/* 차단 */
$blockRec = sql_fetch("select count(mb_block) as cnt from g5_member where mb_block = 1");



function active_check($val, $target){
    $bool_check = $_GET[$target];
    if($bool_check == $val){
        return " active ";
    }
}

function out_check($val){
	$bonus_OUT_CALC = $val;

	if($bonus_OUT_CALC > 100){
		$class = 'over';
	}else{
		$class = '';
	}
	return "<span class=".$class.">".number_format($bonus_OUT_CALC)." % </span>";
}



// 통계수치
$stats_sql = "SELECT COUNT(*) as cnt, SUM(mb_deposit_point) AS deposit, SUM(mb_balance) AS balance, SUM(mb_deposit_point+mb_deposit_calc) AS fund {$sql_common} {$sql_search}";
$stats_result = sql_fetch($stats_sql);
?>

<style>
.local_ov strong{color:red; font-weight:600;}
.local_ov .tit{color:black; font-weight:600;}
.local_ov a{margin-left:20px;}

#member_depth{background:lightskyblue}
	#member_depth:hover{background:black; color:white;}
	.mem_icon{width:20px;height:20px;margin-right:5px;}
	.area{display:inline-block;margin-right:20px;vertical-align: middle}
	.area span{cursor:pointer}
	.area span:hover{text-decoration: underline}
	.area.nation{padding-right:20px;}

	.nation_item{
		display: inline-block;
		padding: 5px 10px;
		border: 1px solid #c8ced1;
		background: #d6dde1;
		text-decoration: none;}
	.nation_item:hover{background:#3e4452;color:white;}
	.nation_item.active{background:#f9a62e;border:1px solid #f9a62e; color:black;}
	.nation_icon{vertical-align:bottom;margin-right:3px;}

	.total{background:#555 !important;color:white !important;}
	.bonus_total{background:teal !important;color:white !important;}
	.bonus_usdt{background:crimson !important;color:white !important;}
	.bonus_usdt a{color:white !important;font-weight:300}
	.bonus_eth{background:#0062cc !important;color:white !important;}

	.bonus_aa{background:yellowgreen !important}
	.bonus_bb{background:skyblue !important}
	.bonus_bb.bonus_out{background:deepskyblue !important}
	.bonus_bb.bonus_benefit{background:gold !important}
	.bonus_calc{background:#3e1f9c !important; }
	.bonus_calc a{color:white !important;font-weight:400}
	.td_mbgrade select{min-width:50px;padding:5px 5px }

	.tbl_head02 tbody td{padding:5px;}
	.over{color:red;}
	.td_mngsmall a {border:1px solid #ccc; padding:3px 10px; display:inline-block;text-decoration:none;}
	.td_mngsmall a:hover{background:black;border:1px solid black; color:white;}
	.labelM{text-align:left;}

	.red{color:red;font-weight:600;}
	.btn_add01{padding-bottom:10px;border-bottom:1px solid #bbb}
	.center {text-align:center !important;}
	
	.td_mail{font-size:11px;letter-spacing:-0.5px;}
	.bonus_eth a {color:white !important}

	.icon,.icon img{width:26px;height:26px;}

	.badge.over{    position: absolute;
    padding: 2px 5px;
    background: #eee;
    font-size: 12px;
	margin-left:-10px;
	margin-top:12px;
    display: inline-block;
    font-weight: 600;
    color: black;}

	.td_mbstat{text-align:right;padding-right:10px !important;font-size:12px;}
	.icon{display:inline-block;vertical-align: bottom;}
	.icon i{vertical-align: -webkit-baseline-middle;}
</style>

<div class="local_ov01 local_ov">
	<?php echo $listall ?>
	총회원수 <strong><?php echo number_format($total_count) ?></strong>명|
	<?
		if($member['mb_id'] == 'admin'){
			echo "<span style='padding-left:10px;'>회원 총 자산 합계 <strong>".Number_format($stats_result['fund'])."</strong></span> | ";
			echo "<span style='padding-left:10px;'>회원 총 입금 합계 <strong>".Number_format($stats_result['deposit'])."</strong></span> | ";
			echo "<span style='padding-left:10px;'>회원 총 수당 합계 <strong>".Number_format($stats_result['balance'])."</strong></span> | ";
		}
	?>
	
	<!-- <a href="?sst=mb_intercept_date&amp;sod=desc&amp;sfl=<?php echo $sfl ?>&amp;stx=<?php echo $stx ?>">
	차단 <?php echo number_format($intercept_count) ?></a>명,
	<a href="?sst=mb_leave_date&amp;sod=desc&amp;sfl=<?php echo $sfl ?>&amp;stx=<?php echo $stx ?>">탈퇴 <?php echo number_format($leave_count) ?></a>명,
	<a href="?block=1">
		지급차단 <?php echo number_format($blockRec['cnt']) ?>명
	</a> -->
</div>

<form id="fsearch" name="fsearch" class="local_sch01 local_sch" method="get">
	<label for="sfl" class="sound_only">검색대상</label>
	<select name="sfl" id="sfl">
		<option value="mb_id"<?php echo get_selected($_GET['sfl'], "mb_id"); ?>>회원아이디</option>
		<option value="mb_nick"<?php echo get_selected($_GET['sfl'], "mb_nick"); ?>>닉네임</option>
		<option value="mb_name"<?php echo get_selected($_GET['sfl'], "mb_name"); ?>>이름</option>
		<!-- <option value="mb_level"<?php echo get_selected($_GET['sfl'], "mb_level"); ?>>권한</option>
		<option value="mb_email"<?php echo get_selected($_GET['sfl'], "mb_email"); ?>>E-MAIL</option> -->
		<option value="mb_tel"<?php echo get_selected($_GET['sfl'], "mb_tel"); ?>>전화번호</option>
		<option value="mb_hp"<?php echo get_selected($_GET['sfl'], "mb_hp"); ?>>휴대폰번호</option>
		<option value="mb_point"<?php echo get_selected($_GET['sfl'], "mb_point"); ?>>PV</option>
		<option value="mb_datetime"<?php echo get_selected($_GET['sfl'], "mb_datetime"); ?>>가입일시</option>
		<option value="mb_ip"<?php echo get_selected($_GET['sfl'], "mb_ip"); ?>>IP</option>
		<option value="mb_recommend"<?php echo get_selected($_GET['sfl'], "mb_recommend"); ?>>추천인</option>
		<option value="mb_wallet"<?php echo get_selected($_GET['sfl'], "mb_wallet"); ?>>지갑</option>
	</select>

	<label for="stx" class="sound_only">검색어<strong class="sound_only"> 필수</strong></label>
	<input type="text" name="stx" value="<?php echo $stx ?>" id="stx" required class="required frm_input">
	<input type="submit" class="btn_submit" value="검색">
</form>


<div style="padding:8px 20px 10px;font-size:15px;margin-bottom:10px;float:left">
<link rel="stylesheet" href="<?=G5_THEME_URL?>/css/scss/custom.css">
<link rel="stylesheet" href="./css/scss/admin_custom.css">

<form name="search_bar" id="search_bar" action="./member_list.php" method="get">
	
	<input type='hidden' name ='nation' id='nation' value=''/>
	<input type='hidden' name ='level' id='level' value=''/>
	
	<div class="area nation">
		<?
		if(NATION_USE){
			
			/* 국가 */
			$nation_sql = "SELECT nation_number, count( nation_number ) as cnt FROM g5_member GROUP BY nation_number ORDER BY FIELD(nation_number, 82) DESC";
			$nation_row = sql_query($nation_sql);

			while( $row = sql_fetch_array($nation_row)){?>
				<span onclick="nation_search('<?=$row['nation_number']?>');" class="nation_item <?=active_check($row['nation_number'], 'nation')?> ">
				<img src='<?=national_flag($row['nation_number'])?>' class='nation_icon'/><?=$row['cnt']?></span> 
			<?}
		}
		?>
	</div>

</form>
</div>


<!--member_list_excel.php로 넘길 회원엑셀다운로드 데이터-->
<form name="myForm" action="../excel/member_list_excel.php" method="post">
<input type="hidden" name="sql_common" value="<?php echo $sql_common ?>" />
<input type="hidden" name="sql_search" value="<?php echo $sql_search ?>" />
<input type="hidden" name="sql_order" value="<?php echo $sql_order ?>" />
<input type="hidden" name="from_record" value="<?php echo $from_record ?>" />
<input type="hidden" name="rows" value="<?php echo $rows ?>" />
</form>


<?php if ($is_admin == 'super') { ?>
	<div class="btn_add01 btn_add">
		<a href="./member_table_fixtest.php">추천관계검사</a>
		<a href="./member_table_depth.php" id="member_depth">회원추천관계갱신</a>
		<!-- <a href="./member_table_sponsor.php" style='background:antiquewhite'>추천상위스폰서갱신</a> -->
		<a href="./member_form.php" id="member_add">회원직접추가</a>
		<a href="../excel/excel_with_eth.php">회원엑셀다운로드</a> 
		<!-- <a href="#" onclick="javascript:document.myForm.submit();">회원엑셀다운로드</a>
		<a href="../excel/all_member_list_excel.php">전체회원엑셀다운로드</a>  -->
	</div>
<?php } ?>

</div>

<div class="local_desc01 local_desc">
    <p>
		- <strong>현재잔고</strong> : ( 총입금액 - 사용/구매 금액 )
	</p>
</div>

<form name="fmemberlist" id="fmemberlist" action="./member_list_update.php" onsubmit="return fmemberlist_submit(this);" method="post">
<input type="hidden" name="sst" value="<?php echo $sst ?>">
<input type="hidden" name="sod" value="<?php echo $sod ?>">
<input type="hidden" name="sfl" value="<?php echo $sfl ?>">
<input type="hidden" name="stx" value="<?php echo $stx ?>">
<input type="hidden" name="page" value="<?php echo $page ?>">
<input type="hidden" name="token" value="">

<div class="tbl_head02 tbl_wrap" style="clear:both">
	<table>
	<caption><?php echo $g5['title']; ?> 목록</caption>
	<colgroup>
		<col width="40"/>
		<col width="40" />
		<col width="150" />
		<col width="120" />
		<col width="120" />
		<col width="180" />
		<col width="180" />
		<col width="180" />
		<col width="180" />
		<col width="180" />
		<col width="180" />
		<col width="180" />
		<col width="140" />
		<col width="100" />
		<col width="140" />
		<col width="120" />
		<col width="100" />
	</colgroup>
	<thead>
	<tr>
		<th scope="col" rowspan="2" id="mb_list_chk">
			<label for="chkall" class="sound_only">회원 전체</label>
			<input type="checkbox" name="chkall" value="1" id="chkall" onclick="check_all(this.form)">
		</th>
		<th scope="col" rowspan="2" id="" class="td_chk" style='max-width:30px;width:40px !important;'><?php echo subject_sort_link('grade') ?>회원등급</th>
		<th scope="col" rowspan="2" id="mb_list_id" class="td_name center"><?php echo subject_sort_link('mb_id') ?>아이디</a></th>
		<!--<th scope="col" rowspan="2"  id="mb_list_cert"><?php echo subject_sort_link('mb_certify', '', 'desc') ?>메일인증확인</a></th>-->

		<!-- <th scope="col" rowspan="2" id="mb_list_mobile" class="td_mail">추천상위스폰서</th> -->
		<th scope="col" rowspan="2" id="mb_list_mobile" class="td_mail">추천인</th>
		<th scope="col" rowspan="2" id="mb_list_mobile" class="td_mail">후원인</th>
		
		<th scope="col" id="mb_list_auth2" class="bonus_calc"  rowspan="2"><?php echo subject_sort_link('deposit_point') ?>총입금액 <br></th>
		<th scope="col" id="mb_list_auth2" class="bonus_bb"  rowspan="2"><?php echo subject_sort_link('deposit_calc') ?>사용금액<br>(출금포함)<br></th>
		<th scope="col" id="mb_list_auth"  class="bonus_eth" rowspan="2"><?php echo subject_sort_link('total_fund') ?>현재잔고<br></a></th>
		<th scope="col" id="mb_list_auth2" class="bonus_usdt" style='color:white !important' rowspan="2"><?php echo subject_sort_link('mb_shift_amt') ?>출금총액<br>(+수수료)<br></th>
		
		<th scope="col" id="mb_list_auth2" class="bonus_bb bonus_benefit"  rowspan="2"><?php echo subject_sort_link('mb_balance') ?> 수당합계</th>
		<th scope="col" id="mb_list_auth2" class="bonus_aa"  rowspan="2"><?php echo subject_sort_link('mb_pv') ?> 누적매출(PV)</th>
		<th scope="col" id="mb_list_auth2" class=""  rowspan="2"><?php echo subject_sort_link('mb_rate') ?> 마이닝해쉬(<?=$mining_hash[0]?>)</th>
		<th scope="col" id="mb_list_auth2" class="bonus_bb bonus_out"  rowspan="2">수당/한계<br>(100%)</th>
		
		<th scope="col" rowspan="2" id="" class="item_title">상위보유패키지</th>
		<th scope="col" id="mb_list_authcheck" style='min-width:130px;' rowspan="2">상태/<?php echo subject_sort_link('mb_level', '', 'desc') ?>회원레벨</a></th>
		<th scope="col" id="mb_list_member"><?php echo subject_sort_link('mb_today_login', '', 'desc') ?>최종접속</a></th>
		<th scope="col" rowspan="2" id="mb_list_mng">관리</th>
	</tr>

	<tr>
		<!--<th scope="col" id="mb_list_mailc"><?php echo subject_sort_link('mb_email_certify', '', 'desc') ?>메일<br>인증</a></th>-->
		<th scope="col" id="mb_list_join"><?php echo subject_sort_link('mb_datetime', '', 'desc') ?>가입일</a></th>
	</tr>

	</thead>

	<tbody>
	<?php
	for ($i=0; $row=sql_fetch_array($result); $i++) {

		// 접근가능한 그룹수
		$sql2 = " select count(*) as cnt from {$g5['group_member_table']} where mb_id = '{$row['mb_id']}' ";
		$row2 = sql_fetch($sql2);

		$group = '';
		if ($row2['cnt'])
			$group = '<a href="./boardgroupmember_form.php?mb_id='.$row['mb_id'].'">'.$row2['cnt'].'</a>';

		if ($is_admin == 'group') {
			$s_mod = '';
		} else {
			$s_mod = '<a href="./member_form.php?'.$qstr.'&amp;w=u&amp;mb_id='.$row['mb_id'].'">회원수정</a>';
			// $s_mod_binary = '<a href="./modify_binary.php?'.$qstr.'&amp;w=u&amp;mb_id='.$row['mb_id'].'">바이너리 수정</a>';

		}
		// $s_grp = '<a href="./boardgroupmember_form.php?mb_id='.$row['mb_id'].'">그룹</a>';

		$leave_date = $row['mb_leave_date'] ? $row['mb_leave_date'] : date('Ymd', G5_SERVER_TIME);
		$intercept_date = $row['mb_intercept_date'] ? $row['mb_intercept_date'] : date('Ymd', G5_SERVER_TIME);

		$mb_nick = get_sideview($row['mb_id'], get_text($row['mb_nick']), $row['mb_email'], $row['mb_homepage']);
		$mb_id = $row['mb_id'];
		
		// 현재잔고
		$total_deposit = $row['mb_deposit_point'] + $row['mb_deposit_calc'];
		$total_bonus = $row['mb_balance'];
		$total_fund = $total_deposit;
		
		$bonus_per = bonus_per($row['mb_id'],$row['mb_balance'],$row['mb_pv']);

		$leave_msg = '';
		$intercept_msg = '';
		$intercept_title = '';
		if ($row['mb_leave_date']) {
			$mb_id = $mb_id;
			$leave_msg = '<span class="mb_leave_msg">탈퇴함</span>';
		}
		else if ($row['mb_intercept_date']) {
			$mb_id = $mb_id;
			$intercept_msg = '<span class="mb_intercept_msg">차단됨</span>';
			$intercept_title = '차단해제';
		}
		if ($intercept_title == '')
			$intercept_title = '차단하기';

		$address = $row['mb_zip1'] ? print_address($row['mb_addr1'], $row['mb_addr2'], $row['mb_addr3'], $row['mb_addr_jibeon']) : '';

		$bg = 'bg'.($i%2);

		switch($row['mb_certify']) {
			case 'hp':
				$mb_certify_case = '휴대폰';
				$mb_certify_val = 'hp';
				break;
			case 'ipin':
				$mb_certify_case = '아이핀';
				$mb_certify_val = '';
				break;
			case 'admin':
				$mb_certify_case = '관리자';
				$mb_certify_val = 'admin';
				break;
			default:
				$mb_certify_case = '&nbsp;';
				$mb_certify_val = 'admin';
				break;
		}
	?>


	<tr class="<?php echo $bg; ?>">
		<td headers="mb_list_chk" class="td_chk" rowspan="2">
			<input type="hidden" name="mb_id[<?php echo $i ?>]" value="<?php echo $row['mb_id'] ?>" id="mb_id_<?php echo $i ?>">
			<label for="chk_<?php echo $i; ?>" class="sound_only"><?php echo get_text($row['mb_name']); ?> <?php echo get_text($row['mb_nick']); ?>님</label>
			<input type="checkbox" name="chk[]" value="<?php echo $i ?>" id="chk_<?php echo $i ?>">
		</td>
		<td headers="mb_list_id" rowspan="2" class="td_num" >
			<input type='hidden' name='grade[]' value= "<?=$row['grade']?>" />
			<?echo "<img src='/img/".$row['grade'].".png' class='icon'>";?>
			<div class='badge over'><?=$row['grade']?></div>
		</td>
		<td headers="mb_list_id" rowspan="2" class="td_name sv_use" style="center">
		
		<?php echo $mb_id ?></td>
		<!-- <td rowspan="2" class="center"><?php echo $sponsor ?></td> -->
		<td rowspan="2" class="center"><?php echo $row['mb_recommend'] ?></td>
		<td rowspan="2" class="center"><?php echo $row['mb_brecommend'] ?></td>
		<!--
			<td headers="mb_list_name" class="td_mbname"><?php echo get_text($row['mb_name']); ?></td>
			<td headers="mb_list_name" class="td_mbname"><?php echo get_text($row['first_name']); ?></td>-->
			<!--
			<td headers="mb_list_otp" class="td_chk">
				<label for="otp_flag<?php echo $i; ?>" class="sound_only">OTP인증</label>
				<input type="checkbox" name="otp_flag[<?php echo $i; ?>]" <?php echo $row['otp_flag'] == 'Y' ?'checked':''; ?> value="Y" id="otp_flag<?php echo $i; ?>">
			</td>
			<td headers="mb_list_mailc" class="td_chk"><?php echo preg_match('/[1-9]/', $row['mb_email_certify'])?'<span class="txt_true">Yes</span>':'<span class="txt_false">No</span>'; ?></td>
			<td headers="mb_list_open" class="td_chk">
				<label for="mb_open_<?php echo $i; ?>" class="sound_only">정보공개</label>
				<input type="checkbox" name="mb_open[<?php echo $i; ?>]" <?php echo $row['mb_open']?'checked':''; ?> value="1" id="mb_open_<?php echo $i; ?>">
			</td>
			-->
			<!--
			<td headers="mb_list_mailr" rowspan="2"class="td_chk">
				<label for="mb_mailling_<?php echo $i; ?>" class="sound_only">메일수신</label>
				<input type="checkbox"  name="mb_mailling[<?php echo $i; ?>]" <?php echo $row['mb_mailling']?'checked':''; ?> value="1" id="mb_mailling_<?php echo $i; ?>">
			</td>
			-->
			<!--
			<td headers="mb_list_sms" class="td_chk">
				<label for="mb_sms_<?php echo $i; ?>" class="sound_only">SMS수신</label>
				<input type="checkbox" name="mb_sms[<?php echo $i; ?>]" <?php echo $row['mb_sms']?'checked':''; ?> value="1" id="mb_sms_<?php echo $i; ?>">
			</td>
			<td headers="mb_list_adultc" class="td_chk">
				<label for="mb_adult_<?php echo $i; ?>" class="sound_only">성인인증</label>
				<input type="checkbox" name="mb_adult[<?php echo $i; ?>]" <?php echo $row['mb_adult']?'checked':''; ?> value="1" id="mb_adult_<?php echo $i; ?>">
			</td>
			<td headers="mb_list_deny" class="td_chk">
				<?php if(empty($row['mb_leave_date'])){ ?>
				<input type="checkbox" name="mb_intercept_date[<?php echo $i; ?>]" <?php echo $row['mb_intercept_date']?'checked':''; ?> value="<?php echo $intercept_date ?>" id="mb_intercept_date_<?php echo $i ?>" title="<?php echo $intercept_title ?>">
				<label for="mb_intercept_date_<?php echo $i; ?>" class="sound_only">접근차단</label>
				<?php } ?>
			</td>
		-->
		<!-- <td headers="mb_list_mobile" rowspan="2" class="td_mail"><?php echo get_text($row['mb_email']); ?></td> -->



		<td headers="mb_list_auth" class="td_mbstat" rowspan="2"><strong><?=shift_auto_zero($row['mb_deposit_point'])?> </strong></td>
		<td headers="mb_list_auth" class="td_mbstat" style='color:red' rowspan="2"><?=shift_auto_zero($row['mb_deposit_calc'])?></td>
		<td headers="mb_list_auth" class="td_mbstat" rowspan="2"><strong><?=shift_auto_zero($total_fund)?></strong></td>
		<td headers="mb_list_auth" class="td_mbstat" style='color:red' rowspan="2"><?=shift_auto_zero($row['mb_shift_amt'])?></td>
		<td headers="mb_list_auth" class="td_mbstat" rowspan="2"><strong><?= shift_auto_zero($total_bonus) ?> </strong></td>
		<!-- <td headers="mb_list_auth" class="td_mbstat" rowspan="2"><?= shift_auto_zero($row['mb_save_point']) ?></td> -->
		<td headers="mb_list_auth" class="td_mbstat" rowspan="2"><?= shift_auto_zero($row['mb_pv']) ?></td>
		<td headers="mb_list_auth" class="td_mbstat" rowspan="2"><?= shift_auto_zero($row['mb_rate']) ?></td>
		<td headers="mb_list_auth" class="td_mbstat" rowspan="2"> <?=percent_express($bonus_per)?></td>
		<td headers="mb_list_auth" class="td_mbstat text-center" rowspan="2"><span class='badge t_white color<?=$row['rank']?>'><?if($row['rank']){echo 'P'.$row['rank'];}?></span></td>
		
		<td headers="mb_list_member" class="td_mbgrade" rowspan="2">
			<span class='icon'><?=user_icon($row['mb_id'],'icon')?></span>
			<?php echo get_member_level_select("mb_level[$i]", 0, $member['mb_level'], $row['mb_level']) ?>
		</td>
		<td headers="mb_list_lastcall" class="td_date"><?php echo substr($row['mb_today_login'],2,8); ?></td>
		<!--<td headers="mb_list_grp" rowspan="1" class="td_numsmall"><?php echo $group ?></td>-->
		<td headers="mb_list_mng" rowspan="2" class="td_mngsmall" style="width:100px;"><?php echo $s_mod ?> <?php echo $s_grp ?></br> <?php echo $s_mod_binary ?></td>

	</tr>
	<tr class="<?php echo $bg; ?>">
		<!-- <td headers="mb_list_nick" class="td_name sv_use"><div><?php echo $mb_nick ?></div></td> -->
		<!--<td headers="mb_list_nick" class="td_name sv_use"><div><?php echo get_text($row['last_name']); ?></div></td>-->
			<!--<td headers="mb_list_cert" colspan="6" class="td_mbcert">-->
			<!-- <input type="radio" name="mb_certify[<?php echo $i; ?>]" value="ipin" id="mb_certify_ipin_<?php echo $i; ?>" <?php echo $row['mb_certify']=='ipin'?'checked':''; ?>>
			<label for="mb_certify_ipin_<?php echo $i; ?>">아이핀</label>
			<input type="radio" name="mb_certify[<?php echo $i; ?>]" value="hp" id="mb_certify_hp_<?php echo $i; ?>" <?php echo $row['mb_certify']=='hp'?'checked':''; ?>>
			<label for="mb_certify_hp_<?php echo $i; ?>">휴대폰</label>

			</td>-->


		<!--<td headers="mb_list_tel" class="td_tel"><?php echo get_text($row['mb_tel']); ?></td>
		<td></td>-->
		<!-- // <td headers="mb_list_point" class="td_num"><a href="point_list.php?sfl=mb_id&amp;stx=<?php echo $row['mb_id'] ?>"><?php echo number_format($row['mb_point']) ?></a></td> // -->
		<td headers="mb_list_join" class="td_date"><?php echo substr($row['mb_datetime'],2,8); ?></td>
		<!--
		<td>
			<a href="https://www.blockchain.com/ko/btc/address/<?php echo $row['mb_wallet'] ?>" target="_balnk">
				<?php echo $row['mb_wallet'] ?>
			</a>
		</td>
		-->
	</tr>

	<?php
	}
	if ($i == 0)
		echo "<tr><td colspan=\"".$colspan."\" class=\"empty_table\">자료가 없습니다.</td></tr>";
	?>
	</tbody>
	</table>
</div>

<div class="btn_list01 btn_list">
	<input type="submit" name="act_button" value="선택수정" onclick="document.pressed=this.value">
	<input type="submit" name="act_button" value="선택삭제" onclick="document.pressed=this.value">
</div>

</form>

<?php echo get_paging(G5_IS_MOBILE ? $config['cf_mobile_pages'] : $config['cf_write_pages'], $page, $total_page, '?'.$qstr.'&amp;page='); ?>

<script>
function fmemberlist_submit(f)
{
	if (!is_checked("chk[]")) {
		alert(document.pressed+" 하실 항목을 하나 이상 선택하세요.");
		return false;
	}

	if(document.pressed == "선택삭제") {
		if(!confirm("해당 회원을 정말 삭제하시겠습니까?")) {
			return false;
		}
	}
	return true;
}

function level_search(param){
	$('#search_bar #level').val(param);
	//console.log($('#search_bar #level').val());
	$('#search_bar').submit();
}

function grade_search(param){
	$('#search_bar #grade').val(param);
	//console.log($('#search_bar #level').val());
	$('#search_bar').submit();
}

function nation_search(param){
	$('#search_bar #nation').val(param);
	//console.log($('#search_bar #nation').val());
	$('#search_bar').submit();
}

// 엑셀 다운로드
$('#excel_btn').on("click", function () {

	var s_date = $('#s_date').val();
	var e_date = $('#e_date').val();
	//var idx_num = $('.select-btn').val();
	var idx_num = '';
	var ck_box = true;
	$('.ckbox').each(function(){
		if( $(this).prop('checked') ){
			if( ck_box == true ){
				ck_box = false;
				idx_num += $(this).val();
			}else{
				idx_num += '_'+$(this).val();
			}
		}
	})
	//console.log("/excel/metal.php?s_date="+s_date+"&e_date="+e_date+"&idx_num="+idx_num+"&idx=<?=$idx?>");

	window.open("/excel/metal.php?s_date="+s_date+"&e_date="+e_date+"&idx_num="+idx_num+"&idx=<?=$idx?>");
});


</script>

<?php
include_once ('./admin.tail.php');
?>
