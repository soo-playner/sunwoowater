<?
include_once('./_common.php');
include_once(G5_THEME_PATH . '/_include/wallet.php');
include_once(G5_THEME_PATH . '/_include/gnb.php');

// 지갑자동입금설정
include_once(G5_LIB_PATH . '/blocksdk.lib.php');
include_once(G5_LIB_PATH . '/crypto.lib.php');

$title = 'Mywallet';


// 입금설정
$deposit_setting = wallet_config('deposit');
$deposit_fee = $deposit_setting['fee'];
$deposit_min_limit = $deposit_setting['amt_minimum'];
$deposit_max_limit = $deposit_setting['amt_maximum'];
$deposit_day_limit = $deposit_setting['day_limit'];

// 출금설정
$withdrwal_setting = wallet_config('withdrawal');
$withdrwal_fee = $withdrwal_setting['fee'];
$withdrwal_min_limit = $withdrwal_setting['amt_minimum'];
$withdrwal_max_limit = $withdrwal_setting['amt_maximum'];
$withdrwal_day_limit = $withdrwal_setting['day_limit'];

// 수수료제외 실제 출금가능금액
$withdrwal_total = floor($total_withraw / (1 + $withdrwal_fee * 0.01));
if ($withdrwal_max_limit != 0 && ($total_withraw * $withdrwal_max_limit * 0.01) < $withdrwal_total) {
  $withdrwal_total = $total_withraw * ($withdrwal_max_limit * 0.01); // 476.19047619048 = 500 * (0 * 0.01)
}

// if ($withdrwal_max_limit > 1 && ($total_withraw * $withdrwal_max_limit * 0.01) < $withdrwal_total) {
//   $withdrwal_total = $total_withraw * ($withdrwal_max_limit * 0.01);
// }else{
//   $withdrwal_total = 0;
// }

//계좌정보
$bank_setting = wallet_config('bank_account');
$bank_name = $bank_setting['bank_name'];
$bank_account = $bank_setting['bank_account'];
$account_name = $bank_setting['account_name'];

//시세 업데이트 시간
// $next_rate_time = next_exchange_rate_time();

//보너스/예치금 퍼센트
// $bonus_per = bonus_state($member['mb_id']);


// 패키지 선택하고 들어왔으면 입금할 가격표시
if ($_GET['sel_price']) {
  $sel_price = $_GET['sel_price'];
}


// 입금 OR 출금
if ($_GET['view'] == 'withdraw') {

  $view = 'withdraw';
  $history_target = $g5['withdrawal'];
} else {
  $view = 'deposit';
  $history_target = $g5['deposit'];
}


// 입금방법 
$deposit_won = array_search('원', $deposit_method); 
//$deposit_coin = array_search($wallet_code[0], $deposit_method);


// ETH 입금 전용 지갑 생성
// if (USE_WALLET && $wallet_code[0] == 'eth') {

//   $callback = G5_URL . "/plugin/blocksdk/point-callback.php";
//   $blocksdk_conf = Crypto::GetConfig();

//   $member_wallet_target = $wallet_code[0] . '_wallet';
//   $member_wallet_target_id = $wallet_code[0] . '_wallet_id';
//   $create_blocksdk_target = 'de_' . $wallet_code[0] . '_use';


//   if (empty($member[$member_wallet_target]) == true && $blocksdk_conf[$create_blocksdk_target] == 1) {

//     $address = Crypto::GetClient("eth")->createAddress([
//       "name" => $wallet_create_code . "_mb_" . $member['mb_no']
//     ]);

//     Crypto::CreateWebHook($callback, $wallet_code[0], $address['address']);

//     // $update_sql .= empty($update_sql) ? "" : ","; 
//     $update_sql = $member_wallet_target . " ='{$address['address']}' ";
//     $update_sql .= ',' . $member_wallet_target_id . " ='{$address['id']}' ";

//     $member[$member_wallet_target] = $address['address'];

//     $sql = "
//     insert into 
//     blocksdk_member_key (id, address, private_key) 
//     values ('{$address['id']}', '{$address['address']}','{$address['private_key']}')
//     ";
//     sql_fetch($sql);

//     if (empty($update_sql) == false) {
//       $sql = "UPDATE {$g5['member_table']} SET {$update_sql} WHERE mb_no={$member['mb_no']}";
//       sql_query($sql);
//     }
//   }

//   $wallet_sql = "SELECT private_key FROM blocksdk_member_key WHERE address = '{$member[$member_wallet_target]}'";
//   $wallet_row = sql_fetch($wallet_sql);

//   $my_wallet = $member[$member_wallet_target];
//   $private_key = $wallet_row['private_key'];
//   $mb_id = $member['mb_id'];


//   if ($member['key_download'] == "0") {
//     include_once(G5_LIB_PATH . "/download_key/set_private_key.php");
//   }

//   if ($member['key_download'] == "1") {
//     include_once(G5_LIB_PATH . "/download_key/get_private_key.php");
//   }
// }

