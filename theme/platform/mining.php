
<?
	include_once('./_common.php');
    include_once(G5_THEME_PATH.'/_include/wallet.php');
	include_once(G5_THEME_PATH.'/_include/gnb.php');

	$title = '마이닝';

    $ordered_items = ordered_items($member['mb_id'],$_GET['item']);
    $mining_cnt = count($ordered_items);

  
    // 내 마이닝상품 리스트 기본값 1
    $list_cnt  = 1;
    if($_GET['myitem'] == 'all'){$list_cnt = $mining_cnt; }

    // 마이닝출금 설정값
    $withdrawal_mining = wallet_config('withdrawal_mining');
    $fee = $withdrawal_mining['fee'];
    $max_limit = $withdrawal_mining['amt_maximum'];
    $min_limit = $withdrawal_mining['amt_minimum'];
    $day_limit = $withdrawal_mining['day_limit'];
   
    $max_mining_total = $mining_total;

    /* 리스트 기본값*/
    $mining_history_limit = " limit 0,1 ";
    $mining_history_limit_text ='전체 내역보기';
    $mining_amt_limit = " limit 0,1 ";
    $mining_amt_limit_text = '전체 내역보기';

    if($_GET['history_limit'] == 'all'){
        $mining_history_limit = "";
        $mining_history_limit_text = "최근내역만보기";
    }

    if($_GET['amt_limit'] == 'all'){
        $mining_amt_limit = "";
        $mining_amt_limit_text = "최근내역만보기";
    }

    // 마이닝 내역
    $mining_history = sql_query("SELECT * from {$g5['mining']} WHERE mb_id = '{$member['mb_id']}' {$mining_history_limit} ");
    $mining_history_cnt = sql_num_rows($mining_history);

    // 마이닝 출금 내역
    $mining_amt_log = sql_query("SELECT * from {$g5['withdrawal']} WHERE mb_id = '{$member['mb_id']}' AND coin = '{$minings[0]}' ");
    $mining_amt_cnt = sql_num_rows($mining_amt_log);


    function category_badge($val){
        if($val == 'mining'){
            return "<span class='badge b_green'>".strtoupper($val)."</span>";
        }else if($val == 'mega_mining'){
            return "<span class='badge b_orange'>".strtoupper($val)."</span>";
        }else if($val == 'zeta_mining'){
            return "<span class='badge b_purple'>".strtoupper($val)."</span>";
        }else{
            return "<span class='badge b_orange'>".strtoupper($val)."</span>";
        }
    }
