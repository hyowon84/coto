<? 
//서버IP, 사무실IP주소는 제외
if($_SERVER['REMOTE_ADDR'] != '221.146.206.90' && $_SERVER['REMOTE_ADDR'] != '106.244.129.133') {
?>
<script type="text/javascript" src="http://wcs.naver.net/wcslog.js"></script>
<script type="text/javascript">
if(!wcs_add) var wcs_add = {};
wcs_add["wa"] = "2b261824c9f800";
wcs_do();
/* NAVER_LOG */	
</script>

<script>
  (function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
  (i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
  m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
  })(window,document,'script','//www.google-analytics.com/analytics.js','ga');

  ga('create', 'UA-72325378-1', 'auto');
  ga('send', 'pageview');
/* GOOGLE_LOG */
</script>
<? 
}
?>