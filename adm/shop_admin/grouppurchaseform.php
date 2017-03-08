<?php
$sub_menu = '500200';
$sub_sub_menu = '2';

include_once('./_common.php');
include_once(G5_EDITOR_LIB);
include_once(G5_LIB_PATH.'/iteminfo.lib.php');

auth_check($auth[$sub_menu], "w");

$html_title = "공동구매 ";

if ($w == "")
{
	$html_title .= "입력";

	// 옵션은 쿠키에 저장된 값을 보여줌. 다음 입력을 위한것임

	$gp['ca_id'] = "2010";
	if (!$gp['ca_id'])
	{
		$sql = " select ca_id from {$g5['g5_shop_category_table']} order by ca_id limit 1 ";
		$row = sql_fetch($sql);
		if (!$row['ca_id'])
			alert("등록된 분류가 없습니다. 우선 분류를 등록하여 주십시오.", './categorylist.php');
		$gp['ca_id'] = $row['ca_id'];
	}

	$gp['gp_metal_don'] = 0;
	$gp['gp_price_type'] = "N";
}
else if ($w == "u")
{
	$html_title .= "수정";

	if ($is_admin != 'super')
	{
		$sql = "	SELECT	it_id
 							FROM		{$g5['g5_shop_group_purchase_table']} a,
 											{$g5['g5_shop_category_table']} b
							WHERE		a.gp_id = '$gp_id'
							AND			a.ca_id = b.ca_id
							AND			b.ca_mb_id = '{$member['mb_id']}'
		";
		$row = sql_fetch($sql);
		if (!$row['it_id'])
			alert("\'{$member['mb_id']}\' 님께서 수정 할 권한이 없는 상품입니다.");
	}

	$sql = "SELECT	GP.*,
									CA.ca_name
 					FROM		{$g5['g5_shop_group_purchase_table']} GP
 									LEFT JOIN {$g5['g5_shop_category_table']} CA ON (CA.ca_id = GP.ca_id)
 					WHERE		gp_id = '$gp_id'
 	";
	$gp = sql_fetch($sql);

	if (!$ca_id)
		$ca_id = $gp['ca_id'];

	$sql = " select * from {$g5['g5_shop_category_table']} where ca_id = '$ca_id' ";
	$ca = sql_fetch($sql);
}
else
{
	alert();
}

$qstr	= $qstr.'&amp;sca='.$sca.'&amp;page='.$page;

$g5['title'] = $html_title;
include_once (G5_ADMIN_PATH.'/admin.head.php');

// 분류리스트
$category_select = '';
$script = '';

/*셀렉트박스 카테고리 옵션 데이터*/
$sql = "	SELECT	 *
 					FROM		g5_shop_category CT
 					WHERE		CT.ca_type = 'C01'
";
if ($is_admin != 'super')	$sql .= " where ca_mb_id = '{$member['mb_id']}' ";
$sql .= " order by ca_id ";


$result = sql_query($sql);

for ($i=0; $row=sql_fetch_array($result); $i++)
{
	$len = strlen($row['ca_id']) / 2 - 1;

	$nbsp = "";
	for ($i=0; $i<$len; $i++)
		$nbsp .= "&nbsp;&nbsp;&nbsp;";

	$category_select .= "<option value=\"{$row['ca_id']}\">$nbsp{$row['ca_name']}</option>\n";

	$script .= "ca_use['{$row['ca_id']}'] = {$row['ca_use']};\n";
	$script .= "ca_stock_qty['{$row['ca_id']}'] = {$row['ca_stock_qty']};\n";
	//$script .= "ca_explan_html['$row[ca_id]'] = $row[ca_explan_html];\n";
	$script .= "ca_sell_email['{$row['ca_id']}'] = '{$row['ca_sell_email']}';\n";
}

$pg_anchor ='<ul class="anchor">
<li><a href="#anc_sitfrm_cate">상품분류</a></li>
<li><a href="#anc_sitfrm_ini">기본정보</a></li>
<li><a href="#anc_sitfrm_sendcost">배송비</a></li>
<li><a href="#anc_sitfrm_goods">상품정보</a></li>
</ul>
';

$frm_submit = '<div class="btn_confirm01 btn_confirm">
	<a href="'.G5_SHOP_URL.'/grouppurchase.php?gp_id='.$gp_id.'" class="btn_frmline">상품확인</a>
	<input type="submit" value="확인" class="btn_submit" accesskey="s">
	<a href="./grouppurchaselist.php?'.$qstr.'">목록</a>
