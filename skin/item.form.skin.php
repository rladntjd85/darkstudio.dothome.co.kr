<?php
if (!defined('_GNUBOARD_')) exit; // 개별 페이지 접근 불가

// add_stylesheet('css 구문', 출력순서); 숫자가 작을 수록 먼저 출력됨
add_stylesheet('<link rel="stylesheet" href="'.G5_SHOP_CSS_URL.'/style.css">', 0);

//후기 작성 카운트
$sql_use = "SELECT count(*) as cnt FROM `g5_shop_item_use` where  it_id = '$it[it_id]' ";
$row_use = sql_fetch($sql_use);
$use_cnt = $row_use[cnt];

//좋아요수
$sql_wish = "select count(*) as num from {$g5['g5_shop_wish_table']} where it_id = '$it[it_id]' ";
$row_wish = sql_fetch($sql_wish);
$wish  = (int)$row_wish['num'];


$sql_wish2 = "select count(*) as num from {$g5['g5_shop_wish_table']} where it_id = '$it[it_id]' and mb_id ='$member[mb_id]' ";
$row_wish2 = sql_fetch($sql_wish2);
$wish2  = (int)$row_wish2['num'];

//좋아요수
$sql_my_wish = "select count(*) as cnt from {$g5['g5_shop_wish_table']} where it_id = '$it[it_id]' and mb_hp = '$ss_mb_hp'";
$row_my_wish = sql_fetch($sql_my_wish);
$my_wish = $row_my_wish[cnt];
?>

