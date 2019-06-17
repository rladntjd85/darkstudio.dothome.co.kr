<?php
$sub_menu = '400450';
include_once('./_common.php');

auth_check($auth[$sub_menu], "r");

$g5['title'] = '정산관리';
include_once (G5_ADMIN_PATH.'/admin.head.php');
include_once(G5_PLUGIN_PATH.'/jquery-ui/datepicker.php');

if(!$mb_id) $mb_id = $member[mb_id];

$sql_common = " from {$g5['member_table']} ";

$sql_search = " where mb_level > 2 and mb_id !='admin' ";


if ($fr_date && $to_date) {
    $date_sql = " and od_time between '$fr_date 00:00:00' and '$to_date 23:59:59' ";
}


if ($mb_level2) {
    $sql_search .= " and mb_level2 = '{$mb_level2}'";
}


if ($mb_name) {
    $mb_name_sql = " and mb_name like '%{$mb_name}%'";
}


//if ($is_admin != 'super')
//    $sql_search .= " and mb_level <= '{$member['mb_level']}' ";

if (!$sst) {
    $sst = "mb_1";
    $sod = "desc";
}

$sql_order = " order by {$sst}*1 {$sod} ";

$sql = " select count(*) as cnt {$sql_common} {$sql_search} {$mb_name_sql} {$sql_order} ";
$row = sql_fetch($sql);
$total_count = $row['cnt'];

$rows = $config['cf_page_rows'];
$rows = 100;
$total_page  = ceil($total_count / $rows);  // 전체 페이지 계산
if ($page < 1) $page = 1; // 페이지가 없으면 첫 페이지 (1 페이지)
$from_record = ($page - 1) * $rows; // 시작 열을 구함


$sql = " select * {$sql_common} {$sql_search} {$mb_name_sql}  {$sql_order} ";
$result = sql_query($sql);

for ($i=0; $row=sql_fetch_array($result); $i++){
	$sql_price = "SELECT mb_id, sum(od_cart_price) as od_cart_price , sum(od_send_cost) as od_send_cost , sum(od_send_cost2) as od_send_cost2, sum(od_receipt_price) as od_receipt_price , sum(od_misu) as od_misu  , sum(od_receipt_point) as od_receipt_point  , sum(od_refund_price) as od_refund_price FROM `g5_shop_order2` WHERE mb_id = '{$row[mb_id]}' {$date_sql} and od_status != '취소'";
	$row_price = sql_fetch($sql_price);
	$tot_orderprice    += ($row_price['od_cart_price'] + $row_price['od_send_cost'] + $row_price['od_send_cost2']);
	$tot_misu          += $row_price['od_misu'];


	$orderprice = $row_price[od_cart_price] + $row_price[od_send_cost] + $row_price[od_send_cost2];
	 $sql_up = "update  g5_member 	
							set mb_8 = '".$orderprice."' ,
							mb_9 = '{$row_price[od_receipt_price]}',  
							mb_10 = '{$row_price[od_misu]}'


					WHERE
						mb_id = '{$row[mb_id]}'
				";
	sql_query($sql_up);
}

 $sql = " select * from {$g5['member_table']}   {$sql_search} {$mb_name_sql}  {$sql_order} limit {$from_record}, {$rows} ";
$result = sql_query($sql);

?>


<div class="local_ov01 local_ov">

    <span class="btn_ov01"><span class="ov_txt">회원수</span><span class="ov_num"> <?php echo number_format($total_count); ?>명</span></span>

    <span class="btn_ov01"><span class="ov_txt">총 거래금액</span><span class="ov_num"> <?php echo number_format($tot_orderprice ); ?>원</span></span>
    <span class="btn_ov01"><span class="ov_txt">총 미수금액</span><span class="ov_num"> <?php echo number_format($tot_misu ); ?>원</span></span>




</div>

<form class="local_sch03 local_sch">


<?
if(!$to_date){
	$to_date =  date("Y-m-d");
}
?>
<div class="sch_last">
<!-- 	<button type="button" onclick="javascript:excel_down();" class="btn btn_03">엑셀다운</button>
	&nbsp;&nbsp; -->
	
	상호 : <input type="text" id="mb_name"  name="mb_name" value="<?php echo $mb_name; ?>" class="frm_input" size="10">&nbsp;&nbsp;
    날짜 : <input type="text" id="fr_date"  name="fr_date" value="<?php echo $fr_date; ?>" class="frm_input" size="10" maxlength="10"> ~
    <input type="text" id="to_date"  name="to_date" value="<?php echo $to_date; ?>" class="frm_input" size="10" maxlength="10">
	<input type="hidden" name="mb_id" value="<?=$mb_id?>">
    <input type="submit" value="검색" class="btn_submit">
</div>

