<?
include_once('./_common.php');

if($_POST[HTTP_CHK] != "CHK_OK") alert("잘못된 접근방식입니다.");

//chmod("../../data/store_slide", 0707);


$chars_array = array_merge(range(0,9), range('a','z'), range('A','Z'));

if($_FILES['csf_file']['name']){

	$tmp_file  = $_FILES['csf_file']['tmp_name'];

	$filename  = $_FILES['csf_file']['name'];
    $filename  = preg_replace('/(<|>|=)/', '', $filename);


	if (is_uploaded_file($tmp_file)) {

		$row = sql_fetch(" select csf_file from {$g5['g5_consulting_file_table']} where csf_idx = '{$csf_idx}' ");
		@unlink(G5_DATA_PATH.'/consulting_file/'.$row['csf_file']);

		 // 아래의 문자열이 들어간 파일은 -x 를 붙여서 웹경로를 알더라도 실행을 하지 못하도록 함
        $filename = preg_replace("/\.(php|phtm|htm|cgi|pl|exe|jsp|asp|inc)/i", "$0-x", $filename);

        shuffle($chars_array);
        $shuffle = implode('', $chars_array);

        // 첨부파일 첨부시 첨부파일명에 공백이 포함되어 있으면 일부 PC에서 보이지 않거나 다운로드 되지 않는 현상이 있습니다. (길상여의 님 090925)
        $csf_file = abs(ip2long($_SERVER['REMOTE_ADDR'])).'_'.substr($shuffle,0,8).'_'.str_replace('%', '', urlencode(str_replace(' ', '_', $filename)));

        $dest_file = G5_DATA_PATH.'/consulting_file/'.$csf_file;

        // 업로드가 안된다면 에러메세지 출력하고 죽어버립니다.
        $error_code = move_uploaded_file($tmp_file, $dest_file) or die($_FILES['csf_file']['error']);

        // 올라간 파일의 퍼미션을 변경합니다.
        chmod($dest_file, G5_FILE_PERMISSION);

		sql_query("update {$g5['g5_consulting_file_table']} set csf_source='".$filename."', csf_file='".$csf_file."' where csf_idx = '{$csf_idx}' ");

	}
}
alert("성공적으로 등록 되었습니다.", "./consultingfile_form.php");
?>