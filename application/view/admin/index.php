<div>
<br/>

    <div>

        <!-- echo out the system feedback (error and success messages) -->
        <?php $this->renderFeedbackMessages(); ?>

		
        <div>
		<div class="row">
		<div class="col-md-2">
		<div class="form-group">
		<button class="btn btn-success" data-toggle="modal" data-target="#myModal4"><span class="glyphicon glyphicon-plus"></span> NEW CANDIDATE</button>
		</div>
		</div>
		<div class="col-md-3">
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
		<div class="col-md-3"><a href="<?php echo Config::get('URL')?>admin/downloadoffline" target="_blank">Download Offline Excel Question</a></div>
		</div>
		
		

            <table class="table">
                <thead>
                <tr>
					<th>No. </th>
					<th>Full Name</th>
                    <th>Username (NRIC)</th>
					<th>Status</th>
					<th>Submission Time</th>

                    <th>Batch</th>
					
					<th>Excel Psychometric</th>
					<th></th>
                </tr>
                </thead>
                <?php 
				$i=1;
				foreach ($this->users as $user) { ?>
                    <tr>
					
						<td><?= $i; ?>. </td>
						
                        <td ><a href="#" onclick="return false" idx="<?php echo $user->user_id; ?>" data-toggle='modal' data-target='#myModal6'><span id="tfullname-<?php echo $user->user_id;?>"><?php echo $user->can_name; ?></span> <span class="glyphicon glyphicon-pencil"></span></a></td>
						
                        <td id="tusername-<?php echo $user->user_id;?>"><?= $user->user_name; ?></td>
						
						<td><span id="tstatus-<?php echo $user->user_id;?>"><?= $user->status; ?></span></td>
						
						<td><?php 
						if($user->finished_at > 0){
							echo date('d M Y h:m:s', $user->finished_at);
						}else{
							echo '-';
						}
						
						
						?></td>
                        
                    
						
						<td><?= $user->can_batch; ?><span id="tbatch-<?php echo $user->user_id;?>" class="hidden"><?= $user->can_batch_id; ?></span></td>
						
						
						
						<td><a href="#" data-toggle="modal" idx="<?php echo $user->user_id;?>" data-target="#myModal8"><button class="btn btn-default btn-sm">Upload Answers</button></a></td>
						
						<td><a href="#" data-toggle="modal" idx="<?php echo $user->user_id;?>" data-target="#myModal5"><span class="glyphicon glyphicon-trash"></span></a></td>
						
                    </tr>
                <?php 
				$i++;
				} 
				?>
            </table>
		
        </div>
    </div>
</div>

<div class="modal fade" id="myModal5" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" style="display: none;">
  <div class="modal-dialog">
    <div class="modal-content">
	<form method="post" action="<?php echo Config::get('URL')?>admin/index/<?php echo $this->sbatch;?>/trashcan">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button>
        <h4 class="modal-title" id="myModalLabel">
		Trashing Candidate : <span id="cand_id"></span></h4>
      </div>
      <div class="modal-body" id="con-modal5">Are you sure to trash this candidate?</div>
	  <input type="hidden" name="trash_can" id="trash_can" value="">
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
		<button type="submit" class="btn btn-danger">Trash <span class="glyphicon glyphicon-trash"></span></button>
      </div>
	  </form>
    </div>
  </div>
</div>


<div class="modal fade" id="myModal6" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
	<form method="post" id="formedituser" action="">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title" id="myModalLabel">Edit Candidate</h4>
      </div>
      <div class="modal-body" id="con-modal5">
	  

<div class="form-group">
    <label for="username">USERNAME:</label><br /><i>Letak No. Kad Pengenalan tanpa (-) e.g. 900213035599</i>
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

  

      </div>
      <div class="modal-footer">
	  <button class="btn btn-primary" type="submit"><span class="glyphicon glyphicon-floppy-disk"></span> SAVE</button>
        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
      </div>
	  </form>
    </div>
  </div>
</div>

<div class="modal fade" id="myModal4" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
	<form method="post" action="<?php echo Config::get('URL')?>admin/index/<?php echo $this->sbatch?>/newcan">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title" id="myModalLabel">NEW Candidate</h4>
      </div>
      <div class="modal-body" id="con-modal5">
	  

<div class="form-group">
    <label for="username">USERNAME:</label><br /><i>Letak No. Kad Pengenalan tanpa (-) e.g. 900213035599</i>
    <input type="text" class="form-control" id="username" name="username" required>
  </div>
  <div class="form-group">
    <label for="fullname">FULL NAME:</label>
    <input type="text" class="form-control" id="fullname" name="fullname" required>
  </div>
  

  <div class="form-group">
    <label for="fullname">DEPARTMENT:</label>
    <input type="text" class="form-control" id="department" name="department" required>
  </div>
  
  


  

      </div>
      <div class="modal-footer">
	  <button class="btn btn-primary" type="submit"><span class="glyphicon glyphicon-floppy-disk"></span> SAVE</button>
        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
      </div>
	  </form>
    </div>
  </div>
</div>


