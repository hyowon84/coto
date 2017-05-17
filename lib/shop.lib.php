<?php
//==============================================================================
// 쇼핑몰 라이브러리 모음 시작
//==============================================================================

/*
간편 사용법 : 상품유형을 1~5 사이로 지정합니다.
$disp = new item_list(1);
echo $disp->run();


유형+분류별로 노출하는 경우 상세 사용법 : 상품유형을 지정하는 것은 동일합니다.
$disp = new item_list(1);
// 사용할 스킨을 바꿉니다.
$disp->set_list_skin("type_user.skin.php");
// 1단계분류를 20으로 시작되는 분류로 지정합니다.
$disp->set_category("20", 1);
echo $disp->run();


분류별로 노출하는 경우 상세 사용법
// type13.skin.php 스킨으로 3개씩 2줄을 폭 150 사이즈로 분류코드 30 으로 시작되는 상품을 노출합니다.
$disp = new item_list(0, "type13.skin.php", 3, 2, 150, 0, "30");
echo $disp->run();


이벤트로 노출하는 경우 상세 사용법
// type13.skin.php 스킨으로 3개씩 2줄을 폭 150 사이즈로 상품을 노출합니다.
$disp = new item_list(0, "type13.skin.php", 3, 2, 150, 0);
// 이벤토번호를 설정합니다.
$disp->set_event("12345678");
echo $disp->run();

참고) 영카트4의 display_type 함수와 사용방법이 비슷한 class 입니다.
	  display_category 나 display_event 로 사용하기 위해서는 $type 값만 넘기지 않으면 됩니다.
*/

class item_list
{
	// 상품유형 : 기본적으로 1~5 까지 사용할수 있으며 0 으로 설정하는 경우 상품유형별로 노출하지 않습니다.
	// 분류나 이벤트로 노출하는 경우 상품유형을 0 으로 설정하면 됩니다.
	protected $type;

	protected $list_skin;
	protected $list_mod;
	protected $list_row;
	protected $img_width;
	protected $img_height;

	// 상품상세보기 경로
	protected $href = "";
	protected $auc_href = "";

	// select 에 사용되는 필드
	protected $fields = "*";

	// 분류코드로만 사용하는 경우 상품유형($type)을 0 으로 설정하면 됩니다.
	protected $ca_id = "";
	protected $ca_id2 = "";
	protected $ca_id3 = "";

	// 노출순서
	protected $order_by = "it_order, it_id desc";

	// 상품의 이벤트번호를 저장합니다.
	protected $event = "";

	// 스킨의 기본 css 를 다른것으로 사용하고자 할 경우에 사용합니다.
	protected $css = "";

	// 상품의 사용여부를 따져 노출합니다. 0 인 경우 모든 상품을 노출합니다.
	protected $use = 1;

	// 모바일에서 노출하고자 할 경우에 true 로 설정합니다.
	protected $is_mobile = false;

	// 기본으로 보여지는 필드들
	protected $view_it_id	= false;	   // 상품코드
	protected $view_it_img   = true;		// 상품이미지
	protected $view_it_name  = true;		// 상품명
	protected $view_it_basic = true;		// 기본설명
	protected $view_it_price = true;		// 판매가격
	protected $view_it_cust_price = false;  // 소비자가
	protected $view_it_icon = false;		// 아이콘
	protected $view_sns = false;			// SNS

	// 몇번째 class 호출인지를 저장합니다.
	protected $count = 0;

	// true 인 경우 페이지를 구한다.
	protected $is_page = false;

	// 페이지 표시를 위하여 총 상품수를 구합니다.
	public $total_count = 0;

	// sql limit 의 시작 레코드
	protected $from_record = 0;

	// 외부에서 쿼리문을 넘겨줄 경우에 담아두는 변수
	protected $query = "";

	// 검색 데이터
	protected $sch_que = "";


	// $type		: 상품유형 (기본으로 1~5까지 사용)
	// $list_skin   : 상품리스트를 노출할 스킨을 설정합니다. 스킨위치는 skin/shop/쇼핑몰설정스킨/type??.skin.php
	// $list_mod	: 1줄에 몇개의 상품을 노출할지를 설정합니다.
	// $list_row	: 상품을 몇줄에 노출할지를 설정합니다.
	// $img_width   : 상품이미지의 폭을 설정합니다.
	// $img_height  : 상품이미지의 높이을 설정합니다. 0 으로 설정하는 경우 썸네일 이미지의 높이는 폭에 비례하여 생성합니다.
	//function __construct($type=0, $list_skin='', $list_mod='', $list_row='', $img_width='', $img_height=0, $ca_id='') {
	function __construct($list_skin='', $list_mod='', $list_row='', $img_width='', $img_height=0, $sch_que='') {
		$this->list_skin  = $list_skin;
		$this->list_mod   = $list_mod;
		$this->list_row   = $list_row;
		$this->img_width  = $img_width;
		$this->img_height = $img_height;
		$this->sch_que	= $sch_que;
		$this->set_href(G5_SHOP_URL.'/item.php?it_id=', G5_SHOP_URL.'/auction.php?it_id=');
		$this->count++;
	}

	function set_type($type) {
		$this->type = $type;
		if ($type) {
			$this->set_list_skin($list_skin);
			$this->set_list_mod($list_mod);
			$this->set_list_row($list_row);
			$this->set_img_size($img_width);
		}
	}

	// 분류코드로 검색을 하고자 하는 경우 아래와 같이 인수를 넘겨줍니다.
	// 1단계 분류는 (분류코드, 1)
	// 2단계 분류는 (분류코드, 2)
	// 3단계 분류는 (분류코드, 3)
	function set_category($ca_id, $level=1) {
		if ($level == 2) {
			$this->ca_id2 = $ca_id;
		} else if ($level == 3) {
			$this->ca_id3 = $ca_id;
		} else {
			$this->ca_id = $ca_id;
		}
	}

	// 이벤트코드를 인수로 넘기게 되면 해당 이벤트에 속한 상품을 노출합니다.
	function set_event($ev_id) {
		$this->event = $ev_id;
	}

	// 리스트 스킨을 바꾸고자 하는 경우에 사용합니다.
	// 리스트 스킨의 위치는 skin/shop/쇼핑몰설정스킨/type??.skin.php 입니다.
	// 특별히 설정하지 않는 경우 상품유형을 사용하는 경우는 쇼핑몰설정 값을 그대로 따릅니다.
	function set_list_skin($list_skin) {
		global $default;
		if ($this->is_mobile) {
			$this->list_skin = $list_skin ? $list_skin : $default['de_mobile_type'.$this->type.'_list_skin'];
		} else {
			$this->list_skin = $list_skin ? $list_skin : $default['de_type'.$this->type.'_list_skin'];
		}
	}

	// 1줄에 몇개를 노출할지를 사용한다.
	// 특별히 설정하지 않는 경우 상품유형을 사용하는 경우는 쇼핑몰설정 값을 그대로 따릅니다.
	function set_list_mod($list_mod) {
		global $default;
		if ($this->is_mobile) {
			$this->list_mod = $list_mod ? $list_mod : $default['de_mobile_type'.$this->type.'_list_mod'];
		} else {
			$this->list_mod = $list_mod ? $list_mod : $default['de_type'.$this->type.'_list_mod'];
		}
	}

	// 몇줄을 노출할지를 사용한다.
	// 특별히 설정하지 않는 경우 상품유형을 사용하는 경우는 쇼핑몰설정 값을 그대로 따릅니다.
	function set_list_row($list_row) {
		global $default;
		if ($this->is_mobile) {
			$this->list_row = $list_row ? $list_row : $default['de_mobile_type'.$this->type.'_list_row'];
		} else {
			$this->list_row = $list_row ? $list_row : $default['de_type'.$this->type.'_list_row'];
		}
		if (!$this->list_row)
			$this->list_row = 1;
	}

	// 노출이미지(썸네일생성)의 폭, 높이를 설정합니다. 높이를 0 으로 설정하는 경우 쎰네일 비율에 따릅니다.
	// 특별히 설정하지 않는 경우 상품유형을 사용하는 경우는 쇼핑몰설정 값을 그대로 따릅니다.
	function set_img_size($img_width, $img_height=0) {
		global $default;
		if ($this->is_mobile) {
			$this->img_width = $img_width ? $img_width : $default['de_mobile_type'.$this->type.'_img_width'];
			$this->img_height = $img_height ? $img_height : $default['de_mobile_type'.$this->type.'_img_height'];
		} else {
			$this->img_width = $img_width ? $img_width : $default['de_type'.$this->type.'_img_width'];
			$this->img_height = $img_height ? $img_height : $default['de_type'.$this->type.'_img_height'];
		}
	}

	// 특정 필드만 select 하는 경우에는 필드명을 , 로 구분하여 "field1, field2, field3, ... fieldn" 으로 인수를 넘겨줍니다.
	function set_fields($str) {
		$this->fields = $str;
	}

	// 특정 필드로 정렬을 하는 경우 필드와 정렬순서를 , 로 구분하여 "field1 desc, field2 asc, ... fieldn desc " 으로 인수를 넘겨줍니다.
	function set_order_by($str) {
		$this->order_by = $str;
	}

	// 사용하는 상품외에 모든 상품을 노출하려면 0 을 인수로 넘겨줍니다.
	function set_use($use) {
		$this->use = $use;
	}

	// 모바일로 사용하려는 경우 true 를 인수로 넘겨줍니다.
	function set_mobile($mobile=true) {
		$this->is_mobile = $mobile;
	}

	// 스킨에서 특정 필드를 노출하거나 하지 않게 할수 있습니다.
	// 가령 소비자가는 처음에 노출되지 않도록 설정되어 있지만 노출을 하려면
	// ("it_cust_price", true) 와 같이 인수를 넘겨줍니다.
	// 이때 인수로 넘겨주는 값은 스킨에 정의된 필드만 가능하다는 것입니다.
	function set_view($field, $view=true) {
		$this->{"view_".$field} = $view;
	}

	// anchor 태그에 하이퍼링크를 다른 주소로 걸거나 아예 링크를 걸지 않을 수 있습니다.
	// 인수를 "" 공백으로 넘기면 링크를 걸지 않습니다.
	function set_href($href, $auc_href='') {
		$this->href = $href;
		$this->auc_href = $auc_href;
	}

	// ul 태그의 css 를 교체할수 있다. "sct sct_abc" 를 인수로 넘기게 되면
	// 기존의 ul 태그에 걸린 css 는 무시되며 인수로 넘긴 css 가 사용됩니다.
	function set_css($css) {
		$this->css = $css;
	}

	// 페이지를 노출하기 위해 true 로 설정할때 사용합니다.
	function set_is_page($is_page) {
		$this->is_page = $is_page;
	}

	// select ... limit 의 시작값
	function set_from_record($from_record) {
		$this->from_record = $from_record;
	}

	// 외부에서 쿼리문을 넘겨줄 경우에 담아둡니다.
	function set_query($query) {
		$this->query = $query;
	}

	// class 에 설정된 값으로 최종 실행합니다.
	function run() {

		global $g5, $config, $member, $default, $sch_que;

		if ($this->query) {

			$sql = $this->query;
			$result = sql_query($sql);
			$this->total_count = @mysql_num_rows($result);

		} else {

			$where = array();
			if ($this->use) {
				$where[] = " it_use = '1' ";
			}

			if ($this->type) {
				$where[] = " it_type{$this->type} = '1' ";
			}

			if ($this->ca_id || $this->ca_id2 || $this->ca_id3) {
				$where_ca_id = array();
				if ($this->ca_id) {
					$where_ca_id[] = " ca_id like '{$this->ca_id}%' ";
				}
				if ($this->ca_id2) {
					$where_ca_id[] = " ca_id2 like '{$this->ca_id2}%' ";
				}
				if ($this->ca_id3) {
					$where_ca_id[] = " ca_id3 like '{$this->ca_id3}%' ";
				}
				$where[] = " (".implode(" or ", $where_ca_id).") ";
			}

			if ($this->order_by) {
				$sql_order = " order by {$this->order_by} ";
			}

			if ($this->event) {
				$sql_select = " select {$this->fields} ";
				$sql_common = " from `{$g5['g5_shop_event_item_table']}` a left join `{$g5['g5_shop_item_table']}` b on (a.it_id = b.it_id) ";
				$where[] = " a.ev_id = '{$this->event}' ";
			} else {
				$sql_select = " select {$this->fields} ";
				$sql_common = " from `{$g5['g5_shop_item_table']}` ";
			}
			$sql_where = " where " . implode(" and ", $where) . $sch_que;
			$sql_limit = " limit " . $this->from_record . " , " . ($this->list_mod * $this->list_row);

			$sql = $sql_select . $sql_common . $sql_where . $sql_order . $sql_limit;

			$result = sql_query($sql);

			if ($this->is_page) {
				$sql2 = " select count(*) as cnt " . $sql_common . $sql_where;
				$row2 = sql_fetch($sql2);
				$this->total_count = $row2['cnt'];
			}

		}

		if ($this->is_mobile) {
			$file = G5_MSHOP_SKIN_PATH."/{$this->list_skin}";
		} else {
			$file = G5_SHOP_SKIN_PATH."/{$this->list_skin}";
		}

		if ($this->list_skin == "") {
			return $this->count."번 item_list() 의 스킨파일이 지정되지 않았습니다.";
		} else if (!file_exists($file)) {
			return $file." 파일을 찾을 수 없습니다.";
		} else {
			ob_start();
			$list_mod = $this->list_mod;
			include($file);
			$content = ob_get_contents();
			ob_end_clean();
			return $content;
		}
	}
}


//경매진행중인 상품목록
class auction_list
{
	// 상품유형 : 기본적으로 1~5 까지 사용할수 있으며 0 으로 설정하는 경우 상품유형별로 노출하지 않습니다.
	// 분류나 이벤트로 노출하는 경우 상품유형을 0 으로 설정하면 됩니다.
	protected $ac_yn = 'Y';
	protected $type;

	protected $list_skin;
	protected $list_mod;
	protected $list_row;
	protected $img_width;
	protected $img_height;

	// 상품상세보기 경로
	protected $href = "";
	protected $auc_href = "";

	// select 에 사용되는 필드
	protected $fields = "*";

	// 분류코드로만 사용하는 경우 상품유형($type)을 0 으로 설정하면 됩니다.
	protected $ca_id = "";
	protected $ca_id2 = "";
	protected $ca_id3 = "";

	// 노출순서
	protected $order_by = "";

	// 상품의 이벤트번호를 저장합니다.
	protected $event = "";

	// 스킨의 기본 css 를 다른것으로 사용하고자 할 경우에 사용합니다.
	protected $css = "";

	// 상품의 사용여부를 따져 노출합니다. 0 인 경우 모든 상품을 노출합니다.
	protected $use = 1;

	// 모바일에서 노출하고자 할 경우에 true 로 설정합니다.
	protected $is_mobile = false;

	// 기본으로 보여지는 필드들
	protected $view_it_id	= false;	   // 상품코드
	protected $view_it_img   = true;		// 상품이미지
	protected $view_it_name  = true;		// 상품명
	protected $view_it_basic = true;		// 기본설명
	protected $view_it_price = true;		// 판매가격
	protected $view_it_cust_price = false;  // 소비자가
	protected $view_it_icon = false;		// 아이콘
	protected $view_sns = false;			// SNS

	// 몇번째 class 호출인지를 저장합니다.
	protected $count = 0;

	// true 인 경우 페이지를 구한다.
	protected $is_page = false;

	// 페이지 표시를 위하여 총 상품수를 구합니다.
	public $total_count = 0;

	// sql limit 의 시작 레코드
	protected $from_record = 0;

	// 외부에서 쿼리문을 넘겨줄 경우에 담아두는 변수
	protected $query = "";

	// 검색 데이터
	protected $sch_que = "";


	// $type		: 상품유형 (기본으로 1~5까지 사용)
	// $list_skin   : 상품리스트를 노출할 스킨을 설정합니다. 스킨위치는 skin/shop/쇼핑몰설정스킨/type??.skin.php
	// $list_mod	: 1줄에 몇개의 상품을 노출할지를 설정합니다.
	// $list_row	: 상품을 몇줄에 노출할지를 설정합니다.
	// $img_width   : 상품이미지의 폭을 설정합니다.
	// $img_height  : 상품이미지의 높이을 설정합니다. 0 으로 설정하는 경우 썸네일 이미지의 높이는 폭에 비례하여 생성합니다.
	//function __construct($type=0, $list_skin='', $list_mod='', $list_row='', $img_width='', $img_height=0, $ca_id='') {
	function __construct($list_skin='', $list_mod='', $list_row='', $img_width='', $img_height=0, $sch_que='') {
		$this->list_skin  = $list_skin;
		$this->list_mod   = $list_mod;
		$this->list_row   = $list_row;
		$this->img_width  = $img_width;
		$this->img_height = $img_height;
		$this->sch_que	= $sch_que;
		$this->set_href(G5_SHOP_URL.'/auction.php?gp_id=', G5_SHOP_URL.'/item.php?it_id=');
		$this->count++;
	}


	function set_acyn($ac_yn) {
		$this->ac_yn = $ac_yn;
	}
	
	function set_type($type) {
		$this->type = $type;
		if ($type) {
			$this->set_list_skin($list_skin);
			$this->set_list_mod($list_mod);
			$this->set_list_row($list_row);
			$this->set_img_size($img_width);
		}
	}

	// 분류코드로 검색을 하고자 하는 경우 아래와 같이 인수를 넘겨줍니다.
	// 1단계 분류는 (분류코드, 1)
	// 2단계 분류는 (분류코드, 2)
	// 3단계 분류는 (분류코드, 3)
	function set_category($ca_id, $level=1) {
		if ($level == 2) {
			$this->ca_id2 = $ca_id;
		} else if ($level == 3) {
			$this->ca_id3 = $ca_id;
		} else {
			$this->ca_id = $ca_id;
		}
	}

	// 이벤트코드를 인수로 넘기게 되면 해당 이벤트에 속한 상품을 노출합니다.
	function set_event($ev_id) {
		$this->event = $ev_id;
	}

	// 리스트 스킨을 바꾸고자 하는 경우에 사용합니다.
	// 리스트 스킨의 위치는 skin/shop/쇼핑몰설정스킨/type??.skin.php 입니다.
	// 특별히 설정하지 않는 경우 상품유형을 사용하는 경우는 쇼핑몰설정 값을 그대로 따릅니다.
	function set_list_skin($list_skin) {
		global $default;
		if ($this->is_mobile) {
			$this->list_skin = $list_skin ? $list_skin : $default['de_mobile_type'.$this->type.'_list_skin'];
		} else {
			$this->list_skin = $list_skin ? $list_skin : $default['de_type'.$this->type.'_list_skin'];
		}
	}

	// 1줄에 몇개를 노출할지를 사용한다.
	// 특별히 설정하지 않는 경우 상품유형을 사용하는 경우는 쇼핑몰설정 값을 그대로 따릅니다.
	function set_list_mod($list_mod) {
		global $default;
		if ($this->is_mobile) {
			$this->list_mod = $list_mod ? $list_mod : $default['de_mobile_type'.$this->type.'_list_mod'];
		} else {
			$this->list_mod = $list_mod ? $list_mod : $default['de_type'.$this->type.'_list_mod'];
		}
	}

	// 몇줄을 노출할지를 사용한다.
	// 특별히 설정하지 않는 경우 상품유형을 사용하는 경우는 쇼핑몰설정 값을 그대로 따릅니다.
	function set_list_row($list_row) {
		global $default;
		if ($this->is_mobile) {
			$this->list_row = $list_row ? $list_row : $default['de_mobile_type'.$this->type.'_list_row'];
		} else {
			$this->list_row = $list_row ? $list_row : $default['de_type'.$this->type.'_list_row'];
		}
		if (!$this->list_row)
			$this->list_row = 1;
	}

	// 노출이미지(썸네일생성)의 폭, 높이를 설정합니다. 높이를 0 으로 설정하는 경우 쎰네일 비율에 따릅니다.
	// 특별히 설정하지 않는 경우 상품유형을 사용하는 경우는 쇼핑몰설정 값을 그대로 따릅니다.
	function set_img_size($img_width, $img_height=0) {
		global $default;
		if ($this->is_mobile) {
			$this->img_width = $img_width ? $img_width : $default['de_mobile_type'.$this->type.'_img_width'];
			$this->img_height = $img_height ? $img_height : $default['de_mobile_type'.$this->type.'_img_height'];
		} else {
			$this->img_width = $img_width ? $img_width : $default['de_type'.$this->type.'_img_width'];
			$this->img_height = $img_height ? $img_height : $default['de_type'.$this->type.'_img_height'];
		}
	}

	// 특정 필드만 select 하는 경우에는 필드명을 , 로 구분하여 "field1, field2, field3, ... fieldn" 으로 인수를 넘겨줍니다.
	function set_fields($str) {
		$this->fields = $str;
	}

	// 특정 필드로 정렬을 하는 경우 필드와 정렬순서를 , 로 구분하여 "field1 desc, field2 asc, ... fieldn desc " 으로 인수를 넘겨줍니다.
	function set_order_by($str) {
		$this->order_by = $str;
	}

