<?php //echo $this->id;?>
<div style="text-align:center"><strong>Name: </strong> <?php echo $this->user->can_name;?> &nbsp;&nbsp;<strong>No. Kad Pengenalan: </strong> <?php echo $this->user->user_name;?></div><br />
<table class="table">
<thead>
<tr>
<?php
$rowstring="";
foreach($this->gcat as $grow){
	echo "<th>".$grow->gcat_text ."</th>";
	$result_cat = TestModel::getAnswersByCat($this->id,$grow->gcat_id);
	$stringdata ="<table class='table'>
	<tr><td><strong>Q</strong></td>
	<td><strong>A</strong></td>
	</tr>";
	$jum = 0;
	foreach($result_cat as $rowcat){
		$stringdata .="<tr>";
		$stringdata .="<td><strong>".$rowcat->quest ."</strong></td>";
		if($rowcat->answer == 1){
			$ans ="<span class='glyphicon glyphicon-ok'></span>";
			$jum +=1;
		}else if($rowcat->answer == 0){
			$ans ="<span class='glyphicon glyphicon-remove'></span>";
		}else{
			$ans ="NA";
		}
		$stringdata .="<td>".$ans ."</td>";
		$stringdata .="</tr>";
	}
	$stringdata .="<tr><td><strong>Total</strong></td><td>".$jum."</td></tr></table>";
	$rowstring .= "<td>".$stringdata."</td>";
}
?>

</tr>
<thead>
<tr>

<?php echo $rowstring; ?>

</tr>
<tbody>
</tbody>
</table>

<h4>Business Idea</h4>

<?php echo nl2br($this->essay)?>