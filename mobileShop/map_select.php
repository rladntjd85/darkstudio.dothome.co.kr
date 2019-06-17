<?php
include_once('./_common.php');
include_once(G5_MSHOP_PATH.'/_head.php');

?>
<!-- 100% 하기위에 헤더에서 시작한 div 닫고 시작-->
</div>
<div><!-- 전체 div 시작  -->
<form name="fitem" action="<?=G5_URL?>" method="post" >

<input type="hidden" name="x_chk" id="x" value="<?=$x?>">
<input type="hidden" name="y_chk" id="y" value="<?=$y?>">
<input type="text" name="addr_chk" id="addr_chk" value="<?=$my_addr?>" onclick="map_search()" class="frm_input" style="width:100%;" readonly>
<div style="position:fixed;width:100%;height:100%;padding-bottom:50px;">
	<div id="map" style="width:100%;height:73%;position:absolute;z-index:1000;"></div>
	<div style="width:10px;height:10px;position:absolute;z-index:1100;top:39%;left:49%;margin-left:-10px;margin-top:-45px;"><img src="<?=G5_IMG_URL?>/m/marker_spot.png" width="30"></div>
	<div style="width:100%;height:21%;bottom:30px;position:absolute;text-align:center">
		<input type="submit" value="위치 선택" style="border:1px solid #be0505;display: inline-block;border-radius: 2px;background-color: #be0505;width:95%;color:#fff;text-align:center;height:50px;font-size:20px;">
	</div> 
</div>


</form>
<script type="text/javascript" src="//dapi.kakao.com/v2/maps/sdk.js?appkey=5ba28f0e3d511ff8a23b395a90279a08&libraries=services"></script>
<script>
var mapContainer = document.getElementById('map'), // 지도를 표시할 div 
    mapOption = { 
        center: new daum.maps.LatLng('<?=$y?>', '<?=$x?>'), // 지도의 중심좌표
        level: 3 // 지도의 확대 레벨
    };

var map = new daum.maps.Map(mapContainer, mapOption); // 지도를 생성합니다

// 마우스 드래그로 지도 이동이 완료되었을 때 마지막 파라미터로 넘어온 함수를 호출하도록 이벤트를 등록합니다
daum.maps.event.addListener(map, 'dragend', function() {        
    
    // 지도 중심좌표를 얻어옵니다 
    var latlng = map.getCenter(); 
    

    $("#y").val(latlng.getLat());
    $("#x").val(latlng.getLng() );

    test2(latlng.getLat(),latlng.getLng());
});
</script>

<script>
$( document ).ready(function() {
	test2("<?=$y?>","<?=$x?>");
});

function test2(lat, lng){

var geocoder = new daum.maps.services.Geocoder();
var coord = new daum.maps.LatLng(lat, lng);
var callback = function(result, status) {
    if (status === daum.maps.services.Status.OK) {
		    $("#addr_chk").val(result[0].address.address_name);
    }
};
geocoder.coord2Address(coord.getLng(), coord.getLat(), callback);

}

function map_search(){
	location.href=g5_url+"/shop/map_search.php";
}
</script>  
</div><!-- 전체 div 끝  -->
<?php
include_once(G5_PATH.'/tail.sub.php');
?>