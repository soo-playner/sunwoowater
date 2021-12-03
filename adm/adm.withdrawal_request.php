<?php
$sub_menu = "700300";
include_once('./_common.php');
include_once(G5_THEME_PATH.'/_include/wallet.php');

$withdraw_curency = WITHDRAW_CURENCY;

$g5['title'] = "수당(".$withdraw_curency.") 출금 요청 내역";
include_once('./adm.header.php');


function short_code($string, $char = 8){
	return substr($string,0,$char)." ... ".substr($string,-8);
}

$sql_condition = " and od_type = '수당출금요청' " ;

if($_GET['fr_id']){
	$sql_condition .= " and A.mb_id = '{$_GET['fr_id']}' ";
	$qstr .= "&fr_id=".$_GET['fr_id'];
}

if($_GET['fr_date'] && $_GET['to_date']){
	$sql_condition .= " and DATE_FORMAT(A.create_dt, '%Y-%m-%d') between '{$_GET['fr_date']}' and '{$_GET['to_date']}' ";
	$qstr .= "&fr_date=".$_GET['fr_date']."&to_date=".$_GET['to_date'];
}

if($_GET['update_dt']){
	$sql_condition .= " and DATE_FORMAT(A.update_dt, '%Y-%m-%d') = '".$_GET['update_dt']."'";
	$qstr .= "&update_dt=".$_GET['update_dt'];
}

if($_GET['status'] != ''){
	// echo $_GET['status']."<Br><br>";
	$sql_condition .= " and A.status = '".$_GET['status']."'";
	$qstr .= "&status=".$_GET['status'];
}


if($_GET['ord']!=null && $_GET['ord_word']!=null){
	$sql_ord = "order by ".$_GET['ord_word']." ".$_GET['ord'];
}

$sql = " select count(*) as cnt, sum(amt) as hap, sum(amt) as amt_total, sum(fee) as feehap, sum(out_amt) as outamt from {$g5['withdrawal']} A WHERE 1=1 AND DATE_FORMAT(A.create_dt, '%Y-%m-%d') between '{$fr_date}' and '{$to_date}'	 ";
$sql .= $sql_condition;
$sql .= $sql_ord;

$row = sql_fetch($sql);

$total_count = $row['cnt'];
$total_hap = $row['hap'];
$total_amt = $row['amt_total'];
$total_out = $row['outamt'];
$total_fee = $row['feehap'];

/* print_r($sql);
echo "<br><br>"; */



$rows = 30;
$total_page  = ceil($total_count / $rows);  // 전체 페이지 계산
if ($page < 1) $page = 1; // 페이지가 없으면 첫 페이지 (1 페이지)
$from_record = ($page - 1) * $rows; // 시작 열을 구함

$sql = "select * from {$g5['withdrawal']} A  WHERE 1=1   ";
$sql .= $sql_condition;

if($sql_ord){
	$sql .= $sql_ord;
}else{
	$sql .= " order by create_dt desc ";
}

$sql .= " limit {$from_record}, {$rows} ";

$excel_query = $sql;
$list = sql_query($sql);

function return_status_tx($val){
	switch($val){
		case 0: return '요청';break;
		case 1: return '승인';break;
		case 2: return '대기';break;
		case 3: return '불가';break;
		case 4: return '취소';break;
	}
}
?>

<link href="https://cdn.jsdelivr.net/npm/remixicon@2.3.0/fonts/remixicon.css" rel="stylesheet">
<link href="<?=G5_ADMIN_URL?>/css/scss/adm.withdrawal_request.css" rel="stylesheet">

