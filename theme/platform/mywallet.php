<?
include_once('./_common.php');
include_once(G5_THEME_PATH . '/_include/wallet.php');
include_once(G5_THEME_PATH . '/_include/gnb.php');
// include_once(G5_LIB_PATH . '/blocksdk.lib.php');
// include_once(G5_LIB_PATH.'/crypto.lib.php');

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
    $withdrwal_total = $total_withraw * ($withdrwal_max_limit * 0.01);
  }

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



//지갑 생성
/* $callback = G5_URL . "/plugin/blocksdk/point-callback.php";
      $blocksdk_conf = Crypto::GetConfig();

      if(empty($member['mb_9'])==true && $blocksdk_conf['de_eth_use'] == 1){
        $address = Crypto::GetClient("eth")->createAddress([
          "name" => "member_no_".$member['mb_no']
        ]);
        
        Crypto::CreateWebHook($callback,"eth",$address['address']);
        
        // $update_sql .= empty($update_sql) ? "" : ","; 
        $update_sql = "mb_9='{$address['address']}'";
        $member['mb_9'] = $address['address'];
        
        $sql = "
        insert into 
        blocksdk_member_eth_addresses (id, address, private_key) 
        values ('{$address['id']}', '{$address['address']}','{$address['private_key']}')
        ";
        sql_fetch($sql);
      }

      if(empty($update_sql) == false){
        $sql = "UPDATE {$g5['member_table']} SET {$update_sql} WHERE mb_no={$member['mb_no']}";
        sql_query($sql);
      } */

// $wallet_sql = "SELECT private_key FROM blocksdk_member_eth_addresses WHERE address = '{$member['mb_9']}'";
// $wallet_row = sql_fetch($wallet_sql);
// $private_key = $wallet_row['private_key'];
// $mb_id = $member['mb_id'];


// if($member['eth_download'] == "0"){      
//     include_once(G5_LIB_PATH."/download_key/set_private_key.php"); 
// }

// if($member['eth_download'] == "1"){
//   include_once(G5_LIB_PATH."/download_key/get_private_key.php");

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
?>


<!-- <link rel="stylesheet" href="<?= G5_THEME_CSS_URL ?>/withdrawal.css"> -->
<!-- <script type="text/javascript" src="./js/qrcode.js"></script> -->

<? include_once(G5_THEME_PATH . '/_include/breadcrumb.php'); ?>

