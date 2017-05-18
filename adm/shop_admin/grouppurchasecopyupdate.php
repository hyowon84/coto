<?php
$sub_menu = '400300';
include_once('./_common.php');

auth_check($auth[$sub_menu], "w");

if ($is_admin != "super")
    alert("최고관리자만 접근 가능합니다.");

if (!trim($gp_id))
	alert("복사할 상품코드가 없습니다.");

//$t_gp_id = preg_replace("/[A-Za-z0-9\-_]/", "", $new_gp_id);
if($t_gp_id)
    alert("상품코드는 영문자, 숫자, -, _ 만 사용할 수 있습니다.");

$row = sql_fetch(" select count(*) as cnt from {$g5['g5_shop_group_purchase_table']} where gp_id = '$new_gp_id' ");
if ($row['cnt'])
    alert('이미 존재하는 상품코드 입니다.');

$sql = " select * from {$g5['g5_shop_group_purchase_table']} where gp_id = '$gp_id' limit 1 ";
$cp = sql_fetch($sql);


// 상품테이블의 필드가 추가되어도 수정하지 않도록 필드명을 추출하여 insert 퀴리를 생성한다. (상품코드만 새로운것으로 대체)
$sql_common = "";
$fields = mysql_list_fields(G5_MYSQL_DB, $g5['g5_shop_group_purchase_table']);
$columns = mysql_num_fields($fields);
for ($i = 0; $i < $columns; $i++) {
  $fld = mysql_field_name($fields, $i);
  if ($fld != 'gp_id') {
      $sql_common .= " , $fld = '".addslashes($cp[$fld])."' ";
  }
}

$sql = " insert {$g5['g5_shop_group_purchase_table']}
			set gp_id = '$new_gp_id'
                $sql_common ";
sql_query($sql);


$reset_sql = "	UPDATE	{$g5['g5_shop_group_purchase_table']}		SET
													ac_yn = 'N',
													ac_code = '',
													ac_enddate = '',
													ac_qty = 0,
													ac_startprice = 0,
													ac_buyprice = 0
								WHERE		gp_id = '$new_gp_id' ";
sql_query($reset_sql);



// 선택/추가 옵션 copy
$opt_sql = " insert ignore into {$g5['g5_shop_group_purchase_option_table']} ( gp_id, po_num, po_sqty, po_eqty, po_cash_price, po_card_price, po_add_price )
                select ('$new_gp_id') as gp_id, po_num, po_sqty, po_eqty, po_cash_price, po_card_price, po_add_price
                    from {$g5['g5_shop_group_purchase_option_table']}
                    where gp_id = '$gp_id'
                    order by gp_id asc ";
sql_query($opt_sql);

// html 에디터로 첨부된 이미지 파일 복사
if($cp['gp_explan']) {
    $matchs = get_editor_image($cp['gp_explan']);

    // 파일의 경로를 얻어 복사
    for($i=0;$i<count($matchs[1]);$i++) {
        $p = parse_url($matchs[1][$i]);
        if(strpos($p['path'], "/data/") != 0)
            $src_path = preg_replace("/^\/.*\/data/", "/data", $p['path']);
        else
            $src_path = $p['path'];

        $srcfile = G5_PATH.$src_path;
        $dstfile = preg_replace("/\.([^\.]+)$/", "_".$new_gp_id.".\\1", $srcfile);

        if(is_file($srcfile)) {
            copy($srcfile, $dstfile);

            $newfile = preg_replace("/\.([^\.]+)$/", "_".$new_gp_id.".\\1", $matchs[1][$i]);
            $cp['gp_explan'] = str_replace($matchs[1][$i], $newfile, $cp['gp_explan']);
        }
    }

    $sql = " update {$g5['g5_shop_group_purchase_table']} set gp_explan = '".addslashes($cp['gp_explan'])."' where gp_id = '$new_gp_id' ";
    sql_query($sql);
	
}

$sql = " update {$g5['g5_shop_group_purchase_table']}
            set gp_img='".$cp[gp_img]."'
            where gp_id = '$new_gp_id' ";
sql_query($sql);

$qstr = "ca_id=$ca_id&amp;sfl=$sfl&amp;sca=$sca&amp;page=$page&amp;stx=".urlencode($stx)."&amp;save_stx=".urlencode($save_stx);

goto_url("grouppurchaselist.php?$qstr");
?>