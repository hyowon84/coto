<h2 class="myPage" style="color:#56ccc8;font-size:22px;font-weight:bold;">
	마이페이지
	<span class="grade">
	<span style="margin:0 0 0 30px;color:#545454;font-size:14px;font-style:italic;"><?=$member[mb_name]?>[<?=$member[mb_nick]?>]</span><span style="color:#545454;font-size:14px;font-style:italic;font-weight:normal;"> 님의 VIP멤버십 등급은 </span><span style="color:#ff4e00;font-size:14px;"><?=$member[mb_level]?></span><span style="color:#545454;font-size:14px;font-style:italic;font-weight:normal;"> 입니다.</span>
	</span>
</h2>

<div id="my_nav">
	<ul>
		<li><div class="title">할인쿠폰</div><div class="des">0장</div></li>
		<li class="line1"></li>
		<li class="line2"></li>
		<li><div class="title">포인트</div><div class="des"><?=number_format($member[mb_point])?>P</div></li>
		<li class="line1"></li>
		<li class="line2"></li>
		<li><div class="title">배송중</div><div class="des"><?=od_status_cnt("배송중")?>개</div></li>
		<li class="line1"></li>
		<li class="line2"></li>
		<li><div class="title">배송완료</div><div class="des"><?=od_status_cnt("배송완료")?>개</div></li>
	</ul>
</div>
<div id="my_tab">
	<ul>
		<li <?if(stristr($_SERVER[REQUEST_URI], "orderinquiry") || stristr($_SERVER[REQUEST_URI], "itemuselist")){?> class="on"<?}?> onclick="goto_url('<?=G5_SHOP_URL?>/orderinquiry.php');">주문배송조회</li>
<!--		<li>할인정보</li>-->
		<li <?if(strpos($_SERVER[REQUEST_URI], "wishlist") !== false || strpos($_SERVER[REQUEST_URI], "wishlist_gp") !== false){?> class="on"<?}?> onclick="goto_url('<?=G5_SHOP_URL?>/wishlist_gp.php');">위시리스트</li>
		<li <?if(strpos($_SERVER[REQUEST_URI], "itemqalist") !== false){?> class="on"<?}?> onclick="goto_url('<?=G5_SHOP_URL?>/gpitemqalist.php');">문의사항</li>
		<li <?if(strpos($_SERVER[REQUEST_URI], "member_confirm1") !== false){?> class="on"<?}?> onclick="goto_url('<?=G5_BBS_URL?>/member_confirm1.php?url=/bbs/register_form.php');">회원정보</li>
	</ul>
</div>