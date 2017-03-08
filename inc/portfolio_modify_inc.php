<form name="fportfolio" id="fportfolio" method="POST" enctype="multipart/form-data">
<input type="hidden" name="HTTP_CHK" value="CHK_OK">


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
			<td align="left"><input type="text" name="port_title[]" style="width:360px;"></td>
			<td align="center" width="70px">
				<select name="port_metal[]">
					<option value="gold">금</option>
					<option value="silver">은</option>
					<option value="platinum">백금</option>
					<option value="palladium">팔라듐</option>
				</select>
			</td>
			<td align="center" width="70px"><input type="text" name="port_cnt[]" style="width:60px;"></td>
			<td align="center" width="70px"><input type="text" name="port_oz[]" style="width:60px;"></td>
			<td align="center" width="100px">$ <input type="text" name="port_buy[]" style="width:80px;"></td>
		</tr>
		<tr height="30px">
			<td align="left" colspan="5">
				이미지&nbsp;&nbsp;&nbsp;&nbsp;
				<input type="file" name="port_file[]">
			</td>
		</tr>
		<tr>
			<td colspan="5">
				
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