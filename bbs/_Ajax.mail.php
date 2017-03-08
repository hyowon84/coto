<?
include_once('./_common.php');
include_once(G5_LIB_PATH.'/mailer.lib.php');

mt_srand((double)microtime()*1000000);
$P_TRADE_CODE =chr(mt_rand(65, 90));
$P_TRADE_CODE =chr(mt_rand(65, 90));
$P_TRADE_CODE.=chr(mt_rand(65, 90));
$P_TRADE_CODE.=chr(mt_rand(65, 90));
$P_TRADE_CODE.=time();

$header = "From: <".$config[cf_admin_email].">\r\n"; 
$header.= "MIME-Version: 1.0\r\n"; 
$header.= "Content-Type: text/html; charset=UTF-8\r\n"; 
$header.= "X-Priority: 1\r\n";

$subject = "=?EUC-KR?B?".base64_encode(iconv("UTF-8","EUC-KR","코인투데이 회원가입 인증 메일입니다."))."?=\r\n";
$type = 2;
$content = "
<table border='0' cellspacing='0' cellpadding='0' width='100%'>
	<tr height='100px'>
		<td align='center'>회원님의 인증번호는 ".$P_TRADE_CODE."</td>
	</tr>
</table>
";

$chk = sql_fetch("select * from g5_mail_auth where ip='".$REMOTE_ADDR."' ");

if($chk[ip] == $REMOTE_ADDR){

	sql_query("
	update g5_mail_auth set
	ip='".$REMOTE_ADDR."',
	auth_key='".$P_TRADE_CODE."',
	date='".strtotime("now")."'
	where ip='".$REMOTE_ADDR."'
	");

}else{

	sql_query("
	insert into g5_mail_auth set
	ip='".$REMOTE_ADDR."',
	auth_key='".$P_TRADE_CODE."',
	date='".strtotime("now")."'
	");

}

$result = @mail($_POST[email],$subject,$content,$header);

if($result){
	echo "성공";
}else{
	echo "실패";
}
//mailer("Cointoday", $config[cf_admin_email], $_POST[email], $subject, $content, $type, "", "", "");
?>