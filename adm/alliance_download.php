<?php
chmod("../data/alliance", 0707);

$filepath = '../data/alliance/'.$_GET[name];
$filesize = filesize($filepath);
$path_parts = pathinfo($filepath);
$filename = $path_parts['basename'];
$extension = $path_parts['extension'];

header("Pragma: public");
header("Expires: 0");
header("Content-Type: application/octet-stream");
header("Content-Disposition: attachment; filename=\"$filename\"");
header("Content-Transfer-Encoding: binary");
header("Content-Length: $filesize");
 
ob_clean();
flush();
readfile($filepath);

chmod("../data/alliance", 0755);
?>