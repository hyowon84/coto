

$(document).ready(function(){
	
	var layerWindow_gp = $('.mw_layer_gp');
	
	//웹/모바일 버전 공동구매 신청하기
	$(document).on("click",'.gp_view_bn',function(){
		var gp_id = $(this).attr("gp_id");
		var ca_id = $(this).attr("ca_id");
		var cnt = $("input[name='ct_qty"+gp_id+"']").val();
		
		var con = "";
		var price = $("input[name='op_price']").val();

		layerWindow_gp.addClass('open_gp');

		/* 웹 기준 */
		//alert($(window).scrollTop());
		
		/* 목록페이지가 아닌 상세페이지의 경우 gpid를 붙일필요없음 */
		if(typeof(cnt) == "undefined") {
			cnt = $("input[name='ct_qty']").val();
		}
		
		
		topmargin = (topmargin) ? topmargin : 0;
		
		if(g5_is_mobile) {
			/* 모바일 기준 */
			$("#layer_gp").css("top", ($(window).scrollTop()+topmargin)+"px");
		} else {
			$("#layer_gp").css("top", ($(window).scrollTop()+topmargin)+"px");
		}
		
		
		
		$("select[name^='gp_option']").each(function(i){
			con += $("select[name^='gp_option']").eq(i).val() + "|";
		});

		$(".gp_view").html("");
		
		$(".gp_view").hide();
		$(".gp_view_loading").show();		
		
		$.ajax({
			type : "GET",
			dataType : "HTML",
			url : "./_Ajax.gp.view.php",	/* 웹/모바일 스킨 분기 */
			data : "gp_id=" + gp_id + "&ca_id=" + ca_id + "&cnt=" + cnt + "&op_name=" + con + "&op_price=" + price,
			success : function(data){
				$(".gp_view").html(data);
				$(".gp_view_loading").hide();				
				$(".gp_view").show();				
			}
		});
	});
	
	/* 레이어팝업 X버튼 클릭시 이벤트 */
	$(document).on("click",'#layer_gp .close_gp',function(){
		layerWindow_gp.removeClass('open_gp');
		return false;
	});
	layerWindow_gp.find('>.bg_gp').mousedown(function(event){
		layerWindow_gp.removeClass('open_gp');
		return false;
	});	
	
	
	
	$(".apm_search_sub").find("input:checkbox").css({"width":"10px", "height":"10px", "border-color":"#cdcbcb", "background":"#fff"});

	$("input:checkbox[name='search_btn[]']").change(function(){

		var val = "";

		$("input:checkbox[name='search_btn[]']").each(function(i){

			if($("input:checkbox[name='search_btn[]']").eq(i).is(":checked") == true){

				val += $("input:checkbox[name='search_btn[]']").eq(i).val() + "|";
			}

		});

		location.href = "./gplist.php?ca_id="+ca_id+"&apm_type="+apm_type+"&sch_val=" + val;
	});

	$("input[name='search_btn_all']").change(function(){

		var val = "";

		for(var i = 0; i < 4; i++){
			$("input:checkbox[name='search_btn[]']").eq(i).attr("checked", false);
		}

		location.href = "./gplist.php?ca_id="+ca_id+"&apm_type="+apm_type+"&sch_val=" + val;
	});

	
	$(".apm_type").find("li").click(function(){
		var type = $(this).attr("type");
		location.href = "./gplist.php?ca_id="+ca_id+"&apm_type=" + type;
	});

	$(".apm_btn").click(function(){
		$("input[name='chk_sch[]']").each(function(i){
			if($(".apm_btn").find("input[name='chk_sch[]']").eq(i).attr("checked") == true){
				alert($(".apm_btn").find("input[name='chk_sch[]']").eq(i).val());
			}
		});
	});

	$("input:checkbox[name='chk_sch[]']").click(function(){
		var status = $(this).attr("status");
		var val = $(this).val();
		var app_status = false;

		$("input:hidden[name='apm_val[]']").each(function(i){
			if($("input:hidden[name='apm_val[]']").eq(i).val() == val){
				app_status = true;
			}
		});

		if(app_status == false){
			$(".apm_sch_param").append("<div style='float:left;padding:0 0 0 3px;border:0px;'>"+status+"<font color='#74d3d0'>"+val+"</font><img src='"+g5_url+"/img/apm_search_del.gif' border='0' align='absmiddle' style='cursor:pointer;' onclick='apm_del_btn(this, \""+val+"\");'><input type='hidden' name='apm_val[]' value='"+val+"'></div>");
		}else{

			$("input:hidden[name='apm_val[]']").each(function(i){
				if($("input:hidden[name='apm_val[]']").eq(i).val() == val){
					$("input:hidden[name='apm_val[]']").eq(i).parent().remove();
				}
			});
			
			$("input[name='chk_sch[]']").each(function(i){
				if($("input[name='chk_sch[]']").eq(i).val() == val){
					$("input[name='chk_sch[]']").eq(i).attr("checked", false);
				}
			});
		}
	});

	$(".apm_all_del").click(function(){
		$("input[name='chk_sch[]']").attr("checked", false);
		$(".apm_sch_param").html("");
	});

	$(".apm_submit_btn").click(function(){

		var val = "";
		$("input[name='apm_val[]']").each(function(i){
			val += $("input[name='apm_val[]']").eq(i).val() + "|";
		});

		val = val.substring(0, val.length-1);

		$("input[name='apmval']").val(val);
		$("form[name='fapmsearch']").submit();
	});

	$(".apm_search_title").click(function(){
		if($(".apm_menu").css("display") == "none"){
			$(".apm_menu").slideDown(500);
		}else{
			$(".apm_menu").slideUp(500);
		}
	});

	$(".sch_all_btn").click(function(){
		var sch_val_all = $("input:text[name='sch_val_all']").val();

		$.post("./Ajax.gplist.search.php", {sch_val : sch_val_all}).done(function(data){
			location.href = "./gplist.php?ca_id="+ca_id+"&apm_type="+apm_type+"&sch_val_all=" + sch_val_all;
		});

	});

	$("input[name='sch_val_all']").keypress(function(){
		if(event.keyCode == 13){
			var sch_val_all = $(this).val();

			$.post("./Ajax.gplist.search.php", {sch_val : sch_val_all}).done(function(data){
				location.href = "./gplist.php?ca_id="+ca_id+"&apm_type="+apm_type+"&sch_val_all=" + sch_val_all;
			});
		}
	});

	$(".apm_hit_btn").click(function(){
		var val = $(this).attr("con");
		location.href = "./gplist.php?ca_id="+ca_id+"&apm_type="+apm_type+"&sch_val_all=" + val;
	});

	
	//수량 +,-
	$(".minus_qty").click(function() {
		var formName = $(this).attr("name").substring(6);
		var qty = parseInt( $("input[name=ct_qty"+formName+"]").val() );
		qty = (qty<=0?0:qty-1)
		$("input[name=ct_qty"+formName+"]").val(qty);
	});

	$(".plus_qty").click(function() {
		var formName = $(this).attr("name").substring(5);
		var qty = parseInt( $("input[name=ct_qty"+formName+"]").val() );
		qty = (qty>=999?999:qty+1)
		$("input[name=ct_qty"+formName+"]").val(qty);
	});
	
});

function apm_del_btn(th, val){
	$(th).parent().remove();

	$("input[name='chk_sch[]']").each(function(i){
		if($("input[name='chk_sch[]']").eq(i).val() == val){
			$("input[name='chk_sch[]']").eq(i).attr("checked", false);
		}
	});
}