	// 사용하는 상품외에 모든 상품을 노출하려면 0 을 인수로 넘겨줍니다.
	function set_use($use) {
		$this->use = $use;
	}

	// 모바일로 사용하려는 경우 true 를 인수로 넘겨줍니다.
	function set_mobile($mobile=true) {
		$this->is_mobile = $mobile;
	}

	// 스킨에서 특정 필드를 노출하거나 하지 않게 할수 있습니다.
	// 가령 소비자가는 처음에 노출되지 않도록 설정되어 있지만 노출을 하려면
	// ("it_cust_price", true) 와 같이 인수를 넘겨줍니다.
	// 이때 인수로 넘겨주는 값은 스킨에 정의된 필드만 가능하다는 것입니다.
	function set_view($field, $view=true) {
		$this->{"view_".$field} = $view;
	}

	// anchor 태그에 하이퍼링크를 다른 주소로 걸거나 아예 링크를 걸지 않을 수 있습니다.
	// 인수를 "" 공백으로 넘기면 링크를 걸지 않습니다.
	function set_href($href, $auc_href='') {
		$this->href = $href;
		$this->auc_href = $auc_href;
	}

	// ul 태그의 css 를 교체할수 있다. "sct sct_abc" 를 인수로 넘기게 되면
	// 기존의 ul 태그에 걸린 css 는 무시되며 인수로 넘긴 css 가 사용됩니다.
	function set_css($css) {
		$this->css = $css;
	}

	// 페이지를 노출하기 위해 true 로 설정할때 사용합니다.
	function set_is_page($is_page) {
		$this->is_page = $is_page;
	}

	// select ... limit 의 시작값
	function set_from_record($from_record) {
		$this->from_record = $from_record;
	}

	// 외부에서 쿼리문을 넘겨줄 경우에 담아둡니다.
	function set_query($query) {
		$this->query = $query;
	}

	// class 에 설정된 값으로 최종 실행합니다.
	function run() {

		global $g5, $config, $member, $default, $sch_que, $sql_auction_item, $mode;

		if ($this->query) {

			$sql = $this->query;
			$result = sql_query($sql);
			$this->total_count = @mysql_num_rows($result);

		} else {

			$where = array();
			if ($this->ac_yn) {
				$where[] = " T.ac_yn = '{$this->ac_yn}' ";
			}

			if ($this->type) {
				$where[] = " it_type{$this->type} = '1' ";
			}

			if ($this->order_by) {
				$sql_order = " order by {$this->order_by} ";
			}

			$sql_auction_item = str_replace('#상품기본조건#', " AND ac_yn = 'Y'  ", $sql_auction_item);
			$it_result = sql_query($sql_auction_item);
			$sql_limit = " limit " . $this->from_record . " , " . ($this->list_mod * $this->list_row);
			
			$sql = $sql_auction_item ." AND ".implode("AND", $where). $sql_order . $sql_limit;
			
			if($mode == 'jhw') {
				echo "<textarea>".$sql."</textarea>";
			}
			$result = sql_query($sql);

			if ($this->is_page) {
				$this->total_count = mysql_num_rows(sql_query($sql_auction_item));
			}
		}

		if ($this->is_mobile) {
			$file = G5_MSHOP_SKIN_PATH."/{$this->list_skin}";
		} else {
			$file = G5_SHOP_SKIN_PATH."/{$this->list_skin}";
		}

		if ($this->list_skin == "") {
			return $this->count."번 item_list() 의 스킨파일이 지정되지 않았습니다.";
		} else if (!file_exists($file)) {
			return $file." 파일을 찾을 수 없습니다.";
		} else {
			ob_start();
			$list_mod = $this->list_mod;
			include($file);
			$content = ob_get_contents();
			ob_end_clean();
			return $content;
		}
	}
}




/* 공동구매 상품목록 추출 함수  */
class group_purchase_list
{
	// 상품유형 : 기본적으로 1~5 까지 사용할수 있으며 0 으로 설정하는 경우 상품유형별로 노출하지 않습니다.
	// 분류나 이벤트로 노출하는 경우 상품유형을 0 으로 설정하면 됩니다.
	protected $type;

	protected $list_skin;
	protected $list_mod;
	protected $list_row;
	protected $img_width;
	protected $img_height;

	// 상품상세보기 경로
	protected $href = "";

	// select 에 사용되는 필드
	protected $fields = "GP.*,	AC.gc_state AS all_gc_state ";	/* 경매상품정보 + 경매진행여부값 by.JHW */

	// 분류코드로만 사용하는 경우 상품유형($type)을 0 으로 설정하면 됩니다.
	protected $ca_id = "";
	protected $ca_id2 = "";
	protected $ca_id3 = "";

	// 노출순서
	protected $order_by = "gp_order, gp_id desc";

	// 상품의 이벤트번호를 저장합니다.
	protected $event = "";

	// 스킨의 기본 css 를 다른것으로 사용하고자 할 경우에 사용합니다.
	protected $css = "";

	// 상품의 사용여부를 따져 노출합니다. 0 인 경우 모든 상품을 노출합니다.
	protected $use = 1;

	// 모바일에서 노출하고자 할 경우에 true 로 설정합니다.
	protected $is_mobile = false;

	// 기본으로 보여지는 필드들
	protected $view_it_id	= false;	   // 상품코드
	protected $view_it_img   = true;		// 상품이미지
	protected $view_gc_state = true;		// 공구진행상태
	protected $view_it_name  = true;		// 상품명
	protected $view_it_basic = true;		// 기본설명
	protected $view_it_price = true;		// 판매가격
	protected $view_it_cust_price = false;  // 소비자가
	protected $view_it_icon = false;		// 아이콘
	protected $view_sns = false;			// SNS

	// 몇번째 class 호출인지를 저장합니다.
	protected $count = 0;

	// true 인 경우 페이지를 구한다.
	protected $is_page = false;

	// 페이지 표시를 위하여 총 상품수를 구합니다.
	public $total_count = 0;

	// sql limit 의 시작 레코드
	protected $from_record = 0;

	// 외부에서 쿼리문을 넘겨줄 경우에 담아두는 변수
	protected $query = "";

	// 검색 데이터
	protected $sch_que = "";

	// 페이지당 리스트 갯수
	protected $listnum = "";


	// $type		: 상품유형 (기본으로 1~5까지 사용)
	// $list_skin   : 상품리스트를 노출할 스킨을 설정합니다. 스킨위치는 skin/shop/쇼핑몰설정스킨/type??.skin.php
	// $list_mod	: 1줄에 몇개의 상품을 노출할지를 설정합니다.
	// $list_row	: 상품을 몇줄에 노출할지를 설정합니다.
	// $img_width   : 상품이미지의 폭을 설정합니다.
	// $img_height  : 상품이미지의 높이을 설정합니다. 0 으로 설정하는 경우 썸네일 이미지의 높이는 폭에 비례하여 생성합니다.
	//function __construct($type=0, $list_skin='', $list_mod='', $list_row='', $img_width='', $img_height=0, $ca_id='') {
	function __construct($list_skin='', $list_mod='', $list_row='', $img_width='', $img_height=0, $sch_que='', $listnum='') {
		$this->list_skin  = $list_skin;
		$this->list_mod   = $list_mod;
		$this->list_row   = $list_row;
		$this->img_width  = $img_width;
		$this->img_height = $img_height;
		$this->sch_que	= $sch_que;
		$this->listnum	= $listnum;
		$this->set_href(G5_SHOP_URL.'/grouppurchase.php?gp_id=');
		$this->count++;
	}

	function set_type($type) {
		$this->type = $type;
		if ($type) {
			$this->set_list_skin($list_skin);
			$this->set_list_mod($list_mod);
			$this->set_list_row($list_row);
			$this->set_img_size($img_width);
		}
	}

	// 분류코드로 검색을 하고자 하는 경우 아래와 같이 인수를 넘겨줍니다.
	// 1단계 분류는 (분류코드, 1)
	// 2단계 분류는 (분류코드, 2)
	// 3단계 분류는 (분류코드, 3)
	function set_category($ca_id, $level=1) {
		if ($level == 2) {
			$this->ca_id2 = $ca_id;
		} else if ($level == 3) {
			$this->ca_id3 = $ca_id;
		} else {
			$this->ca_id = $ca_id;
		}
	}

	// 이벤트코드를 인수로 넘기게 되면 해당 이벤트에 속한 상품을 노출합니다.
	function set_event($ev_id) {
		$this->event = $ev_id;
	}

	// 리스트 스킨을 바꾸고자 하는 경우에 사용합니다.
	// 리스트 스킨의 위치는 skin/shop/쇼핑몰설정스킨/type??.skin.php 입니다.
	// 특별히 설정하지 않는 경우 상품유형을 사용하는 경우는 쇼핑몰설정 값을 그대로 따릅니다.
	function set_list_skin($list_skin) {
		global $default;
		if ($this->is_mobile) {
			$this->list_skin = $list_skin ? $list_skin : $default['de_mobile_type'.$this->type.'_list_skin'];
		} else {
			$this->list_skin = $list_skin ? $list_skin : $default['de_type'.$this->type.'_list_skin'];
		}
	}

	// 1줄에 몇개를 노출할지를 사용한다.
	// 특별히 설정하지 않는 경우 상품유형을 사용하는 경우는 쇼핑몰설정 값을 그대로 따릅니다.
	function set_list_mod($list_mod) {
		global $default;
		if ($this->is_mobile) {
			$this->list_mod = $list_mod ? $list_mod : $default['de_mobile_type'.$this->type.'_list_mod'];
		} else {
			$this->list_mod = $list_mod ? $list_mod : $default['de_type'.$this->type.'_list_mod'];
		}
	}

	// 몇줄을 노출할지를 사용한다.
	// 특별히 설정하지 않는 경우 상품유형을 사용하는 경우는 쇼핑몰설정 값을 그대로 따릅니다.
	function set_list_row($list_row) {
		global $default;
		if ($this->is_mobile) {
			$this->list_row = $list_row ? $list_row : $default['de_mobile_type'.$this->type.'_list_row'];
		} else {
			$this->list_row = $list_row ? $list_row : $default['de_type'.$this->type.'_list_row'];
		}
		if (!$this->list_row)
			$this->list_row = 1;
	}

	// 노출이미지(썸네일생성)의 폭, 높이를 설정합니다. 높이를 0 으로 설정하는 경우 쎰네일 비율에 따릅니다.
	// 특별히 설정하지 않는 경우 상품유형을 사용하는 경우는 쇼핑몰설정 값을 그대로 따릅니다.
	function set_img_size($img_width, $img_height=0) {
		global $default;
		if ($this->is_mobile) {
			$this->img_width = $img_width ? $img_width : $default['de_mobile_type'.$this->type.'_img_width'];
			$this->img_height = $img_height ? $img_height : $default['de_mobile_type'.$this->type.'_img_height'];
		} else {
			$this->img_width = $img_width ? $img_width : $default['de_type'.$this->type.'_img_width'];
			$this->img_height = $img_height ? $img_height : $default['de_type'.$this->type.'_img_height'];
		}
	}

	// 특정 필드만 select 하는 경우에는 필드명을 , 로 구분하여 "field1, field2, field3, ... fieldn" 으로 인수를 넘겨줍니다.
	function set_fields($str) {
		$this->fields = $str;
	}

	// 특정 필드로 정렬을 하는 경우 필드와 정렬순서를 , 로 구분하여 "field1 desc, field2 asc, ... fieldn desc " 으로 인수를 넘겨줍니다.
	function set_order_by($str) {
		$this->order_by = $str;
	}

	// 사용하는 상품외에 모든 상품을 노출하려면 0 을 인수로 넘겨줍니다.
	function set_use($use) {
		$this->use = $use;
	}

	// 모바일로 사용하려는 경우 true 를 인수로 넘겨줍니다.
	function set_mobile($mobile=true) {
		$this->is_mobile = $mobile;
	}

	// 스킨에서 특정 필드를 노출하거나 하지 않게 할수 있습니다.
	// 가령 소비자가는 처음에 노출되지 않도록 설정되어 있지만 노출을 하려면
	// ("it_cust_price", true) 와 같이 인수를 넘겨줍니다.
	// 이때 인수로 넘겨주는 값은 스킨에 정의된 필드만 가능하다는 것입니다.
	function set_view($field, $view=true) {
		$this->{"view_".$field} = $view;
	}

	// anchor 태그에 하이퍼링크를 다른 주소로 걸거나 아예 링크를 걸지 않을 수 있습니다.
	// 인수를 "" 공백으로 넘기면 링크를 걸지 않습니다.
	function set_href($href) {
		$this->href = $href;
	}

	// ul 태그의 css 를 교체할수 있다. "sct sct_abc" 를 인수로 넘기게 되면
	// 기존의 ul 태그에 걸린 css 는 무시되며 인수로 넘긴 css 가 사용됩니다.
	function set_css($css) {
		$this->css = $css;
	}

	// 페이지를 노출하기 위해 true 로 설정할때 사용합니다.
	function set_is_page($is_page) {
		$this->is_page = $is_page;
	}

	// select ... limit 의 시작값
	function set_from_record($from_record) {
		$this->from_record = $from_record;
	}

	// 외부에서 쿼리문을 넘겨줄 경우에 담아둡니다.
	function set_query($query) {
		$this->query = $query;
	}

	// class 에 설정된 값으로 최종 실행합니다.
	function run() {

		global $g5, $config, $member, $default, $sch_que, $listnum;
		global $개인구매코드, $sql_product;
		global $개발자,$is_admin;		/* 최고관리자일경우에만 임시로 공구신청가능 버튼 오픈 */


		if ($this->query) {
			$sql = $this->query;
			$result = sql_query($sql);
			$this->total_count = @mysql_num_rows($result);

		} else {

			$where = array();

			//품목 사용여부값
//			if ($this->use && substr($_GET[ca_id],0,2) != 'CT') {
			//$where[] = " T.gp_use = '1' ";
//			}

			//$where[] = " ( IF( (GP.jaego - IFNULL(CO.CO_SUM,0)) > 0, (GP.jaego - IFNULL(CO.CO_SUM,0)), 0) != 0 && IF( (GP.jaego - IFNULL(PB.PB_SUM,0)) > 0, (GP.jaego - IFNULL(PB.PB_SUM,0)), 0) != 0) ";
			//개별공구 진행중일경우 별도 공구진행중인 상품코드는 노출안되게,  코인용품(CTSS)은 예외
			if($개인구매코드) {

				$find_sql = "	SELECT	VG.links_itid AS it_id
											FROM		(	SELECT	*
																FROM		gp_info
																ORDER BY reg_date DESC
															) GI
															LEFT JOIN v_gpinfo_links VG ON (VG.gpcode = GI.gpcode)
															LEFT JOIN g5_shop_group_purchase GP ON (GP.gp_id = VG.links_itid)
											WHERE		GI.stats IN ('00','10','20')
											AND			GI.start_date <= NOW()
											AND			GI.end_date >= NOW()
											AND			GP.ca_id != 'CTSS'
											ORDER BY GI.gpcode DESC
				";
				$find_result = sql_query($find_sql);
				$find_cnt = mysql_num_rows($find_result);

				//최소 한개 이상이면 제외코드 삽입
				if($find_cnt > 0) {

					while($arr = mysql_fetch_array($find_result)) {
						$제외코드 .= "'$arr[it_id]',";
					}
					$제외코드 = substr($제외코드, 0, strlen($제외코드)-1);
					$inWhere[] = " gp_id NOT IN ($제외코드) ";
				}
			}


			if ($this->ca_id) {
				$where_ca_id = array();
				if ($this->ca_id) {
					$where_ca_id[] = " ca_id like '{$this->ca_id}%' ";
				}
				$inWhere[] = implode(" or ", $where_ca_id);
			}


			if($_GET[mode] == 'jhw') {
				echo "<textarea>this->caid: ".$this->ca_id." inWhere:".$inWhere."</textarea>";
			}



			if ($this->order_by) {
				$sql_order = " order by {$this->order_by} ";
			}

			if($listnum){
				$listnum = $listnum;
			}else{
				$listnum = ($this->list_mod * $this->list_row);
			}


			/* 코투상품 예외처리 */
			if( substr($_GET[ca_id],0,2) != 'CT' ) {
				$상품가격컬럼 = 'GP.gp_price  AS po_cash_price,';
				$outWhere[] = " 1=1 ";
			}
			else {
				$상품가격컬럼 = 'PB.PB_cash_price  AS po_cash_price,';

				//0원인 상품은 노출 안되게,	재고없는 상품은 노출 안되게 수정
				$outWhere[] = " IF( (T.gp_realprice + T.gp_fixprice + T.gp_price) > 0,TRUE,FALSE) ";
				$outWhere[] = " T.real_jaego > 0 ";
			}


			//제한은 마지막에 걸어서
			$sql_inWhere = " AND " . implode(" and ", $inWhere) . $sch_que;
			$sql_outWhere = " WHERE		" . implode(" and ", $outWhere) . $sch_que;

			$sql_limit = " LIMIT " . $this->from_record . " , " . $listnum;


			$sql_product = str_replace('#상품기본조건#', $sql_inWhere, $sql_product);

			/*
			 CO.CO_SUM : 모든공구와 개별주문등등, 지금까지 총 주문수량  빠른배송상품은 공구, 개별주문의 주문량을 빼줘야됨
			 PB.PB_SUM : 공구에 한정된 총주문수량. 딜러업체의 재고값은 우리가 발주할경우 감소하나 딜러업체가 추가입고시 늘어날수 있음.
			 누적주문량을 빼선 안되고 현재 진행중인 주문량만 빼줘야함
			 */
			$sql_select = "	SELECT	T.*,
															IF(T.real_jaego > 0,T.real_jaego,0) AS real_jaego,
															CASE
																WHEN	T.ca_id LIKE 'CT%' || T.ca_id = 'GP'	THEN
																	CASE
																		WHEN	T.gp_price_type = 'Y'	THEN	/*실시간스팟시세*/
																			CEIL(T.gp_realprice / 100) * 100
																		WHEN	T.gp_price_type = 'N'	THEN	/*고정가달러환율*/
																			CEIL(T.gp_fixprice / 100) * 100
																		WHEN	T.gp_price_type = 'W'	THEN	/*원화적용*/
																			T.gp_price
																		ELSE
																			0
																	END
																ELSE
																	CEIL(IFNULL(T.po_cash_price,T.gp_price) / 100) * 100
															END po_cash_price
			";
			$sql_common = "	FROM		$sql_product	$sql_outWhere	";

			$sql = $sql_select . $sql_common . $sql_where . $sql_order . $sql_limit;

			if($_GET[mode] == 'jhw') {
				echo "<textarea>".$sql."</textarea><br><br><br><br>";
			}


			$result = sql_query($sql);

			if ($this->is_page) {
				$sql2 = " select count(*) as cnt " . $sql_common . $sql_where;
				$row2 = sql_fetch($sql2);
				$this->total_count = $row2['cnt'];
			}

		}

		/* TM*/
		if ($this->is_mobile) {
			$file = G5_MSHOP_SKIN_PATH."/{$this->list_skin}";
		} else {
			$file = G5_SHOP_SKIN_PATH."/{$this->list_skin}";
		}

		if ($this->list_skin == "") {
			return $this->count."번 item_list() 의 스킨파일이 지정되지 않았습니다.";
		} else if (!file_exists($file)) {
			return $file." 파일을 찾을 수 없습니다.";
		} else {
			ob_start();
			$list_mod = $this->list_mod;	/* 노출되는 상품개수 */
			include($file);
			$content = ob_get_contents();
			ob_end_clean();
			return $content;
		}
	}
}


/**********************************************************************************************************/






// 장바구니 건수 검사
function get_cart_count($cart_id)
{
	global $g5, $default;

	$sql = " select count(ct_id) as cnt from {$g5['g5_shop_cart_table']} where od_id = '$cart_id' ";
	if($default['de_cart_keep_term']) {
		$ctime = date('Y-m-d H:i:s', G5_SERVER_TIME - (($default['de_cart_keep_term'] - 1) * 86400));
		$sql .= " and ct_time > '$ctime' ";
	}
	$row = sql_fetch($sql);
	$cnt = (int)$row['cnt'];
	return $cnt;
}


// 이미지를 얻는다
function get_image($img, $width=0, $height=0, $img_id='')
{
	global $g5, $default;

	$full_img = G5_DATA_PATH.'/item/'.$img;

	if (file_exists($full_img) && $img)
	{
		if (!$width)
		{
			$size = getimagesize($full_img);
			$width = $size[0];
			$height = $size[1];
		}
		$str = '<img src="'.G5_DATA_URL.'/item/'.$img.'" alt="" width="'.$width.'" height="'.$height.'"';

		if($img_id)
			$str .= ' id="'.$img_id.'"';

		$str .= '>';
	}
	else
	{
		$str = '<img src="'.G5_SHOP_URL.'/img/no_image.gif" alt="" ';
		if ($width)
			$str .= 'width="'.$width.'" height="'.$height.'"';
		else
			$str .= 'width="'.$default['de_mimg_width'].'" height="'.$default['de_mimg_height'].'"';

		if($img_id)
			$str .= ' id="'.$img_id.'"'.
		$str .= '>';
	}

	return $str;
}


