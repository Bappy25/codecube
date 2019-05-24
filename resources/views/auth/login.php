<?php inherits('app'); ?>

<?php startblock('title') ?>

<?php echo 'Log In || '.title(); ?>

<?php endblock() ?>

<?php startblock('content') ?>

<div class="card">
  <div class="card-header">
    Sign In
  </div>
  <div class="card-body">
    <?php 
    if(!empty($_SESSION['timeout'])){
      ?>
      <div class="alert alert-danger"><?php echo $_SESSION['timeout']; ?></div>
      <h5 class="card-title">Spam Protection</h5>
      <div class="row">
        <div class="col-7 pr-2">
          <span class="img"><?php echo '<img src="' . $_SESSION['captcha']['image_src'] . '" alt="CAPTCHA code">'; ?></span>
        </div>
        <div class="col-5 pr-2">
          <a href="<?php echo $_SERVER['REQUEST_URI']; ?>" class="btn btn-block bg-red waves-effect"><i class="material-icons">cached</i> <span class="icon-name">ReCaptcha</span></a>
        </div>
      </div>
      <form method="POST" action="<?php echo route('captcha'); ?>">            
        <input type="hidden" name="_token" value="<?php echo csrf_token(); ?>">
        <div class="form-group">
          <label for="email">Captcha:</label>
          <input type="text" class="form-control" name="check" placeholder="Enter captcha text" required autofocus>
        </div>
        <button type="submit" class="btn btn-primary">Submit</button>
      </form>
      <?php 
    }
    else{
      ?>
      <form method="POST" action="<?php echo route('signin'); ?>">            
        <input type="hidden" name="_token" value="<?php echo csrf_token(); ?>">
        <?php 
        $alerts = App\Base\Request::getFlash();
        if(!empty((array) $alerts)){
          foreach($alerts as $key=>$value){
            ?>
            <div class="alert alert-<?php echo $key; ?>"> 
              <?php echo $value; ?>
            </div>
            <?php                            
          }
        } 
        else { 
          ?> 
          <h5 class="card-title">Please Input Your Login Credentials</h5>
        <?php } ?>
        <div class="form-group">
          <label for="username">Username/Email address:</label>
          <input type="text" id="username" class="form-control" name="username">
        </div>
        <div class="form-group">
          <label for="password">Password:</label>
          <input type="password" id="password" class="form-control" name="password">
        </div>
        <button type="submit" class="btn btn-primary">Submit</button>
      </form>
      <?php    
    }
    ?>
  </div>
</div>

<?php endblock() ?>