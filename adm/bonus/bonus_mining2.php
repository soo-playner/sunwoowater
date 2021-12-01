<?php
$sub_menu = "600299";
include_once('./_common.php');
include_once('./bonus_inc.php');



$g5['title'] = '마이닝 현황 및 지급내역';
include_once('../admin.head.php');

$token = get_token();
auth_check($auth[$sub_menu], 'r');


$func = 1;

if($_GET['page_func']){
  $func = $_GET['page_func']; 
}

if($_GET['view'] == 'all'){
    $page_view = 'all';
}

$pre_sql = "select * from {$g5['bonus_config']} where used = 2 order by no asc";
$pre_list = sql_query($pre_sql);


// 보너스검색 필터 
$bonus_name_arr = [];

$allowcnt=0;
for ($i=0; $row=sql_fetch_array($pre_list); $i++) {
    
    $nnn="allowance_chk".$i;
    $html.= "<input type='checkbox' class='search_item' name='".$nnn."' id='".$nnn."'";

    if($$nnn !=''){
        $html.=" checked='true' ";
    }

    $html.=" value='".$row['code']."'><label for='".$nnn."' class='allow_btn'>". $row['name']."보너스</label>";
	array_push($bonus_name_arr,$row['name']);
	if(${"allowance_chk".$i} !=''){
		if($allowcnt==0){
			$sql_search .= " and ( (allowance_name='".${"allowance_chk".$i}."')";
		}else{
			$sql_search .= "  or ( allowance_name='".${"allowance_chk".$i}."' )";
		}
		$qstr.='&'.$nnn.'='.$row['allowance_name'].${"allowance_chk".$i};

		$allowcnt++;
	}
}
if ($allowcnt>0) $sql_search .= ")";


function bonus_name_tx($code,$return = ''){
    global $bonus_name_arr;

    if($code == 'mining_recommend'){
        $value =1;
    }else if($code == 'mining_sponsor'){
        $value =2;
    }else if($code == 'rank_bonus'){
        $value =3;
    }else{
        $value =0;
    }
    if($return == 'name'){
        return $bonus_name_arr[$value];
    }else{
        return $value;
    }
}

/* 마이닝 시작일 체크 */
$mining_check_sql = "SELECT * from g5_shop_order WHERE mine_rate != 0  AND mine_start_date != '0000-00-00' AND mine_start_date <= '{$now_date}' AND pay_count = 0 AND od_hope_date = '0000-00-00' ";

$mining_check_result = sql_query($mining_check_sql);

while($od_row = sql_fetch_array($mining_check_result)){
    $od_rate = $od_row['mine_rate'];
    $update_mb_mining_rate_sql = "UPDATE g5_member set mb_rate = mb_rate + {$od_rate} WHERE mb_id = '{$od_row['mb_id']}' ";
    $up_result = sql_query($update_mb_mining_rate_sql);

    if($up_result){
        $refresh_sql = "UPDATE g5_shop_order set od_hope_date = '{$now_date}' WHERE no = {$od_row['no']} ";
        $refresh_result = sql_query($refresh_sql);
    }
}

/* 아이디 검색*/
if ($stx) {
    $sql_search .= " and ( ";
	if(($sfl=='mb_id') || ($sfl=='mb_id')){
            $sql_search .= " ({$sfl} = '{$stx}') ";
          
	}else{
            $sql_search .= " ({$sfl} like '%{$stx}%') ";
          
    }
    $sql_search .= " ) ";
}

// 기간설정
if (empty($fr_date)) $fr_date = date("Y-m-d", strtotime(date("Y-m-d")."-7 day"));
if (empty($to_date)) $to_date = G5_TIME_YMD;

// 검색기간검색
if($start_dt){
    if($func == 1){
        $sql_search .= " and mine_date >= '{$start_dt}' and mine_date <= '{$end_dt}'";
    }else{
        $sql_search .= " and day >= '{$start_dt}' ";
        $sql_search .= " and day <= '{$end_dt}' ";
    }
    $fr_date = $start_dt;
    $to_date = $end_dt;
    $qstr ='&start_dt='.$start_dt.'&end_dt='.$end_dt;
}else{
    if($func ==1){
        if($page_view == 'all'){
            $sql_search .= " ";
        }else{
            $sql_search .= " AND mine_date = '{$to_date}'";
        }

    }else{
        $sql_search .= " and day >= '{$fr_date}' and day <= '{$to_date}'";
    }
    $qstr ='&to_date='.$to_date;
}


