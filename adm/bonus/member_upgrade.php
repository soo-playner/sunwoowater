<?php
$sub_menu = "600300";
include_once('./_common.php');

$g5['title'] = "승급 및 내역";

include_once(G5_ADMIN_PATH.'/admin.head.php');
include_once(G5_THEME_PATH.'/_include/wallet.php');
include_once(G5_PLUGIN_PATH.'/jquery-ui/datepicker.php');


// 누적통계
$mb_lvl = [];
$mb_lvl_result = sql_query("SELECT mb_level,COUNT(mb_id) AS cnt from g5_member WHERE mb_level < 9 GROUP BY mb_level ");
while($row = sql_fetch_array($mb_lvl_result)){
    array_push($mb_lvl, $row);
}

if (empty($fr_date)) $fr_date = date("Y-m-d", strtotime(date("Y-m-d")."-90 day"));
if (empty($to_date)) $to_date = G5_TIME_YMD;

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

$slayer = $_GET['slayer'];




$sql = "select * from {$g5['bonus_config']} where used = 4  order by no";
$ranklist = sql_query($sql);

// 수당으로 검색
if ($allowance_name) {
    $sql_search .= " and (";
    if ($chkc) {
        $sql_search .= " category='" . $allowance_name . "'";
    }
    $sql_search .= " )";
}