<form name="fitem" action="<?php echo G5_SHOP_URL; ?>/wishlist.php" method="post" onsubmit="return fitem_submit(this);">
<input type="hidden" name="it_id[]" value="<?php echo $it['it_id']; ?>">
<input type="hidden" name="sw_direct">
<input type="hidden" name="url">
<input type="hidden" name="it_name1" value="<?=$it['it_name']?>">
<!-- slick 슬라이더 css js 로드-->
<link rel="stylesheet" type="text/css" href="<?=G5_CSS_URL?>/slick.css">
<link rel="stylesheet" type="text/css" href="<?=G5_CSS_URL?>/slick-theme.css">
<script src="<?=G5_JS_URL?>/slick.min.js" type="text/javascript" charset="utf-8"></script>
<style type="text/css">
* {
  box-sizing: border-box;
}
.slider {
	width: 100%;
	margin:0;
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
<script type="text/javascript">
$(document).on('ready', function() {
	$('.one-time').slick({
		dots: true,
		infinite: true,
		slidesToShow: 1,
		autoplay: true, //자동플레이 유무( false시 자동플레이 안됨 )
		autoplaySpeed:1500, 
	});
});
</script>

<style>
.addr_leyer_box2{
	width:60%;height:40%;z-index:2500;position:fixed;top: 50%;left: 50%;overflow:hidden;
	margin-top:-25%;
	margin-left:-30%; 
	display:none;
	color:#000;
}
.leyer_box2 {
	border:1px solid #fff;width:100%;overflow:hidden;	background:#fff;

}
.leyer_box2 a{
	color:#000;
}

.leyer_box2 li{
	color:#000;
	font-size:26px;
	font-size:14px;
	padding:20px;
}
.leyer_box2 h2{
	font-size:20px;text-align:center;
}
.leyer_box2 li {
	border-bottom:1px solid #ccc;
}
.leyer_box2 li:last-child {
	border-bottom:0;
}
.addr_leyer_box2 .leyer_close2{
	width:100%;height:30px;color:#fff;text-align:right;font-size:20px;padding-right:10px;
}
.addr_leyer_box2 .leyer_close2 span{
	cursor:pointer;
}

</style>

<div class="mask" id="mask"></div>
<div class="addr_leyer_box2" id="addr_leyer_box2">
	<div class="leyer_close2" ><span onclick="leyer_close2()">X</span></div>
	<div  class="leyer_box2">
		<li><a href="<?=G5_SHOP_URL?>/map_view.php?it_id=<?=$it_id?>">지도 보기</a></li>
	</div>
</div>
<script>
function leyer_close2(){
 $("#addr_leyer_box2").fadeOut(200);
 $("#mask").fadeOut(200);
}
function addr_slelct_leyer2(){
 $("#addr_leyer_box2").fadeIn(200);
 $("#mask").fadeIn(200);
}

</script>
<div class="view_txt_wrap">
		<div class="list_txt">
<!-- 전체 div 시작  -->
	<!-- 슬라이더 이미지 시작 -->
	<section class="one-time slider" >
	<?
	$thumb_img_w = 480; // 넓이
	$thumb_img_h = 280; // 높이
	for ($i=1; $i<=30; $i++){
		if(!$it['it_img'.$i])
			continue;
		$thumb = get_it_thumbnail($it['it_img'.$i], $thumb_img_w, $thumb_img_h);
		if($thumb){
			?>
			<div><?=$thumb?></div>
			<?
		}
	}
	?>
	</section>
	<!-- 슬라이더 이미지 끝 -->

			<h2><?php echo get_text($it[it_name]); ?></h2>
			<p><?php echo get_text($it[it_addr]); ?></p>
			<div class="score">
				<li>
					<!-- 별점 시작-->
					<?if($score = get_star_image($it['it_id'])){ ?>
					<i class="fa fa-star"></i>
					<!-- 점수,(총수) -->
					<?php echo $score*2?>
					<?php } ?>
					<!-- 별점 끝-->
				</li>
				<!-- 후기 수 -->
				<li><i class="fa fa-comment"></i> <?php echo $use_cnt; ?></li>
				<!-- 좋아요 수 하트 이미지 온 오프 2개필요 -->
				<li>
					<?if($wish2 == 0){
						$w = "";
					}else{
						$w = "d";
					}
					?>
					<a href="<?php echo G5_SHOP_URL; ?>/wishupdate.php?it_id=<?=$it_id?>&item_page=Y&w=<?=$w?>" >
					<i class="fa fa-heart"></i> <?php echo $wish; ?>
					</a>
				</li>
			</div>
		</div>
	</div>

	
	<!-- 이미지 없을경우 -->
	<?if(!$thumb){?>
	<div><img src="<?=G5_IMG_URL?>/no_img.png" width="100%"></div>
	<?}?>


    <section id="sit_ov" class="2017_renewal_itemform">
        <h2>이벤트 내용</h2>
        <div class="sit_ov_wr">
           <!--  <strong id="sit_title">쿠폰이벤트</strong>
            			<?php //echo stripslashes($it['it_name']); ?>
            
            <?php if($it['it_mobile_head_html']) { ?><p id="it_mobile_head_html"><?php echo $it['it_mobile_head_html']; ?></p><?php } ?>
            <?php if($is_orderable) { ?>
            <?php } ?> -->


			<?
				$sql_pr ="select * from `g5_shop_program` where it_id = '$it_id'";
				$result = sql_query($sql_pr);

			?>

		<!-- 프로그램 정보 시작-->
		<div class="view_txt">
			<p style="color:#000;font-size:20px;"><b>대표메뉴</b></p>
			<?
				for($i=0; $row=sql_fetch_array($result); $i++){
			?>
			 
				<div class="view_price_wrap">
				<a href="<?=G5_SHOP_URL?>/cart.php?it_id=<?=$it['it_id']?>&p_idx=<?=$row['p_idx']?>&p_title=<?=$row['p_title']?>&p_price=<?=$row['p_price']?>&it_name1=<?=$it[it_name]?>">
					<div class="view_price_left">
						<p style="color:#000;font-size:20px;"><?php echo get_text($row[p_title]); ?></p>
					</div>
					<div class="view_price_right">
						<div class="txt_right">
							<div class="sail_wrap">
								<li class="sail_price"><?php echo number_format($row[p_cust_price]); ?>원</li>
								<li class="flag"><?=get_text($row[p_basic]); ?> &nbsp;&nbsp;</li>
							</div>
							<div class="price"><?php echo number_format($row[p_price]); ?>원</div>
						</div>
					</div>
					 </a>
				</div>
		<!-- 프로그램 정보 끝-->
			<?
				}
			?>
			<div class="sit_ov_wr">
				 <?php if($it['it_mobile_tail_html']) { ?><p id="it_mobile_tail_html"><?php echo $it['it_mobile_tail_html']; ?></p><?php } ?>
			 </div>
<!-- 			 <div class="sit_ov_wr">
			 				<?for($i = 1; $i < 9; $i++){?>
			 					<span><?=$it[it_.$i]; ?></span>
			 				<?}?>
			 </div> -->
 
  <div style="width:100%;overflow:hidden;text-align:center;height:42px;">
	 <button id="sit_btn_wish" style="width:100%;height:42px;background:red;font-size:20px;color:#fff;">장바구니</button>
 </div>
    </section>
</div>

<div class="sit_ov_tbl">
	이 사이트는 서비스 정보중개자로서, 서비스 제공의 당사자가 아니라는 사실을 고지 하며, 서비스의 예약, 이용 및 환불 등과 관련된 의무와 책임은 각 서비스 제공자에게 있습니다.
</div>

<div id="sit_tab">
    <ul class="tab_tit">
        <li style="width:33%"><button type="button" rel="#sit_inf" class="selected">상품정보</button></li>
        <li style="width:34%"><button type="button" rel="#sit_use">사용후기</button></li>
        <li style="width:33%"><button type="button" rel="#sit_qa">상품문의</button></li>
    </ul>
    <ul class="tab_con">

        <!-- 상품 정보 시작 { -->
        <li id="sit_inf">
			<div id="itemuse"><?=$it[it_shop_memo]?></div>
        </li>
        <!-- 사용후기 시작 { -->
        <li id="sit_use">
            <h2>사용후기</h2>

            <div id="itemuse"><?php include_once(G5_SHOP_PATH.'/itemuse.php'); ?></div>
        </li>
        <!-- } 사용후기 끝 -->

        <!-- 상품문의 시작 { -->
        <li id="sit_qa">
            <h2>상품문의</h2>

            <div id="itemqa"><?php include_once(G5_SHOP_PATH.'/itemqa.php'); ?></div>
        </li>
        <!-- } 상품문의 끝 -->

    </ul>
</div>
<script>
$(function (){
    $(".tab_con>li").hide();
    $(".tab_con>li:first").show();   
    $(".tab_tit li button").click(function(){
        $(".tab_tit li button").removeClass("selected");
        $(this).addClass("selected");
        $(".tab_con>li").hide();
        $($(this).attr("rel")).show();
    });
});
</script>
</form>



<script>
$(window).bind("pageshow", function(event) {
    if (event.originalEvent.persisted) {
        document.location.reload();
    }
});

</script>
<?php /* 2017 리뉴얼한 테마 적용 스크립트입니다. 기존 스크립트를 오버라이드 합니다. */ ?>
<script src="<?php echo G5_JS_URL; ?>/shop.override.js"></script>