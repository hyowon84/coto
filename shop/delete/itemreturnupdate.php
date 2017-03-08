<?
include_once('./_common.php');

if($_POST[HTTP_CHK] != "CHK_OK") alert("잘못된 접근 경로입니다.");

$od_id = "";
$it_id = "";

for($i = 0; $i < count($_POST[od_id]); $i++){

	sql_query("
		update {$g5['g5_shop_cart_table']} set
		re_status='y',
		ex_reason='".$_POST[re_status]."',
		ex_content='".$_POST[re_con]."',
		ex_addr='".$_POST[my_ex_addr]."',
		ex_name='".$_POST[my_ex_name]."',
		ex_tel='".$_POST[my_ex_tel]."',
		ex_hp='".$_POST[my_ex_hp]."',
		ex_content1='".$_POST[ex_content1]."'
		where od_id='".$_POST[od_id][$i]."'
		and it_id='".$_POST[it_id][$i]."'
	");

	$od_id .= $_POST[od_id][$i]."|";
	$it_id .= $_POST[it_id][$i]."|";
}

$od_id = substr($od_id, 0, strlen($od_id)-1);
$it_id = substr($it_id, 0, strlen($it_id)-1);

alert("반품요청이 완료되었습니다.", G5_SHOP_URL."/itemreturn_success.php?od_id=".$od_id."&it_id=".$it_id."&cate1=".$cate1);
?>