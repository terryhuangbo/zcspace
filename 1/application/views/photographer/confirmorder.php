  <div ng-controller="ConfirmOrderController">
    <div class="row order">
      <div class="col-xs-4">
        <img src="<?php echo $plan->photoUrl; ?>"/>
      </div>
      <div class="col-xs-7">
        <h4 class="order-title"><?php echo $plan->planName; ?>
          <small class="price">¥<?php echo $plan->price; ?></small>
        </h4>
        <div class="detail">
          <p class="p">
            <?php echo $plan->planDetail; ?>
          </p>
        </div>
      </div>
    </div>

    <div class="order-container">

      <?php
      foreach ($extraServices as $service) {
      ?>

      <div class="order-item">
        <img src="<?php echo $service['service']->photoUrl; ?>"/>
        <h4 class="order-title"><?php echo $service['service']->name; ?></h4>

        <h6 class="price">￥<?php echo $service['service']->price; ?></h6>
        <h6 class="price"><?php echo $service['count']; ?></h6>
      </div>
      <?php 
      }
       ?>
    </div>
    <p class="divider"></p>

    <div class="bar with-bottom">
      <a href="#">服务时间
        <small><?php echo $time['detail']; ?></small>
        <i class="fa fa-angle-right fa-lg pull-right"></i>
      </a>
    </div>
    <p class="divider"></p>
    <div class="bar with-bottom">
      <a href="address.html">服务地址
        <small><?php echo $address->detail; ?></small>
        <i class="fa fa-angle-right fa-lg pull-right"></i>
      </a>
    </div>
    <p class="divider"></p>

    <div class="comment">
      <h3>备注信息</h3>
      <textarea name="comment" rows="4" ng-model="note" placeholder="请输入备注信息"><?php echo $notes;?></textarea>
    </div>

    <div class="sum">
      <h2 class="discount">合计：￥<?php echo $total;?></h2>
      <h4 class="original-price">原价：￥<?php echo $originalTotal;?></h4>
    </div>

    <div class="btn-wrapper">
      <a class="btn btn-large orange" href="#" ng-click="confirmOrder()">确认提交</a>
    </div>
  </div>

</div>
