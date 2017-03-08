var option_add = false;
var supply_add = false;
var isAndroid = (navigator.userAgent.toLowerCase().indexOf("android") > -1);

$(function() {

    // 수량변경 및 삭제
    $("#sit_sel_option button").live("click", function() {

        var mode = $(this).text();
        var this_qty, this_qty1, max_qty = 9999, min_qty = 1;
        var $el_qty = $(this).parent().find("input[name^=ct_qty]");
		var $el_qty1 = $(this).parent().find("input[name=ct_qty1]");
		this_qty1 = $el_qty1.val();

        switch(mode) {
            case "+":
                this_qty = parseInt($el_qty.val().replace(/[^0-9]/, "")) + 1;
                if(this_qty > max_qty) {
                    this_qty = max_qty;
                    alert("최대 구매수량은 "+number_format(String(max_qty))+" 입니다.");
                }

                $el_qty.val(this_qty);
				$el_qty1.val(this_qty1);
                price_calculate();
                break;

            case "-":
                this_qty = parseInt($el_qty.val().replace(/[^0-9]/, "")) - 1;
                if(this_qty < min_qty) {
                    this_qty = min_qty;
                    alert("최소 구매수량은 "+number_format(String(min_qty))+" 입니다.");
                }
                $el_qty.val(this_qty);
				$el_qty1.val(this_qty1);
                price_calculate();
                break;

  
            default:
                alert("올바른 방법으로 이용해 주십시오.");
                break;
        }
    });

	// 수량변경 및 삭제
   /* $("#sit_sel_option1 button").live("click", function() {

        var mode = $(this).text();
        var this_qty, max_qty = 9999, min_qty = 1;
        var $el_qty = $(this).parent().find("input[name^=ct_qty]");

        switch(mode) {
            case "+":
                this_qty = parseInt($el_qty.val().replace(/[^0-9]/, "")) + 1;


                if(this_qty > max_qty) {
                    this_qty = max_qty;
                    alert("최대 구매수량은 "+number_format(String(max_qty))+" 입니다.");
                }

                $el_qty.val(this_qty);
                price_calculate();
                break;

            case "-":
                this_qty = parseInt($el_qty.val().replace(/[^0-9]/, "")) - 1;
                if(this_qty < min_qty) {
                    this_qty = min_qty;
                    alert("최소 구매수량은 "+number_format(String(min_qty))+" 입니다.");
                }
                $el_qty.val(this_qty);
                price_calculate();
                break;

  
            default:
                alert("올바른 방법으로 이용해 주십시오.");
                break;
        }
    });*/

    // 수량직접입력
    $("input[name^=ct_qty]").live("keyup", function() {
        var val= $(this).val();

        if(val != "") {
            if(val.replace(/[0-9]/g, "").length > 0) {
                alert("수량은 숫자만 입력해 주십시오.");
                $(this).val(1);
            } else {
                var d_val = parseInt(val);
                if(d_val < 1 || d_val > 9999) {
                    alert("수량은 1에서 9999 사이의 값으로 입력해 주십시오.");
                    $(this).val(1);
                } 
            }

            price_calculate();
        }
    });
});


// 가격계산
function price_calculate()
{
   
    var $el_qty = $("input[name^=ct_qty]");
	var $el_qty1 = $("input:hidden[name='ct_qty1']");
	var $el_payment = $("input[name^=ct_payment]");
	var op_price = $("input[name=op_price]").val();
    var tmpPrice, tmpPrice1, price, type, qty, qty1, total = 0;

	if(op_price){
		op_price = op_price;
	}else{
		op_price = 0;
	}

	qty = parseInt($el_qty.val());
	qty1 = qty + parseInt($el_qty1.val());

	$("#volume_body > table > tbody > tr").each(function(){
		if(qty1>=parseInt($(this).find("td").eq(0).attr("sqty")) && qty1<=parseInt($(this).find("td").eq(0).attr("eqty"))){

			//if($el_payment.eq(0).attr("checked"))tmpPrice = parseInt($(this).find("td").eq(1).attr("volume_price"));
			//else if($el_payment.eq(1).attr("checked"))tmpPrice = parseInt($(this).find("td").eq(2).attr("volume_price"));

			tmpPrice = parseInt($(this).find("td").eq(3).attr("volume_price"));
			tmpPrice = tmpPrice * qty + parseInt(op_price);

			tmpPrice1 = parseInt($(this).find("td").eq(4).attr("volume_price"));
			tmpPrice1 = tmpPrice1 * qty + parseInt(op_price);

			//$("#it_view_price").html(number_format(String(tmpPrice))+" 원");
			$("#it_view_change_price").html(number_format(String(tmpPrice))+" 원");
			
			$("#it_view_price1").html(number_format(String(tmpPrice))+" 원");
			$("input#it_price").val(tmpPrice);

			//$("#it_view_card_price").html(number_format(String(tmpPrice1))+" 원");
			$("#it_view_card_price1").html(number_format(String(tmpPrice1))+" 원");
			$("input#it_card_price").val(tmpPrice1);
		}
	});
	
	var it_price = parseInt($("input#it_price").val()) + parseInt(op_price);
    if(isNaN(it_price))
        return;

	price = parseInt(it_price);

	total += price * qty;
	

    //$("#sit_tot_price").empty().html("총 금액 : "+number_format(String(total))+"원");

}

// php chr() 대응
function chr(code)
{
    return String.fromCharCode(code);
}