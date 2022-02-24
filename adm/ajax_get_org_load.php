<?php
$sub_menu = "600300";
include_once('./_common.php');
include_once('./inc.member.class.php');

if ($member['mb_org_num']){
	$max_org_num = $member['mb_org_num'];
}else{
	$max_org_num = 50;
}
$org_num     = 0;


if ($gubun=="B"){
	$class_name     = "g5_member_bclass";
	$recommend_name = "mb_brecommend";
}else{
	$class_name     = "g5_member_class";
	$recommend_name = "mb_recommend";
}

$sql = "SELECT c.c_id,c.c_class,(
	SELECT mb_level
	FROM g5_member
	WHERE mb_id=c.c_id) AS mb_level,(
	SELECT mb_name
	FROM g5_member
	WHERE mb_id=c.c_id) AS c_name,(
	SELECT COUNT(*)
	FROM g5_member
	WHERE mb_recommend=c.c_id) AS c_child,(
	SELECT mb_b_child
	FROM g5_member
	WHERE mb_id=c.c_id) AS b_child,(
	SELECT mb_id
	FROM g5_member
	WHERE mb_brecommend=c.c_id AND mb_brecommend_type='L') AS b_recomm,(
	SELECT mb_id
	FROM g5_member
	WHERE mb_brecommend=c.c_id AND mb_brecommend_type='R') AS b_recomm2,(
	SELECT COUNT(mb_no)
	FROM g5_member
	WHERE mb_brecommend=c.c_id AND mb_leave_date = '') AS m_child,  (
	SELECT mb_no
	FROM g5_member
	WHERE mb_id=c.c_id) AS m_no
	,(select mb_save_point FROM g5_member WHERE mb_id=c.c_id) AS mb_rate
	,(select mb_pv FROM g5_member WHERE mb_id=c.c_id) AS mb_pv
	,(select recom_sales FROM g5_member WHERE mb_id=c.c_id) AS recom_sales
	,(SELECT mb_child FROM g5_member WHERE mb_id=c.c_id) AS mb_children
	,(SELECT mb_nick FROM g5_member WHERE mb_id=c.c_id) AS mb_nick
	,(SELECT mb_center FROM g5_member WHERE mb_id=c.c_id) AS mb_center
	,(SELECT mb_jijum FROM g5_member WHERE mb_id=c.c_id) AS mb_jijum
	,(SELECT mb_jisa FROM g5_member WHERE mb_id=c.c_id) AS mb_jisa
	,(SELECT mb_bonbu FROM g5_member WHERE mb_id=c.c_id) AS mb_bonbu
	FROM g5_member m
	JOIN ".$class_name." c ON m.mb_id=c.mb_id
	WHERE c.mb_id='{$member['mb_id']}' AND c.c_id='$go_id'
";
// print_R($sql);
$srow = sql_fetch($sql);
$my_depth = strlen($srow['c_class']);


if ($order_proc==1){
	$sql  = "select today as tpv from brecom_bonus_today where mb_id='".$srow['c_id']."'";
	$row2 = sql_fetch($sql);

	$sql  = "select noo as tpv from ".$ngubun."recom_bonus_noo where mb_id='".$srow['c_id']."'";
	$row3 = sql_fetch($sql);


	$sql  = "select thirty as tpv from ".$ngubun."thirty where mb_id='".$srow['c_id']."'";
	$row5 = sql_fetch($sql);
}else{


	$sql  = "select no,today as tpv from ".$ngubun."today where mb_id='".$srow['c_id']."'";
	$row2 = sql_fetch($sql);

	if ($row2['no']){

	}else{

		$sql  = "select ".$order_field." as tpv from g5_shop_order where mb_id='".$srow['c_id']."' and od_time between '$fr_date 00:00:00' and '$to_date 23:59:59'";
		$row2 = sql_fetch($sql);
		if (!$row2['tpv']) $row2['tpv'] = 0;
		sql_query("insert ".$ngubun."today SET today=".$row2['tpv']." ,mb_id='".$srow['c_id']."'");	
	}

	$sql  = "select no,noo as tpv from ".$ngubun."noo where mb_id='".$srow['c_id']."'";
	$row3 = sql_fetch($sql);
	if ($row3['no']){

	}else{
		$sql  = "select ".$order_field." as tpv from g5_shop_order where mb_id in (select c_id from ".$class_name." where mb_id='".$member['mb_id']."'  and c_class like '".$srow['c_class']."%') and od_receipt_time between '$fr_date 00:00:00' and '$to_date 23:59:59'";
		$row3 = sql_fetch($sql);

		$row3 = sql_fetch($sql);
		if (!$row3['tpv']) $row3['tpv'] = 0;
		$sql  = "insert ".$ngubun."noo SET noo=".$row3['tpv']." ,mb_id='".$srow['c_id']."'";
		sql_query($sql);	
	}

	//���� 30��
	$sql  = "select no,thirty as tpv from ".$ngubun."thirty where mb_id='".$srow['c_id']."'";
	$row5 = sql_fetch($sql);
	if ($row5['no']){

	}else{
		$sql  = "select ".$order_field." as tpv from g5_shop_order where mb_id in (select c_id from ".$class_name." where mb_id='".$member['mb_id']."' and c_class like '".$srow['c_class']."%') and od_receipt_time between '".Date("Y-m-d",time()-(60*60*24*30))." 00:00:00' and '".Date("Y-m-d",time())." 23:59:59'";
		$row5 = sql_fetch($sql);
		if (!$row5['tpv']) $row5['tpv'] = 0;
		sql_query("insert ".$ngubun."thirty SET thirty=".$row5['tpv']." ,mb_id='".$srow['c_id']."'");	
	}

}

