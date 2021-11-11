<?php
$myLang = 'kor';

if($_COOKIE['myLang'])
{
	$myLang = $_COOKIE['myLang'];
}
?>

<style>
	#gnb_anguage{padding:10px}
	.gnb_bottom{background:#f5f5f5}
	.hidden{display:none;}
</style>

<script>
$(document).ready(function(){

	function setCookie(cookie_name, value, days) {
	  var exdate = new Date();
	  exdate.setDate(exdate.getDate() + days);
	  var cookie_value = escape(value) + ((days == null) ? '' : ';    expires=' + exdate.toUTCString());
	  document.cookie = cookie_name + '=' + cookie_value;
	}

	$.i18n.init({
		resGetPath: '/locales/my/__lng__.json',
		load: 'unspecific',
		fallbackLng: false,
		lng: 'kor'
	}, function (t){
		$('body').i18n();
	});

/* 사용안함 - google로 대체
	$('#lang').on('change', function(e) {
		$.i18n.setLng($(this).val(), function(){
			$('body').i18n();
		});
		console.log($(this).val());
		setCookie('myLang',$(this).val(),1,'/');
		//localStorage.setItem('myLang',$(this).val());
	});

	$('#lang').val("<?=$myLang?>").change(); */
});
</script>


<section id="wrapper" >
<header>
	<?if($menubar){?>
	<div class="menuback">
		<a href="javascript:history.back();" class='back_icon'><i class="ri-arrow-left-s-line"></i></a>
	</div>
	<?}else{?>
	<div class="menu">
		<a href="#" class='menu_icon'><i class="ri-menu-2-line"></i></a>
	</div>
	<?}?>
	
	<?if(!$menubar){?>
	<nav class="left_gnbWrap">
		
		<div class="gnb_logo_top">
			<a href="/" style='margin-left:12px;'><img src="<?=G5_THEME_URL?>/img/title.png" alt=""></a>
		</div>

		<a href="#" class="close"><img src="<?=G5_THEME_URL?>/img/gnb/close.png" alt="close"></a>
		<div class='user-content'>
			<ul class="user_wrap row">
				<li class="col-4">
					<p class='userid user_level' style='margin-left:10px;'><?=$user_icon?></p>
				</li>
				<li class="col-8 ">
					<h4 class="font_weight user_id"><?=$member['mb_id']?>님</h4>
				</li>
			</ul>
		</div>
		<div class="b_line3"></div>
		<ul class="left_gnb">
			<!-- <li class="dashboard_icon"><a href="/"><span>대쉬보드</span></a></li> -->
			

			<li class="profile_icon"><a href="/page.php?id=profile"><span>개인정보&보안설정</span></a></li>
			<li class="mywallet_icon"><a href="/page.php?id=mywallet"><span>입출금</span></a></li> 
			<li class="mining_icon"><a href="/page.php?id=mining"><span>마이닝</span></a></li>
			<li class="upstairs_icon"><a href="/page.php?id=upstairs"><span>패키지구매</span></a></li>
			<li class="bonus_history_icon"><a href="/page.php?id=bonus_history"><span>보너스내역</span></a></li>
			<?
				if($member['center_use'] == 1){?>
				<li class="center_page"><a href="/page.php?id=center_page"><i class="ri-team-line" style='font-size:26px;color:#435388'></i><span style='margin-left:22px;'>센터회원관리</span></a></li>
				<?}
			?>
			<li class="recommend_icon"><a href="/page.php?id=structure"><span>추천조직도</span></a></li>
			<li class="support_icon"><a href="/page.php?id=binary"><span>후원조직도</span></a></li>

			<li class="notice_icon"><a href="/page.php?id=news"><span>공지사항</span></a></li>
			<li class="question_icon"><a href="/page.php?id=support_center"><span>1:1문의사항</span></a></li>
			<li class="reffer_icon"><a href="/page.php?id=referral_link"><span>추천인링크</span></a></li>
			<!-- <li class="shopping_mall_icon"><a href="javascript:move_to_shop();"><span>쇼핑몰</span></a></li> -->
			<!-- <div class="b_line3"></div> -->
		</ul>	
		<div class='gnb_bottom text-center hidden'><i class="ri-arrow-down-s-line" style='font-size:20px;vertical-align:top'></i></div>
		<div id='gnb_language'>
			<p class='f_small title'>언어선택</p>
			<?include_once(G5_THEME_PATH.'/_include/lang.php')?>
		</div>
		
		<div class="logout_wrap">
			<a href="javascript:void(0);" class="logout_pop_open"><i class="ri-logout-box-r-line"></i><span>로그아웃</span></a>
			<a href="/page.php?id=member_term"><i class="ri-git-repository-line"></i><span>회원약관</span></a>
		</div>	
	</nav>
	<?}?>

	<div class="top_title">
		<!-- <div id="lang" style="float:right;z-index:100;position:absolute">
			<?//include_once(G5_THEME_PATH.'/_include/lang.php')?>
		</div> -->

		<h3>
			<a href="/"><img src= "<?=G5_THEME_URL?>/img/title.png" alt="logo"></a>
			<?if($member['mb_level'] >= 9){?><button type="button" class='btn adm_btn' onclick="location.href= '<?=G5_ADMIN_URL?>'" ><i class="ri-user-settings-line"></i>Admin</button><?}?>
		</h3>
		
	</div>

	<!-- <div class="lang_selection user-drop-down-section">
		<div class="lang-sel">
			<select class="custom-select" id="lang">
				<option value="kor" selected>언어선택</option>
				<option value="kor">한국어</option>
				<option value="eng" selected>English</option>
				<option value="chn">中文</option>
			</select>
		</div>
	</div> -->
	
</header>

<div id="loading" class="wrap-loading display-none"><img id="loading_img" src="/img/Spinner-1.5s-267px.gif" /></div>


<script>
	$( document ).ajaxStart(function() { 
		$('.wrap-loading').removeClass('display-none');
	});


	$( document ).ajaxStop(function() { 
		$('.wrap-loading').addClass('display-none');
	});

	$(function(){
		var left_gnb = $('.left_gnb');
		if(left_gnb.height() < 400){
			$(".gnb_bottom").css('display','block');

			$(left_gnb).scroll(function () {
				var gnb_height = $(left_gnb).scrollTop();
				
				if(gnb_height > 30){
					$(".gnb_bottom i").attr('class','ri-arrow-up-s-line')
				}else if(gnb_height < 30){
					$(".gnb_bottom i").attr('class','ri-arrow-down-s-line')
				}
			}); 
		}
	});

	

	function move_to_shop(){
		<?php if(strpos($_SERVER['HTTP_USER_AGENT'],'webview//1.0') == true){ ?>
			App.moveToShop()
		<?php }else{?>

			var shop_url = "<?=SHOP_URL?>";
			var form = document.createElement("form");

				form.setAttribute("charset", "UTF-8");
				form.setAttribute("method", "Post");  //Post 방식
				form.setAttribute("action", "<?=SHOP_URL?>"); //요청 보낼 주소

				var hiddenField = document.createElement("input");
				hiddenField.setAttribute("type", "hidden");
				hiddenField.setAttribute("name", "mb_id");
				hiddenField.setAttribute("value", "<?=$member['mb_id']?>");
				form.appendChild(hiddenField);

				hiddenField = document.createElement("input");
				hiddenField.setAttribute("type", "hidden");
				hiddenField.setAttribute("name", "mb_password");
				hiddenField.setAttribute("value", "<?=$member['mb_password']?>");
				form.appendChild(hiddenField);

				hiddenField = document.createElement("input");
				hiddenField.setAttribute("type", "hidden");
				hiddenField.setAttribute("name", "gnu_to_mall");
				hiddenField.setAttribute("value", "gnu_to_mall");
				form.appendChild(hiddenField);

				document.body.appendChild(form);
				form.submit();
		<?php } ?>
	}
</script>