</div>';

if($gp['gp_type1'] == "" || $gp['gp_type2'] == "" || $gp['gp_type3'] == "" || $gp['gp_type4'] == "" || $gp['gp_type5'] == ""){
	$gp_type0 = 1;
}

$item_type_icon_res = sql_query("select * from {$g5['g5_gp_item_type_icon_table']} order by no desc ");
?>

<form name="fitemform" action="./grouppurchaseformupdate.php" method="post" enctype="MULTIPART/FORM-DATA" autocomplete="off" onsubmit="return fitemformcheck(this)">

<input type="hidden" name="mode" value="<?=$mode?>" />
<input type="hidden" name="codedup" value="<?=$default['de_code_dup_use']?>">
<input type="hidden" name="w" value="<?=$w?>">
<input type="hidden" name="sca" value="<?=$sca?>">
<input type="hidden" name="sst" value="<?=$sst?>">
<input type="hidden" name="sod"	value="<?=$sod?>">
<input type="hidden" name="sfl" value="<?=$sfl?>">
<input type="hidden" name="stx"	value="<?=$stx?>">
<input type="hidden" name="page" value="<?=$page?>">

<section id="anc_sitfrm_cate">
	<h2 class="h2_frm">상품분류</h2>
	<?=$pg_anchor?>
	<div class="local_desc02 local_desc">
		<p>기본분류는 반드시 선택하셔야 합니다.</p>
	</div>

	<div class="tbl_frm01 tbl_wrap">
		<table>
		<caption>상품분류 입력</caption>
		<colgroup>
			<col class="grid_4">
			<col>
		</colgroup>

		<tbody>
		<tr>
			<th scope="row"><label for="ca_id">카테고리</label></th>
			<td>
				<?=$gp[ca_name]?> <input type="text" name="ca_id"	value="<?=$gp[ca_id]?>">
			</td>
		</tr>

		<?
		/*
		<tr>
			<th scope="row"><label for="ca_id">기본분류</label></th>
			<td>
				<?php if ($w == "") echo help("기본분류를 선택하면, 판매/재고/HTML사용/판매자 E-mail 등을, 선택한 분류의 기본값으로 설정합니다.")?>
				<select name="ca_id" id="ca_id" onchange="categorychange(this.form)">
					<option value="">선택하세요</option>
					<?=conv_selected_option($category_select, $gp['ca_id'])?>
				</select>
				<script>
					var ca_use = new Array();
					var ca_stock_qty = new Array();
					//var ca_explan_html = new Array();
					var ca_sell_email = new Array();
					var ca_opt1_subject = new Array();
					var ca_opt2_subject = new Array();
					var ca_opt3_subject = new Array();
					var ca_opt4_subject = new Array();
					var ca_opt5_subject = new Array();
					var ca_opt6_subject = new Array();
					<?="\n$script"?>
				</script>
			</td>
		</tr>

		<?php for ($i=2; $i<=3; $i++) { ?>
		<tr>
			<th scope="row"><label for="ca_id<?=$i?>"><?=$i?>차 분류</label></th>
			<td>
				<?=help($i.'차 분류는 기본 분류의 하위 분류 개념이 아니므로 기본 분류 선택시 해당 상품이 포함될 최하위 분류만 선택하시면 됩니다.')?>
				<select name="ca_id<?=$i?>" id="ca_id<?=$i?>">
					<option value="">선택하세요</option>
					<?=conv_selected_option($category_select, $gp['ca_id'.$i])?>
				</select>
			</td>
		</tr>
		<?php } ?>
		*/
		?>

		<tr>
			<th>해외B2B</th>
			<td><input type='checkbox' name='b2b_yn' value='Y' <?=($gp[b2b_yn]=='Y')?'checked':''?>>B2B등록</td>
		</tr>
		<tr>
			<th>이벤트 분류</th>
			<td><input type='checkbox' name='event_yn' value='Y' <?=($gp[event_yn]=='Y')?'checked':''?>>공동구매 추천상품</td>
		</tr>
		</tbody>
		</table>
	</div>
</section>

<?=$frm_submit?>

