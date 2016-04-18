<div class="container">
    <div class="row ">
        <div class="col-md-3 col-md-offset-1 divider-left divider">
        </div>
        <div class="col-md-4 divider-mid divider">
            <h1>平台原理</h1>
        </div>
        <div class="col-md-3 divider-right divider">
        </div>
    </div>
	<div class="row ">
        <div class="col-md-8 col-md-offset-2">
            
            <div class="img-center">
            	<img src="/resource/image/platform.png" class="img-responsive"/>
            </div>
        </div>
    </div>
    <div class="row ">
        <div class="col-md-3 col-md-offset-1 divider-left divider">
        </div>
        <div class="col-md-4 divider-mid divider">
            <h1 class="">职业咨询师</h1>
        </div>
        <div class="col-md-3 divider-right divider">
        </div>
    </div>
    <div class="row">
        <div class="col-md-11 col-md-offset-1  ">
            <ul class="lead">
                <li class="highlight">如果你是职场人，具有3年以上的职业经验，并乐于分享擅长与人沟通，请你加入我们。</li>
                <li >通过择业帮，帮助他人职业启航，成为职业咨询师，变现你的职业经验</li>
            </ul>
            <div class="text-center">
                <div class="modal fade" id="mdJoinIn" data-keyboard="false" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
                  <div class="modal-dialog">
                    <div class="modal-content">
                      <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
                          <p class="modal-title highlight lead" id="myModalLabel"><span class="mpIcon">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span>请留下你的个人信息和联系方式，我们会尽快联系你！</p>
                      </div>
                      <div class="modal-body">
                          <form class="form-horizontal tutor" role="form" action="addtutor" method="post">
                              <div class="form-group">
                                <label class="col-sm-2 control-label">姓名</label>
                                <div class="col-sm-10">
                                  <input type="text" class="form-control" name="name">
                                </div>
                              </div>
                              
                              <div class="form-group hide">
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
                                    <input class="form-control" name="industry" type="text"/>
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
                                  
                              <div class="form-group hide">
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
                                    <ul id="messageBox" class="btn-danger text-left">
                                        <?php if(isset($validation_errors)){?>
                                        <?php echo $validation_errors; 
                                        }?>
                                    </ul>
                                </div>
                              </div>
                              <div class="form-group">
                                    <input type="button" id="btnTutorJoinIn" class="btn btn-submitApply"/>
                              </div>
                                
                            <input  type="text" name="robots" value="" class="hide" />
                            </form>
                            <h1><p id="txtResult" class="hide"></p></h1>
                      </div>
                     	
                      <div class="modal-footer hide">
                        <button type="button" class="btn btn-default" data-dismiss="modal">关闭</button>
                      </div>
                    </div><!-- /.modal-content -->
                  </div><!-- /.modal-dialog -->
                </div><!-- /.modal -->
            </div>
        </div>
    </div>
    
            <div class="text-center">
            	<input type="button" class="btn btn-applyJoin" id="btnJoinIn" data-toggle="modal" data-target="#mdJoinIn"/>
            </div>
    <div class="row">
        <div class="col-md-3 col-md-offset-1 divider-left divider">
        </div>
        <div class="col-md-4 divider-mid divider">
            <h1>职业评测和推荐系统</h1>
        </div>
        <div class="col-md-3 divider-right divider">
        </div>
    </div>
    <div class="row ">
        <div class="col-md-5 col-md-offset-1">
            <p class="lead">择业帮采用当今世界上应用最广泛的职业性格评测工具——MBTI（Myers-Briggs Type Indicator），全球每年MBTI评测的使用者多达200多万。 
择业帮根据用户的MBTI职业性格评测结果，同时结合各职业从事者的职业性格特点，做出适合用户的职业推荐。</p>
        </div>
        <div class="col-md-5">
            <div class="img-center">
            <img src="/resource/image/mbti.png" class="img-responsive"/>
            </div>
        </div>
    </div>
    <div class="row ">
        <div class="col-md-3 col-md-offset-1 divider-left divider">
        </div>
        <div class="col-md-4 divider-mid divider">
            <h1>职业经验数据系统</h1>
        </div>
        <div class="col-md-3 divider-right divider">
        </div>
    </div>
    <div class="row divider">
        <div class="col-md-5 col-md-offset-1 ">
            <p class="lead">择业帮已整合100多种职业从事者的职业经验数据，囊括技术、设计、销售、品牌、财务、客服、人力、行政、采购等20大类职能岗位，涵盖互联网、IT、
金融业、房地产、教育、医疗等数10大行业。</p>
        </div>
        <div class="col-md-5 ">
            <div class="img-center">
            	<img src="/resource/image/database.png" class="img-responsive"/>
            </div>
        </div>
    </div>