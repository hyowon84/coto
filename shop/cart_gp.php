<?php
include_once('./_common.php');
define('_GROUPSHOP_', true);

// cart id 설정
set_cart_id($sw_direct);

$s_cart_id = get_session('ss_cart_id');

/* 대리주문 로그인시 대리주문자의 주문정보를 위한 세션정보 교체 */
if(get_session("mem_order_se")){
	$member[mb_id] = get_session("mem_order_se");
	$member = sql_fetch("select * from {$g5['member_table']} where mb_id='".$member[mb_id]."'");
}

$cart_action_url = G5_SHOP_URL.'/gp_cartupdate.php';


// 코드값 검색
if($ct_type)$ct_type_que = " and ct_type='".$ct_type."' ";
else $ct_type_que = " and ct_type != '' ";

$k = 0;

$g5['title'] = '쇼핑카트';
if (G5_IS_MOBILE) {
	include_once(G5_MSHOP_PATH.'/head.php');
} else {
	include_once(G5_PATH.'/_head.php');
}

?>

<div class="test"></div>

<!-- 장바구니 시작 { -->
<script src="<?=G5_JS_URL?>/imgLiquid.js"></script>
<script type="text/javascript" src="<?=G5_URL?>/js/shop.js"></script>

<!-- 타이틀 -->
<div class="cart_title"><?php echo $member['mb_nick']?><font style="font-size:13px">님의</font> 실시간 공동구매 현황</div>


<!-- navi -->

<!-- 탭메뉴 -->
<br>

<form name="frmcartlist" id="sod_bsk_list" method="post" style="background:#fff" action="<?php echo $cart_action_url; ?>">
<input type="hidden" name="act" value="">
<div class="list_box">

	<div class="sub_title_gp">
		<?if(G5_IS_MOBILE) {?>
			<ul>
				<li id='brand_all' ca_id='all' class='brandCategoryList on' onclick="changeBrandCategory()"><p>ALL</p></li>
				<li id='brand_2010' ca_id='2010' class='brandCategoryList' onclick="changeBrandCategory('2010');"><p>APMEX</p></li>
				<li id='brand_2020' ca_id='2020' class='brandCategoryList' onclick="changeBrandCategory('2020');"><p>Gainesville<br/>Coins</p></li>
				<li id='brand_2030' ca_id='2030' class='brandCategoryList' onclick="changeBrandCategory('2030');"><p>MCM</p></li>
				<li id='brand_2040' ca_id='2040' class='brandCategoryList' onclick="changeBrandCategory('2040');"><p>Scottsdale<br/>Silver</p></li>
				<li id='brand_2050' ca_id='2050' class='brandCategoryList' onclick="changeBrandCategory('2050');"><p>OTHER<br/>DEALER</p></li>
			</ul>
		<?} else {?>
			<ul>
				<li id='brand_all' ca_id='all' class='brandCategoryList on' onclick="changeBrandCategory()">ALL</li>
				<li id='brand_2010' ca_id='2010' class='brandCategoryList' onclick="changeBrandCategory('2010');">APMEX</li>
				<li id='brand_2020' ca_id='2020' class='brandCategoryList' onclick="changeBrandCategory('2020');">Gainesville Coins</li>
				<li id='brand_2030' ca_id='2030' class='brandCategoryList' onclick="changeBrandCategory('2030');">MCM</li>
				<li id='brand_2040' ca_id='2040' class='brandCategoryList' onclick="changeBrandCategory('2040');">Scottsdale Silver</li>
				<li id='brand_2050' ca_id='2050' class='brandCategoryList' onclick="changeBrandCategory('2050');">Other Dealer</li>
			</ul>
		<?}?>
	</div>

	<!--  조회 NAVI -->
	<form name="fsearch" id="fsearch" method="GET">
	<div id="my_month_tab">
		<table border="0" cellspacing="0" cellpadding="0" width="100%">
			<tr>
				<td class="title">
					조회기간
				</td>
				<td>
					<div class="date_diff">
						<div <?if($date_diff == "0"){echo "class='on'";}?>>1주일</div>
						<div <?if($date_diff == "1"){echo "class='on'";}?>>1개월</div>
						<div <?if($date_diff == "2"){echo "class='on'";}?>>3개월</div>
						<div class="right <?if($date_diff == ""){echo "on";}?>">기간</div>
					</div>
					<div class="date_diff1">
						<div><input type="text" name="my_sch_dt_f" id="my_sch_dt_f" class="my_sch_dt" readOnly value="<?=$my_sch_dt_f?>"></div>
						<div style="margin:3px 2px 0 2px;"> ~ </div>
						<div><input type="text" name="my_sch_dt_l" id="my_sch_dt_l" class="my_sch_dt" readOnly value="<?=$my_sch_dt_l?>"></div>
					</div>
					<input type="hidden" name="date_diff" value="<?=$date_diff?>">
				</td>
				<td rowspan="2" class="my_month_bn">
					<img id='btnReqGpList' src="<?=G5_URL?>/img/my_sch_bn.gif" title='조회하기' border="0" align="absmiddle" style="cursor:pointer;">
				</td>
			</tr>
			<tr>
				<td class="title">상태/상품/브랜드</td>
				<td class="searchContainer">
					<select name="my_sch_sel" class="my_sel">
						<option <?if($my_sch_sel == "") echo "selected";?>>전체</option>
						<option value="입금대기" <?if($my_sch_sel == "입금대기") echo "selected";?>>입금대기</option>
						<option value="결제완료" <?if($my_sch_sel == "결제완료") echo "selected";?>>결제완료</option>
						<option value="해외배송대기" <?if($my_sch_sel == "해외배송대기") echo "selected";?>>해외배송대기</option>
						<option value="해외배송중" <?if($my_sch_sel == "해외배송중") echo "selected";?>>해외배송중</option>
						<option value="상품준비중" <?if($my_sch_sel == "상품준비중") echo "selected";?>>상품준비중</option>
						<option value="배송대기" <?if($my_sch_sel == "배송대기") echo "selected";?>>배송대기</option>
						<option value="배송중" <?if($my_sch_sel == "배송중") echo "selected";?>>국내배송중</option>
						<option value="배송완료" <?if($my_sch_sel == "배송완료") echo "selected";?>>배송완료</option>
					</select>
					<input type="text" name="my_sch_val" class="my_val" value="<?if($my_sch_val){echo $my_sch_val;}?>" placeholder="상품명 혹은 브랜드 명 검색">
				</td>
			</tr>
			<tr class="descContainer" height="20px">
				<td class="desc" colspan="3" style="text-align:left;padding:2px 0 0 145px;font-size:12px;">
					구매 이력을 3개월 단위로 조회가 가능합니다.
				</td>
			</tr>
		</table>
	</div>
	</form>

	
	<!--  공동구매 신청정보 -->
	<style>
		.gpShoppingList  thead th { 
			padding: 8px 0;
			border-top: 2px solid #545454;
			border-bottom: 1px solid #d1dee2;
			background: white;
			color: #383838;
			font-size: 13px;
			text-align: center;
			letter-spacing: -0.1em;
		}
		
		.gpShoppingList tbody td { 
			padding: 8px 5px;
			border-bottom: 1px solid #d9d9d9;
			text-align:center;
			line-height: 1.5em;
			word-break: break-all;
		}
	</style>
	<style>
	.brandPrice { border:3px solid #d9d9d9; }
	.brandPrice  th { height:30px; background-color:#f9f9f9; }
	.brandPrice  td { height:55px; background-color:#ffffff; text-align:right; padding-left:5px; padding-right:15px;  }
	.brandPrice  td .tdProductPrice { width:190px; vertical-align:text-top; }
	.brandPrice td .valBrandName { position:absolute; margin-top:-17px; font-weight:bold; }
	.brandPrice td .icon_m img  { position:absolute; margin-top:-2px; left:272px;  }
	.brandPrice td .icon_p img  { position:absolute; margin-top:-2px; left:413px; }
	.brandPrice td .icon_e img  { position:absolute; margin-top:-2px; left:562px; }
	/*
	.brandPrice td .icon_m img  { position:absolute; margin-top:-2px; margin-left:-134px;  }
	.brandPrice td .icon_p img  { position:absolute; margin-top:-2px; margin-left:-143px; }
	.brandPrice td .icon_e img  { position:absolute; margin-top:-2px; margin-left:-220px; }
	*/
	.brandPrice td .valBrandPrice { float:right; }
	</style>
		
	<div id="sod_bsk">
		
		<div class="gpShoppingList">
				
			<table width='100%' cellspacing=0 cellpadding=0>
				<thead>
				<tr>
				<?if(G5_IS_MOBILE) {?>
					<th scope="col"><input type="checkbox" name="chkall" value="1" id="chkall"  onclick="if (this.checked) all_checked(true); else all_checked(false);"></th>
					<th scope="col">브랜드</th>
					<th scope="col" class="right" style="width:50px;">이미지</th>
					<th scope="col" class="right">상품정보</th>
					<th scope="col" class="right">수량</th>
					<th scope="col" class="right">옵션</th>
					<th scope="col" class="gray">상품금액</th>
				<?} else {?>
					<th scope="col"><input type="checkbox" name="chkall" value="1" id="chkall"  onclick="if (this.checked) all_checked(true); else all_checked(false);"></th>
					<th scope="col">브랜드</th>
					<th scope="col" class="right">이미지</th>
					<th scope="col" class="right">상품명</th>
					<th scope="col" class="right">수량</th>
					<th scope="col" class="right">단가</th>
					<th scope="col" class="right">합계</th>
					<th scope="col" class="gray">진행상태</th>
				<?}?>
				</tr>
				</thead>
				
				<tbody id='gpShoppingListTBbody'>
					<!-- 공동구매 신청 리스트 -->
				</tbody>
				
			</table>
		</div>
		
		<!-- 상품 선택 버튼 -->
		<div class="pro_choi_bn">
			<ul>
				<li onclick="return form_check('seldelete', '1');">선택상품 취소</li>
	
			</ul>
		</div>
	</div>


	<div id='brandPriceInfo'>
		<table class='brandPrice' border='0' cellspacing='1' cellpadding='0' bgcolor='#cfcfcf' width='100%'>
			<tbody id='brandPriceTBbody'>
			</tbody>
		</table>
	</div>


</div>



<script>


function changeBrandCategory(v_ca_id) {
	$('.brandCategoryList').attr('class','brandCategoryList');//reset
	v_ca_id = (v_ca_id) ? v_ca_id : 'all';	
	$('#brand_'+v_ca_id).addClass('on');
	createRealGpListItems(v_ca_id);
}


/* 상단 공동구매 신청정보 JSON 데이터 생성  */
function createRealGpListItems(v_ca_id) {
	var v_keyword;
	
	if($("input[name='my_sch_val']").attr('edited') == 'y') {
		v_keyword = $("input[name=my_sch_val]").val();
	}
	

	
	$.post("/ajax/req_gporder_list.php", 
				{	mode:'order',
					ca_id:	v_ca_id,
					sdate:	$('#my_sch_dt_f').val(),
					edate:	$('#my_sch_dt_l').val(),
					status:	$("select[name=my_sch_sel]").val(),
					keyword: v_keyword,
				},
				function(data) {
					/* 상단 공동구매 신청내역 목록 */
					var list = $.parseJSON(data).data;
					var container = $('#gpShoppingListTBbody');
					container.html('');
					
					$.each(list, function(i, item) {
						var resultItem = createRealGpListItem(item);
						container.append(resultItem);
					});
					//$('.prodImgContainer').imgLiquid({fill:false});		
				}
	);
}

/* 레코드 라인생성 */
function createRealGpListItem(item) {
	var itemHtml;

	if(g5_is_mobile) {
		itemHtml =
		"<tr>"+
			"<td><input type='checkbox' class='ct_chk_inac' id='chk_"+item.ct_id+"' name='ct_chk[]' value='"+item.ct_id+"' "+item.disabled+" /></td>"+
			"<td>"+item.brand_name+"</td>"+
			"<td>"+
				"<div class='prodImgContainer'>" +
					"<img style='min-width:50px;' class='prodImg' src=" + decodeURIComponent(item.gp_img).replace("+", "%20") + ">"+
				"</div>"+
			"</td>"+
			"<td style='text-align:left'>"+item.it_name+ ", " + number_format(item.ct_price) +"</td>"+
			"<td>"+number_format(item.ct_qty)+"</td>"+
			"<td>-</td>"+ // 옵션
			"<td style='text-align:right'>"+number_format(item.calc_price)+"</td>"+
		"</tr>";
	} else {
		itemHtml =
			"<tr>"+
			"<td><input type='checkbox' class='ct_chk_inac' id='chk_"+item.ct_id+"' name='ct_chk[]' value='"+item.ct_id+"' "+item.disabled+" /></td>"+
			"<td>"+item.brand_name+"</td>"+
			"<td>"+
			"<div class='prodImgContainer'>" +
			"<img width=64 height=64 class='prodImg' src=" + decodeURIComponent(item.gp_img).replace("+", "%20") + ">"+
			"</div>"+
			"</td>"+
			"<td style='text-align:left'>"+item.it_name+"</td>"+
			"<td>"+number_format(item.ct_qty)+"</td>"+
			"<td style='text-align:right'>"+number_format(item.ct_price)+"</td>"+
			"<td style='text-align:right'>"+number_format(item.calc_price)+"</td>"+
			"<td>"+item.ct_status+"</td>"+
			"</tr>";
	}


	return itemHtml;
}


/* 하단 브랜드가격정보 JSON 데이터 생성  */
function createBrandPriceInfoItems(ca_id) {
	$.post("/ajax/req_gporder_list.php", {mode:'brand',ca_id:ca_id}, function(data) {
		
		/* 하단 브랜드별 합계금액 목록 */
		var list = $.parseJSON(data).data_total;
		var info = [];
		info['rowspan'] = $.parseJSON(data).data_total_rows;
		info['total_price'] = $.parseJSON(data).total_price;

		/* 공동구매 신청정보가 있을경우에만 브랜드별 합계액 출력 */
		if(info['rowspan'] > 0) {
			var container = $('#brandPriceTBbody');
			var top_html = 
			"<tr>"+
				"<th width='88' rowspan='"+(info['rowspan']+1)+"' bgcolor='white'>총합계</th>"+
				"<th width='190'>상품금액</td>"+
				"<th width='139'>할인금액</td>"+
				"<th width='148'>배송비</td>"+
				"<th width='224'>총결제예상금액</th>"+
			"</tr>";
			container.append(top_html);
			
			$.each(list, function(i, item) {
				var resultItem = createBrandPriceInfoItem(item,i,info);
				container.append(resultItem);
			});
		}
		//$('.prodImgContainer').imgLiquid({fill:false});		
	});
}


/* 레코드 라인생성 */
function createBrandPriceInfoItem(item,i,info) {
	/* 첫번째 라인에는 합계액 */
	if(i == 0) {
		var itemHtml = 
		"<tr>"+
			"<td class='tdProductPrice'><div class='valBrandName'>"+item.ca_name+"</div><div class='valBrandPrice'>"+item.brand_price+"원</div></td>"+
			"<td><div class='icon_m'><img src='/img/icon_minus.gif' /></div>"+item.use_point+"원</td>"+
			"<td><div class='icon_p'><img src='/img/icon_plus.gif' /></div>"+item.baesongbi+"원</td>"+
			"<td class='totalAmount' rowspan='"+info['rowspan']+"'><div class='icon_e'><img src='/img/icon_equal.gif' /></div>"+info['total_price']+"원</td>"+
		"</tr>";
	}
	else {
		var itemHtml = 
		"<tr>"+
		"<td class='tdProductPrice'><div class='valBrandName'>"+item.ca_name+"</div><div class='valBrandPrice'>"+item.brand_price+"원</div></td>"+
		"<td><div class='icon_m'><img src='/img/icon_minus.gif' /></div>"+item.use_point+"원</td>"+
		"<td><div class='icon_p'><img src='/img/icon_plus.gif' /></div>"+item.baesongbi+"원</td>"+
		"</tr>";
	}
	return itemHtml;
}


$(document).ready(function(){
	createRealGpListItems('all');
	createBrandPriceInfoItems();
});
</script>



<div id="sod_bsk_act" style="background:#fff !important;padding-bottom:30px">
	<a href="<?php echo G5_SHOP_URL; ?>/gplist.php?ca_id=<?=$continue_ca_id?>"><img src="<?G5_URL?>/shop/img/cart_shop_bn.gif" align="absmiddle" style="border:0;"></a>
</div>


<?php if(count($gpCartList)>0){?>
<div style="color:#ff4e00;padding:00;margin:25px 0;text-align:center;">* 현재 공동구매 신청 상품의 <strong>총결제예상금액</strong>입니다.</div>

<?php
$priceInfoRows = count($gpCartList)+1;
?>
<!-- 일반상품 가격 정보 -->
<div class="tbl_wrap price_info_gp price_info1">
	<table>
		<tbody>
		<tr height="40px">
			<th rowspan="<?php echo $priceInfoRows;?>" width="117px" class="gubun">
				총합계 
			</th>
			<th width="228px">
				상품금액
			</th>
			<th width="135px">
				할인금액
			</th>
			<th width="150px">
				배송비
			</th>
			<th>
				결제예상금액
			</th>
		</tr>
		
		<?php

		// 배열정렬
		ksort($gpCartList);
		
		$ct_send_cost = $purchaseSendCost; // 기본배송비
		$iCount = 0;
		$totalPurchasePrice += $ct_send_cost * count($gpCartList);
		foreach($gpCartList as $key=>$vars){
		?>
		<tr>
			<td>
				<div class="purchase_box">
				<div class="purchase_ca_name"><?php echo getPurchaseCategoryName($key)?></div><div class="price_box"><span class="price"><?=number_format($vars['total_price'])?></span><span style="color:#818181;font-weight:normal;font-size:14px;">원</span></div>
				</div>
			</td>
			<td>
				<div class="purchase_box">
					<div class="icon_minus"> </div>
					<div class="price_box"><span class="price">0</span><span style="color:#818181;font-weight:normal;font-size:14px;">원</span></div>
				</div>
			</td>
			<td>
				<div class="purchase_box">
					<div class="icon_plus"> </div>
					<div class="price_box"><span class="price"><?php echo number_format($ct_send_cost)?></span><span style="color:#818181;font-weight:normal;font-size:14px;">원</span></div>
				</div>
			</td>
			<?php if($iCount==0){?>
			<td rowspan="<?php echo $priceInfoRows-1;?>">
				<div class="purchase_box">
					<div class="icon_eq"> </div>
					<div class="price_box all"><span class="price"><?php echo number_format($totalPurchasePrice);?></span><span style="font-weight:normal;font-size:14px;">원</span></div>
				</div>
			</td>
			<?php }?>
		</tr>
		<?php
			$iCount++;
		}?>
		</tbody>
	</table>
</div>
<?php }?>

</form>
<?php
$gpa = sql_fetch("select * from {$g5['g5_shop_group_purchase_addr_table']} where mb_id = '".$member['mb_id']."'");

$gp_tax1 = $gp_tax1 = $gp_tax3 = $gp_sel_addr1 = $gp_sel_addr2= "";
if($gpa['gp_tax']=="0")$gp_tax1 = " checked";
elseif($gpa['gp_tax']=="1")$gp_tax2 = " checked";
else $gp_tax3 = " checked";

if($gpa['gp_sel_addr']=="new")$gp_sel_addr2 = " checked";
else $gp_sel_addr1 = " checked";
?>
<form name="forderform" method="post" action="cart_gp_addr_update.php" autocomplete="off" style="background:#fff !important">
<section class="sod_bsk_pay" style="margin-top:0px !important">
	<h2>현금영수증</h2>
	<div class="tbl_frm01 tbl_wrap">
		<table>
			<tr>
			<td>
				<p class="payContainer">
				<input type="radio" name="gp_tax" value="0"<?php echo $gp_tax1?>>소득공제용&nbsp;&nbsp;
				<input type="radio" name="gp_tax" value="1"<?php echo $gp_tax2?>>지출증빙용&nbsp;&nbsp;
				<input type="radio" name="gp_tax" value=""<?php echo $gp_tax3?>>미발행&nbsp;&nbsp;
				</p>
				<p class="tax_st" style="margin:5px 0 0 0;padding:10px 0;display:none;">
				
				</p>
			</td>
			</tr>
		</table>
	</div>
</section>

<section class="sod_bsk_pay" style="background:#fff !important">
	<h2>배송지정보</h2>
	<?php
	// 배송지 이력
	$addr_list = '';
	$sep = chr(30);

	// 주문자와 동일
	$addr_list .= '<input type="radio" name="gp_sel_addr" value="same" id="ad_sel_addr_same"'.$gp_sel_addr1.'>'.PHP_EOL;
	$addr_list .= '<label for="ad_sel_addr_same">주문자와 동일</label>'.PHP_EOL;
	$addr_list .= '<input type="radio" name="gp_sel_addr" value="new" id="od_sel_addr_new"'.$gp_sel_addr2.'>'.PHP_EOL;
	$addr_list .= '<label for="od_sel_addr_new">새로운 배송지</label>'.PHP_EOL;
	?>
	<div class="sod_bsk_addr"><?php echo $addr_list; ?></div>
	<div id="sel_addr_same">
		<div class="tbl_frm01 tbl_wrap">
			<table>
			<?if(G5_IS_MOBILE) {?>
			<tr>
				<th scope="row">배송지정보</th>
				<td><b><?php echo $member['mb_name']?></b> | <?php echo $member['mb_hp']?><br/><?php echo $member['mb_addr1']?> <?php echo $member['mb_addr2']?></td>
			</tr>
			<?} else {?>
			<tr>
				<th scope="row">이름</th>
				<td><?php echo $member['mb_name']?></td>
			</tr>
			<tr>
				<th scope="row">연락처</th>
				<td><?php echo $member['mb_hp']?></td>				
			</tr>
			<tr>
				<th scope="row">주소</th>
				<td><?php echo $member['mb_addr1']?> <?php echo $member['mb_addr2']?></td>				
			</tr>
			<?}?>
			</tbody>
			</table>
		</div>
	</div>
	<div id="sel_addr_new">
		<div class="tbl_frm01 tbl_wrap">
			<table>
			
			<tr>
				<th scope="row"><label for="gp_name">이름</label></th>
				<td><input type="text" name="gp_name" id="gp_name" class="frm_input required" maxlength="20" value="<?php echo $gpa['gp_name']?>"></td>
			</tr>
			<tr>
				<th scope="row"><label for="od_b_tel">연락처</label></th>
				<td><input type="text" name="gp_hp" id="gp_hp" class="frm_input required" maxlength="20" value="<?php echo $gpa['gp_hp']?>"></td>
			</tr>

			<?php $zip_href = G5_BBS_URL.'/zip.php?frm_name=forderform&amp;frm_zip1=gp_zip1&amp;frm_zip2=gp_zip2&amp;frm_addr1=gp_addr1&amp;frm_addr2=gp_addr2&amp;frm_addr3=gp_addr3&amp;frm_jibeon=gp_addr_jibeon'; ?>
			<tr>
				<th scope="row">주소</th>
				<td id="sod_frm_addr">
					<label for="gp_zip1" class="sound_only">우편번호 앞자리<strong class="sound_only"> 필수</strong></label>
					<input type="text" name="gp_zip1" id="gp_zip1" class="frm_input required" size="3" maxlength="3" value="<?=$gpa[gp_zip1]?>">
					-
					<label for="gp_zip2" class="sound_only">우편번호 뒷자리<strong class="sound_only"> 필수</strong></label>
					<input type="text" name="gp_zip2" id="gp_zip2" class="frm_input required" size="3" maxlength="3" value="<?=$gpa[gp_zip2]?>">
					<a href="<?php echo $zip_href; ?>" class="btn_frmline win_zip_find" target="_blank">주소 검색</a><br>
					<input type="text" name="gp_addr1" id="gp_addr1" class="frm_input frm_address required" size="40" value="<?=$gpa[gp_addr1]?>">
					
					<input type="hidden" name="gp_addr2" id="gp_addr2" value="<?=$gpa[gp_addr2]?>">
					<input type="text" name="gp_addr3" id="gp_addr3" readonly="readonly" class="frm_input frm_address" size="40" value="<?=$gpa[gp_addr3]?>">
					
					<br><input type="hidden" name="gp_addr_jibeon" value="<?=$gpa[gp_addr_jibeon]?>">
					<span id="gp_addr_jibeon"></span>
				</td>
			</tr>
			</tbody>
			</table>
		</div>
	</div>
</section>
<!-- } 배송지 정보 입력 끝 -->
<div class="btn_confirm">
	<a href="#" onclick="chg_addr();return false;" class="btn03" style="display:none;">변경</a>
</div>


<?php
$result = sql_query("select * from {$g5['g5_shop_order_table']} where mb_id = '$member[mb_id]' and gp_code <> '' and od_status  = '입금대기' and od_cart_price>0");

$total = mysql_num_rows($result);
if($total>0){
?>
<section class="sod_bsk_pay">
	<h2 class="orderConfirm" style="font-size:20px;padding:30px 0;text-align:center"><?php echo $member['mb_nick']?>님의 <font style="color:#ff4e00">주문이 확인</font>되었습니다.</h2>
	<div class="tbl_frm01 tbl_wrap" style="background:#fff !important">
			<table class='orderConfirmBox' style="width:600px;font-weight:bold;" align="center">
				<tbody>
				<tr>
				<th style="background:#fff">주문완료<br>공동구매</th>
				<td>
				<?php 
				for($i=0;$row=sql_fetch_array($result);$i++){
				?>
				<div class="orderConfirm" style="font-size:17px;margin:10px 0">
				<span style="color:#545454"><?php echo $row['gp_code']?></span>
				<span style="margin: 0 20px;font-weight:bold;color:#FF7E39;"><?php echo number_format($row['od_cart_price'] + $row['od_send_cost'] + $row['od_send_cost2'])?> 원</span>
				<span style="color:#1d2088"><?php echo $row['od_status']?></span>
				</div>
				<?php }?>
				</td>
				</tr>

				<tr>
				<th style="background:#fff">입금은행</th>
				<td>				
					<div style="margin:10px 0">
					<span>신한은행</span>				
					</div>			
				</td>
				</tr>

				<tr>
				<th style="background:#fff">입금계좌</th>
				<td>				
					<div style="margin:10px 0">
					<span>110-408-552944 (예금주: 코인즈투데이,박민우)</span>				
					</div>			
				</td>
				</tr>

				

				</tbody>
			</table>

			<table>
				<tbody>
					<tr>			
						<td style="border:0px !important">				
							<div style="text-align:center;padding-top:10px">
							 <img src="<?=G5_URL?>/shop/img/guess.png">				
							</div>			
						</td>
					</tr>

					<tr>			
						<td style="border:0px !important">				
							<div class='noticeBox' style="margin-left:80px;padding:15px 50px;font-size:11px;border:2px solid #000;width:510px;line-height:17px;font-weight:bold">
							<span style="">1.상기 브랜드별 확정 금액을 합산하여 한번에 입금하시면 입금 확인이 느려질 수 있으니 번거로우시더라도 
											&nbsp<font style="color:#f45100">공동구매코드별로 따로 입금</font>하여 주시기 바라며 이점 양해 부탁드립니다.<br>
											2. 필히 <font style="color:#f45100">주문 금액과 입금자명을 동일</font>하게 입금 부탁드립니다.</span>				
							</div>			
						</td>
					</tr>

					<tr>			
						<td style="border:0px !important">				
							<div class="notice" style="text-align:center;margin-left:80px;padding:0px 50px;font-size:11px;width:500px;line-height:17px;font-weight:normal">
							<span style="">입금확인이 되어야 배송을 시작합니다. 입금기한 내에 해당 은행의 계좌번호로 입금 부탁드립니다. <br>
											구매내역을 마이페이지에서 확인 가능합니다.</span>				
							</div>			
						</td>
					</tr>

					<tr>			
						<td style="border:0px !important">				
							<div style="text-align:center;padding:10px 0 30px 0">
								<?if(G5_IS_MOBILE) {?>
								<a href="<?=G5_URL?>/shop/orderinquiry.php"><img src="<?=G5_URL?>/shop/img/gp_mypagebn.png"></a>
								<?} else {?>
								<a href="<?=G5_URL?>/shop/orderinquiry_gp.php?cate1=&ct_type_status=1"><img src="<?=G5_URL?>/shop/img/gp_mypagebn.png"></a>
								<?}?>

							</div>			
						</td>
					</tr>

				</tbody>
			</table>
	</div>
</section>
<?php 
}
?>

<script type="text/javascript">

$(document).ready(function(){
	$("input[name='gp_tax']").click(function(){
		var gp_tax = $(this).val();

		if(gp_tax == '0'){
			$(".tax_st").css("display", "block");
			$(".tax_st").html('<table><tr><th scope="row">휴대폰번호</th><td><input type="text" name="gp_tax_number" class="frm_input required" size="20"></td></tr></table>');
		}else if(gp_tax == '1'){
			$(".tax_st").css("display", "block");
			$(".tax_st").html('<table><tr><th scope="row">사업자번호</th><td><input type="text" name="gp_tax_number" class="frm_input required" size="20"></td></tr></table>');
		}else{
			$(".tax_st").css("display", "none");
		}

	});
	<?php
	if($gpa['gp_tax']){
	?>
		$(".tax_st").css("display", "block");
		<?php if($gpa['gp_tax']=="0"){?>
		$(".tax_st").html('휴대폰번호 <input type="text" name="gp_tax_number" value="<?php echo $gpa[gp_tax_number]?>" class="frm_input required" size="20">');
		<?php }elseif($gpa['gp_tax']=="1"){?>
		$(".tax_st").html('사업자번호 <input type="text" name="gp_tax_number" value="<?php echo $gpa[gp_tax_number]?>" class="frm_input required" size="20">');
		<?php }
	}?>


	<?php if($gpa['gp_sel_addr']=="new"){?>
		$("#sel_addr_same").hide();
		$("#sel_addr_new").show();
	<?php }?>

});

function chg_addr(){
	var f = document.forderform;
	if(confirm("위와같이 정보를 변경하시겠습니까?")){
		f.submit();
	}else return;

}

function all_checked(sw) {
    var f = document.frmcartlist;

    for (var i=0; i<f.length; i++) {
        if (f.elements[i].name == "ct_chk[]")
            f.elements[i].checked = sw;
    }
}


$(function() {
	if(g5_is_mobile) {
		$('.mobile #my_month_tab .my_month_bn').append("<p>조회</p>");
		$('.searchContainer').append($('.descContainer'));

		$('#brandPriceInfo').before("<div class='totalAmountTitle'>현재 공동구매 신청 상품의 <b>총결제예상금액</b>입니다.</div>");

		// 계속 쇼핑하기 버튼 이동
		$('.totalAmountTitle').before($('#sod_bsk_act'));
		$('#sod_bsk_act').css({'padding-bottom':'0', 'margin-top':'15px'});

		$('.brandPrice').after('<div class="info"><i class="fa fa-info-circle"></i><i> 배송비는 브랜드별로 부과됩니다.</i></div>');
		$('.sod_bsk_pay h2').eq(0).after('<div class="info info1"><i class="fa fa-info-circle"></i><i> 원하시는 발급 신청 정보를 선택하신 후 입력해 주시기 바랍니다.</i></div>');
		$('.sod_bsk_pay h2').eq(1).after('<div class="info info1"><i class="fa fa-info-circle"></i><i> 배송지 수정을 하지 않을 경우 회원정보와 동일한 주소지로 배송 됩니다.</i></div>');

		$('#ft').css('padding-top', 0);
	}

	//상품 전체선택
	$(".pro_all_chk").click(function(){
		all_checked(true);
	});

	//상품 선택해제
	$(".pro_all_rel").click(function(){
		all_checked(false);
	});

	$("#ad_sel_addr_same").click(function(){
		$("#sel_addr_same").show();
		$("#sel_addr_new").hide();
	});

	$("#od_sel_addr_new").click(function(){
		$("#sel_addr_same").hide();
		$("#sel_addr_new").show();
	});


	// 라디오버튼 첫번째 버튼 선택
	$('.payContainer input').eq(0).trigger('click');
	$('.sod_bsk_addr input').eq(0).trigger('click');

	$('.sod_bsk_addr input').click(function() {
		switch(this.value) {
			case "same":
				$('.btn03').hide();
			break;

			case "new":
				$('.btn03').show();
			break;
		}
		console.log(this.value);
	})
});


function form_check(act, num, idx) {
    var f = document.frmcartlist;

	var data = "";

    if (act == "buy")
    {
		$("input[name='ct_chk[]']").attr("checked", false);
		$(".ct_chk_inac").attr("checked", true);

        if($("input[name^=ct_chk]:checked").size() < 1) {
            alert("주문하실 상품을 하나이상 선택해 주십시오.");
            return false;
        }

        f.act.value = act;
        f.submit();
    }
    else if (act == "alldelete")
    {
		$(".ct_chk_inac").attr("checked", false);

        f.act.value = act;
        f.submit();
    }
    else if (act == "seldelete")
    {
        if($("input[name^=ct_chk]:checked").size() < 1) {
            alert("삭제하실 상품을 하나이상 선택해 주십시오.");
            return false;
        }

        f.act.value = act;
        f.submit();
    }

    return false;
}

</script>

<link type="text/css" href="http://ajax.googleapis.com/ajax/libs/jqueryui/1.8.4/themes/base/jquery-ui.css" rel="stylesheet" />
<script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jqueryui/1.8.4/jquery-ui.min.js"></script>
<script>

$(document).ready(function(){
	$("input[name='my_sch_val']").focus(function(){
		$("input[name='my_sch_val']").val("");
		$("input[name='my_sch_val']").attr('edited','y');
	});

	$(".ex_bn").click(function(){
		var idx = $(this).attr("idx");
		var od_id = $(this).attr("od_id");
		$("input[name='ex_bn_input["+idx+"]']").val(od_id);
		$("form[name='forderinquiry']").attr("action", "./itemexchange.php").submit();
	});
});

jQuery(function($){
	$.datepicker.regional['ko'] = {
		closeText: '닫기',
		prevText: '이전달',
		nextText: '다음달',
		currentText: '오늘',
		monthNames: ['1월(JAN)','2월(FEB)','3월(MAR)','4월(APR)','5월(MAY)','6월(JUN)',
		'7월(JUL)','8월(AUG)','9월(SEP)','10월(OCT)','11월(NOV)','12월(DEC)'],
		monthNamesShort: ['1월','2월','3월','4월','5월','6월',
		'7월','8월','9월','10월','11월','12월'],
		dayNames: ['일','월','화','수','목','금','토'],
		dayNamesShort: ['일','월','화','수','목','금','토'],
		dayNamesMin: ['일','월','화','수','목','금','토'],
		weekHeader: 'Wk',
		dateFormat: 'yymmdd',
		firstDay: 0,
		isRTL: false,
		showMonthAfterYear: true,
		yearSuffix: ''};
	$.datepicker.setDefaults($.datepicker.regional['ko']);

    $('#my_sch_dt_f').datepicker({
        showOn: 'button',
		buttonImage: '../img/my_sch_dt_ico.gif',
		buttonImageOnly: true,
        buttonText: "달력",
        changeMonth: true,
		changeYear: true,
        showButtonPanel: true,
        yearRange: 'c-99:c+99',
        maxDate: '+0d'
    }); 

	$('#my_sch_dt_l').datepicker({
        showOn: 'button',
		buttonImage: '../img/my_sch_dt_ico.gif',
		buttonImageOnly: true,
        buttonText: "달력",
        changeMonth: true,
		changeYear: true,
        showButtonPanel: true,
        yearRange: 'c-99:c+99',
        maxDate: '+0d'
    }); 
});

$(document).ready(function(){
	/* 1주일 1개월 3개월 기간버튼에 대한 버튼리스너 */
	$(".date_diff").find("div").click(function(){
		var idx = $(".date_diff").find("div").index($(this));
		var settingDate = new Date(<?=date("Y")?>, <?=date("m")?>, <?=date("d")?>);
		$(".date_diff").find("div").each(function(i){
			
			if(i == idx){
				$(".date_diff").find("div").eq(i).addClass("on");
				if(idx == 0){
					$("input[name='my_sch_dt_f']").val("<?php echo date("Ymd", strtotime("-7 day"));?>");
					$("input[name='my_sch_dt_l']").val("<?php echo date("Ymd");?>");
					$("input[name='date_diff']").val("0");
				}else if(idx == 1){
					$("input[name='my_sch_dt_f']").val("<?php echo date("Ymd", strtotime("-1 month"));?>");
					$("input[name='my_sch_dt_l']").val("<?php echo date("Ymd");?>");
					$("input[name='date_diff']").val("1");
				}else if(idx == 2){
					$("input[name='my_sch_dt_f']").val("<?php echo date("Ymd", strtotime("-3 month"));?>");
					$("input[name='my_sch_dt_l']").val("<?php echo date("Ymd");?>");
					$("input[name='date_diff']").val("2");
				}else{
					$("input[name='my_sch_dt_f']").val("");
					$("input[name='my_sch_dt_l']").val("");
					$("input[name='date_diff']").val("");
				}
			}else{
				$(".date_diff").find("div").eq(i).removeClass("on");
			}
		});
	});
	/* 조회하기 */
	$("#btnReqGpList").click(function(){
		createRealGpListItems($('.brandCategoryList.on').attr('ca_id'));
	});
});

</script>
	
	
<!-- } 장바구니 끝 -->

<?php
if (G5_IS_MOBILE) {
	include_once(G5_MSHOP_PATH.'/tail.php');
} else {
	include_once(G5_PATH.'/tail.php');
}