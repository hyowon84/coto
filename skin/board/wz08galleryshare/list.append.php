<?
include_once("./_common.php");
?>
<link rel="stylesheet" href="<?php echo $board_skin_url?>/wetoz.board.css?130625" type='text/css' />

<?

$sop = strtolower($sop);
if ($sop != "and" && $sop != "or")
    $sop = "and";

// 분류 선택 또는 검색어가 있다면
$stx = trim($stx);
if ($sca || $stx)
{
    $sql_search = get_sql_search($sca, $sfl, $stx, $sop);

    // 가장 작은 번호를 얻어서 변수에 저장 (하단의 페이징에서 사용)
    $sql = " select MIN(wr_num) as min_wr_num from $write_table ";
    $row = sql_fetch($sql);
    $min_spt = $row[min_wr_num];

    if (!$spt) $spt = $min_spt;

    $sql_search .= " and (wr_num between '".$spt."' and '".($spt + $config[cf_search_part])."') ";

    // 원글만 얻는다. (코멘트의 내용도 검색하기 위함)
    $sql = " select distinct wr_parent from $write_table where $sql_search ";
    $result = sql_query($sql);
    $total_count = mysql_num_rows($result);
}
else
{
    $sql_search = "";

    $total_count = $board[bo_count_write];
}

$total_page  = ceil($total_count / $board[bo_page_rows]);  // 전체 페이지 계산
if (!$page) { $page = 1; } // 페이지가 없으면 첫 페이지 (1 페이지)
$from_record = ($page - 1) * $board[bo_page_rows]; // 시작 열을 구함


// 정렬
// 인덱스 필드가 아니면 정렬에 사용하지 않음
//if (!$sst || ($sst && !(strstr($sst, 'wr_id') || strstr($sst, "wr_datetime")))) {
if (!$sst)
{
    if ($board[bo_sort_field])
        $sst = $board[bo_sort_field];
    else
        $sst  = "wr_num, wr_reply";
    $sod = "";
}
else {
    // 게시물 리스트의 정렬 대상 필드가 아니라면 공백으로 (nasca 님 09.06.16)
    // 리스트에서 다른 필드로 정렬을 하려면 아래의 코드에 해당 필드를 추가하세요.
    // $sst = preg_match("/^(wr_subject|wr_datetime|wr_hit|wr_good|wr_nogood)$/i", $sst) ? $sst : "";
    $sst = preg_match("/^(wr_datetime|wr_hit|wr_good|wr_nogood)$/i", $sst) ? $sst : "";
}

if ($sst)
    $sql_order = " order by $sst $sod ";

if ($sca || $stx)
{
    $sql = " select distinct wr_parent from $write_table where $sql_search $sql_order limit $from_record, $board[bo_page_rows] ";
}
else
{
    $sql = " select * from $write_table where wr_is_comment = 0 $sql_order limit $from_record, $board[bo_page_rows] ";
}
$result = sql_query($sql);

// 년도 2자리
$today2 = $g4[time_ymd];

$list = array();
$i = 0;

if (!$sca && !$stx)
{
    $arr_notice = explode("\n", trim($board[bo_notice]));
    for ($k=0; $k<count($arr_notice); $k++)
    {
        if (trim($arr_notice[$k])=='') continue;

        $row = sql_fetch(" select * from $write_table where wr_id = '$arr_notice[$k]' ");

        if (!$row[wr_id]) continue;

        $list[$i] = get_list($row, $board, $board_skin_path, $board[bo_subject_len]);
        $list[$i][is_notice] = true;

        $i++;
    }
}

$k = 0;

while ($row = sql_fetch_array($result))
{
    // 검색일 경우 wr_id만 얻었으므로 다시 한행을 얻는다
    if ($sca || $stx)
        $row = sql_fetch(" select * from $write_table where wr_id = '$row[wr_parent]' ");

    $list[$i] = get_list($row, $board, $board_skin_path, $board[bo_subject_len]);
    if (strstr($sfl, "subject"))
        $list[$i][subject] = search_font($stx, $list[$i][subject]);
    $list[$i][is_notice] = false;
    //$list[$i][num] = number_format($total_count - ($page - 1) * $board[bo_page_rows] - $k);
    $list[$i][num] = $total_count - ($page - 1) * $board[bo_page_rows] - $k;

    $i++;
    $k++;
}


$nobr_begin = $nobr_end = "";
if (preg_match("/gecko|firefox/i", $_SERVER['HTTP_USER_AGENT'])) {
    $nobr_begin = "<nobr style='display:block; overflow:hidden;'>";
    $nobr_end   = "</nobr>";
}