// 상품 이미지를 얻는다
function get_it_image($it_id, $width, $height=0, $anchor=false, $img_id='', $img_alt='')
{
	global $g5;

	if(!$it_id || !$width)
		return '';

	$sql = " select it_id, it_img1, it_img2, it_img3, it_img4, it_img5, it_img6, it_img7, it_img8, it_img9, it_img10 from {$g5['g5_shop_item_table']} where it_id = '$it_id' ";
	$row = sql_fetch($sql);

	if(!$row['it_id'])
		return '';

	for($i=1;$i<=10; $i++) {
		$file = G5_DATA_PATH.'/item/'.$row['it_img'.$i];
		if(is_file($file) && $row['it_img'.$i]) {
			$size = @getimagesize($file);
			if($size[2] < 1 || $size[2] > 3)
				continue;

			$filename = basename($file);
			$filepath = dirname($file);
			$img_width = $size[0];
			$img_height = $size[1];

			break;
		}
	}

	if($img_width && !$height) {
		$height = round(($width * $img_height) / $img_width);
	}

	if($filename) {
		//thumbnail($filename, $source_path, $target_path, $thumb_width, $thumb_height, $is_create, $is_crop=false, $crop_mode='center', $is_sharpen=true, $um_value='80/0.5/3')
		$thumb = thumbnail($filename, $filepath, $filepath, $width, $height, false, false, 'center', true, $um_value='80/0.5/3');
	}

	if($thumb) {
		$file_url = str_replace(G5_PATH, G5_URL, $filepath.'/'.$thumb);
		$img = '<img src="'.$file_url.'" width="'.$width.'" height="'.$height.'" alt="'.$img_alt.'"';
	} else {
		$img = '<img src="'.G5_SHOP_URL.'/img/no_image.gif" width="'.$width.'"';
		if($height)
			$img .= ' height="'.$height.'"';
		$img .= ' alt="'.$img_alt.'"';
	}

	if($img_id)
		$img .= ' id="'.$img_id.'"';
	$img .= '>';

	if($anchor)
		$img = '<a href="'.G5_SHOP_URL.'/item.php?it_id='.$it_id.'">'.$img.'</a>';

	return $img;
}

function get_gp_image($gp_id, $width, $height=0, $anchor=true)
{
	global $g5;

	if(!$gp_id || !$width)
		return '';

	$sql = " select gp_id, gp_img from {$g5['g5_shop_group_purchase_table']} where gp_id = '$gp_id' ";
	$row = sql_fetch($sql);

	if(!$row['gp_id'])
		return '';


	if($row[gp_img]) {
		$file_url = str_replace(G5_PATH, G5_URL, $filepath.'/'.$thumb);
		$img = '<img src="'.$row[gp_img].'" width="'.$width.'" height="'.$height.'"';
	}
	$img .= '>';

	$img = ($anchor) ? '<a href="'.G5_SHOP_URL.'/grouppurchase.php?gp_id='.$gp_id.'">'.$img.'</a>' : $img;

	return $img;
}

function get_gp_image1($gp_id)
{
	global $g5;

	$sql = " select gp_id, gp_name from {$g5['g5_shop_group_purchase_table']} where gp_id = '$gp_id' ";
	$row = sql_fetch($sql);

	if(!$row['gp_id'])
		return '';


	if($row[gp_name]) {
		$param = $row[gp_name];
	}

	$param = '<a href="'.G5_SHOP_URL.'/grouppurchase.php?gp_id='.$gp_id.'">'.$param.'</a>';

	return $param;
}

// 상품이미지 썸네일 생성
function get_it_thumbnail($img, $width, $height=0, $id='')
{
	$str = '';

	$file = G5_DATA_PATH.'/item/'.$img;
	if(is_file($file))
		$size = @getimagesize($file);

	if($size[2] < 1 || $size[2] > 3)
		return '';

	$img_width = $size[0];
	$img_height = $size[1];
	$filename = basename($file);
	$filepath = dirname($file);

	if($img_width && !$height) {
		$height = round(($width * $img_height) / $img_width);
	}

	$thumb = thumbnail($filename, $filepath, $filepath, $width, $height, false, false, 'center', true, $um_value='80/0.5/3');

	if($thumb) {
		$file_url = str_replace(G5_PATH, G5_URL, $filepath.'/'.$thumb);
		$str = '<img src="'.$file_url.'" width="'.$width.'" height="'.$height.'"';
		if($id)
			$str .= ' id="'.$id.'"';
		$str .= ' alt="">';
	}

	return $str;
}

//마우스오버 이미지 확대
function get_it_thumbnail1($img, $width, $height=0, $id='', $num='')
{
	$str = '';
	$file = G5_DATA_PATH.'/item/'.$img;

	if(is_file($file))
		$size = @getimagesize($file);

	if($size[2] < 1 || $size[2] > 3)
		return '';

	$img_width = $size[0];
	$img_height = $size[1];
	$filename = basename($file);
	$filepath = dirname($file);

	if($img_width && !$height) {
		$height = round(($width * $img_height) / $img_width);
	}

	$thumb = thumbnail($filename, $filepath, $filepath, $width, $height, false, false, 'center', true, $um_value='80/0.5/3');

	if($thumb) {
		$file_url = str_replace(G5_PATH, G5_URL, $filepath.'/'.$thumb);
		$str = '<img class="demo'.$num.'" src="'.$file_url.'" width="'.$width.'" height="'.$height.'"';
		if($id)
			$str .= ' id="'.$id.'"';
		$str .= ' alt="">';
	}

	return $str;
}


// 이미지 URL 을 얻는다.
function get_it_imageurl($it_id)
{
	global $g5;

	$sql = " select it_img1, it_img2, it_img3, it_img4, it_img5, it_img6, it_img7, it_img8, it_img9, it_img10
				from {$g5['g5_shop_item_table']}
				where it_id = '$it_id' ";
	$row = sql_fetch($sql);
	$filepath = '';

	for($i=1; $i<=10; $i++) {
		$img = $row['it_img'.$i];
		$file = G5_DATA_PATH.'/item/'.$img;
		if(!is_file($file))
			continue;

		$size = @getimagesize($file);
		if($size[2] < 1 || $size[2] > 3)
			continue;

		$filepath = $file;
	}

	if($filepath)
		$str = str_replace(G5_PATH, G5_URL, $filepath);
	else
		$str = G5_SHOP_URL.'/img/no_image.gif';

	return $str;
}


// 상품의 재고 (창고재고수량 - 주문대기수량)
function get_it_stock_qty($it_id)
{
	global $g5;

	$sql = " select it_stock_qty from {$g5['g5_shop_item_table']} where it_id = '$it_id' ";
	$row = sql_fetch($sql);
	$jaego = (int)$row['it_stock_qty'];

	// 재고에서 빼지 않았고 주문인것만
	$sql = " select SUM(ct_qty) as sum_qty
			   from {$g5['g5_shop_cart_table']}
			  where it_id = '$it_id'
				and io_id = ''
				and ct_stock_use = 0
				and ct_status in ('입금대기', '결제완료', '상품준비중') ";
	$row = sql_fetch($sql);
	$daegi = (int)$row['sum_qty'];

	return $jaego - $daegi;
}


// 옵션의 재고 (창고재고수량 - 주문대기수량)
function get_option_stock_qty($it_id, $io_id, $type)
{
	global $g5;

	$sql = " select io_stock_qty
				from {$g5['g5_shop_item_option_table']}
				where it_id = '$it_id' and io_id = '$io_id' and io_type = '$type' and io_use = '1' ";
	$row = sql_fetch($sql);
	$jaego = (int)$row['io_stock_qty'];

	// 재고에서 빼지 않았고 주문인것만
	$sql = " select SUM(ct_qty) as sum_qty
			   from {$g5['g5_shop_cart_table']}
			  where it_id = '$it_id'
				and io_id = '$io_id'
				and io_type = '$type'
				and ct_stock_use = 0
				and ct_status in ('입금대기', '결제완료', '상품준비중') ";
	$row = sql_fetch($sql);
	$daegi = (int)$row['sum_qty'];

	return $jaego - $daegi;
}


// 큰 이미지
function get_large_image($img, $it_id, $btn_image=true)
{
	global $g5;

	if (file_exists(G5_DATA_PATH.'/item/'.$img) && $img != '')
	{
		$size   = getimagesize(G5_DATA_PATH.'/item/'.$img);
		$width  = $size[0];
		$height = $size[1];
		$str = '<a href="javascript:popup_large_image(\''.$it_id.'\', \''.$img.'\', '.$width.', '.$height.', \''.G5_SHOP_URL.'\')">';
		if ($btn_image)
			$str .= '큰이미지</a>';
	}
	else
		$str = '';
	return $str;
}



function display_price($price, $tel_inq=false)
{
	if ($tel_inq)
		$price = '전화문의';
	else
		$price = number_format($price)." 원";

	return $price;
}

// 금액표시
// $it : 상품 배열
function get_price($it)
{
	global $member,$_SESSION;

	if ($it['it_tel_inq']) return '전화문의';


	if($it['it_price_type']=="Y"){
		if($it[it_metal_type]=="EC")$price = $it[it_metal_etc_price] * $_SESSION[unit_kor_duty] * $it[it_metal_don];
		else $price =$_SESSION['metalUsdPrice'][$it[it_metal_type]] * $_SESSION[unit_kor_duty] * $it[it_metal_don];

		if($it[ca_id]=="1010" || $it[ca_id2]=="1010" || $it[ca_id3]=="1010")$price = $price*1.05;
		else $price = $price*1.06;


		if($it[it_real_add_unit]=="W")$price = $price + $it[it_real_add_price];
		elseif($it[it_real_add_unit]=="P")$price = CeilGe($price * (1+($it[it_real_add_price]/100)));
		elseif($it[it_real_add_unit]=="D")$price = CeilGe($price + ($it[it_real_add_price] * $_SESSION[unit_kor_duty]));

		$price = CeilGe($price*1.1);

	}elseif($it['it_price_type']=="U"){

		$siteRealPrice = GetSiteBasicPrice($it);

		if (!$siteRealPrice) return '0';

		// 금일경우 5% 나머지 6% 수수료
		if($it[ca_id]=="1010" || $it[ca_id2]=="1010" || $it[ca_id3]=="1010")$price = $siteRealPrice* $_SESSION[unit_kor_duty]*1.05;
		else $price = $siteRealPrice* $_SESSION[unit_kor_duty]*1.06;

		if($it[it_real_add_unit]=="W")$price = $price + $it[it_real_add_price];
		elseif($it[it_real_add_unit]=="P")$price = CeilGe($price * (1+($it[it_real_add_price]/100)));
		elseif($it[it_real_add_unit]=="D")$price = CeilGe($price + ($it[it_real_add_price] * $_SESSION[unit_kor_duty]));


		$price = CeilGe($price*1.1);


	}else $price = $it['it_price'];

	return $price;
}

// $it : 상품 배열($)
function get_dollar($it)
{
	global $member,$_SESSION;

	if ($it['it_tel_inq']) return '전화문의';


	if($it['it_price_type']=="Y"){
		if($it[it_metal_type]=="EC")$price = $it[it_metal_etc_price] * $it[it_metal_don];
		else $price =$_SESSION['metalUsdPrice'][$it[it_metal_type]] * $it[it_metal_don];

		$price = $price + $it[it_real_add_price];

	}elseif($it['it_price_type']=="U"){

		$siteRealPrice = GetSiteBasicPrice($it);

		if (!$siteRealPrice) return '0';

		// 금일경우 5% 나머지 6% 수수료
		if($it[ca_id]=="1010" || $it[ca_id2]=="1010" || $it[ca_id3]=="1010")$price = $siteRealPrice;
		else $price = $siteRealPrice;

		$price = $price;

	}

	return $price;
}


// 포인트 표시
function display_point($point)
{
	return number_format($point, 0).'점';
}


// 포인트를 구한다
function get_point($amount, $point)
{
	return (int)($amount * $point / 100);
}


// HTML 특수문자 변환 htmlspecialchars
function htmlspecialchars2($str)
{
	$trans = array("\"" => "&#034;", "'" => "&#039;", "<"=>"&#060;", ">"=>"&#062;");
	$str = strtr($str, $trans);
	return $str;
}


// 상품이미지 업로드
function it_img_upload($srcfile, $filename, $dir)
{
	if($filename == '')
		return '';

	$size = @getimagesize($srcfile);
	if($size[2] < 1 || $size[2] > 3)
		return '';

	if(!is_dir($dir)) {
		@mkdir($dir, G5_DIR_PERMISSION);
		@chmod($dir, G5_DIR_PERMISSION);
	}

	$filename = preg_replace("/\s+/", "", $filename);
	$filename = preg_replace("/[#\&\+\-%@=\/\\:;,'\"\^`~\|\!\?\*\$#<>\(\)\[\]\{\}]/", "", $filename);

	$filename = preg_replace_callback(
						  "/[가-힣]+/",
						  create_function('$matches', 'return base64_encode($matches[0]);'),
						  $filename);

	upload_file($srcfile, $filename, $dir);

	$file = str_replace(G5_DATA_PATH.'/item/', '', $dir.'/'.$filename);

	return $file;
}


// 파일을 업로드 함
function upload_file($srcfile, $destfile, $dir)
{
	if ($destfile == "") return false;
	// 업로드 한후 , 퍼미션을 변경함
	@move_uploaded_file($srcfile, $dir.'/'.$destfile);
	@chmod($dir.'/'.$destfile, G5_FILE_PERMISSION);
	return true;
}


function message($subject, $content, $align="left", $width="450")
{
	$str = "
		<table width=\"$width\" cellpadding=\"4\" align=\"center\">
			<tr><td class=\"line\" height=\"1\"></td></tr>
			<tr>
				<td align=\"center\">$subject</td>
			</tr>
			<tr><td class=\"line\" height=\"1\"></td></tr>
			<tr>
				<td>
					<table width=\"100%\" cellpadding=\"8\" cellspacing=\"0\">
						<tr>
							<td class=\"leading\" align=\"$align\">$content</td>
						</tr>
					</table>
				</td>
			</tr>
			<tr><td class=\"line\" height=\"1\"></td></tr>
		</table>
		<br>
		";
	return $str;
}


// 시간이 비어 있는지 검사
function is_null_time($datetime)
{
	// 공란 0 : - 제거
	//$datetime = ereg_replace("[ 0:-]", "", $datetime); // 이 함수는 PHP 5.3.0 에서 배제되고 PHP 6.0 부터 사라집니다.
	$datetime = preg_replace("/[ 0:-]/", "", $datetime);
	if ($datetime == "")
		return true;
	else
		return false;
}


// 출력유형, 스킨파일, 1라인이미지수, 총라인수, 이미지폭, 이미지높이
// 1.02.01 $ca_id 추가
//function display_type($type, $skin_file, $list_mod, $list_row, $img_width, $img_height, $ca_id="")
function display_type($type, $list_skin='', $list_mod='', $list_row='', $img_width='', $img_height='', $ca_id='')
{
	global $member, $g5, $config, $default;

	if (!$default["de_type{$type}_list_use"]) return "";

	$list_skin  = $list_skin  ? $list_skin  : $default["de_type{$type}_list_skin"];
	$list_mod   = $list_mod   ? $list_mod   : $default["de_type{$type}_list_mod"];
	$list_row   = $list_row   ? $list_row   : $default["de_type{$type}_list_row"];
	$img_width  = $img_width  ? $img_width  : $default["de_type{$type}_img_width"];
	$img_height = $img_height ? $img_height : $default["de_type{$type}_img_height"];

	// 상품수
	$items = $list_mod * $list_row;

	// 1.02.00
	// it_order 추가
	$sql = " select * from {$g5['g5_shop_item_table']} where it_use = '1' and it_type{$type} = '1' ";
	if ($ca_id) $sql .= " and ca_id like '$ca_id%' ";
	$sql .= " order by it_order, it_id desc limit $items ";
	$result = sql_query($sql);
	/*
	if (!mysql_num_rows($result)) {
		return false;
	}
	*/

	//$file = G5_SHOP_PATH.'/'.$skin_file;
	$file = G5_SHOP_SKIN_PATH.'/'.$list_skin;
	if (!file_exists($file)) {
		return G5_SHOP_SKIN_URL.'/'.$list_skin.' 파일을 찾을 수 없습니다.';
	} else {
		$td_width = (int)(100 / $list_mod);
		ob_start();
		include $file;
		$content = ob_get_contents();
		ob_end_clean();
		return $content;
	}
}


// 모바일 유형별 상품 출력
function mobile_display_type($type, $skin_file, $list_row, $img_width, $img_height, $ca_id="")
{
	global $member, $g5, $config;

	// 상품수
	$items = $list_row;

	// 1.02.00
	// it_order 추가
	$sql = " select * from {$g5['g5_shop_item_table']} where it_use = '1' and it_type{$type} = '1' ";
	if ($ca_id) $sql .= " and ca_id like '$ca_id%' ";
	$sql .= " order by it_order, it_id desc limit $items ";
	$result = sql_query($sql);
	/*
	if (!mysql_num_rows($result)) {
		return false;
	}
	*/

	$file = G5_MSHOP_PATH.'/'.$skin_file;
	if (!file_exists($file)) {
		echo $file.' 파일을 찾을 수 없습니다.';
	} else {
		//$td_width = (int)(100 / $list_mod);
		include $file;
	}
}


// 분류별 출력
// 스킨파일번호, 1라인이미지수, 총라인수, 이미지폭, 이미지높이 , 분류번호
function display_category($no, $list_mod, $list_row, $img_width, $img_height, $ca_id="")
{
	global $member, $g5;

	// 상품수
	$items = $list_mod * $list_row;

	$sql = " select * from {$g5['g5_shop_item_table']} where it_use = '1'";
	if ($ca_id)
		$sql .= " and ca_id LIKE '{$ca_id}%' ";
	$sql .= " order by it_order, it_id desc limit $items ";
	$result = sql_query($sql);
	if (!mysql_num_rows($result)) {
		return false;
	}

	$file = G5_SHOP_PATH.'/maintype'.$no.'.inc.php';
	if (!file_exists($file)) {
		echo $file.' 파일을 찾을 수 없습니다.';
	} else {
		$td_width = (int)(100 / $list_mod);
		include $file;
	}
}


// 별
function get_star($score)
{
	$star = round($score);
	if ($star > 5) $star = 5;
	else if ($star < 0) $star = 0;

	return $star;
}


// 별 이미지
function get_star_image($it_id)
{
	global $g5;

	$sql = "select (SUM(is_score) / COUNT(*)) as score from {$g5['g5_shop_item_use_table']} where it_id = '$it_id' ";
	$row = sql_fetch($sql);

	return (int)get_star($row['score']);
}


// 메일 보내는 내용을 HTML 형식으로 만든다.
function email_content($str)
{
	global $g5;

	$s = "";
	$s .= "<html><head><meta http-equiv=\"content-type\" content=\"text/html; charset={$g5['charset']}\"><title>메일</title>\n";
	$s .= "<body>\n";
	$s .= $str;
	$s .= "</body>\n";
	$s .= "</html>";

	return $s;
}


// 타임스탬프 형식으로 넘어와야 한다.
// 시작시간, 종료시간
function gap_time($begin_time, $end_time)
{
	$gap = $end_time - $begin_time;
	$time['days']	= (int)($gap / 86400);
	$time['hours']   = (int)(($gap - ($time['days'] * 86400)) / 3600);
	$time['minutes'] = (int)(($gap - ($time['days'] * 86400 + $time['hours'] * 3600)) / 60);
	$time['seconds'] = (int)($gap - ($time['days'] * 86400 + $time['hours'] * 3600 + $time['minutes'] * 60));
	return $time;
}


// 공란없이 이어지는 문자 자르기 (wayboard 참고 (way.co.kr))
function continue_cut_str($str, $len=80)
{
	/*
	$pattern = "[^ \n<>]{".$len."}";
	return eregi_replace($pattern, "\\0\n", $str);
	*/
	$pattern = "/[^ \n<>]{".$len."}/";
	return preg_replace($pattern, "\\0\n", $str);
}


// 제목별로 컬럼 정렬하는 QUERY STRING
// $type 이 1이면 반대
function title_sort($col, $type=0)
{
	global $sort1, $sort2;
	global $_SERVER;
	global $page;
	global $doc;

	$q1 = 'sort1='.$col;
	if ($type) {
		$q2 = 'sort2=desc';
		if ($sort1 == $col) {
			if ($sort2 == 'desc') {
				$q2 = 'sort2=asc';
			}
		}
	} else {
		$q2 = 'sort2=asc';
		if ($sort1 == $col) {
			if ($sort2 == 'asc') {
				$q2 = 'sort2=desc';
			}
		}
	}
	#return "$_SERVER[PHP_SELF]?$q1&amp;$q2&amp;page=$page";
	return "{$_SERVER['PHP_SELF']}?$q1&amp;$q2&amp;page=$page";
}


// 세션값을 체크하여 이쪽에서 온것이 아니면 메인으로
function session_check()
{
	global $g5;

	if (!trim(get_session('ss_uniqid')))
		gotourl(G5_SHOP_URL);
}


