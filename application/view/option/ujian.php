
<div class="container">
	<div class="row">
	<div class="col-md-8">
	<div class="ptitle">
	
	
	Fakulti Keusahawanan dan Perniagaan 
	
	</div>
	<br />
	<strong>Nama:</strong> <?php echo $this->user->can_name ;?><br />
	<strong>No. Kad Pengenalan:</strong>  <?php echo $this->user->user_name ;?>
	<br /><strong>No. Matrik:</strong>  <?php echo $this->user->matric_no ;?>
	<br /><strong>Progam:</strong>  <?php echo $this->user->program ;?>
	
	</div>

	</div>
	

    <div class="box">
	
	<div><strong>SILA PILIH UJIAN ANDA:</strong></div>
<br />
		<div class="form-group">
		<a href="<?=Config::get('URL')?>test/ujian" class="btn btn-primary">UJIAN PSIKOMETRIK</a> 
		</div>
		


<a href='<?php echo Config::get('URL'); ?>login/logout2'>LOGOUT</a>
		
		



    </div>
	
	
	

</div>
