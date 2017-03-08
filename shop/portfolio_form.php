<?php
include_once('./_common.php');
include_once(G5_PATH.'/head.php');
?>
<form name="forderform" method="post" action="../shop/bs_cartupdate.php">
<input type="hidden" name="od_b_tel" id="od_b_tel">
<input type="hidden" name="ad_subject" id="ad_subject">

<section id="sod_frm_besong">
<div class="tbl_frm02 tbl_wrap">
<table>
	<tbody>
        <tr>
            <th scope="row">서비스</th>
            <td>배송대행</td>
        </tr>

		<tr>
            <th scope="row">물류지 구분</th>
            <td>
			<?php
			$i=0;
			foreach($shippingDistributionList as $key=>$vars){?>
			<input type="radio" name="bs_distribution" value="<?php echo $key?>"<?if($i==0)echo "checked";?>> <?php echo $vars?>&nbsp;
			<?php
				$i++;	
			}?></td>
        </tr>

		<tr>
            <th scope="row">수입 구분</th>
            <td><input type="radio" name="bs_shipper" value="1" checked> 개인&nbsp;<input type="radio" name="bs_shipper" value="2"> 사업자</td>
        </tr>

		<tr>
            <th scope="row"><label for="bs_eng_name">신청 이름<strong class="sound_only">필수</strong></label></th>
            <td><input type="text" name="bs_eng_name" value="" id="bs_eng_name" required class="frm_input required" size="20" maxlength="25"> (직구 신청시 shipping address 에 입력한 영문이름)</td>
        </tr>

		<tr>
            <th scope="row">검품 옵션</th>
            <td>
			<?php
			$i=0;
			foreach($shippingCheckingOptionList as $key=>$vars){?>
			<input type="radio" name="bs_checking_option" value="<?php echo $key?>"<?if($i==0)echo "checked";?>> <?php echo $vars?>&nbsp;
			<?php
				$i++;	
			}?></td>
        </tr>

		<tr>
            <th scope="row">주문 내역</th>
            <td>
				<div id="sit_supply_frm" class="sit_option tbl_frm03">
                    <table>
                    <caption>상품추가옵션 입력</caption>
                    <colgroup>
                        <col class="grid_4">
                        <col>
                    </colgroup>
                    <tbody>
                    <?php
                    $i = 0;
					$spl_count = 1;
                    do {
                        $seq = $i + 1;
                    ?>
                    <tr>
                        <th scope="row">
                            <label for="spl_subject_<?php echo $seq; ?>">추가<?php echo $seq; ?></label>
                            <input type="text" name="spl_subject[]" id="spl_subject_<?php echo $seq; ?>" value="<?php echo $spl_subject[$i]; ?>" class="frm_input" size="15">
                        </th>
                        <td>
                            <label for="spl_item_<?php echo $seq; ?>"><b>추가<?php echo $seq; ?> 항목</b></label>
                            <input type="text" name="spl[]" id="spl_item_<?php echo $seq; ?>" value="" class="frm_input" size="40">
                            <?php
                            if($i > 0)
                                echo '<button type="button" id="del_supply_row" class="btn_frmline">삭제</button>';
                            ?>
                        </td>
                    </tr>
                    <?php
                        $i++;
                    } while($i < $spl_count);
                    ?>
                    </tbody>
                    </table>
                    <div id="sit_option_addfrm_btn"><button type="button" id="add_supply_row" class="btn_frmline">옵션추가</button></div>
                </div>
				<table width="99%" align="center" border="0">
					<tr height="30">
					<td>
					&nbsp; [1]사이트 
					<select name = "bs_site[]">
					<option value="">-선택-</option>
					<option value="1">amazon.com</option>
					<option value="2">ebay.com</option>
					<option value="3">ralphlauren.com</option>
					<option value="4">gap.com</option>
					<option value="5">drugstore.com</option>
					<option value="6">6pm.com</option>
					<option value="14">yankeecandle.com</option>
					<option value="15">hockeymonkey.com</option>
					<option value="16">diapers.com</option>
					<option value="17">hm.com</option>
					<option value="18">carters.com</option>
					<option value="19">jcrew.com</option>
					<option value="20">coachfactory.com</option>
					</select>
					&nbsp;직접입력&nbsp; <input type="text" size="20" name="dir_bs_site[]" maxlength="100" name="ct_site" id="ct_site" style="height:22;vertical-align: middle;font-weight:bold;font-size:12px;color:#4682b4;" >
					<td align="right" style="padding-right:10px;">
					<input type="button" value="사이트+" onclick="detail_add_site('two');">&nbsp;
					<input type="button" value="3+" onclick="detail_add_site('three');">&nbsp;
					<input type="button" value="4+" onclick="detail_add_site('four');">&nbsp;

					</tr>
					<tr>
					<td colspan="2">
						<table width="96%" align="center" border="0" style="border-style:solid;border-width:1px;border-color:#cdcdcd;">
							<tr height="30">
							<td width="13%">&nbsp;상품명 
							<td width="87%" colspan="2">
							<input type="text" name="dt_name[]" size="29" maxlength="100" style="height:22;bold;font-size:12px;color:#4682b4;"> 
							가격 
							<input type="text" name="dt_price[]" size="7" maxlength="10" style="height:22;bold;font-size:12px;color:#4682b4;" onchange="dt_cal();";> 
							수량 
							<input type="text"  name="dt_num[]" size="3" value='1' maxlength="5" style="height:22;bold;font-size:12px;color:#4682b4;" onkeyup="dt_cal();"> 
							총가격 
							<input type="text"  name="dt_totprice[]" size="7" maxlength="10" style="height:22;bold;font-size:12px;color:#4682b4;" readonly>
							</tr>
							<tr height="30">
							<td>&nbsp;오더넘버
							<td>
							<input type="text" size="25" maxlength="50" name="ct_odnum[]" id="ct_odnum" style="height:22;vertical-align: middle;font-weight:bold;font-size:12px;color:#4682b4;" onkeyup="checkLogin();">&nbsp;
							옵션 <input type="text" name="ct_opt[]" size="20" style="height:22;vertical-align: middle;font-size:12px;color:#4682b4;">
							<td align="right">
							<input type="button" value="상품+ " onclick="detail_add_item();" style="color:#232323;background-color:orange;font-weight:bold;">&nbsp; 
							</tr>
							<tr height="30">
							<td>&nbsp;상품링크
							<td colspan="2"><input type="text" name="ct_buyurl[]" size="58" style="height:22;vertical-align: middle;font-size:12px;color:#4682b4;">
							</tr>
						</table>
						<span id="item_01"></span>
					</tr>
					<tr height="30">
					<td colspan="2">
					&nbsp; [1] 배송료 <input type="text" name="tmp_dt_deli[]" size="10"> 
					&nbsp; 할인금액 <input type="text" name="tmp_dt_sale[]" size="10">
					&nbsp; 세일즈텍스 <input type="text" name="tmp_dt_stax[]" size="10">
					</tr>
					<tr><td colspan="2" height="10"></tr>
				</table>

				<span id="site_02"></span>
				<span id="site_03"></span>
				<span id="site_04"></span>
			</td>
        </tr>

		<tr>
            <th scope="row">세금 여부</th>
			<td>
			&nbsp;USD&nbsp; 총가격 <input type="text" id="tmp_total" class="frm_input" size="10" readOnly>&nbsp;
			&nbsp; <input type="button" value="확인하기" onclick="total_tax();">&nbsp;
			<span id="tax_word"></span>

			<span style="color:royalblue;">
			<br>&nbsp; 의류<span style="color:#c05555;font-weight:bold;">(모자,장갑,귀마개,헤어밴드는 제외)</span>, 책, 그릇, 신발, 조명기구, CD류 를 제외한 
			<br>&nbsp; 상품들 이라면 15만원 이상인 경우 세금이 나옵니다.</span>
			</td>
        </tr>

		<tr>
            <th scope="row">요청사항</th>
			<td>
			&nbsp; <b>통합배송 관련 내용</b>을 입력해 주세요<br>
			&nbsp; <b>이베이 아이템넘버</b>는 오더번호에 넣어주세요.<br>
			&nbsp; <b>신발인 경우</b> 박스 제거 여부를 말씀해 주세요. (박스제거 - 국제배송비 ↓ , 파손위험 ↑ )<br>
			&nbsp; <textarea name="bs_memo" id="bs_memo" rows="6" class="frm_input"></textarea>
			</td>
        </tr>
	</tbody>
