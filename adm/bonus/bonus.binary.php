<?php
$sub_menu = "600900";
include_once('./_common.php');
// $debug=1;

include_once('./bonus_inc.php');

auth_check($auth[$sub_menu], 'r');



// 바이너리(후원)매칭 수당
$min30= date("Y-m-d", strtotime( "-30 day", strtotime($bonus_day)) );



//회원 리스트를 읽어 온다.
if($_GET['test_id']){
    $pre_sql = "select * from g5_member where mb_id = '".$test_id."'";
}else{
    $pre_sql = "select * from {$g5['member_table']} where (1)".$pre_condition .' '. $admin_condition." order by mb_no desc";
}

$pre_result = sql_query($pre_sql);
$result_cnt = sql_num_rows($pre_result);

if(!$debug){}
    delete_sales();
    habu_sales_calc('',$config['cf_admin'],0);
    habu_sales_calc('b',$config['cf_admin'],0);


ob_start();


// 설정로그 
echo "<strong>".strtoupper($code)." 지급비율 : ". $bonus_row['rate']."%   </strong> |    지급조건 :".$pre_condition.' | '.$bonus_source_tx." | ".$bonus_layer_tx." | ".$bonus_limit_tx."<br>";
echo "<strong>".$bonus_day."</strong><br>";
echo "<br><span class='red'> 기준대상자 : ".$result_cnt." (관리자제외) </span><br><br>";
echo "<div class='btn' onclick='bonus_url();'>돌아가기</div>";

if($debug){
	echo "<code>";
	print_r($pre_sql);
	echo "</code><br>";
}

?>

<html><body>
<header>정산시작</header>    
<div>


<?

//산하매출기록 초기화
function delete_sales(){
    global $bonus_day;
    $sql_sales_del = " TRUNCATE table recom_bonus_noo ";
     sql_query($sql_sales_del);
    
    $sql_sales_del = " TRUNCATE table recom_bonus_week";
        sql_query($sql_sales_del);
    
    $sql_sales_del = " TRUNCATE table recom_bonus_today";
        sql_query($sql_sales_del);
    
    $sql_sales_del = " TRUNCATE table brecom_bonus_noo";
        sql_query($sql_sales_del);
    
    $sql_sales_del = " TRUNCATE table brecom_bonus_week";
        sql_query($sql_sales_del);

    $sql_sales_del = " TRUNCATE table brecom_bonus_today";
        sql_query($sql_sales_del);
}            