/* 마이닝현황 */
if($func == 1){
    $mine_ing_sql = " From g5_shop_order WHERE mine_table > -1 ".$sql_search;
    $sql = "SELECT count(*) as cnt ".$mine_ing_sql;
    $row = sql_fetch($sql);
    $total_count = $row['cnt'];

    $colspan = 11;
    $rows = $config['cf_page_rows'];
    $total_page  = ceil($total_count / $rows);  // 전체 페이지 계산
    if ($page < 1) $page = 1; // 페이지가 없으면 첫 페이지 (1 페이지)
    $from_record = ($page - 1) * $rows; // 시작 열을 구함

    $sql = "SELECT * ".$mine_ing_sql." limit {$from_record} , {$rows} ";
    
    $excel_sql = urlencode($sql);
    $result = sql_query($sql);
    $qstr .= '&page_func=1';
    if($_GET['view']){
        $qstr .= '&view=all';
    }

}else if($func == 2){
    $mine_history_sql = " From soodang_mining WHERE 1=1";

    $sql = "SELECT count(*) as cnt ".$mine_history_sql.$sql_search;
    
    $row = sql_fetch($sql);
    $total_count = $row['cnt'];

    $colspan = 7;
    $rows = $config['cf_page_rows'];
    $total_page  = ceil($total_count / $rows);  // 전체 페이지 계산
    if ($page < 1) $page = 1; // 페이지가 없으면 첫 페이지 (1 페이지)
    $from_record = ($page - 1) * $rows; // 시작 열을 구함

    $sql = "SELECT * ".$mine_history_sql.$sql_search." limit {$from_record} , {$rows} ";
    $result = sql_query($sql);
}


$listall = '<a href="'.$_SERVER['PHP_SELF'].'" class="ov_listall">전체목록</a>';


$qstr .= "&page_func=".$func;
$qstr.='&stx='.$stx.'&sfl='.$sfl;


function mining_kind($kind){
	if($kind == 'mining'){
		$color_class = 'green';
	}else if($kind == 'rank_bonus'){
		$color_class = 'blue';
	}else{
		$color_class = 'orange';
	}
	return $color_class;
}

function active_check($val, $target){
    $bool_check = $_GET[$target];
    if($bool_check == $val){
        return " active ";
    }
}


include_once(G5_PLUGIN_PATH.'/jquery-ui/datepicker.php');
?>   

<link href="<?=G5_ADMIN_URL?>/css/scss/bonus/bonus_mining.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/remixicon@2.3.0/fonts/remixicon.css" rel="stylesheet">


<div class="local_desc01 local_desc">
    <p>
		<strong>마이닝 현황 : </strong> 기준일 마이닝 보너스 지급대상자 확인 <span class='divide'></span>
        <strong>마이닝 지급 : </strong> 개별지급/ 전체선택후 선택지급 실행 <span class='divide'></span>
        <strong>수정 : </strong> 선택항목 지급일 수정<span class='divide'></span><br>
        <strong>마이닝 추천롤업지급 : </strong> 추천롤업지급 <span class='divide'></span>
        <strong>마이닝 후원롤업지급 : </strong> 후원롤업지급 <span class='divide'></span><br>
        <strong>마이닝 지급내역 : </strong> 기간별 지급한 내역 확인 (기본값 최근7일) <br>
        ※ 현재설정된 마이닝 지급량 : <strong><?=$mining_rate?> <?=$minings[0]?></strong> / 1 <?=$mining_hash[0]?><br>
	</p>
</div>