</table>
	</div>
</section>

<!-- 주문하시는 분 입력 시작 { -->
<section id="sod_frm_orderer">
	<h2>신청자 정보</h2>

	<div class="tbl_frm02 tbl_wrap">
		<table>
		<tbody>
		<tr>
			<th scope="row"><label for="od_name">이름</label></th>
			<td><input type="text" name="od_name" value="<?php echo $member['mb_name']; ?>" id="od_name" required class="frm_input required" maxlength="20"></td>
		</tr>


		<tr>
			<th scope="row"><label for="od_hp">핸드폰</label></th>
			<td><input type="text" name="od_hp" value="<?php echo $member['mb_hp']; ?>" id="od_hp" required class="frm_input required" maxlength="20"></td>
		</tr>
		<?php $zip_href = G5_BBS_URL.'/zip.php?frm_name=forderform&amp;frm_zip1=od_zip1&amp;frm_zip2=od_zip2&amp;frm_addr1=od_addr1&amp;frm_addr2=od_addr2&amp;frm_addr3=od_addr3&amp;frm_jibeon=od_addr_jibeon'; ?>
		<tr>
			<th scope="row">주소</th>
			<td>
				<label for="od_zip1" class="sound_only">우편번호 앞자리<strong class="sound_only"> 필수</strong></label>
				<input type="text" name="od_zip1" value="<?php echo $member['mb_zip1'] ?>" id="od_zip1" required class="frm_input required" size="3" maxlength="3">
				-
				<label for="od_zip2" class="sound_only">우편번호 뒷자리<strong class="sound_only"> 필수</strong></label>
				<input type="text" name="od_zip2" value="<?php echo $member['mb_zip2'] ?>" id="od_zip2" required class="frm_input required" size="3" maxlength="3">
				<a href="<?php echo $zip_href; ?>" class="btn_frmline win_zip_find" target="_blank">주소 검색</a><br>
				<input type="text" name="od_addr1" value="<?php echo $member['mb_addr1'] ?>" id="od_addr1" required class="frm_input frm_address required" size="60">
				<label for="od_addr1">기본주소<strong class="sound_only"> 필수</strong></label><br>
				<input type="text" name="od_addr2" value="<?php echo $member['mb_addr2'] ?>" id="od_addr2" class="frm_input frm_address" size="60">
				<label for="od_addr2">상세주소</label><br>
				<input type="text" name="od_addr3" value="<?php echo $member['mb_addr3'] ?>" id="od_addr3" readonly="readonly" class="frm_input frm_address" size="60">
				<label for="od_addr3">참고항목</label>
				<input type="hidden" name="od_addr_jibeon" value="<?php echo $member['mb_addr_jibeon']; ?>"><br>
				<span id="od_addr_jibeon"><?php echo ($member['mb_addr_jibeon'] ? '지번주소 : '.$member['mb_addr_jibeon'] : ''); ?></span>
			</td>
		</tr>
		<tr>
			<th scope="row"><label for="od_email">E-mail</label></th>
			<td><input type="text" name="od_email" value="<?php echo $member['mb_email']; ?>" id="od_email" required class="frm_input required" size="35" maxlength="100"></td>
		</tr>

		</tbody>
		</table>
	</div>
</section>
<!-- } 주문하시는 분 입력 끝 -->

<!-- 받으시는 분 입력 시작 { -->
<section id="sod_frm_taker">
	<h2>수취인 배송정보</h2>

	<div class="tbl_frm02 tbl_wrap">
		<table>
		<tbody>
		<?php
		if($is_member) {
			// 배송지 이력
			$addr_list = '';
			$sep = chr(30);

			// 주문자와 동일
			$addr_list .= '<input type="radio" name="ad_sel_addr" value="same" id="ad_sel_addr_same">'.PHP_EOL;
			$addr_list .= '<label for="ad_sel_addr_same">주문자와 동일</label>'.PHP_EOL;

			// 기본배송지
			$sql = " select *
						from {$g5['g5_shop_order_address_table']}
						where mb_id = '{$member['mb_id']}'
						  and ad_default = '1' ";
			$row = sql_fetch($sql);
			if($row['ad_id']) {
				$val1 = $row['ad_name'].$sep.$row['ad_tel'].$sep.$row['ad_hp'].$sep.$row['ad_zip1'].$sep.$row['ad_zip2'].$sep.$row['ad_addr1'].$sep.$row['ad_addr2'].$sep.$row['ad_addr3'].$sep.$row['ad_jibeon'].$sep.$row['ad_subject'];
				$addr_list .= '<input type="radio" name="ad_sel_addr" value="'.$val1.'" id="ad_sel_addr_def">'.PHP_EOL;
				$addr_list .= '<label for="ad_sel_addr_def">기본배송지</label>'.PHP_EOL;
			}

			// 최근배송지
			$sql = " select *
						from {$g5['g5_shop_order_address_table']}
						where mb_id = '{$member['mb_id']}'
						  and ad_default = '0'
						order by ad_id desc
						limit 1 ";
			$result = sql_query($sql);
			for($i=0; $row=sql_fetch_array($result); $i++) {
				$val1 = $row['ad_name'].$sep.$row['ad_tel'].$sep.$row['ad_hp'].$sep.$row['ad_zip1'].$sep.$row['ad_zip2'].$sep.$row['ad_addr1'].$sep.$row['ad_addr2'].$sep.$row['ad_addr3'].$sep.$row['ad_jibeon'].$sep.$row['ad_subject'];
				$val2 = '<label for="ad_sel_addr_'.($i+1).'">최근배송지('.($row['ad_subject'] ? $row['ad_subject'] : $row['ad_name']).')</label>';
				$addr_list .= '<input type="radio" name="ad_sel_addr" value="'.$val1.'" id="ad_sel_addr_'.($i+1).'"> '.PHP_EOL.$val2.PHP_EOL;
			}

			$addr_list .= '<input type="radio" name="ad_sel_addr" value="new" id="od_sel_addr_new">'.PHP_EOL;
			$addr_list .= '<label for="od_sel_addr_new">신규배송지</label>'.PHP_EOL;

			$addr_list .='<a href="'.G5_SHOP_URL.'/orderaddress.php" id="order_address" class="btn_frmline">배송지목록</a>';
		} else {
			// 주문자와 동일
			$addr_list .= '<input type="checkbox" name="ad_sel_addr" value="same" id="ad_sel_addr_same">'.PHP_EOL;
			$addr_list .= '<label for="ad_sel_addr_same">주문자와 동일</label>'.PHP_EOL;
		}
		?>
		<tr>
			<th scope="row">배송지선택</th>
			<td>
				<?php echo $addr_list; ?>
			</td>
		</tr>
		<tr>
			<th scope="row"><label for="od_b_name">이름</label></th>
			<td><input type="text" name="od_b_name" id="od_b_name" required class="frm_input required" maxlength="20"></td>
		</tr>
		<tr>
			<th scope="row"><label for="od_b_hp">핸드폰</label></th>
			<td><input type="text" name="od_b_hp" id="od_b_hp" required class="frm_input required" maxlength="20"></td>
		</tr>
		<?php $zip_href = G5_BBS_URL.'/zip.php?frm_name=forderform&amp;frm_zip1=od_b_zip1&amp;frm_zip2=od_b_zip2&amp;frm_addr1=od_b_addr1&amp;frm_addr2=od_b_addr2&amp;frm_addr3=od_b_addr3&amp;frm_jibeon=od_b_addr_jibeon'; ?>
		<tr>
			<th scope="row">주소</th>
			<td id="sod_frm_addr">
				<label for="od_b_zip1" class="sound_only">우편번호 앞자리<strong class="sound_only"> 필수</strong></label>
				<input type="text" name="od_b_zip1" id="od_b_zip1" required class="frm_input required" size="3" maxlength="3">
				-
				<label for="od_b_zip2" class="sound_only">우편번호 뒷자리<strong class="sound_only"> 필수</strong></label>
				<input type="text" name="od_b_zip2" id="od_b_zip2" required class="frm_input required" size="3" maxlength="3">
				<a href="<?php echo $zip_href; ?>" class="btn_frmline win_zip_find" target="_blank">주소 검색</a><br>
				<input type="text" name="od_b_addr1" id="od_b_addr1" required class="frm_input frm_address required" size="60">
				<label for="od_b_addr1">기본주소<strong class="sound_only"> 필수</strong></label>
				<input type="text" name="od_b_addr2" id="od_b_addr2" class="frm_input frm_address" size="60">
				<label for="od_b_addr2">상세주소</label>
				<input type="text" name="od_b_addr3" id="od_b_addr3" readonly="readonly" class="frm_input frm_address" size="60">
				<label for="od_b_addr3">참고항목</label>
				<input type="hidden" name="od_b_addr_jibeon" value="">
				<span id="od_b_addr_jibeon"></span>
			</td>
		</tr>
		<tr>
			<th scope="row"><label for="od_memo">배송 기사님께<br />(20자 이내)</label></th>
			<td><textarea name="od_memo" id="od_memo"></textarea></td>
		</tr>
		</tbody>
		</table>
	</div>
</section>
<!-- } 받으시는 분 입력 끝 -->