<script>
	$(function(){

		$('.regTb [name=status]').on('change',function(e){
			var refund = 'N';

			if (confirm('상태값을 변경하시겠습니까?')) {
				
			} else {
				return false;
			}

			if($(this).val() == '4'){
				if (confirm('출금요청금을 반환하시겠습니까?')) {
					refund = 'Y';	
				} else {
					refund = 'N';
				}
			}
			
			var  coins = $(this).parent().parent().find('.coin').val();
			console.log(coins);
			
			$.ajax({
					type: "POST",
					url: "/adm/adm.request_proc.php",
					dataType: "json",
					data:  {
						uid : $(this).attr('uid'),
						status : $(this).val(),
						refund : refund,
						coin : coins,
						func : 'withrawal'
					},
					success: function(data) {
						if(data.code =='0001'){
							alert(data.msg);
							location.reload();
						}else{
							alert("처리되지 않았습니다.");
						}
					},
					error:function(e){
						alert("오류가 발생하였습니다.");
					}
				});
		});


		$.datepicker.regional["ko"] = {
			closeText: "close",
			prevText: "이전달",
			nextText: "다음달",
			currentText: "오늘",
			monthNames: ["1월(JAN)","2월(FEB)","3월(MAR)","4월(APR)","5월(MAY)","6월(JUN)", "7월(JUL)","8월(AUG)","9월(SEP)","10월(OCT)","11월(NOV)","12월(DEC)"],
			monthNamesShort: ["1월","2월","3월","4월","5월","6월", "7월","8월","9월","10월","11월","12월"],
			dayNames: ["일","월","화","수","목","금","토"],
			dayNamesShort: ["일","월","화","수","목","금","토"],
			dayNamesMin: ["일","월","화","수","목","금","토"],
			weekHeader: "Wk",
			dateFormat: "yymmdd",
			firstDay: 0,
			isRTL: false,
			showMonthAfterYear: true,
			yearSuffix: ""
		};
		$.datepicker.setDefaults($.datepicker.regional["ko"]);

		$("#create_dt_fr,#create_dt_to, #update_dt").datepicker({ changeMonth: true, changeYear: true, dateFormat: "yy-mm-dd", showButtonPanel: true, yearRange: "c-99:c+99", maxDate: "+0d" });
	});

</script>

<style>
	table.regTb td.withdrwal{line-height:13px;padding:0 5px;}
	.withdrwal_info{font-weight:600;font-size:13px;}
	.withdrwal_info.coin_tx{font-size:11px;font-weight:300;word-break: break-all;}
</style>



<input type="button" class="btn_submit excel" value="엑셀 다운로드" onclick="window.location.href='../excel/withdrawal_request_excel_down.php?excel_sql=<?=urlencode($excel_query)?>'" />	  
<div class="local_ov01 local_ov">
	<a href="./adm.withdrawal_request.php?<?=$qstr?>" class="ov_listall"> 결과통계 <?=$total_count?> 건 = <strong><?=shift_auto($total_out)?> <?=WITHDRAW_CURENCY?> </strong></a> 
	<?
		// 현재 통계치
		$stats_sql = "SELECT status, sum(out_amt)  as hap, count(out_amt) as cnt from {$g5['withdrawal']} as A WHERE 1=1 ".$sql_condition. " GROUP BY status";
		$stats_result = sql_query($stats_sql);

		while($stats = sql_fetch_array($stats_result)){
			echo "<a href='./adm.withdrawal_request.php?".$qstr."&status=".$stats['status']."'><span class='tit'>";
			echo return_status_tx($stats['status']);
			echo "</span> : ".$stats['cnt'];
			echo "건 = <strong>".shift_auto($stats['hap']).' '.WITHDRAW_CURENCY."</strong></a>";
		}
	?>
</div>

<div class="local_desc01 local_desc">
    <p>
		- 기본값 : 요청 | <strong>승인 : </strong> 수동송금처리후 변경 | <strong>취소 : </strong> 취소시 반환처리하면 차감금액 반환
        <!-- <i class="ri-checkbox-blank-fill" style="color:green;border:1px solid #ccc;font-size:20px;"></i> : 마이닝출금 <i class="ri-checkbox-blank-fill" style="color:#4556ff;border:1px solid #ccc;font-size:20px;"></i> : 수당출금<br> -->
	</p>
</div>

<?php
$ord_array = array('desc','asc'); // 정렬 방법 (내림차순, 오름차순)
$ord_arrow = array('▼','▲'); // 정렬 구분용
$ord = isset($_REQUEST['ord']) && in_array($_REQUEST['ord'],$ord_array) ? $_REQUEST['ord'] : $ord_array[0]; // 지정된 정렬이면 그 값, 아니면 기본 정렬(내림차순)
$ord_key = array_search($ord,$ord_array); // 해당 키 찾기 (0, 1)
$ord_rev = $ord_array[($ord_key+1)%2]; // 내림차순→오름차순, 오름차순→내림차순
?>

