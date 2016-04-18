<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="">
    <meta name="author" content="">
    <link rel="icon" href="../../favicon.ico">

    <title>择业帮，助你决策职业未来</title>

    <!-- Bootstrap core CSS -->
    <link href="http://lib.sinaapp.com/js/bootstrap/v3.0.0/css/bootstrap.min.css" rel="stylesheet">

    <!-- Custom styles for this template -->
    <link href="/resource/css/banner.css" rel="stylesheet">
  </head>

  <body>
<!-- Begin page content -->
<div class="container">

<?php 
foreach ($banners as $banner) {?>
<div class="row">
  <div class="pic">
    <a href="<?php echo $banner["siteUrl"] ?>">
      <img class="img-responsive" src="<?php echo $banner["imgUrl"] ?>">
      <span class="pic_info h4" ><?php echo $banner['title'] ?></span>
      </a>
  </div>
</div>
<?php } ?>
</div>


    <!-- Bootstrap core JavaScript
    ================================================== -->
    <!-- Placed at the end of the document so the pages load faster -->
    <script type="text/javascript" src="http://lib.sinaapp.com/js/jquery/2.0.3/jquery-2.0.3.min.js"></script>
    <script type="text/javascript" src="http://lib.sinaapp.com/js/bootstrap/v3.0.0/js/bootstrap.min.js"></script>
  </body>
</html>