//산하 매출 기록 
function habu_sales_calc($gubun, $recom, $deep){

    global $bonus_day,$min30,$debug;
    $deep++; // 대수

    $res_sql = "select * from g5_member where mb_".$gubun."recommend='".$recom."' ";
    $res= sql_query($res_sql);

    $noo = $today = $mysales = 0;

    for ($j=0; $rrr=sql_fetch_array($res); $j++) { 
	
        $recom=$rrr['mb_id'];
       
        //누적매출
		$sql1= sql_fetch("select sum(upstair)as hap from g5_shop_order where mb_id='".$recom."' AND od_date <= '{$bonus_day}'  ");
        $noo +=$sql1['hap'];
        
        
        //일일매출
		$day_search = " and od_date ='$bonus_day'";
		$sql3= sql_fetch("select sum(upstair)as hap from g5_shop_order where mb_id='".$recom."' $day_search");
        $today +=$sql3['hap'];
        
         // 디버그 로그
         if($debug){
            echo "<span class=red>".$recom." | noo: ".$noo." | mon: ".$mon." | today: ".$today."</span><br>" ;
         }

		list($noo_r,$today_r)=habu_sales_calc($gubun, $recom, $deep);	 

        if($debug){
            echo "<code>";
            echo $recom.' | '.$deep;
        }

        $noo_r += $mysales;
		$today_r += $mysales;

        $noo+=$noo_r;
        $today+=$today_r; 


        if( ($noo>0) && ($noo_r>0)) {
            if($j==0){
                $rec=$noo;
            }else{
                $rec=$noo_r;	
            }

            if($j == count($rrr)) {
                $rec=$rec;
            }else{
                $rec=$noo_r;
            }
            
            $inbnoo = "insert ".$gubun."recom_bonus_noo SET noo=".$rec.", mb_id='".$recom."',  day = '".$bonus_day."'";
            
            // 디버그 로그
            if($debug){
                echo " | noo: ".$rec;
                sql_query($inbnoo);
            }else{
                sql_query($inbnoo);	
            }
        }
			
			
        if(($today>0) && ($today_r>0)) {
            if($j==0){
                $rec=$today;
            }else{
                $rec=$today_r;
            }
            
            if($j == count($rrr)) {
                $rec=$rec;
            }else{
                $rec=$today_r;
            }

            $intoday = "insert ".$gubun."recom_bonus_today SET today=".$rec.", mb_id='".$recom."',  day = '".$bonus_day."'";
            
            // 디버그 로그
            if($debug){
                echo " | today: ".$rec."</code>";
                sql_query($intoday);
            }else{
                sql_query($intoday);
            }
            
        }
        echo "</code>";
    }
	return array($noo,$today);
}



    for($i=0; $member=sql_fetch_array($pre_result); $i++) {

        $mb_no=$member['mb_no'];
        $mb_id=$member['mb_id'];
        $mb_name=$member['mb_name'];
        $mb_level=$member['mb_level'];
        $mb_deposit=$member['mb_deposit_point'];
        $mb_balance=$member['mb_balance'];
        $grade=$member['grade'];
        $recom=$member['mb_recommend'];
        $mb_pv = $member['mb_pv'];
        $mb_sales = $member['mb_save_point'];

        
       /*  if($bonus_limit > 0 && $bonus_row['bonus_condition'] == 'pv'){
            $limit_point = $mb_rate * $bonus_limit;

        }else if($bonus_limit > 0 && $bonus_row['bonus_condition'] == 'sales'){
            $limit_point = $mb_sales * $bonus_limit;

        }else{
            $limit_point = $bonus_limit*$mb_deposit;
        } */

        $limit_point = $mb_sales * $bonus_limit;

        if($mb_level < 10 ){

        list($id1,$hap1,$id2,$hap2) = my_bchild($mb_id,$bonus_day,0);

        echo '<br>▶ 실적 계산 기준  :: ' .$id1.'---'.$hap1.' // '.$id2.'---'.$hap2.' || 수당한계 : <span >'.$limit_point.'</span><br>';
        

        $note='Binary Bonus from member';
        $firstname=$mb_name;
        $firstid=$mb_id;
        
        if(($hap1>0) || ($hap2>0)){
            if( $hap1<$hap2 )
            { //$hap1이 소실적이라면
                $bonus = $hap1*$bonus_rate;

                if( $bonus > $limit_point && $limit_point!=0){ //소실적이 극점?

                    $today_sales=$bonus;
                    
                    // 수당 로그
                    echo "▶▶ 수당 계산 1-1 (수당초과) :: 대실적-<strong>".$hap2."</strong>(".$id2.") ||  소실적-<strong>".$hap1."</strong>(".$id1.") ||  수당: <span class=blue>".($bonus_rate*100)."%</span> || 발생수당 : <strong>".Number_format($bonus)."</strong><br><br>";
                    
                    $note_adm=' 소실적 발생 (대실적만 이월) (1-1-1) 소실적:'.$hap1.	'('.$id1.') || 대실적:'.$hap2.	'('.$id2.') | 이월금:'.($hap2-$hap1);
                    $note_adm2=' 대실적 이월 (1-1-2) :'.$hap2.'('.$id2.') | 이월금:'.($hap2-$hap1);
                    $note_adm3=' 소실적 소멸 (1-1-3) :'.$hap1.'('.$id1.') | 이월금: 소멸';
                    $note = $note." ".$id1;

                    save_benefit($bonus_day, $mb_no, $mb_id, $mb_name, $grade, $mb_level, $recom,  $today_sales, $note_adm, $note, $mb_balance,$mb_deposit);
  
                        iwol_process($bonus_day, $mb_id, $id2, $mb_name, 111, $hap2-$hap1, $note_adm2);
                        iwol_process($bonus_day, $mb_id, $id1, $mb_name, 112, 0, $note_adm3); //소실적 소멸
                    
                }
                else if($hap1 == 0){ //소실적이 0일때
                    
                    $today_sales=$bonus;
                    $note_adm2='소실적 0 (대실적만 이월) (1-3) 소실적:'.$hap1.	'('.$id1.') || 대실적:'.$hap2.'('.$id2.') | 이월금:'.($hap2-$hap1);

                    // 수당 로그
                    echo " ▶▶ 수당 계산 1-3 ::  대실적-<strong>".$hap2."</strong>(".$id2.") ||  소실적-<strong>".$hap1."</strong>(".$id1.") ||  수당: <span class=blue>".($bonus_rate*100)."%</span> || 발생수당 : <strong>".Number_format($today_sales)."</strong><br><br>";
                    $note_adm='소실적 0 (대실적만 이월) (1-3-1) 대실적:'.$hap2.	'('.$id2.') || 소실적:'.$hap1.'('.$id1.') | 이월금:'.($hap2-$hap1);
                        
                        iwol_process($bonus_day, $mb_id, $id2, $mb_name, 13, $hap2-$hap1, $note_adm2);
                }

                else { //수당발생
                
                    $today_sales=$bonus;

                     // 수당 로그
                    echo "▶▶ 수당 계산 1-2 :: 대실적-<strong>".$hap2."</strong>(".$id2.") ||  소실적-<strong>".$hap1."</strong>(".$id1.") ||  수당: <span class=blue>".($bonus_rate*100)."%</span> ||  <span class=red>발생수당 : ".$hap1.'*'.$bonus_rate.'= '.Number_format($today_sales)."</span><br><br>";
                    
                        $note_adm=" 소실적 발생 (대실적만 이월) (1-2-1) 소실적:".$hap1.	'('.$id1.') || 대실적:'.$hap2.	'('.$id2.') | 이월금:'.($hap2-$hap1);
                        $note_adm2=' 대실적 이월 (1-2-2) :'.$hap2.'('.$id2.') | 이월금:'.($hap2-$hap1);
                        $note_adm3=' 소실적 소멸 (1-2-3) :'.$hap1.'('.$id1.') | 이월금: 소멸';
                        $note = $note." ".$id1;

                        save_benefit($bonus_day, $mb_no, $mb_id, $mb_name, $grade, $mb_level, $recom, $today_sales, $note_adm, $note, $mb_balance,$mb_deposit);

                            iwol_process($bonus_day, $mb_id, $id2, $mb_name, 121, $hap2-$hap1, $note_adm2);
                            iwol_process($bonus_day, $mb_id, $id1, $mb_name, 122, 0, $note_adm3); //소실적 소멸
                        
                }
            }  //$hap1이 소실적이라면
            else if( $hap1>$hap2 ){ //$hap2가 소실적이라면

                $bonus = $hap2*$bonus_rate;

                if($bonus >= $limit_point && $limit_point!=0){ //소실적이 극점?

                    $today_sales=$bonus;
                    
                    echo " ▶▶ 수당 계산 2-1 (수당초과) :: 대실적-<strong>".$hap1."</strong>(".$id1.") ||  소실적-<strong>".$hap2."</strong>(".$id2.") ||  수당: <span class=blue>".($bonus_rate*100)."%</span> || 발생수당 : <strong>".Number_format($bonus)."</strong><br><br>";

                        $note_adm=' 소실적 발생 (대실적만 이월) (2-1-1) 대실적:'.$hap1.	'('.$id1.') ||  소실적:'.$hap2.'('.$id2.') | 이월금:'.($hap1-$hap2);
                        $note_adm2=' 대실적 이월 (2-1-1) 대실적:'.$hap1.'('.$id1.') | 이월금:'.($hap1-$hap2);
                        $note_adm3=' 소실적 소멸 (2-1-2) 소실적:'.$hap2.'('.$id2.') | 이월금: 0';
                        $note = $note." ".$id2;
                    
                        save_benefit($bonus_day, $mb_no, $mb_id, $mb_name, $grade, $mb_level, $recom, $bonus, $note_adm, $note, $mb_balance,$mb_deposit);

                            iwol_process($bonus_day, $mb_id, $id1, $mb_name, 211, $hap1-$hap2 , $note_adm2);
                            iwol_process($bonus_day, $mb_id, $id2, $mb_name, 212, 0, $note_adm3); //소실적 소멸
                        
                } else if($hap2 == 0){ //소실적이 0일때
                    
                    $today_sales=$bonus;

                    echo " ▶▶ 수당 계산 2-3 ::  대실적-<strong>".$hap1."</strong>(".$id1.") ||  소실적-<strong>".$hap2."</strong>(".$id2.") ||  수당: <span class=blue>".($bonus_rate*100)."%</span> || 발생수당 : <strong>".Number_format($today_sales)."</strong><br><br>";

                        $note_adm='소실적 0 (대실적만 이월) (2-3) 대실적:'.$hap1.	'('.$id1.') || 소실적:'.$hap2.'('.$id2.') | 이월금:'.($hap1-$hap2);

                        iwol_process($bonus_day, $mb_id, $id1, $mb_name, 23, $hap1-$hap2, $note_adm);
                        
                        
                }else{ //소실적이 극점x
                    
                    $today_sales=$bonus;

                    echo " ▶▶ 수당 계산 2-2 ::  대실적-<strong>".$hap1."</strong>(".$id1.") ||  소실적-<strong>".$hap2."</strong>(".$id2.") ||  수당: <span class=blue>".($bonus_rate*100)."%</span> ||  <span class=red>발생수당 :".$hap2.'*'.$bonus_rate.'= '.Number_format($today_sales)."</span><br><br>";
                       
                        $note_adm='소실적 발생 (대실적만 이월) (2-2-1) 대실적:'.$hap1.	'('.$id1.') || 소실적:'.$hap2.'('.$id2.') | 이월금:'.($hap1-$hap2);
                        $note_adm2=' 대실적 이월 (2-2-1) :'.$hap1.'('.$id1.') | 이월금:'.($hap1-$hap2);
                        $note_adm3=' 소실적 소멸 (2-2-2) :'.$hap2.'('.$id2.') | 이월금: 0';
                        $note = $note." ".$id2;

                        save_benefit($bonus_day, $mb_no, $mb_id, $mb_name, $grade, $mb_level, $recom, $today_sales, $note_adm, $note, $mb_balance,$mb_deposit);
                            
                            iwol_process($bonus_day, $mb_id, $id1, $mb_name, 221, $hap1-$hap2, $note_adm2);
                            iwol_process($bonus_day, $mb_id, $id2, $mb_name, 222, 0, $note_adm3); //소실적 소멸
                }

            }else if( $hap1=$hap2 ){ //$hap1 과 hap2 가 같다면

                    $today_sales=$hap2*$bonus_rate;

                    echo " ▶▶ 수당 계산 3 :: 대실적-<strong>".$hap1."</strong>(".$id1.") ||  소실적-<strong>".$hap2."</strong>(".$id2.") <br>";
                    
                        $note_adm=' 대소실적같음 소멸 (3-1-1) 대실적:'.$hap1.'('.$id1.') || 소실적:'.$hap2.'('.$id2.')';
                        $note_adm2=' 대소실적 소멸 (3-1-2) 대실적:'.$hap1.'('.$id1.') | 이월금: 0';
                        $note_adm3=' 대소실적 소멸 (3-1-3) 소실적:'.$hap2.'('.$id2.') | 이월금: 0';
                        $note = $note." ".$id2;

                        save_benefit($bonus_day, $mb_no, $mb_id, $mb_name, $grade, $mb_level, $recom, $today_sales, $note_adm, $note, $mb_balance,$mb_deposit);
                        
                        iwol_process($bonus_day, $mb_id, $id1, $mb_name, 311, 0 , $note_adm2);
                        iwol_process($bonus_day, $mb_id, $id2, $mb_name, 312, 0, $note_adm3); //소실적 소멸       
            }
        } // for

        $rec='';
        $today_sales=0;
        }
    } //for