/*날짜계산*/
$qstr = "stx=" . $stx . "&fr_date=" . $fr_date . "&amp;to_date=" . $to_date;
$query_string = $qstr ? '?' . $qstr : '';

$fr_date = date("Y-m-d", strtotime(date("Y-m-d") . "-1 day"));
$to_date = date("Y-m-d", strtotime(date("Y-m-d") . "+1 day"));

$sql_search_deposit = " WHERE mb_id = '{$member['mb_id']}' ";
$sql_search_deposit .= " AND create_dt between '{$fr_date}' and '{$to_date}' ";

$rows = 15; //한페이지 목록수


//입금내역
$sql_common_deposit = "FROM {$g5['deposit']}";

$sql_deposit = " select count(*) as cnt {$sql_common_deposit} {$sql_search_deposit} ";
$row_deposit = sql_fetch($sql_deposit);

$total_count_deposit = $row_deposit['cnt'];
$total_page_deposit  = ceil($total_count_deposit / $rows);

if ($page < 1) $page = 1; // 페이지가 없으면 첫 페이지
$from_record_deposit = ($page - 1) * $rows; // 시작 열

$sql_deposit = " select * {$sql_common_deposit} {$sql_search_deposit} order by create_dt desc limit {$from_record_deposit}, {$rows} ";
$result_deposit = sql_query($sql_deposit);



//출금내역
$sql_common = "FROM {$g5['withdrawal']}";
// $sql_common ="FROM wallet_withdrawal_request";
$WITHDRAW_CURENCY = WITHDRAW_CURENCY;
$sql_search = " WHERE mb_id = '{$member['mb_id']}' and coin = '{$WITHDRAW_CURENCY}' ";
// $sql_search .= " AND create_dt between '{$fr_date}' and '{$to_date}' ";

$sql = " select count(*) as cnt {$sql_common} {$sql_search} ";
if ($debug) echo "<code>" . $sql . "</code>";

$row = sql_fetch($sql);
$total_count = $row['cnt'];

$total_page  = ceil($total_count / $rows);
if ($page < 1) $page = 1; // 페이지가 없으면 첫 페이지
$from_record = ($page - 1) * $rows; // 시작 열

$sql = " select * {$sql_common} {$sql_search} order by create_dt desc limit {$from_record}, {$rows} ";
$result_withdraw = sql_query($sql);


/** 코인가격 */
if ($sel_price > 0) {
  $coin_val = $fil_price / $sel_price;
  $coin_price = floor($coin_val * 10000) / 10000;
  $deposit_coin_price = $coin_price * 1.005;
}

?>


<link rel="stylesheet" href="<?= G5_THEME_URL ?>/css/scss/page/mywallet.css">
<!-- <script type="text/javascript" src="./js/qrcode.js"></script> -->

<? include_once(G5_THEME_PATH . '/_include/breadcrumb.php'); ?>

<style>
  .title_btn {
    display: inline-block;
    width: 50%;
    float: left;
    background: #fff;
    padding: 20px 0 20px;
    color: #3b86ff;
    font-weight: 600;
    border: 1px solid #fff;
    border-bottom: 1px solid #e1e1e1;
    cursor: pointer;
  }

  .title_btn.single {
    width: 100%;
    border-top-left-radius: 10px;
    border-top-right-radius: 10px;
    box-shadow: none;
  }

  .title_btn.left {
    border-top-left-radius: 10px;
  }

  .title_btn.left.active {
    box-shadow: inset -1px 0px 0px rgb(0 0 0 / 25%);
  }

  .title_btn.right {
    border-top-right-radius: 10px;
  }

  .title_btn.right.active {
    box-shadow: inset 2px 0px 0px rgb(0 0 0 / 25%)
  }

  .title_btn i {
    vertical-align: middle;
    font-size: 24px;
    padding-right: 5px;
    font-weight: 300;
  }

  .title_btn.active {
    color: #29386b;
    background: #f5f5f5;
    border: 1px solid #e1e1e1;
    border-left: none;
  }

  .title_btn.single.active {
    background: white;
  }



  .mywallet .loadable .bank_info {
    box-shadow: none;
    margin-top: 1px;
  }

  .content-box.catehead {
    padding: 0 15px 15px
  }

  .select_head {
    margin: 0 -15px -12px;
    text-align: left;
  }

  .deposit_stage {
    display: none;
    animation: slidein 0.5s ease-out;
    -webkit-animation: slidein 0.5s ease-out
  }

  .deposit_stage.active {
    display: block;
  }

  .bank_info {
    min-height: 100px;
    display: inline-grid
  }

  .wallet_address {
    color: #492191;
    font-size: 15px;
    word-break: break-all;
  }

  .deposit_requests {
    display: none;
  }

  .deposit_requests.active {
    display: block;
  }

  .wallet_address_qr img {
    max-width: max-content;
    max-height: max-content;
  }

  .tx_hash {
    width: 100%;
    padding: 5px;
    height: 60px;
    font-size: 14px;
    resize: none;
    font-weight: 600;
  }

  .tx_hash::placeholder {
    font-weight: 500;
    font-size: 13px;
    color: #767676;
    letter-spacing: -0.25px;
  }