//���̳ʸ� ���� ���� ����
if ($srow['b_recomm']){
	$left_sql = " SELECT mb_pv, (SELECT noo FROM brecom_bonus_noo WHERE mb_id ='{$srow['b_recomm']}' ) AS noo FROM g5_member WHERE mb_id = '{$srow['b_recomm']}' ";
	$mb_self_left_result = sql_fetch($left_sql);
	$mb_self_left_acc = $mb_self_left_result['mb_pv'] + $mb_self_left_result['noo'];
	$row6['tpv'] = $mb_self_left_acc ;
}else{
	$row6['tpv'] = 0;
}

//���̳ʸ� ������ ���� ����
if ($srow['b_recomm2']){
	$right_sql = " SELECT mb_pv, (SELECT noo FROM brecom_bonus_noo WHERE mb_id ='{$srow['b_recomm2']}' ) AS noo FROM g5_member WHERE mb_id = '{$srow['b_recomm2']}' ";
	$mb_self_right_result = sql_fetch($right_sql);
	$mb_self_right_acc = $mb_self_right_result['mb_pv'] + $mb_self_right_result['noo'];
	$row7['tpv'] = $mb_self_right_acc ;
}else{
	$row7['tpv'] = 0;
}

$sql    = "select c_class from ".$class_name." where mb_id='".$member['mb_id']."' and c_id='".$go_id."'";
$row4   = sql_fetch($sql);
$mdepth = (strlen($row4['c_class'])/2);

			$mb_my_sales=$row2['tpv'];
			$mb_habu_sum=$row3['tpv'];

			if($mb_my_sales==''){ $mb_my_sales=0; }
			if($mb_habu_sum==''){$mb_habu_sum=0;}

			$sql  = "update g5_member set mb_my_sales=".$mb_my_sales." , mb_habu_sum=".$mb_habu_sum."   where mb_id='".$member['mb_id']."'";
			sql_query($sql);

			if (!$srow['b_child']) $srow['b_child']=1;
			//if (!$srow['c_child']) $srow['c_child']=1;

			
if ($srow['c_class']){
?>
		<ul id="org" style="display:none" >
			<li>
			[<?=(strlen($srow['c_class'])/2)-1?>-<?=($srow['c_child'])?>-<?=($srow['b_child']-1)?>]
			|<?=get_member_label($srow['mb_level'])?>
			|<?=$srow['c_id']?>|<?=$srow['c_name']?>
			|<?=$srow['mb_center']?>
			|<?=$srow['mb_jijum']?>
			|<?=$srow['mb_level']?>
			|<?=$srow['mb_jisa']?>
			|<?=$srow['mb_bonbu']?>
			|<?if($srow['mb_nick'] != '') {echo trim($srow['mb_nick']);}else{echo '0';}?>
			|<?=($srow['mb_children']-1)?>
			|<?=Number_format($srow['mb_rate'])?>
			|<?=$srow['recom_sales']?>
			|<?=Number_format($srow['mb_pv'])?>
			|<?=(strlen($srow['c_class'])/2)-1?>
			|<?=($srow['c_child'])?>
			|<?=($srow['b_child']-1)?>
			|<?=$gubun?>


<?
			get_org_down($srow);
?>
			</li>
<?
?>
		</ul>
    <div id="chart-container" class="orgChart"></div>
    <script>
    $(function() {
      $('#chart-container').orgchart({
        'data' : $('#org'),
		 'zoom': false
		});

		var $container = $('#chart-container');
		
		var $chart = $('.orgchart');
		$chart.css('transform', "scale(1,1)");
		var div = $chart.css('transform');
		var values = div.split('(')[1];
		values = values.split(')')[0];
		values = values.split(',');
		var a = values[0];
		var b = values[1];
		var currentZoom = Math.sqrt(a*a + b*b);
		var zoomval = .8;
		$container.scrollLeft(($container[0].scrollWidth - $container.width())/2);
		var my_num = 0;

		// zoom buttons	
		$('#zoomIn').on('click', function () {
			my_num++;
			zoomval = currentZoom += 0.1;
			$chart.css("transform",'matrix('+zoomval+', 0, 0, '+zoomval+', 0 ,'+((my_num)*85)+')');    
			$container.scrollLeft(($container[0].scrollWidth - $container.width())/2);
		});

		$('#zoomOut').on('click', function () {
			zoomval = currentZoom -= 0.1;
			my_num--;
			$chart.css("transform",'matrix('+zoomval+', 0, 0, '+zoomval+', 0 ,'+((my_num)*85)+')');    
			$container.scrollLeft(($container[0].scrollWidth - $container.width())/2);

		});

    });
    </script>
<?}?>
