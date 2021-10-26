<div class="container">

    <!-- echo out the system feedback (error and success messages) -->
    <?php $this->renderFeedbackMessages(); ?>

    <div>
        <div style="margin: 0 auto; width:90%">

            <!-- login box on left side -->
            <div>
			
<img src="<?php echo Config::get('URL')?>/images/Psychometric-Tests.png" width="80%" />

<div class="row">
				
				<div class="col-md-12">
				
				
				
				

				<div class="row">

				<div class="col-md-6">
				
				<?php 
				
				if($this->open == 1){
				?>
				<h4> MULA MENJAWAB / <i>START ANSWERING</i>  </h4><br />
                <form action="<?php echo Config::get('URL'); ?>login/login" method="post">

				
				<div class="form-group">
				<label for="user_name">NRIC/PASSPORT NO.:</label>
				
				<input type="text" name="user_name"  class="form-control input-lg" required />
				</div>
      
					<input type="hidden" name="csrf_token" value="<?= Csrf::makeToken(); ?>" />
                    <input type="submit" class="btn btn-primary" value="LOG IN"/>
                </form>
				<br /><br />
				<?php } else { 
				
				echo '<h3>TUTUP / CLOSED</h3>';
				}
				?>
				</div>
				
				
				<div class="col-md-6">
				
			
				
				
					<?php 
				
				if($this->open == 1){
				?>
					<h4> DAFTAR / <i>REGISTER</i>  </h4><br />
                <form action="<?php echo Config::get('URL'); ?>login/register" method="post">
                
                <div class="form-group">
				<label for="user_name">NAMA / NAME.:</label>
				
				<input type="text" name="fullname"  class="form-control input-lg" required />
				</div>

				
				<div class="form-group">
				<label for="user_name">NRIC/PASSPORT NO.:</label>
				
				<input type="text" name="username"  class="form-control input-lg" required />
				</div>
				
				<div class="form-group">
				<label for="user_name">JABATAN / DEPARTMENT:</label>
				
				<input type="text" name="department"  class="form-control input-lg" required />
				</div>
				
       
					<input type="hidden" name="csrf_token" value="<?= Csrf::makeToken(); ?>" />
                    <input type="submit" class="btn btn-primary" value="REGISTER"/>
                </form>
				
				<?php } else { 
				
				echo '<h3>TUTUP / CLOSED</h3>';
				}
				?>
				
				
				
				
				
				</div>
				</div>
		
				</div>
				<div class="col-md-1"></div>
			</div>

			
			
				
                

            </div>



        </div>
    </div>
</div>
