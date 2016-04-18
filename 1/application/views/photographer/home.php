  <div ng-controller="HomeController">
      <div class="row author">
        <div class="col-xs-4">
        <div class="avatar">
          <?php if ($isVip == 1) {?>

          <div class="vip">
            <img src="http://1.sheyingqiezi.sinaapp.com/web/resource/images/vip.png"/>
          </div>
          <?php
          }
          ?>
          <img src="<?php echo $logoUrl; ?>"/>
        </div>
        </div>
        <div class="col-xs-8">
          <div class="row">
            <div class="col-xs-11 description">
              <h3><?php echo $name; ?>
                <small></small>
                <span class="price"><?php echo $priceDec; ?></span>
              </h3>
              <p class="dept"><?php echo $title; ?></p>

              <p class="star">
                <?php
                for ($i = 0; $i < $level; $i++) {
                  ?>
                  <a href="#"><img src="http://1.sheyingqiezi.sinaapp.com/web/resource/images/xing1.jpg"/></a>

                <?php }
                for ($i = $level + 1; $i <= 5; $i++) {
                  ?>
                  <a href="#"><img src="http://1.sheyingqiezi.sinaapp.com/web/resource/images/xing2.jpg"/></a>

                <?php
                }
                ?>
              </p>

              <p class="skills"><?php echo $strong; ?></p>
            </div>
          </div>
        </div>
      </div>
      <p class="divider"></p>

      <div class="bar">
        <a href="<?php echo 'http://1.sheyingqiezi.sinaapp.com/web/index.php/photographers/comment?photographerId=' . $id ?>">
          网友点评 <span class="count">（<?php echo $commentsCount; ?>）</span>
          <i class="fa fa-angle-right fa-lg pull-right"></i></a>
      </div>
      <p class="divider"></p>

    <div class="bar with-bottom">
        <a href="#">作品专辑 <span class="count">（<?php echo count($albums); ?>）</span></a>
    </div>

    <div class="row opus">
          <?php
          foreach ($albums as $album) {
            ?>
            <div class="col-xs-3">
              <figure>
                <img src="<?php echo $album->pimgUrl; ?>" width="90" height="50" alt=""/>
                <figcaption><?php echo $album->cname; ?></figcaption>
              </figure>
            </div>
          <?php
          }
          ?>
    </div>
      <p class="divider"></p>
      <div class="bar with-bottom left-color">
        <a href="combo.html">选择套餐
          <span class="count">（5）</span>
          <i class="fa fa-angle-right fa-lg pull-right"></i>
        </a>
      </div>

      <?php
      $count = count($planList);
      for ($i=0; $i < count($planList) ; $i=$i+2) {
        if($i < $count)
        {
          $plan1 = $planList[$i];
        }

        if($i + 1 < $count)
        {
          $plan2 = $planList[$i + 1];
        }

      if($i < $count)
      {

      ?>
       <div class="row combo">
          <div class="col-xs-6" >
          <div class="row">
            <div class="col-xs-2">
              <input type="checkbox" name="comboId" value="<?php echo $plan1->userPhotoPlanId; ?>"/>
            </div>
            <div class="col-xs-5">
              <figure>
                <img src="<?php echo $plan1->pimgUrl; ?>"/>
              </figure>
            </div>
            <div class="col-xs-5">
              <h4><?php echo $plan1->pname; ?></h4>
              <p class="price"><?php echo $plan1->price; ?></p>
            </div>
          </div>
        </div>
    <?php } ?>

    <?php if($i + 1 < $count)
        {
    ?>
          <div class="col-xs-6" planId="<?php echo $plan2->userPhotoPlanId; ?>">
        <div class="col-xs-6" >
          <div class="row">
            <div class="col-xs-2">
              <input type="checkbox" value="<?php echo $plan1->userPhotoPlanId; ?>"/>
            </div>
            <div class="col-xs-5">
              <figure>
                <img src="<?php echo $plan2->pimgUrl; ?>"/>
              </figure>
            </div>
            <div class="col-xs-5">
              <h4><?php echo $plan2->pname; ?></h4>
              <p class="price"><?php echo $plan2->price; ?></p>
            </div>
          </div>
        </div>
      <?php
      }

    }
      ?>
      </div>
      <p class="divider"></p>

      <div class="bar with-bottom left-color">
        <a href="../photographers/chengpin">选择成品

          <i class="fa fa-angle-right fa-lg pull-right"></i>
        </a>
      </div>

      <div class="bar with-bottom left-color">
        <a href="<?php echo '../photographers/schedule?userId=' . $id . '&month=' . date('Y/m/d'); ?>">选择时间
          <i class="fa fa-angle-right fa-lg pull-right"></i>
        </a>
      </div>

      <div class="bar with-bottom left-color">
        <a href="<?php echo '../photographers/address?userid=' . $id; ?>">选择地址

          <i class="fa fa-angle-right fa-lg pull-right"></i>
        </a>
      </div>
      <p class="divider"></p>

      <div class="btn-wrapper">
        <a class="btn btn-large orange" ng-click="order()" href="javascript:;">下单</a>
      </div>

      <div style="display: none;">
          <form id="homeform" action="http://1.sheyingqiezi.sinaapp.com/web/index.php/photographers/order" method="post">
            <input type="hidden" name="orderData" >
          </form>
        </div>
  </div>
</div>
