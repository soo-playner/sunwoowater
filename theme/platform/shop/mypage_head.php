<?

if($member['mb_level']==10){
	$member_lvimg='0star.png';
	$member_lvString="S Star";
}
else if($member['mb_level']==9){
	$member_lvimg='0star.png';
	$member_lvString="Manager";
}
else if($member['mb_level']==8){
	$member_lvimg='6star.png';
	$member_lvString="6 Star";
}
else if($member['mb_level']==7){
	$member_lvimg='5star.png';
	$member_lvString="5 Star";
}
else if($member['mb_level']==6){
	$member_lvimg='4star.png';
	$member_lvString="4 Star";
}
else if($member['mb_level']==5){
	$member_lvimg='3star.png';
	$member_lvString="3 Star";
}
else if($member['mb_level']==4){
	$member_lvimg='2star.png';
	$member_lvString="2 Star";
}
else if($member['mb_level']==3){
	$member_lvimg='1star.png';
	$member_lvString="1 Star";
}
else if($member['mb_level']==2){
	$member_lvimg='0star.png';
	$member_lvString="0 Star";
}
else if($member['mb_level']==1){
	$member_lvimg='0star.png';
	$member_lvString="Miner";
}
else{
	$member_lvimg='0star.png';
	$member_lvString="fresh Man";

}

	$sql_coin = "select * from coin_cost";
	$coin_dollor = sql_fetch($sql_coin);
	$qa_sql = "select count(*) as cnt from g5_qa_content where mb_id='{$member['mb_id']}' and qa_1='N';";
	$row = sql_fetch($qa_sql);
	if($row)
	$total_count = $row['cnt'];
	else
	$total_count = 0;
	//echo "membership_yn".$member['membership_yn'];
	$item  = array(1 => $member['it_pool1'], $member['it_pool2'], $member['it_pool3'],$member['it_pool4'],$member['it_GPU']);
	$item_price = array(1 => 3400, 10200, 17000, 40800, 80);
	
	$total_mining_sudang = $member['it_pool1_profit']+$member['it_pool2_profit']+$member['it_pool3_profit']+$member['it_pool4_profit'];
	$eth_wallet = $member['it_pool5_profit'];
	$btc_wallet = $total_mining_sudang+$member[mb_balance];
	
	$sql = "SELECT wr_subject, wr_id FROM g5_write_notice order by wr_id desc";
	$notice = sql_fetch($sql);
	$subject = $notice['wr_subject'];

	$mbid = $member['mb_id'];
	$sql = "select count(mb_recommend) as enroll from g5_member where mb_recommend='$mbid'";
	//echo $sql;
	$ret = sql_fetch($sql);
	$enroll = sql_fetch_array($ret);
	$cont = $ret['enroll'];

	$placement = $member['mb_brecommend'];
