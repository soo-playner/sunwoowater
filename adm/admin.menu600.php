<?php
if($member['mb_id'] == 'admin'){
$menu['menu600'] = array (

    array('600000', '마케팅플랜', ''.G5_ADMIN_URL.'/bonus/bonus_list.php','bbs_board'),
    array('600100', '마케팅 수당 설정', G5_ADMIN_URL.'/bonus/bonus_config.php', 'bbs_board'),
    array('600200', '수당지급 및 지급내역', ''.G5_ADMIN_URL.'/bonus/bonus_list.php','bbs_board'),
    array('600500', '승급 및 내역', ''.G5_ADMIN_URL.'/bonus/member_upgrade.php','bbs_board'),
    array('600800', '조직 관리 및 직급보너스',''.G5_ADMIN_URL.'/bonus/bonus.level_org.php')
);
}else{
    $menu['menu600'] = array (
        array('600000', '마케팅플랜', ''.G5_ADMIN_URL.'/bonus/bonus_list.php','bbs_board'),
        array('600100', '마케팅 수당 설정', G5_ADMIN_URL.'/bonus/bonus_config.php', 'bbs_board'),
        array('600200', '수당지급 및 지급내역', ''.G5_ADMIN_URL.'/bonus/bonus_list.php','bbs_board'),
        array('600500', '승급 및 내역', ''.G5_ADMIN_URL.'/bonus/member_upgrade.php','bbs_board'),
        array('600800', '조직 관리 및 직급보너스',''.G5_ADMIN_URL.'/bonus/bonus.level_org.php')
    );
}
?>