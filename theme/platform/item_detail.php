<?
    include_once('./_common.php');
    include_once(G5_THEME_PATH.'/_include/wallet.php');
    
    $menubar =1;
    include_once(G5_THEME_PATH.'/_include/gnb.php');
   
    $title = 'item_detail';
    $od_id = $_GET['od_id'];

    $sub_sql = "SELECT * FROM g5_shop_order WHERE mb_id = '{$member['mb_id']}' and od_id = '{$od_id}'";
    $sub_result = sql_fetch($sub_sql);

?>


<link rel="stylesheet" href="<?=G5_THEME_URL?>/css/scss/include/schedule.css">

<style>
#wrapper{margin-left:0;}
header .top_title h3{margin-lefT:20px;}
header .top_title{padding:10px 20px}
header .top_title h3 img{margin-top:0;}

.schedule li{width:100% !important;}
.schedule li.header{font-size:12px !important}
.schedule li.li-footer{display:flex;}
.schedule li dt{width:20%;}
.schedule li dd:last-child{width:100px;}

.box-body ul {
    margin: 15px 0;
    border-bottom: 2px dotted #ccc;
    padding-bottom: 10px;
}
.box-body .mining {
    font-size: 17px;
    font-weight: 700;
    font-family: Montserrat, Arial, sans-serif;
    line-height: 30px;
    color: #1a1a1a;
}
.box-body .date {
    font-size: 12px;
    letter-spacing: -0.5px;
    color: #0c4ddf;
}
.box-body .rec_adm {
    font-size: 11px;
    color: #777;
}
</style>

<main>
    <div class='container'>

        <div class="col-sm-12 col-12 content-box mining_detail round mt20" id="<?= $cate ?>">
            <div class="box-header row">
                <div class='col-12 text-left'>
                    <span><?= $sub_result['od_name'] ?></span>
                    <span class='m_hist_exp'> : <?=$sub_result['od_rate'] ?> 구좌 / <?=$sub_result['od_select']?>차 / <?=$sub_result['od_layer']?>대</span>
                </div>
            </div>
            <div class='row'>
                <div class='col-7' style="font-size:13px;"> 
                    <span>기부일 : <?=$sub_result['od_time']?></span>
                </div>
                <div class='col-5 text-right hist_value'>
                    <span><?=shift_auto($sub_result['upstair'])?> <?=PURCHASE_CURENCY?></span>
                </div>
            </div>
        </div>

        <div class="col-sm-12 col-12 content-box mining_history round  mb20">
            
            <div class='schedule'>
                <div>
                    <p class="mb10"> <?=$sub_result['od_select']?>차 수익률 지급스케쥴  </p>
                    <?
                    $price = json_decode($sub_result['od_schedule'.$sub_result['od_select']],true);
                    $total_value =0;

                    $value_month = date("m",time($sub_result['od_date']));
                    $value_layer = $sub_result['od_layer'];

                    echo "<li class='header'><dt>월</dt><dt>대수</dt><dd>지급보너스</dd><dd>지급</dd></li>";

                    foreach ($price as $key => $value) {
                        
                        $total_value += $value;

                        echo "<li>";
                        echo "<dt>".array_month($key,$value_month)."월</dt>";
                        echo "<dt>".($value_layer + $key)."대</dt>";
                        echo "<dd>";
                        echo Number_format($value); 
                        echo "</dd>";   
                        echo "<dd>";
                        if($key < $sub_result['pay_count']){
                            echo "<i class='ri-check-fill font_green'></i>";
                        }else{
                            echo "<span style='color:#999;font-weight:300;font-size:13px;'>예정</span>";
                        }
                        echo "</dd>";
                        echo "</li>";
                    }

                    echo "<li class='li-footer'><dt></dt><dt></dt><dd>";
                    echo Number_format($total_value);
                    echo "</dd><dd></dd></li>";
                    ?>
                </div>
            </div>

        </div>

        <div class="col-sm-12 col-12 content-box mining_history round  mb20">
            <div class="box-header">
                지급 받은 보너스
            </div>

            <div class="box-body">
                <?
                    $bonus_history_sql = "SELECT * from soodang_pay WHERE mb_id = '{$member['mb_id']}' order by day,count desc ";
                    $bonus_history_result = sql_query($bonus_history_sql);

                    while($row = sql_fetch_array($bonus_history_result)){
                ?>
                <ul>
                    <li class="date"><?=$row['day']?></li>
                    <li class="mining"> <?=shift_auto($row['benefit'])?>원</li>
                    <li class="rec_adm">[ <?=$row['count']?>차 ] <?=$row['rec']?></li>
                </ul>
                <?}?>
                
            </div>

        </div>
</main>


<div class="gnb_dim"></div>
</section>


<script>
    $(function() {


        $('.back_btn').click(function() {
            //location.href='page.php?id=bonus_history';
            /*
            pageContainerElement.page({ domCache: false });
            $.domCache().remove();
            $.mobile.page.prototype.options.domCache = false;
            */
        });

    });
</script>

<? include_once(G5_THEME_PATH.'/_include/tail.php'); ?>