<form name="site" method="post" action="" enctype="multipart/form-data" style="margin:0px;" id="form">
<div class="adminWrp">
	<!--<button type="button" class="total_right btn_submit btn2" style="padding:5px 15px; margin-left:20px; " onclick="location.href='./delete_db_sol.php?id=with'">초기화</button>-->
	<table cellspacing="0" cellpadding="0" border="0" class="regTb">
		<thead>
			<!-- <th style="width:3%;">선택</th> -->
			<th style="width:4%;"><a href="?ord=<?php echo $ord_rev; ?>&ord_word=uid">No <?php echo $ord_arrow[$ord_key]; ?></a></th>
			<th style="width:5%;">아이디 </th>
			<!-- <th style="width:5%;">하부추천인</th> -->
			<th style="width:auto">출금정보</th>
			<th style="width:7%">출금정보복사</th>
			<th style="width:6%;">출금단위</th>
			<th style="width:6%;">출금전잔고(<?=ASSETS_CURENCY?>)</th>
			<th style="width:7%;">출금요청액(<?=ASSETS_CURENCY?>)</th>
			<th style="width:5%;">수수료(<?=ASSETS_CURENCY?>)</th>
			<!-- <th style="width:6%;">출금계산액(<?=ASSETS_CURENCY?>)</th> -->
			

			<th style="width:8%;">실출금액 <span style='color:red'>(<?=WITHDRAW_CURENCY?>)</span></th>
			<th style="width:6%;">출금시세</th>
			
			<th style="width:6%;">요청일시</th>
			<th style="width:8%;">승인여부</th>
			<th style="width:6%;">상태변경일</th>
		</thead>

        <tbody>
		<?for ($i=0; $row=sql_fetch_array($list); $i++) { 
			$bg = 'bg'.($i%2);

			$member_sql = "SELECT * from g5_member WHERE mb_id = '{$row['mb_id']}' ";
			$mb = sql_fetch($member_sql);
			if($row['coin'] == '원'){
				$coin_class = 'font_blue';
			}else{
				$coin_class = 'font_green';
			}

			
		?>
	
			<tr class="<?php echo $bg; ?>">
				
				<!-- <td ><input type="checkbox" name="paid_BTC[]" value="<?=$row['uid']?>" class="pay_check">  </td> -->
				<td><?=$row['uid']?></td>
				<td><a href='/adm/member_form.php?w=u&mb_id=<?=$row['mb_id']?>'><?=$row['mb_id']?></a></td>
				<input type="hidden" value="<?=$row['mb_id']?>" name="mb_id[]">
				<!-- <td><?=$mb['mb_child']?></td> -->
				
				<td class='withdrwal'>
					<?if($row['coin'] == '원' || $row['coin'] == '$'){?>
						<?=$row['bank_name']?> | <span class='withdrwal_info' ><?=$row['bank_account']?></span>(<?=$row['account_name']?>)	 
					<?}else{?>	
						<input type="hidden" value="<?=$row['addr']?>" name="addr[]">
						<span class='withdrwal_info coin_tx'><?=retrun_tx_func($row['addr'],$row['coin'])?></span>
					<?}?>
				</td>

				<td><button type="button" class="btn inline_btn copybutton" style='font-size:11px !important;margin-right:10px;'>정보복사</button></td>
				
				<td class="td_amt"><input type="hidden" value="<?=$row['coin']?>" name="coin[]" class='coin'>
					<?=retrun_coin($row['coin'])?> 
				</td>
				
				<!-- 출금전잔고 -->
				<td class="gray" style='font-size:11px;'><?=shift_auto($row['account'],ASSETS_CURENCY)?></td>

				<!-- 출금요청액 -->
				<td class="td_amt <?=$coin_class?>"><?=shift_auto($row['amt'],ASSETS_CURENCY)?></td>
				
				<!-- 수수료 -->
				<td class="gray" style='line-height:18px;'>
					<?=shift_auto($row['fee'],ASSETS_CURENCY)?>
				</td>

				<!-- 출금계산 -->
				<input type="hidden" value="<?=shift_auto($row['amt'],ASSETS_CURENCY)?>" name="amt[]">

				<!-- <td class="gray" style='line-height:18px;'>
					<?=shift_auto($row['amt'],ASSETS_CURENCY)?> 
				</td> -->
				
				
				<!-- 실출금액 -->
				<td  class="td_amt" style="color:red">
					<input type="hidden" value="<?=shift_auto($row['out_amt'])?>" name="out_amt[]">
					<?=shift_auto($row['out_amt'],$row['coin'])?>
				</td>

				<!-- 출금시세 -->
				<td class="gray" style='font-size:11px;'><span><?=shift_auto($row['cost'],$row['coin'])?> <?=WITHDRAW_CURENCY?></span></td>

				<td  style="font-size:11px;"><?=timeshift($row['create_dt'])?></td>
				<td>
					<select name="status" uid="<?=$row['uid']?>" class='sel_<?=$row['status']?>'>
						<option <?=$row['status'] == 0 ? 'selected':'';?> value=0>요청</option>
						<option <?=$row['status'] == 1 ? 'selected':'';?> value=1>승인</option>
						<option <?=$row['status'] == 2 ? 'selected':'';?> value=2>대기</option>
						<option <?=$row['status'] == 3 ? 'selected':'';?> value=3>불가</option>
						<option <?=$row['status'] == 4 ? 'selected':'';?> value=4>취소</option>
					</select>
				</td>

				<input type="hidden" value="<?=$row['status']?>" name="t_status[]">
				<td class='gray' style="font-size:11px;">
				<?if($row['update_dt'] != '0000-00-00 00:00:00'){
					echo timeshift($row['update_dt']);
				}else{echo '-';}?></td>
			</tr>
		<?}?>
		</tbody>

		<tfoot>
			<td>합계:</td>
			<td><?=$total_count?></td>
			<td colspan=4></td>
			<td colspan=1><?=shift_auto_zero($total_amt)?></td>
			<td><?=shift_auto_zero($total_fee)?></td>
			
			<td colspan=1><?=shift_auto_zero($total_out,WITHDRAW_CURENCY)?></td>
			<td colspan=4></td>
		</tfoot>
    </table>