?>

	<div class="top-nav-one">
			<i class="fas fa-bars" onclick="toggleSideMenu()"></i>					
			<a href="#">
				<div class="logo-container">
					<img src="./images/logo.png" alt="pinnacle logo" class="top-logo-icon"><span class="top-logo-text">Pinnacle Mining</span>
				</div>
			</a>

			<div class="user-drop-down-section">				

				<div class="lang-sel">
					<select class="custom-select">
					  <option selected="Eng">English</option>
				  <option value="kor">한국어</option>
				  <option value="jap">日本語</option>
				  <option value="chin">中文</option>
				  <option value="viet">Tiếng Việt</option>
					</select>
		  	</div>

		  	<div class="dropdown">
				  <button class="btn dropdown-toggle" type="button" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
				    <i class="fas fa-bell"></i>
				  </button>
				  <div class="dropdown-menu dropdown-menu-right shadow" aria-labelledby="dropdownMenuButton">
				    <a class="dropdown-item" href="#">New Promotion!</a>
				  </div>
				</div>

		  	<div class="dropdown">
				  <button class="btn dropdown-toggle" type="button" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
				    <img src="./images/<?echo $member_lvimg;?>" class="user-dropdown-rank" alt="<?echo $member_lvString?>">
				  </button>
				  <div class="dropdown-menu dropdown-menu-right shadow" aria-labelledby="dropdownMenuButton">
				  	<span class="username"><?echo $member['mb_id']?></span>
				  	<div class="dropdown-divider"></div>
					    <a class="dropdown-item" href="./manage_profile.php">Manage Profile</a>
					    <a class="dropdown-item" href="./order_history.php">Order History</a>
					    <a class="dropdown-item" href="./support_center.php">Support Center</a>
						<div class="dropdown-divider"></div>
					    <a class="dropdown-item" href="/bbs/logout.php">Sign Out</a>
				  </div>
				</div>
			</div>
		</div>
	  		

		<div class="top-nav-two">
			<div class="payout-countdown nav-two-line-height">
					<span class="nav-two-big blue clock" data-timezone="America/New_York">18 : 29 : 31</span> <br>
					<span class="gray">Next Payout</span>
			</div>

			<div class="price-and-wallets">
				<div class="btc-price">
					<span class="gray">Bitcoin Price</span> <br>
					<span class="nav-two-big nav-two-line-height blue">$<?=$coin_dollor['btc_cost']?></span>
				</div>

				<div class="pinnacle-wallet">
					<span class="gray">BTC Wallet</span> <br>
					<img src="./images/btc_logo.png" class="nav-two-btc-logo" alt="btc logo"> <span class="nav-two-big nav-two-line-height"><?=$btc_wallet?> BTC</span>
				</div>

				<div class="mining-wallet">
					<span class="gray">ETH Wallet</span> <br>&nbsp;
					<img src="./images/eth_logo.png" class="nav-two-eth-logo" alt="btc logo"> <span class="nav-two-big nav-two-line-height"><?=$eth_wallet?>ETH</span>
				</div>
			</div>

				<!-- MAX-WIDTH: 880PX VIEWPORTS -->
				<div class="dropdown mw-880 countdown-dropdown">
				  <button class="btn dropdown-toggle" type="button" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
				    <img src="./images/countdown_clock.png" class="mw-880-img" alt="countdown clock">
				  </button>
				  <div class="dropdown-menu dropdown-menu shadow payout-countdown-dropdown" aria-labelledby="dropdownMenuButton">
				  	<span class="username next-payout">Next Payout</span>
				  	<div class="dropdown-divider"></div>
				    <span class="nav-two-big blue clock" data-timezone="America/New_York">12 : 34 : 56</span>
				  </div>
				</div>

				<div class="dropdown mw-880 btc-usd-dropdown">
				  <button class="btn dropdown-toggle" type="button" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
				    <img src="./images/btc_usd.png" alt="btc usd price">
				  </button>
				  <div class="dropdown-menu dropdown-menu shadow payout-countdown-dropdown" aria-labelledby="dropdownMenuButton">
				  	<span class="username next-payout">Bitcoin Price</span>
				  	<div class="dropdown-divider"></div>
				    <span class="username blue dropdown-info">$<?=$coin_dollor['btc_cost']?></span>
				  </div>
				</div>

				<div class="dropdown mw-880 pinnacle-wallet-dropdown">
				  <button class="btn dropdown-toggle" type="button" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
				    <img src="./images/pinnacle_wallet.png" alt="pinnacle wallet">
				  </button>
				  <div class="dropdown-menu dropdown-menu shadow payout-countdown-dropdown wallet-resize" aria-labelledby="dropdownMenuButton">
				  	<span class="username next-payout">BTC Wallet</span>
				  	<div class="dropdown-divider"></div>
				    <span class="username blue dropdown-info"><?=$btc_wallet?> BTC </span>
				  </div>
				</div>

				<div class="dropdown mw-880 mining-wallet-dropdown">
				  <button class="btn dropdown-toggle" type="button" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
				    <img src="./images/mining_wallet.png" alt="pinnacle wallet">
				  </button>
				  <div class="dropdown-menu dropdown-menu shadow payout-countdown-dropdown wallet-resize" aria-labelledby="dropdownMenuButton">
				  	<span class="username next-payout">ETH Wallet</span>
				  	<div class="dropdown-divider"></div>

				    <span class="username blue dropdown-info"><?=$eth_wallet?>ETH</span>
				  </div>
				</div>
			</div>

			<div id="side-menu" class="side-nav">
				<ul class="link_list">
			    <a href="dashboard.php">
			    	<li class="side-nav-link">
			      	<i class="fas fa-home"></i> Dashboard 
			    	</li>
			    </a>

			    
		    	<li class="side-nav-link menu-dropdown">
		      	<i class="fas fa-address-card"></i> Membership
		    	</li>
			    
		      	<ul class="sub-link-list">
			      	<a href="manage_profile.php">
			      		<li class="side-nav-sub-link">
			      			<i class="fas fa-user"></i> Manage Profile
			      		</li>
			      	</a>
			      	<a href="automatic_upgrade.php">
			      		<li class="side-nav-sub-link">
			      			<i class="fas fa-sort-amount-up"></i> Automatic Upgrade
			      		</li>
			      	</a>
			      	<a href="earnings_report.php">
			      		<li class="side-nav-sub-link">
			      			<i class="fas fa-file-invoice-dollar"></i> Earnings Report
			      		</li>
			      	</a>
			      	<a href="order_history.php">
			      		<li class="side-nav-sub-link">
			      			<i class="fas fa-history"></i> Order History
			      		</li>
							</a>
							<a href="order_history_eth.php">
			      		<!--li class="side-nav-sub-link">
			      			<i class="fas fa-history"></i> Order History - eth
			      		</li-->
			      	</a>
			      </ul>

			    <a href="bonus_earnings.php">
			    	<li class="side-nav-link">
				      <i class="fas fa-coins"></i> Bonus Earnings
				    </li>
				  </a>

				  <a href="hash_power.php">
			    	<li class="side-nav-link">
				      <i class="fas fa-bolt"></i> Hash Power
				    </li>
				  </a>
		    
		    	<li class="side-nav-link menu-dropdown">
			      <i class="fas fa-handshake"></i> Team Information
			    </li>
			  
					<ul class="sub-link-list">
						<a href="level_structure.php">
							<li class="side-nav-sub-link">
								<i class="fas fa-sitemap"></i> Level Structure
							</li>
						</a>
						<a href="binary_tree.php">
							<li class="side-nav-sub-link">
								<i class="fas fa-tree"></i> Binary Tree
							</li>
						</a>
					</ul>

					<li class="side-nav-link menu-dropdown">
						<i class="fas fa-wallet"></i> Crypto Wallets
					</li>

					<ul class="sub-link-list">
						<a href="crypto_wallet.php">
							<li class="side-nav-sub-link">
								<i class="fas fa-wallet" style="margin-left: 56px !important;"></i>crypto wallets
							</li>
						</a>
						<a href="crypto_wallet_eth.php">
							<li class="side-nav-sub-link">
								<i class="fas fa-wallet" style="margin-left: 56px !important;"></i>crypto wallets - eth
							</li>
						</a>
					</ul>

			    <li class="side-nav-link menu-dropdown">
			      <i class="fas fa-shopping-cart"></i> Purchase Hash
			    </li>

			    	<ul class="sub-link-list">
			      	<a href="purchase_hash_full.php">
			      		<li class="side-nav-sub-link">
			      			<i class="fas fa-star"></i> Buy Full Hash
			      		</li>
			      	</a>
			      	<a href="purchase_hash_partial.php">
			      		<li class="side-nav-sub-link">
			      			<i class="fas fa-star-half-alt"></i> Partial
			      		</li>
			      	</a>
			      </ul>

			    <li class="side-nav-link menu-dropdown" >
			      <i class="fas fa-user-friends"></i> Referral Link
			    </li>

			    	<ul class="sub-link-list">
			      	<li class="side-nav-sub-link" >
			      		<i class="fas fa-clone"></i> 
								<input type="text" id="url" onclick="copyURL()" style="width: 100px;border:none;" placeholder="click"/>
			      	</li>
			      </ul>

			    <br>
				<?//if($member['mb_level']>4){?>
			    <!--a href="mp_dashboard.php">
			    	<li class="side-nav-link">
				      <i class="fas fa-pen-alt"></i> MP Dashboard
				    </li>
		    	</a-->
				<?//}?>
			    <a href="bonus_plan.php">
			    	<li class="side-nav-link">
				      <i class="fas fa-hand-holding-usd"></i> Bonus Plan
				    </li>
		    	</a>

			    <a href="pinnacle_news.php">
			    	<li class="side-nav-link">
			    	  <i class="fas fa-bullhorn"></i> Pinnacle News
			    	</li>
			    </a>

			   	<a href="faq.php">
				    <li class="side-nav-link">
				      <i class="fas fa-question"></i> FAQs
				    </li>
				  </a>

				  <a href="support_center.php">
				    <li class="side-nav-link">
				      <i class="fas fa-life-ring"></i> Support Center
				    </li>
				  </a>

			  </ul>
			</div>

