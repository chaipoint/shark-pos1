<?php include 'header.php'; ?>
<div class="wrapper">
    
    <form class="form-signin" name="loginform" id="loginform" method="post" >       
      <h2 class="form-signin-heading">Please login</h2>
      <div class="alert alert-danger" id="error_message">
        <ul>
        </ul>
    </div>
      <input type="text" class="form-control" name="username" id="username" placeholder="Username" required="" autofocus="" /></br>
      <input type="password" class="form-control" name="password" id="password" placeholder="Password" required=""/> </br> 
      <input type="hidden" name="action" value="get_store_list">    
     <!-- <label class="checkbox">
        <input type="checkbox" value="remember-me" id="rememberMe" name="rememberMe"> Remember me
      </label> -->
      <button class="btn btn-lg btn-primary btn-block"  type="submit">Login</button>   
    </form>
  </div>