// 상품 선택옵션
function get_item_options($it_id, $subject)
{
	global $g5;

	if(!$it_id || !$subject)
		return '';

	$sql = " select * from {$g5['g5_shop_item_option_table']} where io_type = '0' and it_id = '$it_id' and io_use = '1' order by io_no asc ";
	$result = sql_query($sql);
	if(!mysql_num_rows($result))
		return '';

	$str = '';
	$subj = explode(',', $subject);
	$subj_count = count($subj);

	if($subj_count > 1) {
		$options = array();

		// 옵션항목 배열에 저장
		for($i=0; $row=sql_fetch_array($result); $i++) {
			$opt_id = explode(chr(30), $row['io_id']);

			for($k=0; $k<$subj_count; $k++) {
				if(!is_array($options[$k]))
					$options[$k] = array();

				if($opt_id[$k] && !in_array($opt_id[$k], $options[$k]))
					$options[$k][] = $opt_id[$k];
			}
		}

		// 옵션선택목록 만들기
		for($i=0; $i<$subj_count; $i++) {
			$opt = $options[$i];
			$opt_count = count($opt);
			$disabled = '';
			if($opt_count) {
				$seq = $i + 1;
				if($i > 0)
					$disabled = ' disabled="disabled"';
				$str .= '<tr>'.PHP_EOL;
				$str .= '<th><label for="it_option_'.$seq.'">'.$subj[$i].'</label></th>'.PHP_EOL;

				$select = '<select id="it_option_'.$seq.'" class="it_option"'.$disabled.'>'.PHP_EOL;
				$select .= '<option value="">선택</option>'.PHP_EOL;
				for($k=0; $k<$opt_count; $k++) {
					$opt_val = $opt[$k];
					if($opt_val) {
						$select .= '<option value="'.$opt_val.'">'.$opt_val.'</option>'.PHP_EOL;
					}
				}
				$select .= '</select>'.PHP_EOL;

				$str .= '<td>'.$select.'</td>'.PHP_EOL;
				$str .= '</tr>'.PHP_EOL;
			}
		}
	} else {
		$str .= '<tr>'.PHP_EOL;
		$str .= '<th><label for="it_option_1">'.$subj[0].'</label></th>'.PHP_EOL;

		$select = '<select id="it_option_1" class="it_option">'.PHP_EOL;
		$select .= '<option value="">선택</option>'.PHP_EOL;
		for($i=0; $row=sql_fetch_array($result); $i++) {
			if($row['io_price'] >= 0)
				$price = '&nbsp;&nbsp;+ '.number_format($row['io_price']).'원';
			else
				$price = '&nbsp;&nbsp; '.number_format($row['io_price']).'원';

			if(!$row['io_stock_qty'])
				$soldout = '&nbsp;&nbsp;[품절]';
			else
				$soldout = '';

			$select .= '<option value="'.$row['io_id'].','.$row['io_price'].','.$row['io_stock_qty'].'">'.$row['io_id'].$price.$soldout.'</option>'.PHP_EOL;
		}
		$select .= '</select>'.PHP_EOL;

		$str .= '<td>'.$select.'</td>'.PHP_EOL;
		$str .= '</tr>'.PHP_EOL;
	}

	return $str;
}


// 상품 추가옵션
function get_item_supply($it_id, $subject)
{
	global $g5;

	if(!$it_id || !$subject)
		return '';

	$sql = " select * from {$g5['g5_shop_item_option_table']} where io_type = '1' and it_id = '$it_id' and io_use = '1' order by io_no asc ";
	$result = sql_query($sql);
	if(!mysql_num_rows($result))
		return '';

	$str = '';

	$subj = explode(',', $subject);
	$subj_count = count($subj);
	$options = array();

	// 옵션항목 배열에 저장
	for($i=0; $row=sql_fetch_array($result); $i++) {
		$opt_id = explode(chr(30), $row['io_id']);

		if($opt_id[0] && !array_key_exists($opt_id[0], $options))
			$options[$opt_id[0]] = array();

		if($opt_id[1]) {
			if($row['io_price'] >= 0)
				$price = '&nbsp;&nbsp;+ '.number_format($row['io_price']).'원';
			else
				$price = '&nbsp;&nbsp; '.number_format($row['io_price']).'원';
			$io_stock_qty = get_option_stock_qty($it_id, $row['io_id'], $row['io_type']);

			if($io_stock_qty < 1)
				$soldout = '&nbsp;&nbsp;[품절]';
			else
				$soldout = '';

			$options[$opt_id[0]][] = '<option value="'.$opt_id[1].','.$row['io_price'].','.$io_stock_qty.'">'.$opt_id[1].$price.$soldout.'</option>';
		}
	}

	// 옵션항목 만들기
	for($i=0; $i<$subj_count; $i++) {
		$opt = $options[$subj[$i]];
		$opt_count = count($opt);
		if($opt_count) {
			$seq = $i + 1;
			$str .= '<tr>'.PHP_EOL;
			$str .= '<th><label for="it_supply_'.$seq.'">'.$subj[$i].'</label></th>'.PHP_EOL;

			$select = '<select id="it_supply_'.$seq.'" class="it_supply">'.PHP_EOL;
			$select .= '<option value="">선택</option>'.PHP_EOL;
			for($k=0; $k<$opt_count; $k++) {
				$opt_val = $opt[$k];
				if($opt_val) {
					$select .= $opt_val.PHP_EOL;
				}
			}
			$select .= '</select>'.PHP_EOL;

			$str .= '<td class="td_sit_sel">'.$select.'</td>'.PHP_EOL;
			$str .= '</tr>'.PHP_EOL;
		}
	}

	return $str;
}


function print_item_options($it_id, $cart_id)
{
	global $g5;

	$sql = " select ct_option, ct_qty, io_price
				from {$g5['g5_shop_cart_table']} where it_id = '$it_id' and od_id = '$cart_id' order by io_type asc, ct_id asc ";
	$result = sql_query($sql);

	$str = '';
	for($i=0; $row=sql_fetch_array($result); $i++) {
		if($i == 0)
			$str .= '<ul>'.PHP_EOL;
		$price_plus = '';
		if($row['io_price'] >= 0)
			$price_plus = '+';
		$str .= '<li>'.$row['ct_option'].' '.$row['ct_qty'].'개 ('.$price_plus.display_price($row['io_price']).')</li>'.PHP_EOL;
	}

	if($i > 0)
		$str .= '</ul>';

	return $str;
}


// 일자형식변환
function date_conv($date, $case=1)
{
	if ($case == 1) { // 년-월-일 로 만들어줌
		$date = preg_replace("/([0-9]{4})([0-9]{2})([0-9]{2})/", "\\1-\\2-\\3", $date);
	} else if ($case == 2) { // 년월일 로 만들어줌
		$date = preg_replace("/-/", "", $date);
	}

	return $date;
}


// 배너출력
function display_banner($position, $num="")
{
	global $g5;

	if (!$position) $position = '왼쪽';

	include G5_SHOP_SKIN_PATH.'/boxbanner'.$num.'.skin.php';
}


// 1.00.02
// 파일번호, 이벤트번호, 1라인이미지수, 총라인수, 이미지폭, 이미지높이
// 1.02.01 $ca_id 추가
function display_event($no, $event, $list_mod, $list_row, $img_width, $img_height, $ca_id="")
{
	global $member, $g5;

	// 상품수
	$items = $list_mod * $list_row;

	// 1.02.00
	// b.it_order 추가
	$sql = " select b.* from {$g5['g5_shop_event_item_table']} a, {$g5['g5_shop_item_table']} b where a.it_id = b.it_id and b.it_use = '1' and a.ev_id = '$event' ";
	if ($ca_id) $sql .= " and ca_id = '$ca_id' ";
	$sql .= " order by b.it_order, a.it_id desc limit $items ";
	$result = sql_query($sql);
	if (!mysql_num_rows($result)) {
		return false;
	}

	$file = G5_SHOP_PATH.'/maintype'.$no.'.inc.php';
	if (!file_exists($file)) {
		echo $file.' 파일을 찾을 수 없습니다.';
	} else {
		$td_width = (int)(100 / $list_mod);
		include $file;
	}
}


function get_yn($val, $case='')
{
	switch ($case) {
		case '1' : $result = ($val > 0) ? 'Y' : 'N'; break;
		default :  $result = ($val > 0) ? '예' : '아니오';
	}
	return $result;
}


// 상품명과 건수를 반환
function get_goods($cart_id)
{
	global $g5;

	// 상품명만들기
	$row = sql_fetch(" select a.it_id, b.it_name from {$g5['g5_shop_cart_table']} a, {$g5['g5_shop_item_table']} b where a.it_id = b.it_id and a.od_id = '$cart_id' order by ct_id limit 1 ");
	// 상품명에 "(쌍따옴표)가 들어가면 오류 발생함
	$goods['it_id'] = $row['it_id'];
	$goods['full_name']= $goods['name'] = addslashes($row['it_name']);
	// 특수문자제거
	$goods['full_name'] = preg_replace ("/[ #\&\+\-%@=\/\\\:;,\.'\"\^`~\_|\!\?\*$#<>()\[\]\{\}]/i", "",  $goods['full_name']);

	// 상품건수
	$row = sql_fetch(" select count(*) as cnt from {$g5['g5_shop_cart_table']} where od_id = '$cart_id' ");
	$cnt = $row['cnt'] - 1;
	if ($cnt)
		$goods['full_name'] .= ' 외 '.$cnt.'건';
	$goods['count'] = $row['cnt'];

	return $goods;
}


// 패턴의 내용대로 해당 디렉토리에서 정렬하여 <select> 태그에 적용할 수 있게 반환
function get_list_skin_options($pattern, $dirname='./', $sval='')
{
	$str = '<option value="">선택</option>'.PHP_EOL;

	unset($arr);
	$handle = opendir($dirname);
	while ($file = readdir($handle)) {
		if (preg_match("/$pattern/", $file, $matches)) {
			$arr[] = $matches[0];
		}
	}
	closedir($handle);

	sort($arr);
	foreach($arr as $value) {
		if($value == $sval)
			$selected = ' selected="selected"';
		else
			$selected = '';

		$str .= '<option value="'.$value.'"'.$selected.'>'.$value.'</option>'.PHP_EOL;
	}

	return $str;
}


// 일자 시간을 검사한다.
function check_datetime($datetime)
{
	if ($datetime == "0000-00-00 00:00:00")
		return true;

	$year   = substr($datetime, 0, 4);
	$month  = substr($datetime, 5, 2);
	$day	= substr($datetime, 8, 2);
	$hour   = substr($datetime, 11, 2);
	$minute = substr($datetime, 14, 2);
	$second = substr($datetime, 17, 2);

	$timestamp = mktime($hour, $minute, $second, $month, $day, $year);

	$tmp_datetime = date("Y-m-d H:i:s", $timestamp);
	if ($datetime == $tmp_datetime)
		return true;
	else
		return false;
}


// 경고메세지를 경고창으로
function alert_opener($msg='', $url='')
{
	global $g5;

	if (!$msg) $msg = '올바른 방법으로 이용해 주십시오.';

	echo "<meta http-equiv=\"content-type\" content=\"text/html; charset=utf-8\">";
	echo "<script>";
	echo "alert(\"$msg\");";
	echo "opener.location.href=\"$url\";";
	echo "self.close();";
	echo "</script>";
	exit;
}


// option 리스트에 selected 추가
function conv_selected_option($options, $value)
{
	if(!$options)
		return '';

	$options = str_replace('value="'.$value.'"', 'value="'.$value.'" selected', $options);

	return $options;
}


// 주문서 번호를 얻는다.
function get_new_od_id()
{
	global $g5;

	// 주문서 테이블 Lock 걸고
	sql_query(" LOCK TABLES {$g5['g5_shop_order_table']} READ, {$g5['g5_shop_order_table']} WRITE ", FALSE);
	// 주문서 번호를 만든다.
	$date = date("ymd", time());	// 2002년 3월 7일 일경우 020307
	$sql = " select max(od_id) as max_od_id from {$g5['g5_shop_order_table']} where SUBSTRING(od_id, 1, 6) = '$date' ";
	$row = sql_fetch($sql);
	$od_id = $row['max_od_id'];
	if ($od_id == 0)
		$od_id = 1;
	else
	{
		$od_id = (int)substr($od_id, -4);
		$od_id++;
	}
	$od_id = $date . substr("0000" . $od_id, -4);
	// 주문서 테이블 Lock 풀고
	sql_query(" UNLOCK TABLES ", FALSE);

	return $od_id;
}


// cart id 설정
function set_cart_id($direct)
{
	global $g5, $default, $member;

	if ($direct) {
		$tmp_cart_id = get_session('ss_cart_direct');
		if(!$tmp_cart_id) {
			$tmp_cart_id = get_uniqid();
			set_session('ss_cart_direct', $tmp_cart_id);
		}
	} else {
		// 비회원장바구니 cart id 쿠키설정
		if($default['de_guest_cart_use']) {
			$tmp_cart_id = get_cookie('ck_guest_cart_id');
			if($tmp_cart_id) {
				set_session('ss_cart_id', $tmp_cart_id);
				set_cookie('ck_guest_cart_id', $tmp_cart_id, (($default['de_cart_keep_term'] - 1) * 86400));
			} else {
				$tmp_cart_id = get_uniqid();
				set_session('ss_cart_id', $tmp_cart_id);
				set_cookie('ck_guest_cart_id', $tmp_cart_id, (($default['de_cart_keep_term'] - 1) * 86400));
			}
		} else {
			$tmp_cart_id = get_session('ss_cart_id');
			if(!$tmp_cart_id) {
				$tmp_cart_id = get_uniqid();
				set_session('ss_cart_id', $tmp_cart_id);
			}
		}

		// 보관된 회원장바구니 자료 cart id 변경
		if($member['mb_id'] && $tmp_cart_id) {
			$sql = " update {$g5['g5_shop_cart_table']}
						set od_id = '$tmp_cart_id'
						where mb_id = '{$member['mb_id']}'
						  and ct_direct = '0'
						  and ct_status = '쇼핑' ";
			if($default['de_cart_keep_term']) {
				$ctime = date('Y-m-d H:i:s', G5_SERVER_TIME - (($default['de_cart_keep_term'] - 1) * 86400));
				$sql .= " and ct_time > '$ctime' ";
			}

			sql_query($sql);
		}
	}
}


// 상품 목록 : 관련 상품 출력
function relation_item($it_id, $width, $height, $rows=3)
{
	global $g5;

	$str = '';

	if(!$it_id)
		return $str;

	$sql = " select b.it_id, b.it_name, b.it_price, b.it_tel_inq from {$g5['g5_shop_item_relation_table']} a left join {$g5['g5_shop_item_table']} b on ( a.it_id2 = b.it_id ) where a.it_id = '$it_id' order by ir_no asc limit 0, $rows ";
	$result = sql_query($sql);

	for($i=0; $row=sql_fetch_array($result); $i++) {
		if($i == 0) {
			$str .= '<span class="sound_only">관련 상품 시작</span>';
			$str .= '<ul class="sct_rel_ul">';
		}

		$it_name = get_text($row['it_name']); // 상품명
		$it_price = get_price($row); // 상품가격
		if(!$row['it_tel_inq'])
			$it_price = display_price($it_price);

		$img = get_it_image($row['it_id'], $width, $height);

		$str .= '<li class="sct_rel_li"><a href="'.G5_SHOP_URL.'/item.php?it_id='.$row['it_id'].'" class="sct_rel_a">'.$img.'</a></li>';
	}

	if($i > 0)
		$str .= '</ul><span class="sound_only">관련 상품 끝</span>';

	return $str;
}


// 상품이미지에 유형 아이콘 출력
function item_icon($it)
{
	global $g5;

	$row = sql_fetch("select * from {$g5['g5_item_type_icon_table']} where no='".$it[it_type]."' ");
	$icon = '<span class="sit_icon">';

	if($row[tp_img]){
		$icon .= '<img src="'.G5_URL.'/data/item_type_icon/'.$row[tp_img].'" alt="'.$row[tp_name].'">';
	}
	// 품절
	/*
	if (is_soldout($it['it_id']))
		$icon .= '<img src="'.G5_SHOP_URL.'/img/icon_soldout.gif" alt="품절"> ';

	if ($it['it_type1'])
		$icon .= '<img src="'.G5_SHOP_URL.'/img/icon_hit.gif" alt="최신상품">';

	if ($it['it_type2'])
		$icon .= '<img src="'.G5_SHOP_URL.'/img/icon_rec.gif" alt="히트상품">';

	if ($it['it_type3'])
		$icon .= '<img src="'.G5_URL.'/img/new_bn.png" alt="추천상품">';

	if ($it['it_type4'])
		$icon .= '<img src="'.G5_URL.'/img/best_bn.png" alt="인기상품">';

	*/

	/*
	if ($it['it_type5'])
		$icon .= '<img src="'.G5_SHOP_URL.'/img/icon_discount.gif" alt="할인상품">';

	if ($it['it_type6'])
		$icon .= '<img src="'.G5_SHOP_URL.'/img/icon_auction.gif" alt="경매상품">';
	*/

	$icon .= '</span>';

	return $icon;
}

function item_icon1($gp)
{
	global $g5;

	$row = sql_fetch("select * from {$g5['g5_gp_item_type_icon_table']} where no='".$gp[it_type]."' ");
	$icon = '<span class="sit_icon">';
	// 품절
	//if (is_soldout($gp['gp_id']))
	//	$icon .= '<img src="'.G5_SHOP_URL.'/img/icon_soldout.gif" alt="품절"> ';

	$size = (G5_IS_MOBILE) ? '48' : '64';
	
	if($row[tp_img]){
		$icon .= '<img src="'.G5_URL.'/data/gpitem_type_icon/'.$row[tp_img].'" width='.$size.' alt="'.$row[tp_name].'">';
	}


	/*
	if ($it['it_type5'])
		$icon .= '<img src="'.G5_SHOP_URL.'/img/icon_discount.gif" alt="할인상품">';

	if ($it['it_type6'])
		$icon .= '<img src="'.G5_SHOP_URL.'/img/icon_auction.gif" alt="경매상품">';
	*/

	$icon .= '</span>';

	return $icon;
}


// sns 공유하기
function get_sns_share_link($sns, $url, $title, $img)
{
	if(!$sns)
		return '';

	switch($sns) {
		case 'facebook':
			$str = '<a href="https://www.facebook.com/sharer/sharer.php?u='.urlencode($url).'&amp;t='.urlencode($title).'" class="share-facebook" target="_blank"><img src="'.$img.'" alt="페이스북에 공유"></a>';
			break;
		case 'twitter':
			$str = '<a href="https://twitter.com/share?url='.urlencode($url).'&amp;text='.urlencode($title).'" class="share-twitter" target="_blank"><img src="'.$img.'" alt="트위터에 공유"></a>';
			break;
		case 'googleplus':
			$str = '<a href="https://plus.google.com/share?url='.urlencode($url).'" class="share-googleplus" target="_blank"><img src="'.$img.'" alt="구글플러스에 공유"></a>';
			break;
	}

	return $str;
}


// 상품이미지 썸네일 삭제
function delete_item_thumbnail($dir, $file)
{
	if(!$dir || !$file)
		return;

	$filename = preg_replace("/\.[^\.]+$/i", "", $file); // 확장자제거

	$files = glob($dir.'/thumb-'.$filename.'*');

	if(is_array($files)) {
		foreach($files as $thumb_file) {
			@unlink($thumb_file);
		}
	}
}


// 쿠폰번호 생성함수
function get_coupon_id()
{
	$len = 16;
	$chars = "ABCDEFGHJKLMNPQRSTUVWXYZ123456789";

	srand((double)microtime()*1000000);

	$i = 0;
	$str = '';

	while ($i < $len) {
		$num = rand() % strlen($chars);
		$tmp = substr($chars, $num, 1);
		$str .= $tmp;
		$i++;
	}

	$str = preg_replace("/([0-9A-Z]{4})([0-9A-Z]{4})([0-9A-Z]{4})([0-9A-Z]{4})/", "\\1-\\2-\\3-\\4", $str);

	return $str;
}


// array_map() 대체
function array_add_callback($func, $array)
{
	if(!$func) {
		return;
	}

	if(is_array($array)) {
		foreach($array as $key => $value) {
			if(is_array($value)) {
				$array[$key] = array_add_callback($func, $value);
			} else {
				$array[$key] = call_user_func($func, $value);
			}
		}
	} else {
		$array = call_user_func($func, $array);
	}

	return $array;
}