<section id="anc_sitfrm_ini">
	<h2 class="h2_frm">기본정보</h2>
	<?=$pg_anchor?>
	<div class="tbl_frm01 tbl_wrap">
		<table>
		<caption>기본정보 입력</caption>
		<colgroup>
			<col class="grid_4">
			<col>
		</colgroup>
		<tbody>
		<tr>
			<th scope="row">상품코드</th>
			<td>
				<?php if ($w == '') { // 추가 ?>
					<!-- 최근에 입력한 코드(자동 생성시)가 목록의 상단에 출력되게 하려면 아래의 코드로 대체하십시오. -->
					<!-- <input type=text class=required name=it_id value="<?=10000000000-time()?>" size=12 maxlength=10 required> <a href='javascript:;' onclick="codedupcheck(document.all.it_id.value)"><img src='./img/btn_code.gif' border=0 align=absmiddle></a> -->
					<?=help("상품의 코드는 10자리 숫자로 자동생성합니다. <b>직접 상품코드를 입력할 수도 있습니다.</b>\n상품코드는 영문자, 숫자, - 만 입력 가능합니다.")?>
					<input type="text" name="gp_id" value="<?=time()?>" id="it_id" required class="frm_input required" size="50" >
					<!-- <?php if ($default['de_code_dup_use']) { ?><button type="button" class="btn_frmline" onclick="codedupcheck(document.all.it_id.value)">중복검사</a><?php } ?> -->
				<?php } else { ?>
					<input type="hidden" name="gp_id" value="<?=$gp['gp_id']?>">
					<span class="frm_ca_id"><?=$gp['gp_id']?></span>
					<a href="<?=G5_SHOP_URL?>/item.php?it_id=<?=$gp_id?>" class="btn_frmline">상품확인</a>
					<a href="<?=G5_ADMIN_URL?>/shop_admin/itemuselist.php?sfl=a.it_id&amp;stx=<?=$gp_id?>" class="btn_frmline">사용후기</a>
					<a href="<?=G5_ADMIN_URL?>/shop_admin/itemqalist.php?sfl=a.it_id&amp;stx=<?=$gp_id?>" class="btn_frmline">상품문의</a>
				<?php } ?>
			</td>
		</tr>



		<tr>
			<th scope="row"><label for="gp_site">상품주소</label></th>
			<td colspan="2">
				<?=help("외부사이트에서 가져올 URL 주소를 입력합니다.")?>
				<input type="text" name="gp_site" value="<?=get_text(cut_str($gp['gp_site'], 250, ""))?>" id="gp_site" class="frm_input" size="95">
			</td>
		</tr>

		 <!--tr>
			<th scope="row"><label for="gp_objective_price">목표금액</label></th>
			<td colspan="2">
				 <input type="text" name="gp_objective_price" value="<?=$gp['gp_objective_price']?>" id="gp_objective_price" required class="frm_input required" size="10"> 원
			</td>
		</tr-->

		<tr>
			<th scope="row"><label for="gp_charge">수수료</label></th>
			<td>
				<input type="text" name="gp_charge" value="<?=$gp['gp_charge']?>" id="gp_charge" required class="required frm_input" size="4"> %
			</td>
		</tr>

		<tr>
			<th scope="row"><label for="gp_duty">관세</label></th>
			<td>
				<input type="text" name="gp_duty" value="<?=$gp['gp_duty']?>" id="gp_duty" required class="required frm_input" size="4"> %
			</td>
		</tr>


		<tr>
			<th scope="row"><label for="gp_order">출력순서</label></th>
			<td>
				<?=help("숫자가 작을 수록 상위에 출력됩니다. 음수 입력도 가능하며 입력 가능 범위는 -2147483648 부터 2147483647 까지입니다.\n<b>입력하지 않으면 자동으로 출력됩니다.</b>")?>
				<input type="text" name="gp_order" value="<?=$gp['gp_order']?>" id="gp_order" class="frm_input" size="12">
			</td>
		</tr>
		<tr>
			<th scope="row"><label for="gp_use">판매가능</label></th>
			<td>
				<?=help("잠시 판매를 중단하거나 재고가 없을 경우에 체크를 해제해 놓으면 출력되지 않으며, 주문도 받지 않습니다.")?>
				<input type="checkbox" name="gp_use" value="1" id="gp_use" <?=($gp['gp_use']) ? "checked" : ""?>> 예
			</td>
		</tr>
		<tr>
			<th scope="row"><label for="gp_use">상품유형</label></th>
			<td>
				<?=help("메인화면에 유형별로 출력할때 사용합니다.\n이곳에 체크하게되면 상품리스트에서 유형별로 정렬할때 체크된 상품이 가장 먼저 출력됩니다.")?>
				<!--<input type="checkbox" name="it_type1" value="1" <?php// echo ($it['it_type1'] ? "checked" : "")?> id="it_type1">
				<label for="it_type1">히트 <img src="<?php// echo G5_SHOP_URL?>/img/icon_hit2.gif" alt=""></label>
				<input type="checkbox" name="it_type2" value="1" <?php// echo ($it['it_type2'] ? "checked" : "")?> id="it_type2">
				<label for="it_type2">추천 <img src="<?php// echo G5_SHOP_URL?>/img/icon_rec2.gif" alt=""></label>-->
				<input type="radio" name="it_type" value="" id="it_type" <?=($it['it_type'] == "" ? "checked" : "")?>>
				<label for="it_type">선택없음</label>

				<?
				for($i = 0; $item_type_icon_row = mysql_fetch_array($item_type_icon_res); $i++){
				?>

				<input type="radio" name="it_type" value="<?=$item_type_icon_row[no]?>" <?=($gp['it_type'] == $item_type_icon_row[no] ? "checked" : "")?> id="it_type<?=$item_type_icon_row[no]?>">
				<label for="it_type<?=$item_type_icon_row[no]?>"><?=$item_type_icon_row[tp_name]?> <img src="<?=G5_URL?>/data/gpitem_type_icon/<?=$item_type_icon_row[tp_img]?>" alt=""></label>

				<?
				}
				?>

				<!--<input type="radio" name="it_type" value="3" <?php// echo ($it['it_type3'] ? "checked" : "")?> id="it_type3">
				<label for="it_type3">신상품 <img src="<?php// echo G5_SHOP_URL?>/img/icon_new2.gif" alt=""></label>
				<input type="radio" name="it_type" value="4" <?php// echo ($it['it_type4'] ? "checked" : "")?> id="it_type4">
				<label for="it_type4">인기 <img src="<?php// echo G5_SHOP_URL?>/img/icon_best2.gif" alt=""></label>
				<input type="radio" name="it_type" value="5" <?php //echo ($it['it_type5'] ? "checked" : "")?> id="it_type5">
				<label for="it_type5">할인 <img src="<?php //echo G5_SHOP_URL?>/img/icon_discount2.gif" alt=""></label>
				<input type="radio" name="it_type" value="6" <?php// echo ($it['it_type6'] ? "checked" : "")?> id="it_type6">
				<label for="it_type6">Auction <img src="<?php// echo G5_SHOP_URL?>/img/icon_auction2.gif" alt=""></label>
				-->
			</td>
		</tr>
		</tbody>
		</table>
	</div>