<?
	$sql_coin = "select * from coin_cost";
	$row = sql_fetch($sql_coin);
?>
		<div class="inner counter after" style="display:none;">
		
			<div class="flot-left p-counter">
				<b>Payout Counter : <span class="clock" data-timezone="America/New_York" style="width: 120px;text-align: right;">18 : 29 : 31</span></b> 
			</div>
			
			<div class="flot-right mw">
				
				<span>1 BTC : $ <?=$row['btc_cost']?> </span> &nbsp;&nbsp;&nbsp;&nbsp;
			<?if($member['mb_level']!=10){?>
				<span class="ms-box">Message : <img src="img/adm/message_icon.png" alt="" /> <b><?=$total_count?></b></span>
			<?}?>
				<ul>
					<li class="pinnacle">Pinnacle Wallet : <span><?=$member['mb_balance'];?> BTC </span></li>
					<li>Mining Wallet : <span> <?=$total_mining_sudang?> BTC   &nbsp;&nbsp;&nbsp; <?=$total_mining_sudang2?> ETH </span></li>
				</ul>
			</div>
		</div><!-- inner -->


<script src="js/moment.js"></script>
<script src="js/moment-timezone.js"></script>
<script src="js/moment-timezone-with-data-2012-2022.js"></script>
<script>
	var clocks = document.getElementsByClassName("clock");
		
	var dt= moment().tz("America/Los_Angeles").hours(24).minutes(0).second(0);

	var countDownDate = dt.valueOf();

	function updateClocks() {
		var now = moment().tz("America/Los_Angeles").valueOf();
		var distance = countDownDate - now;

		var hours = Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
		var minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
		var seconds = Math.floor((distance % (1000 * 60)) / 1000);

		clocks[0].textContent = hours + " : "+ minutes + " : " + seconds;
		clocks[1].textContent = hours + " : "+ minutes + " : " + seconds;
		if (distance < 0) {
			clearInterval(x);
			clocks[0].textContent = "EXPIRED";
			clocks[1].textContent = "EXPIRED";
		}
	}

	// Update every minute:
	var x = setInterval(updateClocks, 1000);
	updateClocks();

	function recommendRegister(){
		var specs = "left=10,top=10,width=500,height=800";
		specs += ",toolbar=no,menubar=no,status=no,scrollbars=no,resizable=no";
		window.open("/shop/recommend_register.php?now_id=<?=$member['mb_id']?>", "recommend_register", specs);
	}

	var menu_dropdown = document.getElementsByClassName('menu-dropdown');

	for (var i = 0; i < menu_dropdown.length; i++) {
		menu_dropdown[i].onclick = function() {
			this.classList.toggle('is_open');

			var menu_item = this.nextElementSibling;

			if (menu_item.style.maxHeight) {
				menu_item.style.maxHeight = null;
			} else {
				menu_item.style.maxHeight = menu_item.scrollHeight + "px";
			}
		}
	}

jQuery(document).ready(function($) {
  var alterClass = function() {
    var ww = document.body.clientWidth;
    if (ww >= 1000) {
      $('#side-menu').addClass('side-menu-open');
      $('#body-wrapper').addClass('nav-body-shift');
    } else if (ww < 1000) {
    	$('#side-menu').removeClass('side-menu-open');
    	$('#body-wrapper').removeClass('nav-body-shift');
    }
  };
  alterClass();
});

function toggleSideMenu() {
	document.getElementById('side-menu').classList.toggle('side-menu-open');
	document.getElementById('side-menu').classList.toggle('shadow');

	if (document.body.clientWidth >= 1000) {
		document.getElementById('body-wrapper').classList.toggle('nav-body-shift');		
	}
}

</script>
	<script>
		var token = <? echo $result;?>;
		function copyURL(){
			//$('#url').val("http://www.pinnaclemining.net/bbs/register_form.php?mb_recommend=<? echo $member['mb_id'];?>").show();
			$('#url').val(token.result.url).show();
			document.getElementById("url").select();
			document.execCommand("copy");
		}
	</script>