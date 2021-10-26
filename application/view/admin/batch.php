<div>
<br/>

    <div>

        <!-- echo out the system feedback (error and success messages) -->
        <?php $this->renderFeedbackMessages(); ?>

		
        <div>
		<form action="<?php echo Config::get('URL')?>admin/batch/savebatch" method="post">
		
		<div class="row">
			<div class="col-md-2"><b>Open Online Interview</b></div>
			<div class="col-md-1">
			
			<select class="form-control" name="open">
				<option value="0" <?=$this->open == 0 ? 'selected' : ''?>>No</option>
				<option value="1" <?=$this->open == 1 ? 'selected' : ''?>>Yes</option>
			</select>
			
			</div>
		</div>
		
		<br />
		
		
		<div class="row">
		<div class="col-md-4">
		<table class="table">
                <thead>
                <tr>
					<th>No. </th>
					<th>Batch Name</th>
					<th>Showing</th>
                </tr>
                </thead>
                <?php 
				$i=1;
				foreach ($this->batch as $user) { 
				if($this->show == $user->bat_id){
					$c="checked";
				}else{
					$c="";
				}
				?>
                    <tr>
					
						<td><?= $i; ?>. </td>
                        <td ><?php echo $user->bat_text; ?></td>
						<td ><input type="radio" name="shwbatch" value="<?php echo $user->bat_id;?>" <?php echo $c;?> /></td>
						
                        
						
                    </tr>
                <?php 
				$i++;
				} 
				?>
            </table>
			
		</div>
		</div>
		
		<button class="btn btn-primary" type="submit"><span class="glyphicon glyphicon-floppy-disk"></span> SAVE SETTING</button>
            
		</form>
        </div>
    </div>
</div>
