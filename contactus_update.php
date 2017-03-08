<?
include_once('./_common.php');
include_once(G5_LIB_PATH.'/mailer.lib.php');

$header = "From:".$_POST[cu_name]." <".$_POST[cu_email].">\r\n"; 
$header.= "MIME-Version: 1.0\r\n"; 
$header.= "Content-Type: text/html; charset=UTF-8\r\n"; 
$header.= "X-Priority: 1\r\n";

$subject = "코인투데이 문의입니다.";
$type = 2;
$content = "
<table border='0' cellspacing='0' cellpadding='0' width='100%'>
	<tr height='30px'>
		<td align='center'>".$_POST[cu_message]."</td>
	</tr>
</table>
";

$tomail = $config[cf_admin_email];
//$tomail = "lsy5718zzang@naver.com";
//$result = @mail($tomail,$subject,$content,$header);
$result = mailer($_POST[cu_name], $_POST[cu_email], $tomail, $subject, $content, $type, "", "", "");

if($result){
	alert("성공적으로 메일이 발송되었습니다.", G5_URL."/contactus.php");
}else{
	alert("메일 발송이 실패되었습니다.", G5_URL."/contactus.php");
}
//mailer("Cointoday", $config[cf_admin_email], $_POST[email], $subject, $content, $type, "", "", "");
?>