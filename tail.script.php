<?php
if (!defined('_GNUBOARD_')) exit; // 개별 페이지 접근 불가
?>

<script>
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