<form name="fsearch" id="fsearch" class="local_sch01 local_sch" style="clear:both;padding:10px 20px 20px;" method="get" >
	<input type='hidden' name='page_func' id="page_func" value='<?=$func?>'>
    <div class="local_ov white" >
        <li class="outbox">
            기준일 : 
            <input type="text" name="to_date" value="<?php if($to_date){echo $to_date; }else{echo date("Ymd");} ?>" id="to_date" required class="required frm_input date_input" size="13" maxlength="10"> 
        </li>
        <li class='outbox'>
            <input type='button' name="act_button" value="마이닝 현황"  class="frm_input box_btn benefit func_btn <?=active_check(1, 'page_func')?>" data-id=1 >
        </li>

        <li class='outbox' >
            <input type='button' name="act_button" value="마이닝 추천롤업 지급"  class="frm_input box_btn benefit exc_btn b1" data-id='mining_recommend' >
            <input type='button' name="act_button" value="마이닝 추천롤업 지급기록"  class="frm_input box_btn view_btn"  data-id='mining_recommend' >
        </li>

        <li class='outbox divided'>
            <input type='button' name="act_button" value="마이닝 후원롤업 지급"  class="frm_input box_btn benefit exc_btn b2" data-id='mining_sponsor' >
            <input type='button' name="act_button" value="마이닝 후원롤업 지급기록"  class="frm_input box_btn view_btn" data-id='mining_sponsor' >
        </li>

        <li class='outbox divided'>
            <input type='button' name="act_button" value="승급 보너스 지급"  class="frm_input box_btn benefit exc_btn b3" data-id='rank_bonus' >
            <input type='button' name="act_button" value="승급 보너스 지급기록"  class="frm_input box_btn view_btn" data-id='rank_bonus' >
        </li>

        <li class='outbox '>
            <input type='button' name="act_button" value="마이닝 지급내역"  class="frm_input box_btn benefit func_btn <?=active_check(2, 'page_func')?>" data-id=2>
        </li>

        <br>

        <li class='outbox' style='margin-top:10px;border-top:1px dashed #ccc;width:100%;padding-top:10px;'>
        상세검색 : 
            <select name="sfl" id="sfl">
                <option value="mb_id"<?php echo get_selected($_GET['sfl'], "mb_id"); ?>>회원아이디</option>
                <!-- <option value="mb_name"<?php echo get_selected($_GET['sfl'], "mb_name"); ?>>회원이름</option> -->
            </select>
        
        <label for="stx" class="sound_only">검색어<strong class="sound_only"> 필수</strong></label>
        <input type="text" name="stx" value="<?php echo $stx ?>" id="stx" class="frm_input">
        | 검색 기간 : <input type="text" name="start_dt" id="start_dt" placeholder="From" class="frm_input" value="<?=$fr_date?>" /> 
        ~ <input type="text" name="end_dt" id="end_dt" placeholder="To" class="frm_input" value="<?=$to_date?>"/>
        <?if($page_func == 2){
            print_R($html);
        }?>
        <input type="submit" class="btn_submit search" value="검색"/>
        <!-- <input type="button" class="btn_submit excel" value="엑셀" onclick="document.location.href='/excel/benefit_list_excel_down.php?excel_sql=<?echo $excel_sql ?>&start_dt=<?=$_GET['start_dt']?>&end_dt=<?=$_GET['end_dt']?>'" />	 -->
        </li>
    </div>
</form>


<form name="benefitlist" id="benefitlist" action='./bonus_mining_excute.php' onsubmit="return fmemberlist_submit(this);" method="post">
<input type="hidden" name="bonus_day" value="<?=$to_date?>">
<input type="hidden" name="global_mining_rate" id='global_mining_rate' value="<?=$mining_rate?>">
<!-- <input type="hidden" name="sst" value="<?php echo $sst ?>">
<input type="hidden" name="sod" value="<?php echo $sod ?>"> -->
<input type="hidden" name="sfl" value="<?php echo $sfl ?>">
<input type="hidden" name="stx" value="<?php echo $stx ?>">
<input type="hidden" name="page" value="<?php echo $page ?>">
<input type="hidden" name="token" value="<?php echo $token ?>">

<div class="local_ov01 ">
    <?php echo $listall ?>
    전체 <?php echo number_format($total_count) ?> 건 