<table width="794" align="center" cellpadding="0" cellspacing="0" bgcolor="#f8f8ff">
<tr><td width="705" height="1" bgcolor="#cccccc"></td></tr>

<tr>
	<td width="705" height="50" align="center" valign="middle">
		<input type="checkbox" id="agreement" name="agreement" style="CURSOR:hand;">
		<label for='agreement'><b> <a href="http://www.modubuy.com/delivery/index.php#eye" target="_new">'배송대행 이용시 주의사항'</a> 과 <a href="http://www.modubuy.com/shop/content.php?co_id=provision" target="_new">'서비스 이용약관'</a>을 읽고 확인했으며, 이에 동의 합니다.</b></label>
	</td>
</tr>

<tr><td width="705" height="1" bgcolor="#cccccc"></td></tr>
</table>

						<table>
<tr><td width="705" height="10"></td></tr>
</table>

						<table>
</table>

<div class="btn_confirm">
	<input type="submit" value="배송대행 신청하기" class="btn_submit">
</div>

</FORM>

<script type="text/javascript">

var ar_juso = Array();



function exp_site()
{
	var form = document.fbesong;

	if(form.bs_site.value == '2')
	{
		document.getElementById("iea_03").innerHTML = "";
	}else
	{
		document.getElementById("iea_03").innerHTML = "";
	}
}

function exp_type(value)
{
	if(value == '1')
	{
		document.getElementById("iea_02").innerHTML= "";
	}else if(value == '2')
	{
		document.getElementById("iea_02").innerHTML= "요청사항에 상품링크와 상품옵션을 입력해 주세요. ";
	}else if(value == '3')
	{
		if(document.getElementById("bs_insure").checked)
		{
			document.getElementById("iea_02").innerHTML= "(총 상품가+국제운송비의 5%가 추가됩니다.)";
		}else
		{
			document.getElementById("iea_02").innerHTML= "";
		}
	}
}

function exp_shipper(value)
{
	if(value == '1' )
	{
		document.getElementById("iea_01").innerHTML = "";
	}else if(value == '2')
	{
		document.getElementById("iea_01").innerHTML = "사업자명을 입력해 주세요.";
	}
}

function total_tax()
{
	//주문내역의 total_value의 합을 구한 후.. 세금 내용을 나오도록 하면 됨.
	var tot_value = document.getElementsByName("dt_totprice[]");

	var arr_dt_deli = document.getElementsByName("tmp_dt_deli[]");
	var arr_dt_sale = document.getElementsByName("tmp_dt_sale[]");
	var arr_dt_stax = document.getElementsByName("tmp_dt_stax[]");

	var tmp_value = 0;
	var tmp_value1 = 0;
	var tmp_value2 = 0;
	var tmp_value3 = 0;
	var tax_msg = "";

	for(var i = 0; i < tot_value.length; i++ )
	{
		tmp_value += eval(tot_value[i].value);
	}

	//배열로 해서 총 합을 구해서 가지고 감.
	for(var i = 0; i < arr_dt_deli.length; i++ )
	{
		if(arr_dt_deli[i].value)
		{
			tmp_value1 = parseFloat(arr_dt_deli[i].value);

			tmp_value = tmp_value + tmp_value1;
		}
	}

	for(var i = 0; i < arr_dt_sale.length; i++ )
	{
		if(arr_dt_sale[i].value)
		{
			tmp_value2 = parseFloat(arr_dt_sale[i].value);

			tmp_value = tmp_value - tmp_value2;
		}
	}

	for(var i = 0; i < arr_dt_stax.length; i++ )
	{
		if(arr_dt_stax[i].value)
		{
			tmp_value3 = parseFloat(arr_dt_stax[i].value);

			tmp_value = tmp_value + tmp_value3;
		}
	}

	if(document.getElementById("tmp_total") )
	{
		tmp_value = myRound(tmp_value,2);
		document.getElementById("tmp_total").value= tmp_value;
	}


	if(isNaN(tmp_value))
	{
		tax_msg = "정확히 입력해 주세요.";
	}else
	{
		if(tmp_value > 200)
		{
			tax_msg = "고객님! 세금이 나올 듯 합니다 - (참고용)";
		}else
		{
			tax_msg = "고객님! 세금 걱정 없습니다 - (참고용)";
		}
	}

	var tax_show = document.getElementById("tax_word");

	tax_show.innerHTML = tax_msg;
}

function detail_add_item()
{
	var div = document.createElement('div');

	div.innerHTML = "<table width=\"96%\" align=\"center\" border=\"0\" style=\"border-style:solid;border-width:1px;border-color:#cdcdcd;\"><tr height=\"30\"><td width=\"13%\">&nbsp;상품명 <td width=\"87%\" colspan=\"2\"><input type=\"text\" name=\"dt_name[]\" size=\"29\" maxlength=\"100\" style=\"height:22;bold;font-size:12px;color:#4682b4;\"> 가격 <input type=\"text\" name=\"dt_price[]\" size=\"7\" maxlength=\"10\" style=\"height:22;bold;font-size:12px;color:#4682b4;\" onchange=\"dt_cal();\"> 수량 <input type=\"text\"  name=\"dt_num[]\" size=\"3\" value='1' maxlength=\"5\" style=\"height:22;bold;font-size:12px;color:#4682b4;\" onkeyup=\"dt_cal();\"> 총가격 	<input type=\"text\"  name=\"dt_totprice[]\" size=\"7\" maxlength=\"10\" style=\"height:22;bold;font-size:12px;color:#4682b4;\" readonly></tr><tr height=\"30\"><td>&nbsp;오더넘버<td><input type=\"text\" size=\"25\" maxlength=\"50\" name=\"ct_odnum[]\" id=\"ct_odnum\" style=\"height:22;vertical-align: middle;font-weight:bold;font-size:12px;color:#4682b4;\">&nbsp;옵션 <input type=\"text\" name=\"ct_opt[]\" size=\"20\" style=\"height:22;vertical-align: middle;font-size:12px;color:#4682b4;\"><td align=\"right\"><input type=\"button\" value=\"지우기\" onclick=\"item_del(this);\">&nbsp; </tr><tr height=\"30\"><td>&nbsp;상품링크<td colspan=\"2\"><input type=\"text\" name=\"ct_buyurl[]\" size=\"58\" style=\"height:22;vertical-align: middle;font-size:12px;color:#4682b4;\"></tr></table>";

	document.getElementById('item_01').appendChild(div);
}

