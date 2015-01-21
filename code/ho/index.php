<?php require_once 'common/header.php'; ?>
<div class="container">
  <div class="col-md-4 col-md-offset-4" id="login-box">
    <div class="padded" style="text-align: center; margin-top:10px;">
      <img src="images/logo.png" alt="Simple POS" height="117" />
      <div class="panel panel-primary">
        <div class="panel-heading">Login</div>
        <div class="panel-body" style="padding-bottom: 0;">
          <div class="alert alert-danger" id="error_message"></div>
          <form id="loginform" action="" method="post" accept-charset="utf-8" class="separate-sections form-horizontal" autocomplete="off">
            <div class="input-group">
              <span class="input-group-addon"> <i class="glyphicon glyphicon-user"></i></span>
              <input type="text" name="username" value="" id="username" class="form-control" placeholder="Employee Code" autocomplete="off"/>
              <input type="hidden" name="action" value="get_store_list">
            </div>
            <div class="input-group"><span class="input-group-addon"><i class="glyphicon glyphicon-lock"></i> </span>
              <input type="password" name="password" value="" id="password" class="form-control" placeholder="Password" autocomplete="off"/>
            </div>
            <div class="row">
              <div class="col-md-12">
                <button type="submit" class="btn btn-success btn-block btn-lg">Login <i class="glyphicon glyphicon-log-in"></i></button>
              </div>
            </div>
          </form>
        </div>
      </div>
      <?php require_once 'common/footer.php';?>
    </div>
  </div>
</div>