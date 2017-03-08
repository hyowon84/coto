<?php
$sub_menu = '500300';
$sub_sub_menu = '3';

include_once('./_common.php');

auth_check($auth[$sub_menu], "r");

$g5['title'] = '공동구매관리';
include_once (G5_ADMIN_PATH.'/admin.head.php');
include_once(G5_PLUGIN_PATH.'/jquery-ui/datepicker.php');


$result = sql_query("select * from g5_group_cnt_pay order by no asc");

$frm_submit = '<div class="btn_confirm01 btn_confirm">
    <input type="submit" value="확인" class="btn_submit" accesskey="s">
</div>';

?>

<form name="cntPayForm" method="POST" action="./grouppur_update.php">
<input type="hidden" name="HTTP_CHK" value="CHK_OK">
<input type="hidden" name="mode" value="">
<input type="hidden" name="idx" value="">
<input type="hidden" name="dealer" value="">

<div class="local_sch02 local_sch">

	<div>
		<font color="red">예) 공동구매가 완료시 종료버튼을 꼭 누른 후 새 공동구매코드와 날짜, 총액을 입력하시고 확인을 눌러야 새 공동구매가 시작됩니다.</font>
	</div>

	<?php
	for($i=0;$row=sql_fetch_array($result);$i++){

		$gc_state = "";
		if($row['gc_state']=="S")$gc_state = "<font color='blue'>신청중</font>";
		elseif($row['gc_state']=="W")$gc_state = "<font color='green'>집계중</font>";
		elseif($row['gc_state']=="E")$gc_state = "<font color='red'>주문완료</font>";
	?>
    <div>
        <strong class="sch_long"><?=$row[gubun]?><br>(<?php echo $gc_state;?>)</strong>

        <input type="hidden" name="gubun[<?=$row[no]?>]" value="<?=$row[gubun]?>">

        <label for="f_date" class="sound_only">기간 시작일</label>
        <input type="text" name="fr_date[<?=$row[no]?>]" value="<?php echo $row['fr_date']; ?>" id="fr_date_<?=$row[no]?>" required class="required frm_input grouppur_date" size="10" maxlength="8">
        ~
        <label for="l_date" class="sound_only">기간 종료일</label>
        <input type="text" name="to_date[<?=$row[no]?>]" value="<?php echo $row['to_date']; ?>" id="to_date_<?=$row[no]?>" required class="required frm_input grouppur_date" size="10" maxlength="8">
		/
		총액 : <input type="text" name="cnt_pay[<?=$row[no]?>]" value="<?php echo number_format($row[cnt_pay]); ?>" id="cnt_pay" required class="required frm_input" size="10" maxlength="20">

		/ 공동구매 건별 코드<input type="text" name="group_code[<?=$row[no]?>]" value="<?php echo $row[group_code]; ?>" id="group_code" required class="required frm_input" size="20">

		<input type="button" value="확인" class="btn_submit" style="background:#ddd;color:#000;" idx="<?=$row['no']?>" mode="modify" dealer="<?=$row[gubun_code]?>">
		<input type="button" value="집계중" class="btn_submit" style="background:#617D46;" idx="<?=$row['no']?>" mode="wait" dealer="<?=$row[gubun_code]?>">
		<input type="button" value="주문완료" class="btn_submit" idx="<?=$row['no']?>" mode="end" dealer="<?=$row[gubun_code]?>">

    </div>
	<?php
	}
	?>
</div>

</form>


<form name="cntPayForm2" method="POST" action="./grouppur_update.php">
<input type="hidden" name="HTTP_CHK" value="CHK_OK">
<input type="hidden" name="mode" value="reOrder">

<div class="local_sch02 local_sch">

	<div>
		<font color="red">생성되지 않는 주문 재 생성하기</font>
	</div>

	<div class="tbl_frm01 tbl_wrap">
        <table>
        <caption>홈페이지 기본환경 설정</caption>
        <colgroup>
            <col class="grid_4">
            <col>
        </colgroup>
        <tbody>
        <tr>
            <th scope="row"><label for="group_code">공동구매 코드<strong class="sound_only">필수</strong></label></th>
            <td><input type="text" name="group_code" value="" id="group_code" required class="required frm_input" size="40"> <input type="submit" value="주문생성" class="btn_submit"></td>
        </tr>
		</table>
	</div>
</div>

</form>