// 주문의 금액, 배송비 과세금액 등의 정보를 가져옴
function get_order_info($od_id)
{
	global $g5;

	// 주문정보
	$sql = " select * from {$g5['g5_shop_order_table']} where od_id = '$od_id' ";
	$od = sql_fetch($sql);

	if(!$od['od_id'])
		return false;

	$info = array();

	// 장바구니 주문금액정보
	$sql = " select SUM(IF(io_type = 1, (io_price * ct_qty), ((ct_price + io_price) * ct_qty))) as price,
					SUM(cp_price) as coupon,
					SUM( IF( ct_notax = 0, ( IF(io_type = 1, (io_price * ct_qty), ( (ct_price + io_price) * ct_qty) ) - cp_price ), 0 ) ) as tax_mny,
					SUM( IF( ct_notax = 1, ( IF(io_type = 1, (io_price * ct_qty), ( (ct_price + io_price) * ct_qty) ) - cp_price ), 0 ) ) as free_mny
				from {$g5['g5_shop_cart_table']}
				where od_id = '$od_id'
				  and ct_status IN ( '입금대기', '결제완료', '상품준비중', '배송대기', '배송중', '배송완료' ) ";
	$sum = sql_fetch($sql);

	$cart_price = $sum['price'];
	$cart_coupon = $sum['coupon'];

	// 배송비
	$send_cost = get_sendcost($od_id);

	$od_coupon = $od_send_coupon = 0;

	if($od['mb_id']) {
		// 주문할인 쿠폰
		$sql = " select a.cp_id, a.cp_type, a.cp_price, a.cp_trunc, a.cp_minimum, a.cp_maximum
					from {$g5['g5_shop_coupon_table']} a right join {$g5['g5_shop_coupon_log_table']} b on ( a.cp_id = b.cp_id )
					where b.od_id = '$od_id'
					  and b.mb_id = '{$od['mb_id']}'
					  and a.cp_method = '2' ";
		$cp = sql_fetch($sql);

		$tot_od_price = $cart_price - $cart_coupon;

		if($cp['cp_id']) {
			$dc = 0;

			if($cp['cp_minimum'] <= $tot_od_price) {
				if($cp['cp_type']) {
					$dc = floor(($tot_od_price * ($cp['cp_price'] / 100)) / $cp['cp_trunc']) * $cp['cp_trunc'];
				} else {
					$dc = $cp['cp_price'];
				}

				if($cp['cp_maximum'] && $dc > $cp['cp_maximum'])
					$dc = $cp['cp_maximum'];

				if($tot_od_price < $dc)
					$dc = $tot_od_price;

				$tot_od_price -= $dc;
				$od_coupon = $dc;
			}
		}

		// 배송쿠폰 할인
		$sql = " select a.cp_id, a.cp_type, a.cp_price, a.cp_trunc, a.cp_minimum, a.cp_maximum
					from {$g5['g5_shop_coupon_table']} a right join {$g5['g5_shop_coupon_log_table']} b on ( a.cp_id = b.cp_id )
					where b.od_id = '$od_id'
					  and b.mb_id = '{$od['mb_id']}'
					  and a.cp_method = '3' ";
		$cp = sql_fetch($sql);

		if($cp['cp_id']) {
			$dc = 0;
			if($cp['cp_minimum'] <= $tot_od_price) {
				if($cp['cp_type']) {
					$dc = floor(($send_cost * ($cp['cp_price'] / 100)) / $cp['cp_trunc']) * $cp['cp_trunc'];
				} else {
					$dc = $cp['cp_price'];
				}

				if($cp['cp_maximum'] && $dc > $cp['cp_maximum'])
					$dc = $cp['cp_maximum'];

				if($dc > $send_cost)
					$dc = $send_cost;

				$od_send_coupon = $dc;
			}
		}
	}

	// 과세, 비과세 금액정보
	$tax_mny = $sum['tax_mny'];
	$free_mny = $sum['free_mny'];

	if($od['od_tax_flag']) {
		$tot_tax_mny = ( $tax_mny + $send_cost + $od['od_send_cost2'] )
					   - ( $od_coupon + $od_send_coupon + $od['od_receipt_point'] );
		if($tot_tax_mny < 0) {
			$free_mny += $tot_tax_mny;
			$tot_tax_mny = 0;
		}
	} else {
		$tot_tax_mny = ( $tax_mny + $free_mny + $send_cost + $od['od_send_cost2'] )
					   - ( $od_coupon + $od_send_coupon + $od['od_receipt_point'] );
		$free_mny = 0;
	}

	$od_tax_mny = round($tot_tax_mny / 1.1);
	$od_vat_mny = $tot_tax_mny - $od_tax_mny;
	$od_free_mny = $free_mny;

	// 장바구니 취소금액 정보
	$sql = " select SUM(IF(io_type = 1, (io_price * ct_qty), ((ct_price + io_price) * ct_qty))) as price
				from {$g5['g5_shop_cart_table']}
				where od_id = '$od_id'
				  and ct_status IN ( '취소', '반품', '품절' ) ";
	$sum = sql_fetch($sql);
	$cancel_price = $sum['price'];

	// 미수금액
	$od_misu = ( $cart_price + $send_cost + $od['od_send_cost2'] )
			   - ( $cart_coupon + $od_coupon + $od_send_coupon )
			   - ( $od['od_receipt_price'] + $od['od_receipt_point'] - $od['od_refund_price'] );

	if($od_misu < 0){
		$od_misu = 0;
	}else{
		$od_misu = $od_misu;
	}

	// 장바구니상품금액
	$od_cart_price = $cart_price + $cancel_price;

	// 결과처리
	$info['od_cart_price']	  = $od_cart_price;
	$info['od_send_cost']	   = $send_cost;
	$info['od_coupon']		  = $od_coupon;
	$info['od_send_coupon']	 = $od_send_coupon;
	$info['od_cart_coupon']	 = $cart_coupon;
	$info['od_tax_mny']		 = $od_tax_mny;
	$info['od_vat_mny']		 = $od_vat_mny;
	$info['od_free_mny']		= $od_free_mny;
	$info['od_cancel_price']	= $cancel_price;
	$info['od_misu']			= $od_misu;

	return $info;
}

function get_purchase_info($od_id)
{
	global $g5;

	// 주문정보
	$sql = " select * from {$g5['g5_purchase_order_table']} where od_id = '$od_id' ";
	$od = sql_fetch($sql);

	if(!$od['od_id'])
		return false;

	$info = array();


	// 미수금액
	$od_misu = ( $od['od_cart_price'] + $send_cost + $od['od_send_cost2'] )
			   - ( $od['od_receipt_price'] );

	// 장바구니상품금액
	$od_cart_price = $cart_price + $cancel_price;

	// 결과처리
	$info['od_cart_price']	  = $od['od_cart_price'];
	$info['od_cancel_price']	= $od['od_refund_price'];
	$info['od_misu']			= $od_misu;

	return $info;
}

// 상품포인트
function get_item_point($it)
{
	$it_point = 0;

	if($it['it_point_type']) {
		$it_point = floor(($it['it_price'] * ($it['it_point'] / 100) / 10)) * 10;
	} else {
		$it_point = $it['it_point'];
	}

	return $it_point;
}


// 배송비 구함
function get_sendcost($cart_id, $selected=1)
{
	global $default, $g5;

	$send_cost = 0;
	$total_price = 0;
	$total_send_cost = 0;

	$sql = " select distinct it_id
				from {$g5['g5_shop_cart_table']}
				where od_id = '$cart_id'
				  and ct_send_cost = '0'
				  and ct_status IN ( '쇼핑', '입금대기', '결제완료', '상품준비중', '배송대기', '배송중', '배송완료' )
				  and ct_select = '$selected'
				  and ct_type=''
				  ";

	$result = sql_query($sql);
	for($i=0; $sc=sql_fetch_array($result); $i++) {
		// 합계
		$sql = " select SUM(IF(io_type = 1, (io_price * ct_qty), ((ct_price + io_price) * ct_qty))) as price,
						SUM(ct_qty) as qty
					from {$g5['g5_shop_cart_table']}
					where it_id = '{$sc['it_id']}'
					  and od_id = '$cart_id'
					  and ct_type=''
					  ";
		$sum = sql_fetch($sql);

		$send_cost = get_item_sendcost($sc['it_id'], $sum['price'], $sum['qty']);

		if($send_cost > 0)
			$total_send_cost += $send_cost;

		if($default['de_send_cost_case'] == '차등' && $send_cost == -1)
			$total_price += $sum['price'];
	}

	$send_cost = 0;
	if($default['de_send_cost_case'] == '차등' && $total_price > 0) {
		// 금액별차등 : 여러단계의 배송비 적용 가능
		$send_cost_limit = explode(";", $default['de_send_cost_limit']);
		$send_cost_list  = explode(";", $default['de_send_cost_list']);
		$send_cost = 0;
		for ($k=0; $k<count($send_cost_limit); $k++) {
			// 총판매금액이 배송비 상한가 보다 작다면
			if ($total_price < preg_replace('/[^0-9]/', '', $send_cost_limit[$k])) {
				$send_cost = preg_replace('/[^0-9]/', '', $send_cost_list[$k]);
				break;
			}
		}
	}

	return ($total_send_cost + $send_cost);
}

function get_sendcost1($cart_id, $selected=1, $auc_status)
{
	global $default, $g5;

	$send_cost = 0;
	$total_price = 0;
	$total_send_cost = 0;

	$sql = " select distinct it_id
				from {$g5['g5_shop_cart_table']}
				where od_id = '$cart_id'
				  and ct_send_cost = '0'
				  and ct_status IN ( '쇼핑', '입금대기', '결제완료', '상품준비중', '배송대기', '배송중', '배송완료' )
				  and ct_select = '$selected'
				  and ct_type=''
				  and auc_status='$auc_status'
				  ";

	$result = sql_query($sql);
	for($i=0; $sc=sql_fetch_array($result); $i++) {
		// 합계
		$sql = " select SUM(IF(io_type = 1, (io_price * ct_qty), ((ct_price + io_price) * ct_qty))) as price,
						SUM(ct_qty) as qty
					from {$g5['g5_shop_cart_table']}
					where it_id = '{$sc['it_id']}'
					and od_id = '$cart_id'
					and ct_type=''
					and auc_status='$auc_status'
					";
		$sum = sql_fetch($sql);

		$send_cost = get_item_sendcost($sc['it_id'], $sum['price'], $sum['qty']);

		if($send_cost > 0)
			$total_send_cost += $send_cost;

		if($default['de_send_cost_case'] == '차등' && $send_cost == -1)
			$total_price += $sum['price'];
	}

	$send_cost = 0;
	if($default['de_send_cost_case'] == '차등' && $total_price > 0) {
		// 금액별차등 : 여러단계의 배송비 적용 가능
		$send_cost_limit = explode(";", $default['de_send_cost_limit']);
		$send_cost_list  = explode(";", $default['de_send_cost_list']);
		$send_cost = 0;
		for ($k=0; $k<count($send_cost_limit); $k++) {
			// 총판매금액이 배송비 상한가 보다 작다면
			if ($total_price < preg_replace('/[^0-9]/', '', $send_cost_limit[$k])) {
				$send_cost = preg_replace('/[^0-9]/', '', $send_cost_list[$k]);
				break;
			}
		}
	}

	return ($total_send_cost + $send_cost);
}



//공동구매
function get_sendcost_gp($cart_id, $selected=1)
{
	global $default, $g5;

	$send_cost = 0;
	$total_price = 0;
	$total_send_cost = 0;

	$sql = " select distinct it_id
				from {$g5['g5_shop_cart_table']}
				where od_id = '$cart_id'
				  and ct_send_cost = '0'
				  and ct_status IN ( '쇼핑', '입금대기', '결제완료', '상품준비중', '배송대기', '배송중', '배송완료' )
				  and ct_select = '$selected'
				  and ct_type!=''
				  ";

	$result = sql_query($sql);
	for($i=0; $sc=sql_fetch_array($result); $i++) {
		// 합계
		$sql = " select SUM(IF(io_type = 1, (io_price * ct_qty), ((ct_price + io_price) * ct_qty))) as price,
						SUM(ct_qty) as qty
					from {$g5['g5_shop_cart_table']}
					where it_id = '{$sc['it_id']}'
					  and od_id = '$cart_id'
					  and ct_type!=''
					  ";
		$sum = sql_fetch($sql);

		$send_cost = get_item_sendcost($sc['it_id'], $sum['price'], $sum['qty']);

		if($send_cost > 0)
			$total_send_cost += $send_cost;

		if($default['de_send_cost_case'] == '차등' && $send_cost == -1)
			$total_price += $sum['price'];
	}

	$send_cost = 0;
	if($default['de_send_cost_case'] == '차등' && $total_price > 0) {
		// 금액별차등 : 여러단계의 배송비 적용 가능
		$send_cost_limit = explode(";", $default['de_send_cost_limit']);
		$send_cost_list  = explode(";", $default['de_send_cost_list']);
		$send_cost = 0;
		for ($k=0; $k<count($send_cost_limit); $k++) {
			// 총판매금액이 배송비 상한가 보다 작다면
			if ($total_price < preg_replace('/[^0-9]/', '', $send_cost_limit[$k])) {
				$send_cost = preg_replace('/[^0-9]/', '', $send_cost_list[$k]);
				break;
			}
		}
	}

	return ($total_send_cost + $send_cost);
}

function get_sendcost_gp1($cart_id, $selected=1, $ct_type_que)
{
	global $default, $g5;

	$send_cost = 0;
	$total_price = 0;
	$total_send_cost = 0;

	$sql = " select distinct it_id
				from {$g5['g5_shop_cart_table']}
				where od_id = '$cart_id'
				  and ct_select = '$selected'
				  and ct_type!=''
				  $ct_type_que
				  ";

	$result = sql_query($sql);

	for($i=0; $sc=sql_fetch_array($result); $i++) {
		// 합계
		$sql = " select SUM(IF(io_type = 1, (io_price * ct_qty), ((ct_price + io_price) * ct_qty))) as price,
						SUM(ct_qty) as qty
					from {$g5['g5_shop_cart_table']}
					where it_id = '{$sc['it_id']}'
					and od_id = '$cart_id'
					$ct_type_que
					";
		$sum = sql_fetch($sql);

		$send_cost = get_gp_sendcost($sc['it_id']);

		if($send_cost > 0)
			$total_send_cost += $send_cost;

	}

	return $total_send_cost;
}


// 상품별 배송비
function get_item_sendcost($it_id, $price, $qty)
{
	global $g5, $default;

	$sql = " select it_id, it_sc_type, it_sc_method, it_sc_price, it_sc_minimum, it_sc_qty
				from {$g5['g5_shop_item_table']}
				where it_id = '$it_id' ";
	$it = sql_fetch($sql);
	if(!$it['it_id'])
		return 0;

	if($it['it_sc_type'] > 1) {
		if($it['it_sc_type'] == 2) { // 조건부무료
			if($price >= $it['it_sc_minimum'])
				$sendcost = 0;
			else
				$sendcost = $it['it_sc_price'];
		} else if($it['it_sc_type'] == 3) { // 유료배송
			$sendcost = $it['it_sc_price'];
		} else { // 수량별 부과
			if(!$it['it_sc_qty'])
				$it['it_sc_qty'] = 1;

			$q = ceil((int)$qty / (int)$it['it_sc_qty']);
			$sendcost = (int)$it['it_sc_price'] * $q;
		}
	} else if($it['it_sc_type'] == 1) { // 무료배송
		$sendcost = 0;
	} else {
		$sendcost = -1;
	}

	return $sendcost;
}

function get_gp_sendcost($it_id)
{
	global $g5, $default;

	$sql = " select *
				from {$g5['g5_shop_group_purchase_table']}
				where gp_id = '$it_id' ";
	$it = sql_fetch($sql);

	return $it[gp_sc_price];
}


// 가격비교 사이트 상품 배송비
function get_item_sendcost2($it_id, $price, $qty)
{
	global $g5, $default;

	$sql = " select it_id, it_sc_type, it_sc_method, it_sc_price, it_sc_minimum, it_sc_qty
				from {$g5['g5_shop_item_table']}
				where it_id = '$it_id' ";
	$it = sql_fetch($sql);
	if(!$it['it_id'])
		return 0;

	$sendcost = 0;

	// 쇼핑몰 기본설정을 사용할 때
	if($it['it_sc_type'] == 0)
	{
		if($default['de_send_cost_case'] == '차등') {
			// 금액별차등 : 여러단계의 배송비 적용 가능
			$send_cost_limit = explode(";", $default['de_send_cost_limit']);
			$send_cost_list  = explode(";", $default['de_send_cost_list']);

			for ($k=0; $k<count($send_cost_limit); $k++) {
				// 총판매금액이 배송비 상한가 보다 작다면
				if ($price < preg_replace('/[^0-9]/', '', $send_cost_limit[$k])) {
					$sendcost = preg_replace('/[^0-9]/', '', $send_cost_list[$k]);
					break;
				}
			}
		}
	}
	else
	{
		if($it['it_sc_type'] > 1) {
			if($it['it_sc_type'] == 2) { // 조건부무료
				if($price >= $it['it_sc_minimum'])
					$sendcost = 0;
				else
					$sendcost = $it['it_sc_price'];
			} else if($it['it_sc_type'] == 3) { // 유료배송
				$sendcost = $it['it_sc_price'];
			} else { // 수량별 부과
				if(!$it['it_sc_qty'])
					$it['it_sc_qty'] = 1;

				$q = ceil((int)$qty / (int)$it['it_sc_qty']);
				$sendcost = (int)$it['it_sc_price'] * $q;
			}
		} else if($it['it_sc_type'] == 1) { // 무료배송
			$sendcost = 0;
		}
	}

	return $sendcost;
}


// 쿠폰 사용체크
function is_used_coupon($mb_id, $cp_id)
{
	global $g5;

	$used = false;

	$sql = " select count(*) as cnt from {$g5['g5_shop_coupon_log_table']} where mb_id = '$mb_id' and cp_id = '$cp_id' ";
	$row = sql_fetch($sql);

	if($row['cnt'])
		$used = true;

	return $used;
}

// 품절상품인지 체크
function is_soldout($it_id)
{
	global $g5;

	// 상품정보
	$sql = " select it_soldout, it_stock_qty from {$g5['g5_shop_item_table']} where it_id = '$it_id' ";
	$it = sql_fetch($sql);

	if($it['it_soldout'] || $it['it_stock_qty'] <= 0)
		return true;

	$count = 0;
	$soldout = false;

	// 상품에 선택옵션 있으면..
	$sql = " select count(*) as cnt from {$g5['g5_shop_item_option_table']} where it_id = '$it_id' and io_type = '0' ";
	$row = sql_fetch($sql);

	if($row['cnt']) {
		$sql = " select io_id, io_type, io_stock_qty
					from {$g5['g5_shop_item_option_table']}
					where it_id = '$it_id'
					  and io_type = '0'
					  and io_use = '1' ";
		$result = sql_query($sql);

		for($i=0; $row=sql_fetch_array($result); $i++) {
			// 주문대기수량
			$sql = " select SUM(ct_qty) as qty from {$g5['g5_shop_cart_table']}
					  where it_id = '$it_id'
						and io_id = '{$row['io_id']}'
						and io_type = '{$row['io_type']}'
						and ct_stock_use = 0
						and ct_status in ('입금대기', '결제완료', '상품준비중') ";
			$sum = sql_fetch($sql);

			// 옵션 재고수량
			$stock_qty = get_option_stock_qty($it_id, $row['io_id'], $row['io_type']);

			if($stock_qty - $sum['qty'] <= 0)
				$count++;
		}

		// 모든 선택옵션 품절이면 상품 품절
		if($i == $count)
			$soldout = true;
	} else {
		// 주문대기수량
		$sql = " select SUM(ct_qty) as qty from {$g5['g5_shop_cart_table']}
				  where it_id = '$it_id'
					and io_id = ''
					and io_type = '0'
					and ct_stock_use = 0
					and ct_status in ('입금대기', '결제완료', '상품준비중') ";
		$sum = sql_fetch($sql);

		// 상품 재고수량
		$stock_qty = get_it_stock_qty($it_id);

		if($stock_qty - $sum['qty'] <= 0)
			$soldout = true;
	}

	return $soldout;
}

// 상품후기 작성가능한지 체크
function check_itemuse_write($close=true)
{
	global $g5, $default, $is_admin;

	if(!$is_admin && $default['de_item_use_write'])
	{
		$sql = " select count(*) as cnt
					from {$g5['g5_shop_cart_table']}
					where it_id = '$it_id'
					  and mb_id = '{$member['mb_id']}'
					  and ct_status = '배송완료' ";
		$row = sql_fetch($sql);

		if($row['cnt'] == 0)
		{
			if($close)
				alert_close('사용후기는 주문이 완료된 경우에만 작성하실 수 있습니다.');
			else
				alert('사용후기는 주문하신 상품의 상태가 완료인 경우에만 작성하실 수 있습니다.');
		}
	}
}