</style>



<main>
  <div class='container mywallet'>

    <div class="my_btn_wrap">
      <div class="row mywallet_btn">
        <div class='col-lg-6 col-12'>
          <button type='button' class='btn wd main_btn b_darkblue round' onclick="switch_func('deposit')"> 입금</button>
        </div>
        <div class='col-lg-6 col-12'>
          <button type='button' class='btn wd main_btn b_skyblue round' onclick="switch_func('withdraw')">출금</button>
        </div>
      </div>
    </div>


    <!-- 입금 -->
    <section id='deposit' class='loadable'>
      <div class="content-box round">
        <h3 class="wallet_title" data-i18n="deposit.입금계좌">Deposit Account</h3>

        <div class="row ">
          <div class='col-12 text-center bank_info'>
            <?= $bank_name ?> : <input type="text" id="bank_account" class="bank_account" value="<?= $bank_account ?>" title='bank_account' disabled />(<?= $account_name ?>)
            <?if ($sel_price) { ?>
              <div class='sel_price'>입금액 : <span class='price'><?= Number_format($sel_price) ?><?= ASSETS_CURENCY ?></span></div>
            <?}?>
          </div>
        </div>

          <div class='col-12'>
            <button class="btn wd line_btn " id="accountCopy" onclick="copyURL('#bank_account')">
              <span data-i18n="deposit.계좌복사"> Copy Address </span>
            </button>
          </div>
        </div>


      <!-- <div class="content-box catehead"> -->
        <!-- 선택버튼 -->
        <!-- <div class="select_head" style='text-align:center'>
          <? if (count($deposit_method) > 1) { ?>
            <div class='title_btn left active' data-category="won"><i class="ri-bank-line"></i>원화 입금 계좌 </div>
            <div class='title_btn right' data-category="coin"><i class="ri-wallet-3-line"></i>코인 입금 주소</div>
          <? } else {
            if ($deposit_won > -1) {
              echo "<div class='title_btn single active' data-category='won'><i class='ri-bank-line'></i>원화 입금 계좌 </div>";
            }
            if ($deposit_coin > -1) {
              echo "<div class='title_btn single active' data-category='coin'><i class='ri-wallet-3-line'></i>" . coin_prices($wallet_code[0], 'name') . " 입금 주소 </div>";
            }
          ?>
          <? } ?>
        </div> -->

        <!-- <section id='won' class='deposit_stage'>
          <div class="row ">
            <div class='col-12 text-center bank_info'>
              <?= $bank_name ?> : <input type="text" id="bank_account" class="bank_account" value="<?= $bank_account ?>" title='bank_account' disabled />(<?= $account_name ?>)
              <? if ($sel_price) { ?>
                <div class='sel_price'>
                  입금액 : <span class='price'><?= ASSETS_CURENCY ?><?= Number_format($sel_price) ?></span><br>
                  <span class='small'>( =￦<?= Shift_auto($sel_price * $usd_price) ?><?= DEPOSIT_CURENCY ?> )</span>
                </div>
              <? } ?>
            </div>
          </div>
          <div class='col-12'>
            <button class="btn wd line_btn " id="accountCopy" onclick="copyURL('#bank_account')">
              <span> 계좌번호 복사 </span>
            </button>
          </div>
        </section>

        <section id='eth' class='deposit_stage'>
          <div class="row ">
            <div class='col-12 text-center bank_info'>
              <div class="row" style='padding:15px;'>

                <div class="col-4 wallet_address_qr" id="wallet_address_qr"></div>

                <div class="col-8 text-left">
                  <span class='desc' style='line-height:26px;color:#777;margin:0;font-size:14px;'><?= coin_prices($wallet_code[0], 'name') ?> 입금주소 :</span>
                    <p class='wallet_address' id='wallet_address'><?= $my_wallet ?></p>
                </div>
              </div>
              <? if ($sel_price) { ?>
                <div class='sel_price '>
                  입금액 : <span class='price'><?= ASSETS_CURENCY ?><?= Number_format($sel_price) ?></span><br>
                  <span class='small'>( = <?= shift_auto_zero($deposit_coin_price, $wallet_code[0]) ?> <?= strtoupper($wallet_code[0]) ?> )</span>
                </div>
              <? } ?>
            </div>
          </div>

          <div class='col-12'>
            <button class="btn wd line_btn " id="AdressCopy" onclick="copyADREESS('#wallet_address')">
              <span> 지갑주소 복사 </span>
            </button>
          </div>
        </section> -->


        <!-- 코인 입금 시작 -->
        <!-- <section id='coin' class='deposit_stage active'>
          <div class="row ">
            <div class='col-12 text-center bank_info'>
              <div class="row" style='padding:15px;'>

                <div class="col-4 wallet_address_qr" id="deposit_wallet_address_qr"></div>

                <div class="col-8 text-left">
                  <span class='desc' style='line-height:26px;color:#777;margin:0;font-size:14px;'><?= coin_prices($wallet_code[0], 'name') ?> 입금주소 :</span>
                  <p class='wallet_address' id='deposit_wallet_address'><?= $deposit_wallet_address ?></p>
                </div>
              </div>
              <? if ($sel_price) { ?>
                <div class='sel_price '>
                  입금액 : <span class='price'><?= ASSETS_CURENCY ?><?= Number_format($sel_price) ?></span><br>
                  <span class='small'>( = <?= shift_auto_zero($deposit_coin_price, $wallet_code[0]) ?> <?= strtoupper($wallet_code[0]) ?> )</span>
                </div>
              <? } ?>
            </div>
          </div>

          <div class='col-12'>
            <button class="btn wd line_btn " id="AdressCopy" onclick="copyADREESS('#deposit_wallet_address')">
              <span> 지갑주소 복사 </span>
            </button>
          </div>
        </section>-->
      <!-- 코인 입금 끝 -->
      <!-- </div>  -->



      <div class="col-sm-12 col-12 content-box round mt20">
        <!-- 원화 입금요청 시작 -->
        <section id="deposit_request_won" class="deposit_requests active">
          <h3 class="wallet_title">입금확인요청 </h3> <span class='desc'> - 계좌입금후 1회만 요청해주세요</span>
          <div class="row">

            <div class="col-12 btn_ly qrBox_right "></div>
            <div class="col-12 withdraw mt20">
              <input type="text" id="deposit_name" class='b_ghostwhite p15 ' placeholder="입금자명을 입력해주세요">
              <input type="text" id="deposit_value" class='b_ghostwhite p15 cabinet' placeholder="입금액(숫자만)을 입력해주세요" inputmode="numeric">
              <span class='cabinet_inner'>※입금하신 원화금액을 입력해주세요</span>
              <span class='cabinet_outer'></span>
              <label class='currency-right2'><?= DEPOSIT_CURENCY ?></label>

              <!-- <input type='button' class='btn input_right_btn' value='$'> -->
              <!-- <input type='button' class='btn input_right_btn' value='원'> -->

            </div>

            <div class='col-sm-12 col-12 mt20'>
              <button class="btn btn_wd font_white deposit_request" id="deposit_request_btn" data-currency="원">
                <span>입금확인요청</span>
              </button>
            </div>
          </div>
        </section>
        <!-- 원화 입금요청 끝 -->

        <!-- <section id="deposit_request_eth" class="deposit_requests">
          <h3 class="wallet_title" style="margin:5px;font-size:12px;">※ 입금 전용 <?= $wallet_code[0] ?>주소로 코인입금시 자동 처리됩니다. </h3>
        </section> -->

        <!-- 코인 입금요청 시작 -->
        <!-- <section id="deposit_request_coin" class="deposit_requests  active">
          <h3 class="wallet_title">입금확인요청 </h3> <span class='desc'> - 전송 건당 1회만 요청해주세요</span>
          <div class="row">

            <div class="col-12 btn_ly qrBox_right "></div>
            <div class="col-12 withdraw mt20"> -->
              <!-- <input type="text" id="deposit_name" class='b_ghostwhite p15 ' placeholder="전송 처리된 TX HASH 코드를 입력해주세요"> -->
              <!-- <span style="font-size:12px;">Transaction Hash code:</span>
              <textarea name='tx_hash' id="tx_hash" class='b_ghostwhite p15 tx_hash' placeholder="전송 처리된 Transaction(Message ID) 코드를 정확히 입력해주세요"></textarea>

            </div>

            <div class='col-sm-12 col-12 mt20'>
              <button class="btn btn_wd font_white deposit_request" id="deposit_request_coin_btn" data-currency="<?= $deposit_method[0] ?>">
                <span>입금확인요청</span>
              </button>
            </div>
          </div>
        </section> -->
        <!-- 코인 입금요청 끝 -->

      </div>

      <style>
        /* .w70{width:70%;} */
        .input_right_btn {
          width: 45px;
          height: 45px;
        }
      </style>

      <!-- 입금 요청 내역 -->
      <div class="history_box content-box mt40">
        <h3 class="hist_tit" data-i18n="deposit.입금 내역">입금 내역</h3>
        <div class="b_line2"></div>
        <? if (sql_num_rows($result_deposit) == 0) { ?>
          <div class="no_data"> 입금내역이 존재하지 않습니다.</div>
        <? } ?>

        <? while ($row = sql_fetch_array($result_deposit)) { ?>
          <div class='hist_con'>
            <div class="hist_con_row1">
              <div class="row">
                <span class="hist_date"><?= $row['create_dt'] ?></span>
                <span class="hist_value">
                   <?= Number_format($row['in_amt']) ?><?= ASSETS_CURENCY ?><br>
                  <span class='small' style='color:#333'>= ￦<?= Number_format($row['amt']) ?><?= $row['coin'] ?></span>
                </span>
              </div>

              <div class="row">
                <span class='hist_name'>입금자 :<?= $row['txhash'] ?></span>
                <span class="hist_value status"><? string_shift_code($row['status']) ?></span>
              </div>
            </div>
          </div>
        <? } ?>

        <?php
        $pagelist = get_paging($config['cf_write_pages'], $page, $total_page_deposit, "{$_SERVER['SCRIPT_NAME']}?id=mywallet&$qstr&view=deposit");
        echo $pagelist;
        ?>
      </div>
    </section>




    <!-- 출금 -->
    <section id='withdraw' class='loadable'>
      <div class="col-sm-12 col-12 content-box round mt20">
        <h3 class="wallet_title">보너스 잔고 출금</h3>
        <span class="desc f_right"> 총 출금 가능액 :
          <span class='price font_red font_weight' style='padding:0 5px;'> <?= number_format($withdrwal_total) ?> <?= ASSETS_CURENCY ?> </span>
        </span>
        
        <!-- 
        <div class="coin_select_wrap">
            <select class="form-control" name="" id="select_coin">
                <option value="eth" selected>ETH</option>
                <option value="mbm">MBM</option>
            </select>
        </div>   
        -->

        <? if (WITHDRAW_CURENCY == '원') { ?>
          <div class="row">
            <div class='col-12'><label class="sub_title">- 출금계좌정보 (최초 1회입력) </label></div>
            <div class='col-6'>
              <input type="text" id="withdrawal_bank_name" class="b_ghostwhite " placeholder="은행명" value="<?= $member['bank_name'] ?>">
            </div>
            <div class='col-6'>
              <input type="text" id="withdrawal_account_name" class="b_ghostwhite " placeholder="예금주" value="<?= $member['account_name'] ?>">
            </div>
            <div class='col-12'>
              <input type="text" id="withdrawal_bank_account" class="b_ghostwhite " placeholder="출금계좌" value="<?= $member['bank_account'] ?>">
            </div>
          </div>
        <?}else { ?>
          <div class="row">
            <div class='col-12'><label class="sub_title">- 출금주소입력 (최초 1회입력))</label></div>
            <div class='col-12'>
              <input type="text" id="withdrawal_wallet_addr" class="b_ghostwhite " placeholder="출금지갑주소" value="<?= $member['withdraw_wallet'] ?>">
            </div>
          </div>
        <?}?>

        <div class="input_shift_value">
          <label class="sub_title">- 출금금액 (수수료:<?= $withdrwal_fee ?>%)</label>
          <span style='display:inline-block; float:right;'><button type='button' id='max_value' class='btn inline' value=''>전액</button></span>


          <input type="text" id="sendValue" class="send_coin b_ghostwhite p15 cabinet" placeholder="출금 금액(<?=BALANCE_CURENCY?>)을 입력해주세요" inputmode="numeric">
          <!-- <span class='cabinet_inner font_red' style='display:contents'>※ 실제 출금은 <?= coin_prices($wallet_code[0], 'name')?> 로 출금됩니다.</span> -->
          <label class='currency-right2'><?= ASSETS_CURENCY ?></label>


          <!-- <div class="row fee">
            <div class="col-5 text_left fee_left">
              <i class="ri-exchange-fill"></i>
              <span id="active_amt">0</span>
            </div>

            <div class="col-12 text_right fee_right">
              <label class="fees">+ 수수료 :</label>
              <i class='ri-money-dollar-circle-line'></i>
              <span id="fee_val">0</span>
            </div>
          </div> -->
        </div>

        <div class="b_line5 mt10 mb10" style='position:inherit'></div>
        <div class="otp-auth-code-container mt20">
          <div class="verifyContainerOTP">
            <label class="sub_title" data-i18n="">- 출금 비밀번호</label>
            <input type="password" id="pin_auth_with" class="b_ghostwhite" name="pin_auth_code" placeholder="Please enter 6-digits pin number" maxlength="6" data-i18n='[placeholder]withdraw.6 자리 핀코드를 입력해주세요'>

          </div>
        </div>

        <div class="send-button-container row">
          <div class="col-5">
            <button id="pin_open" class="btn wd yellow form-send-button" data-i18n="withdraw.인증">인증</button>
          </div>
          <div class="col-7">
            <button type="button" class="btn wd btn_wd form-send-button" id="Withdrawal_btn" data-toggle="modal" data-target="" data-i18n="withdraw.출금 신청" disabled>Withdrawal USDT</button>
          </div>
        </div>
      </div>


      <!-- 출금내역 -->
      <div class="history_box content-box mt40">
        <h3 class="hist_tit" data-i18n='withdraw.출금 내역'>출금 내역</h3>
        <div class="b_line2"></div>

        <? if (sql_num_rows($result_withdraw) == 0) { ?>
          <div class="no_data">출금내역이 존재하지 않습니다</div>
        <? } ?>

        <? while ($row = sql_fetch_array($result_withdraw)) { ?>
          <div class='hist_con'>
            <div class="hist_con_row1">
              <div class="row">
                <span class="hist_date"><?= $row['create_dt'] ?></span>
                <span class="hist_value "><?= BALANCE_CURENCY ?> <?= shift_doller($row['amt_total']) ?></span>
              </div>

              <div class="row">
                <span class="hist_withval"><?= BALANCE_CURENCY ?> <?= $row['amt'] ?> / <label>Fee : </label><?= BALANCE_CURENCY ?> <?= $row['fee'] ?></span>
                <span class="hist_value status"><?= $row['out_amt'] ?> <?= $row['coin'] ?></span>
              </div>

              <div class="row">
                <span class='hist_bank'><label>Address : </label><?= $row['addr'] ?></span>
              </div>

              <div class="row">
                <span class="hist_withval f_small"><label>Result :</label> </span>
                <span class="hist_value status"><? string_shift_code($row['status']) ?></span>
              </div>


            </div>
          </div>
        <? } ?>

        <?php
        $pagelist = get_paging($config['cf_write_pages'], $page, $total_page, "{$_SERVER['SCRIPT_NAME']}?id=mywallet&$qstr&view=withdraw");
        echo $pagelist;
        ?>
      </div>
    </section>
  </div>
