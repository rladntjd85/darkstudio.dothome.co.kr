<?php
include_once('./_common.php');
include_once(G5_MSHOP_PATH.'/_head.php');
http://ttmin.cafe24.com
?>
<div style="padding:10px;"><!-- 전체 div 시작  -->
<form name="fitem" action="<?=G5_URL?>/shop/map_select.php" method="post" >
<input type="hidden" name="x_chk" id="it_lat_x" value="<?=$x?>">
<input type="hidden" name="y_chk" id="it_lat_y" value="<?=$y?>">
<input type="text" name="addr_chk" value="<?=$my_addr?>" id="it_addr" placeholder="주소검색 클릭하세요" class="frm_input" style="width:100%;"   onclick="sample5_execDaumPostcode()" readonly>

<div id="map" style="width:100%;height:300px;margin-top:10px;display:none"></div>

<script src="http://dmaps.daum.net/map_js_init/postcode.v2.js"></script>
<script src="//dapi.kakao.com/v2/maps/sdk.js?appkey=5ba28f0e3d511ff8a23b395a90279a08&libraries=services"></script><script>
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
               //         mapContainer.style.display = "block";
                        //map.relayout();
                        // 지도 중심을 변경한다.
                       // map.setCenter(coords);
                        // 마커를 결과값으로 받은 위치로 옮긴다.
                       // marker.setPosition(coords);
						document.fitem.submit();
						//location.href=g5_url+"?x_chk="+result.y+"&y_chk="+result.x;
                    }
                });
            }
        }).open();
    }
</script>
</form>
</div>
<?php
include_once(G5_PATH.'/tail.sub.php');
?>