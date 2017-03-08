<?
include_once('./_common.php');

if($_POST[sch_val]){

	$res = sql_query("select * from g5_shop_gphit where sch_text='".$_POST[sch_val]."' ");
	$row = mysql_fetch_array($res);

	if($row[sch_text]){
		$cnt = $row[sch_cnt] + 1;
		sql_query("
		update g5_shop_gphit set
		sch_cnt='$cnt'
		where sch_text='".$_POST[sch_val]."'
		");
	}else{
		sql_query("
		insert into g5_shop_gphit set
		sch_text='".$_POST[sch_val]."',
		sch_cnt='0',
		date='".strtotime("now")."'
		");
	}
}
?>