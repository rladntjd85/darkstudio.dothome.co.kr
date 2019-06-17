<?php
$sub_menu = '400300';
include_once('./_common.php');

$g5['title'] = '상품관리';


include_once(G5_PATH.'/head.sub.php');
echo '<link rel="stylesheet" href="'.G5_ADMIN_URL.'/css/admin.css">'.PHP_EOL;


// 분류
$ca_list  = '<option value="">선택</option>'.PHP_EOL;
$sql = " select * from {$g5['g5_shop_category_table']} ";
if ($is_admin != 'super')
    $sql .= " where ca_mb_id = '{$member['mb_id']}' ";
$sql .= " order by ca_order, ca_id ";
$result = sql_query($sql);
for ($i=0; $row=sql_fetch_array($result); $i++)
{
    $len = strlen($row['ca_id']) / 2 - 1;
    $nbsp = '';
    for ($i=0; $i<$len; $i++) {
        $nbsp .= '&nbsp;&nbsp;&nbsp;';
    }
    $ca_list .= '<option value="'.$row['ca_id'].'">'.$nbsp.$row['ca_name'].'</option>'.PHP_EOL;
}

$where = " and ";
$sql_search = "";
if ($stx != "") {
    if ($sfl != "") {
        $sql_search .= " $where $sfl like '%$stx%' ";
        $where = " and ";
    }
    if ($save_stx != $stx)
        $page = 1;
}

if ($sca != "") {
    $sql_search .= " $where (a.ca_id like '$sca%' or a.ca_id2 like '$sca%' or a.ca_id3 like '$sca%') ";
}

if ($sfl == "")  $sfl = "it_name";