<div class="modal fade" id="myModal8" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
	<form method="post" action="<?php echo Config::get('URL')?>admin/newcan">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title" id="myModalLabel">UPLOAD ANSWERS</h4>
      </div>
      <div class="modal-body" id="con-modal5">
	  <span id="upuserid" class="hidden"></span>
	  <div align="center"><strong><span id="upfullname"></span></strong></div>
	  <div align="center"><i><span id="upusername"></span></i></div>
	  <br /><br />

<div class="fileUpload" id="button_upload">
    <div class="sfile btn btn-success"><span>Select Excel File</span><input class="upload" type="file" name="xlfile" id="xlf"></div>
	</div>
	<div align="center" class="hidden" id="conlinkresult">
	<a href="" id="linkresult" class="btn btn-warning">Lihat Keputusan</a></div>
<br />
	<div align="center" id="uploadmsg"></div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
      </div>
	  </form>
    </div>
  </div>
</div>

<script src="<?php echo Config::get('URL')?>excel/shim.js"></script>
<script src="<?php echo Config::get('URL')?>excel/jszip.js"></script>
<script src="<?php echo Config::get('URL')?>excel/xlsx.js"></script>



<script>

$(document).ready(function(){


$('#myModal5').on('show.bs.modal', function (event) {
  var button = $(event.relatedTarget) ;
  var paper = button.attr('idx') ;
  var modal = $(this);
  modal.find('#trash_can').val(paper);
  modal.find('#cand_id').html(paper);
});

$('#myModal6').on('show.bs.modal', function (event) {
  var button = $(event.relatedTarget) ;
  var user = button.attr('idx');
  var modal = $(this);
  var username = $("#tusername-"+user).text();
  var fullname = $("#tfullname-"+user).text();
  var batch = $("#tbatch-"+user).text();
  var zone = $("#tzone-"+user).text();
  modal.find("#formedituser").attr("action", "<?php echo Config::get('URL')?>admin/index/"+batch+"/edituser/"+user);
  modal.find('#username').val(username);
  modal.find('#fullname').val(fullname);
  modal.find('#batch').val(batch);
  modal.find('#zone').val(zone);
});

$('#myModal8').on('show.bs.modal', function (event) {
  var button = $(event.relatedTarget) ;
  var user = button.attr('idx');
  var modal = $(this);
  var username = $("#tusername-"+user).text();
  var fullname = $("#tfullname-"+user).text();
  modal.find('#upuserid').text(user);
  modal.find('#upusername').text(username);
  modal.find('#upfullname').text(fullname);
  modal.find("#button_upload").removeClass("hidden");
  modal.find("#linkresult").attr("href","<?php echo Config::get('URL')?>admin/allresult/0/0/9/"+user);
  //
  $("#uploadmsg").html("");
});



	var X = XLSX;

	function fixdata(data) {
		var o = "", l = 0, w = 10240;
		for(; l<data.byteLength/w; ++l) o+=String.fromCharCode.apply(null,new Uint8Array(data.slice(l*w,l*w+w)));
		o+=String.fromCharCode.apply(null, new Uint8Array(data.slice(l*w)));
		return o;
	}
	
	
	function showWaiting(f){
		var tt ="";
		var ext = f.split('.').pop();
		if(ext == "xls" || ext == "xlsx"){
			tt="Sila Tunggu...";
		}else{
			tt="Jenis file salah ("+f+")";
		}
		$("#uploadmsg").text(tt);
	}
	

	function to_csv(workbook) {
		var result = [];
		var tetapan="";
		var baik = 0;
		workbook.SheetNames.forEach(function(sheetName) {
			var csv = X.utils.sheet_to_csv(workbook.Sheets[sheetName]);
			if(csv.length > 0){
				if(sheetName=="answers"){
					var baik = 1;
					var user = $('#upuserid').text();
					saveAllAnswers(user, csv);
					//alert(user);
					//alert(csv);	
						//alert(baik);
				}
			}
		});
		
		if(baik ==0){
			//$("#uploadmsg").html("Maaf, jawapan tidak berjaya direkod!");
		}
		
	}
	
	function saveAllAnswers(user, csv){
		
	$.post("<?php echo Config::get('URL')?>admin/uploadanswers/"+user, 
		{data: csv}, 
		function(result){
		if(result == 1){
			$("#uploadmsg").text("Jawapan telah berjaya direkod");
			$("#button_upload").addClass("hidden");
			$("#conlinkresult").removeClass("hidden");
			$("#tstatus-"+user).text("Submitted");
		}else{
			$("#uploadmsg").html("Maaf, jawapan tidak berjaya direkod!");
		}
        
    });
	
	}


	var xlf = document.getElementById('xlf');
	
	function handleFile(e) {
	
		var files = e.target.files;
		var f = files[0];
		var fname = f.name;
		showWaiting(fname);
	
		{
			var reader = new FileReader();
			var name = f.name;
			reader.onload = function(e) {
				var data = e.target.result;
				var wb;
				var arr = fixdata(data);
				wb = X.read(btoa(arr), {type: 'base64'});
				to_csv(wb);

			};
			reader.readAsArrayBuffer(f);
		}
		
	}
	
	if(xlf.addEventListener){
		xlf.addEventListener('change', handleFile, false);
	}

	$("#batchsel").change(function(){
		var batch = $("#batchsel").val();
		location.href='<?php echo Config::get('URL');?>admin/index/'+batch;
	});
	
});
</script>