<?php
if (!defined("_GNUBOARD_")) exit; // 개별 페이지 접근 불가

if ($ca_id)
{
    $str = $bar = "";
    $len = strlen($_GET[ca_id]) / 2;
    for ($i=1; $i<=$len; $i++)
    {
        $code = substr($_GET[ca_id],0,$i*2);

        $sql = " select ca_name from {$g5['g5_shop_category_table']} where ca_id = '$code' ";
        $row = sql_fetch($sql);

        $sct_here = '';
        if ($ca_id == $code) // 현재 분류와 일치하면
            $sct_here = 'sct_here';

        if ($i != $len) // 현재 위치의 마지막 단계가 아니라면
            $sct_bg = 'sct_bg';
        else $sct_bg = '';

        $str .= $bar.'<a href="./list.php?ca_id='.$code.'" class="'.$sct_here.' '.$sct_bg.'">'.$row['ca_name'].'</a>';
    }
}
else
    $str = $g5['title'];

//if ($it_id) $str .= " > $it[it_name]";
?>

<link rel="stylesheet" href="<?php echo G5_MSHOP_SKIN_URL; ?>/style.css">

<span id="sct_location">
    <a href='<?php echo G5_SHOP_URL; ?>/' class="sct_bg">홈</a>
    <?php echo $str; ?>
</span>
