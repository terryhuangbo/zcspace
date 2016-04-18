<script type="text/javascript" src="http://4.1diantao.sinaapp.com/html/v20150420/js/jquery.min.js">

</script>
<script type="text/javascript">
	$.post('http://4.1diantao.sinaapp.com/web/index.php/api/user/register',
{
  "appkey": "xinguo","phoneNumber":"13581680624","pwd":"adbcd1234","verifyCode":"123456"
},function  (data) {
	alert();
},'json'
		); 
</script>