//지급차수 여부 
if($slayer > 0){
    $start_dt ='';
    $end_dt ='';
    $sql_search .= " and count = {$slayer} ";
}else{
    
    // 검색기간검색
    if ($start_dt) {
        $sql_search .= " and rank_day >= '{$start_dt}' ";
        $qstr .= "&start_dt=" . $start_dt;
    }
    if ($end_dt) {
        $sql_search .= " and rank_day <= '{$end_dt}'";
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


if($fr_id){
    $sql_search .= " AND mb_id = '{$fr_id}'";
}

    
    // 보너스검색 필터 
    $allowcnt = 0;
    for ($i = 0; $row = sql_fetch_array($ranklist); $i++) {

        $nnn = "allowance_chk" . $i;
        $html .= "<input type='checkbox' class='search_item' name='" . $nnn . "' id='" . $nnn . "'";

        if ($$nnn != '') {
            $html .= " checked='true' ";
        }

        $html .= " value='" . $i . "'><label for='" . $nnn . "' class='allow_btn'>" . $row['name'] . "보너스</label>";
        

        if (${"allowance_chk" . $i} != '') {
            if ($allowcnt == 0) {
                $sql_search .= " and ( (category='" . ${"allowance_chk" . $i} . "')";
            } else {
                $sql_search .= "  or ( category='" . ${"allowance_chk" . $i} . "' )";
            }
            $qstr .= '&' . $nnn . '=' . $row['allowance_name'] . ${"allowance_chk" . $i};

            $allowcnt++;
        }
    }
    if ($allowcnt > 0) $sql_search .= ")";


    $colspan = 9;
    $sql_common = " from rank WHERE 1=1 ";


    $sql = " select count(*) as cnt 
    {$sql_common} 
    {$sql_search}";

    $rows = sql_fetch($sql);
    $total_count = $rows['cnt'];

    $rows = 50;
    $total_page  = ceil($total_count / $rows);  // 전체 페이지 계산
    if ($page < 1) $page = 1; // 페이지가 없으면 첫 페이지 (1 페이지)
    $from_record = ($page - 1) * $rows; // 시작 열을 구함

    $sql = " select *
                {$sql_common} 
                {$sql_search}
                order by rank_day desc
                limit {$from_record}, {$rows} ";
    $result = sql_query($sql);

    $rank_category = ['직급승급','동반승급','관리자수동승급'];

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
    .outbox.rank .benefit {
        background: dodgerblue;
    }
    .outbox +li{
        margin-left:-3px;
    }
    .outbox +li input{
        height:36px;
    }

    .outbox.with_rank .benefit {
        background: brown;
    }
</style>

<div class="local_desc01 local_desc">
	<p>
		일반회원:   <strong><?=member_cnt('0')?></strong> 명
        | 정회원:   <strong><?=member_cnt('1')?></strong> 명
        | 센터:     <strong><?=member_cnt('2')?></strong> 명
        | 지점:     <strong><?=member_cnt('3')?></strong> 명
        | 지사:     <strong><?=member_cnt('4')?></strong> 명
        | 본부:     <strong><?=member_cnt('5')?></strong> 명
		
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
	$sql = "select * from {$g5['bonus_config']} where used = 4  order by no";
	$ranklist = sql_query($sql);
	for ($i = 0; $row = sql_fetch_array($ranklist); $i++) { 
		$code = $row['code'];
	?>
        <li class='outbox <?=$code?>'>
            <input type='submit' name="act_button" value="<?= $row['name'] ?> 실행" class="frm_input benefit" onclick="bonus_excute('<?= $code ?>','<?= $row['name'] ?>');">
        </li>
        <li>
            <input type="submit" name="act_button" value="<?= $row['name'] ?> 내역" class="view_btn" onclick="bonus_view('<?= $code ?>');">
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

	
	<label for="stx" class="sound_only">기간검색<strong class="sound_only"> 필수</strong></label>
	<input type="text" name="stx" value="<?php echo $stx ?>" id="stx" class="frm_input">
	검색 기간 : <input type="text" name="start_dt" id="start_dt" placeholder="From" class="frm_input date" value="<?= $start_dt ?>" />
	~ <input type="text" name="end_dt" id="end_dt" placeholder="To" class="frm_input date" value="<?= $end_dt ?>" />
	

	|<label for="stx" class="sound_only">검색차수<strong class="sound_only"> 필수</strong></label>
	<input type="text" name="slayer" value="<?=$slayer?>" id="slayer" class="frm_input text" placeholder="승급차수">

	<input type="submit" class="btn_submit search" value="검색" />
	<!-- <input type="button" class="btn_submit excel" value="엑셀" onclick="document.location.href='/excel/benefit_list_excel_down.php?excel_sql=<? echo $excel_sql ?>&start_dt=<?= $_GET['start_dt'] ?>&end_dt=<?= $_GET['end_dt'] ?>'" /> -->
	|
	<?= $html ?>
	</div>
</form>



<link href="https://cdn.jsdelivr.net/npm/remixicon@2.3.0/fonts/remixicon.css" rel="stylesheet">
<div class="tbl_head01 tbl_wrap">
    <table>
    <caption><?php echo $g5['title']; ?> 목록</caption>
    <thead>
    <tr>
        <th>no</th>
        <th>회원아이디</th>
        <th>기존직급</th>
        <th>승급직급</th>
        <th>승급종류</th>
        <th>승급차수</th>
        <th>승급산정PV(B)</th>
        <th>승급일</th>
        <th>승급기록</th>
    </tr>
    </thead>
    <tbody>
    <?php
    for ($i=0; $row=sql_fetch_array($result); $i++) {
        $bg = 'bg'.($i%2);
    ?>
   
    <tr class="<?php echo $bg; ?>">
        <td class='no'><?=$row['no']?></td>
        <td class='text-center'><?=$row['mb_id']?></td>
        <!-- <td class='text-center'><img src="<?=G5_URL?>/img/<?=$row['old_level']?>.png" class='rank_img'><?=$row['old_level'];?></td>	
        <td class='text-center'><img src="<?=G5_URL?>/img/<?=$row['rank']?>.png" class='rank_img'><?=$row['rank'];?> </td> -->
        <td class='text-center'> <?=$member_level_array[$row['old_level']]?></td>
        <td class='text-center'> <?=$member_level_array[$row['rank']]?></td>
        <td class='text-center cellbg_<?=$row['category']?>'> <?=$rank_category[$row['category']]?>	</td>
        <td class='text-center'> <?=$row['count'];?> 대	</td>
        <td class='text-center'> <?=Number_format($row['upgrade_clue'])?> B</td>
        <td class='text-center'> <?=$row['rank_day'];?>	</td>
        <td><?=$row['rank_note'];?></td>
    </tr>

    <?php
    }
    if ($i == 0)
        echo '<tr><td colspan="'.$colspan.'" class="empty_table">자료가 없거나 관리자에 의해 삭제되었습니다.</td></tr>';
    ?>
    </tbody>
    </table>
</div>

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

    
	function bonus_view(n) {
		console.log("bonus_view");
		// var strdate = document.getElementById("to_date").value;
		var str_layer = document.getElementById("exc_layer").value;

		file_src = n + "_" + str_layer + ".html";
		file_path = g5_url + "/data/log/" + n + "/" + file_src; //롤다운
		console.log(file_path);

		if (UrlExists(file_path)) {
			window.open(file_path);
		} else {
			alert('해당내역이 없습니다.');
		}
    }

    
	function UrlExists(url) {
		var http = new XMLHttpRequest();
		http.open('HEAD', url, false);
		http.send();
		return http.status != 404;
	}


</script>



<?
include_once (G5_ADMIN_PATH.'/admin.tail.php');
?>


