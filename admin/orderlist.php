<?php
$sub_menu = '400400';
include_once('./_common.php');

auth_check($auth[$sub_menu], "r");

$g5['title'] = '주문내역';
include_once (G5_ADMIN_PATH.'/admin.head.php');

$od_misu = preg_replace('/[^0-9a-z]/i', '', $od_misu);

$sql ="select count(*) as cnt from `g5_shop_order2` where 1";

$cnt = sql_fetch($sql);

$total_count = $cnt[cnt];

$page_rows = 5;

if(!$total_count) $total_count = 0;
if(!$page) {$page = 1;}

if($page < 1 ) {$page = 1; }

//$total_page = ceil($total_count / $page_row); 오타 인지 달름 page_rows 이거야함
$total_page = ceil($total_count / $page_rows); // 

$from_record = ($page - 1) * $page_rows;

$query = "select * from `g5_shop_order2` where 1 order by od_id desc limit {$from_record}, $page_rows";

$result = sql_query($query);


?>
<div class="tbl_head01 tbl_wrap">
    <table id="sodr_list">
		<caption>주문 내역 목록</caption>
		<thead>
		<tr>
			<th>주문번호</th>
			<th>사업자</th>
			<th>사업자 연락처</th>
			<th>주문자</th>
			<th>주문자 연락처</th>
			<th>주문금액</th>
			<th>미수금액</th>
			<th>요청사항</th>
			<th>상태</th>
			<th>주문일</th>
		</tr>
		</thead>
		<?for($i = 0; $row = sql_fetch_array($result); $i++){
			$row2 = sql_fetch("select * from g5_member where mb_id ='$row[od_name]' ");//사업자 정보
			$row3 = sql_fetch("select * from g5_member where mb_id ='$row[mb_id]' ");//주문자 정보

		?>
		<tr>
			<td>
				<a href="./oderView.php?od_id=<?=$row[od_id]?>"><?=$row[od_id]?></a>
			</td>
			<td><?=$row2[mb_name]?> (<?=$row[od_name]?>)</td>
			<td><?=$row2[mb_hp]?></td>
			<td><?=$row3[mb_name]?> (<?=$row[mb_id]?>)</td>
			<td><?=$row3[mb_hp]?></td>

			<td><?=number_format($row[od_cart_price])?></td>
			<td><?=number_format($row[od_misu])?></td>
			<td><?=$row[od_memo]?></td>
			<td><?=$row[od_status]?></td>
			<td><?=$row[od_time]?></td>

		</tr>
		<?}?>
	</table>
</div>
<!-- 페이지 -->

<?=get_paging($page_rows, $page, $total_page,'./orderlist.php?page='); ?>

<?php
include_once (G5_ADMIN_PATH.'/admin.tail.php');
?>
