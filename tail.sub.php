<?php
if (!defined('_GNUBOARD_')) exit; // 개별 페이지 접근 불가
?>

<?php if ($is_admin == 'super') {  ?><!-- <div style='float:left; text-align:center;'>RUN TIME : <?php echo get_microtime()-$begin_time; ?><br></div> --><?php }  ?>

<!-- ie6,7에서 사이드뷰가 게시판 목록에서 아래 사이드뷰에 가려지는 현상 수정 -->
<!--[if lte IE 7]>
<script>
$(function() {
    var $sv_use = $(".sv_use");
    var count = $sv_use.length;

    $sv_use.each(function() {
        $(this).css("z-index", count);
        $(this).css("position", "relative");
        count = count - 1;
    });
});
</script>
<![endif]-->

</body>

<script>


//장바구니담기 함수
function cart_add(mode,it_id,gpcode) {
	var it_qty = $('#'+it_id+'_qty').val();

	$.ajax({
		dataType:"json",
		type: "POST",
		url: "/coto/cart.add.php",
		data: {
			'mode'	: mode,
			'gpcode' : gpcode,
			'it_id' : it_id,
			'it_qty' : it_qty
		},
		cache: false,
		success: function(data) {
			alert(data.msg);
		}
	});
}

function keyNumeric()
{
	var key = event.keyCode;
	
	if( (key >= 48 && key <= 57) || key == 8  || key == 9 || (key >= 37 && key <= 40) || key == 46 || (key == 13) || ((key < 12592) && (key > 12687)) ) {
		event.returnValue = true;
	}
	else{
		event.returnValue = false;
	}
	
	return event.returnValue;
}
$("input[type=tel]").bind("keydown",function(){
	keyNumeric();
});


</script>

</html>
<?php echo html_end(); // HTML 마지막 처리 함수 : 반드시 넣어주시기 바랍니다. 
//$sqli->close();

//여기에 놓으면 안됨
//mysql_close($connect_db);
?>