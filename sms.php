<?php
include "common.php";

/**
 * 아이코드 발송서버 정보
 *
 * 문자를 발송하기 위한 서버정보입니다.
 * IP는 아이코드의 고정서버이니 변경하지 않으셔도 됩니다.
 * 아이코드의 고정서버 IP는 '211.172.232.124' 입니다.
 * Port번호는 상황에 따라 달라질 수 있습니다. 형태는 다음과 같습니다.
 */
$socket_host	= "211.172.232.124";
$socket_port	= 9201;

/**
 * 아이코드 발송전용 토큰키
 *
 * 아이코드 접속을 위한 토큰키입니다.
 * 발송 전용 토큰키를 넣어주시기 바랍니다.
 * 토큰키는 아이코드 사이트인 'http://www.icodekorea.com/'의
 * 기업고객페이지의 모듈다운로드의 '토큰키 관리' 화면에서 생성가능합니다.
 */
$icode_key	= "a274fd7208b128a35590ac33ce8f625b";


/**
 * 발신번호 사전등록제 (전기통신사업법 제84조)
 *  거짓으로 표시된 전화번호를 인한 이용자 피해 예방을 위하여 문자 전송시
 *  사전 인증된 발신번호로만 사용할 수 있도록 등록하는 제도입니다.
 *  발신번호등록은 아이코드 사이트 로그인 후 상단 발신번호 등록를 참고 하시기 바랍니다.
 */

$SMS = new SMSN;		/* SMS 모듈 클래스 생성 */
$SMS->SMS_con($socket_host,$socket_port,$icode_key);		/* 아이코드 서버 접속 */

/**
 * 문자발송 Form을 사용하지 않고 자동 발송의 경우 수신번호가 1개일 경우 번호 마지막에 ";"를 붙인다
 * ex) $strTelList = "0100000001;";
 */
$strTelList     = '01044820607';		/* 수신번호 : 01000000001;0100000002; */
$strCallBack    = '0220886657';	/* 발신번호 : 0317281281 */
$strSubject     = 'LMS제목  : LMS발송에 이용되는 제목( component.php 60라인을 참고 바랍니다.';		/* LMS제목  : LMS발송에 이용되는 제목( component.php 60라인을 참고 바랍니다. */
$strData        = "내가 나이 먹을 때마다 날 찾아온 겨울
매일 밤 흥겨울 줄 알았던 나의 이십대는
좆도 역겨울 정도로 가난했고
존내 다사다난 했고... 뭐 다들 비슷할걸?
다 담아 내 거로 만들고 싶던 겨울밤 종로
도로의 불빛 따라 걸어
더러운 손가락에다 감정 넣어 매퀘한 밤공기를
노트에 담곤 길들이듯 쓰다 지우다 쓰다 지우다...
지칠 땐 눈을 감던 길
";        /* 메세지 : 발송하실 문자 메세지 */
$chkSendFlag    = '0';	/* 예약 구분자 : 0 즉시전송, 1 예약발송 */
$R_YEAR         = $_POST["R_YEAR"];         /* 예약 : 년(4자리) 2016 */
$R_MONTH        = $_POST["R_MONTH"];        /* 예약 : 월(2자리) 01 */
$R_DAY          = $_POST["R_DAY"];          /* 예약 : 일(2자리) 31 */
$R_HOUR         = $_POST["R_HOUR"];         /* 예약 : 시(2자리) 02 */
$R_MIN          = $_POST["R_MIN"];          /* 예약 : 분(2자리) 59 */

$strDest	= explode(";",$strTelList);
$nCount		= count($strDest)-1;		// 문자 수신번호 갯수

// 예약설정을 합니다.
if ($chkSendFlag) $strDate = $R_YEAR.$R_MONTH.$R_DAY.$R_HOUR.$R_MIN;
else $strDate = "";

// 문자 발송에 필요한 항목을 배열에 추가
$result = $SMS->Add($strDest, $strCallBack, $strCaller, $strSubject, $strURL, $strData, $strDate, $nCount);

// 패킷 정의의 결과에 따라 발송여부를 결정합니다.
if ($result) {
	echo "일반메시지 입력 성공<BR>";
	echo "<HR>";

	// 패킷이 정상적이라면 발송에 시도합니다.
	$result = $SMS->Send();

	if ($result) {
		echo "서버에 접속했습니다.<br>";
		$success = $fail = 0;
		$isStop = 0;
		foreach($SMS->Result as $result) {

			list($phone,$code)=explode(":",$result);

			if (substr($code,0,5)=="Error") {
				echo $phone.' 발송에러('.substr($code,6,2).'): ';
				switch (substr($code,6,2)) {
					case '17':	 // "07: 발송대기 처리. 지연해소시 발송됨."
						echo "일시적인 지연으로 인해 발송대기 처리되었습니다.<br>";
						break;
					case '23':	 // "23:데이터오류, 전송날짜오류, 발신번호미등록"
						echo "데이터를 다시 확인해 주시기바랍니다.<br>";
						break;

					// 아래의 사유들은 발송진행이 중단됨.
					case '85':	 // "85:발송번호 미등록"
						echo "등록되지 않는 발송번호 입니다.<br>";
						break;
					case '87':	 // "87:인증실패"
						echo "(정액제-계약확인)인증 받지 못하였습니다.<br>";
						break;
					case '88':	 // "88:연동모듈 발송불가"
						echo "연동모듈 사용이 불가능합니다. 아이코드로 문의하세요.<br>";
						break;

					case '96':	 // "96:토큰 검사 실패"
						echo "사용할 수 없는 토큰키입니다.<br>";
						break;
					case '97':	 // "97:잔여코인부족"
						echo "잔여코인이 부족합니다.<br>";
						break;
					case '98':	 // "98:사용기간만료"
						echo "사용기간이 만료되었습니다.<br>";
						break;
					case '99':	 // "99:인증실패"
						echo "서비스 사용이 불가능합니다. 아이코드로 문의하세요.<br>";
						break;
					default:	 // "미 확인 오류"
						echo "알 수 없는 오류로 전송이 실패하었습니다.<br>";
						break;
				}
				$fail++;
			} else {
				echo $phone."로 전송했습니다. (msg seq : ".$code.")<br>";
				$success++;
			}
		}
		echo '<br>'.$success."건을 전송했으며 ".$fail."건을 보내지 못했습니다.<br>";
		$SMS->Init(); // 보관하고 있던 결과값을 지웁니다.
	}
	else echo "에러: SMS 서버와 통신이 불안정합니다.<br>";
}

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
?>