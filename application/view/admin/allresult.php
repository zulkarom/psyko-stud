<div>
<br/>
    <div>
        <?php $this->renderFeedbackMessages(); ?>

        <div>
		
		<div class="row">
		
		<div class="col-md-2">
			<div class="form-group">
			<select id="batchsel" name="batchsel" class="form-control">
				<?php
				foreach($this->batch as $bat){
					if($this->sbatch ==0){
						if($this->dbatch == $bat->bat_id)
						{$s="selected";}else{$s="";}
					}else{
						if($this->sbatch == $bat->bat_id)
						{$s="selected";}else{$s="";}
					}
					echo "<option value='".$bat->bat_id ."' ".$s.">";
					echo $bat->bat_text;
					echo "</option>";
				}
				?>
			</select>
			</div>
		</div>

		
		<div class="col-md-2">
			<div class="form-group">
			<select class="form-control" id="statussel">
			<option value="9">-=Select Status=-</option>
			<?php
			$status_arr = array('3'=>"Submitted", '1'=>"Started", '0'=>"Not Started");
			foreach($status_arr as $key=>$rs){
				if($this->sstatus == $key)
						{$s="selected";}else{$s="";}
				echo '<option value="'.$key.'" '.$s.'>'.$rs.'</option>';
			}
			
			?>

			</select>
			</div>
		
		</div>
		
		<div class="col-md-2">
			<div class="form-group">
			<select class="form-control" id="sortingsel">
			<option value="9">-=Select Sorting=-</option>
			<?php
			$status_arr = array('1'=>"Psychometric Score", '2'=>"Submission Time");
			foreach($status_arr as $key=>$rs){
				if($this->ssorting == $key)
						{$s="selected";}else{$s="";}
				echo '<option value="'.$key.'" '.$s.'>'.$rs.'</option>';
			}
			
			?>

			</select>
			</div>
		
		</div>
		
		<div class="col-md-3"><a href="<?php echo Config::get('URL')?>admin/allpdfresult/<?php echo $this->sbatch ."/".$this->szone ."/".$this->sstatus;?>" target="_blank" class="btn btn-danger"> DOWNLOAD RESULT</a></div>
		
		</div>
		 
            <table class="table">
                <thead>
                <tr>
                    <th>No. </th>
                    <th>Full Name<i>(NRIC)</i></th>
                    <th>Department / Batch</th>

					<th>Status</th>
					<th>Submission Time</th>
					
					<?php
					foreach($this->gcat as $grow){
							echo "<th>".$grow->gcat_text ."</th>";
						}
					?>

					<th>Total</th>
					<th></th>

                </tr>
                </thead>
                <?php 
				$i=1;
				foreach ($this->users as $user) { ?>
                    <tr>
                        <td><?php echo $i; ?>. </td>
                        
                        <td><?php echo $user->can_name; ?><br/><i>(<?php echo $user->user_name; ?>)</i></td>
                        <td><?php echo $user->department; ?><br /><?php echo $user->can_batch; ?></td>
						<td><?php echo $user->status; ?></td>
						
						<td><?php 
						if($user->finished_at > 0){
							echo date('d M Y h:m:s', $user->finished_at);
						}else{
							echo '-';
						}
						
						
						?></td>
						
						
						<?php 
						$set_sort = TestModel::getGradeCat();
							foreach($set_sort as $sr){
								$id = "c".$sr->gcat_id;
								echo "<td>".$user->$id."</td>";
							}
						?>
						

						
						<td><?php echo $user->total; ?></td>
						<td>
						<button type="button" class="btn btn-warning btn-sm"  data-toggle='modal' data-target='#myModal' id="<?php echo $user->user_id?>">View</button>
						</td>
						<td>
						<a href="<?php echo Config::get('URL')?>admin/resultpdf/<?php echo $user->user_id?>" target="_blank" class="btn btn-danger btn-sm">PDF</button>
						</td>
						
                    </tr>
                <?php 
				$i++;
				} ?>
            </table>
        </div>
    </div>
</div>



<div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
	 <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title" id="myModalLabel">Result Detail</h4>
      </div>
	  <div class="modal-body" id="result-detail"></div>
	  
	<div class="modal-footer">
	  <a href="" id="dwnpdf" target="_blank" class="btn btn-danger">Download PDF</a>
        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>

      </div>
    </div>
  </div>
</div>


<script>

$(document).ready(function(){
	$('#myModal').on('show.bs.modal', function (event) {
	  var button = $(event.relatedTarget) ;
	  var can_id = button.attr('id'); 
	  
	  
	  //var title = $('#pap-'+paper+'').html() ;
	  var modal = $(this);
	  modal.find('#result-detail').load('<?php echo Config::get('URL');?>admin/individual/'+can_id);
	  modal.find("#dwnpdf").attr("href","<?php echo Config::get('URL')?>admin/resultpdf/"+can_id);
	  window_size = $(window).height();
	  modal.find('.modal-body').css("height", window_size -200 + "px");
	modal.find('.modal-body').css("overflow", "auto");
	});
	
	$("#batchsel, #zonesel, #statussel, #sortingsel").change(function(){
		var batch = $("#batchsel").val();
		var zone = $("#zonesel").val();
		var status = $("#statussel").val();
		var sorting = $("#sortingsel").val();
		location.href='<?php echo Config::get('URL');?>admin/allresult/'+batch+'/'+zone+'/'+status+'/0/'+sorting;
	});
	
});


$('#myModal').on('hidden.bs.modal', '.modal', function () {
  $(this).removeData('bs.modal');
});

</script>