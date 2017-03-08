<?
include_once("./_common.php");
//error_reporting(E_ALL);
//ini_set("display_errors", 1);

if($_POST[HTTP_CHK] != "CHK_OK"){ alert("잘못된 접근 방식입니다.");}

$path = "../data/alliance";
chmod("../data/alliance", 0707);

$name = $_POST[name];
$tel = $_POST[tel1]."-".$_POST[tel2]."-".$_POST[tel3];
$hp = $_POST[hp1]."-".$_POST[hp2]."-".$_POST[hp3];
$email = $_POST[email1]."@".$_POST[email2];
$file_arr = explode(".", $_FILES["al_file"][name]);
$filename = $file_arr[0]."_".strtotime("now");
$ext = $file_arr[1];
$subject = "=?UTF-8?B?".base64_encode("[코인즈투데이]제휴문의 드립니다.")."?=";
$mail_con = str_replace("\\r\\n", "</br>", $_POST[content]);

$header = "From: ".$name."<".$email.">\r\n"; 
$header.= "MIME-Version: 1.0\r\n"; 
$header.= "Content-Type: text/html; charset=UTF-8\r\n"; 
$header.= "X-Priority: 1\r\n";

mail($config['cf_admin_email'], $subject, $mail_con, $header);

move_uploaded_file($_FILES["al_file"][tmp_name], $path."/".$filename.".".$ext);

$sql = "
insert into {$g5['g5_alliance_table']} set
com_info='".$_POST[com_info]."',
name='".$_POST[name]."',
content='".$_POST[content]."',
tel='".$tel."',
hp='".$hp."',
email='".$email."',
file='".$filename.".".$ext."',
date='".strtotime("now")."',
reply_status='n'
";

sql_query($sql);

chmod($path, 0755);

alert("등록이 완료 되었습니다.", "./alliance.php");
?>