<div class="container">

    <!-- echo out the system feedback (error and success messages) -->
    <?php $this->renderFeedbackMessages(); ?>

    <div>
        <div>

            <!-- login box on left side -->
            <div style="margin:0 auto;width:50%">
                <h2>Administrator Login</h2>
                <form action="<?php echo Config::get('URL'); ?>login/loginadmin" method="post">
				
				
				<div class="form-group">
				<label for="user_name">USERNAME:</label>
				<input type="text" name="user_name" placeholder="Username" class="form-control" required />
				</div>
				<div class="form-group">
				<label for="user_password">PASSWORD:</label>
				<input type="password" name="user_password" placeholder="Password" class="form-control" required />
				</div>
                    <?php if (!empty($this->redirect)) { ?>
                        <input type="hidden" name="redirect" value="<?php echo $this->encodeHTML($this->redirect); ?>" />
                    <?php } ?>
					<input type="hidden" name="csrf_token" value="<?= Csrf::makeToken(); ?>" />
                    <input type="submit" class="btn btn-primary" value="Log in"/>
                </form>
				<br /><br />
<a href="<?php echo Config::get('URL');?>" style="text-align:center">Candidate Page</a>
            </div>


		
        </div>
    </div>
</div>