</section>


<section id="anc_sitfrm_sendcost">
	<h2 class="h2_frm">배송비</h2>
	<?=$pg_anchor?>
	<div class="local_desc02 local_desc">
		<p>쇼핑몰설정 &gt; 배송비유형 설정보다 <strong>개별상품 배송비설정이 우선</strong> 적용됩니다.</p>
	</div>

	<div class="tbl_frm01 tbl_wrap">
		<table>
		<caption>배송비 입력</caption>
		<colgroup>
			<col class="grid_4">
			<col>
			<col class="grid_3">
		</colgroup>
		<tbody>

			<tr id="sc_con_method">
				<th scope="row"><label for="gp_sc_method">배송비 결제</label></th>
				<td>
					<select name="gp_sc_method" id="gp_sc_method">
						<option value="0"<?=get_selected('0', $gp['gp_sc_method'])?>>선불</option>
						<option value="1"<?=get_selected('1', $gp['gp_sc_method'])?> selected>착불</option>
						<option value="2"<?=get_selected('2', $gp['gp_sc_method'])?>>사용자선택</option>
					</select>
				</td>
			</tr>
			<tr id="sc_con_basic">
				<th scope="row"><label for="gp_sc_price">기본배송비</label></th>
				<td>
					<?=help("무료배송 이외의 설정에 적용되는 배송비 금액입니다.")?>
					 <input type="text" name="gp_sc_price" value="<?php if($gp['gp_sc_price']){ echo $gp['gp_sc_price'];}else{echo "3500";} ?>" id="gp_sc_price" class="frm_input" size="4"> 원
				</td>
			</tr>

		</tbody>
		</table>
	</div>

