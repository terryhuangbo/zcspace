<div ng-controller="AddressController">
  <form class="basic-form my-address" action="#">
    <div class="form-group">
      <label class="form-label">
        <i class="fa fa-user"></i> 姓名：
      </label>
      <input type="text" class="form-input" name="name" ng-model="name" required/>
    </div>

    <div class="form-group">
      <label class="form-label">
        <i class="fa fa-phone"></i> 手机：
      </label>
      <input type="text" class="form-input" name="phoneNumber" ng-model="phoneNumber" required/>
    </div>

    <div class="form-group">
      <label class="form-label">
        <i class="fa fa-map-marker"></i> 地区：
      </label>
      <input type="hidden" class="form-input" name="area" ng-model="area" required/>

      <div class="region">
        <a href="#"><i class="fa fa-angle-right"></i></a>
      </div>
    </div>

    <div class="form-group">
      <label class="form-label">
        <i class="fa fa-map-marker"></i> 地址：
      </label>
      <input type="text" class="form-input" name="detailAddress" ng-model="detailAddress" required/>
    </div>

    <div class="form-group form-btn">
      <button class="btn btn-add" ng-click="addAddress()"><i class="fa fa-plus"></i></button>
    </div>
  </form>

  <p class="divider"></p>

  <ul class="addr-list">
    <?php if (count($addresses) > 0) {
      ?>

      <li>
        <h4>使用过的地址：</h4>
      </li>
    <?php
    }
    ?>
    <?php
    foreach ($addresses as $address) {
      ?>

      <li class="addr-item">
        <?php if ($address->isDefault == '1') { ?>
          <input type="radio" value="<?php echo $address->id; ?>" name="address" checked
            />
        <?php
        } else {
          ?>
          <input type="radio" value="<?php echo $address->id; ?>" name="address"
            />
        <?php
        }
        ?>
        <p class="addr-header">
          <span><?php echo $address->name; ?></span>
          <span class="user-phone addr"><?php echo $address->mobile; ?></span>

          <?php if ($address->isDefault == '1') { ?>
            <span class="user-option addr"><i class="fa fa-map-marker"></i> 默认地址</span>
          <?php
          }
          ?>
        </p>
        <span class="addr-text"><?php echo $address->detail; ?></span>

        <div class="new-addr-btn">
          <a href="#"> 编辑 </a><span class="new-addr-bar">|</span><a href="#"> 删除 </a>
        </div>
      </li>
    <?php
    }
    ?>

  </ul>

  <div class="btn-wrapper">
    <button type="button" class="btn btn-large orange" ng-click="confirmAddress()">确定</button>
  </div>
</div>

</div>
