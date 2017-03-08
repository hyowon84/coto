<?
include_once("./_common.php");

$wr_id = $_POST[wr_id];

$row = sql_fetch("select * from g5_write_portfolio where wr_id=".$wr_id." ");
?>
<form name="fportfolio_u" id="fportfolio_u" method="POST" enctype="multipart/form-data">
<input type="hidden" name="HTTP_CHK" value="CHK_OK">
<input type="hidden" name="mode" value="u">
<input type="hidden" name="wr_id" value="<?=$wr_id?>">


<div class="cl" style="background:#223652;height:17px;padding:7px 0 7px 0;margin:5px 0 0 0;">
	<table border="0" cellspacing="0" cellpadding="0" width="100%">
		<tr>
			<td align="center" style="color:#3aaaf2;font-weight:bold;">아이템명</td>
			<td align="center" width="70px" style="color:#3aaaf2;font-weight:bold;">금속</td>
			<td align="center" width="70px" style="color:#3aaaf2;font-weight:bold;">수량</td>
			<td align="center" width="70px" style="color:#3aaaf2;font-weight:bold;">온스</td>
			<td align="center" width="100px" style="color:#3aaaf2;font-weight:bold;">구입가</td>
		</tr>
	</table>
</div>

<div class="cl">
	<table border="0" cellspacing="0" cellpadding="0" width="100%">
		<tr height="30px">
			<td align="left"><input type="text" name="port_title" style="width:360px;" value="<?=$row[wr_subject]?>"></td>
			<td align="center" width="70px">
				<select name="port_metal">
					<option value="gold" <?if($row[wr_2] == "gold"){ echo "selected"; }?>>금</option>
					<option value="silver" <?if($row[wr_2] == "silver"){ echo "selected"; }?>>은</option>
					<option value="platinum" <?if($row[wr_2] == "platinum"){ echo "selected"; }?>>백금</option>
					<option value="palladium" <?if($row[wr_2] == "palladium"){ echo "selected"; }?>>팔라듐</option>
				</select>
			</td>
			<td align="center" width="70px"><input type="text" name="port_cnt" style="width:60px;" value="<?=$row[wr_3]?>"></td>
			<td align="center" width="70px"><input type="text" name="port_oz" style="width:60px;" value="<?=$row[wr_4]?>"></td>
			<td align="center" width="100px">$ <input type="text" name="port_buy" style="width:80px;" value="<?=$row[wr_5]?>"></td>
		</tr>
		<tr height="30px">
			<td align="left" colspan="5">
				이미지&nbsp;&nbsp;&nbsp;&nbsp;
				<input type="file" name="port_file">
			</td>
		</tr>
		<tr>
			<td colspan="5">
				<?
				if($row[wr_1]){

					if($row[img_height] > 300){
						echo "<img src='".G5_URL."/data/file/portfolio/".$row[wr_1]."' width='300px' height='300px'>";
					}else{
						echo "<img src='".G5_URL."/data/file/portfolio/".$row[wr_1]."'>";
					}
				}
				?>
			</td>
		</tr>
		<tr height="10px"><td colspan="5"></td></tr>
		<tr height="50px">
			<td align="center" colspan="5">
				<img src="<?=G5_URL?>/img/port_del_bn.gif" border="0" align="absmiddle" style="cursor:pointer;" class="port_delete">
				<img src="<?=G5_URL?>/img/port_submit_bn.gif" border="0" align="absmiddle" style="cursor:pointer;" class="port_submit">
			</td>
		</tr>
	</table> 
</div>
</form>

<script type="text/javascript">
$(document).ready(function(){
	$(".port_submit").click(function(){
		var chk = false;
		var chk1;
		var data;

		if($("input:text[name='port_title']").val() == ""){
			alert("제목을 입력하세요.");
			$("input:text[name='port_title']").focus();
			return false;
		}else if($("input:text[name='port_cnt']").val() == ""){
			alert("수량을 입력하세요.");
			$("input:text[name='port_cnt']").focus();
			return false;
		}else if($("input:text[name='port_oz']").val() == ""){
			alert("온스를 입력하세요.");
			$("input:text[name='port_oz']").focus();
			return false;
		}else if($("input:text[name='port_buy']").val() == ""){
			alert("구입가를 입력하세요.");
			$("input:text[name='port_buy']").focus();
			return false;
		}
		chk1 = true;

		if(chk1 == true){

			if(confirm("등록 하시겠습니까?")){
				$("form[name='fportfolio_u']").attr({"action":"<?=G5_URL?>/portfolio/portfolio_update.php"}).submit();
			}
		}
	});
});
</script>