$sql_common = " from {$g5['g5_shop_item_table']} a ,
                     {$g5['g5_shop_category_table']} b
               where (a.ca_id = b.ca_id";
if ($is_admin != 'super')
    $sql_common .= " and mb_id = '{$member['mb_id']}'";
$sql_common .= ") ";
$sql_common .= $sql_search;

// 테이블의 전체 레코드수만 얻음
$sql = " select count(*) as cnt " . $sql_common;
$row = sql_fetch($sql);
$total_count = $row['cnt'];

$rows = $config['cf_page_rows'];
$total_page  = ceil($total_count / $rows);  // 전체 페이지 계산
if ($page < 1) { $page = 1; } // 페이지가 없으면 첫 페이지 (1 페이지)
$from_record = ($page - 1) * $rows; // 시작 열을 구함

if (!$sst) {
    $sst  = "it_id";
    $sod = "desc";
}
$sql_order = "order by $sst $sod";


$sql  = " select *
           $sql_common
           $sql_order
           limit $from_record, $rows ";
$result = sql_query($sql);

//$qstr  = $qstr.'&amp;sca='.$sca.'&amp;page='.$page;
$qstr  = $qstr.'&amp;sca='.$sca.'&amp;page='.$page.'&amp;save_stx='.$stx;

$listall = '<a href="'.$_SERVER['SCRIPT_NAME'].'" class="ov_listall">전체목록</a>';
?>

<div class="local_ov01 local_ov">
    <?php echo $listall; ?>
    <span class="btn_ov01"><span class="ov_txt">등록된 상품</span><span class="ov_num"> <?php echo $total_count; ?>건</span></span>
</div>

<form name="flist" class="local_sch01 local_sch">
<input type="hidden" name="save_stx" value="<?php echo $stx; ?>">

<label for="sca" class="sound_only">분류선택</label>
<select name="sca" id="sca">
    <option value="">전체분류</option>
    <?php
    $sql1 = " select ca_id, ca_name from {$g5['g5_shop_category_table']} order by ca_order, ca_id ";
    $result1 = sql_query($sql1);
    for ($i=0; $row1=sql_fetch_array($result1); $i++) {
        $len = strlen($row1['ca_id']) / 2 - 1;
        $nbsp = '';
        for ($i=0; $i<$len; $i++) $nbsp .= '&nbsp;&nbsp;&nbsp;';
        echo '<option value="'.$row1['ca_id'].'" '.get_selected($sca, $row1['ca_id']).'>'.$nbsp.$row1['ca_name'].'</option>'.PHP_EOL;
    }
    ?>
</select>

<label for="sfl" class="sound_only">검색대상</label>
<select name="sfl" id="sfl">
    <option value="it_name" <?php echo get_selected($sfl, 'it_name'); ?>>상품명</option>
    <option value="it_id" <?php echo get_selected($sfl, 'it_id'); ?>>상품코드</option>
    <option value="it_maker" <?php echo get_selected($sfl, 'it_maker'); ?>>제조사</option>
    <option value="it_origin" <?php echo get_selected($sfl, 'it_origin'); ?>>원산지</option>
    <option value="it_sell_email" <?php echo get_selected($sfl, 'it_sell_email'); ?>>판매자 e-mail</option>
</select>

<label for="stx" class="sound_only">검색어</label>
<input type="text" name="stx" value="<?php echo $stx; ?>" id="stx" class="frm_input">
<input type="submit" value="검색" class="btn_submit">

</form>

<form name="fitemlistupdate" method="post" action="./itemlistupdate.php" onsubmit="return fitemlist_submit(this);" autocomplete="off" id="fitemlistupdate">
<input type="hidden" name="sca" value="<?php echo $sca; ?>">
<input type="hidden" name="sst" value="<?php echo $sst; ?>">
<input type="hidden" name="sod" value="<?php echo $sod; ?>">
<input type="hidden" name="sfl" value="<?php echo $sfl; ?>">
<input type="hidden" name="stx" value="<?php echo $stx; ?>">
<input type="hidden" name="page" value="<?php echo $page; ?>">

<div class="tbl_head01 tbl_wrap">
    <table width="100%"  border="0" cellpadding="0" cellspacing="0">
    <caption><?php echo $g5['title']; ?> 목록</caption>
    <thead>
    <tr>
        <th scope="col" >
            <label for="chkall" class="sound_only">상품 전체</label>
            <input type="checkbox" name="chkall" value="1" id="chkall" onclick="check_all(this.form)">
        </th>
        <th scope="col" >코드</th>
        <th scope="col"  id="th_img">이미지</th>
		<th scope="col" >업체명</th>
		<th scope="col" >업체 주소</th>
		<!-- <th scope="col" >기능선택</th> -->
        <th scope="col"  id="th_qty">공개여부</th>
        <th scope="col"  id="th_mskin">관리</th>
    </tr>
    </thead>
    <tbody>
    <?php
    for ($i=0; $row=sql_fetch_array($result); $i++)
    {
        $href = G5_SHOP_URL.'/item.php?it_id='.$row['it_id'];
        $bg = 'bg'.($i%2);

        $it_point = $row['it_point'];
        if($row['it_point_type'])
            $it_point .= '%';
    ?>
    <tr class="<?php echo $bg; ?>">
        <td  class="td_chk">
            <label for="chk_<?php echo $i; ?>" class="sound_only"><?php echo get_text($row['it_name']); ?></label>
            <input type="checkbox" name="chk[]" value="<?php echo $i ?>" id="chk_<?php echo $i; ?>">

			<input type="hidden" name="ca_id[<?php echo $i; ?>]" id="ca_id_<?php echo $i; ?>" value="<?=$row['ca_id']?>">
			<input type="hidden" name="ca_id2[<?php echo $i; ?>]" id="ca_id_<?php echo $i; ?>" value="<?=$row['ca_id2']?>">
			<input type="hidden" name="ca_id3[<?php echo $i; ?>]" id="ca_id_<?php echo $i; ?>" value="<?=$row['ca_id3']?>">

        </td>
        <td class="td_num">
            <input type="hidden" name="it_id[<?php echo $i; ?>]" value="<?php echo $row['it_id']; ?>">
            <?php echo $row['it_id']; ?>
        </td>
		<td class="td_img"><a href="<?php echo $href; ?>"><?php echo get_it_image($row['it_id'], 100, 70); ?></a></td>
		<td headers="th_pc_title"  class="td_input">
            <label for="name_<?php echo $i; ?>" class="sound_only">상품명</label>
            <?php echo htmlspecialchars2(cut_str($row['it_name'],250, "")); ?> 
			<input type="hidden" name="it_name[<?php echo $i; ?>]" id="it_name_<?php echo $i; ?>" value="<?=$row['it_name']?>">
        </td>
		<td headers="th_pc_title" class="td_input">
            <label for="name_<?php echo $i; ?>" class="sound_only">업체주소</label>
            <?php echo htmlspecialchars2(cut_str($row['it_addr'],250, "")); ?> 
        </td>
		<!-- <td>
		            <input type="checkbox" name="it_1[<?php echo $i; ?>]" <?php echo ($row['it_1'] ? 'checked' : ''); ?> value="24시할인" id="use_<?php echo $i; ?>">24시할인
		
				<input type="checkbox" name="it_2" value="무료주차" <?php echo ($row['it_2'] ? "checked" : ""); ?> id="it_2">무료주차
		
				<input type="checkbox" name="it_3" value="수면가능" <?php echo ($row['it_3'] ? "checked" : ""); ?> id="it_3">수면가능
		
				<input type="checkbox" name="it_4" value="샤워가능" <?php echo ($row['it_4'] ? "checked" : ""); ?> id="it_4">샤워가능
		
				<input type="checkbox" name="it_5" value="이벤트중" <?php echo ($row['it_5'] ? "checked" : ""); ?> id="it_5">이벤트중
		
				<input type="checkbox" name="it_6" value="커플할인" <?php echo ($row['it_6'] ? "checked" : ""); ?> id="it_6">커플할인
		
				<input type="checkbox" name="it_7" value="WIFI" <?php echo ($row['it_7'] ? "checked" : ""); ?> id="it_7">WIFI
		
				<input type="checkbox" name="it_8" value="인기업체" <?php echo ($row['it_8'] ? "checked" : ""); ?> id="it_8">인기업체
		
				<select name="it_9" id="it_9" onchange="" style="width:100px;">
		                    <option value="">마사지연령 선택</option>
					<option value="20대" <?php if($row['it_9'] =='20대')  echo "selected"; ?>>20대</option>
					<option value="30대" <?php if($row['it_9'] =='30대')  echo "selected"; ?>>30대</option>
					<option value="40대" <?php if($row['it_9'] =='40대')  echo "selected"; ?>>40대</option>
					<option value="50대" <?php if($row['it_9'] =='50대')  echo "selected"; ?>>50대</option>
		                </select>
		
		</td> -->
		<td>
            <label for="use_<?php echo $i; ?>" class="sound_only">공개여부</label>
            <input type="checkbox" name="it_use[<?php echo $i; ?>]" <?php echo ($row['it_use'] ? 'checked' : ''); ?> value="1" id="it_use<?php echo $i; ?>">
        </td>
		<td class="td_mng td_mng_s">
            <a href="./itemform.php?w=u&amp;it_id=<?php echo $row['it_id']; ?>&amp;ca_id=<?php echo $row['ca_id']; ?>&amp;<?php echo $qstr; ?>" class="btn btn_03"><span class="sound_only"><?php echo htmlspecialchars2(cut_str($row['it_name'],250, "")); ?> </span>수정</a>
            <a href="<?php echo $href; ?>" class="btn btn_02"><span class="sound_only"><?php echo htmlspecialchars2(cut_str($row['it_name'],250, "")); ?> </span>보기</a>
        </td>
    </tr>
    <tr class="<?php echo $bg; ?>">
    </tr>
    <tr class="<?php echo $bg; ?>">
    </tr>
    <?php
    }
    if ($i == 0)
        echo '<tr><td colspan="12" class="empty_table">자료가 한건도 없습니다.</td></tr>';
    ?>
    </tbody>
    </table>
</div>

<div class="btn_fixed_top">

    <a href="./itemform.php" class="btn btn_01">업체등록</a>
    <!-- <a href="./itemexcel.php" onclick="return excelform(this.href);" target="_blank" class="btn btn_02">상품일괄등록</a> -->
    <input type="submit" name="act_button" value="선택수정" onclick="document.pressed=this.value" class="btn btn_02">
    <?php if ($is_admin == 'super') { ?>
    <input type="submit" name="act_button" value="선택삭제" onclick="document.pressed=this.value" class="btn btn_02">
    <?php } ?>
</div>
<!-- <div class="btn_confirm01 btn_confirm">
    <input type="submit" value="일괄수정" class="btn_submit" accesskey="s">
</div> -->
</form>

<?php echo get_paging(G5_IS_MOBILE ? $config['cf_mobile_pages'] : $config['cf_write_pages'], $page, $total_page, "{$_SERVER['SCRIPT_NAME']}?$qstr&amp;page="); ?>

<script>
function fitemlist_submit(f)
{
    if (!is_checked("chk[]")) {
        alert(document.pressed+" 하실 항목을 하나 이상 선택하세요.");
        return false;
    }

    if(document.pressed == "선택삭제") {
        if(!confirm("선택한 자료를 정말 삭제하시겠습니까?")) {
            return false;
        }
    }

    return true;
}

$(function() {
    $(".itemcopy").click(function() {
        var href = $(this).attr("href");
        window.open(href, "copywin", "left=100, top=100, width=300, height=200, scrollbars=0");
        return false;
    });
});

function excelform(url)
{
    var opt = "width=600,height=450,left=10,top=10";
    window.open(url, "win_excel", opt);
    return false;
}
</script>

<?php
include_once (G5_ADMIN_PATH.'/admin.tail.php');
?>