</section>


<?=$frm_submit?>

<section id="anc_sitfrm_ini">
	<h2 class="h2_frm">상품정보</h2>
	<?=$pg_anchor?>
	<div class="tbl_frm01 tbl_wrap">
		<table>
		<caption>상품정보 입력</caption>
		<colgroup>
			<col class="grid_4">
			<col>
		</colgroup>
		<tbody>

		<tr>
			<th scope="row"><label for="gp_name">상품명</label></th>
			<td>
				<input type="text" name="gp_name" value='<?=get_text(cut_str($gp['gp_name'], 250, ""))?>' id="gp_name" class="frm_input" size="95">
			</td>
		</tr>

		<tr>
			<th scope="row"><label for="gp_img">상품이미지URL</label></th>
			<td>
				<?=help("외부사이트에서 가져올 이미지 URL 주소를 입력합니다.")?>
				<input type="text" name="gp_img" value="<?=$gp['gp_img']?>" id="gp_img" class="frm_input" size="95">
			</td>
		</tr>

		<tr>
			<th scope="row"><label for="gp_explan">상품설명</label></th>
			<td><?=editor_html("gp_explan", $gp['gp_explan'])?></td>
		</tr>

		<tr>
			<th scope="row"><label for="gp_360img">360도 소스</label></th>
			<td><?=editor_html("gp_360img", $gp['gp_360img'])?></td>
		</tr>
		
		<tr>
			<th scope="row"><label for="gp_metal_don">금속 정보</label></th>
			<td colspan="2">
			<select name="gp_metal_type" id="gp_metal_type">
				<option value="">-선택안함-</option>
				<?php foreach($goodsMetalListArray as $key=>$vars){?>
				<option value="<?=$key?>"<?php if($key==$gp[gp_metal_type])echo " selected";?>><?=$vars?></option>
				<?php }?>
			</select> /	<input type="text" name="gp_metal_don" value="<?=$gp['gp_metal_don']?>" id="gp_metal_don" required class="required frm_input" size="5"> oz,t
			<span id="gp_metal_etc_price_view"<?if($gp[gp_metal_type]!="EC")echo ' style="display:none;"';?>> / 기타 금속 가격 <input type="text" name="gp_metal_etc_price" value="<?=$gp['gp_metal_etc_price']?>" id="gp_metal_etc_price" class="frm_input" size="8"> USD</span>
			</td>
		</tr>

		<tr>
			<th scope="row"><label for="gp_metal_don">상품 가격 타입</label></th>
			<td colspan="2">

							<input type="radio" name="gp_price_type" id="gp_fix_price_type" value="W" <?php if($gp[gp_price_type]=="W")echo "checked";?>> 원화기준
							<input type="radio" name="gp_price_type" id="gp_fix_price_type" value="N" <?php if($gp[gp_price_type]=="N")echo "checked";?>> 달러기준
							<input type="radio" name="gp_price_type" id="gp_real_price_type" value="Y" <?php if($gp[gp_price_type]=="Y")echo "checked";?>> 스팟시세기준

			</td>
		</tr>


		<tr>
			<th scope="row">상품수량별 가격</th>
			<td colspan="2">
				<div class="sit_option tbl_frm01">
					<table>
					<caption>상품수량별 가격 입력</caption>
					<colgroup>
						<col class="grid_8">
						<col class="grid_7">
						<col>
					</colgroup>
					<tbody>
					<?php for($i=0;$i<7;$i++){
						$gpo = sql_fetch("select * from $g5[g5_shop_group_purchase_option_table] where gp_id = '".$gp[gp_id]."' and po_num = '".$i."'");

						$po_qty_type = 0;
						if($gpo[po_sqty]==0 and $gpo[po_eqty]==99999){
							$gpo[po_sqty] = $gpo[po_eqty] = "";
							$po_qty_type = 1;
						}
						?>
					<tr>
						<th scope="row">
							<label for="opt1_subject">수량</label>
							<input type="text" name="po_sqty[<?=$i?>]" value="<?=$gpo[po_sqty]?>" id="po_sqty<?=$i?>" class="frm_input" size="5"> ~
							<input type="text" name="po_eqty[<?=$i?>]" value="<?=$gpo[po_eqty]?>" id="po_eqty<?=$i?>" class="frm_input" size="5">
							<input type="radio" name="po_qty_type[<?=$i?>]" value="1" <?php if($po_qty_type==1)echo "checked";?>> 모든수량
							<input type="radio" name="po_qty_type[<?=$i?>]" value="0" <?php if($po_qty_type==0)echo "checked";?>> 선택수량
						</th>
						<td>
							<label for="opt1"><b>실시간용 추가금액</b></label>
							USD <input type="text" name="po_add_price[<?=$i?>]" value="<?=$gpo[po_add_price]?>" id="po_add_price<?=$i?>" class="frm_input" size="20">
						</td>
						<td>
							<label for="opt1"><b>현금가</b></label>
							USD <input type="text" name="po_cash_price[<?=$i?>]" value="<?=$gpo[po_cash_price]?>" id="po_cash_price<?=$i?>" class="frm_input" size="20">
						</td>
						<td>
							<label for="opt1"><b>카드가</b></label>
							USD <input type="text" name="po_card_price[<?=$i?>]" value="<?=$gpo[po_card_price]?>" id="po_card_price<?=$i?>" class="frm_input" size="20">
						</td>
					</tr>
					<?php }?>
					</tbody>
					</table>
				</div>
			</td>
		</tr>


		<tr>
			<th scope="row">
				옵션추가</br>
				<input type="text" name="op_title_add" class="frm_input" size="7">
				<input type="button" value="+" style="padding:0px 3px 14px 3px;height:18px;" class="add_op_bn">
			</th>
			<td>

				<table border="0" cellspacing="0" cellpadding="0" width="100%" class="add_op">

					<?
					$op_res = sql_query("select * from {$g5['g5_shop_option1_table']} where it_id='".$gp_id."' and gubun='P' order by no asc ");
					for($i = 0; $op_row = mysql_fetch_array($op_res); $i++){
					?>
					<tr class='sub_tr'>
						<td>
							<label for='opt1'><b>타이틀</b></label>
							<input type='text' class='frm_input' name='gp_op_title[]' id='gp_op_title' size='10' value='<?=$op_row[con]?>'>
							<input type="button" value="+" style="padding:0px 3px 14px 3px;height:18px;" onclick="add_op_bn1($(this))">
							<input type="button" value="-" style="padding:0px 3px 14px 3px;height:18px;" onclick="add_op_del($(this));">
						</td>
						<td>
							<table border="0" cellspacing="0" cellpadding="0" width="100%" class="add_op_sub">

								<?
								$op2_res = sql_query("select * from {$g5['g5_shop_option2_table']} where num='".$op_row[no]."' and it_id='".$gp_id."' order by no asc ");
								for($k = 0; $op2_row = mysql_fetch_array($op2_res); $k++){
								?>
								<tr>
									<td>
										<label for="opt1"><b>옵션명</b></label>
										<input type="text" name="gp_op_name<?=$i?>[]" value='<?=$op2_row[con]?>' id="gp_op_name" class="frm_input" size="30">
										<label for="opt1"><b>금액</b></label>
										<input type="text" name="gp_op_price<?=$i?>[]" value='<?=$op2_row[price]?>' id="gp_op_price" class="frm_input" size="30">
										<input type="button" value="-" style="padding:0px 3px 14px 3px;height:18px;" onclick="add_op_del($(this));">
									</td>
								</tr>
								<?
								}
								?>
							</table>
						</td>
					</tr>
					<?
					}
					?>
				</table>

			</td>
		</tr>


		</tbody>
		</table>
	</div>
