<?php
$sub_menu = '300700';
include_once('./_common.php');

auth_check($auth[$sub_menu], "w");

$g5['title'] = "국내시세 관리";
include_once(G5_ADMIN_PATH.'/admin.head.php');

$frm_submit = '<div class="btn_confirm01 btn_confirm">
    <input type="submit" value="확인" class="btn_submit" accesskey="s">
    <a href="'.G5_URL.'/">메인으로</a>
</div>';
?>

<form name="fconfigform" id="fconfigform" method="post" onsubmit="return fconfigform_submit(this);">
<input type="hidden" name="token" value="<?php echo $token ?>" id="token">



<div class="tbl_head01 tbl_wrap">
    <table>
    <caption><?php echo $g5['title']; ?> 목록</caption>
    <thead>
    <tr>
        <th scope="col">종류</th>
        <th scope="col">살때(VAT별도) </th>
        <th scope="col">팔때</th>
        <th scope="col">변화량</th>
    </tr>
    </thead>
    <tbody>
    <?php
		$i=0;
    foreach($domesticPriceGubunList as $key=>$vars) {

		$sql = "select * from $g5[g5_domestic_price_table] where dp_gubun = '".$key."' order by dp_date desc limit 1";
	    $row = sql_fetch($sql);

		if(!$row[dp_arrow])$row[dp_arrow]="up";

        $bg = 'bg'.($i%2);

		$readonly = "";
		if($i==1 || $i==2)$readonly = " readonly";
    ?>
	<input type="hidden" name="dp_gubun[]" value="<?php echo $key?>">
    <tr class="<?php echo $bg; ?>">
        <td class="td_mbid"><?php echo $vars?></td>
        <td class="td_numbig"><input type="text" name="dp_buy_price[<?php echo $key?>]" id="dp_buy_price_<?php echo $i?>" <?php echo $readonly?> class="frm_input" value="<?php echo $row[dp_buy_price]?>"> 원</td>
        <td class="td_numbig"><input type="text" name="dp_sell_price[<?php echo $key?>]" id="dp_sell_price_<?php echo $i?>" <?php echo $readonly?> class="frm_input" value="<?php echo $row[dp_sell_price]?>"> 원</td>
        <td class="td_num td_pt">
		<input type="radio" name="dp_arrow[<?php echo $key?>]" value="up" <?php if($row[dp_arrow]=="up")echo "checked";?>> +
		<input type="radio" name="dp_arrow[<?php echo $key?>]" value="down" <?php if($row[dp_arrow]=="down")echo "checked";?>> -
		<input type="text" name="dp_rate[<?php echo $key?>]" class="frm_input" value="<?php echo $row[dp_rate]?>" size=8> 원</td>
    </tr>

    <?php
		$i++;
    }
    ?>
    </tbody>
    </table>
</div>

<?php echo $frm_submit; ?>

</form>

<script>

$(function(){
	$("#dp_buy_price_0").keyup(function(){
		var buy_gold24k = 0;
		var buy_gold18k = 0;

		if(parseInt($(this).val())>=100){
			buy_gold24k = Ceil($(this).val() * 0.825,2,"T");
			buy_gold18k = Ceil($(this).val() * 0.6435,2,"T");
		}

		$("#dp_buy_price_1").val(buy_gold24k);
		$("#dp_buy_price_2").val(buy_gold18k);
	});

	$("#dp_sell_price_0").keyup(function(){

		var sell_gold24k = 0;
		var sell_gold18k = 0;

		if(parseInt($(this).val())>=100){
			sell_gold24k = Ceil($(this).val() * 0.735,2,"T");
			sell_gold18k = Ceil($(this).val() * 0.57,2,"T");
		}

		$("#dp_sell_price_1").val(sell_gold24k);
		$("#dp_sell_price_2").val(sell_gold18k);
	});
});

function fconfigform_submit(f)
{
    f.action = "./domesticprice_form_update.php";
    return true;
}
</script>


<?php
include_once(G5_ADMIN_PATH.'/admin.tail.php');
?>