// 본인 매출
function today_sales($mb_id, $day){
    
	$day_search = " and od_date = '$day'";
	$sql= sql_fetch("select sum(upstair)as hap from g5_shop_order where mb_id='".$mb_id."' $day_search");
	if($sql['hap']=='')
	{
		$hap=0;
	}else{
		$hap=$sql['hap'];
    }
	return $hap;
}


// 하부매출
function btoday_select($mb_id,$day){
	$res= sql_fetch("select today from brecom_bonus_today where mb_id='".$mb_id."' and day='".$day."'");
	if($res['today']=='')
	{
		$hap=0;
	}else{
		$hap=$res['today'];
	}
	return $hap;
}

//이월된 매출
function habu_iwol($mb_id,$day){
   
	$hap1=(btoday_select($mb_id,$day)+today_sales($mb_id,$day));  //자기매출과 하부매출을 합하여
	$res2= sql_fetch("select pv as hap from iwol where mb_id='".$mb_id."' order by iwolday desc limit 0,1");
    $hap2=$res2['hap'];
    
	echo '▷ '.$mb_id.'/'.$day.' 산하매출: '.btoday_select($mb_id,$day)." + 본인매출: ".today_sales($mb_id,$day).' + 이월매출:'.$hap2.' <br>';

	return ($hap1+$hap2);
	//return ($hap2);
}

