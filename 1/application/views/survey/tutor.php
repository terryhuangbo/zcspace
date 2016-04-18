<div class="jumbotron hide">
    <h2 class="text-left">关于我们的项目</h2>
    <p class="text-left">
        择业帮项目，基于移动互联网，运用专业职业性格评测方法和各行业从业者资源优势，提供对大学生和年轻职场人的择业建议、规划等职业咨询服务。
    </p>
    
    <h2 class="text-left">调研说明</h2>
    <p class="text-left">
        本次调研目的为了解各行各业的职业情况和职业发展路径，同时寻找潜在的合作伙伴。保证在未征得调研对象允许之前，不会泄露调研对象姓名、联系方式等隐私给第三方。
    </p>
</div>

<div class="row marketing">
    <div class="col-sm-offset-2 col-sm-8">
    <form class="form-horizontal tutor" role="form" action="addtutor" method="post">
      <div class="form-group">
        <label class="col-sm-2 control-label">姓名</label>
        <div class="col-sm-10">
          <input type="text" class="form-control" name="name">
        </div>
      </div>
      
      <div class="form-group">
        <label for="" class="col-sm-2 control-label">城市</label>
        <div class="col-sm-10 ">
          <input type="text" class="form-control" name="city">
        </div>
      </div>
      
      <div class="form-group">
        <label for="" class="col-sm-2  control-label">QQ</label>
        <div class="col-sm-10 ">
          <input type="text" class="form-control" name="qq">
        </div>
      </div>
      <div class="form-group">
        <label for="" class="col-sm-2 control-label">手机</label>
        <div class="col-sm-10 ">
          <input type="text" class="form-control" name="mobile">
        </div>
      </div>
      <div class="form-group">
        <label for="" class="col-sm-2 control-label">行业</label>
        <div class="col-sm-10 ">
          <select class="form-control" name="industry">
              <option></option>			
<option>IT/通信/电子/互联网</option>	
<option>金融业	</option>
<option>房地产/建筑业	</option>
<option>商业服务	</option>
<option>造纸及纸制品/印刷业	</option>
<option>贸易/批发/零售业	</option>
<option>文教体育/工艺美术	</option>
<option>加工制造/仪表设备	</option>
<option>交通运输仓储	</option>
<option>制药医疗/生物/卫生保健</option>	
<option>教育	</option>
<option>酒店/餐饮/旅游	</option>
<option>文化/体育/娱乐业	</option>
<option>能源/电气/采掘/地质/石油加工</option>	
<option>政府/非盈利机构	</option>
    <option>农林牧渔/其他	</option>
          </select>
        </div>
      </div>
      
      <div class="form-group">
        <label for="" class="col-sm-2 control-label">职业</label>
        <div class="col-sm-10 ">
            
          <input type="text" class="form-control" name="job">
        </div>
      </div>
      
      <div class="form-group">
        <label for="" class="col-sm-2 control-label">工作年限</label>
        <div class="col-sm-10 ">
          <input type="text" class="form-control" name="yearsOfWorking">
        </div>
      </div>
          
      <div class="form-group">
        <label for="" class="col-sm-2 control-label">愿意将你的工作经验分享</label>
        <div class="col-sm-10 ">
          <div class="radio">
          <label>
            <input type="radio" name="willingToShare" value="1" checked>
            是
          </label>
            </div>
            <div class="radio">
          <label>
            <input type="radio" name="willingToShare" value="0">
            否
          </label>
        </div>
        </div>
      <div class="form-group hide">
        <label for="" class="control-label">请输入下列验证码</label>
        <div class="">
          <?php echo $math_captcha_question;?>
        </div>
        <div class="">
            <input type="text" class="form-control" name="math_captcha">
        </div>
      </div>
      </div>
      <div class="form-group">
        <div class="col-sm-offset-2 col-sm-10 ">
            <ul id="messageBox" class="btn-danger">
                <?php if(isset($validation_errors)){?>
                <?php echo $validation_errors; 
                }?>
            </ul>
          <button type="submit" class="btn btn-primary">开始调研问卷</button>
        </div>
      </div>
        
	<input  type="hidden" name="robots" value="" />
    </form>
    </div>
</div>