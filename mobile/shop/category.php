<?php
include_once('./_common.php');

$g5['title'] = '카테고리';
include_once(G5_PATH.'/head.sub.php');

$ca = $_GET['ca'];

if($ca) {
    $ca_len = strlen($ca) + 2;
    $sql_where = " where ca_id like '$ca%' and length(ca_id) = $ca_len ";
} else {
    $sql_where = " where length(ca_id) = '2' ";
}

$sql = " select ca_id, ca_name from {$g5['g5_shop_category_table']}
          $sql_where
            and ca_use = '1'
          order by ca_id ";
$result = sql_query($sql);
?>

<style>
#sct_win {}
#sct_win h1 {padding:15px 0;text-align:center}
#sct_win_nav {margin:0 0 20px}
#sct_win_nav h2 {position:absolute;font-size:0;text-indent:-9999em;line-height:0;overflow:hidden}
#sct_win_nav ul {border-top:1px solid #e9e9e9}
#sct_win_nav li {position:relative;border-bottom:1px solid #e9e9e9;background:#f5f6fa}
#sct_win_nav a {display:block;padding:0.5em 1em;text-decoration:none}
.sct_ct_view {font-weight:bold}
.sct_list_view {position:absolute;top:0;right:0;width:4em;background:#333;color:#fff !important;text-align:center}

#sct_win_empty {margin:0 0 1em;padding:5em 0;border-top:1px solid #e9e9e9;border-bottom:1px solid #e9e9e9;background:#f5f6fa;text-align:center}

#sct_win .win_btn {text-align:center}
#sct_win .win_btn button {display:inline-block;padding:0 10px;height:2.5em;border:0;background:#666;color:#fff;text-decoration:none;line-height:2.5em}
</style>

<div id="sct_win">

    <h1><?php echo $config['cf_title']; ?> 카테고리</h1>

    <?php
    for($i=0; $row=sql_fetch_array($result); $i++) {
        if($i == 0)
            echo '<nav id="sct_win_nav"><h2>카테고리 목록</h2><ul>';

        $ca_href = G5_SHOP_URL.'/category.php?ca='.$row['ca_id'];
        $list_href = G5_SHOP_URL.'/list.php?ca_id='.$row['ca_id'];
    ?>
        <li>
            <a href="<?php echo $ca_href; ?>" class="sct_ct_view"><?php echo $row['ca_name']; ?></a>
            <a href="<?php echo $list_href; ?>" class="sct_list_view">상품보기</a>
        </li>
    <?php
    }

    if($i > 0)
        echo '</ul></nav>';

    if($i ==0) {
        echo '<p id="sct_win_empty">하위 분류가 없습니다.</p>';
    }
    ?>

    <div class="win_btn">
        <?php if ($i == 0 || $ca) { ?><button onclick="javascript:history.back(-1);" class="btn02">돌아가기</button><?php } ?>
        <button onclick="javascript:window.close();">창닫기</button>
    </div>

</div>

<script>
$(function() {
    $(".sct_list_view").click(function() {
        window.opener.location = $(this).attr("href");
        window.close();
        return false;
    });
});
</script>

<?php
include_once(G5_PATH.'/tail.sub.php');
?>