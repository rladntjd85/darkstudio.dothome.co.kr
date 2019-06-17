<?php
include_once('./_common.php');

if (!$is_member)
	goto_url(G5_BBS_URL."/login.php?url=".urlencode($_SERVER['HTTP_REFERER']));

$g5['title'] = '삼품담기';
include_once(G5_MSHOP_PATH.'/_head.php');
$sql = "select * from `g5_shop_cart2` where mb_id = '$mb_id'";
$it = sql_fetch($sql);

?>
<!-- slick 슬라이더 css  -->
<link rel="stylesheet" type="text/css" href="<?=G5_CSS_URL?>/slick.css">
<link rel="stylesheet" type="text/css" href="<?=G5_CSS_URL?>/slick-theme.css">
<style type="text/css">
* {
  box-sizing: border-box;
}
.slider {
	width: 100%;
	margin:0;
	display: block; margin: 0px auto; 
}
.slick-slide img {
  width: 100%;
}
.slick-prev:before,
.slick-next:before {
  color: black;
  display:none;
}


</style>

<!-- slick 슬라이더 js  -->
<script src="<?=G5_JS_URL?>/slick.min.js" type="text/javascript" charset="utf-8"></script>
<script type="text/javascript">
$(document).on('ready', function() {
	$('.one-time').slick({
		dots: true,
		infinite: true,
		slidesToShow: 1,
		autoplay: true, //자동플레이 유무( false시 자동플레이 안됨 )
		autoplaySpeed:3000, 

	});
});
</script>

<div id="sit_ov_wrap">
    <?php
    // 이미지(중) 썸네일
    $thumb_img = '';
    $thumb_img_w = 480; // 넓이
    $thumb_img_h = 280; // 높이
    for ($i=1; $i<=10; $i++)
    {
        if(!$it['it_img'.$i])
            continue;

        $thumb = get_it_thumbnail($it['it_img'.$i], $thumb_img_w, $thumb_img_h);

        if(!$thumb)
            continue;

        $thumb_img .= '<li>';
        $thumb_img .= $thumb;
        $thumb_img .= '</li>'.PHP_EOL;
    }
    if ($thumb_img)
    {
        echo '<div id="sit_pvi">'.PHP_EOL;
        echo '<ul class="one-time slider" style="width:'.$thumb_img_w.'px;height:'.$thumb_img_h.'px">'.PHP_EOL;
        echo $thumb_img;
        echo '</ul>'.PHP_EOL;
        echo '</div>';
    }

    ?>
</div>
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

.cart_box p {
	width:98%;
	height:30px;
	border:0px;
	background:red;
	color:#fff;
}

.line{border-bottom:1px solid black;}

</style>


<form name="form" id="sod_bsk_list" class="2017_renewal_itemform" method="post" action="<?php echo $cart_action_url; ?>">
<input type="hidden" name="mb_id" value="<?=$member['mb_id']?>">
<input type="hidden" name="p_title" value="<?=$p_title?>">
<input type="hidden" name="w" value="">
<input type="hidden" name="c" value="1">
<input type="hidden" name="p_price" value="<?=$p_price?>">
<input type="hidden" name="p_idx" value="<?=$p_idx?>">
<input type="hidden" name="it_id" value="<?=$it_id?>">
<div class="cart_box" style="margin:10px"> 
<table border="0" width="100%" style="background-color:white;padding:5%;font-size:18px;">
	<tr>
		<td align="center" colspan="2" class="line">
			<h2><?=$it_name1?></h2>
		</td>
	</tr>
	<tr>
		<th align="center" colspan="2"><br><?=$p_title?></th>
	</tr>
	<tr>
		<th align="left">기본</td>
		<td align="right"><?=number_format($p_price)?>원</td>
	</tr>
	<tr>
		<th align="left">수량</td>
		<th align="right">
			<input type="button" value=" - " onclick="cnt_btn('down');">
			<input type="text" name="c_num" id="amount" value="1" size="2" onchange="change();" readonly style="width:40px;text-align:center">
			<input type="button" value=" + " onclick="cnt_btn('up');">
		</th>
	</tr>
</table>
<?
	$sql_it ="select * from `g5_shop_cart2` where mb_id = '$member[mb_id]' and od_id = ''";
	$row_it = sql_fetch($sql_it);

	$sql ="select count(*) as cnt from `g5_shop_cart2` where mb_id = '$member[mb_id]' and od_id = ''";
	$row = sql_fetch($sql);

	//it_id가 같고 장바구니에 아이템이 없거나 있어도 it_id가 같으면 떠라
	if($it_id == $row_it[it_id] or $row[cnt] == 0){
?>
<div>
	<button type="submit" style="height:40px;font-size:20px"><span id="all_cnt">1</span> 개 담기 <span id="all_price"><?=number_format($p_price)?></span>원</button>
</div>
<?}else if($it_id != $row[it_id]){?>
<!-- 장바구니에는 같은 가게의 메뉴만 담을 수 있습니다. 이전에 담은 메뉴가 삭제됩니다.(삭제 후 현재 메뉴 담기) -->
<div>
	<a href="./cartdelete.php?it_id=<?=$row_it[it_id]?>&new_it_id=<?=$it_id?>&mb_id=<?=$member['mb_id']?>" ><p style="height:40px;font-size:20px" onclick="if(!confirm('장바구니에는 같은 가게의 메뉴만 담을 수 있습니다. 이전에 담은 메뉴가 삭제됩니다.')){return false;}"><span id="all_cnt">1</span> 개 담기 <span id="all_price"><?=number_format($p_price)?></span>원</p></a>

</div>
<?}?>
</div>
</form>
<script language="JavaScript">

function cnt_btn (type) {
	var price ="<?=$p_price?>";
	var amount = $("#amount").val();
	var sum =0;
	var type = type;

	price = parseInt(price);
	amount = parseInt(amount);

	if(type == "up"){
		amount = amount +1;
		$("#amount").val(amount);
	}else{
		if(amount <=1){
			amount = 1;
		}else{
			amount = amount -1;
		}
		$("#amount").val(amount);
	}

	sum = price * amount;

	$("#all_cnt").html(amount);
	$("#all_price").html(sum);
}

</script>

<?php
include_once(G5_MSHOP_PATH.'/_tail.php');
?>