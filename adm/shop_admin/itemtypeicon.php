<?php
$sub_menu = '400200';
$sub_sub_menu = '2';

include_once('./_common.php');

auth_check($auth[$sub_menu], "r");

$token = get_token();

$g5['title'] = '상품유형아이콘관리';
include_once (G5_ADMIN_PATH.'/admin.head.php');

$item_type_res = sql_query("select * from {$g5['g5_item_type_icon_table']} order by no desc ");
?>

<section id="scp_list">
    <h2>상품유형아이콘관리</h2>

	<form name="fitemtypemodify" id="fitemtypemodify" method="post" enctype="multipart/form-data">
	<input type="hidden" name="HTTP_CHK" value="CHK_OK">
	<input type="hidden" name="mode" value="u">
	<input type="hidden" name="idx">

    <div class="tbl_head01 tbl_wrap">
        <table>
        <caption>상품유형아이콘관리</caption>
        <tbody>

		<?
		for($i = 0; $i < $item_type_row = mysql_fetch_array($item_type_res); $i++){
		?>
        <tr class="<?php echo $bg; ?> del<?=$item_type_row[no]?>">
            <td width="7%"><?=$item_type_row[no]?></td>
            <td width="20%">
				<input type="file" name="img[<?=$item_type_row[no]?>]">
				<?=$item_type_row[tp_img]?>
			</td>
			<td>
				<input type="text" name="item_type_name[<?=$item_type_row[no]?>]" style="padding:5px;width:250px;height:20px;border:1px solid #ccc;" value="<?=$item_type_row[tp_name]?>">
			</td>
            <td width="7%" style="text-align:center;">
				<input type="button" value="수정" class="btn" mode="modify" idx="<?=$item_type_row[no]?>"> 
				<input type="button" value="삭제" class="btn" mode="del" idx="<?=$item_type_row[no]?>">
			</td>
        </tr>
		<?
		}
		?>
        </tbody>
        </table>
    </div>
	</form>


	<form name="fitemtype" id="fitemtype" method="post" enctype="multipart/form-data">
	<input type="hidden" name="w" value="d">
	<input type="hidden" name="HTTP_CHK" value="CHK_OK">
	<input type="hidden" name="mode" value="w">

    <div class="btn_list01 btn_list">

		<input type="file" name="item_type_img" style="padding:5px;height:20px;">
		유형명 : <input type="text" name="item_type_name" style="padding:5px;width:250px;height:20px;">
        <input type="button" value="이미지 등록" style="padding:5px;height:32px;" class="item_type_submit">
    </div>
	</form>

</section>

<div class="test"></div>

<script type="text/javascript">

$(document).ready(function(){

	$(".item_type_submit").click(function(){
		
		$("form[name='fitemtype']").attr("action", "./itemtypeicon_update.php").submit();
		
	});

	$(".btn").click(function(){
		var idx = $(this).attr("idx");
		var mode = $(this).attr("mode");
		var img = $("input[name='img["+idx+"]']").val();
		
		if(mode == "del"){
			if(confirm("정말 삭제하시겠습니까?")){
				$.ajax({
					type : "POST",
					dataType : "HTML",
					url : "./_Ajax.item_type.php",
					data : "mode="+mode+"&idx=" + idx,
					success : function(data){
						$(".del" + idx).remove();
						//$(".test").html(data);
					}
				});
			}
		}else{
			if(confirm("수정하시겠습니까?")){
				$("form[name='fitemtypemodify']").find("input[name='idx']").val(idx);
				$("form[name='fitemtypemodify']").attr("action", "./itemtypeicon_update.php").submit();
			}
		}
	});


});

</script>

<?php
include_once(G5_ADMIN_PATH.'/admin.tail.php');
?>