</div>

    <?if($func == 1){?>
        <div class="btn_list01 btn_list">
            <input type="button" name="view_all" id="view_all" class="btn-default <?if($page_view == 'all'){echo 'active';}?>"style='float:left' value="전체현황보기">
            <input type="submit" name="act_button" value="선택 수정" onclick="document.pressed=this.value">
            <input type="submit" name="act_button" class='btn_submit' value="선택 마이닝 지급" onclick="document.pressed=this.value">
        </div>
        <div class="tbl_head01 tbl_wrap">
            마이닝보너스 현황
            <table>
            <caption><?php echo $g5['title']; ?> 목록</caption>
            <thead>
            <tr>
                <th scope="col" rowspan="2" id="mb_list_chk">
                    <label for="chkall" class="sound_only">회원 전체</label>
                    <input type="checkbox" name="chkall" value="1" id="chkall" onclick="check_all(this.form)">
                </th>
                <th scope="col">회원아이디</th>
                <th scope="col">마이닝상품</th>
                <th scope="col">마이닝해쉬</th>
                <th scope="col">마이닝구매일</th>
                <th scope="col">마이닝시작일</th>
                <th scope="col">마이닝지급일</th>
                <th scope="col">누적지급량</th>
                <th scope="col">누적지급회차</th>
                <th scope="col">마이닝지급량 (<?=strtoupper($minings[0])?>)</th>
                <th scope="col">마이닝지급</th>				
            </tr>
            </thead>
            <tbody>

            <?php
            for ($i=0; $row=sql_fetch_array($result); $i++) {
                $bg = 'bg'.($i%2);
                $mining_value = $row['mine_rate']*$mining_rate;
                $mining_sum += $mining_value;
                $mining_acc += $row['mine_acc'];
                $hash_sum +=  $row['mine_rate'];
            ?>

            <tr class="<?=$bg ?>">
                <td headers="mb_list_chk" class="td_chk">
                    <input type="hidden" name="no[]" value="<?=$row['no']?>"> 
                    <label for="chk_<?php echo $i; ?>" class="sound_only"></label>
                    <input type="checkbox" name="chk[]" value="<?php echo $i ?>" id="chk_<?php echo $i ?>">
                </td>
                <td width="80" class='text-center strong'>
                    <a href='/adm/member_form.php?w=u&mb_id=<?=$row['mb_id']?>'><?=get_text($row['mb_id'])?></a>
                </td>
                <td width='50' class='text-center'><?=$row['od_name'];?></td>
                <td width='50' class='text-center'><?=$row['mine_rate'];?></td>
                <td width='80' class='text-center'><?=$row['od_date']?></td>
                <td width='80' class='text-center'><?=$row['mine_start_date']?></td>
                <td width='80' class='text-center green strong'>
                    <input type='text' class='frm_input text-center green strong' value='<?=$row['mine_date']?>' name='mine_date[]'>
                </td>
                <td width="100" class='text-center'><?=shift_auto($row['mine_acc'],$minings[0]) ?></td>
                <td width='80' class='text-center green strong'><?=$row['pay_count']?></td>
                <td width="100" class='bonus'>
                    <input type="hidden" name="mine_bonus[]" value="<?=$mining_value?>"> 
                    <?=shift_auto($mining_value,$minings[0]) ?>
                </td>
                <td width="100" class='text-center'>
                    <input type='button' class='btn mining_excute' value='마이닝지급' data-no='<?=$row['no']?>' >
                </td>
            </tr>

            <?}
            if ($i == 0)
                echo '<tr><td colspan="'.$colspan.'" class="empty_table">조건에 해당되는건이 없습니다.</td></tr>';
            ?>
            </tbody>

            <tfoot>
            <tr class="<?php echo $bg; ?>">
                <td></td>
                <td><?=$i?>건</td>
                <td></td>
                <td><?=$hash_sum?></td>
                <td colspan='3'></td>
                <td><?=shift_auto($mining_acc,$minings[0])?></td>
                <td></td>
                <td width="150" class='bonus' style='color:red'><?=shift_auto($mining_sum,$minings[0])?></td>
                <td></td>
            </tr>
            </tfoot>
            </table>
        </div>
        <div class="btn_list01 btn_list">
            <input type="submit" name="act_button" value="선택 수정" onclick="document.pressed=this.value">
            <input type="submit" name="act_button" class='btn_submit' value="선택 마이닝 지급" onclick="document.pressed=this.value">
        </div>
    <?}else{?>
        
        <div class="tbl_head01 tbl_wrap">
        마이닝보너스 지급내역
            <table>
            <caption><?php echo $g5['title']; ?> 목록</caption>
            <thead>
            <tr>
                <th scope="col">회원아이디</th>
                <th scope="col">보너스구분</th>
                <th scope="col">지급일</th>
                <th scope="col">보유해쉬</th>
                <th scope="col">마이닝지급량 (<?=strtoupper($minings[0])?>)</th>
                <th scope="col">지급기록</th>			
            </tr>
            </thead>
            <tbody>

            <?php
            for ($i=0; $row=sql_fetch_array($result); $i++) {
                $bg = 'bg'.($i%2);
                $mining_sum += $row['mining'];
                $hash_sum +=  $row['rate'];
            ?>

            <tr class="<?=$bg ?>">
                
                <td width="80" class='text-center strong'>
                    <a href='/adm/member_form.php?w=u&mb_id=<?=$row['mb_id']?>'><?=get_text($row['mb_id'])?></a>
                </td>
                <td width='50' class='text-center'><?=bonus_name_tx($row['allowance_name'],'name');?></td>
                <td width='50' class='text-center'><?=$row['day'];?></td>
                <td width='50' class='text-center'><?=$row['rate'];?></td>
                <td width='80' class='text-center strong bonus_<?=bonus_name_tx($row['allowance_name']);?>'><?=point_number($row['mining']) ?></td>
                <td width='200' class='text-center'><?=$row['rec']?></td>
               
            </tr>

            <?}
            if ($i == 0)
                echo '<tr><td colspan="'.$colspan.'" class="empty_table">자료가 없습니다.</td></tr>';
            ?>
            </tbody>

            <tfoot>
            <tr class="<?php echo $bg; ?>">
                <td></td>
                <td></td>
                <td><?=$i?>건</td>
                <td><?=$hash_sum?></td>
                <td width="150" class='bonus text-center' style='color:red'><?=point_number($mining_sum)?> </td>
                <td></td>
            </tr>
            </tfoot>
            </table>
        </div>
    <?}?>