<main>
  <div class='container mywallet'>

    <div class="my_btn_wrap">
      <div class="row mywallet_btn">
        <div class='col-lg-6 col-12'>
          <button type='button' class='btn wd main_btn b_darkblue round' onclick="switch_func('deposit')" data-i18n="deposit.대문자 입금"> DEPOSIT</button>
        </div>
        <div class='col-lg-6 col-12'>
          <button type='button' class='btn wd main_btn b_skyblue round' onclick="switch_func('withdraw')" data-i18n="withdraw.대문자 출금">WITHDRAW</button>
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

          <!-- 이더전용입금 -->
          <!-- 
            <div class="wallet qrBox col-3">
                <div class="eth_qr_img qr_img" id="my_eth_qr"></div>
            </div> 
            <div class='qrBox_right col-9'>
                <input type="text" id="my_eth_wallet" class="wallet_addr" value="" title='my address' disabled/>
                <button class="btn wd line_btn" id="accountCopy" onclick="copyURL('#my_eth_wallet')">
                        <span data-i18n="deposit.주소복사"> Copy Address </span>
                </button>
            </div>
          -->
      </div>


  <div class="col-sm-12 col-12 content-box round mt20" id="eth">
    <h3 class="wallet_title" data-i18n="deposit.입금확인요청">입금확인요청 </h3> <span class='desc'> - 계좌입금후 1회만 요청해주세요</span>

    <div class="row">
      <div class="btn_ly qrBox_right "></div>
      <div class="col-sm-12 col-12 withdraw mt20">
        <input type="text" id="deposit_name" class='b_ghostwhite p15' placeholder="" data-i18n='[placeholder]deposit.입금자명을 입력해주세요'>

        <input type="text" id="deposit_value" class='b_ghostwhite p15' placeholder="" data-i18n='[placeholder]deposit.입금액을 입력해주세요' inputmode="numeric">
        <label class='currency-right'><?= ASSETS_CURENCY ?></label>
      </div>

      <div class='col-sm-12 col-12 '>
        <button class="btn btn_wd font_white deposit_request" data-currency="원">
          <span data-i18n="deposit.입금확인요청">입금확인요청</span>
        </button>
      </div>
    </div>
  </div>


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
          <span class="hist_value"><?= Number_format($row['in_amt']) ?><?= $row['coin'] ?></span>
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
      <h3 class="wallet_title" data-i18n="withdraw.출금">출금</h3>
      <span class="desc"> 총 출금 가능액 : <?= number_format($withdrwal_total) ?> <?= ASSETS_CURENCY ?></span>
      <!-- 
      <div class="coin_select_wrap">
          <select class="form-control" name="" id="select_coin">
              <option value="eth" selected>ETH</option>
              <option value="mbm">MBM</option>
          </select>
      </div> 
      -->

      <div class="row">
        <div class='col-12'><label class="sub_title">- 출금계좌정보 (최초 1회입력))</label></div>
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

      <div class="input_shift_value">
        <label class="sub_title">- 출금금액 (수수료:<?= $withdrwal_fee ?>%)</label>
        <span style='display:inline-block;float:right;'><button type='button' id='max_value' class='btn inline' value=''>max</button></span>

        <input type="text" id="sendValue" class="send_coin b_ghostwhite " placeholder="Enter Withdraw quantity" data-i18n='[placeholder]withdraw.출금 금액을 입력해주세요' inputmode="numeric">
        <label class='currency-right'><?= ASSETS_CURENCY ?></label>
        <? if ($fee != 0) { ?>
          <div class='fee'>
            <span>출금 총액(+fee): </span><span id='fee_val'></span>
          </div>
        <? } ?>
      </div>

      <div class="b_line5"></div>
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
            <span class="hist_value "><?= BALANCE_CURENCY ?> <?=shift_doller($row['amt_total']) ?></span>
          </div>

          <div class="row">
            <span class="hist_withval"><?= BALANCE_CURENCY ?> <?= $row['amt'] ?> / <label>Fee : </label><?= BALANCE_CURENCY ?> <?= $row['fee'] ?></span>
            <span class="hist_value status"><?=$row['out_amt']?> <?= $row['coin'] ?></span>
          </div>

          <div class="row">
            <span class='hist_bank'><label>Address : </label><?=$row['addr']?></span>
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

