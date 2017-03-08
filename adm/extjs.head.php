<?php
if (!defined('_GNUBOARD_')) exit;

$begin_time = get_microtime();

include_once(G5_PATH.'/head.sub.php');

function print_menu1($key, $no)
{
	global $menu;
	$str = print_menu2($key, $no);
	return $str;
}

function print_menu2($key, $no)
{
	global $menu, $auth_menu, $is_admin, $auth, $g5;

	$str .= "<ul class=\"gnb_2dul\">";
	for($i=1; $i<count($menu[$key]); $i++)
	{
		$sub_key = $i;

		if ($is_admin != 'super' && (!array_key_exists($menu[$key][$i][0],$auth) || !strstr($auth[$menu[$key][$i][0]], 'r')))
			continue;

		if ($menu[$key][$i][4] == 1 && $gnb_grp_style == false) $gnb_grp_div = 'gnb_grp_div';
		else if ($menu[$key][$i][4] != 1 && $gnb_grp_style == true) $gnb_grp_div = 'gnb_grp_div';
		else $gnb_grp_div = '';

		if ($menu[$key][$i][4] == 1) $gnb_grp_style = 'gnb_grp_style';
		else $gnb_grp_style = '';

		$str .= '<li class="gnb_2dli"><a href="'.$menu[$key][$i][2].'" class="gnb_2da '.$gnb_grp_style.' '.$gnb_grp_div.'">'.$menu[$key][$i][1].'</a>';

		if($key=="menu500")$sub_key+=1;
		if(is_file(G5_ADMIN_PATH."/admin.".$key.".sub".$sub_key.".php")){
			$str .= '<ul class="gnb_2dul" style="top:0;border-top:1px #999 solid;">';
			include(G5_ADMIN_PATH."/admin.".$key.".sub".$sub_key.".php");
			$str .= '</ul>';
		}
		$str .= '</li>';

		$auth_menu[$menu[$key][$i][0]] = $menu[$key][$i][1];
	}
	$str .= "</ul>";

	return $str;
}
?>


<link href="<?=G5_URL?>/css/dropdown.css" media="screen" rel="stylesheet" type="text/css" />
<link href="<?=G5_URL?>/css/default.ultimate.css" media="screen" rel="stylesheet" type="text/css" />
<link rel='stylesheet' href='<?=G5_CSS_URL?>/Nwagon.css' type='text/css'>
<script src='<?=G5_JS_URL?>/Nwagon.js'></script>


<!--[if lt IE 9]>
	<script src="http://css3-mediaqueries-js.googlecode.com/svn/trunk/css3-mediaqueries.js"></script>
<![endif]-->

<!--[if lte IE 8]>
<script src="<?=G5_JS_URL?>/html5.js"></script>
<![endif]-->

<!--[if lt IE 7]>
<script type="text/javascript" src="<?=G5_URL?>/js/jquery.dropdown.js"></script>
<![endif]-->

<script>
var tempX = 0;
var tempY = 0;

function imageview(id, w, h)
{

	menu(id);

	var el_id = document.getElementById(id);

	//submenu = eval(name+".style");
	submenu = el_id.style;
	submenu.left = tempX - ( w + 11 );
	submenu.top  = tempY - ( h / 2 );

	selectBoxVisible();

	if (el_id.style.display != 'none')
		selectBoxHidden(id);
}
</script>

<div id="to_content"><a href="#container">본문 바로가기</a></div>



<style type="text/css" media="print">
.noprint { display:none; }
</style>

<style>
<?
$sql = "SELECT	*
				FROM		comcode CC
				WHERE		1=1
				AND			CC.ctype	IN ('clayorder','gpinfo','invoice','bankdb')
				AND			CC.col		IN ('stats','bank_type')
				ORDER BY CC.ctype ASC, CC.col ASC, CC.order ASC
";
$result = sql_query($sql);

while( $code = mysql_fetch_array($result)) {
	echo ".".$code[ctype]."_".$code[col]."_".$code[code]." {	background-color:$code[bgcolor]; } \r\n";
	echo ".".$code[ctype]."_".$code[col]."_".$code[code].":hover {	background-color:white; } \r\n";
	echo ".x-grid-item-selected .".$code[ctype]."_".$code[col]."_".$code[code]." { background-color:white; !important } \r\n";
}
?>
</style>

<div id="hd" class='noprint'>
	<div id="hd_wrap"  class='noprint'>
		<!--<h1><?php echo $config['cf_title'] ?></h1>-->

		<div id="logo"><a href="<?=G5_ADMIN_URL?>/shop_admin/"><img src="<?=G5_ADMIN_URL?>/img/logo_admin.png" alt="<?php echo $config['cf_title'] ?> 관리자"></a></div>

		<ul id="tnb">
			<li><a href="<?=G5_ADMIN_URL?>/member_form.php?w=u&amp;mb_id=<?php echo $member['mb_id'] ?>">관리자정보</a></li>
			<li><a href="<?=G5_ADMIN_URL?>/config_form.php">기본환경</a></li>
			<!--<li><a href="<?=G5_URL?>/">커뮤니티</a></li>-->
			<?php if(defined('G5_USE_SHOP')) { ?>
			<li><a href="<?=G5_ADMIN_URL?>/shop_admin/configform.php">쇼핑몰환경</a></li>
			<!--<li><a href="<?=G5_SHOP_URL?>/">쇼핑몰</a></li>-->
			<?php } ?>
			<!--<li id="tnb_logout"><a href="<?=G5_BBS_URL?>/logout.php">로그아웃</a></li>-->
		</ul>

		<ul id="gnb" class="dropdown dropdown-horizontal">
			<h2>관리자 주메뉴</h2>
			<script>$('#gnb').addClass('gnb_js');</script>
			<?php
			//$gnb_str = "<ul id=\"gnb_1dul\">";
			foreach($amenu as $key=>$value) {
				$href1 = $href2 = '';
				if ($menu['menu'.$key][0][2]) {
					$href1 = '<a href="'.$menu['menu'.$key][0][2].'" class="gnb_1da">';
					$href2 = '</a>';
				} else {
					continue;
				}
				$current_class = "";
				if (isset($sub_menu) && (substr($sub_menu, 0, 2) == substr($menu['menu'.$key][0][0], 0, 2)))
					$current_class = " gnb_1dli_air";
				$gnb_str .= '<li class="gnb_1dli">'.PHP_EOL;
				$gnb_str .=  $href1 . $menu['menu'.$key][0][1] . $href2;
				$gnb_str .=  print_menu1('menu'.$key, 1);
				$gnb_str .=  "</li>";
			}
			//$gnb_str .= "</ul>";
			echo $gnb_str;
			?>
		</ul>

	</div>
</div>