</main>

<?php include_once(G5_THEME_PATH . '/_include/tail.php'); ?>
<div class="gnb_dim"></div>
</section>


<!-- <script src="<?= G5_THEME_URL ?>/_common/js/timer.js"></script> -->
<script type="text/javascript" src="./js/qrcode.js"></script>

<script>
  window.onload = function() {
    switch_func("<?= $view ?>");
    // move(<?= $bonus_per ?>); 
    // getTime("<?= $next_rate_time ?>");
  }

  $(function() {



    $(".top_title h3").html("<span data-i18n=''>입출금</span>");

    $(".title_btn").on('click', function() {
      var value = $(this).data("category");
      // console.log(value);

      // 버튼
      $(".title_btn").removeClass('active');
      $(this).addClass('active');

      // 스테이지
      $(".deposit_stage").removeClass('active');
      $("#" + value).addClass('active');

      // 확인요청 
      $(".deposit_requests").removeClass('active');
      $("#deposit_request_" + value).addClass('active');
    });

    var debug = "<?= $is_debug ?>";
    var use_wallet = "<?= USE_WALLET ?>";

    var my_wallet = "<?= $my_wallet ?>";
    var deposit_wallet_address = "<?= $deposit_wallet_address ?>";

    /* if(debug){
      console.log('[ Mode : debug ]');
      $('#Withdrawal_btn').attr('disabled',false);
    } */

    // 입금 전용 지갑사용
    if (use_wallet && my_wallet != '') {

      console.log('입금전용 ETH 지갑생성');
      var wallet_qrcode = generateQrCode("wallet_address_qr", my_wallet, 80, 80);
      $('#wallet_address_qr').val(wallet_qrcode);
    } else if (use_wallet && deposit_wallet_address != '') {
      console.log('입금 회사 지갑 사용');
      var wallet_qrcode = generateQrCode("deposit_wallet_address_qr", deposit_wallet_address, 80, 80);
      $('#deposit_wallet_address_qr').val(wallet_qrcode);
    }





    /* 출금*/
    var usd_price = '<?= $usd_price ?>';
    var fil_price = '<?= $fil_price ?>';
    var WITHDRAW_CURENCY = '<?= WITHDRAW_CURENCY ?>';
    var ASSETS_CURENCY = '<?=ASSETS_CURENCY?>';

    var mb_block = Number("<?= $member['mb_block'] ?>"); // 차단

    var mb_id = '<?= $member['mb_id'] ?>';
    var nw_with = '<?= $nw_with ?>'; // 출금서비스 가능여부

    // 출금설정
    var out_fee = (<?= $withdrwal_fee ?> * 0.01);
    var out_min_limit = '<?= $withdrwal_min_limit ?>';
    var out_max_limit = '<?= $withdrwal_max_limit ?>';
    var out_day_limit = '<?= $withdrwal_day_limit ?>';

    // 최대출금가능금액
    var out_mb_max_limit = <?= $withdrwal_total ?>;


    onlyNumber('pin_auth_with');



    // 출금금액 변경 
    function input_change() {

      var inputValue = $('#sendValue').val().replace(/,/g, '');
      var fee_calc = (inputValue * out_fee);
      var fee_result = Price_value(fee_calc, '$', fil_price, 'fil');

      // result = parseFloat(fee_calc.toFixed(4));
      // var fee_result = result.toLocaleString('ko-KR');

      $('.fee').css('display', 'flex');
      $('#active_amt').text(Price_value(inputValue, '$', fil_price, 'fil') + " <?= WITHDRAW_CURENCY ?>");
      $('#fee_val').text(fee_result + " <?= WITHDRAW_CURENCY ?>");
    }

    $('#sendValue').change(input_change);


    // 출금가능 맥스
    $('#max_value').on('click', function() {
      $("#sendValue").val(out_mb_max_limit.toLocaleString('ko-KR'));
      input_change();
    });


    /*핀 입력*/
    $('#pin_open').on('click', function(e) {

      // 회원가입시 핀입력안한경우
      if ("<?= $member['reg_tr_password'] ?>" == "") {
        dialogModal('Withdraw PIN authentication', '<p>Please register pin number</p>', 'warning');

        $('#modal_return_url').click(function() {
          location.href = "./page.php?id=profile"
        })
        return;
      }

      if ($('#pin_auth_with').val() == "") {
        dialogModal('출금 PIN 코드 인증', '<p>Please put pin number</p>', 'warning');
        return;
      }

      $.ajax({
        url: './util/pin_number_check_proc.php',
        type: 'POST',
        cache: false,
        async: false,
        data: {
          "mb_id": mb_id,
          "pin": $('#pin_auth_with').val()
        },
        dataType: 'json',
        success: function(result) {
          if (result.response == "OK") {
            dialogModal('출금 PIN 코드 인증', '<p>Pin 코드가 인증되었습니다.</p>', 'success');

            $('#Withdrawal_btn').attr('disabled', false);
            $('#pin_open').attr('disabled', true);
            $("#pin_auth_with").attr("readonly", true);
          } else {
            dialogModal('Withdraw PIN authentication', '<p>Pin number mismatch. retry </p>', 'failed');
          }
        },
        error: function(e) {
          //console.log(e);
        }
      });
    });


    // 출금요청
    $('#Withdrawal_btn').on('click', function() {

      var inputVal = $('#sendValue').val().replace(/,/g, '');
      var dataset = [];
      console.log(` out_min_limit : ${out_min_limit}\n out_max_limit:${out_max_limit}\n out_day_limit:${out_day_limit}\n out_fee: ${out_fee}`);


      // 출금계좌정보확인
      if(WITHDRAW_CURENCY == '원' || WITHDRAW_CURENCY =='$'){
        var withdrawal_bank_name = $('#withdrawal_bank_name').val();
        var withdrawal_account_name = $('#withdrawal_account_name').val();
        var withdrawal_bank_account = $('#withdrawal_bank_account').val();

        if (withdrawal_bank_name == '' || withdrawal_bank_account == '' || withdrawal_account_name == '') {
          dialogModal('출금 정보 오류 ', '<strong> 출금계좌정보를 정확히 입력해주세요.</strong>', 'warning');
          return false;
        }
        dataset = {bank_name: withdrawal_bank_name,
            bank_account: withdrawal_bank_account,
            account_name: withdrawal_account_name}
        
      }else{
        var withdrawal_wallet_addr = $('#withdrawal_wallet_addr').val();
        if (withdrawal_wallet_addr== '' || withdrawal_wallet_addr.length < 10) {
          dialogModal('출금 정보 오류', '<strong> 출금 지갑 주소를 정확히 입력해주세요.</strong>', 'warning');
          return false;
        }
        dataset = {withdrawal_wallet_addr: withdrawal_wallet_addr}
      }

      // 출금서비스 이용가능 여부 확인
      if (nw_with == 'N') {
        dialogModal('서비스 이용 에러', '<strong>현재 서비스를 이용할수 없습니다. 잠시후 다시 이용해주세요</strong>', 'warning');
        return false;
      }

      // 금액 입력 없거나 출금가능액 이상일때  
      if (inputVal == '' || inputVal > out_mb_max_limit) {
        console.log(`input : ${inputVal} \n max : ${out_mb_max_limit}`);
        dialogModal('출금신청금액 오류', '<strong>출금액을 확인해주세요.</strong>', 'warning');
        return false;
      }

      // 최소 금액 확인
      if (out_min_limit != 0 && inputVal < Number(out_min_limit)) {
        dialogModal('check input quantity', '<strong> 최소가능금액은 ' + Price(out_min_limit) + ' ' + ASSETS_CURENCY + '입니다.</strong>', 'warning');
        return false;
      }

      //최대 금액 확인
      if (out_max_limit != 0 && inputVal > Number(out_max_limit)) {
        dialogModal('check input quantity', '<strong> 1회 출금 가능금액은 ' + Price(out_max_limit) + ' ' + ASSETS_CURENCY + '입니다.</strong>', 'warning');
        return false;
      }


      if (!mb_block) {
        $.ajax({
          type: "POST",
          url: "./util/withdrawal_proc.php",
          cache: false,
          async: false,
          dataType: "json",
          data: {
            mb_id: mb_id,
            func: 'withdraw',
            amt: inputVal,
            select_coin: WITHDRAW_CURENCY,
            dataset
          },
          success: function(res) {
            if (res.result == "success") {
              dialogModal("출금 신청 접수", "<p>출금신청이 정상적으로 접수되어 \n최대 24 시간 이내 처리예정입니다.</p>", "success");

              $('.closed').click(function() {
                location.href = '/page.php?id=mywallet&view=withdraw';
              });
            } else {
              dialogModal('Withdraw Failed', "<p>" + res.sql + "</p>", 'warning');
            }
          }
        });

      } else {
        dialogModal('Withdraw Failed', "<p>Not available right now</p>", 'failed');
      }

    });


    /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    /* 입금 */

    // 입금확인요청 - 원화
    $('#deposit_request_btn').on('click', function(e) {
      var coin = $(this).data('currency');
      var d_name = $('#deposit_name').val(); // 입금자
      var d_price = $('#deposit_value').val().replace(/,/g, ""); // 입금액


      // 입금설정
      var in_fee = (<?= $deposit_fee ?> * 0.01);
      var in_min_limit = '<?= $deposit_min_limit ?>';
      var in_max_limit = '<?= $deposit_max_limit ?>';
      var in_day_limit = '<?= $deposit_day_limit ?>';

      console.log(` in_min_limit : ${in_min_limit}\n in_max_limit:${in_max_limit}\n in_day_limit:${in_day_limit}\n in_fee: ${in_fee}`);
      console.log(' 입금자 : ' + d_name + ' || 입금액 :' + d_price);

      if (d_name == '' || d_price == '') {
        dialogModal('<p>입금 요청값 확인</p>', '<p>항목을 입력해주시고 다시시도해주세요.</p>', 'warning');
        return false;
      }

      if (in_min_limit > 0 && Number(d_price) < Number(in_min_limit)) {
        dialogModal('<p>최소입금액 확인</p>', '<p>최소입금확인금액은 ' + Price(in_min_limit) + coin + ' 입니다. </p>', 'warning');
        return false;
      }


      $.ajax({
        url: '/util/request_deposit.php',
        type: 'POST',
        cache: false,
        dataType: 'json',
        data: {
          "mb_id": mb_id,
          "coin": coin,
          "hash": d_name,
          "d_price": d_price
        },
        success: function(result) {
          if (result.response == "OK") {
            dialogModal('Deposit Request', 'Deposit Request success', 'success');
            $('.closed').click(function() {
              location.reload();
            });
          } else {
            dialogModal('Deposit Request', result.data, 'failed');
          }
        },
        error: function(e) {
          if (debug) dialogModal('ajax ERROR', 'IO ERROR', 'failed');
        }

      });

    });

    // 입금 확인 요청 - coin
    // $('#deposit_request_coin_btn').on('click', function(e) {

    //   var coin = $(this).data('currency');
    //   var hash_target = $('#tx_hash').val();

    //   console.log('입금 : ' + coin + ' || tx :' + hash_target + "\n length : " + hash_target.length);

    //   if (hash_target == "" || hash_target == undefined || hash_target.length < 10) {
    //     dialogModal('Deposit Confirmation Request', '<p> Check Transaction Hash !</p>', 'warning');
    //     return;
    //   }

    //   $.ajax({
    //     url: '/util/request_deposit.php',
    //     type: 'POST',
    //     cache: false,
    //     async: false,
    //     data: {
    //       "mb_id": mb_id,
    //       "coin": coin,
    //       "hash": hash_target.trim(),
    //       "d_price": 0
    //     },
    //     dataType: 'json',
    //     success: function(result) {
    //       if (result.response == "OK") {
    //         dialogModal('Deposit Request', 'Deposit Request success', 'success');
    //         $('.closed').click(function() {
    //           location.reload();
    //         });
    //       } else {
    //         if (debug) dialogModal('Deposit Request', result.data, 'failed');
    //         else dialogModal('Deposit Request', '<p>ERROR<br>Please try later</p>', 'failed');
    //       }
    //     },
    //     error: function(e) {
    //       if (debug) dialogModal('ajax ERROR', 'IO ERROR', 'failed');
    //     }

    //   });

    // });

    /* $('#deposit_value').on('change',function(){
      var input_val = $(this).val().replace(/,/g, "");
      var output_val = Price(input_val/ 1.2/1000);
      $('.cabinet_outer').text("= $"+output_val).css('display','contents');
    });

    $('#deposit_value').on('click',function(){
      $('.cabinet_outer').css('display','none');
    });
    */


  });




  function switch_func(n) {
    $('.loadable').removeClass('active');
    $('#' + n).toggleClass('active');
  }

  function switch_func_paging(n) {
    $('.loadable').removeClass('active');
    $('#' + n).toggleClass('active');
    window.location.href = window.location.pathname + "?id=mywallet&'<?= $qstr ?>'&page=1&view=" + n;
  }

  function copyURL(addr) {
    alert("계좌번호가 복사 되었습니다");
    var temp = $("<input>");
    $("body").append(temp);
    temp.val($(addr).val()).select();
    document.execCommand("copy");
    temp.remove();
  }

  function copyADREESS(addr) {
    alert("입금 지갑주소가 복사 되었습니다");
    var temp = $("<input>");
    $("body").append(temp);
    temp.val($(addr).text()).select();
    document.execCommand("copy");
    temp.remove();
  }

  // QR코드
  function generateQrCode(qrImg, text, width, height) {
    return new QRCode(document.getElementById(qrImg), {
      text: text,
      width: width,
      height: height,
      colorDark: "#000000",
      colorLight: "#ffffff",
      correctLevel: QRCode.CorrectLevel.H
    });
  }
</script>