for ($i=0; $i<count($list); $i++) { 
		
	$subject = $list[$i][subject];
	$wr_id = $list[$i][wr_id];

	$checkbox = "";
	//if ($is_checkbox)
	$checkbox = "<input type=checkbox name=chk_wr_id[] value='{$list[$i][wr_id]}'>";

	$mem_row = sql_fetch("select * from {$g5[member_table]} where mb_id='".$list[$i][mb_id]."' ");
?>

	<li class="sortbox">
		<div style="height:70px;background:#efefef;font-style:italic">
			<p style="font-size:12px;margin-left:25px;font-weight:bold;padding:15px 0 5px 0">Photo by <?=$mem_row[mb_nick]?></p>
			<p style="font-size:18px;margin-left:25px">
				<span><?php echo $nobr_begin.$checkbox.$subject.$nobr_end;?></span>
				<?if($board['bo_write_level'] <= $member['mb_level']){?>
					<span style="float:right;margin:-20px 20px 0 0;"><a href="<?php echo $list[$i][href]; ?>">수정</a></span>
					<?if($member[mb_id] == "admin"){?>
					<span style="float:right;margin:-20px 20px 0 0;"><a href="javascript:void(0);" onclick="bestshot_bn('<?=$wr_id?>')">베스트샷</a></span>
					<?}?>
				<?}?>
			</p>
		</div>
		<div style="height:35px;background:#fff;">
			<p style="font-size:14px;margin-left:25px;font-weight:bold;padding:7px 0 7px 0"><?=cut_str($list[$i][wr_content], 50)?></p>
		</div>
		<div class="iner">
			<!--<a href="<?php// echo $list[$i][href]; ?>">-->
			<figure>
				<a href="javascript:void(0)" id="show-button<?=$wr_id?>" onclick="show_button('<?=$wr_id?>')">
				<?php
			if ($list[$i]['is_notice']) { // 공지사항  ?>
				<strong style="width:<?php echo $board['bo_gallery_width'] ?>px;height:<?php echo $board['bo_gallery_height'] ?>px">공지</strong>
			<?php } else {
				//$thumb = get_list_thumbnail($board['bo_table'], $list[$i]['wr_id'], $board['bo_gallery_width'], $board['bo_gallery_height']);
				$img_row = sql_fetch("select * from {$g5['board_file_table']} where bo_table='".$board['bo_table']."' and wr_id='".$list[$i]['wr_id']."' and bf_no='0' ");

				//if($thumb['src']) {
				if($img_row) {
					//$img_content = '<img src="'.$thumb['src'].'" alt="'.$thumb['alt'].'" width="'.$board['bo_gallery_width'].'" height="'.$board['bo_gallery_height'].'">';
					$img_content = '<img src="'.G5_URL.'/data/file/'.$board['bo_table'].'/'.$img_row[bf_file].'" alt="'.$thumb['alt'].'" width="'.$board['bo_gallery_width'].'" height="'.$board['bo_gallery_height'].'">';
				} else {
					$img_content = '<span style="width:'.$board['bo_gallery_width'].'px;height:'.$board['bo_gallery_height'].'px">no image</span>';
				}

				echo $img_content;
			}
			 ?>
				</a>
				<figcaption class="caption">
				
					<div style="position:relative;padding:10px 40px 5px 40px;">
						<span onclick="recomm_bn('<?=$wr_id?>')" style="float:left;border:1px #cfcfcf solid;padding:7px;cursor:pointer;">
							<img src="<?=G5_URL?>/img/heart_ico.png">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
							<span class="recomm_bn<?=$wr_id?>">
							<?
							if($list[$i][wr_1]){
								echo $list[$i][wr_1];
							}else{
								echo 0;
							}
							?>
							</span>
						</span>
						<span style="float:left;padding:5px 0 0 10px;">
						<?php
						include(G5_SNS_PATH."/list.sns.skin.php");
						?>
						</span>
					</div>
					<div class="cl" style="position:relative;padding:5px 40px 10px 40px;">
						<?php
						// 코멘트 입출력
						include('../../../bbs/list_comment.php');
						 ?>
					</div>
				</figcaption>
			</figure>
			

			<ul id="interactive-image-list<?=$wr_id?>" style="display:none;">
			<?
			$img_res1 = sql_query("select * from {$g5['board_file_table']} where bo_table='".$board['bo_table']."' and wr_id='".$list[$i]['wr_id']."' order by bf_no asc ");
			for($k = 0; $img_row1 = mysql_fetch_array($img_res1); $k++){
			?>
				<li class='zoom ex<?=$wr_id?>'><a href="<?=G5_URL?>/data/file/<?=$board['bo_table']?>/<?=$img_row1[bf_file]?>" title="<?=$subject?>">&nbsp;</a></li>
			<?}?>
			</ul>
		</div>
	</li><!-- sortbox -->

<?php
}
?>