// 구매 본인인증 체크
function shop_member_cert_check($id, $type)
{
	global $g5, $member;

	$msg = '';

	switch($type)
	{
		case 'item':
			$sql = " select ca_id, ca_id2, ca_id3 from {$g5['g5_shop_item_table']} where it_id = '$id' ";
			$it = sql_fetch($sql);

			$seq = '';
			for($i=0; $i<3; $i++) {
				$ca_id = $it['ca_id'.$seq];

				if(!$ca_id)
					continue;

				$sql = " select ca_cert_use, ca_adult_use from {$g5['g5_shop_category_table']} where ca_id = '$ca_id' ";
				$row = sql_fetch($sql);

				// 본인확인체크
				if($row['ca_cert_use'] && !$member['mb_certify']) {
					if($member['mb_id'])
						$msg = '회원정보 수정에서 본인확인 후 이용해 주십시오.';
					else
						$msg = '본인확인된 로그인 회원만 이용할 수 있습니다.';

					break;
				}

				// 성인인증체크
				if($row['ca_adult_use'] && !$member['mb_adult']) {
					if($member['mb_id'])
						$msg = '본인확인으로 성인인증된 회원만 이용할 수 있습니다.\\n회원정보 수정에서 본인확인을 해주십시오.';
					else
						$msg = '본인확인으로 성인인증된 회원만 이용할 수 있습니다.';

					break;
				}

				if($i == 0)
					$seq = 1;
				$seq++;
			}

			break;
		case 'list':
			$sql = " select * from {$g5['g5_shop_category_table']} where ca_id = '$id' ";
			$ca = sql_fetch($sql);

			// 본인확인체크
			if($ca['ca_cert_use'] && !$member['mb_certify']) {
				if($member['mb_id'])
					$msg = '회원정보 수정에서 본인확인 후 이용해 주십시오.';
				else
					$msg = '본인확인된 로그인 회원만 이용할 수 있습니다.';
			}

			// 성인인증체크
			if($ca['ca_adult_use'] && !$member['mb_adult']) {
				if($member['mb_id'])
					$msg = '본인확인으로 성인인증된 회원만 이용할 수 있습니다.\\n회원정보 수정에서 본인확인을 해주십시오.';
				else
					$msg = '본인확인으로 성인인증된 회원만 이용할 수 있습니다.';
			}

			break;
		default:
			break;
	}

	return $msg;
}

//카테고리 이름
function category_name($ca_id){
	global $g5;
	$row = sql_fetch("select * from {$g5['g5_shop_category_table']} where ca_id='".$ca_id."' ");
	$ca_name = $row[ca_name];
	return $ca_name;
}


// 배송조회버튼 생성
function get_delivery_inquiry($company, $invoice, $class='')
{
	if(!$company || !$invoice)
		return '';

	$dlcomp = explode(")", str_replace("(", "", G5_DELIVERY_COMPANY));

	for($i=0; $i<count($dlcomp); $i++) {
		if(strstr($dlcomp[$i], $company)) {
			list($com, $url, $tel) = explode("^", $dlcomp[$i]);
			break;
		}
	}

	$str = '';
	if($com && $url) {
		$str .= '<a href="'.$url.$invoice.'" target="_blank"';
		if($class)
			$str .= ' class="'.$class.'"';
		$str .='>배송조회</a>';
		/*
		if($tel)
			$str .= ' (문의전화: '.$tel.')';
			*/
	}

	return $str;
}

// 배송조회 전화번호 생성
function get_delivery_inquiry_tel($company, $invoice)
{
	if(!$company || !$invoice)
		return '';

	$dlcomp = explode(")", str_replace("(", "", G5_DELIVERY_COMPANY));

	for($i=0; $i<count($dlcomp); $i++) {
		if(strstr($dlcomp[$i], $company)) {
			list($com, $url, $tel) = explode("^", $dlcomp[$i]);
			break;
		}
	}

	$str = '';
	if($com && $url) {
		if($tel)
			$str .= $tel;
	}

	return $str;
}

// 배송조회 배송회사 링크
function get_delivery_inquiry_company($company, $invoice)
{
	if(!$company || !$invoice)
		return '';

	$dlcomp = explode(")", str_replace("(", "", G5_DELIVERY_COMPANY));

	for($i=0; $i<count($dlcomp); $i++) {
		if(strstr($dlcomp[$i], $company)) {
			list($com, $url, $tel) = explode("^", $dlcomp[$i]);
			break;
		}
	}

	$str = '';
	if($com && $url) {
		$str .= '<a href="'.$url.$invoice.'" target="_blank"';
		if($class)
			$str .= ' class="'.$class.'"';
		$str .='>'.$com.'</a>';
	}

	return $str;
}


// 사용후기의 확인된 건수를 상품테이블에 저장합니다.
function update_use_cnt($it_id)
{
	global $g5;
	$row = sql_fetch(" select count(*) as cnt from {$g5['g5_shop_item_use_table']} where it_id = '{$it_id}' and is_confirm = 1 ");
	return sql_query(" update {$g5['g5_shop_item_table']} set it_use_cnt = '{$row['cnt']}' where it_id = '{$it_id}' ");
}


// 사용후기의 선호도(별) 평균을 상품테이블에 저장합니다.
function update_use_avg($it_id)
{
	global $g5;
	$row = sql_fetch(" select count(*) as cnt, sum(is_score) as total from {$g5['g5_shop_item_use_table']} where it_id = '{$it_id}' ");
	$average = ($row['total'] && $row['cnt']) ? $row['total'] / $row['cnt'] : 0;
	return sql_query(" update {$g5['g5_shop_item_table']} set it_use_avg = '$average' where it_id = '{$it_id}' ");
}


//------------------------------------------------------------------------------
// 주문포인트를 적립한다.
// 설정일이 지난 포인트 부여되지 않은 배송완료된 장바구니 자료에 포인트 부여
// 설정일이 0 이면 주문서 완료 설정 시점에서 포인트를 바로 부여합니다.
//------------------------------------------------------------------------------
function save_order_point($ct_status="배송완료")
{
	global $g5, $default;

	$beforedays = date("Y-m-d H:i:s", ( time() - (86400 * (int)$default['de_point_days']) ) ); // 86400초는 하루
	$sql = " select * from {$g5['g5_shop_cart_table']} where ct_status = '$ct_status' and ct_point_use = '0' and ct_time <= '$beforedays' ";
	$result = sql_query($sql);
	for ($i=0; $row=sql_fetch_array($result); $i++) {
		// 회원 ID 를 얻는다.
		$od_row = sql_fetch("select od_id, mb_id from {$g5['g5_shop_order_table']} where od_id = '{$row['od_id']}' ");
		if ($od_row['mb_id'] && $row['ct_point'] > 0) { // 회원이면서 포인트가 0보다 크다면
			$po_point = $row['ct_point'] * $row['ct_qty'];
			$po_content = "주문번호 {$od_row['od_id']} ({$row['ct_id']}) 배송완료";
			insert_point($od_row['mb_id'], $po_point, $po_content, "@delivery", $od_row['mb_id'], "{$od_row['od_id']},{$row['ct_id']}");
		}
		sql_query("update {$g5['g5_shop_cart_table']} set ct_point_use = '1' where ct_id = '{$row['ct_id']}' ");
	}
}


// 배송업체 리스트 얻기
function get_delivery_company($company)
{
	$option = '<option value="">없음</option>'.PHP_EOL;
	$option .= '<option value="자체배송" '.get_selected($company, '자체배송').'>자체배송</option>'.PHP_EOL;

	$dlcomp = explode(")", str_replace("(", "", G5_DELIVERY_COMPANY));
	for ($i=0; $i<count($dlcomp); $i++) {
		if (trim($dlcomp[$i])=="") continue;
		list($value, $url, $tel) = explode("^", $dlcomp[$i]);
		$option .= '<option value="'.$value.'" '.get_selected($company, $value).'>'.$value.'</option>'.PHP_EOL;
	}

	return $option;
}

function GetSiteBasicPrice($it)
{

	global $g5;

	$amount = 0;

	$updateTime = strtotime("+1 hours",strtotime($it[it_real_update]));


	if($it[it_real_usd_price]=="" || $updateTime<=G5_SERVER_TIME){

		if($it[it_real_url]){

			$gp_site = $it[it_real_url];


			$Response = curl($gp_site);

			if($Response){

				$gpPricing = array();
				if(stristr($gp_site,"apmex.com")){

					$tmp_gp_pricing = getExplodeValue(getExplodeValue($Response,"<table class=\"table-volume-pricing\">","</table>"),"<tbody>","</tbody>");
					$gp_pricing = explode("</tr>",str_replace(" itemprop=price","",str_replace(" itemprop=\"price\"","",$tmp_gp_pricing)));
					for($i=0;$i<(count($gp_pricing)-1);$i++){

						$po_qty  = getExplodeValue($gp_pricing[$i],"<td>","</td>");
						$po_cash_price  = trim(preg_replace("/[a-z\$\<\>\/]/","",getShortExplodeValue($gp_pricing[$i],"</td>",1)));
						$po_card_price  = trim(preg_replace("/[a-z\$\<\>\/]/","",getShortExplodeValue($gp_pricing[$i],"</td>",2)));
						/*
						$po_cash_price  = str_replace(",","",str_replace("$","",getExplodeValue($gp_pricing[$i],"class=\"mid\">","</td>")));
						$po_card_price  = str_replace(",","",str_replace("$","",getExplodeValue($gp_pricing[$i],"class=\"last\">","</td>")));
						*/

						if(stristr($po_qty,"-")){
							$tmpQty = explode("-",$po_qty);
							$po_sqty = trim($tmpQty[0]);
							$po_eqty = trim($tmpQty[1]);
						}else{
							$tmpQty = explode("or",$po_qty);
							$po_sqty = trim($tmpQty[0]);
							$po_eqty = "99999";
						}

						if($po_cash_price=="" || $po_cash_price==0 || $po_card_price=="" || $po_card_price==0){
							$isPrice = false;
							break;
						}

						$gpPricing[$i][po_sqty] = $po_sqty;
						$gpPricing[$i][po_eqty] = $po_eqty;
						$gpPricing[$i][po_cash_price] = $po_cash_price;
						$gpPricing[$i][po_card_price] = $po_card_price;
					}

					if(count($gp_pricing)<=1)$isPrice = false;
				}elseif(stristr($gp_site,"gainesvillecoins.com")){

					$tmp_gp_pricing = getExplodeValue($Response,"<tr itemprop=\"offers\" itemscope itemtype=\"http://schema.org/Offer\">","</table>");

					$gp_pricing = explode("</tr>",$tmp_gp_pricing);
					for($i=0;$i<(count($gp_pricing)-1);$i++){

						$po_qty  = getExplodeValue($gp_pricing[$i],"<td class=\"tierQty\" nowrap=\"nowrap\">","</td>");
						$tmp_po_cash_price  = getExplodeValue($gp_pricing[$i],"class=\"price-wire","</td>");
						$po_cash_price = explode("$",$tmp_po_cash_price);
						$tmp_po_card_price  = getExplodeValue($gp_pricing[$i],"class=\"price-cc","</td>");
						$po_card_price = explode("$",$tmp_po_card_price);

						if(stristr($po_qty,"-")){
							$tmpQty = explode("-",$po_qty);
							$po_sqty = trim($tmpQty[0]);
							$po_eqty = trim($tmpQty[1]);
						}else{
							$tmpQty = explode("+",$po_qty);
							$po_sqty = trim($tmpQty[0]);
							$po_eqty = "99999";
						}

						if(str_replace(",","",$po_cash_price[1])=="" || str_replace(",","",$po_cash_price[1])=="0" || str_replace(",","",$po_card_price[1])=="" || str_replace(",","",$po_card_price[1])=="0"){
							$isPrice = false;
							break;
						}

						$gpPricing[$i][po_sqty] = $po_sqty;
						$gpPricing[$i][po_eqty] = $po_eqty;
						$gpPricing[$i][po_cash_price] = str_replace(",","",$po_cash_price[1]);
						$gpPricing[$i][po_card_price] = str_replace(",","",$po_card_price[1]);


					}
					if(count($gp_pricing)<=1)$isPrice = false;
				}
			}
		}

		if($gpPricing[0][po_cash_price]){

			sql_query("update $g5[g5_shop_item_table] set it_real_usd_price = '".$gpPricing[0][po_cash_price]."', it_real_update = '".G5_TIME_YMDHIS."' where it_id = '".$it[it_id]."'");
			$amount = $gpPricing[0][po_cash_price];
		}
	}else $amount = $it[it_real_usd_price];

	return $amount;
}


// 상품가격 업데이트
function UpdateGroupPurchasePrice($gp_id)
{
	global $g5;


	$sql = " select gp_site from {$g5['g5_shop_group_purchase_table']}  where gp_id = '$gp_id' ";

	$row = sql_fetch($sql);

	if($row[gp_site]){

		$gp_site = $row[gp_site];

		$Response = curl($gp_site);
		if($Response){

			$isStock = false;
			$isUpdate = false;
			$isPrice = true;

			$gpPricing = array();

			if(stristr($gp_site,"apmex.com")){

				$tmp_gp_pricing = getExplodeValue(getExplodeValue($Response,"<table class=\"table-volume-pricing\">","</table>"),"<tbody>","</tbody>");

					$gp_pricing = explode("</tr>",str_replace(" itemprop=price","",str_replace(" itemprop=\"price\"","",$tmp_gp_pricing)));

				for($i=0;$i<(count($gp_pricing)-1);$i++){

					$po_qty  = getExplodeValue($gp_pricing[$i],"<td>","</td>");

					$po_cash_price  = trim(preg_replace("/[a-z\$\<\>\/]/","",getShortExplodeValue($gp_pricing[$i],"</td>",1)));
					$po_card_price  = trim(preg_replace("/[a-z\$\<\>\/]/","",getShortExplodeValue($gp_pricing[$i],"</td>",2)));
					/*
					$po_cash_price  = str_replace(",","",str_replace("$","",getExplodeValue($gp_pricing[$i],"class=\"mid\">","</td>")));
					$po_card_price  = str_replace(",","",str_replace("$","",getExplodeValue($gp_pricing[$i],"class=\"last\">","</td>")));
					*/

					if(stristr($po_qty,"-")){

						$tmpQty = explode("-",$po_qty);
						$po_sqty = trim($tmpQty[0]);
						$po_eqty = trim($tmpQty[1]);
					}else{
						$tmpQty = explode("or",$po_qty);
						$po_sqty = trim($tmpQty[0]);
						$po_eqty = "99999";
					}

					if($po_cash_price=="" || $po_cash_price=="0.00" || $po_cash_price==0 || str_replace(",","",$po_card_price)=="" || str_replace(",","",$po_card_price)==0){
						$isPrice = false;
						break;
					}



					$gpPricing[$i][po_sqty] = $po_sqty;
					$gpPricing[$i][po_eqty] = $po_eqty;
					$gpPricing[$i][po_cash_price] = str_replace(",","",$po_cash_price);
					$gpPricing[$i][po_card_price] = str_replace(",","",$po_card_price);

				}
				if(count($gp_pricing)<=1)$isPrice = false;
				$isUpdate = true;
			}elseif(stristr($gp_site,"gainesvillecoins.com")){

				$tmp_gp_pricing = getExplodeValue($Response,"<tr itemprop=\"offers\" itemscope itemtype=\"http://schema.org/Offer\">","</table>");

				$gp_pricing = explode("</tr>",$tmp_gp_pricing);
				for($i=0;$i<(count($gp_pricing)-1);$i++){

					$po_qty  = getExplodeValue($gp_pricing[$i],"<td class=\"tierQty\" nowrap=\"nowrap\">","</td>");
					$tmp_po_cash_price  = getExplodeValue($gp_pricing[$i],"class=\"price-wire","</td>");
					$po_cash_price = explode("$",$tmp_po_cash_price);
					$tmp_po_card_price  = getExplodeValue($gp_pricing[$i],"class=\"price-cc","</td>");
					$po_card_price = explode("$",$tmp_po_card_price);

					if(stristr($po_qty,"-")){
						$tmpQty = explode("-",$po_qty);
						$po_sqty = trim($tmpQty[0]);
						$po_eqty = trim($tmpQty[1]);
					}else{
						$tmpQty = explode("+",$po_qty);
						$po_sqty = trim($tmpQty[0]);
						$po_eqty = "99999";
					}

					if(str_replace(",","",$po_cash_price[1])=="" || str_replace(",","",$po_cash_price[1])=="0" || str_replace(",","",$po_card_price[1])=="" || str_replace(",","",$po_card_price[1])=="0"){
						$isPrice = false;
						break;
					}

					$gpPricing[$i][po_sqty] = $po_sqty;
					$gpPricing[$i][po_eqty] = $po_eqty;
					$gpPricing[$i][po_cash_price] = str_replace(",","",$po_cash_price[1]);
					$gpPricing[$i][po_card_price] = str_replace(",","",$po_card_price[1]);


				}
				if(count($gp_pricing)<=1)$isPrice = false;
				$isUpdate = true;

			}
			else{
				/* 위 4개의 사이트에 해당하지 않을때 isStock값을 true */
				$isStock = true;
			}

			//
			if($isUpdate && $isPrice){

				sql_query("update {$g5['g5_shop_group_purchase_table']}  set gp_stock = '1' where gp_id = '$gp_id'");

				sql_query("delete from $g5[g5_shop_group_purchase_option_table] where gp_id = '$gp_id'");

				for($i=0;$i<count($gpPricing);$i++){
					sql_query("insert into $g5[g5_shop_group_purchase_option_table] set gp_id = '$gp_id', po_num = '$i', po_sqty = '".$gpPricing[$i][po_sqty]."', po_eqty = '".$gpPricing[$i][po_eqty]."', po_cash_price = '".$gpPricing[$i][po_cash_price]."', po_card_price = '".$gpPricing[$i][po_card_price]."'");
				}
			}
			elseif($isStock){
				sql_query("update {$g5['g5_shop_group_purchase_table']}  set gp_stock = '1' where gp_id = '$gp_id'");
			}
			else {
				sql_query("update {$g5['g5_shop_group_purchase_table']}  set gp_stock = '' where gp_id = '$gp_id'");
			}


		}//if($reponse) end
	}
}

function UpdateGroupPurchaseEtc($gp_id){
	global $g5;

	$row=sql_fetch("select gp_site from {$g5['g5_shop_group_purchase_table']} where gp_id = '$gp_id'");

	$Response = curl($row['gp_site']);

	if($Response){

		$is_Update = false;

		if(stristr($row['gp_site'],"apmex.com")){


			$gp_name = getExplodeValue($Response,"<h1 class=\"product-title\" itemprop=\"name\">","</h1>");
			$gp_img = getExplodeValue(getExplodeValue($Response,"<div class=\"small\">","</div>"),"src=\"","\"");
			if(!$gp_img) $gp_img = getExplodeValue(getExplodeValue($Response,"<div class=\"small az-small\">","</div>"),"src=\"","\"");
			//$gp_explan = getExplodeValue($Response,"<div class=\"product-description\">"," <div class=\"product-specs\">");
			$gp_explan = getExplodeValue($Response,"<div class=\"product-description\">","</div>");
			$gp_explan .= getExplodeValue($Response,"<div class=\"product-specs\">","</div>");
			$gp_explan = addslashes($gp_explan);

			$is_Update = true;
		}elseif(stristr($row['gp_site'],"gainesvillecoins.com")){
			$gp_name = getExplodeValue($Response,"<h1 itemprop=\"name\">","</h1>");
			$gp_img = "http://".getExplodeValue($Response,"<a id=\"ctl00_cphSiteBlank_hlLargeImage\" class=\"zoom\" href=\"//","\">");

			if($gp_img=="" || $gp_img=="http://")$gp_img = "http://".getExplodeValue($Response,"<a id=\"ctl00_cphB_hlLargeImage\" class=\"zoom\" href=\"//","\">");

			if($gp_img=="" || $gp_img=="http://")$gp_img = getExplodeValue($Response,"<a id=\"ctl00_cphB_hlLargeImage\" class=\"zoom\" href=\"","\">");

			if($gp_img=="" || $gp_img=="http://"){
				$tmp_gp_img = getExplodeValue($Response,"<div class=\"imgFrame\">","</div>");

				$gp_img = getExplodeValue($tmp_gp_img,"<a href=\"","\" class=\"jsLargeImg zoom\">");
			}

			$gp_explan = getExplodeValue($Response,"<div class=\"text-block\" itemprop=\"description\">","</div>");
			$gp_explan .= getExplodeValue($Response,"<div id=\"product-specs\">","</div>");
			$gp_explan = addslashes($gp_explan);

			$is_Update = true;
		}

		if($is_Update){

			$sql_common = " gp_name				= '".addslashes($gp_name)."',
					gp_img				= '$gp_img',
					gp_explan			= '".$gp_explan."'
					";
			$sql = " update {$g5['g5_shop_group_purchase_table']} set $sql_common  where gp_id = '$gp_id' ";
			sql_query($sql);
		}
	}
}

function getExchangeBRate($USD)
{
	global $_SESSION;

	$totalPrice = CeilGe($USD * $_SESSION[unit_kor_duty],2);

	return $totalPrice;
}

function getExchangeEbayRate($USD)
{
	global $_SESSION;

	$totalPrice = CeilGe($USD * $_SESSION[unit_kor_duty] * 1.075 * 1.1);

	return $totalPrice;
}


/* 고정형 상품 가격계산 */
function getExchangeRate($USD,$gp_id)
{
	global $_SESSION, $g5;

	$sql = " select gp_charge, gp_duty from {$g5['g5_shop_group_purchase_table']}  where gp_id = '$gp_id' ";
	$row = sql_fetch($sql);

	$totalPrice = (($USD * $_SESSION[unit_kor_duty]) * (1 + (($row[gp_charge]+$row[gp_duty])/100))) * 1.1;
	/* (상품 달러가격 * 달러대비원화) X (1 + (수수료 + 관세)/100 )) X 1.1(마지막부가세10%)      */
	return CeilGe($totalPrice);
}

