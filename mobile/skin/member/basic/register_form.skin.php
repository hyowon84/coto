<?
if (!defined('_GNUBOARD_')) exit; // 개별 페이지 접근 불가
?>

<link rel="stylesheet" href="<? echo $member_skin_url ?>/style.css">

<div class="mbskin">
    <script src="<? echo G5_JS_URL ?>/jquery.register_form.js"></script>
    <? if($config['cf_cert_use'] && ($config['cf_cert_ipin'] || $config['cf_cert_hp'])) { ?>
        <script src="<? echo G5_JS_URL ?>/certify.js"></script>
    <? } ?>

    <form name="fregisterform" id="fregisterform" action="<? echo $register_action_url ?>" onsubmit="return fregisterform_submit(this);" method="post" enctype="multipart/form-data" autocomplete="off">
        <input type="hidden" name="w" value="<? echo $w ?>">
        <input type="hidden" name="url" value="<? echo $urlencode ?>">
        <input type="hidden" name="agree" value="<? echo $agree ?>">
        <input type="hidden" name="agree2" value="<? echo $agree2 ?>">
        <input type="hidden" name="cert_type" value="<? echo $member['mb_certify']; ?>">
        <? if (isset($member['mb_sex'])) { ?><input type="hidden" name="mb_sex" value="<? echo $member['mb_sex'] ?>"><? } ?>

        <? if(!$member['mb_id']) { ?>
        <input type="hidden" name="id_hidden" value="">
        <? } ?>

        <h1>회원가입</h1>
        <h2>사이트 이용정보 입력</h2>
        <div class="tbl_frm01 tbl_wrap">
            <label for="reg_mb_id" class="sound_only">아이디<strong class="sound_only">필수</strong></label>
            <? if(!$member['mb_id']) { ?>
            <button type="button" id="id_check" class="btn_frmline">중복확인</button>
            <? } ?>
            <input type="email" name="mb_id" placeholder="아이디(이메일)" value="<?=$member['mb_id']?>" id="reg_mb_id" class="frm_input email minlength_3 <?=$required ?> <?=$readonly ?>" maxlength="100" <?=$required ?> <?=$readonly ?>>
            <label for="reg_mb_password" class="sound_only">비밀번호<strong class="sound_only">필수</strong></label>
            <input type="password" name="mb_password" placeholder="비밀번호" id="reg_mb_password" class="frm_input minlength_3 <?=$required?>" maxlength="100" <?=$required?>>
            <label for="reg_mb_password_re" class="sound_only">비밀번호 확인<strong class="sound_only">필수</strong></label>
            <input type="password" name="mb_password_re" placeholder="비밀번호 확인" id="reg_mb_password_re" class="frm_input minlength_3 <?=$required?>" maxlength="100" <?=$required?>>
        </div>

        <h2>개인정보 입력</h2>
        <div class="tbl_frm01 tbl_wrap">
            <label for="reg_mb_name" class="sound_only">이름<strong class="sound_only">필수</strong></label>
            <?/*?>
            <? if ($w=="u" && $config['cf_cert_use']) { ?>
                <span class="frm_info">아이핀 본인확인 후에는 이름이 자동 입력되고 휴대폰 본인확인 후에는 이름과 휴대폰번호가 자동 입력되어 수동으로 입력할수 없게 됩니다.</span>
            <? } ?>
            <?*/?>
            <input type="text" id="reg_mb_name" name="mb_name" placeholder="이름" value="<?=$member['mb_name']?>" <?=$required?> <? if ($w=='u') echo 'readonly'; ?> class="frm_input nospace <?=$required?> <?=$readonly?>">

            <?
            if($config['cf_cert_use']) {
                if($config['cf_cert_ipin'])
                    echo '<button type="button" id="win_ipin_cert" class="btn_frmline">아이핀 본인확인</button>'.PHP_EOL;
                if($config['cf_cert_hp'])
                    echo '<button type="button" id="win_hp_cert" class="btn_frmline">휴대폰 본인확인</button>'.PHP_EOL;

                echo '<noscript>본인확인을 위해서는 자바스크립트 사용이 가능해야합니다.</noscript>'.PHP_EOL;
            }
            ?>
            <?
            if ($config['cf_cert_use'] && $member['mb_certify']) {
                if($member['mb_certify'] == 'ipin')
                    $mb_cert = '아이핀';
                else
                    $mb_cert = '휴대폰';
                ?>
                <div id="msg_certify">
                    <strong><? echo $mb_cert; ?> 본인확인</strong><? if ($member['mb_adult']) { ?> 및 <strong>성인인증</strong><? } ?> 완료
                </div>
            <? } ?>
