<?php
include_once('./_common.php');

$g5['title'] = "배달정보";
include_once(G5_MSHOP_PATH.'/_head.php');
?>

<?php
include_once('./_common.php');

$tablet_size = "1.0"; // 화면 사이즈 조정 - 기기화면에 맞게 수정(갤럭시탭,아이패드 - 1.85, 스마트폰 - 1.0)

?>

<style>
.cart_box div {
	overflow:hidden;
	margin:20px 20px;
	text-align:center;
}
.cart_box button {
	width:98%;
	height:30px;
	border:0px;
	background:red;
	color:#fff;
	
}
.cart_box h2 {
	font-size:20px;
}

.span1 {
    font-size: 1em;
    width: 2em;
    border-radius: 3em;
    padding: .1em  .2em;
    line-height: 1.25em;
    border: 1px solid #333;
    display: inline-block;
    text-align: center;
	background-color:white;
	color:black;
  }

  input {
			width: 100%;
			padding: 10px 20px;
			margin: 5px 0;
			box-sizing: border-box;
		}
		input[type="text"] {
			border: solid 2px black;
			border-radius: 8px;
		}

</style>


<form name="form" id="sod_bsk_list" class="" method="post" action="<?php echo G5_SHOP_URL; ?>/orderformUpdate2.php">
<input type="hidden" name="mb_id" value="<?=$member['mb_id']?>">
<input type="hidden" name="p_title" value="<?=$p_title?>">
<input type="hidden" name="p_price" value="<?=$p_price?>">
<input type="hidden" name="p_idx" value="<?=$p_idx?>">
<input type="hidden" name="tot_ct_price" value="<?=$sum?>">
<input type="hidden" name="od_status" value="주문">

<div class="cart_box" style="margin:10px"> 
<table border="0" width="100%" style="background-color:white;padding:5%;font-size:18px;">
	<?
		$sql ="select * from `g5_shop_cart2` where mb_id = '$member[mb_id]' and od_id = ''";
		$row = sql_fetch($sql);

		$sql_pr ="select * from `g5_shop_item` where it_id = $row[it_id]";

		$row2 = sql_fetch($sql_pr);
	?>
	<tr>
		<td align="center" colspan="2" class="line">
			<h2><?=$row2[it_name]?></h2>
		</td>
	</tr>
	<?
		$result = sql_query($sql);
		$sum = 0;

		for($i=0; $row=sql_fetch_array($result); $i++){
			$sum += $row[p_price]; 
	?>
	<tr >
		<th align="left">
			<?=$row[p_title]?>
			<input type="hidden" name="it_id" value="<?=$row[it_id]?>">
		</th>
		<td align="right">
			 <?=number_format($row[c_num])?>개 <?=number_format($row[p_price])?>원
		</td>
		
	</tr>
	
	<?
		}
	?>
	<tr>
		<td colspan="2">
			<input type="text" name="od_addr1" value="<?=$member[mb_addr1]?>" placeholder="주소" required>
		</td>
	</tr>
	<tr>
		<td colspan="2">
			<input type="text" name="od_addr2" value="<?=$member[mb_addr2]?>" placeholder="주소" required>
		</td>
	</tr>
	<tr>
		<td colspan="2">
			<input type="text" name="od_addr3" value="<?=$member[mb_addr3]?>" placeholder="상세주소" required>
		</td>
	</tr>
	<tr>
		<td colspan="2">
			<input type="text" name="od_hp" value="<?=$member[mb_hp]?>" placeholder="연락처" required>
		</td>
	</tr>
	<tr>
		<th colspan="2">요청사항</th>
	</tr>
	<tr>
		<td colspan="2" align="center">
			<input type="text" name="od_memo" value=""  placeholder="요청사항을 적어주세요">
		</td>
	</tr>
</table>
	<?
		if($i != 0){
	?>
		 <div>
			<button type="submit" style="height:40px;font-size:20px"><?=number_format($sum)?>원 주문하기</button>
			<input type="hidden" name="tot_ct_price" value="<?=$sum?>">
		</div>
	<?
	}
	?>
	<div class="sit_ov_tbl">
			이 사이트는 서비스 정보중개자로서, 서비스 제공의 당사자가 아니라는 사실을 고지 하며, 서비스의 예약, 이용 및 환불 등과 관련된 의무와 책임은 각 서비스 제공자에게 있습니다.
	</div>
</form>
</div>
<script language="JavaScript">


</script>

<?php
include_once(G5_MSHOP_PATH.'/_tail.php');
?>