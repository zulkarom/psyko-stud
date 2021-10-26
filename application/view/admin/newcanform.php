<div>
    

    <div style="width:30%">
<br/>
		
<form role="form" method="post" action="<?php echo Config::get('URL');?>admin/newcan">
<div class="form-group">
    <label for="username">USERNAME:</label><br /><i>contoh i.c. number</i>
    <input type="text" class="form-control" id="username" name="username">
  </div>
  <div class="form-group">
    <label for="fullname">FULL NAME:</label>
    <input type="text" class="form-control" id="fullname" name="fullname">
  </div>
  

  <div class="form-group">
    <label for="zone">ZONE:</label>
    <select class="form-control" id="zone" name="zone" >
	<?php
	foreach($this->zone as $row){
		echo "<option value='".$row->zone_id ."'>".$row->zone_text ."</option>";
	}
	
	?>
	</select>
  </div>
  <div class="form-group">
    <label for="batch">BATCH:</label>
    <select class="form-control" id="batch" name="batch">
	<?php
	foreach($this->batch as $row){
		echo "<option value='".$row->bat_id ."'>".$row->bat_text ."</option>";
	}
	
	?>
	</select>
  </div>

  
  
  <button type="submit" class="btn btn-primary">CREATE NEW CANDIATE</button>
</form>
        
    </div>
</div>
