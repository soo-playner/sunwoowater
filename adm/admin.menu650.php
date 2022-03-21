<?php
if($member['mb_id'] == 'admin'){
$menu['menu650'] = array (

    array('650000', '조직도보기', ''.G5_ADMIN_URL.'/member_tree.php','allowance_sett'),
    array('650100', '조직도(트리) 보기', ''.G5_ADMIN_URL.'/member_tree.php', 'bbs_board'),
    array('650200', '조직도(박스) 보기', ''.G5_ADMIN_URL.'/member_org.php', 'bbs_board')
	/*
    array('600920', 'MP 승인', G5_ADMIN_URL.'/approve_marketer.php', 'bbs_board'),
    array('600930', 'MP 포인트 수당', G5_ADMIN_URL.'/mp_soodang.php', 'bbs_board'),
   */
);
}else{
   /* $menu['menu650'] = array (
        array('650000', '조직도보기', ''.G5_ADMIN_URL.'/member_tree.php','allowance_sett'),
        array('650100', '조직도(트리) 보기', ''.G5_ADMIN_URL.'/member_tree.php', 'bbs_board'),
        array('650200', '조직도(박스) 보기', ''.G5_ADMIN_URL.'/member_org.php', 'bbs_board')
    );*/
}
?>