// 하위매출 가져오기
function my_bchild($mb_id,$day){
    echo "<br><br><div class='title block' style='font-size:30px;padding:0 5px;'>".$mb_id."</div><br>";
    
	$id1='';
	$id2='';
	$hap1=0;
	$hap2=0;

	$res= sql_query("select mb_id from g5_member where mb_brecommend='".$mb_id."' order by mb_no");
	
	for ($j=0; $rrr=sql_fetch_array($res); $j++) {
		if($j==0){
			$id1=$rrr['mb_id'];
			$hap1=habu_iwol($id1, $day);
			if($hap1==''){ $hap1=0;}
		}
		if($j==1){
			$id2=$rrr['mb_id'];
			$hap2=habu_iwol($id2, $day);
			if($hap2==''){ $hap2=0;}
		}
	}
	
	return array($id1, $hap1, $id2, $hap2);
}



/* 이월 DB 저장 */
function iwol_process($bonus_day,$mb_brecommend, $mb_id, $mb_name, $kind, $pv, $note){
    global $debug;
    
	if( $pv>=0){
        
    }else{
        $pv = $pv * -1;
    }

    $temp_sql1 = " insert iwol set iwolday='".$bonus_day."'";
    $temp_sql1 .= " ,mb_id		= '".$mb_id."'";
    $temp_sql1 .= " ,mb_name		= '".$mb_name."'";
    $temp_sql1 .= " ,kind		= '".$kind."'";
    $temp_sql1 .= " ,pv		= '".$pv."'";
    $temp_sql1 .= " ,note		= '".$note."'";
    $temp_sql1 .= " ,mb_brecommend		= '".$mb_brecommend."'";

    
    if($pv == '0'){
        echo '<br><span class=black> ▶▶▶▶ 이월금소멸 : '.Number_format($pv).'</span> <span style=margin-left:20px>['.$note.']</span>';
    }else{
        echo '<br><span class=blue> ▶▶▶▶ 이월금 : '.Number_format($pv).'</span> <span style=margin-left:20px>['.$note.']</span>';
    }

    if($debug){
        echo "<code>";
        print_R($temp_sql1);
        sql_query($temp_sql1);
        echo "</code>";
    }else{
        sql_query($temp_sql1);
    }
	
}