<!--
            <? if (isset($member['mb_nick_date']) && $member['mb_nick_date'] > date("Y-m-d", G5_SERVER_TIME - ($config['cf_nick_modify'] * 86400))) { // 닉네임수정일이 지나지 않았다면 ?>
                <input type="hidden" name="mb_nick_default" value="<? echo $member['mb_nick'] ?>">
                <input type="hidden" name="mb_nick" value="<? echo $member['mb_nick'] ?>">
            <? } ?>
-->
            <? if ($req_nick) { ?>
                <input type="hidden" name="nick_hidden" value="">
                <label for="reg_mb_nick" class="sound_only">닉네임<strong class="sound_only">필수</strong></label>

                <input type="hidden" name="mb_nick_default" value="<? echo isset($member['mb_nick'])?$member['mb_nick']:''; ?>">
                <button type="button" id="nick_check" class="btn_frmline">중복확인</button>

                <input type="text" name="mb_nick" placeholder="닉네임" value="<? echo isset($member['mb_nick'])?$member['mb_nick']:''; ?>" id="reg_mb_nick" required class="frm_input required nospace" maxlength="20">

                <span id="msg_mb_nick"></span>

                <p class="nickname_info">
                <span class="frm_info">
                    공백없이 한글,영문,숫자만 입력 가능 (한글2자, 영문4자 이상)<br>
                    닉네임을 바꾸시면 앞으로 <?=((int)$config['cf_nick_modify'])?>일 이내에는 변경 할 수 없습니다.
                </span>
                </p>


            <? } else {?>
                <input type="hidden" name="mb_nick_default" value="<?=$member['mb_nick'] ?>">
                <input type="hidden" name="mb_nick" value="<?=$member['mb_nick'] ?>">
                <input type="hidden" name="nick_hidden" value="1">
            <? } ?>

            <input type="text" id="reg_birthday" name="birthday" placeholder="생년월일" maxlength="8" size="10" value="<?=$member['mb_name']?>" <?=$required?> <? if ($w=='u') echo 'readonly'; ?> class="frm_input nospace <?=$required?> <?=$readonly?>">

            <label for="reg_mb_email" class="sound_only">E-mail<strong class="sound_only">필수</strong></label>

            <? if ($config['cf_use_email_certify']) {  ?>
                <span class="frm_info">
        <? if ($w=='') { echo "E-mail 로 발송된 내용을 확인한 후 인증하셔야 회원가입이 완료됩니다."; }  ?>
        <? if ($w=='u') { echo "E-mail 주소를 변경하시면 다시 인증하셔야 합니다."; }  ?>
                </span>
            <? }  ?>
            <input type="hidden" name="old_email" value="<? echo $member['mb_email'] ?>">
            <input type="email" name="mb_email" placeholder="E-mail" value="<? echo isset($member['mb_email'])?$member['mb_email']:''; ?>" id="reg_mb_email" required class="frm_input email required" size="50" maxlength="100">

            <? if ($config['cf_use_hp']) {  ?>
                <label for="reg_mb_hp" class="sound_only">휴대폰번호<? if ($config['cf_req_hp']) { ?><strong class="sound_only">필수</strong><? } ?></label>
                <input type="text" name="mb_hp" placeholder="휴대폰 번호" value="<? echo $member['mb_hp'] ?>" id="reg_mb_hp" <? echo ($config['cf_req_hp'])?"required":""; ?> class="frm_input <? echo ($config['cf_req_hp'])?"required":""; ?>" maxlength="20">
                <? if ($config['cf_cert_use'] && $config['cf_cert_hp']) { ?>
                    <input type="hidden" name="old_mb_hp" value="<? echo $member['mb_hp'] ?>">
                <? } ?>
            <? } ?>

            <? if ($config['cf_use_addr']) { ?>
                <div class="add_input">
                    <label for="reg_mb_addr1" class="sound_only">주소 입력<? echo $config['cf_req_addr']?'<strong class="sound_only"> 필수</strong>':''; ?></label>
                    <input type="text" name="mb_addr1" placeholder="주소 입력(주소 검색창을 눌러주세요)" value="<? echo $member['mb_addr1'] ?>" id="reg_mb_addr1" <? echo $config['cf_req_addr']?"required":""; ?> class="frm_input frm_address <? echo $config['cf_req_addr']?"required":""; ?>" size="50"><br>
                    <label for="reg_mb_zip1" class="sound_only">우편번호 앞자리<? echo $config['cf_req_addr']?'<strong class="sound_only"> 필수</strong>':''; ?></label>
                    <input type="text" name="mb_zip1" value="<? echo $member['mb_zip1'] ?>" id="reg_mb_zip1" <? echo $config['cf_req_addr']?"required":""; ?> class="frm_input <? echo $config['cf_req_addr']?"required":""; ?>" size="3" maxlength="3">
                    <label for="reg_mb_zip2" class="sound_only">우편번호 뒷자리<? echo $config['cf_req_addr']?'<strong class="sound_only"> 필수</strong>':''; ?></label>
                    <input type="text" name="mb_zip2" value="<? echo $member['mb_zip2'] ?>" id="reg_mb_zip2" <? echo $config['cf_req_addr']?"required":""; ?> class="frm_input <? echo $config['cf_req_addr']?"required":""; ?>" size="3" maxlength="3">
                    <p class="add_search"><a href="/bbs/zip.php?frm_name=fregisterform&amp;frm_zip1=mb_zip1&amp;frm_zip2=mb_zip2&amp;frm_addr1=mb_addr1&amp;frm_addr2=mb_addr2&amp;frm_addr3=mb_addr3&amp;frm_jibeon=mb_addr_jibeon" id="reg_zip_find" class="btn_frmline win_zip_find" target="_blank">주소 검색</a></p>
                    <label for="reg_mb_addr2" class="sound_only">상세 주소</label>
                    <input type="text" name="mb_addr2" placeholder="상세 주소 입력" value="<? echo $member['mb_addr2'] ?>" id="reg_mb_addr2" class="frm_input frm_address" size="50"><br>
                    <label for="reg_mb_addr3" class="sound_only" style="display:none;">참고항목</label>
                    <input type="text" name="mb_addr3"  style="display:none;" value="<? echo $member['mb_addr3'] ?>" id="reg_mb_addr3" readonly="readonly" class="frm_input frm_address" size="50">
                    <input type="hidden" name="mb_addr_jibeon" value="<? echo $member['mb_addr_jibeon']; ?>"><br>
                    <span id="mb_addr_jibeon"><? echo ($member['mb_addr_jibeon'] ? '지번주소 : '.$member['mb_addr_jibeon'] : ''); ?></span>
<!--                    <label for="reg_mb_zip1" class="sound_only">우편번호 앞자리--><? //echo $config['cf_req_addr']?'<strong class="sound_only"> 필수</strong>':''; ?><!--</label>-->
<!--                    <input type="text" name="mb_zip1" value="--><? //echo $member['mb_zip1'] ?><!--" id="reg_mb_zip1" --><? //echo $config['cf_req_addr']?"required":""; ?><!-- class="frm_input --><? //echo $config['cf_req_addr']?"required":""; ?><!--" size="3" maxlength="3">-->
<!--                    <label for="reg_mb_zip2" class="sound_only">우편번호 뒷자리--><? //echo $config['cf_req_addr']?'<strong class="sound_only"> 필수</strong>':''; ?><!--</label>-->
<!--                    <input type="text" name="mb_zip2" value="--><? //echo $member['mb_zip2'] ?><!--" id="reg_mb_zip2" --><? //echo $config['cf_req_addr']?"required":""; ?><!-- class="frm_input --><? //echo $config['cf_req_addr']?"required":""; ?><!--" size="3" maxlength="3">-->
<!--                    <p class="add_search"><a href="--><? //echo G5_BBS_URL ?><!--/zip.php?frm_name=fregisterform&amp;frm_zip1=mb_zip1&amp;frm_zip2=mb_zip2&amp;frm_addr1=mb_addr1&amp;frm_addr2=mb_addr2&amp;frm_addr3=mb_addr3&amp;frm_jibeon=mb_addr_jibeon" id="reg_zip_find" class="btn_frmline win_zip_find" target="_blank">주소 검색</a></p>-->
<!--                    <label for="reg_mb_addr1" class="sound_only">주소 입력--><? //echo $config['cf_req_addr']?'<strong class="sound_only"> 필수</strong>':''; ?><!--</label>-->
<!--                    <input type="text" name="mb_addr1" placeholder="주소 입력" value="--><? //echo $member['mb_addr1'] ?><!--" id="reg_mb_addr1" --><? //echo $config['cf_req_addr']?"required":""; ?><!-- class="frm_input frm_address --><? //echo $config['cf_req_addr']?"required":""; ?><!--" size="50"><br>-->
<!--                    <label for="reg_mb_addr2" class="sound_only">상세 주소</label>-->
<!--                    <input type="text" name="mb_addr2" placeholder="상세 주소 입력" value="--><? //echo $member['mb_addr2'] ?><!--" id="reg_mb_addr2" class="frm_input frm_address" size="50"><br>-->
<!--                    <label for="reg_mb_addr3" class="sound_only">참고항목</label>-->
<!--                    <input type="text" name="mb_addr3" value="--><? //echo $member['mb_addr3'] ?><!--" id="reg_mb_addr3" readonly="readonly" class="frm_input frm_address" size="50">-->
<!--                    <input type="hidden" name="mb_addr_jibeon" value="--><? //echo $member['mb_addr_jibeon']; ?><!--"><br>-->
<!--                    <span id="mb_addr_jibeon">--><? //echo ($member['mb_addr_jibeon'] ? '지번주소 : '.$member['mb_addr_jibeon'] : ''); ?><!--</span>-->
                </div>

            <? } ?>

        </div>

        <div class="per_set tbl_frm01 tbl_wrap">
            <h2>기타 개인설정</h2>
            <? if ($config['cf_use_signature']) { ?>
                <label for="reg_mb_signature">서명<? if ($config['cf_req_signature']){ ?><strong class="sound_only">필수</strong><? } ?></label>
                <textarea name="mb_signature" id="reg_mb_signature" class="<? echo $config['cf_req_signature']?"required":""; ?>" <? echo $config['cf_req_signature']?"required":""; ?>><? echo $member['mb_signature'] ?></textarea>
            <? } ?>

            <? if ($config['cf_use_profile']) { ?>
                <label for="reg_mb_profile">자기소개</label>
                <textarea name="mb_profile" id="reg_mb_profile" class="<? echo $config['cf_req_profile']?"required":""; ?>" <? echo $config['cf_req_profile']?"required":""; ?>><? echo $member['mb_profile'] ?></textarea>
            <? } ?>