<script>
  window.onload = function() {
    switch_func("<?= $view ?>");
    // move(<?= $bonus_per ?>); 
    // getTime("<?= $next_rate_time ?>");
  }
  
  $(function() {
    $(".top_title h3").html("<span data-i18n=''>입출금</span>");

    var debug = "<?= $is_debug ?>";

    /* if(debug){
      console.log('[ Mode : debug ]');
      $('#Withdrawal_btn').attr('disabled',false);
    } */

    // 회사 지갑사용
    // var eth_wallet_addr = '<?= ETH_ADDRESS ?>';
    // if(eth_wallet_addr != ''){
    //     $('#eth_wallet_addr').val(eth_wallet_addr);
    //     generateQrCode("eth_qr_img",eth_wallet_addr, 80, 80);
    // }

    // 입금 전용 지갑사용
    /* var my_eth_wallet = "<?= $member['mb_9'] ?>"
    if(my_eth_wallet != ''){
      $('#my_eth_wallet').val(my_eth_wallet);
        generateQrCode("my_eth_qr",my_eth_wallet, 80, 80);
    } */


    /* 출금*/
    var ASSETS_CURENCY = '<?= ASSETS_CURENCY ?>';
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
      var fee_calc = Number(inputValue * out_fee) + Number(inputValue);
      result = parseFloat(fee_calc.toFixed());
      var fee_result = result.toLocaleString('ko-KR');

      $('.fee').css('display', 'block');
      $('#fee_val').text(fee_result + " <?= BALANCE_CURENCY ?>");
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
        dialogModal('Withdraw PIN authentication', '<p>Please put pin number</p>', 'warning');
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
            dialogModal('Withdraw PIN authentication', '<p>Pin number match</p>', 'success');

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
      console.log(` out_min_limit : ${out_min_limit}\n out_max_limit:${out_max_limit}\n out_day_limit:${out_day_limit}\n out_fee: ${out_fee}`);
    

      // 출금계좌정보확인
      var withdrawal_bank_name = $('#withdrawal_bank_name').val();
      var withdrawal_account_name = $('#withdrawal_account_name').val();
      var withdrawal_bank_account = $('#withdrawal_bank_account').val();

      if (withdrawal_bank_name == '' || withdrawal_bank_account == '' || withdrawal_account_name == '') {
        dialogModal('Check input field ', '<strong> Please check withdrawal account.</strong>', 'warning');
        return false;
      }

      // 출금서비스 이용가능 여부 확인
      if (nw_with == 'N') {
        dialogModal('Not available right now', '<strong>Not available right now.</strong>', 'warning');
        return false;
      }

      // 금액 입력 없거나 출금가능액 이상일때  
      if (inputVal == '' || inputVal > out_mb_max_limit) {
        console.log(`input : ${inputVal} \n max : ${out_mb_max_limit}`);
        dialogModal('check field quantity', '<strong>출금액을 확인해주세요.</strong>', 'warning');
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
            select_coin : ASSETS_CURENCY,
            bank_name: withdrawal_bank_name,
            bank_account: withdrawal_bank_account,
            account_name: withdrawal_account_name
          },
          success: function(res) {
            if (res.result == "success") {
              dialogModal('Withdraw has been successfully withdrawn', '<p>Please allow up to 24 hours for the transaction to complete.</p>', 'success');

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




    /*입금 확인 요청 - coin */
    /* $('.deposit_request.eth').on('click', function (e) {
      var d_price = $('#deposit_value').val();

      if($('.d_price').text() != ""){
          d_price = $('.d_price').text();
      }
      
      var coin = $(this).data('currency');
      var hash_target = $(this).parent().parent().find('.confirm_hash');
      
      if(hash_target.val()==""){
          dialogModal('Deposit Confirmation Request','<p>Transaction Hash is empty!</p>','warning');
          return;
      }

      if(debug) console.log('입금 : '+ coin +' || tx :' + hash_target.val());

      $.ajax({
        url: '/util/request_deposit.php',
        type: 'POST',
        cache: false,
        async: false,
        data: {
          "mb_id" : mb_id,
          "coin" : coin,
          "hash": hash_target.val(),
          "d_price" : d_price
        },
        dataType: 'json',
        success: function(result) {
          if(result.response == "OK"){
            dialogModal('Deposit Request', 'Deposit Request success', 'success');
            $('.closed').click(function(){
              location.reload();
            });
          }else{
            if(debug) dialogModal('Deposit Request',result.data,'failed'); 
            else dialogModal('Deposit Request','<p>ERROR<br>Please try later</p>','failed');
          }
        },
        error: function(e){
          if(debug) dialogModal('ajax ERROR','IO ERROR','failed'); 
        }
        
      });
    });  */


    // 입금확인요청 
    $('.deposit_request').on('click', function(e) {
      var d_name = $('#deposit_name').val(); // 입금자
      var d_price = $('#deposit_value').val().replace(/,/g, ""); // 입금액
      var coin = $(this).data('currency');

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
      
      if(in_min_limit > 0 &&  Number(d_price) < Number(in_min_limit) ){
        dialogModal('<p>최소입금액 확인</p>', '<p>최소입금확인금액은 '+ Price(in_min_limit)+coin +' 입니다. </p>', 'warning');
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

  /* QR코드
  function generateQrCode(qrImg, text, width, height){
      return new QRCode(document.getElementById(qrImg), {
          text: text,
          width: width,
          height: height,
          colorDark : "#000000",
          colorLight : "#ffffff",
          correctLevel : QRCode.CorrectLevel.H
      });
  } */
</script>