function save_benefit($bonus_day, $mb_no, $mb_id, $mb_name, $grade, $mb_level, $recom, $today_sales,$rec_adm, $rec,$mb_balance,$mb_deposit){
    global $g5, $debug, $code,$bonus_rate, $bonus_limit;
    
    $benefit = $today_sales;

    list($mb_balance,$balance_limit,$benefit_limit,$mb_pv,$direct_LR) = bonus_limit_check($mb_id,$benefit,1);
    

    // 디버그 로그
    
        echo "<code>";
        echo "현재수당 : ".$mb_balance."  | 수당한계 :". $balance_limit.' | PV :'.$mb_pv.' | ';
        echo "발생할수당: ".$benefit." | 지급할수당 :".$benefit_limit;
        echo "</code>";
    

    // 수당제한
    // echo $mb_id." | ".Number_format($benefit).'*'.$bonus_rate;


    // 직추천인조건
    if($direct_LR < 1){
        list($L_recomm_count,$R_recomm_count) = brecom_recomm($mb_id);
        echo "<br><span>▶▶▶ 후원 산하 직추천인 :: ";
        echo "L : ".$L_recomm_count.' / R :'.$R_recomm_count;

        if($L_recomm_count > 0 && $R_recomm_count > 0){
            $recomm_condition = 1;
            echo "<span class='red'> === OK</span><br><br>";
        }else{
            $recomm_condition = 0;    
        }
    }else{
        $recomm_condition = 1;
    }

    if($recomm_condition > 0){
        if(($benefit > $benefit_limit && $balance_limit != 0) ){
            $rec_adm .= " | benefit overflow";
            echo "<span class=blue> ▶▶▶ 수당 지급 : ".Number_format($benefit)."</span>";
            echo "<span class=red> ▶▶▶ 수당 초과 (한계까지만 지급) : ".Number_format($benefit_limit)." </span><br>";
        }else if($mb_pv < $benefit){
            $rec_adm .= " | benefit overflow PV";
            echo "<span class=blue> ▶▶▶ 수당 지급 : ".Number_format($benefit)."</span>";
            $benefit_limit = $mb_pv;
            echo "<span class=red> ▶▶▶ PV 초과(일 상한제한) (한계까지만 지급) : ".Number_format($benefit_limit)." </span><br>";
        }else if($benefit != 0 && $balance_limit == 0 && $benefit_limit == 0){

            $rec_adm .= " | Sales zero";
            echo "<span class=blue> ▶▶▶ 수당 지급 : ".Number_format($benefit)."</span>";
            echo "<span class=red> ▶▶▶ 수당 초과 (기준매출없음) : ".Number_format($benefit_limit)." </span><br>";

        }else if($benefit == 0){
            echo "<span class=blue> ▶▶▶ 수당 미발생 </span>";

        }else{
            
            echo "<span class=blue> ▶▶▶ 수당 지급 : ".Number_format($benefit)."</span><br>";
        }
    }else{
        echo "<span class=red> ▶▶▶ 직추천수 미달 : ".Number_format($benefit)."</span><br><br>";
        $benefit_limit = 0;
    }

    if($benefit > 0 && $benefit_limit > 0){

        $record_result = soodang_record($mb_id, $code, $benefit_limit,$rec,$rec_adm,$bonus_day,$mb_no,$mb_level);

        if($record_result){
            $balance_up = "update g5_member set mb_balance = mb_balance + {$benefit_limit}, mb_3 = 1  where mb_id = '".$mb_id."'";

            // 디버그 로그
            if($debug){
                echo "<code>";
                print_R($balance_up);
                echo "</code>";
            }else{
                sql_query($balance_up);
            }
        }
    }
}



