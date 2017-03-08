<?php
include_once('./_common.php');

/* 반응형으로 갈 것임
if (G5_IS_MOBILE) {
	include_once(G5_MSHOP_PATH.'/orderinquiry.php');
	return;
}
*/

define("_ORDERINQUIRY_", true);

if(get_session("mem_order_se")){
	$member[mb_id] = get_session("mem_order_se");
	$member = sql_fetch("select * from {$g5['member_table']} where mb_id='".$member[mb_id]."'");
}else{
	$member[mb_id] = $member[mb_id];
}

$od_pwd = sql_password($od_pwd);

// 회원인 경우
if ($is_member)
{
	$sql_common = " from {$g5['g5_shop_order_table']} where od_id in ( select distinct od_id from g5_shop_cart where ct_gubun = 'N' and ct_type='' ) and mb_id = '{$member['mb_id']}' ";
}
else if ($od_id && $od_pwd) // 비회원인 경우 주문서번호와 비밀번호가 넘어왔다면
{
	$sql_common = " from {$g5['g5_shop_order_table']} where od_id in ( select distinct od_id from g5_shop_cart where ct_gubun = 'N' and ct_type='' ) and od_id = '$od_id' and od_pwd = '$od_pwd' ";
}
else // 그렇지 않다면 로그인으로 가기
{
  	//URL변수에 같은도메인이 중복될경우 IIS서버에서 거부함으로 인해 도메인주소는 생략하고 상대경로만 입력
	goto_url(G5_BBS_URL.'/login.php?url='.urlencode('/shop/orderinquiry.php'));
}

// 테이블의 전체 레코드수만 얻음
$sql = " select count(*) as cnt " . $sql_common;
$row = sql_fetch($sql);
$total_count = $row['cnt'];

// 비회원 주문확인시 비회원의 모든 주문이 다 출력되는 오류 수정
// 조건에 맞는 주문서가 없다면
if ($total_count == 0)
{
	//if ($is_member) // 회원일 경우는 메인으로 이동
		//alert('주문이 존재하지 않습니다.', G5_SHOP_URL);
	//else // 비회원일 경우는 이전 페이지로 이동
		//alert('주문이 존재하지 않습니다.');
}

$rows = $config['cf_page_rows'];
$total_page  = ceil($total_count / $rows);  // 전체 페이지 계산
if ($page == '') { $page = 1; } // 페이지가 없으면 첫 페이지 (1 페이지)
$from_record = ($page - 1) * $rows; // 시작 열을 구함


// 비회원 주문확인의 경우 바로 주문서 상세조회로 이동
if (!$is_member)
{
	$sql = " select od_id, od_time, od_ip from {$g5['g5_shop_order_table']} where od_id = '$od_id' and od_pwd = '$od_pwd' ";
	$row = sql_fetch($sql);
	if ($row['od_id']) {
		$uid = md5($row['od_id'].$row['od_time'].$row['od_ip']);
		set_session('ss_orderview_uid', $uid);
		goto_url(G5_SHOP_URL.'/orderinquiryview.php?od_id='.$row['od_id'].'&amp;uid='.$uid);
	}
}