?>

    <?include_once(G5_THEME_PATH.'/_include/breadcrumb.php');?>
    <link href="<?=G5_THEME_URL?>/css/scss/page/mining.css" rel="stylesheet">

    <main>
        <div id="mining" class="container mining">

            <section id='mymining' class="mt10">
                <h3 class="title">내 마이닝 상품 (<?=$mining_cnt?>)</h3>
                <form name="myitemform" method="get" action="<?=G5_URL?>/page.php?id=mining" style="display:contents;">
                    <input type="hidden" name="id" value='mining'/>
                    <input type="hidden" id="myitem" name="myitem" value="<?if($list_cnt == 1){echo 'all';}else{echo '';}?>"/>
                    <?if($mining_cnt > 1){?><input type="submit" class="btn all_view" value="<?if($list_cnt == 1){echo '전체보기';}else{echo '접어두기';}?>"></input><?}?>
                </form>

                <div class="mining_wrap">
                <?
                if($mining_cnt < 1) {
                    echo "<div class='no_data box_on'>내 보유 상품이 존재하지 않습니다</div>";  
                }else{

                for($i = 0; $i < $list_cnt; $i++){	
                    $color_num = substr($ordered_items[$i]['it_maker'],1,1); 
                ?>
                    <div class="product_buy_wrap round r_card r_card_<?=$color_num?> col-12">
                        <li class="row">
                            <p class="title col-12" style="font-size:14px;">
                                
                                <?if($color_num>0){
                                    echo $ordered_items[$i]['it_option_subject']." -";
                                }?>  
                                <?=$ordered_items[$i]['it_name']?></span>
                            </p> 

                            <p class="num col-12" style="font-size:12px;padding-left:0;padding-right:15px;line-height:12px;">No. <?=$ordered_items[$i]['row']['od_id']?></p>
                        </li>
                        <div class="b_line4"></div>
                        <li class="row">
                            <div class="value col-10">
                                <div class="value1" style="font-size:18px;line-height:42px;">
                                    <?=$ordered_items[$i]['pv']?> mh/s
                                </div>
                                <div class="date"><?=$ordered_items[$i]['row']['cdate']?> ~ <?=expire_date($ordered_items[$i]['row']['cdate'])?></div>
                            </div>
                            <div class="col-2" style='padding:8px 0px 0px;'>
                                <span class='mining_ico' ><img src='<?=coin_prices('eth','icon')?>'/></span>
                            </div>
                        </li>
                    </div>
                    <?php
                        // echo "<script>slide_color('$color_num')</script>";
                    ?>
                    <?php }
                    } ?>
                </div>
            </section>
            
            <div class="b_line5 mt10"></div>

            <section id='withdraw' class='col-12 content-box round mt40'>

                <div class="polding_btn">
                    <div class="btn_title"><span class="wallet_title" >마이닝 출금</span></div> 
                    <div class="max_with">[ <?=$max_mining_total?> <?=$minings[0]?> 출금가능 ]</div>
                    <div class="caret_box"><span class="btn inline"><i class="ri-arrow-down-s-line"></i></span></div>
                </div>

                <div class="polding mt10 hidden">
                
                    <div class="input_address">
                        <label class="sub_title">- 출금주소</label><span class='comment'></span>
                        <input type="text" id="withdrawal-address" class="send_coin b_ghostwhite f_small" placeholder="Enter the Account address (<?=$minings[0]?>)" data-i18n='[placeholder]withdraw.ETH 출금 주소를 입력해주세요' value="<?=$member['withdraw_wallet']?>">
                    </div>

                    <div class="input_shift_value">
                        <label class="sub_title">- 출금금액 (<?=$minings[0]?>)</label>
                        <div style='display:inline-block; float:right;'><button type='button' id='max_value' class='btn inline' value=''>max</button></div>
                        
                        <div class='mt10'>
                            <input type="text" id="sendValue" class="send_coin b_ghostwhite" placeholder="Enter Withdraw quantity" data-i18n='[placeholder]withdraw.출금 금액을 입력해주세요' >
                            <label class='currency-right'><?=$minings[0]?></label>
                        </div>

                        <div class="row fee hidden" style='width:initial'>         
                            <div class="col-5" style="text-align:left">
                            <i class="ri-exchange-fill"></i>
                            <span id="active_amt" >0</span>
                            </div>

                            <div class="col-7" style="text-align:right">
                            <label class="fees">- 수수료(<?=$fee?>%) :</label>
                            <i class="ri-coins-line"></i>
                            <span id="fee_val">0</span>
                            </div>
                        </div>
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
                        <button type="button" class="btn wd btn_wd form-send-button" id="withdrawal_btn" data-toggle="modal" data-target="" disabled>출금신청</button>
                        </div>
                    </div>
                </div>
            </section>

        </div> <!-- mining end-->
        
        <section id='mining_history'>
            
            <div class="history_box content-box mt20">
                <div>
                    <h3 class="hist_tit">내 마이닝 내역 <span class='mymining_total'><?=shift_coin($mining_acc)?> <?=strtoupper($minings[0])?></span></h3>
                
                    <?if($mining_history_cnt < 1){?>
                        <ul class="row">
                            <li class="no_data">내 마이닝 내역이 존재하지 않습니다</li>
                        </ul>   
                    <?}else{?>
                        <?while($row = sql_fetch_array($mining_history)){?>
                        <ul class="row">
                            <li class="col-3 hist_date"><?=$row['day']?></li>
                            <li class="col-4 hist_td"><?=category_badge($row['allowance_name'])?></li>
                            <li class="col-5 hist_value"><?=shift_coin($row['mining'])?> <?=strtoupper($row['currency'])?></li>
                            <li class="col-12 hist_rec"><?=$row['rec']?></li>
                        </ul>
                        <?}?>
                        
                        <div><button type='button' id="mining_history_more" class="btn wd"><?=$mining_history_limit_text?></button></div>

                    <?}?>
                </div>
              
                <?if($mining_amt_cnt > 0){?>
                <div class="b_line6"></div>
                <div id='mining_amt_log' class='mt20'>
                
                <h3 class="hist_tit">마이닝 출금 내역 <span class='mymining_total'> <?=shift_coin($mining_amt)?> <?=strtoupper($minings[0])?></span></h3>
                
                    <?while($row = sql_fetch_array($mining_amt_log)){?>
                        <ul class='row'>
                            <li class="col-12">
                                <span class="col-8 nopadding"><i class="ri-calendar-check-fill"></i><?= $row['create_dt'] ?></span>
                                <span class="col-4 nopadding text-right amt_total"><?=shift_coin($row['amt_total']) ?> <?=$row['coin'] ?></span>
                            </li>
                        
                            <li class="col-12">
                                <span class="col-8 nopadding amt"><i class="ri-coins-line"></i>fee / <?= shift_coin($row['fee']) ?>
                                </span>
                                <span class="col-4 nopadding text-right amt"><i class="ri-refund-2-line"></i><?=shift_coin($row['out_amt'])?> <?=$row['coin'] ?></span>
                            </li>
                        
                            <li class="col-12 "><span class='hist_bank'><i class="ri-wallet-2-fill"></i><?=$row['addr']?></span></li>
                        
                            <li class="col-12 mt10">
                                <span class="col-6 nopadding"><i class="ri-survey-line"></i>process result</span>
                                <span class="col-6 nopadding text-right result"><? string_shift_code($row['status']) ?></span>
                            </li>
                        </ul>
                    <?}?>

                    <div><button type='button' id="mining_amt_more"  class="btn wd"><?=$mining_amt_limit_text?></button></div>
                </div>
                <?}?>
                

            </div>
        </section>

    </main>
    <div class="gnb_dim"></div>

    <script>
        $(function(){
            $(".top_title h3").html("<span data-i18n=''>마이닝</span>")
        });
        
        $(function(){

            $('.polding_btn').click(function(){
                var target = $(this).parent();
                target.find('.polding').slideToggle(300);
                target.toggleClass('open');

                if(target.hasClass('open')){
                    $(this).find('.caret_box i').attr('class','ri-arrow-up-s-line');
                }else{
                    $(this).find('.caret_box i').attr('class','ri-arrow-down-s-line');
                }
            });

            $('#mining_history_more').on('click',function(){
                locationURL('history_limit','mining_history');
            });

            $('#mining_amt_more').on('click',function(){
                locationURL('amt_limit','mining_amt_log');
            });

            function locationURL(keyword,anchor=''){
                var url = location.href.split('?')[0];
                var params = new URLSearchParams(location.search);

                if(params.get(keyword) == 'all'){
                    params.set(keyword, '0');
                }else{
                    params.set(keyword, 'all');
                }
                var queryString = params.toString();
                
                console.log(url+ '?' + queryString);
                window.location.href = url+ '?' + queryString+ '#'+anchor;
            }

            onlyNumber('pin_auth_with');

            /* 출금*/
            var WITHDRAW_CURENCY = '<?=$minings[0]?>';
            var COIN_NUMBER_POINT = '<?=COIN_NUMBER_POINT?>';

            var mb_block = Number("<?= $member['mb_block'] ?>"); // 차단
            var mb_id = '<?= $member['mb_id'] ?>';
            var fee_total = fee_calc = coin_amt = 0;
            var eth_price = '<?=$eth_price?>';

            // 출금설정
            var nw_with = '<?= $nw_with ?>'; // 출금서비스 가능여부
            var fee = (<?= $fee ?> * 0.01);
            var min_limit = '<?= $min_limit ?>';
            var max_limit = '<?= $max_limit ?>';
            var day_limit = '<?= $day_limit ?>';

            // 최대출금가능금액
            var mb_max_limit = '<?=$max_mining_total?>';
            // console.log(` min_limit : ${min_limit}\n max_limit:${max_limit}\n day_limit:${day_limit}\n fee: ${fee}`);
            
            function withdraw_value(num){
                return Number(num).toFixed(COIN_NUMBER_POINT);
            }

            function withdraw_curency(num){
                return Number(num)+' '+ WITHDRAW_CURENCY;
            }

            // 출금금액 변경 
            function input_change() {
                // var inputValue = $('#sendValue').val().replace(/,/g, '');
                var inputValue = $('#sendValue').val();
                
                if(inputValue > mb_max_limit){
                    dialogModal('출금가능 금액확인', '<strong> 출금가능금액내에서 정수 단위로 입력해주세요 </strong>', 'warning');
                }

                fee_total =  withdraw_value(inputValue * fee);
                fee_calc = Number(inputValue) - Number(fee_total);
                coin_amt = withdraw_value(fee_calc);

                // console.log(`fee : ${fee_total}\nfee_calc : ${fee_calc}\n실제출금계산액 :: ${fee_calc}`);

                $('.fee').css('display', 'flex');
                $('#fee_val').text(withdraw_curency(fee_total));
                $('#active_amt').text(withdraw_curency(coin_amt));
            }

            $('#sendValue').change(input_change);

            // 출금가능 맥스
            $('#max_value').on('click', function() {
                $("#sendValue").val(mb_max_limit.toLocaleString('ko-KR'));
                input_change();
            });
           

            /*핀 입력*/
            $('#pin_open').on('click', function(e) {

                // 회원가입시 핀입력안한경우
                if ("<?= $member['reg_tr_password'] ?>" == "") {
                    dialogModal('Withdraw PIN authentication', '<p>Please register pin number</p>', 'warning');
                    $('#modal_return_url').click(function() {
                        location.href = "./page.php?id=profile";
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
                            $('#withdrawal_btn').attr('disabled', false);
                            $('#pin_open').attr('disabled', true);
                            $("#pin_auth_with").attr("readonly", true);
                        }else {
                            dialogModal('Withdraw PIN authentication', '<p>Pin number mismatch. retry </p>', 'failed');
                        }
                    },
                    error: function(e) {
                        //console.log(e);
                    }
                });
            });

            
            $('#withdrawal_btn').on('click',function(){
                
                var inputVal = $('#sendValue').val().replace(/,/g, '');

                // 출금주소 입력확인
                if($('#withdrawal-address').val() == ""){
                    dialogModal('empty wallet address', '<strong> wallet address to withdraw </strong>', 'warning');
                    return false;
                }

                // 출금서비스 이용가능 여부 확인
                if (nw_with == 'N') {
                    dialogModal('Not available right now', '<strong>Not available right now.</strong>', 'warning');
                    return false;
                }

                // 금액 입력 없을때 
                if (inputVal == '' || inputVal <= 0) {
                    dialogModal('check field quantity', '<strong>Please check field and retry.</strong>', 'warning');
                    return false;
                }

                // 최소 금액 확인
                if (min_limit != 0 && inputVal < Number(min_limit)) {
                    dialogModal('check input quantity', '<strong> 최소가능금액은 '+ min_limit + WITHDRAW_CURENCY +' 입니다.</strong>', 'warning');
                    return false;
                }

                //최대 금액 확인
                if (max_limit != 0 && inputVal > Number(max_limit)) {
                    dialogModal('check input quantity', '<strong> 최대가능금액은 '+ max_limit + WITHDRAW_CURENCY +' 입니다.</strong>', 'warning');
                    return false;
                }

                if (!mb_block) {
                    $.ajax({
                    type: "POST",
                    url: "./util/withdrawal_coin_proc.php",
                    cache: false,
                    async: false,
                    dataType: "json",
                    data: {
                        mb_id: mb_id,
                        wallet_addr: $('#withdrawal-address').val(),
                        func: 'mining-withdraw',
                        amt: inputVal,
                        fee : fee_total,
                        coin_amt : coin_amt,
                        coin_cost : eth_price,
                        select_coin : WITHDRAW_CURENCY
                    },
                    success: function(res) {
                        if (res.result == "success") {
                            dialogModal('Withdraw has been successfully withdrawn', '<p>Please allow up to 24 hours for the transaction to complete.</p>', 'success');

                            $('.closed').click(function() {
                                location.reload();
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

            });

    </script>

<? include_once(G5_THEME_PATH.'/_include/tail.php'); ?>
