<?php

$block_list = "	46.161.9.6 ";

$IP주소 = $_SERVER['REMOTE_ADDR'];

if(eregi($IP주소, $block_list)) {
	exit;
}

?>