function detail_add_item2()
{
	var div = document.createElement('div');

	div.innerHTML = "<table width=\"96%\" align=\"center\" border=\"0\" style=\"border-style:solid;border-width:1px;border-color:#cdcdcd;\"><tr height=\"30\"><td width=\"13%\">&nbsp;상품명 <td width=\"87%\" colspan=\"2\"><input type=\"text\" name=\"dt_name[]\" size=\"29\" maxlength=\"100\" style=\"height:22;bold;font-size:12px;color:#4682b4;\"> 가격 <input type=\"text\" name=\"dt_price[]\" size=\"7\" maxlength=\"10\" style=\"height:22;bold;font-size:12px;color:#4682b4;\" onchange=\"dt_cal();\"> 수량 <input type=\"text\"  name=\"dt_num[]\" size=\"3\" value='1' maxlength=\"5\" style=\"height:22;bold;font-size:12px;color:#4682b4;\" onkeyup=\"dt_cal();\"> 총가격 	<input type=\"text\"  name=\"dt_totprice[]\" size=\"7\" maxlength=\"10\" style=\"height:22;bold;font-size:12px;color:#4682b4;\" readonly></tr><tr height=\"30\"><td>&nbsp;오더넘버<td><input type=\"text\" size=\"25\" maxlength=\"50\" name=\"ct_odnum[]\" id=\"ct_odnum\" style=\"height:22;vertical-align: middle;font-weight:bold;font-size:12px;color:#4682b4;\">&nbsp;옵션 <input type=\"text\" name=\"ct_opt[]\" size=\"20\" style=\"height:22;vertical-align: middle;font-size:12px;color:#4682b4;\"><td align=\"right\"><input type=\"button\" value=\"지우기\" onclick=\"item_del(this);\">&nbsp; </tr><tr height=\"30\"><td>&nbsp;상품링크<td colspan=\"2\"><input type=\"text\" name=\"ct_buyurl[]\" size=\"58\" style=\"height:22;vertical-align: middle;font-size:12px;color:#4682b4;\"></tr></table>";

	document.getElementById("item_02").appendChild(div);
}

function detail_add_item3()
{
	var div = document.createElement('div');

	div.innerHTML = "<table width=\"96%\" align=\"center\" border=\"0\" style=\"border-style:solid;border-width:1px;border-color:#cdcdcd;\"><tr height=\"30\"><td width=\"13%\">&nbsp;상품명 <td width=\"87%\" colspan=\"2\"><input type=\"text\" name=\"dt_name[]\" size=\"29\" maxlength=\"100\" style=\"height:22;bold;font-size:12px;color:#4682b4;\"> 가격 <input type=\"text\" name=\"dt_price[]\" size=\"7\" maxlength=\"10\" style=\"height:22;bold;font-size:12px;color:#4682b4;\" onchange=\"dt_cal();\"> 수량 <input type=\"text\"  name=\"dt_num[]\" size=\"3\" value='1' maxlength=\"5\" style=\"height:22;bold;font-size:12px;color:#4682b4;\" onkeyup=\"dt_cal();\"> 총가격 	<input type=\"text\"  name=\"dt_totprice[]\" size=\"7\" maxlength=\"10\" style=\"height:22;bold;font-size:12px;color:#4682b4;\" readonly></tr><tr height=\"30\"><td>&nbsp;오더넘버<td><input type=\"text\" size=\"25\" maxlength=\"50\" name=\"ct_odnum[]\" id=\"ct_odnum\" style=\"height:22;vertical-align: middle;font-weight:bold;font-size:12px;color:#4682b4;\">&nbsp;옵션 <input type=\"text\" name=\"ct_opt[]\" size=\"20\" style=\"height:22;vertical-align: middle;font-size:12px;color:#4682b4;\"><td align=\"right\"><input type=\"button\" value=\"지우기\" onclick=\"item_del(this);\">&nbsp; </tr><tr height=\"30\"><td>&nbsp;상품링크<td colspan=\"2\"><input type=\"text\" name=\"ct_buyurl[]\" size=\"58\" style=\"height:22;vertical-align: middle;font-size:12px;color:#4682b4;\"></tr></table>";

	document.getElementById("item_03").appendChild(div);
}

function detail_add_item4()
{
	var div = document.createElement('div');

	div.innerHTML = "<table width=\"96%\" align=\"center\" border=\"0\" style=\"border-style:solid;border-width:1px;border-color:#cdcdcd;\"><tr height=\"30\"><td width=\"13%\">&nbsp;상품명 <td width=\"87%\" colspan=\"2\"><input type=\"text\" name=\"dt_name[]\" size=\"29\" maxlength=\"100\" style=\"height:22;bold;font-size:12px;color:#4682b4;\"> 가격 <input type=\"text\" name=\"dt_price[]\" size=\"7\" maxlength=\"10\" style=\"height:22;bold;font-size:12px;color:#4682b4;\" onchange=\"dt_cal();\"> 수량 <input type=\"text\"  name=\"dt_num[]\" size=\"3\" value='1' maxlength=\"5\" style=\"height:22;bold;font-size:12px;color:#4682b4;\" onkeyup=\"dt_cal();\"> 총가격 	<input type=\"text\"  name=\"dt_totprice[]\" size=\"7\" maxlength=\"10\" style=\"height:22;bold;font-size:12px;color:#4682b4;\" readonly></tr><tr height=\"30\"><td>&nbsp;오더넘버<td><input type=\"text\" size=\"25\" maxlength=\"50\" name=\"ct_odnum[]\" id=\"ct_odnum\" style=\"height:22;vertical-align: middle;font-weight:bold;font-size:12px;color:#4682b4;\">&nbsp;옵션 <input type=\"text\" name=\"ct_opt[]\" size=\"20\" style=\"height:22;vertical-align: middle;font-size:12px;color:#4682b4;\"><td align=\"right\"><input type=\"button\" value=\"지우기\" onclick=\"item_del(this);\">&nbsp; </tr><tr height=\"30\"><td>&nbsp;상품링크<td colspan=\"2\"><input type=\"text\" name=\"ct_buyurl[]\" size=\"58\" style=\"height:22;vertical-align: middle;font-size:12px;color:#4682b4;\"></tr></table>";

	document.getElementById("item_04").appendChild(div);
}