</div>
</form>

<!-- <input type="text" placeholder="지갑주소를 입력해주세요." class="frm_input wallet_input" id="wallet_address">
<input type="text" placeholder="지갑키를 입력해주세요." class="frm_input wallet_input" id="wallet_key">
<input type="button" value="전체선택" onclick="javascript:select_all_check()" class="transfer">
<input type="button" value="보내기" onclick="javascript:start_transfer()" class="transfer"> -->
<!-- // adminWrp // -->
<?php
$pagelist = get_paging($config['cf_write_pages'], $page, $total_page, $_SERVER['SCRIPT_NAME'].'?'.$qstr.'&amp;domain='.$domain.'&amp;page=');
if ($pagelist) {
	echo $pagelist;
}
?>
</div>

<script>
$(function() {
	$('.copybutton').on('click',function(){
		//commonModal("Address copy",'Your Wallet address is copied!',100);

		console.log( $(this).parent().parent().find('.withdrwal_info').text() );

		var $temp = $("<input>");
			$("body").append($temp);
		$temp.val($(this).parent().parent().find('.withdrwal_info').text()).select();
			document.execCommand("copy");
		$temp.remove();

		alert('주소가 복사되었습니다.');
	})
});

function select_all_check(){
if(!$(".pay_check").is(":checked")) { 
	$("input[type=checkbox]").prop("checked",true); 

}else { 
	$("input[type=checkbox]").prop("checked",false); 
}

}

function start_transfer(){

	if($('#wallet_address').val() == ""){
		alert("지갑주소를 입력해주세요.")
		return;
	}

	if($('#wallet_key').val() == ""){
		alert("지갑키를 입력해주세요.")
		return;
	}

	if(!$('.pay_check').is(":checked")){
		alert("보내실 명단을 체크해주세요.")
		return;
	}

	var f = document.getElementById("form")
	for (i=0; i<f.elements['paid_BTC[]'].length; i++)
        {
            if (f.elements['paid_BTC[]'][i].checked==false)
            {
			
				f.elements['mb_id[]'][i].disabled=true;
				f.elements['addr[]'][i].disabled=true;
				f.elements['coin[]'][i].disabled=true;
				f.elements['amt[]'][i].disabled=true;
                f.elements['out_amt[]'][i].disabled=true;
				f.elements['t_status[]'][i].disabled=true;
            }
        }
	
	$('#form').attr("action","./adm.transfer_withdrawal.php")
	$('#form').append("<input type='hidden' name='wallet_address' value='"+$('#wallet_address').val()+"'/>")
	$('#form').append("<input type='hidden' name='wallet_key' value='"+$('#wallet_key').val()+"'/>")
	f.submit();
}

</script>

<?
include_once ('./admin.tail.php');
?>

