<?php
include_once(G5_EDITOR_LIB);
include_once(G5_LIB_PATH.'/iteminfo.lib.php');
include_once(G5_PATH.'/adm/shop_admin/admin.shop.lib.php');



include_once(G5_PATH.'/head.sub.php');
echo '<link rel="stylesheet" href="'.G5_ADMIN_URL.'/css/admin.css">'.PHP_EOL;


//include_once(G5_PATH.'/adm/admin.lib.php');

// 입력 폼 안내문
function help($help="")
{
    global $g5;

    $str  = '<span class="frm_info">'.str_replace("\n", "<br>", $help).'</span>';

    return $str;
}


//auth_check($auth[$sub_menu], "w");

$html_title = "상품 ";

if ($w == "")
{
    $html_title .= "입력";

    // 옵션은 쿠키에 저장된 값을 보여줌. 다음 입력을 위한것임
    //$it[ca_id] = _COOKIE[ck_ca_id];
    $it['ca_id'] = get_cookie("ck_ca_id");
    $it['ca_id2'] = get_cookie("ck_ca_id2");
    $it['ca_id3'] = get_cookie("ck_ca_id3");
    if (!$it['ca_id'])
    {
        $sql = " select ca_id from {$g5['g5_shop_category_table']} order by ca_order, ca_id limit 1 ";
        $row = sql_fetch($sql);
        if (!$row['ca_id'])
            alert("등록된 분류가 없습니다. 우선 분류를 등록하여 주십시오.", './categorylist.php');
        $it['ca_id'] = $row['ca_id'];
    }
    //$it[it_maker]  = stripslashes($_COOKIE[ck_maker]);
    //$it[it_origin] = stripslashes($_COOKIE[ck_origin]);
    $it['it_maker']  = stripslashes(get_cookie("ck_maker"));
    $it['it_origin'] = stripslashes(get_cookie("ck_origin"));
}
else if ($w == "u")
{
    $html_title .= "수정";

    if ($is_admin != 'super')
    {
        $sql = " select it_id from {$g5['g5_shop_item_table']} a, {$g5['g5_shop_category_table']} b
                  where a.it_id = '$it_id'
                    and a.ca_id = b.ca_id
                    and b.ca_mb_id = '{$member['mb_id']}' ";
        $row = sql_fetch($sql);
        if ($row['it_id'])
            alert("\'{$member['mb_id']}\' 님께서 수정 할 권한이 없는 상품입니다.");
    }

    $sql = " select * from {$g5['g5_shop_item_table']} where it_id = '$it_id' ";
    $it = sql_fetch($sql);

    if(!$it)
        alert('상품정보가 존재하지 않습니다.');

    if (!$ca_id)
        $ca_id = $it['ca_id'];

    $sql = " select * from {$g5['g5_shop_category_table']} where ca_id = '$ca_id' ";
    $ca = sql_fetch($sql);
}
else
{
    alert();
}

$qstr  = $qstr.'&amp;sca='.$sca.'&amp;page='.$page;

$g5['title'] = $html_title;
//include_once (G5_ADMIN_PATH.'/admin.head.php');