</form>


<?php echo get_paging(G5_IS_MOBILE ? $config['cf_mobile_pages'] : $config['cf_write_pages'], $page, $total_page, "{$_SERVER['PHP_SELF']}?$qstr&amp;page="); ?>
</section>


<script type="text/javascript" src="/js/common.js"></script>


<script>
function fmemberlist_submit(f)
{
    if (!is_checked("chk[]")) {
        alert(document.pressed+" 하실 항목을 하나 이상 선택하세요.");
        return false;
    }

    if(document.pressed == "선택 마이닝 지급") {
        if(!confirm("선택한 건의 마이닝을 지급 하시겠습니까?")) {
            return false;
        }
    }
    return true;
}

$(function(){
	$("#fr_date, #to_date").datepicker({ changeMonth: true, changeYear: true, dateFormat: "yy-mm-dd", showButtonPanel: true, yearRange: "c-99:c+99", maxDate: "+31d" });
	$("#start_dt, #end_dt").datepicker({ changeMonth: true, changeYear: true, dateFormat: "yy-mm-dd", showButtonPanel: true, yearRange: "c-99:c+99", maxDate: "+31d" });

	$('.search_item:checked').each(function() {
		$(this).addClass('active');
	});

    $('.search_item').on('click',function(){
        if($(this).is(":checked")){
            $(this).addClass('active');
        }else{
            $(this).removeClass('active');
        }
    });
    
	
    $('.func_btn').on('click',function(){
        var value = $(this).data('id');
        var to_date = $('#to_date').val();
        location.href="./bonus_mining2.php?page_func="+value+"&to_date="+to_date;
    });

    $('.view_btn').on('click',function(){
        var value = $(this).data('id');
        var to_date = $('#to_date').val();
        var file_src = value+"_"+to_date+".html";
        location.href=  g5_url+"/data/log/"+value+"/"+file_src ;
    });

    $('.exc_btn').on('click', function(){
        var value = $(this).data('id');
        var to_date = $('#to_date').val();
        
        if(value == 'mining_recommend'){
            var vaue_tx = '추천롤업';
        }else if(value == 'mining_sponsor'){
            var vaue_tx = '후원롤업';
        }else if(value == 'rank_bonus'){
            var vaue_tx = '승급보너스';
        }else{
            var vaue_tx = '오류';
        }

        if (confirm(to_date + '일 '+vaue_tx+' 마이닝 수당을 지급하시겠습니까?')) {
        } else {
            return false;
        }

        location.href="./bonus."+value+".php?page_func="+value+"&to_date="+to_date;
    });

    $('.mining_excute').on('click',function(){
        console.log('마이닝지급');
        var select_id = $(this).data('no');
        var global_mining_rate = $('#global_mining_rate').val();
        var bonus_day = $('#to_date').val();

        $.ajax({
            url: '/adm/bonus/bonus_mining_excute.php',
            type: 'POST',
            data: {
            "idx" : select_id,
            "global_mining_rate" : global_mining_rate,
            "bonus_day" : bonus_day,
            "act_button": 'solid'
            },
            dataType: 'json',
            success: function(result) {
                if(result.code == "0001"){
                    alert(result.sql);
                    location.reload();
                }else{
                    alert(result.sql);
                }
            },
            error: function(e){
                alert('정상처리되지 않았습니다. 문제가 지속되면 관리자에게 연락주세요');
            }
            
        });
    });

    $('#view_all').on('click',function(){
        var url = "./bonus_mining2.php?page_func=1&view=all";
        $(location).attr('href',url);
    });
    
});


</script>

<?php
include_once ('../admin.tail.php');
?>
