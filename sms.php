<?php
include "common.php";



/**
 * "SMS 서버와 통신이 불안정합니다." 라는 메세지가 나올 경우
 * component.php파일의 36~51 라인을 참고 하여 문자 발송 시 사용되는 Port를
 *  아웃바운드로 허용 하시기 바랍니다.(서버관리자나 호스팅업체에 문의)
 * 발송 port는 여러개의 포트를 랜덤으로 선택해서 보내기 때문에 여러개의 포트를 아웃바운드로
 *  오픈하기가 힘드실 경우 단일 Port로 지정하여 발송도 가능합니다.
 * ex) $this->socket_port = (int)rand(6295,6297);	// SMS
 *     -> 수정 후
 *     $this->socket_port = 6295; // SMS
 */

//echo sendSms('01044820607;',"내용");
?>