<?php
	include_once('./_common.php');

	$type = $_POST['type'];

	if($_POST['mb_id']){
		// 추천인
		if($type == 1){
			$sth = sql_query("select mb_id,mb_level,grade from {$g5['member_table']}  where mb_level < 10 AND mb_level > 0  AND (mb_nick like '%{$_POST['mb_id']}%' OR mb_id like '%{$_POST['mb_id']}%' ) ");
			$rows = array();
			while($r = sql_fetch_array($sth)) {
				$rows[] = $r;
			}
			print json_encode($rows);

		// 센터멤버, 센터이름검색 
		}else if($type == 2){
			$sth = sql_query("select mb_id,mb_name,mb_nick,grade,mb_level from {$g5['member_table']}  where (mb_nick like '%{$_POST['mb_id']}%' OR mb_id like '%{$_POST['mb_id']}%' ) and (center_use ='1' OR mb_level =2) ");
			$rows = array();
			while($r = sql_fetch_array($sth)) {
				$rows[] = $r;
			}
			print json_encode($rows);
		}
	}else{
		print "[]";
	}

?>