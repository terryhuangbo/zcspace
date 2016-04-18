<html>
<head>
	<title>Upload Form</title>
</head>
<body>

	<?php echo $error;?>
	<b>用户头像上传</b>
	<form method="post" action="avatar" enctype="multipart/form-data" /> 
	 	<br />
		<b>userId:</b><input type="text" name="userId" value="user5584effcaa3ef" />
		<input type="file" name="avatarUrl" size="20" />
		<br /><br />
		<input type="submit" value="上传" />
	</form>
	<hr />
 
	<b>项目Logo上传</b>
	<form method="post" action="projectLogo" enctype="multipart/form-data" /> 
	 	<br />
	 	<b>userId:</b><input type="text" name="userId" value="user5584effcaa3ef" />
	 	<br /><br />
		<b>projdectId:</b><input type="text" name="projectId" value="" />
		<input type="file" name="logo" size="20" />
		<br /><br />
		<input type="submit" value="上传" />
	</form>
	<hr />

	<b>孵化器Logo上传</b>
	<form method="post" action="incubatorLogo" enctype="multipart/form-data" /> 
	 	<br />
	 	<b>userId:</b><input type="text" name="userId" value="user5584effcaa3ef" />
	 	<br /><br />
		<b>incubatorId:</b><input type="text" name="incubatorId" value="" />
		<input type="file" name="logo" size="20" />
		<br /><br />
		<input type="submit" value="上传" />
	</form>
	<hr />

	<b>投资方Logo上传</b>
	<form method="post" action="investorLogo" enctype="multipart/form-data" /> 
	 	<br />
	 	<b>userId:</b><input type="text" name="userId" value="user5584effcaa3ef" />
	 	<br /><br />
		<b>investorId:</b><input type="text" name="investorId" value="" />
		<input type="file" name="logo" size="20" />
		<br /><br />
		<input type="submit" value="上传" />
	</form>
	<hr />

	<b>孵化器文档上传</b>
	<form method="post" action="incubatorFile" enctype="multipart/form-data" /> 
	 	<br />
	 	<b>userId:</b><input type="text" name="userId" value="user5584effcaa3ef" />
	 	<br /><br />
		<b>incubatorId:</b><input type="text" name="incubatorId" value="" />
		<input type="file" name="files" size="20" />
		<br /><br />
		<input type="submit" value="上传" />
	</form>
	<hr />

	<b>孵化器文档删除</b>
	<form method="post" action="deleteIncubatorFile" enctype="multipart/form-data" /> 
	 	<br />
	 	<b>userId:</b><input type="text" name="userId" value="user5584effcaa3ef" />
	 	<br /><br />
		<b>incubatorFileId:</b><input type="text" name="fileId" value="" />
		<br /><br />
		<input type="submit" value="提交" />
	</form>
	<hr />

	<b>添加明星项目</b>
	<form method="post" action="starProject" enctype="multipart/form-data" /> 
	 	<br />
	 	<b>userId:</b><input type="text" name="userId" value="user5584effcaa3ef" />
	 	<br /><br />
	 	<input type="file" name="logo" size="20" />
	 	<br /><br />
		<b>incubatorId:</b><input type="text" name="incubatorId" value="incubator5584effcaa3ef" />
		<br /><br />
		<b>incuProId:</b><input type="text" name="incuProId" value="" />
		<br /><br />
		<b>name:</b><input type="text" name="name" value="" />
		<br /><br />
		<b>entreOrentation:</b><input type="text" name="entreOrentation" value="" />
		<br /><br />
		<b>brief:</b><input type="text" name="brief" value="" />
		<br /><br />
		<b>process:</b><input type="text" name="process" value="" />
		
		<br /><br />
		<input type="submit" value="上传" />
	</form>
	<hr />

	<b>添加团队成员</b>
	<form method="post" action="teamer" enctype="multipart/form-data" /> 
		<br />
	 	<b>userId:</b><input type="text" name="userId" value="user5584effcaa3ef" />
	 	<br /><br />
	 	<b>projectId:</b><input type="text" name="projectId" value="project55388437541c3" />
	 	<br /><br />
		<b>teamerId:</b><input type="text" name="teamerId" value="" />
		<br /><br />
	 	<input type="file" name="avatarUrl" size="20" />
	 	<br /><br />
		<b>name:</b><input type="text" name="name" value="" />
		<br /><br />
		<b>position:</b><input type="text" name="position" value="" />
		<br /><br />
		<b>school:</b><input type="text" name="school" value="" />
		<br /><br />
		<br /><br />
		<input type="submit" value="上传" />
	</form>
	<hr />

	<b>添加孵化器活动投资人</b>
	<form method="post" action="incuActInvestor" enctype="multipart/form-data" /> 
	 	<br />
	 	<b>userId:</b><input type="text" name="userId" value="user5584effcaa3ef" />
	 	<br /><br />
	 	<input type="file" name="avatarUrl" size="20" />
	 	<br /><br />
		<b>incubatorActivityId:</b><input type="text" name="incubatorActivityId" value="" />
		<br /><br />
		<b>name:</b><input type="text" name="name" value="" />
		
		<br /><br />
		<input type="submit" value="上传" />
	</form>
	<hr />

	<b>添加投资人</b>
	<form method="post" action="investPartner" enctype="multipart/form-data" /> 
	 	<br />
	 	<b>userId:</b><input type="text" name="userId" value="user5584effcaa3ef" />
	 	<br /><br />
	 	<input type="file" name="logo" size="20" />
	 	<br /><br />
		<b>investorId:</b><input type="text" name="investorId" value="investor559a82b0c9cd9" />
		<br /><br />
		<b>investPartId:</b><input type="text" name="investPartId" value="" />
		<br /><br />
		<b>name:</b><input type="text" name="name" value="" />		
		<br /><br />
		<input type="submit" value="上传" />
	</form>
	<hr />

	<b>添加投资项目</b>
	<form method="post" action="investProject" enctype="multipart/form-data" /> 
	 	<br />
	 	<b>userId:</b><input type="text" name="userId" value="user5584effcaa3ef" />
	 	<br /><br />
	 	<input type="file" name="logo" size="20" />
	 	<br /><br />
		<b>incubatorId:</b><input type="text" name="investorId" value="investor559a82b0c9cd9" />
		<br /><br />
		<b>name:</b><input type="text" name="name" value="" />
		<br /><br />
		<b>entreOrentation:</b><input type="text" name="entreOrentation" value="" />
		<br /><br />
		<b>brief:</b><input type="text" name="brief" value="" />
		<br /><br />
		<b>process:</b><input type="text" name="process" value="" />
		
		<br /><br />
		<input type="submit" value="上传" />
	</form>
	<hr />


</body>
</html>