</section>

<?=$frm_submit?>

</form>


<script>
var f = document.fitemform;

$(function() {

	$("#gp_fix_price_type, #gp_auc_price_type").live("click",function(){
		$(".realaddP").hide();
	});

	$("#it_real_price_type").live("click",function(){
		$(".realaddP").show();
	});

	$("#gp_metal_type").change(function(){
		if($(this).val()=="EC")$("#gp_metal_etc_price_view").show();
		else $("#gp_metal_etc_price_view").hide();
	});

});

$(document).ready(function(){

	//옵션 추가
	$(".add_op_bn").click(function(){
		var title = $("input[name='op_title_add']").val();
		var con;
		var idx = $(".sub_tr").length;
		con = "<tr class='sub_tr'>";
		con += "<td>";
		con += "<label for='opt1'><b>타이틀</b></label> ";
		con += "<input type='text' class='frm_input' name='gp_op_title[]' id='gp_op_title' size='10' value='"+title+"'> ";
		con += '<input type="button" value="+" style="padding:0px 3px 14px 3px;height:18px;" onclick="add_op_bn1($(this))"> ';
		con += '<input type="button" value="-" style="padding:0px 3px 14px 3px;height:18px;" onclick="add_op_del($(this));">';
		con += '</td>';
		con += '<td>';
		con += '<table border="0" cellspacing="0" cellpadding="0" width="100%">';
		con += '<tr>';
		con += '<td>';
		con += '<label for="opt1"><b>옵션명</b></label> ';
		con += '<input type="text" name="gp_op_name'+idx+'[]" value="" id="gp_op_name" class="frm_input" size="30"> ';
		con += '<label for="opt1"><b>금액</b></label> ';
		con += '<input type="text" name="gp_op_price'+idx+'[]" value="" id="gp_op_price" class="frm_input" size="30"> ';
		con += '<input type="button" value="-" style="padding:0px 3px 14px 3px;height:18px;" onclick="add_op_del($(this));">';
		con += '</td>';
		con += '</tr>';
		con += '</table>';
		con += '</td>';
		con += '</tr>';

		$(".add_op").append(con);
	});

});