<?/*
            <? if ($config['cf_use_member_icon'] && $member['mb_level'] >= $config['cf_icon_level']) { ?>
                <label for="reg_mb_icon">회원아이콘</label>
                <span class="frm_info">
                    이미지 크기는 가로 <? echo $config['cf_member_icon_width'] ?>픽셀, 세로 <? echo $config['cf_member_icon_height'] ?>픽셀 이하로 해주세요.<br>
                    gif만 가능하며 용량 <? echo number_format($config['cf_member_icon_size']) ?>바이트 이하만 등록됩니다.
                </span>
                        <input type="file" name="mb_icon" id="reg_mb_icon" class="frm_input">
                        <? if ($w == 'u' && file_exists($mb_icon)) { ?>
                            <img src="<? echo $mb_icon_url ?>" alt="회원아이콘">
                            <input type="checkbox" name="del_mb_icon" value="1" id="del_mb_icon">
                            <label for="del_mb_icon">삭제</label>
                        <? } ?>

            <? } ?>
<? */ ?>
            <div class="set_position">
                <input type="checkbox" name="mb_mailling" value="1" id="reg_mb_mailling" <? echo ($w=='' || $member['mb_mailling'])?'checked':''; ?>>
                <label for="reg_mb_mailling">메일링서비스</label>
                <p>정보 메일을 받겠습니다.</p>
            </div>
            <? if ($config['cf_use_hp']) { ?>
            <div class="set_position">
                <input type="checkbox" name="mb_sms" value="1" id="reg_mb_sms" <? echo ($w=='' || $member['mb_sms'])?'checked':''; ?>>
                <label for="reg_mb_sms">SMS 수신여부</label>
                <p>휴대폰 문자메세지를 받겠습니다.</p>
            </div>
            <? } ?>
            <? if (isset($member['mb_open_date']) && $member['mb_open_date'] <= date("Y-m-d", G5_SERVER_TIME - ($config['cf_open_modify'] * 86400)) || empty($member['mb_open_date'])) { // 정보공개 수정일이 지났다면 수정가능 ?>
            <div class="set_position">
                <input type="hidden" name="mb_open_default" value="<? echo $member['mb_open'] ?>">
                <input type="checkbox" name="mb_open" value="1" id="reg_mb_open" <? echo ($w=='' || $member['mb_open'])?'checked':''; ?>>
                <label for="reg_mb_open">정보공개</label>
                <p>다른분들이 나의 정보를 볼 수 있도록 합니다.</p>
                <span class="frm_info">정보공개를 바꾸시면 앞으로 <? echo (int)$config['cf_open_modify'] ?>일 이내에는 변경이 안됩니다.</span>
            </div>
<!---->
            <? } else { ?>
                <tr>
                    <th scope="row">정보공개</th>
                    <td>
                <span class="frm_info">
                    정보공개는 수정후 <? echo (int)$config['cf_open_modify'] ?>일 이내, <? echo date("Y년 m월 j일", isset($member['mb_open_date']) ? strtotime("{$member['mb_open_date']} 00:00:00")+$config['cf_open_modify']*86400:G5_SERVER_TIME+$config['cf_open_modify']*86400); ?> 까지는 변경이 안됩니다.<br>
                    이렇게 하는 이유는 잦은 정보공개 수정으로 인하여 쪽지를 보낸 후 받지 않는 경우를 막기 위해서 입니다.
                </span>
                        <input type="hidden" name="mb_open" value="<? echo $member['mb_open'] ?>">
                    </td>
                </tr>
            <? } ?>

            <? if ($w == "" && $config['cf_use_recommend']) { ?>
                <tr>
                    <th scope="row"><label for="reg_mb_recommend">추천인아이디</label></th>
                    <td><input type="text" name="mb_recommend" id="reg_mb_recommend" class="frm_input"></td>
                </tr>
            <? } ?>
