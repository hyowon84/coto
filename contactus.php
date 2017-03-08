<?php
define('_INDEX_', true);
include_once('./_common.php');

// 초기화면 파일 경로 지정 : 이 코드는 가능한 삭제하지 마십시오.
if ($config['cf_include_index']) {
    if (!@include_once($config['cf_include_index'])) {
        die('기본환경 설정에서 초기화면 파일 경로가 잘못 설정되어 있습니다.');
    }
    return; // 이 코드의 아래는 실행을 하지 않습니다.
}

// 루트 index를 쇼핑몰 index 설정했을 때
if(isset($default['de_root_index_use']) && $default['de_root_index_use']) {
    require_once(G5_SHOP_PATH.'/index.php');
    return;
}

if (G5_IS_MOBILE) {
    include_once(G5_MOBILE_PATH.'/index.php');
    return;
}

include_once('./_head.php');
?>

<style>
#sub_title2 {float:left;position:relative;line-height:25px ;padding-top:20px;padding-bottom:10px; width:800px;border-bottom:1px solid #000;margin-bottom:15px;list-style:none}
#sub_title2 ul {list-style:none;float:right;padding:0;margin:0;padding-right:10px }
#sub_title2 li {float:left;position:relative;line-height:25px ;padding-left:3px;font-weight:bold }
#sub_title2 span { padding-left:15px;font-size:20px;color:#000;font-weight:bold;font-family:dotum}


#sub_cate3 ul {margin-bottom:10px;padding-left:1px;width:800px;zoom:1}
#sub_cate3 ul:after {display:block;visibility:hidden;clear:both;content:""}
#sub_cate3 li {float:left;margin-bottom:-1px;list-style:none}
#sub_cate3 a {display:block;position:relative;margin-left:-1px;padding:6px 0 5px;width:150px;border:1px solid #ddd;background:#fff;color:#000;text-align:center;letter-spacing:-0.1em;line-height:1.7em;cursor:pointer}
#sub_cate3 a:focus, #sub_cate3 a:hover, #sub_cate3 a.sub_cate3_on {text-decoration:none;background:#213652;color:#fff}
#sub_cate3 #sub_cate_on3 {z-index:2;border:1px solid #565e60;background:#213652;color:#fff;font-weight:bold}

