<?php
include_once('./_common.php');
include_once(G5_PATH.'/head.php');

if($w==""){
	$spl_count = 1;
}

?>
<form name="forderform" method="post" action="purchase_request_update.php" onsubmit="return forderform_check(this);">
<input type="hidden" name="od_b_tel" id="od_b_tel">
<input type="hidden" name="ad_subject" id="ad_subject">
<input type="hidden" name="od_id" id="od_id" value="<?php echo $od_id?>">
<section id="sod_frm_besong">
<div class="tbl_frm02 tbl_wrap">
<table>
	<tbody>
        <tr>
            <th scope="row">서비스</th>
            <td>구매대행</td>
        </tr>

		
		<tr>
            <th scope="row"><label for="od_site">구매 사이트</label></th>
            <td><input type="text" name="od_site" id="od_site" value="" required class="required frm_input">&nbsp;
				<select id="tmp_pc_site" onchange="$('#od_site').val(this.value);">
					<option value="">직접입력</option>
					<?php
					$i=0;
					foreach($purchaseShoppingSiteList as $vars){?>
					<option value="<?php echo $vars?>"<?if($i==0)echo "checked";?>><?php echo $vars?></option>
					<?php
						$i++;	
					}?>
				</select> (한글 입력시 지연됨.) 
			</td>
        </tr>

		<tr>
            <th scope="row">주문 내역</th>
            <td>
				<div id="sit_request_frm" class="sit_option tbl_frm03">
					<div id="sit_option_addfrm_btn"><button type="button" id="add_request_row" class="btn_frmline">상품추가</button></div>
                    <table>
					<tbody>
                    <?php
                    $i = 0;
					
                    do {
                        $seq = $i + 1;
                    ?>	
					<tr>
					<td>
						<table>
							<colgroup>
								<col class="grid_2">
								<col>
							</colgroup>
							<tbody>

							<tr>
								<th scope="row"><label for="pc_item_<?php echo $seq; ?>">상품명</label></th>
								<td><input type="text" name="pc_item[]" id="pc_item_<?php echo $seq; ?>" value="<?php echo $spl_subject[$i]; ?>" class="frm_input" size="75"></td>
							</tr>

							 <tr>
								<th scope="row"><label for="pc_item_url_<?php echo $seq; ?>">상품주소 URL</label></th>
								<td><input type="text" name="pc_item_url[]" id="pc_item_url_<?php echo $seq; ?>" value="<?php echo $spl_subject[$i]; ?>" class="frm_input" size="75"></td>
							</tr>

							<tr>
								<th scope="row"><label for="pc_item_option_<?php echo $seq; ?>">상품옵션</label></th>
								<td><input type="text" name="pc_item_option[]" id="pc_item_option_<?php echo $seq; ?>" value="<?php echo $spl_subject[$i]; ?>" class="frm_input" size="75"></td>
							</tr>

						
							 <tr>
								<th scope="row"><label for="pc_type_<?php echo $seq; ?>">구입방식</label></th>
								<td><?php echo getSelectArrayList($purchaseShoppingType,"pc_type[]","pc_type_".$seq,$selected," onchange=\"pc_type_chgmsg('".$seq."',this.value);\"");?></td>
							</tr>


							 <tr>
								<th scope="row"><label for="pc_price_<?php echo $seq; ?>">상품가격</label></th>
								<td>USD <input type="text" name="pc_price[]" id="pc_price_<?php echo $seq; ?>" value="<?php echo $spl_subject[$i]; ?>" class="frm_input" size="10"><span id="pc_price_msg_<?php echo $seq; ?>"></span></td>
							</tr>

							<tr>
								<th scope="row"><label for="pc_qty_<?php echo $seq; ?>">상품수량</label></th>
								<td><input type="text" name="pc_qty[]" id="pc_qty_<?php echo $seq; ?>" value="1" class="frm_input" size="3"> 개</td>
							</tr>

							</tbody>
						</table>
					</td>
					</tr>

                    <?php
                        $i++;
                    } while($i < $spl_count);
                    ?>
                    </tbody>
                    </table>
                    
                </div>
			</td>
        </tr>

		<tr>
            <th scope="row"><label for="od_exception">상품이 품절 및<br />가격변동시</label></th>
            <td>
			<?php
			$i=0;
			foreach($purchaseExceptionList as $key=>$vars){?>
			<input type="radio" name="od_exception" id="od_exception_<?php echo $key?>" value="<?php echo $key?>" <?php if($i==0)echo "checked";?>> <?php echo $vars?>&nbsp;
			<?php
				$i++;	
			}?>
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



<div class="btn_confirm">
	<input type="submit" value="구매대행 신청하기" class="btn_submit">
</div>

</FORM>