$brcomm_arr = [];

function brecom_recomm($mb_id,$grade_condition = 0,$sales_condition = 0){
    global $config,$brcomm_arr,$debug;
	$origin = $mb_id;

    // 후원 하부 L,R 구분
    list($leg_list,$cnt) = brecommend_direct($mb_id);

    if($cnt == 2){
        
        $L_member = $leg_list[0]['mb_id'];
        $R_member = $leg_list[1]['mb_id'];
        
        $brcomm_arr = [];
        array_push($brcomm_arr, $leg_list[0]);
        $manager_list_L = brecommend_array($L_member, 0);
        // echo "<br><br> L ::<br>";print_R($manager_list_L);
    
        $brcomm_arr = [];
        array_push($brcomm_arr, $leg_list[1]);
        $manager_list_R = brecommend_array($R_member, 0);
        // echo "<br><br> R ::<br>";print_R($manager_list_R);

       
        /* 하부 직추천인 확인 */
        // echo "<br><span>▶▶▶ 직추천인 :: ";
        list($L_recomm_array, $L_recomm_count) = array_Match_sort($manager_list_L,'mb_recommend',$origin);
        list($R_recomm_array, $R_recomm_count) = array_Match_sort($manager_list_R,'mb_recommend',$origin);

        return array($L_recomm_count,$R_recomm_count);
        

    }else if($cnt < 2){
        echo "<span>후원 L,R 라인 없음</span>";
    }else{
        echo "<span>후원인 초과</span>";
    }

}