function detail_add_site(val)
{
	alert(val);
	if(val == 'two')
	{
		var add_table = document.getElementById("site_02");

		if(!add_table.innerHTML)
		{
			//없을 때만 들어오세요!!

			add_table.innerHTML += "<table width=\"99%\" align=\"center\"><tr height=\"30\"><td>&nbsp; [2]사이트 <select name = \"bs_site[]\"><option value=\"\">-선택-</option><option value=\"1\">amazon.com</option><option value=\"2\">ebay.com</option><option value=\"3\">ralphlauren.com</option><option value=\"4\">gap.com</option><option value=\"5\">drugstore.com</option><option value=\"6\">6pm.com</option><option value=\"14\">yankeecandle.com</option><option value=\"15\">hockeymonkey.com</option><option value=\"16\">diapers.com</option><option value=\"17\">hm.com</option><option value=\"18\">carters.com</option><option value=\"19\">jcrew.com</option><option value=\"20\">coachfactory.com</option></select> 또는&nbsp;직접입력&nbsp; <input type=\"text\" size=\"25\" name=\"dir_bs_site[]\" maxlength=\"100\" name=\"ct_site\" id=\"ct_site\" style=\"height:22;vertical-align: middle;font-weight:bold;font-size:12px;color:#4682b4;\" ><td align=\"right\" style=\"padding-right:10px;\"><input type=\"button\" value=\"사이트- \" onclick=\"site_del(this);\"></tr><tr><td colspan=\"2\"><table width=\"96%\" align=\"center\" style=\"border-style:solid;border-width:1px;border-color:#cdcdcd;\"><tr height=\"30\"><td width=\"13%\">&nbsp;상품명 <td width=\"87%\" colspan=\"2\"><input type=\"text\" name=\"dt_name[]\" size=\"29\" maxlength=\"100\" style=\"height:22;bold;font-size:12px;color:#4682b4;\"> 가격 <input type=\"text\" name=\"dt_price[]\" size=\"7\" maxlength=\"10\" style=\"height:22;bold;font-size:12px;color:#4682b4;\" onchange=\"dt_cal();\";> 수량 <input type=\"text\"  name=\"dt_num[]\" size=\"3\" value=\"1\" maxlength=\"5\" style=\"height:22;bold;font-size:12px;color:#4682b4;\" onkeyup=\"dt_cal();\"> 총가격 <input type=\"text\"  name=\"dt_totprice[]\" size=\"7\" maxlength=\"10\" style=\"height:22;bold;font-size:12px;color:#4682b4;\" readonly></tr><tr height=\"30\"><td>&nbsp;오더넘버<td><input type=\"text\" size=\"25\" maxlength=\"50\" name=\"ct_odnum[]\" id=\"ct_odnum\" style=\"height:22;vertical-align: middle;font-weight:bold;font-size:12px;color:#4682b4;\">&nbsp;옵션 <input type=\"text\" name=\"ct_opt[]\" size=\"20\" style=\"height:22;vertical-align: middle;font-size:12px;color:#4682b4;\"><td align=\"right\"><input type=\"button\" value=\"상품+ \" onclick=\"detail_add_item2();\" style=\"color:#232323;background-color:orange;font-weight:bold;\">&nbsp; </tr><tr height=\"30\"><td>&nbsp;상품링크<td colspan=\"2\"><input type=\"text\" name=\"ct_buyurl[]\" size=\"58\" style=\"height:22;vertical-align: middle;font-size:12px;color:#4682b4;\"></tr></table><span id=\"item_02\"></span></tr><tr height=\"30\"><td colspan=\"2\">&nbsp; [2] 배송료 <input type=\"text\" name=\"tmp_dt_deli[]\" size=\"10\"> &nbsp; 할인금액 <input type=\"text\" name=\"tmp_dt_sale[]\" size=\"10\"> &nbsp; 세일즈텍스 <input type=\"text\" name=\"tmp_dt_stax[]\" size=\"10\"></tr><tr><td colspan=\"2\" height=\"10\"></tr></table>";
		}

	}else if(val == 'three')
	{
		var add_table = document.getElementById("site_03");

		if(!add_table.innerHTML)
		{
			//없을때만 들어온다.
			add_table.innerHTML += "<table width=\"99%\" align=\"center\"><tr height=\"30\"><td>&nbsp; [3]사이트 <select name = \"bs_site[]\"><option value=\"\">-선택-</option><option value=\"1\">amazon.com</option><option value=\"2\">ebay.com</option><option value=\"3\">ralphlauren.com</option><option value=\"4\">gap.com</option><option value=\"5\">drugstore.com</option><option value=\"6\">6pm.com</option><option value=\"14\">yankeecandle.com</option><option value=\"15\">hockeymonkey.com</option><option value=\"16\">diapers.com</option><option value=\"17\">hm.com</option><option value=\"18\">carters.com</option><option value=\"19\">jcrew.com</option><option value=\"20\">coachfactory.com</option></select> 또는&nbsp;직접입력&nbsp; <input type=\"text\" size=\"25\" name=\"dir_bs_site[]\" maxlength=\"100\" name=\"ct_site\" id=\"ct_site\" style=\"height:22;vertical-align: middle;font-weight:bold;font-size:12px;color:#4682b4;\" ><td align=\"right\" style=\"padding-right:10px;\"><input type=\"button\" value=\"사이트- \" onclick=\"site_del(this);\"></tr><tr><td colspan=\"2\"><table width=\"96%\" align=\"center\" style=\"border-style:solid;border-width:1px;border-color:#cdcdcd;\"><tr height=\"30\"><td width=\"13%\">&nbsp;상품명 <td width=\"87%\" colspan=\"2\"><input type=\"text\" name=\"dt_name[]\" size=\"29\" maxlength=\"100\" style=\"height:22;bold;font-size:12px;color:#4682b4;\"> 가격 <input type=\"text\" name=\"dt_price[]\" size=\"7\" maxlength=\"10\" style=\"height:22;bold;font-size:12px;color:#4682b4;\" onchange=\"dt_cal();\";> 수량 <input type=\"text\"  name=\"dt_num[]\" size=\"3\" value=\"1\" maxlength=\"5\" style=\"height:22;bold;font-size:12px;color:#4682b4;\" onkeyup=\"dt_cal();\"> 총가격 <input type=\"text\"  name=\"dt_totprice[]\" size=\"7\" maxlength=\"10\" style=\"height:22;bold;font-size:12px;color:#4682b4;\" readonly></tr><tr height=\"30\"><td>&nbsp;오더넘버<td><input type=\"text\" size=\"25\" maxlength=\"50\" name=\"ct_odnum[]\" id=\"ct_odnum\" style=\"height:22;vertical-align: middle;font-weight:bold;font-size:12px;color:#4682b4;\">&nbsp;옵션 <input type=\"text\" name=\"ct_opt[]\" size=\"20\" style=\"height:22;vertical-align: middle;font-size:12px;color:#4682b4;\"><td align=\"right\"><input type=\"button\" value=\"상품+ \" onclick=\"detail_add_item3();\" style=\"color:#232323;background-color:orange;font-weight:bold;\">&nbsp; </tr><tr height=\"30\"><td>&nbsp;상품링크<td colspan=\"2\"><input type=\"text\" name=\"ct_buyurl[]\" size=\"58\" style=\"height:22;vertical-align: middle;font-size:12px;color:#4682b4;\"></tr></table><span id=\"item_03\"></span></tr><tr height=\"30\"><td colspan=\"2\">&nbsp; [3] 배송료 <input type=\"text\" name=\"tmp_dt_deli[]\" size=\"10\"> &nbsp; 할인금액 <input type=\"text\" name=\"tmp_dt_sale[]\" size=\"10\"> &nbsp; 세일즈텍스 <input type=\"text\" name=\"tmp_dt_stax[]\" size=\"10\"></tr><tr><td colspan=\"2\" height=\"10\"></tr></table>";
		}

	}else if(val == 'four')
	{
		var add_table = document.getElementById("site_04");

		if(!add_table.innerHTML)
		{
			//없을때만 들어온다.
			add_table.innerHTML += "<table width=\"99%\" align=\"center\"><tr height=\"30\"><td>&nbsp; [4]사이트 <select name = \"bs_site[]\"><option value=\"\">-선택-</option><option value=\"1\">amazon.com</option><option value=\"2\">ebay.com</option><option value=\"3\">ralphlauren.com</option><option value=\"4\">gap.com</option><option value=\"5\">drugstore.com</option><option value=\"6\">6pm.com</option><option value=\"14\">yankeecandle.com</option><option value=\"15\">hockeymonkey.com</option><option value=\"16\">diapers.com</option><option value=\"17\">hm.com</option><option value=\"18\">carters.com</option><option value=\"19\">jcrew.com</option><option value=\"20\">coachfactory.com</option></select> 또는&nbsp;직접입력&nbsp; <input type=\"text\" size=\"25\" name=\"dir_bs_site[]\" maxlength=\"100\" name=\"ct_site\" id=\"ct_site\" style=\"height:22;vertical-align: middle;font-weight:bold;font-size:12px;color:#4682b4;\" ><td align=\"right\" style=\"padding-right:10px;\"><input type=\"button\" value=\"사이트- \" onclick=\"site_del(this);\"></tr><tr><td colspan=\"2\"><table width=\"96%\" align=\"center\" style=\"border-style:solid;border-width:1px;border-color:#cdcdcd;\"><tr height=\"30\"><td width=\"13%\">&nbsp;상품명 <td width=\"87%\" colspan=\"2\"><input type=\"text\" name=\"dt_name[]\" size=\"29\" maxlength=\"100\" style=\"height:22;bold;font-size:12px;color:#4682b4;\"> 가격 <input type=\"text\" name=\"dt_price[]\" size=\"7\" maxlength=\"10\" style=\"height:22;bold;font-size:12px;color:#4682b4;\" onchange=\"dt_cal();\";> 수량 <input type=\"text\"  name=\"dt_num[]\" size=\"3\" value=\"1\" maxlength=\"5\" style=\"height:22;bold;font-size:12px;color:#4682b4;\" onkeyup=\"dt_cal();\"> 총가격 <input type=\"text\"  name=\"dt_totprice[]\" size=\"7\" maxlength=\"10\" style=\"height:22;bold;font-size:12px;color:#4682b4;\" readonly></tr><tr height=\"30\"><td>&nbsp;오더넘버<td><input type=\"text\" size=\"25\" maxlength=\"50\" name=\"ct_odnum[]\" id=\"ct_odnum\" style=\"height:22;vertical-align: middle;font-weight:bold;font-size:12px;color:#4682b4;\">&nbsp;옵션 <input type=\"text\" name=\"ct_opt[]\" size=\"20\" style=\"height:22;vertical-align: middle;font-size:12px;color:#4682b4;\"><td align=\"right\"><input type=\"button\" value=\"상품+ \" onclick=\"detail_add_item4();\" style=\"color:#232323;background-color:orange;font-weight:bold;\">&nbsp; </tr><tr height=\"30\"><td>&nbsp;상품링크<td colspan=\"2\"><input type=\"text\" name=\"ct_buyurl[]\" size=\"58\" style=\"height:22;vertical-align: middle;font-size:12px;color:#4682b4;\"></tr></table><span id=\"item_04\"></span></tr><tr height=\"30\"><td colspan=\"2\">&nbsp; [4] 배송료 <input type=\"text\" name=\"tmp_dt_deli[]\" size=\"10\"> &nbsp; 할인금액 <input type=\"text\" name=\"tmp_dt_sale[]\" size=\"10\"> &nbsp; 세일즈텍스 <input type=\"text\" name=\"tmp_dt_stax[]\" size=\"10\"></tr><tr><td colspan=\"2\" height=\"10\"></tr></table>";
		}
	}
}