//옵션 삭제
function add_op_del(th){
	th.parent().parent().remove();
}

//하위 옵션 추가
function add_op_bn1(th){
	var idx = $(".add_op").find(".sub_tr").index(th.parent().parent());
	var con;
	con = "<tr>";
	con += "<td>";
	con += "<label for='opt1'><b>옵션명</b></label> ";
	con += "<input type='text' name='gp_op_name"+idx+"[]' value='' id='gp_op_name' class='frm_input' size='30'> ";
	con += "<label for='opt1'><b>금액</b></label> ";
	con += "<input type='text' name='gp_op_price"+idx+"[]' value='' id='gp_op_price' class='frm_input' size='30'> ";
	con += "<input type='button' value='-' style='padding:0px 3px 14px 3px;height:18px;' onclick='add_op_del($(this));'>";
	con += "</td>";
	con += "</tr>";
	$(".add_op").find(".sub_tr").eq(idx).find("table").append(con);
}

function codedupcheck(id)
{
	if (!id) {
		alert('상품코드를 입력하십시오.');
		f.it_id.focus();
		return;
	}

	var it_id = id.replace(/[A-Za-z0-9\-_\.]/g, "");
	if(it_id.length > 0) {
		//alert("상품코드는 영문자, 숫자, -, _ 만 사용할 수 있습니다.");
		//return false;
	}

	$.post(
		"./codedupcheck.php",
		{ it_id: id },
		function(data) {
			if(data.name) {
				alert("코드 '"+data.code+"' 는 '".data.name+"' (으)로 이미 등록되어 있으므로\n\n사용하실 수 없습니다.");
				return false;
			} else {
				alert("'"+data.code+"' 은(는) 등록된 코드가 없으므로 사용하실 수 있습니다.");
				document.fitemform.codedup.value = '';
			}
		}, "json"
	);
}

function fitemformcheck(f)
{
	if (!f.ca_id.value) {
		alert("기본분류를 선택하십시오.");
		f.ca_id.focus();
		return false;
	}

	<?=get_editor_js("gp_explan")?>

	<?=get_editor_js("gp_360img")?>

	if (f.w.value == "") {
		var error = "";
		$.ajax({
			url: "./ajax.gp_id.php",
			type: "POST",
			data: {
				"gp_id": f.gp_id.value
			},
			dataType: "json",
			async: false,
			cache: false,
			success: function(data, textStatus) {
				error = data.error;
			}
		});

		if (error) {
			alert(error);
			return false;
		}
	}

	return true;
}

function categorychange(f)
{
	var idx = f.ca_id.value;

	if (f.w.value == "" && idx)
	{
		f.gp_use.checked = ca_use[idx] ? true : false;
	}
}

categorychange(document.fitemform);
</script>

<?php
include_once (G5_ADMIN_PATH.'/admin.tail.php');
?>
