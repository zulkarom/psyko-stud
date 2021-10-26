<div>
    <?php $this->renderFeedbackMessages(); ?>

    <div style="width:30%">
<br/>
		
<form role="form" method="post" action="<?php echo Config::get('URL');?>admin/renewpass">
<div class="form-group">
    <label for="username">Current Password:</label>
    <input type="password" name="user_password_current" class="form-control" required />
  </div>

  <div class="form-group">
    <label for="username">New Password:</label>
    <input type="password" class="form-control" name="user_password_new" required />
  </div>
  <div class="form-group">
    <label for="username">Repeat New Password:</label>
    <input type="password" class="form-control" name="user_password_new_repeat" required />
  </div>
  


  
  <button type="submit" class="btn btn-primary">CHANGE PASSWORD</button>
</form>
        
    </div>
</div>