$g5['title'] = '주문내역조회';
if (G5_IS_MOBILE) {
	include_once(G5_MSHOP_PATH.'/head.php');
} else {
	include_once(G5_PATH.'/_head.php');
}
?>
<script>
	$(function() {

		// 모바일 마이페이지 뷰단 관련으로 넣음
		if(isMobile) {
			$('.mobile #my_month_tab .my_month_bn').append("<p>조회</p>");
			$('.searchContainer').append($('.descContainer'));

			$('.mobile .storeTitle').hide();
			$('#general_box').prepend('<h3 class="mobileStoreTitle">공동구매 상품</h3>');

			createOrderListItems();

			$(document).on('click', '.detailViewBtn', function(e) {
				var $indent = $(e.target).parent().parent();

				var orderId = $indent.find('.orderId').text().replace(/[^0-9]/g,'');



				$.ajax({
					type : "POST",
					dataType : "HTML",
					url : "/ajax/req_order_dtl_list.php",
					data : {od_id:orderId},
					success : function(data){
						var resultData = JSON.parse(data);

						console.log(resultData);

						var items = createDetailViewItems(resultData.data);

						var template =
						"<div class='detailViewContainer'>" +
							"<div><span class='odTitle'>주문번호</span><span class='odNumber'>" + resultData.od_id + "</span></div>" +
							"<div><span class='brandTitle'>브랜드건별주문번호</span><span class='brandNumber'>" + resultData.gp_code + "</span></div>" +
							"<table class='list'>" +
								"<thead>" +
									"<tr>" +
										"<th width='60%'>상품정보</th>" +
										"<th width='10%'>수량</th>" +
										"<th width='10%'>옵션</th>" +
										"<th width='10%'>상품금액</th>" +
//										"<th width='10%'>할인</th>" +
									"</tr>" +
								"</thead>" +
								"<tbody>" +
									items +
								"</tbody>" +
							"</table>" +
							"<table class='result'>" +
								"<tr>" +
									"<th rowspan='2' class='total'>총합계</th>" +
									"<th>상품금액</th>" +
//									"<th>할인금액</th>" +
									"<th>배송비</th>" +
									"<th>결제금액</th>" +
								"</tr>" +
								"<tr>" +
									"<td>" + resultData.total_product_price + "원</td>" +
//									"<td>-</td>" +
									"<td>" + resultData.total_baesongbi + "원</td>" +
									"<td class='totalPrice'>" + resultData.total_price + "원</td>" +
								"</tr>" +
							"</table>" +
							"<div id='close'>닫기</div>";
						"</div>";

						$.magnificPopup.open({
							showCloseBtn:false,
							items: {
								type: 'inline',
									src: template
							},
							type:'inline',
							callbacks: {
								open:function() {
									$('.detailViewContainer .prodImg').imgLiquid({fill:false});
								}
							}
						});
					}
				});
			});

			$(document).on("click", '#close', function() {
				$.magnificPopup.close();
			});
		}

		function createDetailViewItems(items) {
			var datas = "";

			$.each(items, function(i, item) {
				var data =
					"<tr>" +
						"<td>" + //
							"<div class='prodImg'>" +
								"<img src='" + item.img_url + "'>" +
							"</div>" +
							"<p class='prodName'>" + item.gp_name + "</p>" +
						"</td>" +
						"<td>" + // 수량
							item.ct_qty +
						"</td>" +
						"<td>" + // 옵션
							"-" +
						"</td>" +
						"<td class='totalPrice'>" + // 상품금액
							Number(item.ct_price).toLocaleString() +
						"</td>" +
//						"<td>" + // 할인
//							"-" +
//						"</td>" +
					"</tr>";

				datas += data;
			});

			return datas;
		}

		function createOrderListItems() {
			$.post("/ajax/req_orderlist.php", function(data) {
				var list = $.parseJSON(data).data;

				var container = $('#orderListContainer');

				$.each(list, function(i, item) {
					var resultItem = createOrderListItem(item);
					container.append(resultItem);
				});

				$('.prodImgContainer').imgLiquid({fill:false});
			});
		}

		function createOrderListItem(item) {
		var itemHtml =
			"<div class='orderListItemContainer'>" +
			"<div class='prodName ellipsis'>" + item.it_name + "</div>" +
			"<div class='prodImgContainer'>" +
			"<a href='/shop/item.php?it_id=" + item.it_id + "'>" +
			"<img class='prodImg' src=" + decodeURIComponent(item.gp_img).replace("+", "%20") + ">" +
			"</a>" +
			"</div>" +
			"<div class='prodInfoContainer'>" +
			"<div class='indent'>" +
			"<div class='orderDate ellipsis'><span>주문일자</span>" + item.ct_time + "</div>" +
			"<div class='orderId ellipsis'><span>주문번호</span>" + item.od_id + "</div>" +
			"<div class='totalPrice ellipsis'><span>주문금액(수량)</span>" + item.TOTAL_PRICE + "(" + item.ct_qty + "개)</div>" +
			"<div class='qty'><span>주문상태</span>" + item.ct_status + "</div>" +
			"<ul class='prodBtnContainer'>" +
			"<li class='detailViewBtn'>상세보기</li>" +
			"</ul>" +
			"</div>" +
			"</div>" +
			"</div>";
		return itemHtml;
	}
	});
</script>

<!-- 주문 내역 시작 { -->
<div id="sod_v">

	<?php
	$limit = " limit $from_record, $rows ";
	include "./orderinquiry.sub.php";
	?>

	<?php echo get_paging($config['cf_write_pages'], $page, $total_page, "{$_SERVER['PHP_SELF']}?$qstr&amp;page="); ?>
</div>
<!-- } 주문 내역 끝 -->

<?php
if (G5_IS_MOBILE) {
	include_once(G5_MSHOP_PATH.'/tail.php');
} else {
	include_once(G5_PATH.'/tail.php');
}
?>