// 후원인 하부 회원 
function brecommend_array($brecom_id, $count)
{
	global $brcomm_arr;

	// $new_arr = array();
	$b_recom_sql = "SELECT mb_id,grade,mb_rate,mb_save_point,mb_recommend,mb_brecommend_type from g5_member WHERE mb_brecommend='{$brecom_id}' ";
	$b_recom_result = sql_query($b_recom_sql);
	$cnt = sql_num_rows($b_recom_result);

	if ($cnt < 1) {
		// 마지막
	} else {
		++$count;
		while ($row = sql_fetch_array($b_recom_result)) {
			brecommend_array($row['mb_id'], $count);
            // print_R($count.' :: '.$row['mb_id'].' | type ::'.$row['grade']);
            // $brcomm_arr[$count]['id'] = $brecom_id;
            array_push($brcomm_arr, $row);
		}
	}
	return $brcomm_arr;
}


function brecommend_direct($mb_id){
   
    $down_leg = array();
    $sql = "SELECT mb_id,grade,mb_rate,mb_save_point,mb_recommend,mb_brecommend_type FROM g5_member where mb_brecommend = '{$mb_id}' AND mb_brecommend != '' ORDER BY mb_brecommend_type ASC ";
    $sql_result = sql_query($sql);
    $cnt = sql_num_rows($sql_result);

    while($result = sql_fetch_array($sql_result) ){
        array_push($down_leg,$result);
    } 
    return array($down_leg,$cnt);
}



// 배열정렬 + 지정값 이상 카운팅
function array_index_sort($list,$key,$average)
{
    $count = 0;
    $a = array_count_values(array_column($list,$key));

    foreach($a as $key => $value) {
        
        if($key >= $average){
            $count += intval($value);
        }
    }
    return array($a, $count);
}



// 배열정렬 + 지정값 매칭 카운팅
function array_Match_sort($list,$key,$Matchvalue)
{
    $count = 0;
    $a = array_count_values(array_column($list,$key));

    foreach($a as $key => $value) {
        
        if($key == $Matchvalue){
            $count = intval($value);
        }
    }
    return array($a, $count);
}
?>


<?include_once('./bonus_footer.php');?>

<?
if($debug){}else{
    $html = ob_get_contents();
    //ob_end_flush();
    $logfile = G5_PATH.'/data/log/'.$code.'/'.$code.'_'.$bonus_day.'.html';
    fopen($logfile, "w");
    file_put_contents($logfile, ob_get_contents());
}

?>