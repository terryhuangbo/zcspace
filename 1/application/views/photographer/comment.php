<?php 
foreach ($comments as $comment) {
?>
<ul class="comment-list">
    <li>
      <div class="items">
        <div class="item-core">
          <div class="avatar">
            <a href="#">
              <img src="<?php echo $comment->pimgUrl; ?>" style="width: 70px;height: 70px;"/>
            </a>
          </div>

          <div class="comment-info">
            <h4 class="nickname"><?php echo $comment->name; ?></h4>

            <p class="star">
              <?php 
              for ($i=0; $i < $comment->takeServiceQualityGrade; $i++) { 
              ?>
              <a href="#"><img src="http://1.sheyingqiezi.sinaapp.com/web/resource/images/xing1.jpg"/></a>
              <?php
              }
              ?>

              <?php 
              for ($i=$comment->takeServiceQualityGrade; $i < 5; $i++) { 
              ?>
              <a href="#"><img src="http://1.sheyingqiezi.sinaapp.com/web/resource/images/xing2.jpg"/></a>
              <?php
              }
              ?>
            </p>

            <p><?php echo $comment->notes; ?></p>
          </div>
        </div>
        <p class="date"><?php echo $comment->addDate; ?></p>
      </div>
    </li>
  </ul>
  <p class="divider"></p>
<?php
}
?>