/* 실시간형 상품 가격계산, 금유형, 금무게까지 계산하려고한거 같은데 정확하지 않은 데이터로 인해 이건 사용할수 없음. */
function getExchangeRealRate($gp,$gpo)
{
	global $_SESSION, $g5;

	if($gp[gp_metal_type]=="EC")$product = CeilGe(($gp[gp_metal_etc_price]  + $gpo[po_add_price])* $_SESSION[unit_kor_duty]);
	else $product = CeilGe((($_SESSION['metalUsdPrice'][$gp[gp_metal_type]] * $gp[gp_metal_don])  + $gpo[po_add_price])* $_SESSION[unit_kor_duty]);

	$totalPrice = ($product * (1 + (($gp[gp_charge]+$gp[gp_duty])/100))) * 1.1;
	return $totalPrice;
}

function getExchangeUSDRate($USD,$gp_id)
{
	global $_SESSION, $g5;

	$sql = " select gp_charge, gp_duty from {$g5['g5_shop_group_purchase_table']}  where gp_id = '$gp_id' ";
	$row = sql_fetch($sql);

	//$totalPrice = ($USD  * (1 + (($row[gp_charge]+$row[gp_duty])/100))) * 1.1;
	$totalPrice = $USD;

	return round($totalPrice,2);
}

function getExchangeRealUSDRate($gp,$gpo)
{
	global $_SESSION, $g5;

	if($gp[gp_metal_type]=="EC")$product = $gp[gp_metal_etc_price]  + $gpo[po_add_price];
	else $product = ($_SESSION['metalUsdPrice'][$gp[gp_metal_type]]  * $gp[gp_metal_don]) + $gpo[po_add_price];

	$totalPrice = $product;

	return round($totalPrice,2);
}



function getGroupPurchaseQtyBasicUSD($gp_id,$qty)
{
	global $g5;

	$sql = " select * from {$g5['g5_shop_group_purchase_table']}  where gp_id = '$gp_id' ";
	$gp = sql_fetch($sql);

	$sql = " select po_cash_price, po_add_price from {$g5['g5_shop_group_purchase_option_table']}  where gp_id = '$gp_id' and po_sqty<= '$qty' and po_eqty>='$qty'";
	$row = sql_fetch($sql);

	if($gp[gp_price_type]=="N")$po_cash_price = $row[po_cash_price];
	elseif($gp[gp_price_type]=="Y")$po_cash_price = getExchangeRealUSDRate($gp,$row);

	return $po_cash_price;

}

function getGroupPurchaseQtyBasicPrice($gp_id,$qty)
{
	global $g5;

	$sql = " select * from {$g5['g5_shop_group_purchase_table']}  where gp_id = '$gp_id' ";
	$gp = sql_fetch($sql);

	$sql = " select po_cash_price, po_add_price from {$g5['g5_shop_group_purchase_option_table']}  where gp_id = '$gp_id' and po_sqty<= '$qty' and po_eqty>='$qty'";
	$row = sql_fetch($sql);

	if($gp[gp_price_type]=="N")$po_cash_price = getExchangeRate($row[po_cash_price],$gp_id);
	elseif($gp[gp_price_type]=="Y")$po_cash_price = getExchangeRealRate($gp,$row);

	return CeilGe($po_cash_price);

}

function getGroupPurchaseQtyBasicPrice1($gp_id,$qty)
{
	global $g5;

	$sql = " select * from {$g5['g5_shop_group_purchase_table']}  where gp_id = '$gp_id' ";
	$gp = sql_fetch($sql);

	$sql = " select po_card_price, po_add_price from {$g5['g5_shop_group_purchase_option_table']}  where gp_id = '$gp_id' and po_sqty<= '$qty' and po_eqty>='$qty'";
	$row = sql_fetch($sql);

	if($gp[gp_price_type]=="N")$po_card_price = getExchangeRate($row[po_card_price],$gp_id);
	elseif($gp[gp_price_type]=="Y"){
		$po_card_price = getExchangeRealRate($gp,$row);
		$po_card_price = $po_card_price * 1.03;
	}

	return $po_card_price;

}


// 일반, 경매 종류 분류
function item_type($it_id){
	global $g5;
	$row = sql_fetch("select * from {$g5['g5_shop_item_table']} where it_id='".$it_id."' ");
	if($row[it_price_type] == "A"){
		$ret = "경매";
	}else{
		$ret = "일반";
	}
	return $ret;
}

// 배송 종류 종류 분류
function item_deli_type($od_id, $dealer_status="", $T_od_id="", $status=""){
	global $g5;
	$row = sql_fetch("select * from {$g5['g5_shop_order_table']} where od_id='".$od_id."' ");
	if($row[combine_deli_status] == "y"){
		$ret = "
			통합배송</br>
			<span style='color:#56ccc8;font-size:9px;'>통합배송신청날짜</span></br>
			".date("Y.m.d", $row[combine_deli_date])."</br><img src='".G5_URL."/img/my_view_bn.gif' class='layer_trigger_my' T_od_id='".$T_od_id."' status='".$status."' dealer_status='".$dealer_status."'>";
	}else{
		$ret = "일반배송";
	}
	return $ret;
}

