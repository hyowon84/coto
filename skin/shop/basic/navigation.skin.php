<?php
if (!defined("_GNUBOARD_")) exit; // 개별 페이지 접근 불가

if ($ca_id)
{
    $navigation = $bar = "";
    $len = strlen($ca_id) / 2;
    for ($i=1; $i<=$len; $i++)
    {
        $code = substr($ca_id,0,$i*2);

        $sql = " select ca_name from {$g5['g5_shop_category_table']} where ca_id = '$code' ";
        $row = sql_fetch($sql);

        $sct_here = '';
        if ($ca_id == $code) // 현재 분류와 일치하면
            $sct_here = 'sct_here';

        if ($i != $len) // 현재 위치의 마지막 단계가 아니라면
            $sct_bg = 'sct_bg';
        else $sct_bg = '';

		if(strpos($_SERVER[REQUEST_URI], "gplist") !== false || strpos($_SERVER[REQUEST_URI], "grouppurchase") !== false){
			$navigation .= $bar.'<a href="./gplist.php?ca_id='.$code.'" class="'.$sct_here.' '.$sct_bg.'">'.$row['ca_name'].'</a>';
		}else{
			$navigation .= $bar.'<a href="./list.php?ca_id='.$code.'" class="'.$sct_here.' '.$sct_bg.'">'.$row['ca_name'].'</a>';
		}
		$ca_name = $row['ca_name'];
    }
}
else
    $navigation = $g5['title'];

//if ($it_id) $navigation .= " > $it[it_name]";
?>

<link rel="stylesheet" href="<?php echo G5_SHOP_SKIN_URL; ?>/style.css">



<div id="sub_title2">
    <li><?php echo $navigation; ?></li>	
	<li>></li>
    <li>홈</li>
</div>