function myRound(val, pos)
{
	var posV = Math.pow(10, (pos ? pos : 2));
	return Math.round(val*posV)/posV ;
}

function dt_cal()
{
	var arr_dt_price			= document.getElementsByName('dt_price[]');
	var arr_dt_qty				= document.getElementsByName('dt_num[]');
	var arr_dt_totprice		= document.getElementsByName('dt_totprice[]');

	for(var i = 0; i < arr_dt_price.length; i++)
	{
		if(arr_dt_price[i].value != '' )
		{
			if(isNaN(arr_dt_price[i].value) )
			{
				alert("숫자만 입력해 주세요.");
				arr_dt_price[i].value = '';
				return;
			}

			var tmp_totprice = arr_dt_price[i].value * arr_dt_qty[i].value;
			tmp_totprice = myRound(tmp_totprice);

			arr_dt_totprice[i].value = tmp_totprice;
		}
	}
}

function dt_Formdel(value)	//이전..
{
	if(confirm("상품을 삭제합니까?"))	
	{
		value.parentNode.parentNode.parentNode.parentNode.removeChild(value.parentNode.parentNode.parentNode);
	}
}

function item_del(value)
{
	if(confirm("상품을 삭제합니까?"))
	{
		value.parentNode.parentNode.parentNode.parentNode.parentNode.parentNode.removeChild(value.parentNode.parentNode.parentNode.parentNode.parentNode);
	}
}

function site_del(value)
{
	if(confirm("사이트 덩어리를 삭제합니까?"))
	{
		value.parentNode.parentNode.parentNode.parentNode.parentNode.removeChild(value.parentNode.parentNode.parentNode.parentNode);
	}
}

</script>