//전체 상품 주문배송조회 상태별 갯수
function od_status_cnt($status){
	global $g5, $member;

	$od_status_row = sql_fetch("
		select count(*) as cnt
		from {$g5['g5_shop_order_table']} as a
		LEFT JOIN {$g5['g5_shop_cart_table']} as b
		ON a.od_id=b.od_id
		where a.mb_id = '{$member['mb_id']}'
		and od_status='".$status."'
	");

	return $od_status_row[cnt];
}

//투데이 상품 주문배송조회 상태별 갯수
function od_status_cnt_today($status){
	global $g5, $member;

	$od_status_row = sql_fetch("
		select count(*) as cnt
		from {$g5['g5_shop_order_table']}
		where od_id in ( select distinct od_id from g5_shop_cart where ct_gubun <> 'P' and ct_type!='' )
		and mb_id = '{$member['mb_id']}'
		and od_status='".$status."'
	");

	return $od_status_row[cnt];
}

//공동구매 상품 주문배송조회 상태별 갯수
function od_status_cnt_gp($status){
	global $g5, $member;

	$od_status_row = sql_fetch("
		select count(*) as cnt
		from {$g5['g5_shop_order_table']}
		where od_id in ( select distinct od_id from g5_shop_cart where ct_gubun = 'P' and ct_type!='' )
		and mb_id = '{$member['mb_id']}'
		and od_status='".$status."'
	");

	return $od_status_row[cnt];
}


// 투데이 매출관리 주문건수
function order_cnt_fn($it_name, $all_status="", $view_status="", $ct_id=""){
	global $g5, $member;

	if($all_status){
		$ret_res = sql_query("select count(*) as cnt from {$g5['g5_shop_cart_table']} where ct_gubun='N' group by od_id ");
		$ret[cnt] = mysql_num_rows($ret_res);
	}else if($view_status){
		$ret = sql_fetch("select count(*) as cnt from {$g5['g5_shop_cart_table']} where ct_gubun='N' and ct_id='$ct_id' ");
	}else{
		$ret_res = sql_query("select count(*) as cnt from {$g5['g5_shop_cart_table']} where ct_gubun='N' and it_name='".$it_name."' group by od_id ");
		$ret[cnt] = mysql_num_rows($ret_res);
	}

	if($ret[cnt]){
		$ret[cnt] = $ret[cnt];
	}else{
		$ret[cnt] = 0;
	}

	return $ret[cnt];
}

// 투데이 매출관리 판매건수
function buy_cnt_fn($it_name, $all_status="", $view_status="", $ct_id=""){
	global $g5, $member;

	if($all_status){
		$ret_res = sql_query("select count(*) as cnt from {$g5['g5_shop_cart_table']} where ct_gubun='N' and ct_status in('결제완료', '상품준비중', '배송대기', '배송중', '배송완료') group by od_id ");
		$ret[cnt] = mysql_num_rows($ret_res);
	}else if($view_status){
		$ret = sql_fetch("select count(*) as cnt from {$g5['g5_shop_cart_table']} where ct_gubun='N' and ct_status in('결제완료', '상품준비중', '배송대기', '배송중', '배송완료') and it_name='".$it_name."' and ct_id='$ct_id' ");
	}else{
		$ret_res = sql_query("select count(*) as cnt from {$g5['g5_shop_cart_table']} where ct_gubun='N' and ct_status in('결제완료', '상품준비중', '배송대기', '배송중', '배송완료') and it_name='".$it_name."' group by od_id ");
		$ret[cnt] = mysql_num_rows($ret_res);
	}

	if($ret[cnt]){
		$ret[cnt] = $ret[cnt];
	}else{
		$ret[cnt] = 0;
	}

	return $ret[cnt];
}

// 투데이 매출관리 물품갯수
function item_cnt_fn($it_name, $all_status="", $view_status="", $ct_id=""){
	global $g5, $member;

	if($all_status){
		$ret = sql_fetch("select SUM(ct_qty) as cnt from {$g5['g5_shop_cart_table']} where ct_gubun='N' and ct_status in('결제완료', '상품준비중', '배송대기', '배송중', '배송완료') ");
	}else if($view_status){
		$ret = sql_fetch("select SUM(ct_qty) as cnt from {$g5['g5_shop_cart_table']} where ct_gubun='N' and ct_id='$ct_id' and ct_status in('결제완료', '상품준비중', '배송대기', '배송중', '배송완료') ");
	}else{
		$ret = sql_fetch("select SUM(ct_qty) as cnt from {$g5['g5_shop_cart_table']} where ct_gubun='N' and ct_status in('결제완료', '상품준비중', '배송대기', '배송중', '배송완료') and it_name='".$it_name."' ");
	}

	if($ret[cnt]){
		$ret[cnt] = $ret[cnt];
	}else{
		$ret[cnt] = 0;
	}

	return $ret[cnt];
}

// 투데이 매출관리 입금합계
function buy_sum_fn($it_name, $all_status="", $view_status="", $ct_id=""){
	global $g5, $member;

	if($all_status){
		$ret = sql_fetch("
			select SUM(b.od_cart_price + b.od_send_cost + b.od_send_cost2) as price from {$g5['g5_shop_cart_table']} as a
			LEFT JOIN {$g5['g5_shop_order_table']} as b
			ON a.od_id=b.od_id
			where ct_gubun='N'
			and ct_status in('결제완료', '상품준비중', '배송대기', '배송중', '배송완료')
		");
	}else if($view_status){
		$ret = sql_fetch("
			select SUM(b.od_cart_price + b.od_send_cost + b.od_send_cost2) as price from {$g5['g5_shop_cart_table']} as a
			LEFT JOIN {$g5['g5_shop_order_table']} as b
			ON a.od_id=b.od_id
			where ct_gubun='N'
			and ct_id='$ct_id'
			and ct_status in('결제완료', '상품준비중', '배송대기', '배송중', '배송완료')
		");
	}else{
		$ret = sql_fetch("
			select SUM(b.od_cart_price + b.od_send_cost + b.od_send_cost2) as price from {$g5['g5_shop_cart_table']} as a
			LEFT JOIN {$g5['g5_shop_order_table']} as b
			ON a.od_id=b.od_id
			where ct_gubun='N'
			and ct_status in('결제완료', '상품준비중', '배송대기', '배송중', '배송완료') and it_name='".$it_name."'
		");
	}

	return $ret[price];
}

// 투데이 매출관리 카드입금
function buy_card_sum_fn($it_name, $all_status="", $view_status="", $ct_id=""){
	global $g5, $member;

	if($all_status){
		$ret = sql_fetch("
			select SUM(ct_price) as price from {$g5['g5_shop_cart_table']} as a
			LEFT JOIN {$g5['g5_shop_order_table']} as b
			ON a.od_id=b.od_id
			where
			a.ct_gubun='N'
			and a.ct_status in('결제완료', '상품준비중', '배송대기', '배송중', '배송완료')
			and b.od_settle_case='신용카드'
		");
	}else if($view_status){
		$ret = sql_fetch("
			select SUM(ct_price) as price from {$g5['g5_shop_cart_table']} as a
			LEFT JOIN {$g5['g5_shop_order_table']} as b
			ON a.od_id=b.od_id
			where
			a.ct_gubun='N'
			and ct_id='$ct_id'
			and a.ct_status in('결제완료', '상품준비중', '배송대기', '배송중', '배송완료')
			and b.od_settle_case='신용카드'
		");
	}else{
		$ret = sql_fetch("
			select SUM(ct_price) as price from {$g5['g5_shop_cart_table']} as a
			LEFT JOIN {$g5['g5_shop_order_table']} as b
			ON a.od_id=b.od_id
			where
			a.ct_gubun='N'
			and a.ct_status in('결제완료', '상품준비중', '배송대기', '배송중', '배송완료')
			and a.it_name='".$it_name."'
			and b.od_settle_case='신용카드'
		");

	}
	return $ret[price];
}

// 투데이 매출관리 포인트입금
function buy_point_sum_fn($it_name, $all_status="", $view_status="", $ct_id=""){
	global $g5, $member;

	if($all_status){
		$ret = sql_fetch("
			select SUM(od_receipt_point) as point from {$g5['g5_shop_cart_table']} as a
			LEFT JOIN {$g5['g5_shop_order_table']} as b
			ON a.od_id=b.od_id
			where
			a.ct_gubun='N'
			and a.ct_status in('결제완료', '상품준비중', '배송대기', '배송중', '배송완료')
			and b.od_receipt_point>0
		");
	}else if($view_status){
		$ret = sql_fetch("
			select SUM(od_receipt_point) as point from {$g5['g5_shop_cart_table']} as a
			LEFT JOIN {$g5['g5_shop_order_table']} as b
			ON a.od_id=b.od_id
			where
			a.ct_gubun='N'
			and ct_id='$ct_id'
			and a.ct_status in('결제완료', '상품준비중', '배송대기', '배송중', '배송완료')
			and b.od_receipt_point>0
		");
	}else{
		$ret = sql_fetch("
			select SUM(od_receipt_point) as point from {$g5['g5_shop_cart_table']} as a
			LEFT JOIN {$g5['g5_shop_order_table']} as b
			ON a.od_id=b.od_id
			where
			a.ct_gubun='N'
			and a.ct_status in('결제완료', '상품준비중', '배송대기', '배송중', '배송완료')
			and a.it_name='".$it_name."'
			and b.od_receipt_point>0
		");
	}

	return $ret[price];
}

// 투데이 매출관리 무통장입금
function buy_mu_sum_fn($it_name, $all_status="", $view_status="", $ct_id=""){
	global $g5, $member;

	if($all_status){
		$ret = sql_fetch("
			select SUM(b.od_cart_price + b.od_send_cost + b.od_send_cost2) as price from {$g5['g5_shop_cart_table']} as a
			LEFT JOIN {$g5['g5_shop_order_table']} as b
			ON a.od_id=b.od_id
			where
			a.ct_gubun='N'
			and a.ct_status in('결제완료', '상품준비중', '배송대기', '배송중', '배송완료')
			and b.od_settle_case='무통장'
		");
	}else if($view_status){
		$ret = sql_fetch("
			select SUM(b.od_cart_price + b.od_send_cost + b.od_send_cost2) as price from {$g5['g5_shop_cart_table']} as a
			LEFT JOIN {$g5['g5_shop_order_table']} as b
			ON a.od_id=b.od_id
			where
			a.ct_gubun='N'
			and ct_id='$ct_id'
			and a.ct_status in('결제완료', '상품준비중', '배송대기', '배송중', '배송완료')
			and b.od_settle_case='무통장'
		");
	}else{
		$ret = sql_fetch("
			select SUM(b.od_cart_price + b.od_send_cost + b.od_send_cost2) as price from {$g5['g5_shop_cart_table']} as a
			LEFT JOIN {$g5['g5_shop_order_table']} as b
			ON a.od_id=b.od_id
			where
			a.ct_gubun='N'
			and a.ct_status in('결제완료', '상품준비중', '배송대기', '배송중', '배송완료')
			and a.it_name='".$it_name."'
			and b.od_settle_case='무통장'
		");
	}

	return $ret[price];
}

// 투데이 매출관리 취소
function buy_cancel_sum_fn($it_name, $all_status="", $view_status="", $ct_id=""){
	global $g5, $member;

	if($all_status){
		$ret = sql_fetch("
			select SUM(b.od_cart_price + b.od_send_cost + b.od_send_cost2) as price from {$g5['g5_shop_cart_table']} as a
			LEFT JOIN {$g5['g5_shop_order_table']} as b
			ON a.od_id=b.od_id
			where
			a.ct_gubun='N'
			and a.it_name='".$it_name."'
			and a.ct_status='취소'
		");
	}else if($view_status){
		$ret = sql_fetch("
			select SUM(b.od_cart_price + b.od_send_cost + b.od_send_cost2) as price from {$g5['g5_shop_cart_table']} as a
			LEFT JOIN {$g5['g5_shop_order_table']} as b
			ON a.od_id=b.od_id
			where
			a.ct_gubun='N'
			and ct_id='$ct_id'
			and a.it_name='".$it_name."'
			and a.ct_status='취소'
		");
	}else{
		$ret = sql_fetch("
			select SUM(b.od_cart_price + b.od_send_cost + b.od_send_cost2) as price from {$g5['g5_shop_cart_table']} as a
			LEFT JOIN {$g5['g5_shop_order_table']} as b
			ON a.od_id=b.od_id
			where
			a.ct_gubun='N'
			and a.ct_status='취소'
		");
	}

	return $ret[price];
}

// 투데이 매출관리 반품
function buy_re_sum_fn($it_name, $all_status="", $view_status="", $ct_id=""){
	global $g5, $member;

	if($all_status){
		$ret = sql_fetch("
			select SUM(ct_price) as price from {$g5['g5_shop_cart_table']} as a
			LEFT JOIN {$g5['g5_shop_order_table']} as b
			ON a.od_id=b.od_id
			where
			a.ct_gubun='N'
			and a.ct_status='반품'
		");
	}else if($view_status){
		$ret = sql_fetch("
			select SUM(ct_price) as price from {$g5['g5_shop_cart_table']} as a
			LEFT JOIN {$g5['g5_shop_order_table']} as b
			ON a.od_id=b.od_id
			where
			a.ct_gubun='N'
			and ct_id='$ct_id'
			and a.ct_status='반품'
		");
	}else{
		$ret = sql_fetch("
			select SUM(ct_price) as price from {$g5['g5_shop_cart_table']} as a
			LEFT JOIN {$g5['g5_shop_order_table']} as b
			ON a.od_id=b.od_id
			where
			a.ct_gubun='N'
			and a.it_name='".$it_name."'
			and a.ct_status='반품'
		");
	}

	return $ret[price];
}

// 투데이 매출관리 교환
function buy_ex_sum_fn($it_name, $all_status="", $view_status="", $ct_id=""){
	global $g5, $member;

	if($all_status){
		$ret = sql_fetch("
			select count(*) as cnt from {$g5['g5_shop_cart_table']} as a
			LEFT JOIN {$g5['g5_shop_order_table']} as b
			ON a.od_id=b.od_id
			where
			a.ct_gubun='N'
			and a.ct_status='교환'
		");
	}else if($view_status){
		$ret = sql_fetch("
			select count(*) as cnt from {$g5['g5_shop_cart_table']} as a
			LEFT JOIN {$g5['g5_shop_order_table']} as b
			ON a.od_id=b.od_id
			where
			a.ct_gubun='N'
			and ct_id='$ct_id'
			and a.ct_status='교환'
		");
	}else{
		$ret = sql_fetch("
			select count(*) as cnt from {$g5['g5_shop_cart_table']} as a
			LEFT JOIN {$g5['g5_shop_order_table']} as b
			ON a.od_id=b.od_id
			where
			a.ct_gubun='N'
			and a.it_name='".$it_name."'
			and a.ct_status='교환'
		");
	}

	return $ret[cnt];
}

// 투데이 매출관리 교환
function buy_misu_sum_fn($it_name, $all_status="", $view_status="", $ct_id=""){
	global $g5, $member;

	if($all_status){
		$ret = sql_fetch("
			select SUM(ct_price) as price from {$g5['g5_shop_cart_table']} as a
			LEFT JOIN {$g5['g5_shop_order_table']} as b
			ON a.od_id=b.od_id
			where
			a.ct_gubun='N'
			and a.ct_status in('결제완료', '상품준비중', '배송대기', '배송중', '배송완료')
			and b.od_misu>0
		");
	}else if($view_status){
		$ret = sql_fetch("
			select SUM(ct_price) as price from {$g5['g5_shop_cart_table']} as a
			LEFT JOIN {$g5['g5_shop_order_table']} as b
			ON a.od_id=b.od_id
			where
			a.ct_gubun='N'
			and ct_id='$ct_id'
			and a.ct_status in('결제완료', '상품준비중', '배송대기', '배송중', '배송완료')
			and b.od_misu>0
		");
	}else{
		$ret = sql_fetch("
			select SUM(ct_price) as price from {$g5['g5_shop_cart_table']} as a
			LEFT JOIN {$g5['g5_shop_order_table']} as b
			ON a.od_id=b.od_id
			where
			a.ct_gubun='N'
			and a.ct_status in('결제완료', '상품준비중', '배송대기', '배송중', '배송완료')
			and a.it_name='".$it_name."'
			and b.od_misu>0
		");
	}

	return $ret[price];
}



// 공동구매 매출관리 주문건수
function order_cnt_fn_gp($it_name, $all_status="", $view_status="", $ct_id=""){
	global $g5, $member;

	if($all_status){
		$ret_res = sql_query("select count(*) as cnt from {$g5['g5_shop_cart_table']} where ct_gubun='P' group by od_id ");
		$ret[cnt] = mysql_num_rows($ret_res);
	}else if($view_status){
		$ret = sql_fetch("select count(*) as cnt from {$g5['g5_shop_cart_table']} where ct_gubun='P' and ct_id='$ct_id' ");
	}else{
		$ret_res = sql_query("select count(*) as cnt from {$g5['g5_shop_cart_table']} where ct_gubun='P' and it_name='".$it_name."' group by od_id ");
		$ret[cnt] = mysql_num_rows($ret_res);
	}

	if($ret[cnt]){
		$ret[cnt] = $ret[cnt];
	}else{
		$ret[cnt] = 0;
	}

	return $ret[cnt];
}

// 공동구매 매출관리 판매건수
function buy_cnt_fn_gp($it_name, $all_status="", $view_status="", $ct_id=""){
	global $g5, $member;

	if($all_status){
		$ret_res = sql_query("select count(*) as cnt from {$g5['g5_shop_cart_table']} where ct_gubun='P' and ct_status in('결제완료', '해외배송대기', '해외배송중', '상품준비중', '배송대기', '배송중', '배송완료') group by od_id ");
		$ret[cnt] = mysql_num_rows($ret_res);
	}else if($view_status){
		$ret = sql_fetch("select count(*) as cnt from {$g5['g5_shop_cart_table']} where ct_gubun='P' and ct_status in('결제완료', '해외배송대기', '해외배송중', '상품준비중', '배송대기', '배송중', '배송완료') and it_name='".$it_name."' and ct_id='$ct_id' ");
	}else{
		$ret_res = sql_query("select count(*) as cnt from {$g5['g5_shop_cart_table']} where ct_gubun='P' and ct_status in('결제완료', '해외배송대기', '해외배송중', '상품준비중', '배송대기', '배송중', '배송완료') and it_name='".$it_name."' group by od_id ");
		$ret[cnt] = mysql_num_rows($ret_res);
	}

	if($ret[cnt]){
		$ret[cnt] = $ret[cnt];
	}else{
		$ret[cnt] = 0;
	}

	return $ret[cnt];
}

// 공동구매 매출관리 물품갯수
function item_cnt_fn_gp($it_name, $all_status="", $view_status="", $ct_id=""){
	global $g5, $member;

	if($all_status){
		$ret = sql_fetch("select SUM(ct_qty) as cnt from {$g5['g5_shop_cart_table']} where ct_gubun='P' and ct_status in('결제완료', '해외배송대기', '해외배송중', '상품준비중', '배송대기', '배송중', '배송완료') ");
	}else if($view_status){
		$ret = sql_fetch("select SUM(ct_qty) as cnt from {$g5['g5_shop_cart_table']} where ct_gubun='P' and ct_id='$ct_id' and ct_status in('결제완료', '해외배송대기', '해외배송중', '상품준비중', '배송대기', '배송중', '배송완료') ");
	}else{
		$ret = sql_fetch("select SUM(ct_qty) as cnt from {$g5['g5_shop_cart_table']} where ct_gubun='P' and ct_status in('결제완료', '해외배송대기', '해외배송중', '상품준비중', '배송대기', '배송중', '배송완료') and it_name='".$it_name."' ");
	}

	if($ret[cnt]){
		$ret[cnt] = $ret[cnt];
	}else{
		$ret[cnt] = 0;
	}

	return $ret[cnt];
}

// 공동구매 매출관리 입금합계
function buy_sum_fn_gp($it_name, $all_status="", $view_status="", $ct_id=""){
	global $g5, $member;

	if($all_status){
		$ret = sql_fetch("
			select SUM(b.od_cart_price + b.od_send_cost + b.od_send_cost2) as price from {$g5['g5_shop_cart_table']} as a
			LEFT JOIN {$g5['g5_shop_order_table']} as b
			ON a.od_id=b.od_id
			where ct_gubun='P'
			and ct_status in('결제완료', '해외배송대기', '해외배송중', '상품준비중', '배송대기', '배송중', '배송완료')
		");
	}else if($view_status){
		$ret = sql_fetch("
			select SUM(b.od_cart_price + b.od_send_cost + b.od_send_cost2) as price from {$g5['g5_shop_cart_table']} as a
			LEFT JOIN {$g5['g5_shop_order_table']} as b
			ON a.od_id=b.od_id
			where ct_gubun='P'
			and ct_id='$ct_id'
			and ct_status in('결제완료', '해외배송대기', '해외배송중', '상품준비중', '배송대기', '배송중', '배송완료')
		");
	}else{
		$ret = sql_fetch("
			select SUM(b.od_cart_price + b.od_send_cost + b.od_send_cost2) as price from {$g5['g5_shop_cart_table']} as a
			LEFT JOIN {$g5['g5_shop_order_table']} as b
			ON a.od_id=b.od_id
			where ct_gubun='P'
			and ct_status in('결제완료', '해외배송대기', '해외배송중', '상품준비중', '배송대기', '배송중', '배송완료') and it_name='".$it_name."'
		");
	}

	return $ret[price];
}

// 공동구매 매출관리 카드입금
function buy_card_sum_fn_gp($it_name, $all_status="", $view_status="", $ct_id=""){
	global $g5, $member;

	if($all_status){
		$ret = sql_fetch("
			select SUM(ct_price) as price from {$g5['g5_shop_cart_table']} as a
			LEFT JOIN {$g5['g5_shop_order_table']} as b
			ON a.od_id=b.od_id
			where
			a.ct_gubun='P'
			and a.ct_status in('결제완료', '해외배송대기', '해외배송중', '상품준비중', '배송대기', '배송중', '배송완료')
			and b.od_settle_case='신용카드'
		");
	}else if($view_status){
		$ret = sql_fetch("
			select SUM(ct_price) as price from {$g5['g5_shop_cart_table']} as a
			LEFT JOIN {$g5['g5_shop_order_table']} as b
			ON a.od_id=b.od_id
			where
			a.ct_gubun='P'
			and ct_id='$ct_id'
			and a.ct_status in('결제완료', '해외배송대기', '해외배송중', '상품준비중', '배송대기', '배송중', '배송완료')
			and b.od_settle_case='신용카드'
		");
	}else{
		$ret = sql_fetch("
			select SUM(ct_price) as price from {$g5['g5_shop_cart_table']} as a
			LEFT JOIN {$g5['g5_shop_order_table']} as b
			ON a.od_id=b.od_id
			where
			a.ct_gubun='P'
			and a.ct_status in('결제완료', '해외배송대기', '해외배송중', '상품준비중', '배송대기', '배송중', '배송완료')
			and a.it_name='".$it_name."'
			and b.od_settle_case='신용카드'
		");

	}
	return $ret[price];
}

// 공동구매 매출관리 포인트입금
function buy_point_sum_fn_gp($it_name, $all_status="", $view_status="", $ct_id=""){
	global $g5, $member;

	if($all_status){
		$ret = sql_fetch("
			select SUM(od_receipt_point) as point from {$g5['g5_shop_cart_table']} as a
			LEFT JOIN {$g5['g5_shop_order_table']} as b
			ON a.od_id=b.od_id
			where
			a.ct_gubun='P'
			and a.ct_status in('결제완료', '해외배송대기', '해외배송중', '상품준비중', '배송대기', '배송중', '배송완료')
			and b.od_receipt_point>0
		");
	}else if($view_status){
		$ret = sql_fetch("
			select SUM(od_receipt_point) as point from {$g5['g5_shop_cart_table']} as a
			LEFT JOIN {$g5['g5_shop_order_table']} as b
			ON a.od_id=b.od_id
			where
			a.ct_gubun='P'
			and ct_id='$ct_id'
			and a.ct_status in('결제완료', '해외배송대기', '해외배송중', '상품준비중', '배송대기', '배송중', '배송완료')
			and b.od_receipt_point>0
		");
	}else{
		$ret = sql_fetch("
			select SUM(od_receipt_point) as point from {$g5['g5_shop_cart_table']} as a
			LEFT JOIN {$g5['g5_shop_order_table']} as b
			ON a.od_id=b.od_id
			where
			a.ct_gubun='P'
			and a.ct_status in('결제완료', '해외배송대기', '해외배송중', '상품준비중', '배송대기', '배송중', '배송완료')
			and a.it_name='".$it_name."'
			and b.od_receipt_point>0
		");
	}

	return $ret[price];
}

// 공동구매 매출관리 무통장입금
function buy_mu_sum_fn_gp($it_name, $all_status="", $view_status="", $ct_id=""){
	global $g5, $member;

	if($all_status){
		$ret = sql_fetch("
			select SUM(b.od_cart_price + b.od_send_cost + b.od_send_cost2) as price from {$g5['g5_shop_cart_table']} as a
			LEFT JOIN {$g5['g5_shop_order_table']} as b
			ON a.od_id=b.od_id
			where
			a.ct_gubun='P'
			and a.ct_status in('결제완료', '해외배송대기', '해외배송중', '상품준비중', '배송대기', '배송중', '배송완료')
			and b.od_settle_case='무통장'
		");
	}else if($view_status){
		$ret = sql_fetch("
			select SUM(b.od_cart_price + b.od_send_cost + b.od_send_cost2) as price from {$g5['g5_shop_cart_table']} as a
			LEFT JOIN {$g5['g5_shop_order_table']} as b
			ON a.od_id=b.od_id
			where
			a.ct_gubun='P'
			and ct_id='$ct_id'
			and a.ct_status in('결제완료', '해외배송대기', '해외배송중', '상품준비중', '배송대기', '배송중', '배송완료')
			and b.od_settle_case='무통장'
		");
	}else{
		$ret = sql_fetch("
			select SUM(b.od_cart_price + b.od_send_cost + b.od_send_cost2) as price from {$g5['g5_shop_cart_table']} as a
			LEFT JOIN {$g5['g5_shop_order_table']} as b
			ON a.od_id=b.od_id
			where
			a.ct_gubun='P'
			and a.ct_status in('결제완료', '해외배송대기', '해외배송중', '상품준비중', '배송대기', '배송중', '배송완료')
			and a.it_name='".$it_name."'
			and b.od_settle_case='무통장'
		");
	}

	return $ret[price];
}

// 공동구매 매출관리 취소
function buy_cancel_sum_fn_gp($it_name, $all_status="", $view_status="", $ct_id=""){
	global $g5, $member;

	if($all_status){
		$ret = sql_fetch("
			select SUM(b.od_cart_price + b.od_send_cost + b.od_send_cost2) as price from {$g5['g5_shop_cart_table']} as a
			LEFT JOIN {$g5['g5_shop_order_table']} as b
			ON a.od_id=b.od_id
			where
			a.ct_gubun='P'
			and a.it_name='".$it_name."'
			and a.ct_status='취소'
		");
	}else if($view_status){
		$ret = sql_fetch("
			select SUM(b.od_cart_price + b.od_send_cost + b.od_send_cost2) as price from {$g5['g5_shop_cart_table']} as a
			LEFT JOIN {$g5['g5_shop_order_table']} as b
			ON a.od_id=b.od_id
			where
			a.ct_gubun='P'
			and ct_id='$ct_id'
			and a.it_name='".$it_name."'
			and a.ct_status='취소'
		");
	}else{
		$ret = sql_fetch("
			select SUM(b.od_cart_price + b.od_send_cost + b.od_send_cost2) as price from {$g5['g5_shop_cart_table']} as a
			LEFT JOIN {$g5['g5_shop_order_table']} as b
			ON a.od_id=b.od_id
			where
			a.ct_gubun='P'
			and a.ct_status='취소'
		");
	}

	return $ret[price];
}

// 공동구매 매출관리 반품
function buy_re_sum_fn_gp($it_name, $all_status="", $view_status="", $ct_id=""){
	global $g5, $member;

	if($all_status){
		$ret = sql_fetch("
			select SUM(ct_price) as price from {$g5['g5_shop_cart_table']} as a
			LEFT JOIN {$g5['g5_shop_order_table']} as b
			ON a.od_id=b.od_id
			where
			a.ct_gubun='P'
			and a.ct_status='반품'
		");
	}else if($view_status){
		$ret = sql_fetch("
			select SUM(ct_price) as price from {$g5['g5_shop_cart_table']} as a
			LEFT JOIN {$g5['g5_shop_order_table']} as b
			ON a.od_id=b.od_id
			where
			a.ct_gubun='P'
			and ct_id='$ct_id'
			and a.ct_status='반품'
		");
	}else{
		$ret = sql_fetch("
			select SUM(ct_price) as price from {$g5['g5_shop_cart_table']} as a
			LEFT JOIN {$g5['g5_shop_order_table']} as b
			ON a.od_id=b.od_id
			where
			a.ct_gubun='P'
			and a.it_name='".$it_name."'
			and a.ct_status='반품'
		");
	}

	return $ret[price];
}

// 공동구매 매출관리 교환
function buy_ex_sum_fn_gp($it_name, $all_status="", $view_status="", $ct_id=""){
	global $g5, $member;

	if($all_status){
		$ret = sql_fetch("
			select count(*) as cnt from {$g5['g5_shop_cart_table']} as a
			LEFT JOIN {$g5['g5_shop_order_table']} as b
			ON a.od_id=b.od_id
			where
			a.ct_gubun='P'
			and a.ct_status='교환'
		");
	}else if($view_status){
		$ret = sql_fetch("
			select count(*) as cnt from {$g5['g5_shop_cart_table']} as a
			LEFT JOIN {$g5['g5_shop_order_table']} as b
			ON a.od_id=b.od_id
			where
			a.ct_gubun='P'
			and ct_id='$ct_id'
			and a.ct_status='교환'
		");
	}else{
		$ret = sql_fetch("
			select count(*) as cnt from {$g5['g5_shop_cart_table']} as a
			LEFT JOIN {$g5['g5_shop_order_table']} as b
			ON a.od_id=b.od_id
			where
			a.ct_gubun='P'
			and a.it_name='".$it_name."'
			and a.ct_status='교환'
		");
	}

	return $ret[cnt];
}

// 공동구매 매출관리 교환
function buy_misu_sum_fn_gp($it_name, $all_status="", $view_status="", $ct_id=""){
	global $g5, $member;

	if($all_status){
		$ret = sql_fetch("
			select SUM(ct_price) as price from {$g5['g5_shop_cart_table']} as a
			LEFT JOIN {$g5['g5_shop_order_table']} as b
			ON a.od_id=b.od_id
			where
			a.ct_gubun='P'
			and a.ct_status in('결제완료', '해외배송대기', '해외배송중', '상품준비중', '배송대기', '배송중', '배송완료')
			and b.od_misu>0
		");
	}else if($view_status){
		$ret = sql_fetch("
			select SUM(ct_price) as price from {$g5['g5_shop_cart_table']} as a
			LEFT JOIN {$g5['g5_shop_order_table']} as b
			ON a.od_id=b.od_id
			where
			a.ct_gubun='P'
			and ct_id='$ct_id'
			and a.ct_status in('결제완료', '해외배송대기', '해외배송중', '상품준비중', '배송대기', '배송중', '배송완료')
			and b.od_misu>0
		");
	}else{
		$ret = sql_fetch("
			select SUM(ct_price) as price from {$g5['g5_shop_cart_table']} as a
			LEFT JOIN {$g5['g5_shop_order_table']} as b
			ON a.od_id=b.od_id
			where
			a.ct_gubun='P'
			and a.ct_status in('결제완료', '해외배송대기', '해외배송중', '상품준비중', '배송대기', '배송중', '배송완료')
			and a.it_name='".$it_name."'
			and b.od_misu>0
		");
	}

	return $ret[price];
}

//투데이 금속 타입
function metal_type($metal){
	$ret = "";
	switch ($metal){
		case "GL":
			$ret = "Gold";
			break;
		case "SL":
			$ret = "Silver";
			break;
		case "PT":
			$ret = "Platinum";
			break;
		case "PD":
			$ret = "Palladium";
			break;
		default:
			$ret = "ECT";
	}
	return $ret;
}

//투데이 금속 SUM
function metal_sum($metal){
	global $g5, $member;

	if($metal == "other"){
		$ret = sql_fetch("select IF(SUM(wr_4) <> 0, SUM(wr_4), 0) as wr_4, IF(SUM(wr_5) <> 0, SUM(wr_5), 0) as wr_5, IF(SUM(wr_6) <> 0, SUM(wr_6), 0) as wr_6, IF(SUM(wr_3) <> 0, SUM(wr_3), 0) as wr_3 from g5_write_portfolio where wr_2 not in ('GL', 'SL', 'PT', 'PD') and mb_id='".$member[mb_id]."' ");
	}else{
		$ret = sql_fetch("select IF(SUM(wr_4) <> 0, SUM(wr_4), 0) as wr_4, IF(SUM(wr_5) <> 0, SUM(wr_5), 0) as wr_5, IF(SUM(wr_6) <> 0, SUM(wr_6), 0) as wr_6, IF(SUM(wr_3) <> 0, SUM(wr_3), 0) as wr_3 from g5_write_portfolio where wr_2='$metal' and mb_id='".$member[mb_id]."' ");
	}

	return $ret;
}

//투데이 금속 SUM
function metal_sum_now($metal){
	global $g5, $member, $_SESSION;

	if($metal == "other"){
		$port_res = sql_query("select * from g5_write_portfolio where wr_2 not in ('GL', 'SL', 'PT', 'PD') and mb_id='".$member[mb_id]."' ");
	}else{
		$port_res = sql_query("select * from g5_write_portfolio where wr_2='$metal' and mb_id='".$member[mb_id]."' ");
	}

	for($i = 0; $port_row = mysql_fetch_array($port_res); $i++){

		$it = sql_fetch("select * from {$g5['g5_shop_item_table']} where it_name='".$port_row[wr_subject]."' ");
		$gp = sql_fetch("select * from {$g5['g5_shop_group_purchase_table']} where gp_name='".$port_row[wr_subject]."' ");
		if($port_row[wr_7] == "N"){
			$ret[usd] += get_dollar($it) * $port_row[wr_3];
			$ret[ko] += get_price($it) * $port_row[wr_3];
		}else if($port_row[wr_7] == "P"){
			$ret[usd] += (getGroupPurchaseQtyBasicUSD($gp[gp_id], 1) * $port_row[wr_3]);
			$ret[ko] += (getGroupPurchaseQtyBasicPrice($gp[gp_id], 1) * $port_row[wr_3]);
		}
	}

	if($ret[usd]){
		$ret[usd] = $ret[usd];
	}else{
		$ret[usd] = 0;
	}

	if($ret[ko]){
		$ret[ko] = $ret[ko];
	}else{
		$ret[ko] = 0;
	}

	return $ret;
}


//==============================================================================
// 쇼핑몰 라이브러리 모음 끝
//==============================================================================





/****************************** 관리대상 함수들  by. JHW *********************************/





/* 공동구매 상품 가격정보 - 구매가 */
function getGroupPurchaseBasicPrice($gp_id)
{
	global $g5;

	$sql = " select * from {$g5['g5_shop_group_purchase_table']}  where gp_id = '$gp_id' ";
	$gp = sql_fetch($sql);

	$sql = " select po_cash_price, po_add_price from {$g5['g5_shop_group_purchase_option_table']}  where gp_id = '$gp_id' and po_num = 0 ";
	$row = sql_fetch($sql);

	//고정형
	if($gp[gp_price_type]=="N") {
		$po_cash_price = getExchangeRate($row[po_cash_price],$gp_id);

		if($_GET[mode] == 'jhw') echo "N2";
	}
	//실시간형
	elseif($gp[gp_price_type]=="Y") {
		$po_cash_price = getExchangeRealRate($gp,$row);

		if($_GET[mode] == 'jhw') echo "Y2";
	}

	return $po_cash_price;

}

/* 공동구매 상품 가격정보 - 카드가 */
function getGroupPurchaseBasicPrice1($gp_id)
{
	global $g5;

	$sql = " select * from {$g5['g5_shop_group_purchase_table']}  where gp_id = '$gp_id' ";
	$gp = sql_fetch($sql);

	$sql = " select po_card_price, po_add_price from {$g5['g5_shop_group_purchase_option_table']}  where gp_id = '$gp_id' and po_num = 0 ";
	$row = sql_fetch($sql);


	/* 고정형 */
	if($gp[gp_price_type]=="N") {
		/* 고정형 상품 가격계산 */
		$po_card_price = getExchangeRate($row[po_card_price],$gp_id);

		if($_GET[mode] == 'jhw') echo "N2";
	}
	/* 실시간형 */
	elseif($gp[gp_price_type]=="Y") {
		/* 실시간형 상품 가격계산 */
		$po_card_price = getExchangeRealRate($gp,$row);
		$po_card_price = $po_card_price * 1.03;

		if($_GET[mode] == 'jhw') echo "Y2";
	}

	return $po_card_price;

}







?>