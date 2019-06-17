<?php
include_once('./_common.php');

$g5['title'] = "장바구니";
include_once(G5_MSHOP_PATH.'/_head.php');

$sql ="select * from `g5_shop_cart2` where mb_id = '$member[mb_id]' and od_id = '' ";
$row1 = sql_fetch($sql);

$sql ="select * from `g5_shop_item` where it_id = '$row1[it_id]'";
$it = sql_fetch($sql);

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
    font-size: 0.8em;
    width: 1.6em;
    border-radius: 3em;
    padding: .1em  .2em;
    line-height: 1.25em;
    border: 1px solid #333;
    display: inline-block;
    text-align: center;
	background-color:white;
	color:black;
  }

  
  .line{border-bottom:1px solid black;}

</style>


<form name="form" id="sod_bsk_list" class="" method="post" action="<?php echo G5_SHOP_URL; ?>/cartupdate.php">
<input type="hidden" name="mb_id" value="<?=$member['mb_id']?>">
<input type="hidden" name="p_title" value="<?=$p_title?>">
<input type="hidden" name="p_price" value="<?=$p_price?>">
<input type="hidden" name="w" value="u">


<div class="cart_box" style="margin:10px"> 
<table border="0" width="100%" style="background-color:white;padding:5%;font-size:18px;">
	<tr>
		<td align="center" colspan="2" class="line">
			<h2><?=$it[it_name]?></h2>
		</td>
	</tr>
	<?
		$sql ="select * from `g5_shop_cart2` where mb_id = '$member[mb_id]' and od_id = ''";
		$result = sql_query($sql);
		$sum = 0;
		$cnt = 0;

		for($i=0; $row=sql_fetch_array($result); $i++){
			$sum += $row[p_price]; 
			$cnt += $row[c_num];

			$sql_pr ="select * from `g5_shop_program` where p_idx = '$row[p_idx]'";
			$row2 = sql_fetch($sql_pr);
	?>
	<tr>
		<th align="left"><?=$row[p_title]?><br><span style="color:#ccc">기본 1인 <?=number_format($row2[p_price])?> 원</span>
			<input type="hidden" name="it_id[]" value="<?=$row['it_id']?>">
			<input type="hidden" name="c_idx[]" value="<?=$row['c_idx']?>">
		</th>
		<td  align="right"><span class="wish_del"><a href="<?php echo G5_SHOP_URL; ?>/wishupdate.php?w=d&amp;c_idx=<?php echo $row['c_idx']; ?>"><i class="fa fa-trash" aria-hidden="true"></i><span class="sound_only">삭제</span></a></span>
		</td>
	</tr>
	<tr>
		<td align="left" colspan="2">
		<input type="hidden" name="p_price[]" id="p_price_<?=$i?>" value="<?=$row[p_price]?>" size="3"  readonly style="border:0;">
		<span id="p_price_view_<?=$i?>"><?=number_format($row[p_price])?></span>원
		</td>

	</tr>
	<tr>
		<td align="center" colspan="2" >
			<input type="button" value=" - " onclick="cnt_btn('down','<?=$i?>','<?=$row2[p_price]?>');" style="width:28px;">
			<input type="text" name="c_num[]" id="amount_<?=$i?>" value="<?=$row[c_num]?>" style="width:40px;text-align:center"  readonly >
			<input type="button" value=" + " onclick="cnt_btn('up','<?=$i?>','<?=$row2[p_price]?>');" style="width:28px;">
		</td>
	</tr>
	<?
		}
	?>
	
	<tr>
		<td align="center" colspan="2">
			<?if($row1[it_id]){?>
				<br><a href="./item.php?it_id=<?=$row1[it_id]?>" onclick="" style="color:red;">+더 담으러 가기</a>
			<?}else{?>
				<br><a href="./list.php?ca_id=10" onclick="" style="color:red;">+더 담으러 가기</a>
			<?}?>
		</td>
	</tr>
</table>
	<?
		if($i != 0){
	?>
		 <div>
			<input type="hidden" name="sum" id="all_price" value="<?=$sum?>" size="3"  readonly style="border:0;">
			<button type="submit" style="height:40px;font-size:20px"><span id="all_cnt" class="span1"><?=$cnt?></span>&nbsp;<span id="all_price_view"><?=number_format($sum)?></span>원 주문하기</button>
		</div>
	<?
	}
	?>
		
		<div class="sit_ov_tbl">
				이 사이트는 서비스 정보중개자로서, 서비스 제공의 당사자가 아니라는 사실을 고지 하며, 서비스의 예약, 이용 및 환불 등과 관련된 의무와 책임은 각 서비스 제공자에게 있습니다.
		</div>
</div>
</form>
<script language="JavaScript">

//숫자 콤마 넣기
function numberWithCommas(x) {
    return x.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");
}
function cnt_btn (type,idx,pay) {

	var idx = idx;
	var pay = pay;//상품 기본가격

	var amount = $("#amount_"+idx).val();
	var sum =0;

	var type = type;
	pay = parseInt(pay);
	amount = parseInt(amount);

	if(type == "up"){
		amount = amount +1;
		$("#amount_"+idx).val(amount);

	}else{
		if(amount <=1){
			amount = 1;
		}else{
			amount = amount -1;
		}
		$("#amount_"+idx).val(amount);

	}

	sum = pay * amount;

	$("#p_price_"+idx).val(sum);
	$("#p_price_view_"+idx).html(numberWithCommas(sum));

	$("#all_cnt").html(amount);
	
	$("#all_price_view").html(numberWithCommas(sum));
	var tot_pay = 0;
	var pay_chk = 0;
	var tot_num = 0;
	var num_chk = 0;

	
	for(var c=0; c < "<?=$i?>"; c++){

		pay_chk = $("#p_price_"+c).val();
		pay_chk = parseInt(pay_chk);
		tot_pay = tot_pay + pay_chk;


		num_chk = $("#amount_"+c).val();
		num_chk = parseInt(num_chk);
		tot_num = tot_num + num_chk;
	}

	$("#all_cnt").html(tot_num);
	$("#all_price_view").html(numberWithCommas(tot_pay));
	$("#all_price").val(tot_pay);
}

</script>

<?php
include_once(G5_MSHOP_PATH.'/_tail.php');
?>