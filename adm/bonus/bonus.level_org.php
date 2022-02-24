<?php
$sub_menu = "600300";
include_once('./_common.php');

$g5['title'] = "조직 관리 및 직급보너스";

include_once(G5_ADMIN_PATH.'/admin.head.php');
include_once(G5_THEME_PATH.'/_include/wallet.php');
include_once('./bonus_inc.php');
include_once(G5_PLUGIN_PATH.'/jquery-ui/datepicker.php');

$bonus_rate_array = bonus_pick('rank_bonus');
$bonus_rates = explode(',', $bonus_rate_array['rate']);


// 누적통계
$mb_lvl = [];
$mb_lvl_result = sql_query("SELECT mb_level,COUNT(mb_id) AS cnt from g5_member WHERE mb_level < 9 GROUP BY mb_level ");
while($row = sql_fetch_array($mb_lvl_result)){
    array_push($mb_lvl, $row);
}

if (empty($fr_date)) $fr_date = date("Y-m-d", strtotime(date("Y-m-d")."-90 day"));
if (empty($to_date)) $to_date = G5_TIME_YMD;


$slayer = $_GET['slayer'];

$sql = "select * from {$g5['bonus_config']} where used = 3  order by no";
$ranklist = sql_query($sql);

//지급차수 여부 
if($slayer > 0){
    $start_dt ='';
    $end_dt ='';
    $sql_search .= " and count = {$slayer} ";
    $qstr .= "&slayer=" . $slayer;
}else{
    if(!$_GET['start_dt']){
        $start_dt = $fr_date;
    }else{
        $start_dt = $_GET['start_dt'];
    }
    
    if(!$_GET['end_dt']){
        $end_dt = $to_date;
    }else{
        $end_dt = $_GET['end_dt'];
    }

    // 검색기간검색
    if ($start_dt) {
        $sql_search .= " and day >= '{$start_dt}' ";
        $qstr .= "&start_dt=" . $start_dt;
    }
    if ($end_dt) {
        $sql_search .= " and day <= '{$end_dt}'";
        $qstr .= "&end_dt=" . $end_dt;
    }
};

// 이름검색
if ($stx) {
    $sql_search .= " and ( ";
    if (($sfl == 'mb_id') || ($sfl == 'mb_id')) {
        $sql_search .= " ({$sfl} = '{$stx}') ";
    } else {
        $sql_search .= " ({$sfl} like '%{$stx}%') ";
    }
    $sql_search .= " ) ";
}

// 조직검색
$target = '';
if ($_GET['center'] != '' ) {
    $target = 'center';
    $keyword = trim($_GET['center']);
    $qstr .= "&center=" . $keyword;
}

if ($_GET['jijum'] != '' ) {
    $target = 'jijum';
    $keyword = trim($_GET['jijum']);
    $qstr .= "&jijum=" . $keyword;
}

if ($_GET['jisa'] != '' ) {
    $target = 'jisa';
    $keyword = trim($_GET['jisa']);
    $qstr .= "&jisa=" . $keyword;
}

if ($_GET['bonbu'] != '' ) {
    $target = 'bonbu';
    $keyword = trim($_GET['bonbu']);
    $qstr .= "&bonbu=" . $keyword;
}

if($target != ''){
    $mb = search_org($keyword,$target);
    $sql_search .= " and ( ";
    $sql_search .= " {$target} = '{$mb}' ";
    $sql_search .= " ) ";
}

function search_org($keyword,$target){
    $keyword = $keyword;
    $mb_taret = sql_fetch("SELECT mb_id FROM g5_member WHERE (mb_id = '{$keyword}' OR mb_nick Like '{$keyword}' )")['mb_id'];
    return $mb_taret;
}

$sql_order = "order by";

if($_GET['sst'] == "center"){
	$sql_order .= " center {$sod} ";
}else if($_GET['sst'] == "jijum"){
	$sql_order .= " jijum {$sod} ";
}else if($_GET['sst'] == "jisa"){
	$sql_order .= " jisa {$sod} ";
}else if($_GET['sst'] == "bonbu"){
	$sql_order .= " bonbu {$sod} ";
}else{
    $sql_order .= " day desc ";
}


    $colspan = 9;
    $sql_common = " from soodang_extra WHERE 1=1 ";


    $sql = " select count(*) as cnt 
    {$sql_common} 
    {$sql_search}";

    $rows = sql_fetch($sql);
    $total_count = $rows['cnt'];

    $rows = 200;
    $total_page  = ceil($total_count / $rows);  // 전체 페이지 계산
    if ($page < 1) $page = 1; // 페이지가 없으면 첫 페이지 (1 페이지)
    $from_record = ($page - 1) * $rows; // 시작 열을 구함

    $query = " select *
                {$sql_common} 
                {$sql_search}
                {$sql_order}
                limit {$from_record}, {$rows} ";
                
    $result = sql_query($query);

    $rank_category = ['센터수당지급','지점수당지급','지사수당지급','본부수당지급'];

    function member_cnt($val){
        global $mb_lvl;
        $key = array_search($val,array_column($mb_lvl,'mb_level'));
        
        if($key > -1){
            return $mb_lvl[$key]['cnt'];
        }else{
            return 0;
        }
    }
    