<!---->
            <h3>자동등록방지</h3>
            <div class="reg_prevent">
                <?= captcha_html(); ?>
            </div>
        </div>

        <div class="btn_confirm">
            <a href="<? echo $g5['path'] ?>/" class="btn_cancel">취소</a>
            <input type="submit" value="<? echo $w==''?'회원가입':'정보수정'; ?>" id="btn_submit" class="btn_submit" accesskey="s">
        </div>
    </form>

    <script>
        $(function() {
            $("#reg_zip_find").css("display", "inline-block");
            $("#reg_mb_zip1, #reg_mb_zip2, #reg_mb_addr1").attr("readonly", true);

            <? if($config['cf_cert_use'] && $config['cf_cert_ipin']) { ?>
            // 아이핀인증
            $("#win_ipin_cert").click(function() {
                if(!cert_confirm())
                    return false;

                var url = "<? echo G5_OKNAME_URL; ?>/ipin1.php";
                certify_win_open('kcb-ipin', url);
                return;
            });

            <? } ?>
            <? if($config['cf_cert_use'] && $config['cf_cert_hp']) { ?>

            // 휴대폰인증
            $("#win_hp_cert").click(function() {
                if(!cert_confirm())
                    return false;

                <?
                switch($config['cf_cert_hp']) {
                    case 'kcb':
                        $cert_url = G5_OKNAME_URL.'/hs_cnfrm_popup2.php';
                        $cert_type = 'kcb-hp';
                        break;
                    case 'kcp':
                        $cert_url = G5_KCPCERT_URL.'/kcpcert_form.php';
                        $cert_type = 'kcp-hp';
                        break;
                    default:
                        echo 'alert("기본환경설정에서 휴대폰 본인확인 설정을 해주십시오");';
                        echo 'return false;';
                        break;
                }
                ?>

                certify_win_open("<? echo $cert_type; ?>", "<? echo $cert_url; ?>");
                return;
            });
            <? } ?>

            // 중복 체크
            $("#id_check").click(function(){
                var mb_id = $("input[name='mb_id']");
                var id = mb_id.val();

                if(id == ""){
                    alert("아이디를 입력하세요.");
                    mb_id.focus();
                    $("input[name='id_hidden']").val("");
                    return false;
                }
                $.post("_Ajax.id_nick_chk.php", {mode : "id_chk", id : id}, function(data) {
                    if(data > 0) {
                        alert("중복된 아이디가 존재합니다");
                        mb_id.focus();
                        return false;
                    }else{
                        alert("사용 가능한 아이디입니다.");
                        $("input[name='id_hidden']").val(1);
                    }
                });
            });

            $("#nick_check").click(function(){
                var mb_nick = $("input[name='mb_nick']")
                var nick = mb_nick.val();
                if(nick == ""){
                    alert("닉네임을 입력하세요.");
                    mb_nick.focus();
                    $("input[name='nick_hidden']").val("");
                    return false;
                }
                $.post("_Ajax.id_nick_chk.php", {mode : "nick_chk", nick : nick}, function(data){
                    if(data > 0){
                        alert("중복된 닉네임이 존재합니다");
                        mb_nick.focus();
                        return false;
                    }else{
                        alert("사용 가능한 닉네임입니다.");
                        $("input[name='nick_hidden']").val(1);
                    }
                });
            });

        });

        // 인증체크
        function cert_confirm()
        {
            var val = document.fregisterform.cert_type.value;
            var type;

            switch(val) {
                case "ipin":
                    type = "아이핀";
                    break;
                case "hp":
                    type = "휴대폰";
                    break;
                default:
                    return true;
            }

            if(confirm("이미 "+type+"으로 본인확인을 완료하셨습니다.\n\n이전 인증을 취소하고 다시 인증하시겠습니까?"))
                return true;
            else
                return false;
        }

        // submit 최종 폼체크
        function fregisterform_submit(f)
        {
            // 회원아이디 검사
            if (f.w.value == "") {
                var msg = reg_mb_id_check();
                if (msg) {
                    alert(msg);
                    f.mb_id.select();
                    return false;
                }
            }

            if (f.w.value == '') {
                if (f.mb_password.value.length < 3) {
                    alert('비밀번호를 3글자 이상 입력하십시오.');
                    f.mb_password.focus();
                    return false;
                }
            }

            if (f.mb_password.value != f.mb_password_re.value) {
                alert('비밀번호가 같지 않습니다.');
                f.mb_password_re.focus();
                return false;
            }

            if (f.mb_password.value.length > 0) {
                if (f.mb_password_re.value.length < 3) {
                    alert('비밀번호를 3글자 이상 입력하십시오.');
                    f.mb_password_re.focus();
                    return false;
                }
            }

            // 이름 검사
            if (f.w.value=='') {
                if (f.mb_name.value.length < 1) {
                    alert('이름을 입력하십시오.');
                    f.mb_name.focus();
                    return false;
                }
            }

            // 닉네임 검사
            if ((f.w.value == "") || (f.w.value == "u" && f.mb_nick.defaultValue != f.mb_nick.value)) {
                var msg = reg_mb_nick_check();
                if (msg) {
                    alert(msg);
                    f.reg_mb_nick.select();
                    return false;
                }
            }

            // 중복확인
			if(f.id_hidden.value == ""){
				alert("아이디 중복확인을 하십시오.");
				f.mb_id.focus();
				return false;
			}

			if(f.nick_hidden.value == ""){
				alert("닉네임 중복확인을 하십시오.");
				f.mb_nick.focus();
				return false;
			}

            // E-mail 검사
            if ((f.w.value == "") || (f.w.value == "u" && f.mb_email.defaultValue != f.mb_email.value)) {
                var msg = reg_mb_email_check();
                if (msg) {
                    alert(msg);
                    f.reg_mb_email.select();
                    return false;
                }
            }

            if (typeof f.mb_icon != 'undefined') {
                if (f.mb_icon.value) {
                    if (!f.mb_icon.value.toLowerCase().match(/.(gif)$/i)) {
                        alert('회원아이콘이 gif 파일이 아닙니다.');
                        f.mb_icon.focus();
                        return false;
                    }
                }
            }

            if (typeof(f.mb_recommend) != 'undefined' && f.mb_recommend.value) {
                if (f.mb_id.value == f.mb_recommend.value) {
                    alert('본인을 추천할 수 없습니다.');
                    f.mb_recommend.focus();
                    return false;
                }

                var msg = reg_mb_recommend_check();
                if (msg) {
                    alert(msg);
                    f.mb_recommend.select();
                    return false;
                }
            }

//            if (document.fregisterform.cert_type.value == "") {
//                alert('본인 인증이 필요합니다.\n본인 인증을 해주시기 바랍니다.');
//                f.reg_mb_name.focus();
//                return false;
//            }

            <? echo chk_captcha_js(); ?>

            document.getElementById("btn_submit").disabled = "disabled";
//            alert('onSubmit complete;;;');
//            return false;
            return true;
        }
    </script>
</div>