<form name="forderlist" id="forderlist" onsubmit="return forderlist_submit(this);" method="post" autocomplete="off">
<input type="hidden" name="search_od_status" value="<?php echo $od_status; ?>">

<div class="tbl_head01 tbl_wrap">
    <table id="sodr_list">
    <caption>주문 내역 목록</caption>
    <thead>
	<tr>
		<th width="100">아이디</th>
		<th width="100">등급</th>
		<th width="100">지역</th>
		<th width="350">상호</th>
		<th width="150"><a href="./orderlist_result.php?sst=mb_17&sod=<?if($sod=="desc"){ echo"desc";}else{echo"desc";}?>&mb_id=<?=$mb_id?>">거래금액</a></th>
		<th width="150"><a href="./orderlist_result.php?sst=mb_18&sod=<?if($sod=="desc"){ echo"desc";}else{echo"desc";}?>&mb_id=<?=$mb_id?>">입금액</a></th>
		<th width="150"><a href="./orderlist_result.php?sst=mb_19&sod=<?if($sod=="desc"){ echo"desc";}else{echo"desc";}?>&mb_id=<?=$mb_id?>">미수금액</a></th>
		<th width="auto"></th>
	</tr>

    </thead>
    <tbody>
    <?php
    for ($i=0; $row=sql_fetch_array($result); $i++){


        $bg = 'bg'.($i%2);

 	
	$sql_price = "SELECT mb_id, sum(od_cart_price) as od_cart_price , sum(od_send_cost) as od_send_cost , sum(od_send_cost2) as od_send_cost2, sum(od_receipt_price) as od_receipt_price , sum(od_misu) as od_misu , sum(od_receipt_point) as od_receipt_point , sum(od_refund_price) as od_refund_price  FROM `g5_shop_order` WHERE mb_id = '{$row[mb_id]}'  {$date_sql} and od_status != '취소'";
	$row_price = sql_fetch($sql_price);

	$mb_addr1 = explode(" ",$row['mb_addr1']);
    ?>
	<tr class="orderlist<?php echo ' '.$bg; ?>">
		<td><?php echo $row['mb_id']; ?></td>
		<td><?php echo $row['mb_level2']; ?></td>
		<td> <?php echo $mb_addr1[0]; ?></td>
		<td class="td_left">
			<a href="./order_history_list.php?mb_id=<?php echo $row['mb_id']; ?>&fr_date=<?=$fr_date?>&amp;to_date=<?=$to_date?>" class="mng_mod btn btn_02"><span class="sound_only"></span><?php echo $row['mb_name']; ?> </a>
			
			
		</td>
		<td class="td_num_right"><?php echo number_format($row['mb_8']); ?></td>
		<td class="td_num_right"><?php echo number_format($row['mb_9']); ?></td>
		<td class="td_num_right"><?php echo number_format($row['mb_10']); ?></td>

		<td></td>

	</tr>

    <?php

    }

    if ($i == 0)
        echo '<tr><td colspan="12" class="empty_table">자료가 없습니다.</td></tr>';
    ?>
    </tbody>
    <tfoot>

    </tfoot>
    </table>
</div>

</form>

<?php echo get_paging(G5_IS_MOBILE ? $config['cf_mobile_pages'] : $config['cf_write_pages'], $page, $total_page, "{$_SERVER['SCRIPT_NAME']}?".$qstr."&amp;page=&amp;mb_id=".$mb_id."&amp;fr_date=".$fr_date."&amp;to_date=".$to_date); ?>

<script>
$(function(){

});
function momo_over(id,mb_id){
	$(".memo_over_box").hide();
	$("#"+id).fadeIn(0);

}
function momo_out(){
	$(".memo_over_box").hide();
}




function memo_load(mb_id){
//	$("#ajax_box").empty("");
//	$("#ajax_box").hide();
	var mb_id =mb_id;

	$.ajax({
		url:g5_url+"/adm/ajax_memo_list2.php",
		type:'POST',
		cache: false,
		async: true,
		data: {mb_id : mb_id },
		 dataType : 'html',
		success:function(html){
	$(".memo_over_box").hide();
			$("#mask").fadeIn(0);

			$("#ajax_box").fadeIn(0);
			$("#ajax_box").html(html);
			$("#memo_w").val('');
			$("#mo_memo").val('');
			$("#mo_idx").val('');
			$('html, body').css({'overflow': 'hidden', 'height': '100%'});

		},
		error:function(jqXHR, textStatus, errorThrown){
		}
	});
}



</script>
<script>
$(function(){
    $("#fr_date, #to_date").datepicker({ changeMonth: true, changeYear: true, dateFormat: "yy-mm-dd", showButtonPanel: true, yearRange: "c-99:c+99", maxDate: "+0d" });

});
</script>
<?php
include_once (G5_ADMIN_PATH.'/admin.tail.php');
?>
