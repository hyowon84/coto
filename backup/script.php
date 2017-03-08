<?php

/*
	/var/lib/mysql/  DB데이터는 여기에
*/

@set_time_limit (0); 
@ini_set ("memory_limit", "20M");
$save_dir = '/home/coin/db';//저장할 장소, 절대경로/dbbackup 퍼미션은 777로 주세요 
$dateYmdH = date("YmdH"); 
$break_dbname = 'mysql|information_schema|test';//디비백업에 열외시킬 디비들 
$deldate = 7;//삭제일 현재는 7일지난 데이터는 삭제
echo "[백업한 sql] \n\n"; 
mysql_connect('localhost', 'root', '!alfmgpswl!@'); 
$db_list = mysql_list_dbs();
$i = 0; 
$cnt = mysql_num_rows($db_list); 
while ($i < $cnt) {
  $dbname = mysql_db_name($db_list, $i); 
  $i++;
  if (!empty($dbname)) {
    if (!empty($break_dbname) && preg_match("`^(" . $break_dbname . ")$`i", $dbname))
       continue;
    echo "[ " . $dbname . "]\n";
    exec('/usr/bin/mysqldump -hlocalhost -uroot -p!alfmgpswl!@ ' . $dbname . ' > ' . $save_dir . '/' . $dbname . $dateYmdH . '.sql');
   } 
}

echo "\n\n[삭제한 sql] \n\n"; 
$d = dir($save_dir); 
while (false !== ($entry = $d->read())) {
  if (!preg_match("|^\.|", $entry)) {
      if (!empty($break_dbname) && preg_match("`^(" . $break_dbname . ")[0-9]{0,10}\.sql$`i", $entry))
         continue;
      $temp_file = $save_dir . '/' . $entry; 
      $temp_mtime = @filemtime($temp_file); 
      $temp_during = time() - $temp_mtime;
      if ($temp_during > (60 * 60 * 24 * $deldate)){ 
  
        $temp_cnt++; 
        @unlink($temp_file); 
        echo "[" . $temp_file . "]\n"; 
      } 
  } 
}
$d->close();
exit;
?>