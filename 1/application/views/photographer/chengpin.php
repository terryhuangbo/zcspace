  <div ng-controller="ChengPinController">
    <ul class="goods-list">

      <?php foreach ($extraServices as $service) {
        ?>
        <li>
          <div class="items" serviceId="<?php echo $service->id;?>">
            <div class="check-wrapper"></div>
            <div class="item-core">
              <a class="goods-pic" href="#">
                <img src="<?php echo $service->pimgUrl; ?>" style="width: 70px;height: 70px;"/>
              </a>

              <div class="goods-info">
                <h4 id="goods-title"><?php echo $service->name; ?></h4>

                <div class="quantity-wrapper">
                  <a class="quantity-decrease" href="#">-</a>
                  <input type="text" name="quantity" class="quantity" value="1" readonly/>
                  <a class="quantity-increase" href="#">+</a>
                </div>

                <p>规格：<span><?php echo $service->detail; ?></span></p>

                <p class="price">￥<?php echo $service->price; ?></p>
              </div>
            </div>
          </div>
        </li>
      <?php
      }
      ?>
    </ul>

    <div class="btn-wrapper">
      <a class="btn btn-large orange" href="#" ng-click="confirmChengPin()">确定</a>
    </div>
  </div>
</div>
