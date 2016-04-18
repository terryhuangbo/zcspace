<!DOCTYPE html>
<html ng-app="app">
<head lang="en">
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


  <link rel="stylesheet" href="http://1.sheyingqiezi.sinaapp.com/h5/libs/font-awesome/css/font-awesome.min.css"/>
  <link rel="stylesheet" href="http://1.sheyingqiezi.sinaapp.com/h5/libs/amazeui/dist/css/amazeui.css"/>
  <link rel="stylesheet" href="http://1.sheyingqiezi.sinaapp.com/h5/css/base.css"/>

  <style>
    .time-container {
      position: relative;
      top: 320px;
      color: #844598;
      font-size: 1em;
      font-weight: 600;
    }

    .time-bar {
      background-color: #844598;
      color: #FFFFFF;
      line-height: 40px;
      font-size: 1.6rem;
      font-weight: normal;
    }

    [layout="row"] {
      display: -webkit-flex;
      display: flex;
      -webkit-flex-flow: row nowrap;
      flex-flow: row nowrap;
      -webkit-justify-content: space-around;
      justify-content: space-around;
      -webkit-align-items: stretch;
      align-items: stretch;
      margin-bottom: 10px;
    }

    [layout="row"] > div {
      line-height: 50px;
      width: 50px;
    }

    [layout="row"] > div:first-child {
      line-height: 50px;
      width: inherit;
    }

    [layout="row"] > .circle {
      border: 1px solid #834597;
      border-radius: 50%;
      text-align: center;
      cursor: pointer;
    }

    [layout="row"] > .circle:after {
      content: "";
      /*border: 1px solid #834597;*/
      /*transform: rotate(-90deg);*/
    }

    [layout="row"] > .active {
      background-color: #844598;
      color: #FFFFFF;
    }

  </style>
</head>

<body>
<div class="viewport">
<input type="hidden" id="datepicker" class="am-form-field" placeholder="日历组件" data-am-datepicker="{theme: 'purple'}"
       readonly/>

<div class="time-container">
  <div layout="row" class="time-bar">
    时间
  </div>
  <div layout="row">
    <div>上午</div>

    <div class="circle">
      8:00
    </div>

    <div class="circle">
      9:00
    </div>

    <div class="circle">
      10:00
    </div>

    <div class="circle">
      11:00
    </div>

    <div class="circle">
      12:00
    </div>
  </div>

  <div layout="row">
    <div>下午</div>

    <div class="circle">
      13:00
    </div>

    <div class="circle">
      14:00
    </div>

    <div class="circle">
      15:00
    </div>

    <div class="circle">
      16:00
    </div>

    <div class="circle">
      17:00
    </div>

  </div>

  <div layout="row">
    <div>晚上</div>

    <div class="circle">
      18:00
    </div>

    <div class="circle">
      19:00
    </div>

    <div class="circle">
      20:00
    </div>

    <div class="circle">
      21:00
    </div>

    <div class="circle">
      22:00
    </div>
  </div>
</div>

<div class="btn-wrapper" style="position: relative; top: 340px;">
  <button type="button" class="btn btn-large orange -confirm">确定</button>
</div>

<script src="http://1.sheyingqiezi.sinaapp.com/h5/libs/jquery/dist/jquery.min.js"></script>
  <script src="http://1.sheyingqiezi.sinaapp.com/h5/libs/amazeui/dist/js/amazeui.js"></script>
  <script>
    $(function () {
      // 禁止日期控件选择后隐藏
      $(document).on('click', 'body', function () {
        return false;
      });

      $('#datepicker').datepicker('open')
          .on('changeDate.datepicker.amui', function (event) {
            sessionStorage.setItem('selectedDay', $(this).val());
          });

      $('.am-datepicker-header').next("tr").addClass("am-datepicker-header");

      $(document).on('click', '.circle', function () {
        var $this = $(this),
            time = parseInt($this.text());
        $.each($('.circle'), function (i, item) {
          $(item).removeClass('active');
        });
        $this.addClass('active');
        sessionStorage.setItem('selectedTime', time)
      });
    });
  </script>