textarea {display:block;margin-bottom:10px;padding:20px;width:750px;height:700px;border:1px solid #e9e9e9;background:#f7f7f7;margin-left:0px;margin-top:20px}
span {margin:0px 0 20px 20px;display:inline-block;line-height:1.4em}

#company .mail{}
#company .mail li{margin:0 0 20px 0;}
#company .mail .cu_name{width:260px;height:37px;padding:0 0 0 10px;}
#company .mail .cu_email{width:260px;height:37px;padding:0 0 0 10px;}
#company .mail .cu_message{width:260px;height:125px;background:#fff;padding:10px 0 0 10px;}
#company .mail .con_send{cursor:pointer;}
</style>



<div id="aside2"></div>



<div id="sub_title2" style="border:0;padding:0px;margin:0 0 0 -30px"><span>Contact Us</span>

</div>


<div id="company">

		
		<li class="sub_content" style="background:#fff;width:820px"> 	
			<div style="padding:35px">
				<div style="padding:0px;font-size:12px;font-weight:bold">
					<ul style="margin:0;padding:0">

					<li style="list-stlye:none;position:relative;float:left;width:315px;padding:0 0 0 60px;">
					
					<form name="fcu_mail" id="fcu_mail" method="POST">
						<div class="mail">
							<ul>
								<li><input type="text" name="cu_name" value="Your Name" class="cu_name"></li>
							</ul>
							<ul>
								<li style="margin:0px;"><input type="text" name="cu_email" value="Your Email" class="cu_email"></li>
							</ul>
							<ul>
								<li>
									<textarea name="cu_message" class="cu_message">Your Message</textarea>
								</li>
							</ul>
							<ul>
								<li><img src="<?php G5_URL ?>/img/con_send.png" class="con_send"></li>
							</ul>
						</div>
					</form>
					
					</li>




					<li style="list-stlye:none;position:relative;float:left;width:375px">
						<span style="font-size:20px;">Address</span>
						<span style="font-size:15px;font-weight:normal">
								서울특별시 강남구 밤고개로14길 13-34(자곡동 274)
								#274, Jagok-dong, Gangnam-gu, Seoul, Republic of Korea
						</span>

						<span style="font-size:15px;font-weight:normal;width:375px">
								T: 070 4323 6999,6998<br>
								F: 070 8230 0777						
						</span>

						<span style="font-size:15px;font-weight:normal">
								E: <font style="color:#56ccc8">info@coinstoday.co.kr</font>
						</span>
						<span>
							<img src="<?php G5_URL ?>/img/con_sns.jpg">
						</span>

					</li>
					</ul>

				</div>
				<div style="padding:0px ; margin-left:-35px;font-size:12px;font-weight:bold">
					<iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3166.0501974092217!2d127.10324394377417!3d37.48314179121497!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x357ca5ddc74016ab%3A0x93a4c5fee8e6512f!2z7ISc7Jq47Yq567OE7IucIOqwleuCqOq1rCDsnpDqs6Hrj5kgMjc0!5e0!3m2!1sko!2skr!4v1487228701241" width="600" height="450" frameborder="0" style="border:0" allowfullscreen></iframe>
				</div>
				<div style="padding:0px;font-size:12px;font-weight:bold">
					<div style="text-align:center;font-size:23px;font-weight:bold;padding:20px 0 20px 0"> Our Team </div>
					<div style="color:#4b4b4b;text-align:center;font-size:11px;font-weight:bold;padding:0px 0 30px 0;line-height:1.5em">
						코인즈투데이의 담당자별 연락처입니다.<br>
						문의 / 상담을 원하실 경우 연락 주시면, 친절히 답변해 드리겠습니다.
					</div>
					cccc
				</div>

			</div>			
		</li>




</div>


       

<script type="text/javascript">

$(document).ready(function(){
	$("input:text[name='cu_name']").focusin(function(){
		$("input[name='cu_name']").val("");
	});
	$("input:text[name='cu_name']").focusout(function(){
		if($("input[name='cu_name']").val() == "Your Name"){
			$("input[name='cu_name']").val("Your Name");
		}
	});

	$("input:text[name='cu_email']").focusin(function(){
		$("input[name='cu_email']").val("");
	});
	$("input:text[name='cu_email']").focusout(function(){
		if($("input[name='cu_email']").val() == "Your Email"){
			$("input[name='cu_email']").val("Your Email");
		}
	});

	$("textarea[name='cu_message']").focusin(function(){
		$("textarea[name='cu_message']").val("");
	});
	$("textarea[name='cu_message']").focusout(function(){
		if($("textarea[name='cu_message']").val() == "Your Massege"){
			$("textarea[name='cu_message']").val("Your Massege");
		}
	});

	$(".con_send").click(function(){
		if($("input:text[name='cu_name']").val() == "" || $("input:text[name='cu_name']").val() == "Your Name"){
			alert("Name을 입력해주세요.");
			$("input:text[name='cu_name']").focus();
			return false;
		}
		if($("input:text[name='cu_email']").val() == "" || $("input:text[name='cu_email']").val() == "Your Email"){
			alert("Email을 입력해주세요.");
			$("input:text[name='cu_email']").focus();
			return false;
		}
		if($("textarea[name='cu_message']").val() == "" || $("textarea[name='cu_massege']").val() == "Your Message"){
			alert("Name을 입력해주세요.");
			$("textarea[name='cu_message']").focus();
			return false;
		}
		$("form[name='fcu_mail']").attr("action", "./contactus_update.php").submit();
	});

});

</script>

		




<?php
include_once('./_tail.php');
?>