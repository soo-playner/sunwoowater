<?php
$sub_menu = "600100";
include_once('./_common.php');
$g5['title'] = '수당 설정/관리';

include_once('../admin.head.php');
include_once(G5_THEME_PATH.'/_include/wallet.php');

auth_check($auth[$sub_menu], 'r');
$token = get_token();

?>

<link href="<?=G5_ADMIN_URL?>/css/scss/bonus/bonus_config2.css" rel="stylesheet">
<form name="allowance" id="allowance" method="post" action="./bonus_config_update.php" onsubmit="return frmconfig_check(this);" >

<div class="local_desc01 local_desc">
    <p>
        - 마케팅수당설정 - 관리자외 설정금지<br>
        - 현재 누적볼 총합 : <strong><?=Number_format($total_ball_count)?></strong> / 다음대수까지 : <span class="bold">-<?=Number_format($next_layer_remain)?></span><br>
        - 현재 누적 차수 : <?=$total_layer?> 대
	</p>
</div>
<style>
    .tbl_head02 tbody .sub_th th{background:turquoise;border:1px solid #34c9ba;}
</style>

<div class="tbl_head02 tbl_wrap">
    <table >
    <caption><?php echo $g5['title']; ?> 목록</caption>
    <thead>
    <tr>
        <th scope="col" width="30px">No</th>
        <th scope="col" width="40px">사용</th>
        <th scope="col" width="100px">수당명</th>	
        <th scope="col" width="80px">수당코드</th>
        <th scope="col" width="80px">수당지급수단</th>
        <th scope="col" width="80px">수당한계</th>
		<th scope="col" width="200px">수당비율 (%)<br>( 콤마로 구분)</th>
		<th scope="col" width="200px">지급한계(대수/%)<br>( 콤마로 구분)</th>
        <th scope="col" width="80px">수당지급방식</th>
        <th scope="col" width="100px">수당조건</th>
        <th scope="col" width="auto">수당설명</th>
    </tr>
    </thead>

    <tbody>
    <?for($i=0; $row=sql_fetch_array($list); $i++){?>
        <?if($row['code'] == 'extra'){?>
        <tr class='sub_th'>
            <th scope="col" width="30px">No</th>
            <th scope="col" width="40px">사용</th>
            <th scope="col" width="100px">수당명</th>	
            <th scope="col" width="80px">수당코드</th>
            <th scope="col" width="80px"></th>
            <th scope="col" width="80px"></th>
            <th scope="col" width="200px">현재 누적볼수량</th>
            <th scope="col" width="200px">현재 누적 대수(자동) </th>
            <th scope="col" width="80px">수당지급방식</th>
            <th scope="col" width="100px">재기부횟수</th>
            <th scope="col" width="auto">수당설명</th>
        </tr>
        <?}?>

        <tr class='<?if($i == 0){echo 'first';}?>'>
    
        <td style="text-align:center"><input type="hidden" name="idx[]" value="<?=$row['idx']?>"><?=$row['idx']?></td>
        <td style="text-align:center"><input type='checkbox' class='checkbox' name='check' <?php echo $row['used'] > 0?'checked':''; ?>>
            <input type="hidden" name="used[]" class='used' value="<?=$row['used']?>">
        </td>
        <td style="text-align:center"><input class='bonus_input' name="name[]"  value="<?=$row['name']?>"></input></td>
        <td style="text-align:center"><input class='bonus_input' name="code[]"  value="<?=$row['code']?>"></input></td>
        
        <td style="text-align:center"><input class='bonus_input' name="kind[]"  value="<?=$row['kind']?>"></input></td>
        <td style="text-align:center"><input class='bonus_input' name="limited[]"  value="<?=$row['limited']?>"></input></td>
        <td style="text-align:center"><input class='bonus_input' name="rate[]"  value="<?=$row['rate']?>"></input></td>
        <td style="text-align:center"><input class='bonus_input' name="layer[]"  value="<?if($row['used'] == 9){echo $total_layer;}?>" readonly></input></td>
        <td style="text-align:center">
            <select id="bonus_source" class='bonus_source' name="source[]">
                <?php echo option_selected(0, $row['source'], "ALL"); ?>
                <?php echo option_selected(1, $row['source'], "추천인[tree]"); ?>
                <?php echo option_selected(2, $row['source'], "바이너리[binary]"); ?>
            </select>
        </td>
        <td style="text-align:center"><input class='bonus_input' name="bonus_condition[]"  value="<?=$row['bonus_condition']?>"></input></td>
        <td style="text-align:center"><input class='bonus_input' name="memo[]"  value="<?=$row['memo']?>"></input></td>
        </tr>
        <?}?>
    
    
    
    <tfoot>
        <td colspan=12 height="100px" style="padding:20px 0px" class="btn_ly">
            <input  style="text-align:center;padding:15px 50px;background:cornflowerblue;" type="submit" class="btn btn_confirm btn_submit" value="저장하기" id="com_send"></input>
            <input  style="text-align:center;padding:15px 50px;background:coral;" type="button" class="btn btn_confirm btn_submit" value="스케쥴미리보기" id="pre_schedule"></input>
        </td>
    </tfoot>
</table>

</div>
</form>

<?
    $mining_use = sql_fetch("SELECT used from wallet_bonus_config WHERE code = 'mining' ")['used'];
    if($mining_use > 0){
?>
<style>
    #mining_log{width:400px;margin: 20px;}
    #mining_log .head{border:1px solid #eee;background:orange;display: flex;width:inherit}
    #mining_log .body{border:1px solid #eee;display: flex;width:inherit}
    #mining_log dt,#mining_log dd{display:block;padding:5px 10px;text-align: center;width:inherit;}
    #mining_log dd{border-left:1px solid #eee}
</style>



<div id='mining_log'>
    마이닝 지급량 기록 (최근 7일)
    <div class='head'>
        <dt>지급일</dt>
        <dd>마이닝지급량</dd>
    </div>

    <?
        $mining_rate_result = sql_query("SELECT day,rate from soodang_mining group by day order by day desc limit 0,7");
        while($row = sql_fetch_array($mining_rate_result)){
    ?>
    <div class='body'>
        <dt><?=$row['day']?></dt>
        <dd><?=$row['rate']?></dd>
    </div>
    <?}?>
</div>
<?}?>

<script>

    function frmconfig_check(f){
        
    }

    $(document).ready(function(){

        $(".checkbox" ).on( "click",function(){
            if($("input:checkbox[name='check']").is(":checked") == true){
                console.log( $(this).next().val() );
                $(this).next().val(1);
            }else{
                $(this).next().val(0);
            }
        });

        $("#pre_schedule").on('click',function(){
            var options = 'top=10, left=10, width=500, height=700, status=no, menubar=no, toolbar=no, resizable=no';
            window.open("/core/","schedule",options);
        });
        
    });

</script>
</div>

<?php
include_once ('../admin.tail.php');
?>