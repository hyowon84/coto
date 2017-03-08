<?php
$sub_menu = '400300';
include_once('./_common.php');

auth_check($auth[$sub_menu], "r");

$g5['title'] = '상품 복사';
include_once(G5_PATH.'/head.sub.php');
?>

<div class="new_win">
    <h1>상품 복사</h1>
    <form name="fgrouppurchasecopy">

    <div id="sit_copy">
        <label for="new_it_id">상품코드</label>
        <input type="text" name="new_gp_id" value="<?php echo time(); ?>" id="new_gp_id" class="frm_input" size="50">
    </div>

    <div class="btn_confirm01 btn_confirm">
      <input type="button" value="복사하기" class="btn_submit" onclick="_copy('grouppurchasecopyupdate.php?gp_id=<?php echo $gp_id; ?>&amp;ca_id=<?php echo $ca_id; ?>');">
      <button type="button" onclick="self.close();">창닫기</button>
    </div>

    </form>
</div>

<script>
// <![CDATA[
function _copy(link)
{
    var new_gp_id = document.getElementById('new_gp_id').value;
    var t_gp_id = new_gp_id.replace(/[A-Za-z0-9\-_]/g, "");
    if(t_gp_id.length > 0) {
        alert("상품코드는 영문자, 숫자, -, _ 만 사용할 수 있습니다.");
        return false;
    }
    opener.parent.location.href = encodeURI(link+'&new_gp_id='+new_gp_id);
    self.close();
}
// ]]>
</script>

<?php
include_once(G5_PATH.'/tail.sub.php');
?>