?>


<script>
$(function(){
    $("#fr_date, #to_date, #start_dt, #end_dt").datepicker({ changeMonth: true, changeYear: true, dateFormat: "yy-mm-dd", showButtonPanel: true, yearRange: "c-99:c+99", maxDate: "+0d" });
});

function fvisit_submit(act)
{
    var f = document.fvisit;
    f.action = act;
    f.submit();
}
</script>


<link href="<?= G5_ADMIN_URL ?>/css/scss/bonus/bonus_list.css" rel="stylesheet">

<style>
    .red{color:red}
    .text-center{text-align:center}
    .sch_last{display:inline-block;}
    .rank_img{width:20px;height:20px;margin-right:10px;}
	.btn_submit{width:100px;margin-left:20px;}
	.black_btn{background:#333 !important; border:1px solid black !important; color:white;}
    .cellbg_0{color:dodgerblue}
    .cellbg_1{color:brown;}

    .outbox{margin-right:0 !important;}

    .outbox.rank_bonus .benefit {
        background: darkorange;
    }
    .outbox +li{
        margin-left:-3px;
    }
    .outbox +li input{
        height:36px;
    }


    .mb_id{width:130px;}
    .btn_list {text-align: right;}
    .btn_submit.fix{color:black;}
</style>

<div class="local_desc01 local_desc">
	<p>
		조직정보 : 수당지급시점의 조직정보상태이며, 정보수정/등록 시 회원정보쪽에도 변경내용 연동처리됨  
            <br><strong>조직관리 : 하위직급이 누락된 경우 - 자동으로 상위 직급으로 표시되며(빨간색 미등록인상태)</strong><br>
            ① 체크- 정보수정/등록 하여 빨간색이 아닌상태에서만 직급수당 지급됨. <br>
	</p>
</div>

<div class="local_ov01 local_ov white" style="border-bottom:1px dashed black;">
    <li class="outbox">
		<label for="to_date" class="sound_only">기간 종료일</label>
        <span>기준일자</span>
		<input type="text" name="to_date" value="<?=$to_date?$to_date:date("Ymd")?>" id="to_date" required class="required frm_input date_input" size="13" maxlength="10">
        <input type="radio" name="price" id="pv" value='pv' checked='true' style="display:none;">
		
	</li>

	<li class="right-border ">
        <span>승급실행차수</span>
		<label for="to_date" class="sound_only">차수</label>
		<input type="text" name="exc_layer" id="exc_layer" class="required frm_input date_input" size="3" value="<?=$total_layer?>">
	</li>

	<?
	$sql = "select * from {$g5['bonus_config']} where used = 3  order by no";
	$ranklist = sql_query($sql);
	for ($i = 0; $row = sql_fetch_array($ranklist); $i++) { 
		$code = $row['code'];
	?>
        <li class='outbox <?=$code?>'>
            <?if($super_admin == 1){?><input type='submit' name="act_button" value="<?= $row['name'] ?> 보너스 지급" class="frm_input benefit" onclick="bonus_excute('<?= $code ?>','<?= $row['name'] ?>');"><?}?>
        </li>
        <li>
            <input type="submit" name="act_button" value="<?= $row['name'] ?> 보너스 내역" class="view_btn" style="height:36px; " onclick="bonus_view('<?= $code ?>');">
        </li>
	<? } ?>

</div>



<form name="fsearch" id="fsearch" class="local_sch01 local_sch" style="clear:both;padding:10px 20px 20px;" method="get">

	<label for="sfl" class="sound_only">검색대상</label>
	<select name="sfl" id="sfl">
		<option value="mb_id" <?php echo get_selected($_GET['sfl'], "mb_id"); ?>>회원아이디</option>>
		<option value="mb_name" <?php echo get_selected($_GET['sfl'], "mb_name"); ?>>회원이름</option>
		<option value="mb_nick" <?php echo get_selected($_GET['sfl'], "mb_nick"); ?>>회원닉네임</option>
	</select>
    <input type="text" name="stx" value="<?php echo $stx ?>" id="stx" class="frm_input" style="padding-left:5px;">

	
	<label for="stx" class="sound_only">기간검색<strong class="sound_only"> 필수</strong></label>
	검색 기간 : <input type="text" name="start_dt" id="start_dt" placeholder="From" class="frm_input date" value="<?= $start_dt ?>" />
	~ <input type="text" name="end_dt" id="end_dt" placeholder="To" class="frm_input date" value="<?= $end_dt ?>" />
	

	| 검색차수 :<label for="stx" class="sound_only">검색차수<strong class="sound_only"> 필수</strong></label>
	<input type="text" name="slayer" value="<?=$slayer?>" id="slayer" class="frm_input text" placeholder="검색차수">
    |
    <input type="text" name="center" value="<?=$center?>" id="center" class="frm_input text" placeholder="센터회원/명">
    <input type="text" name="jijum" value="<?=$jijum?>" id="jijum" class="frm_input text" placeholder="지점회원/명">
    <input type="text" name="jisa" value="<?=$jisa?>" id="jisa" class="frm_input text" placeholder="지사회원/명">
    <input type="text" name="bonbu" value="<?=$bonbu?>" id="bonbu" class="frm_input text" placeholder="본부회원/명">
	
    <input type="submit" class="btn_submit search" style="width:100px;margin-left:10px;" value="검색" />
    
	<input type="button" class="btn_submit excel" style="width:100px;margin-left:5px;" value="전체엑셀다운" onclick="document.location.href='/excel/excel_excute.php?excel=soodang_extra&order=no asc'" />
	
	</div>
</form>



<link href="https://cdn.jsdelivr.net/npm/remixicon@2.3.0/fonts/remixicon.css" rel="stylesheet">
<form name="benefitlist" id="benefitlist" method="POST" action="./bonus.level_org_proc.php?<?=$qstr?>" onsubmit="" >
    <input type="hidden" name="slayer" value="<?=$slayer?>">

    <div class="btn_list01 btn_list">
        <!-- <input type="button" name="view_all" id="view_all" class="btn-default <?if($page_view == 'all'){echo 'active';}?>" style='float:left' value="전체현황보기"> -->
        <input type="submit" name="act_button" class="btn_submit fix" value="정보수정/등록" onclick="document.pressed=this.value">
    </div>

    <div class="tbl_head01 tbl_wrap">
    
    <table>
    <caption><?php echo $g5['title']; ?> 목록</caption>
    <thead>
    <tr>
        <th scope="col" class="num">
            <label for="chkall" class="sound_only">상품 전체</label>
            <input type="checkbox" name="chkall" value="1" id="chkall" onclick="check_all(this.form)">
        </th>
        <th>no</th>
        <th class='mb_id'>회원아이디</th>
        <th style="width:130px;">기준매출내역</th>
        <th>해당차수</th>
        <th>해당차수 1차수당(원)</th>
        <th class='mb_id'>추천인</th>
        <th class='mb_id'><?php echo subject_sort_link('center',$qstr) ?>센터 (<?=$bonus_rates[0]?>%)</th>
        <th class='mb_id'><?php echo subject_sort_link('jijum',$qstr) ?>지점 (<?=$bonus_rates[1]?>%)</th>
        <th class='mb_id'><?php echo subject_sort_link('jisa',$qstr) ?>지사 (<?=$bonus_rates[2]?>%)</th>
        <th class='mb_id'><?php echo subject_sort_link('bonbu',$qstr) ?>본부 (<?=$bonus_rates[3]?>%)</th>
    </tr>
    </thead>
    <tbody>
    <?php
    function input_replace($val,$category){
        $input = "<input type='text' class='frm_input text-center red' name='".$category."[]' value='".$val."' >";
        return $input;
    }

    function link_with_id($val,$category){
        if(strlen($val) < 4){
            $link = "<input type='text' class='frm_input text-center' name='".$category."[]' value='".$val."' >";
        }else{
            $link = "<input type='hidden' name='".$category."[]' value='".$val."' ><a href='/adm/member_form.php?w=u&mb_id=".$val."'>$val</a>";
        }
        return $link;
    }

    function org_check($center_1,$jijum_1,$jisa_1,$bonbu_1){

        $list = [$center_1,$jijum_1,$jisa_1,$bonbu_1];
        $res = array_keys(array_filter($list));

        if(count($res) > 1){
            $blank_key = min($res);
        }else{
            $blank_key = 0;
        }
        
        // $center = blank_replace_input($center_1,);
        if($blank_key == 0){
            return array(link_with_id($center_1,'center'),link_with_id($jijum_1,'jijum'),link_with_id($jisa_1,'jisa'),link_with_id($bonbu_1,'bonbu'));
        }else if ($blank_key == 1){
            return array(input_replace($jijum_1,'center'),link_with_id($jijum_1,'jijum'),link_with_id($jisa_1,'jisa'),link_with_id($bonbu_1,'bonbu'));
        }else if ($blank_key == 2){
            return array(input_replace($jisa_1,'center'),input_replace($jisa_1,'jijum'),link_with_id($jisa_1,'jisa'),link_with_id($bonbu_1,'bonbu'));
        }else if ($blank_key == 3){
            return array(input_replace($bonbu_1,'center'),input_replace($bonbu_1,'jijum'),input_replace($bonbu_1,'jisa'),link_with_id($bonbu_1,'bonbu'));
        }
        
    }

    for ($i=0; $row=sql_fetch_array($result); $i++) {
        $bg = 'bg'.($i%2);
        $total_bonus += $row['bonus'];

        list($center,$jijum,$jisa,$bonbu) = org_check(trim($row['center']),trim($row['jijum']),trim($row['jisa']),trim($row['bonbu']));
    ?>
   
    <tr class="<?php echo $bg; ?>">
        <!--체크박스-->
        <td class="td_chk">
            <label for="chk_<?php echo $i; ?>" class="sound_only"></label>
            <input type='hidden' name='no[]' value="<?=$row['no']?>">
            <input type="checkbox" name="chk[]" value="<?php echo $i ?>" id="chk_<?php echo $i; ?>">
        </td>
        <td class='no text-center'><?=$row['no']?></td>
        <td class='text-center'><input type='hidden' name="mb_id[]" value="<?=$row['mb_id']?>"/><a href="/adm/member_form.php?w=u&mb_id=<?=$row['mb_id']?>"><?=$row['mb_id']?></a></td>
        <td class='text-center'><a href="/adm/shop_admin/orderlist.php?sel_field=od_id&search=<?=$row['od_id']?>"><?=$row['od_id']?></a> </td>
        <td class='text-center'><?=$row['count']?> </td>
        <td class='text-center'><?=shift_auto($row['bonus'])?></td>
        <td class='text-center'><a href='/adm/member_form.php?w=u&mb_id=<?=$row['recommend']?>'><?=$row['recommend']?></a></td>
        <td class='text-center'><?=$center?></td>
        <td class='text-center'><?=$jijum?></td>
        <td class='text-center'><?=$jisa?></td>
        <td class='text-center'><?=$bonbu?></td>
    </tr>

    <?php
    }
    if ($i == 0)
        echo '<tr><td colspan="'.$colspan.'" class="empty_table">자료가 없거나 관리자에 의해 삭제되었습니다.</td></tr>';
    ?>
    </tbody>
    <tfoot>
        <td><?=$i?>건</td>
        <td colspan="3"></td>
        <td><?=shift_auto($total_bonus)?></td>
        <td></td>
        <td></td>
        <td><?=shift_auto($total_bonus * ($bonus_rates[0]/100))?></td>
        <td><?=shift_auto($total_bonus * ($bonus_rates[1]/100))?></td>
        <td><?=shift_auto($total_bonus * ($bonus_rates[2]/100))?></td>
        <td><?=shift_auto($total_bonus * ($bonus_rates[3]/100))?></td>
    </tfoot>
    </tfoot>
    </table>
</div>

<div class="btn_list01 btn_list">
        <!-- <input type="button" name="view_all" id="view_all" class="btn-default <?if($page_view == 'all'){echo 'active';}?>" style='float:left' value="전체현황보기"> -->
        <input type="submit" name="act_button" class="btn_submit fix" value="정보수정/등록" onclick="document.pressed=this.value">
</div>
</form>

<?php
if (isset($domain)){
    $qstr .= "&amp;domain=$domain";
    $qstr .= "&amp;page=";
}

$pagelist = get_paging($config['cf_write_pages'], $page, $total_page, "{$_SERVER['SCRIPT_NAME']}?$qstr");
echo $pagelist;

?>

<script>
    var str = '';
    function bonus_excute(n, name) {
		var exc_layer = $("#exc_layer").val();
		var exc_txt = exc_layer + "대 :";
		var tx = '을 실행';
		


		if (!confirm(document.getElementById("to_date").value + "일\n" + exc_txt + name + tx + ' 하시겠습니까?')) {
			return false;
		}

		str = str + 'to_date=' + document.getElementById("to_date").value;
		str += "&exc_layer="+exc_layer;
		location.href = '/adm/bonus/bonus.' + n + '.php?' + str;
	}

    $(function() {
		
		$('.search_item:checked').each(function() {
			$(this).addClass('active');
		});

		$('.search_item').on('click', function() {
			var chk = $(this).is(":checked");
			if (chk) {
				$(this).addClass('active');
			} else {
				$(this).removeClass('active');
			}
		});
	});

</script>



<?
include_once (G5_ADMIN_PATH.'/admin.tail.php');
?>