<script>
$(function() {
    var $cp_btn_el;
    var $cp_row_el;
    var zipcode = "";

    $(".cp_btn").click(function() {
        $cp_btn_el = $(this);
        $cp_row_el = $(this).closest("tr");
        $("#cp_frm").remove();
        var it_id = $cp_btn_el.closest("tr").find("input[name^=it_id]").val();

        $.post(
            "./orderitemcoupon.php",
            { it_id: it_id,  sw_direct: "<?php echo $sw_direct; ?>" },
            function(data) {
                $cp_btn_el.after(data);
            }
        );
    });

    $(".cp_apply").live("click", function() {
        var $el = $(this).closest("tr");
        var cp_id = $el.find("input[name='f_cp_id[]']").val();
        var price = $el.find("input[name='f_cp_prc[]']").val();
        var subj = $el.find("input[name='f_cp_subj[]']").val();
        var sell_price;

        if(parseInt(price) == 0) {
            if(!confirm(subj+"쿠폰의 할인 금액은 "+price+"원입니다.\n쿠폰을 적용하시겠습니까?")) {
                return false;
            }
        }

        // 이미 사용한 쿠폰이 있는지
        var cp_dup = false;
        var cp_dup_idx;
        var $cp_dup_el;
        $("input[name^=cp_id]").each(function(index) {
            var id = $(this).val();

            if(id == cp_id) {
                cp_dup_idx = index;
                cp_dup = true;
                $cp_dup_el = $(this).closest("tr");;

                return false;
            }
        });

        if(cp_dup) {
            var it_name = $("input[name='it_name["+cp_dup_idx+"]']").val();
            if(!confirm(subj+ "쿠폰은 "+it_name+"에 사용되었습니다.\n"+it_name+"의 쿠폰을 취소한 후 적용하시겠습니까?")) {
                return false;
            } else {
                coupon_cancel($cp_dup_el);
                $("#cp_frm").remove();
                $cp_dup_el.find(".cp_btn").text("적용").focus();
                $cp_dup_el.find(".cp_cancel").remove();
            }
        }

        var $s_el = $cp_row_el.find(".total_price");;
        sell_price = parseInt($cp_row_el.find("input[name^=it_price]").val());
        sell_price = sell_price - parseInt(price);
        if(sell_price < 0) {
            alert("쿠폰할인금액이 상품 주문금액보다 크므로 쿠폰을 적용할 수 없습니다.");
            return false;
        }
        $s_el.text(number_format(String(sell_price)));
        $cp_row_el.find("input[name^=cp_id]").val(cp_id);
        $cp_row_el.find("input[name^=cp_price]").val(price);

        calculate_total_price();
        $("#cp_frm").remove();
        $cp_btn_el.text("변경").focus();
        if(!$cp_row_el.find(".cp_cancel").size())
            $cp_btn_el.after("<button type=\"button\" class=\"cp_cancel btn_frmline\">취소</button>");
    });

    $("#cp_close").live("click", function() {
        $("#cp_frm").remove();
        $cp_btn_el.focus();
    });

    $(".cp_cancel").live("click", function() {
        coupon_cancel($(this).closest("tr"));
        calculate_total_price();
        $("#cp_frm").remove();
        $(this).closest("tr").find(".cp_btn").text("적용").focus();
        $(this).remove();
    });

    $("#od_coupon_btn").click(function() {
        $("#od_coupon_frm").remove();
        var $this = $(this);
        var price = parseInt($("input[name=org_od_price]").val()) - parseInt($("input[name=item_coupon]").val());
        if(price <= 0) {
            alert('상품금액이 0원이므로 쿠폰을 사용할 수 없습니다.');
            return false;
        }
        $.post(
            "./ordercoupon.php",
            { price: price },
            function(data) {
                $this.after(data);
            }
        );
    });

    $(".od_cp_apply").live("click", function() {
        var $el = $(this).closest("tr");
        var cp_id = $el.find("input[name='o_cp_id[]']").val();
        var price = parseInt($el.find("input[name='o_cp_prc[]']").val());
        var subj = $el.find("input[name='o_cp_subj[]']").val();
        var send_cost = $("input[name=od_send_cost]").val();
        var item_coupon = parseInt($("input[name=item_coupon]").val());
        var od_price = parseInt($("input[name=org_od_price]").val()) - item_coupon;

        if(price == 0) {
            if(!confirm(subj+"쿠폰의 할인 금액은 "+price+"원입니다.\n쿠폰을 적용하시겠습니까?")) {
                return false;
            }
        }

        if(od_price - price <= 0) {
            alert("쿠폰할인금액이 주문금액보다 크므로 쿠폰을 적용할 수 없습니다.");
            return false;
        }

        $("input[name=sc_cp_id]").val("");
        $("#sc_coupon_btn").text("쿠폰적용");
        $("#sc_coupon_cancel").remove();

        $("input[name=od_price]").val(od_price - price);
        $("input[name=od_cp_id]").val(cp_id);
        $("input[name=od_coupon]").val(price);
        $("input[name=od_send_coupon]").val(0);
        $("#od_cp_price").text(number_format(String(price)));
        $("#sc_cp_price").text(0);
        calculate_order_price();
        $("#od_coupon_frm").remove();
        $("#od_coupon_btn").text("쿠폰변경").focus();
        if(!$("#od_coupon_cancel").size())
            $("#od_coupon_btn").after("<button type=\"button\" id=\"od_coupon_cancel\" class=\"btn_frmline\">쿠폰취소</button>");
    });

    $("#od_coupon_close").live("click", function() {
        $("#od_coupon_frm").remove();
        $("#od_coupon_btn").focus();
    });

    $("#od_coupon_cancel").live("click", function() {
        var org_price = $("input[name=org_od_price]").val();
        var item_coupon = parseInt($("input[name=item_coupon]").val());
        $("input[name=od_price]").val(org_price - item_coupon);
        $("input[name=sc_cp_id]").val("");
        $("input[name=od_coupon]").val(0);
        $("input[name=od_send_coupon]").val(0);
        $("#od_cp_price").text(0);
        $("#sc_cp_price").text(0);
        calculate_order_price();
        $("#od_coupon_frm").remove();
        $("#od_coupon_btn").text("쿠폰적용").focus();
        $(this).remove();
        $("#sc_coupon_btn").text("쿠폰적용");
        $("#sc_coupon_cancel").remove();
    });

    $("#sc_coupon_btn").click(function() {
        $("#sc_coupon_frm").remove();
        var $this = $(this);
        var price = parseInt($("input[name=od_price]").val());
        var send_cost = parseInt($("input[name=od_send_cost]").val());
        $.post(
            "./ordersendcostcoupon.php",
            { price: price, send_cost: send_cost },
            function(data) {
                $this.after(data);
            }
        );
    });

    $(".sc_cp_apply").live("click", function() {
        var $el = $(this).closest("tr");
        var cp_id = $el.find("input[name='s_cp_id[]']").val();
        var price = parseInt($el.find("input[name='s_cp_prc[]']").val());
        var subj = $el.find("input[name='s_cp_subj[]']").val();
        var send_cost = parseInt($("input[name=od_send_cost]").val());

        if(parseInt(price) == 0) {
            if(!confirm(subj+"쿠폰의 할인 금액은 "+price+"원입니다.\n쿠폰을 적용하시겠습니까?")) {
                return false;
            }
        }

        $("input[name=sc_cp_id]").val(cp_id);
        $("input[name=od_send_coupon]").val(price);
        $("#sc_cp_price").text(number_format(String(price)));
        calculate_order_price();
        $("#sc_coupon_frm").remove();
        $("#sc_coupon_btn").text("쿠폰변경").focus();
        if(!$("#sc_coupon_cancel").size())
            $("#sc_coupon_btn").after("<button type=\"button\" id=\"sc_coupon_cancel\" class=\"btn_frmline\">쿠폰취소</button>");
    });

    $("#sc_coupon_close").live("click", function() {
        $("#sc_coupon_frm").remove();
        $("#sc_coupon_btn").focus();
    });

    $("#sc_coupon_cancel").live("click", function() {
        $("input[name=od_send_coupon]").val(0);
        $("#sc_cp_price").text(0);
        calculate_order_price();
        $("#sc_coupon_frm").remove();
        $("#sc_coupon_btn").text("쿠폰적용").focus();
        $(this).remove();
    });

    $("#od_b_addr2").focus(function() {
        var zip1 = $("#od_b_zip1").val().replace(/[^0-9]/g, "");
        var zip2 = $("#od_b_zip2").val().replace(/[^0-9]/g, "");
        if(zip1 == "" || zip2 == "")
            return false;

        var code = String(zip1) + String(zip2);

        if(zipcode == code)
            return false;

        zipcode = code;
        calculate_sendcost(code);
    });

    $("#od_settle_bank").on("click", function() {
        $("[name=od_deposit_name]").val( $("[name=od_name]").val() );
        $("#settle_bank").show();
    });

    $("#od_settle_iche,#od_settle_card,#od_settle_vbank,#od_settle_hp").bind("click", function() {
        $("#settle_bank").hide();
    });

    // 배송지선택
    $("input[name=ad_sel_addr]").on("click", function() {
        var addr = $(this).val().split(String.fromCharCode(30));

        if (addr[0] == "same") {
            if($(this).is(":checked"))
                gumae2baesong(true);
            else
                gumae2baesong(false);
        } else {
            if(addr[0] == "new") {
                for(i=0; i<10; i++) {
                    addr[i] = "";
                }
            }

            var f = document.forderform;
            f.od_b_name.value        = addr[0];
            f.od_b_tel.value         = addr[1];
            f.od_b_hp.value          = addr[2];
            f.od_b_zip1.value        = addr[3];
            f.od_b_zip2.value        = addr[4];
            f.od_b_addr1.value       = addr[5];
            f.od_b_addr2.value       = addr[6];
            f.od_b_addr3.value       = addr[7];
            f.od_b_addr_jibeon.value = addr[8];
            f.ad_subject.value       = addr[9];

            document.getElementById("od_b_addr_jibeon").innerText = "지번주소 : "+addr[8];

            var zip1 = addr[3].replace(/[^0-9]/g, "");
            var zip2 = addr[4].replace(/[^0-9]/g, "");

            if(zip1 != "" && zip2 != "") {
                var code = String(zip1) + String(zip2);

                if(zipcode != code) {
                    zipcode = code;
                    calculate_sendcost(code);
                }
            }
        }
    });

    // 배송지목록
    $("#order_address").on("click", function() {
        var url = this.href;
        window.open(url, "win_address", "left=100,top=100,width=800,height=600,scrollbars=1");
        return false;
    });
});


function calculate_sendcost(code)
{
    $.post(
        "./ordersendcost.php",
        { zipcode: code },
        function(data) {
            $("input[name=od_send_cost2]").val(data);
            $("#od_send_cost2").text(number_format(String(data)));

            calculate_order_price();
        }
    );
}

