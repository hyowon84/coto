<?
include_once('./_common.php');

$num = $_POST[num];
$num1 = $_POST[num1];
$getcon = $_POST[getcon];					//일반환율

$num_res_arr = explode(".", round(($num - $num1) * 0.2 + $num1, 2));
if($num_res_arr[1]){
	$num_res_arr1 = $num_res_arr[1];
}else{
	$num_res_arr1 = "00";
}

$num_res = $num_res_arr[0].".".$num_res_arr1;	//공동구매환율

$num_res1 = round($num_res - $getcon, 2);	//증감

if($num_res1 > 0){
	$style = "color='red'";
}else{
	$style = "color='blue'";
}

echo json_encode(array('getcon' => $getcon, 'num_res' => "<font ".$style.">".$num_res."</font>", 'num_res1' => "<font ".$style.">".$num_res1."</font>"));
?>