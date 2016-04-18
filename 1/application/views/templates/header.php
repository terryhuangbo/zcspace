<!DOCTYPE html>
<html ng-app="app">
<head lang="zh-cn">
  <meta charset="UTF-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no">
  <title><?php echo $pageTitle; ?></title>

  <link rel="stylesheet"
        href="http://1.sheyingqiezi.sinaapp.com/web/resource/libs/font-awesome/css/font-awesome.min.css"/>
  <link rel="stylesheet" href="http://1.sheyingqiezi.sinaapp.com/web/resource/css/base.css"/>
  <link rel="stylesheet" href="http://1.sheyingqiezi.sinaapp.com/web/resource/css/app.css"/>

  <script src="http://1.sheyingqiezi.sinaapp.com/web/resource/libs/angular/angular.min.js"></script>
  <script src="http://1.sheyingqiezi.sinaapp.com/web/resource/libs/zeptojs/zepto.min.js"></script>
  <script src="http://1.sheyingqiezi.sinaapp.com/web/resource/js/controller.js"></script>
  <script type="text/javascript">
    $userId = 'user_54be51063d489';
    $photographerId = getPhotographerId();

    function getPhotographerId() {
      if (location.search) {
        return location.search.substring(4);
      }
    }
  </script>
</head>

<body>
<div class="viewport">
  <header ng-controller="HeaderController">
    <nav class="header">
      <a href="#" class="back" ng-click="Header.goBack()"><i class="fa fa-angle-left fa-lg"></i></a>

      <h2 class="header-title"><?php echo $pageTitle; ?></h2>

      <a href="#" class="favorite" ng-click="Header.favorite()"><i class="fa fa-star-o"></i></a>
      <a href="#" class="share" ng-click="Header.share()"><i class="fa fa-share"></i></a>
    </nav>
  </header>

<div class="viewport">