<script>
function byte_check(el_cont, el_byte)
{
    var cont = document.getElementById(el_cont);
    var bytes = document.getElementById(el_byte);
    var i = 0;
    var cnt = 0;
    var exceed = 0;
    var ch = '';

    for (i=0; i<cont.value.length; i++) {
        ch = cont.value.charAt(i);
        if (escape(ch).length > 4) {
            cnt += 2;
        } else {
            cnt += 1;
        }
    }

    //byte.value = cnt + ' / 80 bytes';
    bytes.innerHTML = cnt + ' / 80 bytes';

    if (cnt > 80) {
        exceed = cnt - 80;
        alert('메시지 내용은 80바이트를 넘을수 없습니다.\r\n작성하신 메세지 내용은 '+ exceed +'byte가 초과되었습니다.\r\n초과된 부분은 자동으로 삭제됩니다.');
        var tcnt = 0;
        var xcnt = 0;
        var tmp = cont.value;
        for (i=0; i<tmp.length; i++) {
            ch = tmp.charAt(i);
            if (escape(ch).length > 4) {
                tcnt += 2;
            } else {
                tcnt += 1;
            }

            if (tcnt > 80) {
                tmp = tmp.substring(0,i);
                break;
            } else {
                xcnt = tcnt;
            }
        }
        cont.value = tmp;
        //byte.value = xcnt + ' / 80 bytes';
        bytes.innerHTML = xcnt + ' / 80 bytes';
        return;
    }
}
</script>

<?php

$gp_default = sql_fetch("select * from {$g5['g5_shop_group_purchase_default_table']}");
?>

<form name="fconfig" action="./grouppur_form_sms_update.php" method="post">
<section id="anc_scf_sms" >
    <h2 class="h2_frm">SMS 설정</h2>

    <section id="scf_sms_pre">
        <h3>사전에 정의된 SMS프리셋</h3>
        <div class="local_desc01 local_desc">
            <dl>
                <dt>입금대기</dt>
                <dd>{이름} {공동구매코드} {주문금액} {회사명}</dd>
                <dt>결제완료</dt>
                <dd>{이름} {공동구매코드} {주문금액} {회사명}</dd>
                <dt>배송중</dt>
                <dd>{이름} {택배회사} {운송장번호} {공동구매코드} {회사명}</dd>
            </dl>
           <p><?php echo help('주의! 80 bytes 까지만 전송됩니다. (영문 한글자 : 1byte , 한글 한글자 : 2bytes , 특수문자의 경우 1 또는 2 bytes 임)'); ?></p>
        </div>

        <div id="scf_sms">
            <?php
            $scf_sms_title = array (1=>"입금대기", "결제완료", "배송중");
            for ($i=1; $i<=3; $i++) {
            ?>
            <section class="scf_sms_box">
                <h4><?php echo $scf_sms_title[$i]?></h4>
                <div class="scf_sms_img">
                    <textarea id="gp_sms_cont<?php echo $i; ?>" name="gp_sms_cont<?php echo $i; ?>" ONKEYUP="byte_check('gp_sms_cont<?php echo $i; ?>', 'byte<?php echo $i; ?>');"><?php echo $gp_default['gp_sms_cont'.$i]; ?></textarea>
                </div>
                <span id="byte<?php echo $i; ?>" class="scf_sms_cnt">0 / 80 바이트</span>
            </section>

            <script>
            byte_check('gp_sms_cont<?php echo $i; ?>', 'byte<?php echo $i; ?>');
            </script>
            <?php } ?>
        </div>
    </section>

</section>

<?php echo $frm_submit; ?>

</form>


<script type="text/javascript">

$(document).ready(function(){
	$(".btn_submit").click(function(){
		var idx = $(this).attr("idx");
		var mode = $(this).attr("mode");
		var dealer = $(this).attr("dealer");

		$("form[name='cntPayForm']").find("input[name='mode']").val(mode);
		$("form[name='cntPayForm']").find("input[name='idx']").val(idx);
		$("form[name='cntPayForm']").find("input[name='dealer']").val(dealer);

		if(mode == "modify"){
			if(confirm("수정하시겠습니까?")){
				$("form[name='cntPayForm']").submit();
			}
		}else if(mode == "wait"){
			if(confirm("집계하시겠습니까?")){
				$("form[name='cntPayForm']").submit();
			}

		}else if(mode == "end"){
			if(confirm("종료시 해당 날짜에 대한 1건이 저장 됩니다.\n종료하시겠습니까?")){
				$("form[name='cntPayForm']").submit();
			}
		}
	});
});

$(function(){
    $(".grouppur_date").datepicker({ changeMonth: true, changeYear: true, dateFormat: "yy-mm-dd", showButtonPanel: true, yearRange: "c-99:c+99", minDate: "-30d" });
});

function forderprintcheck(f)
{
    if (f.csv[0].checked || f.csv[1].checked)
    {
        f.target = "_top";
    }
    else
    {
        var win = window.open("", "winprint", "left=10,top=10,width=670,height=800,menubar=yes,toolbar=yes,scrollbars=yes");
        f.target = "winprint";
    }

    f.submit();
}
</script>

<?php
include_once (G5_ADMIN_PATH.'/admin.tail.php');
?>