// 재입고알림 설정 필드 추가
if(!sql_query(" select it_stock_sms from {$g5['g5_shop_item_table']} limit 1 ", false)) {
    sql_query(" ALTER TABLE `{$g5['g5_shop_item_table']}`
                    ADD `it_stock_sms` tinyint(4) NOT NULL DEFAULT '0' AFTER `it_stock_qty` ", true);
}

// 추가옵션 포인트 설정 필드 추가
if(!sql_query(" select it_supply_point from {$g5['g5_shop_item_table']} limit 1 ", false)) {
    sql_query(" ALTER TABLE `{$g5['g5_shop_item_table']}`
                    ADD `it_supply_point` int(11) NOT NULL DEFAULT '0' AFTER `it_point_type` ", true);
}

// 상품메모 필드 추가
if(!sql_query(" select it_shop_memo from {$g5['g5_shop_item_table']} limit 1 ", false)) {
    sql_query(" ALTER TABLE `{$g5['g5_shop_item_table']}`
                    ADD `it_shop_memo` text NOT NULL AFTER `it_use_avg` ", true);
}

// 지식쇼핑 PID 필드추가
// 상품메모 필드 추가
if(!sql_query(" select ec_mall_pid from {$g5['g5_shop_item_table']} limit 1 ", false)) {
    sql_query(" ALTER TABLE `{$g5['g5_shop_item_table']}`
                    ADD `ec_mall_pid` varchar(255) NOT NULL AFTER `it_shop_memo` ", true);
}

$pg_anchor ='<ul class="anchor">
<li><a href="#anc_sitfrm_ini">업체정보</a></li>
<li><a href="#anc_sitfrm_img">업체이미지</a></li>
</ul>
';


// 쿠폰적용안함 설정 필드 추가
if(!sql_query(" select it_nocoupon from {$g5['g5_shop_item_table']} limit 1", false)) {
    sql_query(" ALTER TABLE `{$g5['g5_shop_item_table']}`
                    ADD `it_nocoupon` tinyint(4) NOT NULL DEFAULT '0' AFTER `it_use` ", true);
}


?>
<script src="http://dmaps.daum.net/map_js_init/postcode.v2.js"></script>
<script src="//dapi.kakao.com/v2/maps/sdk.js?appkey=e22964d26bb9c740c95419f26d92e5d9&libraries=services"></script>

<form name="fitemform" action="./itemformupdate.php" method="post" enctype="MULTIPART/FORM-DATA" autocomplete="off" onsubmit="return fitemformcheck(this)">

<input type="hidden" name="codedup" value="<?php echo $default['de_code_dup_use']; ?>">
<input type="hidden" name="w" value="<?php echo $w; ?>">
<input type="hidden" name="sca" value="<?php echo $sca; ?>">
<input type="hidden" name="sst" value="<?php echo $sst; ?>">
<input type="hidden" name="sod"  value="<?php echo $sod; ?>">
<input type="hidden" name="sfl" value="<?php echo $sfl; ?>">
<input type="hidden" name="stx"  value="<?php echo $stx; ?>">
<input type="hidden" name="page" value="<?php echo $page; ?>">
<input type="hidden" name="ca_id" value="10">

<section id="anc_sitfrm_ini">
    <h2 class="h2_frm">기본정보</h2>
    <?php echo $pg_anchor; ?>
    <div class="tbl_frm01 tbl_wrap">
        <table>
        <caption>기본정보 입력</caption>
        <colgroup>
            <col class="grid_4">
            <col>
            <col class="grid_3">
        </colgroup>
        <tbody>
        <tr>
            <th scope="row">상품코드</th>
            <td colspan="2">
                <?php if ($w == '') { // 추가 ?>


                   
                    <input type="hidden" name="it_id" value="<?php echo time(); ?>" id="it_id" required class="frm_input required" size="20" maxlength="20"><?php echo time(); ?>

                <?php } else { ?>
                    <input type="hidden" name="it_id" value="<?php echo $it['it_id']; ?>">
                    <span class="frm_ca_id"><?php echo $it['it_id']; ?></span>
                    <a href="<?php echo G5_ADMIN_URL; ?>/shop_admin/itemuselist.php?sfl=a.it_id&amp;stx=<?php echo $it_id; ?>" class="btn_frmline">사용후기</a>
                <?php } ?>
            </td>
        </tr>
        <tr>
            <th scope="row"><label for="it_name">업체명</label></th>
            <td colspan="2">
                <?php echo help("HTML 입력이 불가합니다."); ?>
                <input type="text" name="it_name" value="<?php echo get_text(cut_str($it['it_name'], 250, "")); ?>" id="it_name" required class="frm_input required" size="95">
            </td>
        </tr>
        <tr>
            <th scope="row"><label for="it_basic">LIST 설명문구</label></th>
            <td>
                <input type="text" name="it_basic" value="<?php echo get_text($it['it_basic']); ?>" id="it_basic" class="frm_input" size="95">
            </td>
        </tr>
		<tr>
            <th scope="row"><label for="it_use">사용</label></th>
            <td>
                <?php echo help("체크를 해제해 놓으면 출력되지 않습니다."); ?>
                <input type="checkbox" name="it_use" value="1" id="it_use" <?php echo ($it['it_use']) ? "checked" : ""; ?>> 예
            </td>
        </tr>
		<tr>
            <th scope="row"><label for="it_addr">주소</label></th>
            <td>
				x : <input type="text" name="it_lat_y"  id="it_lat_y" placeholder="x" value="<?=$it['it_lat_y']?>">
				&nbsp;&nbsp;&nbsp;
				y : <input type="text" name="it_lat_x"  id="it_lat_x" placeholder="y" value="<?=$it['it_lat_x']?>">
				<Br><Br>
				<input type="text" name="it_addr" value="<?=$it['it_addr']?>" id="it_addr" placeholder="주소" class="frm_input" size="95">&nbsp;&nbsp;<input type="button" onclick="sample5_execDaumPostcode()" value="주소 검색" class="btn_frmline" style=" cursor:pointer">
				<Br><Br>
				<input type="text" name="it_addr2" value="<?=$it['it_addr2']?>" id="it_addr2" placeholder="나머지 주소" class="frm_input" size="95">
				<br>
				<div id="map" style="width:300px;height:300px;margin-top:10px;display:none"></div>

				<script>
					var mapContainer = document.getElementById('map'), // 지도를 표시할 div
						mapOption = {
							center: new daum.maps.LatLng(37.537187, 127.005476), // 지도의 중심좌표
							level: 5 // 지도의 확대 레벨
						};

					//지도를 미리 생성
					var map = new daum.maps.Map(mapContainer, mapOption);
					//주소-좌표 변환 객체를 생성
					var geocoder = new daum.maps.services.Geocoder();
					//마커를 미리 생성
					var marker = new daum.maps.Marker({
						position: new daum.maps.LatLng(37.537187, 127.005476),
						map: map
					});


					function sample5_execDaumPostcode() {
						new daum.Postcode({
							oncomplete: function(data) {
								var addr = data.address; // 최종 주소 변수

								// 주소 정보를 해당 필드에 넣는다.
								document.getElementById("it_addr").value = addr;
								// 주소로 상세 정보를 검색
								geocoder.addressSearch(data.address, function(results, status) {
									// 정상적으로 검색이 완료됐으면
									if (status === daum.maps.services.Status.OK) {

										var result = results[0]; //첫번째 결과의 값을 활용

										// 해당 주소에 대한 좌표를 받아서
										var coords = new daum.maps.LatLng(result.y, result.x);
										// 지도를 보여준다.
										console.log(result.y, result.x);
										document.getElementById("it_lat_y").value = result.y;
										document.getElementById("it_lat_x").value = result.x;
										mapContainer.style.display = "block";
										map.relayout();
										// 지도 중심을 변경한다.
										map.setCenter(coords);
										// 마커를 결과값으로 받은 위치로 옮긴다.
										marker.setPosition(coords)
									}
								});
							}
						}).open();
					}
				</script>
            </td>
        </tr>
		<tr>
            <th scope="row"><label for="it_10">업체전화</label></th>
            <td>
                <input type="text" name="it_10" value="<?php echo $it['it_10']; ?>" id="it_10" class="frm_input" size="95">
            </td>
        </tr>

		<!-- <tr>
		            <th scope="row">기능선택 항목</th>
			  <td>
		                <input type="checkbox" name="it_1" value="24시할인" <?php echo ($it['it_1'] ? "checked" : ""); ?> id="it_1">24시할인
		
				<input type="checkbox" name="it_2" value="무료주차" <?php echo ($it['it_2'] ? "checked" : ""); ?> id="it_2">무료주차
		
				<input type="checkbox" name="it_3" value="수면가능" <?php echo ($it['it_3'] ? "checked" : ""); ?> id="it_3">수면가능
		
				<input type="checkbox" name="it_4" value="샤워가능" <?php echo ($it['it_4'] ? "checked" : ""); ?> id="it_4">샤워가능
		
				<input type="checkbox" name="it_5" value="이벤트중" <?php echo ($it['it_5'] ? "checked" : ""); ?> id="it_5">이벤트중
		
				<input type="checkbox" name="it_6" value="커플할인" <?php echo ($it['it_6'] ? "checked" : ""); ?> id="it_6">커플할인
		
				<input type="checkbox" name="it_7" value="WIFI" <?php echo ($it['it_7'] ? "checked" : ""); ?> id="it_7">WIFI
		
				<input type="checkbox" name="it_8" value="인기업체" <?php echo ($it['it_8'] ? "checked" : ""); ?> id="it_8">인기업체
		
				<select name="it_9" id="it_9" onchange="">
		                    <option value="">마사지연령 선택</option>
					<option value="20대" <?php if($it['it_9'] =='20대')  echo "selected"; ?>>20대</option>
					<option value="30대" <?php if($it['it_9'] =='30대')  echo "selected"; ?>>30대</option>
					<option value="40대" <?php if($it['it_9'] =='40대')  echo "selected"; ?>>40대</option>
					<option value="50대" <?php if($it['it_9'] =='50대')  echo "selected"; ?>>50대</option>
		                </select>
		            </td>
		        </tr> -->
        <tr>
            <th scope="row">프로그램 내용 </th>
			<?if ($w == ""){?>
			<td>
			<input type="button" class="btnAdd btn_frmline" value="프로그램추가" style=" cursor:pointer" name="addStaff">


				<div style="margin-top:20px;border:1px solid #ccc" name="trStaff">
					<table width="100%" border="0" cellpadding="0" cellspacing="0">
						<tr>
							<th width="100">프로그램명</th>
							<td  width="500">
								<input type="text" name="p_title[]" value="" id="p_title" class="frm_input frm_input_full" placeholder="프로그램명">
								<input type="hidden" name="p_idx[]" value="">
							</td>
							<th  width="100">프로그램명 가격</th>
							<td  width="">
								<input type="text" name="p_cust_price[]" value="" id="p_cust_price" class="frm_input " size="20" placeholder="할인전 가격"> 원
							</td>
						</tr>
						<tr>
							<th>프로그램 설명</th>
							<td>
								<input type="text" name="p_basic[]" value="" id="p_basic" class="frm_input frm_input_full" placeholder="프로그램 설명">
							</td>
							<th>프로그램명 할인 가격</th>
							<td>
								<input type="text" name="p_price[]" value="" id="p_price" class="frm_input" size="20" placeholder="할인된 가격" > 원
							</td>
						</tr>
					</table>
				</div>
				<div style="margin-top:20px;border:1px solid #ccc" name="trStaff"></div>

			</td>
		</tr>
		<?}else if($w == "u"){?>
            <td>
			<input type="button" class="btnAdd btn_frmline" value="프로그램추가" style=" cursor:pointer" name="addStaff">

			<?
				$sql_pr ="select * from `g5_shop_program` where it_id = '$it_id'";
				$result = sql_query($sql_pr);
				for($i=0; $row=sql_fetch_array($result); $i++){
			?>
				<div style="margin-top:20px;border:1px solid #ccc" name="trStaff">
					<table width="100%" border="0" cellpadding="0" cellspacing="0">
						<tr>
							<th width="100">프로그램명</th>
							<td  width="500">
								<input type="text" name="p_title[]" value="<?=$row[p_title]?>" id="p_title" class="frm_input frm_input_full" placeholder="프로그램명">
								<input type="hidden" name="p_idx[]" value="<?=$row[p_idx]?>">
							</td>
							<th  width="100">프로그램명 가격</th>
							<td  width="">
								<input type="text" name="p_cust_price[]" value="<?=$row[p_cust_price]?>" id="p_cust_price" class="frm_input " size="20" placeholder="할인전 가격"> 원
							</td>
						</tr>
						<tr>
							<th>프로그램 설명</th>
							<td>
								<input type="text" name="p_basic[]" value="<?=$row[p_basic]?>" id="p_basic" class="frm_input frm_input_full" placeholder="프로그램 설명">
							</td>
							<th>프로그램명 할인 가격</th>
							<td>
								<input type="text" name="p_price[]" value="<?=$row[p_price]?>" id="p_price" class="frm_input" size="20" placeholder="할인된 가격" > 원
							</td>
						</tr>
						</tbody>
					</table>
					<div style="cursor:pointer;text-align:center;width:100%;height:25px;line-height:25px;background:#9eacc6;color:#fff" onclick="remove_test(this)">삭제</div>
				</div>
				<div style="margin-top:20px;border:1px solid #ccc" name="trStaff"></div>

				<?
					}
				}
				?>
			</td>
		</tr>
		<script src="https://code.jquery.com/jquery-1.10.2.js"></script>
		<script>
			 $(document).on("click","input[name=addStaff]",function(){

			var addStaffText = '<div style="margin-top:20px;border:1px solid #ccc" name="trStaff"><table width="100%" border="0" cellpadding="0" cellspacing="0"><tr><th width="100">프로그램명</th><td  width="500"><input type="text" name="p_title[]" value="" id="p_title" class="frm_input frm_input_full" placeholder="프로그램명" required><input type="hidden" name="p_idx[]" value=""></td><th  width="100">프로그램명 가격</th><td  width=""><input type="text" name="p_cust_price[]" value="" id="p_cust_price" class="frm_input " size="20" placeholder="할인전 가격"> 원</td></tr><tr><th>프로그램 설명</th><td><input type="text" name="p_basic[]" value="" id="p_basic" class="frm_input frm_input_full" placeholder="프로그램 설명"></td><th>프로그램명 할인 가격</th><td><input type="text" name="p_price[]" value="" id="p_price" class="frm_input" size="20" placeholder="할인된 가격" required> 원</td></tr></table> <div style="cursor:pointer;text-align:center;width:100%;height:25px;line-height:25px;background:#9eacc6;color:#fff" onclick="remove_test(this)">삭제</div></div>'

			var divHtml = $( "div[name=trStaff]:last" ); //last를 사용하여 trStaff라는 명을 가진 마지막 태그 호출
			 
			divHtml.after(addStaffText); //마지막 trStaff명 뒤에 붙인다.
			 
		});

		</script>
<script>
function remove_test(val){
	$(val).parent().remove ();
}
</script>
		<tr>
            <th scope="row">이벤트 내용</th>
            <td><?php echo help("이벤트 내용 출력하는 HTML 내용입니다."); ?>
			<?php echo editor_html('it_mobile_head_html', get_text($it['it_mobile_head_html'], 0)); ?></td>
        </tr>
		 <tr>
            <th scope="row">예약 주의사항</th>
            <td><?php echo help("예약 주의사항 출력하는 HTML 내용입니다"); ?><?php echo editor_html('it_mobile_tail_html', get_text($it['it_mobile_tail_html'], 0)); ?></td>
        </tr>
        <tr>
            <th scope="row"><label for="it_shop_memo">업체메모</label></th>
            <td><textarea name="it_shop_memo" id="it_shop_memo"><?php echo $it['it_shop_memo']; ?></textarea></td>
        </tr>
        </tbody>
        </table>
    </div>
</section>


<section id="anc_sitfrm_img">
    <h2 class="h2_frm">이미지</h2>
    <?php echo $pg_anchor; ?>

    <div class="tbl_frm01 tbl_wrap">
        <table>
        <caption>이미지 업로드</caption>
        <colgroup>
            <col class="grid_4">
            <col>
        </colgroup>
        <tbody>
        <?php for($i=1; $i<=10; $i++) { ?>
        <tr>
            <th scope="row"><label for="it_img<?php echo $i; ?>">이미지 <?php echo $i; ?></label></th>
            <td>
                <input type="file" name="it_img<?php echo $i; ?>" id="it_img<?php echo $i; ?>">
                <?php
                $it_img = G5_DATA_PATH.'/item/'.$it['it_img'.$i];
                if(is_file($it_img) && $it['it_img'.$i]) {
                    $size = @getimagesize($it_img);
                    $thumb = get_it_thumbnail($it['it_img'.$i], 25, 25);
                ?>
                <label for="it_img<?php echo $i; ?>_del"><span class="sound_only">이미지 <?php echo $i; ?> </span>파일삭제</label>
                <input type="checkbox" name="it_img<?php echo $i; ?>_del" id="it_img<?php echo $i; ?>_del" value="1">
                <span class="sit_wimg_limg<?php echo $i; ?>"><?php echo $thumb; ?></span>
                <div id="limg<?php echo $i; ?>" class="banner_or_img">
                    <img src="<?php echo G5_DATA_URL; ?>/item/<?php echo $it['it_img'.$i]; ?>" alt="" width="<?php echo $size[0]; ?>" height="<?php echo $size[1]; ?>">
                    <button type="button" class="sit_wimg_close">닫기</button>
                </div>
                <script>
                $('<button type="button" id="it_limg<?php echo $i; ?>_view" class="btn_frmline sit_wimg_view">이미지<?php echo $i; ?> 확인</button>').appendTo('.sit_wimg_limg<?php echo $i; ?>');
                </script>
                <?php } ?>
            </td>
        </tr>
        <?php } ?>
        </tbody>
        </table>
    </div>
</section>

<div class="btn_fixed_top">
    <a href="./itemlist.php?<?php echo $qstr; ?>" class="btn btn_02">목록</a>
    <a href="<?php echo G5_SHOP_URL ;?>/item.php?it_id=<?php echo $it_id ;?>" class="btn_02  btn">상품보기</a>
    <input type="submit" value="확인" class="btn_submit btn" accesskey="s">
</div>
</form>


<script>
var f = document.fitemform;

<?php if ($w == 'u') { ?>
$(".banner_or_img").addClass("sit_wimg");
$(function() {
    $(".sit_wimg_view").bind("click", function() {
        var sit_wimg_id = $(this).attr("id").split("_");
        var $img_display = $("#"+sit_wimg_id[1]);

        $img_display.toggle();

        if($img_display.is(":visible")) {
            $(this).text($(this).text().replace("확인", "닫기"));
        } else {
            $(this).text($(this).text().replace("닫기", "확인"));
        }

        var $img = $("#"+sit_wimg_id[1]).children("img");
        var width = $img.width();
        var height = $img.height();
        if(width > 700) {
            var img_width = 700;
            var img_height = Math.round((img_width * height) / width);

            $img.width(img_width).height(img_height);
        }
    });
    $(".sit_wimg_close").bind("click", function() {
        var $img_display = $(this).parents(".banner_or_img");
        var id = $img_display.attr("id");
        $img_display.toggle();
        var $button = $("#it_"+id+"_view");
        $button.text($button.text().replace("닫기", "확인"));
    });
});
<?php } ?>


function fitemformcheck(f){

    <?php echo get_editor_js('it_mobile_head_html'); ?>
    <?php echo get_editor_js('it_mobile_tail_html'); ?>

    return true;
}


</script>

<?php
include_once (G5_ADMIN_PATH.'/admin.tail.php');
?>
