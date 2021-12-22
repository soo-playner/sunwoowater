<?php
$sub_menu = "700600";
include_once('./_common.php');
include_once(G5_THEME_PATH.'/_include/wallet.php');
include_once(G5_LIB_PATH.'/api/filecoin/filecoin.infura.php');

auth_check($auth[$sub_menu], 'r');

$g5['title'] = "입금 처리 내역";

include_once('./adm.header.php');

// tx hash - 사용안함
/* function short_code($string, $char = 8){
	return substr($string,0,$char)." ... ".substr($string,-8);
} */

$status_string = array('요청확인중','승인','대기','불가','취소');

function status($val){
    global $status_string;
    return $status_string[$val];
}


/* 조건검색*/
if($_GET['fr_id']){
	$sql_condition .= " and A.mb_id = '{$_GET['fr_id']}' ";
	$qstr .= "&fr_id=".$_GET['fr_id'];
}

if($fr_date && $to_date){
	$sql_condition .= " and DATE_FORMAT(A.create_dt, '%Y-%m-%d') between '{$fr_date}' and '{$to_date}' ";
	$qstr = "fr_date=".$fr_date."&amp;to_date=".$to_date."&amp;to_id=".$fr_id;
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


$colspan = 11;
// $to_date = date("Y-m-d", strtotime(date("Y-m-d")."+1 day"));

$sql_common = " from `{$g5['deposit']}` as A";
$sql_search = " WHERE 1=1 ".$sql_condition;
$sql = " select count(create_d) as cnt, sum(in_amt) as hap
{$sql_common}
{$sql_search}";
$rows = sql_fetch($sql);
$total_count = $rows['cnt'];
$total_hap = $rows['hap'];

$rows = 30;
$total_page  = ceil($total_count / $rows);  // 전체 페이지 계산
if ($page < 1) $page = 1; // 페이지가 없으면 첫 페이지 (1 페이지)
$from_record = ($page - 1) * $rows; // 시작 열을 구함

$sql = " select *
            {$sql_common}
            {$sql_search}
            order by create_dt desc
            limit {$from_record}, {$rows} ";
$result = sql_query($sql);

?>


<style>
    .red{color:red}
    .f_blue{color:blue}
    .f_red{color:red}
    .text-center{text-align:center}
    .hash{min-width:120px;height:auto;display:block;}
    .reg_text{border:1px solid #ccc;padding:5px 10px;width:80%;}
    select{padding:5px;min-width:80px;width:80%;}
    table tr td{text-align:center}
    .row_dup td{background:rgba(253,240,220,0.8)}
    .btn_submit.excel{background:green}

    
	.local_ov strong{color:red; font-weight:600;}
	.local_ov .tit{color:black; font-weight:600;}
	.local_ov a{margin-left:20px;}

    

    .btn.in_btn{background:#2b72db;color:white;padding:4px 10px;box-shadow:0px 2px 3px rgba(0,0,0,0.25)}
    .btn.in_btn:hover{background:gold;color:black;}
</style>


<script>
    function fil_update(){
        location.href = "/lib/api/filecoin/filecoin.php";
    }

	$(function(){

		$('.regTb [name=status]').on('change',function(e){
            var refund = 'N';
            var _target = $(this).parent().parent();

            var coin = _target.find('.coin').text();
            var amt = _target.find('.input_amt_val').val().replace(/,/g,'');

			if (confirm('상태값을 변경하시겠습니까?')) {
			} else {
				return false;
			}

			if($(this).val() == '1' && coin != '' && amt > 0){
				if (confirm('입금액을 반영하시겠습니까?')) {
					refund = 'Y';	
				} else {
					refund = 'N';
				}
			}

            console.log( `id : ${$(this).attr('uid')} / coin : ${coin} / amt_val : ${amt} / refund : ${refund}`);

            $.ajax({
                url: '/adm/adm.request_proc.php',
                type: 'POST',
                dataType: 'json',
                data: {
                    uid : $(this).attr('uid'),
                    status : $(this).val(),
                    refund : refund,
                    coin : coin,
                    amt : amt,
                    func : 'deposit'
                },
                success: function(res) {
                    if(res.result == "success"){
                        alert(res.msg);
                        location.reload();
                    }else{
                        alert("처리되지 않았습니다.");
                    }
                },
                error: function(e){
                    if(debug) dialogModal('ajax ERROR','IO ERROR','failed'); 
                }
                
            });
		});

        $('').on('click',function(e){

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




<div class="local_ov01 local_ov">
	<a href="./adm.deposit_request.php?<?=$qstr?>" class="ov_listall"> 결과통계 <?=$total_count?> 건 = <strong><?=number_format($total_hap,ASSETS_NUMBER_POINT)?></strong></a> 
	<?
		// 현재 통계치
		$stats_sql = "SELECT status, sum(in_amt) as hap, count(in_amt) as cnt from `{$g5['deposit']}` as A WHERE 1=1 ".$sql_condition. " GROUP BY status";
		$stats_result = sql_query($stats_sql);
        

		while($stats = sql_fetch_array($stats_result)){
			// $Nresult = $total_result['hap'] ? round($total_result['hap'],2) : '0';
			// $Ncount =  $total_result['cnt'];
			echo "<a href='./adm.deposit_request.php?".$qstr."&status=".$stats['status']."'><span class='tit'>";
			echo status($stats['status']);
			echo "</span> : ".$stats['cnt'];
			echo "건 = <strong>".Number_format($stats['hap'],ASSETS_NUMBER_POINT)."</strong></a>";
		}
	?>
</div>

<div class="local_desc01 local_desc">
    <p>
        <strong>- 요청확인중 :</strong> 기본값 | <strong>승인 :</strong> 입금금액 포인트 반영 | <strong>대기 :</strong> 확인처리중 | <strong>불가 :</strong> 입금자, 입금액 불일치 - 입금액변경하여 처리가능 | <strong>취소 :</strong> 미승인처리<br>
        - 상태값 승인 => 입금액 반영시에만 회원에게 포인트 지급처리<br>
        <!-- - <button type='button' class='btn in_btn' onclick="fil_update()">FIL 입금정보 가져오기</button> : 파일코인 TX코드 조회하여 입금요청금액 자동 업데이트 -->
	</p>
</div>

<form 
<div class="tbl_head01 tbl_wrap">
    <table class='regTb'>
    <caption><?php echo $g5['title']; ?> 목록</caption>
    <thead>
    <tr>
        <th scope="col" width='5%'>no</th>
        <th scope="col" width='8%'>아이디</th>
        <th scope="col" width='8%'>추천인</th>
        <th scope="col" width='auto'>입금자명</th>
        <th scope="col" width='8%'>입금요청금액</th>
        <th scope="col" width='5%'>입금종류</th>
        <th scope="col" width='10%'>입금처리금액 (<?=ASSETS_CURENCY?>)</th>
        <th scope="col" width='10%'>승인여부</th>
        <th scope="col" width='10%'>요청시간</th>
        <th scope="col" width='15%'>상태변경일</th>
    </tr>
    </thead>
    <tbody>
    <?php
    function value_color($val){
        if($val > 0 ){
            return "f_blue";
        }else{ 
            return "f_red";
        }
    }

    


    function retrun_value_func($value,$coin){
        if(strtolower($coin) == 'eth'){
            return shift_auto_zero($value,'eth');
        }else if(strtolower($coin) =='fil'){
            return shift_auto_zero($value);
        }else if(strtolower($coin) =='$'){
            return shift_auto_zero($value);
        }else{
            return shift_auto_zero($value,'원');
        }
    }

    for ($i=0; $row=sql_fetch_array($result); $i++) {
        $bg = 'bg'.($i%2);
        $duplicate_sql ="select COUNT(*) as cnt from `{$g5['deposit']}` WHERE mb_id='{$row['mb_id']}' ";
        $duplicate_result = sql_fetch($duplicate_sql);
        $duplicate = $duplicate_result['cnt'];

        if($duplicate > 1){$row_dup = 'row_dup';}else{$row_dup = '';}

        $member_sql = "SELECT A.mb_recommend,A.mb_sponsor,B.mb_brecommend from g5_member A, g5_member B WHERE A.mb_id = '{$row['mb_id']}' AND B.mb_id = A.mb_recommend";
        $member_result = sql_fetch($member_sql);
        
    ?>

    <tr class=" <?=$row_dup?>">
        <td ><?php echo $row['uid'] ?></td>
        <td style='color:#333;font-weight:600'><a href='/adm/member_form.php?sst=&sod=&sfl=&stx=&page=&w=u&mb_id=<?=$row['mb_id']?>' target='_blank'><?=$row['mb_id'] ?></a></td>
        <td style='color:#666'><?=$member_result['mb_recommend']?></td>
        <td>
            <?=retrun_tx_func($row['txhash'],$row['coin']);?>
        </td>
        <td style="text-align:right;padding-right:10px">
            <?=retrun_value_func($row['amt'],$row['coin']);?>
        </td>
        <td class='coin'><?=retrun_coin($row['coin'])?></td>
        <td><input type='text' class='reg_text input_amt_val <?=value_color($row['in_amt'])?>' style='font-weight:600;' value='<?=shift_auto_zero($row['in_amt'],'$')?>' inputmode="numeric"></td>
        
        <td>
            <!-- <?=status($row['status'])?> -->
            <select name="status" uid="<?=$row['uid']?>" class='sel_<?=$row['status']?>'>
                <option <?=$row['status'] == 0 ? 'selected':'';?> value=0>요청확인중</option>
                <option <?=$row['status'] == 1 ? 'selected':'';?> value=1>승인</option>
                <option <?=$row['status'] == 2 ? 'selected':'';?> value=2>대기</option>
                <option <?=$row['status'] == 3 ? 'selected':'';?> value=3>불가</option>
                <option <?=$row['status'] == 4 ? 'selected':'';?> value=4>취소</option>
            </select>	
        </td>
        <td><?=$row['create_dt']?></td>
        <td><?=$row['update_dt']?></td>
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
if (isset($domain))
    $qstr .= "&amp;domain=$domain";
$qstr .= "&amp;page=";

$pagelist = get_paging($config['cf_write_pages'], $page, $total_page, "{$_SERVER['SCRIPT_NAME']}?$qstr");
echo $pagelist;


include_once('./admin.tail.php');
?>
