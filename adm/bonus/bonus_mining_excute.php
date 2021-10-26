<?
include_once('./_common.php');
include_once('./bonus_inc.php');



/* 
    $debug=1;
    if($debug){
    $_POST['bonus_day'] = '2021-10-26';
    $_POST['idx'] = 1;
    $_POST['global_mining_rate'] = 0.001;
    $_POST['act_button'] = 'solid';
} */

$code = 'mining';
$category = 'mining';

$bonus_day = $_POST['bonus_day'];
$func = $_POST['act_button'];

$global_mining_rate = $mining_rate;


if($func == 'solid'){
    $idx = $_POST['idx'];
    $select_order_sql = "SELECT * from g5_shop_order WHERE no = {$idx} ";
    $select_order = sql_fetch($select_order_sql);

    $od_id = $select_order['od_id'];
    $mb_id = $select_order['mb_id'];
    $hash_power = $select_order['mine_rate'];
    $benefit = $select_order['od_rate']*$global_mining_rate;

    /* 수당지급 */
    if($benefit > 0){
        $rec=$code.' Bonus By '.$hash_power.' mh/s | '.$od_id;
        $rec_adm = $hash_power.' * '.$global_mining_rate.' = '.$benefit;

        echo "<span class=blue> ▶▶ 마이닝 지급 : ".$benefit.' '.$minings[0]."</span><br>";
        $record_result = mining_record($mb_id, $code, $benefit,$hash_power,$minings[0], $rec, $rec_adm, $bonus_day);
        
        if($record_result){
            $balance_up = "update g5_member set {$mining_target} = {$mining_target} + {$benefit}  where mb_id = '{$mb_id}' ";

            // 디버그 로그
            if($debug){
                echo "<code>";
                print_R($balance_up);
                echo "</code>";
            }else{
                $member_update_result = sql_query($balance_up);
            }
        }

        /* 주문내역 업데이트*/
        $next_date = date("Y-m-d", strtotime($bonus_day."+30 day"));
        $update_order_sql = "UPDATE g5_shop_order set mine_date = '{$next_date}', pay_count = pay_count + 1, mine_acc = mine_acc + {$benefit} WHERE no = {$idx}";

        if($debug){
            echo "<br><code>";
            print_R($update_order_sql);
            $order_update_result = 1;
            echo "</code>";
        }else{
            $order_update_result = sql_query($update_order_sql);
        } 
    }

    if($member_update_result && $order_update_result){
        ob_clean();
        echo (json_encode(array("result" => "sucess", "code" => "0001", "sql" => "정상지급되었습니다.")));
    }else{
        ob_clean();
        echo (json_encode(array("result" => "failed", "code" => "9999", "sql" => "정상처리되지않았습니다.")));
    }

}else{

    if($func == '선택 마이닝 지급'){
        echo "<html><body>";
        echo "<header>정산시작</header>";
        echo "<div class='btn' onclick=bonus_url('mining');>돌아가기</div>";
        echo "<div>";
    }

    if($_POST['chk']){
        for($i = 0 ; $i < count($_POST['chk']); $i ++){

            $k = $_POST['chk'][$i];

            $idx = $_POST['no'][$k];
            $mine_date = $_POST['mine_date'][$k];
            $mine_bonus = $_POST['mine_bonus'][$k];
            
            if($func == '선택 마이닝 지급'){
                
                $select_order_sql = "SELECT * from g5_shop_order WHERE no = {$idx} ";
                $select_order = sql_fetch($select_order_sql);

                $od_id = $select_order['od_id'];
                $mb_id = $select_order['mb_id'];
                $hash_power = $select_order['mine_rate'];
                $benefit = $mine_bonus;

                echo "<br><br><span class='title block gold' style='font-size:30px;'>".$mb_id."</span><br>";

                /* 수당지급 */
                if($benefit > 0){
                    $rec=$code.' Bonus By '.$hash_power.' mh/s | '.$od_id;
                    $rec_adm = $hash_power.' * '.$global_mining_rate.' = '.$benefit;

                    echo "<span class=blue> ▶▶ 마이닝 지급 : ".$benefit.' '.$minings[0]."</span><br>";
                    $record_result = mining_record($mb_id, $code, $benefit,$hash_power,$minings[0], $rec, $rec_adm, $bonus_day);

                    
                    if($record_result){
                        $balance_up = "update g5_member set {$mining_target} = {$mining_target} + {$benefit}  where mb_id = '{$mb_id}' ";

                        // 디버그 로그
                        if($debug){
                            echo "<code>";
                            print_R($balance_up);
                            echo "</code>";
                        }else{
                            sql_query($balance_up);
                        }
                    }
                }else{
                    echo "<span class=red> ▶▶ 지급수당 없음</span><br>";
                }

                /* 주문내역 업데이트*/
                $next_date = date("Y-m-d", strtotime($bonus_day."+30 day"));
                $update_order_sql = "UPDATE g5_shop_order set mine_date = '{$next_date}', pay_count = pay_count + 1, mine_acc = mine_acc + {$benefit} WHERE no = {$idx}";

                if($debug){
                    echo "<code>";
                    print_R($update_order_sql);
                    $order_update_result = 1;
                    echo "</code>";
                }else{
                    $order_update_result = sql_query($update_order_sql);
                }

                if($order_update_result){
                    echo "<span class='blue'>지급처리완료</span>";
                }

            }else if($func == '선택 수정'){
                $memo = "관리자 수정_".G5_TIME_YMD;
                $update_order_sql = "UPDATE g5_shop_order set mine_date = '{$mine_date}', od_memo = '{$memo}' WHERE no = {$idx} ";

                if($debug){
                    print_R($update_order_sql);
                    echo "<br>";
                }else{
                    $up_query = sql_query($update_order_sql);
                }

                if($up_query){
                    ob_clean();
                    alert('선택항목이 변경처리 되었습니다.');
                    goto_url('./bonus_mining2.php');
                }
            }
        }
    }

}
?>

<?if($func == '선택 마이닝 지급'){
    include_once('./bonus_footer.php');
}?>