<script>
$(function() {
    var $cp_btn_el;
    var $cp_row_el;
    var zipcode = "";


    $("#od_b_addr2").focus(function() {
        var zip1 = $("#od_b_zip1").val().replace(/[^0-9]/g, "");
        var zip2 = $("#od_b_zip2").val().replace(/[^0-9]/g, "");
        if(zip1 == "" || zip2 == "")
            return false;

        var code = String(zip1) + String(zip2);

        if(zipcode == code)
            return false;

        zipcode = code;
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


	// 상품추가
	$("#add_request_row").click(function() {

		var $elLatest = $("#sit_request_frm > table > tbody > tr");
		var lastSize = $elLatest.size();

		var nextId = parseInt($elLatest.eq(parseInt(lastSize)-1).find("tr:first-child").find("label").attr("for").replace("pc_item_",""))+1;

		var $el = $("#sit_request_frm > table > tbody > tr:last");
		var fld = "<tr>\n";
		fld += "<td>\n";
		fld += "<table>\n";
		fld += "<colgroup>\n";
		fld += "<col class=\"grid_2\">\n";
		fld += "<col>\n";
		fld += "</colgroup>\n";
		fld += "<tbody>\n";
		fld += "<tr>\n";
		fld += "<th scope=\"row\"><label for=\"pc_item_"+nextId+"\">상품명</label></th>\n";;
		fld += "<td><input type=\"text\" name=\"pc_item[]\" id=\"pc_item_"+nextId+"\" value=\"\" class=\"frm_input\" size=\"75\"> <button type=\"button\" id=\"del_request_row\" class=\"btn_frmline\">삭제</button></td>\n";
		fld += "</tr>\n";
		fld += "<tr>\n";
		fld += "<th scope=\"row\"><label for=\"pc_item_url_"+nextId+"\">상품주소 URL</label></th>\n";
		fld += "<td><input type=\"text\" name=\"pc_item_url[]\" id=\"pc_item_url_"+nextId+"\" value=\"\" class=\"frm_input\" size=\"75\"></td>\n";
		fld += "</tr>\n";
		fld += "<tr>\n";
		fld += "<th scope=\"row\"><label for=\"pc_item_option_"+nextId+"\">상품옵션</label></th>\n";
		fld += "<td><input type=\"text\" name=\"pc_item_option[]\" id=\"pc_item_option_"+nextId+"\" value=\"\" class=\"frm_input\" size=\"75\"></td>\n";
		fld += "</tr>\n";
		fld += "<tr>\n";
		fld += "<th scope=\"row\"><label for=\"pc_type_"+nextId+"\">구입방식</label></th>\n";
		fld += '<td><?php echo getSelectArrayList($purchaseShoppingType,"pc_type[]","pc_type_".'+nextId+',""," onchange=\"pc_type_chgmsg('+nextId+',this.value);\"");?></td>\n';
		fld += "</tr>\n";
		fld += "<tr>\n";
		fld += "<th scope=\"row\"><label for=\"pc_price_"+nextId+"\">상품가격</label></th>\n";
		fld += "<td>USD <input type=\"text\" name=\"pc_price[]\" id=\"pc_price_"+nextId+"\" value=\"\" class=\"frm_input\" size=\"10\"><span id='pc_price_msg_"+nextId+"'></span></td>\n";
		fld += "</tr>\n";
		fld += "<tr>\n";
		fld += "<th scope=\"row\"><label for=\"pc_qty_"+nextId+"\">상품수량</label></th>\n";
		fld += "<td><input type=\"text\" name=\"pc_qty[]\" id=\"pc_qty_"+nextId+"\" value=\"1\" class=\"frm_input\" size=\"3\"> 개</td>\n";
		fld += "</tr>\n";
		fld += "</tbody>\n";
		fld += "</table>\n";
		fld += "</td>\n";
		fld += "</tr>";

		$el.after(fld);



	});

	// 입력필드삭제
	$("#del_request_row").live("click", function() {
		$(this).closest("tr").parent().closest("tr").remove();

		supply_sequence();
	});


});


function pc_type_chgmsg(num,selVal)
{
	if(selVal=="N"){
		$("#pc_price_msg_"+num).html('');
		$("label[for=pc_price_"+num+"]").html("상품가격");
	}else if(selVal=="A"){
		$("#pc_price_msg_"+num).html(" 한도내에서 입찰해주세요!");
		$("label[for=pc_price_"+num+"]").html("입찰 최대 가격");
	}
}

function forderform_check(f)
{
    errmsg = "";
    errfld = "";
    var deffld = "";

	$("#sit_request_frm").find("input").each(function(){
		if($(this).val()==""){
			if($(this).attr("name")=="pc_item[]"){ alert("상품명을 입력하세요!"); $(this).focus(); return false; } 
			else if($(this).attr("name")=="pc_item_url[]"){ alert("상품주소 URL을 입력하세요!"); $(this).focus(); return false; } 
			else if($(this).attr("name")=="pc_price[]"){ alert("가격을 입력하세요!"); $(this).focus(); return false; } 
			else if($(this).attr("name")=="pc_qty[]"){ alert("수량을 입력하세요!"); $(this).focus(); return false; } 
		}
	});

    check_field(f.od_name, "주문하시는 분 이름을 입력하십시오.");
    check_field(f.od_addr1, "우편번호 찾기를 이용하여 주문하시는 분 주소를 입력하십시오.");
    //check_field(f.od_addr2, " 주문하시는 분의 상세주소를 입력하십시오.");
    check_field(f.od_zip1, "");
    check_field(f.od_zip2, "");

    clear_field(f.od_email);
    if(f.od_email.value=='' || f.od_email.value.search(/(\S+)@(\S+)\.(\S+)/) == -1)
        error_field(f.od_email, "E-mail을 바르게 입력해 주십시오.");

    check_field(f.od_b_name, "받으시는 분 이름을 입력하십시오.");
    check_field(f.od_b_addr1, "우편번호 찾기를 이용하여 받으시는 분 주소를 입력하십시오.");
    //check_field(f.od_b_addr2, "받으시는 분의 상세주소를 입력하십시오.");
    check_field(f.od_b_zip1, "");
    check_field(f.od_b_zip2, "");

    return true;
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