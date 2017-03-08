<?
include_once('./_common.php');
include_once(G5_LIB_PATH.'/mailer.lib.php');

$header = "From:".$_POST[sec_name]." <".$_POST[sec_email1]."@".$_POST[sec_email2].">\r\n"; 
$header.= "MIME-Version: 1.0\r\n"; 
$header.= "Content-Type: text/html; charset=UTF-8\r\n"; 
$header.= "X-Priority: 1\r\n";

$subject = "코인투데이 실물투자 컨설팅 & 보안금고 신청입니다.";
$type = 2;
$content = '

신청자명 : '.$_POST[sec_name].'</br>
이메일 : '.$_POST[sec_email1]."@".$_POST[sec_email2].'</br>
휴대폰 : '.$_POST[sec_hp1]."-".$_POST[sec_hp2]."-".$_POST[sec_hp3].'</br></br></br>
1. 당신의 귀금속 구매 목적은 무엇입니까?</br>
'.$_POST[sec_radio1].'</br></br>

2. 보유하신 금속 중 가장 많은 비율의 금속은?</br>
'.$_POST[sec_radio2].'</br></br>

3. 보유 하고 있는 중량은 대략 얼마나 되십니까?</br>
'.$_POST[sec_radio3].'</br></br>

4. 구매하신 귀금속의 구매가를 기록 하고 있습니까?</br>
'.$_POST[sec_radio4].'</br></br>

궁금한 사항 있으시면 말씀해 주세요.</br>
'.$_POST[sec_radio5].'</br></br>
';

//$tomail = $config[cf_admin_email];
$tomail = "lsy5718zzang@naver.com";
//$result = @mail($tomail,$subject,$content,$header);
$result = mailer($_POST[sec_name], $_POST[sec_email1]."@".$_POST[sec_email2], $tomail, $subject, $content, $type, "", "", "");

if($result){
	alert("성공적으로 메일이 발송되었습니다.", G5_URL."/sec_lock.php");
}else{
	alert("메일 발송이 실패되었습니다.", G5_URL."/sec_lock.php");
}
//mailer("Cointoday", $config[cf_admin_email], $_POST[email], $subject, $content, $type, "", "", "");
?>