function forderform_check(f)
{
    errmsg = "";
    errfld = "";
    var deffld = "";

    check_field(f.od_name, "주문하시는 분 이름을 입력하십시오.");
    if (typeof(f.od_pwd) != 'undefined')
    {
        clear_field(f.od_pwd);
        if( (f.od_pwd.value.length<3) || (f.od_pwd.value.search(/([^A-Za-z0-9]+)/)!=-1) )
            error_field(f.od_pwd, "회원이 아니신 경우 주문서 조회시 필요한 비밀번호를 3자리 이상 입력해 주십시오.");
    }
    check_field(f.od_tel, "주문하시는 분 전화번호를 입력하십시오.");
    check_field(f.od_addr1, "우편번호 찾기를 이용하여 주문하시는 분 주소를 입력하십시오.");
    //check_field(f.od_addr2, " 주문하시는 분의 상세주소를 입력하십시오.");
    check_field(f.od_zip1, "");
    check_field(f.od_zip2, "");

    clear_field(f.od_email);
    if(f.od_email.value=='' || f.od_email.value.search(/(\S+)@(\S+)\.(\S+)/) == -1)
        error_field(f.od_email, "E-mail을 바르게 입력해 주십시오.");

    if (typeof(f.od_hope_date) != "undefined")
    {
        clear_field(f.od_hope_date);
        if (!f.od_hope_date.value)
            error_field(f.od_hope_date, "희망배송일을 선택하여 주십시오.");
    }

    check_field(f.od_b_name, "받으시는 분 이름을 입력하십시오.");
    check_field(f.od_b_tel, "받으시는 분 전화번호를 입력하십시오.");
    check_field(f.od_b_addr1, "우편번호 찾기를 이용하여 받으시는 분 주소를 입력하십시오.");
    //check_field(f.od_b_addr2, "받으시는 분의 상세주소를 입력하십시오.");
    check_field(f.od_b_zip1, "");
    check_field(f.od_b_zip2, "");

    var od_settle_bank = document.getElementById("od_settle_bank");
    if (od_settle_bank) {
        if (od_settle_bank.checked) {
            check_field(f.od_bank_account, "계좌번호를 선택하세요.");
            check_field(f.od_deposit_name, "입금자명을 입력하세요.");
        }
    }

    // 배송비를 받지 않거나 더 받는 경우 아래식에 + 또는 - 로 대입
    f.od_send_cost.value = parseInt(f.od_send_cost.value);

    if (errmsg)
    {
        alert(errmsg);
        errfld.focus();
        return false;
    }

    var settle_case = document.getElementsByName("od_settle_case");
    var settle_check = false;
    var settle_method = "";
    for (i=0; i<settle_case.length; i++)
    {
        if (settle_case[i].checked)
        {
            settle_check = true;
            settle_method = settle_case[i].value;
            break;
        }
    }
    if (!settle_check)
    {
        alert("결제방식을 선택하십시오.");
        return false;
    }

    var od_price = parseInt(f.od_price.value);
    var send_cost = parseInt(f.od_send_cost.value);
    var send_cost2 = parseInt(f.od_send_cost2.value);
    var send_coupon = parseInt(f.od_send_coupon.value);

    var max_point = 0;
    if (typeof(f.max_temp_point) != "undefined")
        max_point  = parseInt(f.max_temp_point.value);

    var temp_point = 0;
    if (typeof(f.od_temp_point) != "undefined") {
        if (f.od_temp_point.value)
        {
            var point_unit = parseInt(<?php echo $default['de_settle_point_unit']; ?>);
            temp_point = parseInt(f.od_temp_point.value);

            if (temp_point < 0) {
                alert("포인트를 0 이상 입력하세요.");
                f.od_temp_point.select();
                return false;
            }

            if (temp_point > od_price) {
                alert("상품 주문금액(배송비 제외) 보다 많이 포인트결제할 수 없습니다.");
                f.od_temp_point.select();
                return false;
            }

            if (temp_point > <?php echo (int)$member['mb_point']; ?>) {
                alert("회원님의 포인트보다 많이 결제할 수 없습니다.");
                f.od_temp_point.select();
                return false;
            }

            if (temp_point > max_point) {
                alert(max_point + "점 이상 결제할 수 없습니다.");
                f.od_temp_point.select();
                return false;
            }

            if (parseInt(parseInt(temp_point / point_unit) * point_unit) != temp_point) {
                alert("포인트를 "+String(point_unit)+"점 단위로 입력하세요.");
                f.od_temp_point.select();
                return false;
            }

            // pg 결제 금액에서 포인트 금액 차감
            if(settle_method != "무통장") {
                f.good_mny.value = od_price + send_cost + send_cost2 - send_coupon - temp_point;
            }
        }
    }

    var tot_price = od_price + send_cost + send_cost2 - send_coupon - temp_point;

    if (document.getElementById("od_settle_iche")) {
        if (document.getElementById("od_settle_iche").checked) {
            if (tot_price - temp_point < 150) {
                alert("계좌이체는 150원 이상 결제가 가능합니다.");
                return false;
            }
        }
    }

    if (document.getElementById("od_settle_card")) {
        if (document.getElementById("od_settle_card").checked) {
            if (tot_price - temp_point < 1000) {
                alert("신용카드는 1000원 이상 결제가 가능합니다.");
                return false;
            }
        }
    }

    if (document.getElementById("od_settle_hp")) {
        if (document.getElementById("od_settle_hp").checked) {
            if (tot_price - temp_point < 350) {
                alert("휴대폰은 350원 이상 결제가 가능합니다.");
                return false;
            }
        }
    }

    <?php if($default['de_tax_flag_use']) { ?>
    calculate_tax();
    <?php } ?>

    // pay_method 설정
    switch(settle_method)
    {
        case "계좌이체":
            f.pay_method.value = "010000000000";
            break;
        case "가상계좌":
            f.pay_method.value = "001000000000";
            break;
        case "휴대폰":
            f.pay_method.value = "000010000000";
            break;
        case "신용카드":
            f.pay_method.value = "100000000000";
            break;
        default:
            f.pay_method.value = "무통장";
            break;
    }

    // kcp 결제정보설정
    f.buyr_name.value = f.od_name.value;
    f.buyr_mail.value = f.od_email.value;
    f.buyr_tel1.value = f.od_tel.value;
    f.buyr_tel2.value = f.od_hp.value;
    f.rcvr_name.value = f.od_b_name.value;
    f.rcvr_tel1.value = f.od_b_tel.value;
    f.rcvr_tel2.value = f.od_b_hp.value;
    f.rcvr_mail.value = f.od_email.value;
    f.rcvr_zipx.value = f.od_b_zip1.value + f.od_b_zip2.value;
    f.rcvr_add1.value = f.od_b_addr1.value;
    f.rcvr_add2.value = f.od_b_addr2.value;

    if(f.pay_method.value != "무통장") {
        if(jsf__pay( f )) {
            return true;
        } else {
            return false;
        }
    } else {
        return true;
    }
}

// 구매자 정보와 동일합니다.
function gumae2baesong(checked) {
    var f = document.forderform;

    if(checked == true) {
        f.od_b_name.value = f.od_name.value;
        f.od_b_hp.value   = f.od_hp.value;
        f.od_b_zip1.value = f.od_zip1.value;
        f.od_b_zip2.value = f.od_zip2.value;
        f.od_b_addr1.value = f.od_addr1.value;
        f.od_b_addr2.value = f.od_addr2.value;
        f.od_b_addr3.value = f.od_addr3.value;
        f.od_b_addr_jibeon.value = f.od_addr_jibeon.value;
        document.getElementById("od_b_addr_jibeon").innerText = document.getElementById("od_addr_jibeon").innerText;

        calculate_sendcost(String(f.od_b_zip1.value) + String(f.od_b_zip2.value));
    } else {
        f.od_b_name.value = "";
        f.od_b_hp.value   = "";
        f.od_b_zip1.value = "";
        f.od_b_zip2.value = "";
        f.od_b_addr1.value = "";
        f.od_b_addr2.value = "";
        f.od_b_addr3.value = "";
        f.od_b_addr_jibeon.value = "";
        document.getElementById("od_b_addr_jibeon").innerText = "";
    }
}

</script>

<?php
include_once(G5_PATH.'/tail.php');
?>