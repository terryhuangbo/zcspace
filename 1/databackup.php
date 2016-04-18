<?php
$StorageName = 'dbbackup';//Storage存储空间名称
$DataPath = date('Y').'/'.date('m').'/'.date('Y-m-d.H:i:s').'.sql.zip'; //按年/月目录存储
$Storage = new SaeStorage();
$StorageAttr = array('private'=>false); //设置存储空间为公有
$Storage->setDomainAttr($StorageName, $StorageAttr);
$DeferredJob = new SaeDeferredJob();
$TaskID = $DeferredJob->addTask('export','mysql',$StorageName,$DataPath,SAE_MYSQL_DB,'',''); //备份数据库
if($TaskID){
echo "RUN:Success";//备份成功输出
}
else{
send_mail('10540860@qq.com', '数据库备份失败', '数据库备份失败！<br>错误代码：'.$DeferredJob->errno().'<br>错误消息：'.$DeferredJob->errmsg().'<br>报告时间：'.date('Y-m-d H:i:s'));
echo 'RUN:Failure';//备份失败输出
}
$StorageAttr = array('private'=>true); //设置存储空间为私有
$Storage->setDomainAttr($StorageName, $StorageAttr);
?>