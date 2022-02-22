<?
include_once('./_common.php');

$debug = 1;
$today=date("Y-m-d");

$target = $_REQUEST['excel'];

if($_REQUEST['order']){
    $order = $_REQUEST['order'];
}else{
    $order = " create_dt desc ";
}

header( "Content-type: application/vnd.ms-excel;charset=utf-8" ); 
header( "Content-Disposition: attachment; filename=$target-$today".".xls" );
header( "Content-Description: PHP4 Generated Data" ); 
print("<meta http-equiv=\"Content-Type\" content=\"application/vnd.ms-excel; charset=utf-8\">");

function sql_fetch_row_array($result)
{
	if(function_exists('mysqli_fetch_row') && G5_MYSQLI_USE)
		$row = @mysqli_fetch_row($result);
	else
		$row = @mysqli_fetch_row($result);

	return $row;
}

?>

<html> 
<body>
    
<style type="text/css">
/*br태그 alt+enter 로 치환하기*/
br{mso-data-placement:same-cell;}
.text{mso-number-format:'\@';}
.title_header{background:royalblue;color:white}
@page{ margin:2in 1in 2in 1in } 
</style>

<table cellspacing=0 cellpadding=0 border=1>
      
<?
$query = "select * from {$target} order by {$order}";
$result = sql_query($query);
?>

<!-- 출금 -->
<?if($target == 'wallet_withdrawal_request'){?>
    <tr align='center' class='title_header'>
        <td>입금은행</td>
        <td>입금계좌번호</td>
        <td>받는분</td>
        <td>이체금액</td>
        <td>받는분통장표시</td>
        <td>내통장표시</td>
    </tr>

    <?while($row = sql_fetch_array($result)){?>
        <tr align='center'>
            <td><?=$row['bank_name']?></td>
            <td class='text'><?=$row['bank_account']?></td>
            <td><?=$row['account_name']?></td>
            <td><?=Number_format($row['amt'])?></td>
            <td></td>
            <td></td>
        </tr>
    <?}?>

<?}else if($target == 'soodang_extra' ){?>
    <tr align='center' class='title_header'>
        <td>no</td>
        <td>차수</td>
        <td>주문번호</td>
        <td>1차수당금액</td>
        <td>날짜</td>
        <td>날짜시간</td>
        <td>회원아이디</td>
        <td>추천인</td>
        <td>센터</td>
        <td>지점</td>
        <td>지사</td>
        <td>본부</td>
    </tr>  
    <?while($row = sql_fetch_row_array($result)){?>
        <tr align='center'>
        <?for($i=0;$i<count($row);$i++){?>
            <td><?=$row[$i]?></td>
        <?}?>
        </tr>
    <?}?>

<?}else{?>

    <tr align='center'>
        <td>번호</td>
        <td>이름</td>
        <td>연락처</td>		
        <td>우편번호</td>	
        <td>주소</td>
        <td>홈페이지</td>
        <td>작성일</td>
        <td>IP</td>
    </tr>

    <?while($row = sql_fetch_row_array($result)){?>
        <tr align='center'>
        <?for($i=0;$i<count($row);$i++){?>
            <td><?=$row[$i]?></td>
        <?}?>
        </tr>
    <?}?>

<?}?>


</table>
</body> 
<script>
window.close();
</script>
</html> 
