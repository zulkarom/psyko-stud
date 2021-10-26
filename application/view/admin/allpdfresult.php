<?php
class MYPDF extends TCPDF {

    //Page header
    public function Header() {
		//$this->myX = $this->getX();
		//$this->myY = $this->getY();
		//$savedX = $this->x;
		//savedY = $this->y;

        $this->SetFont('times', '', 10);
		 $html = 'hai';
		//$this->writeHTMLCell($w = 0, $h = 0, $x = '', $y = '', $html, $border = 0, $ln = 1, $fill = 0, $reseth = true, $align = 'top', $autopadding = true);
	 
		//$this->setX($this->myX);
		//$this->setY($this->myY);
		$this->setY(10);
		//$this->SetY($savedY);
		//$this->SetX($savedX);
        // Title
		$this->Cell(0, 10, 'UJIAN PSIKOMETRIK / PSYCHOMETRIC TEST', 0, 1, 'R', 0, '', 0, false, 'M', 'M');

	    $this->SetTopMargin($this->GetY() + 10);
    }

    // Page footer
    public function Footer() {
        // Position at 15 mm from bottom
        $this->SetY(-30);
		 $html = '';
		$this->writeHTMLCell($w = 0, $h = 0, $x = '', $y = '', $html, $border = 0, $ln = 1, $fill = 0, $reseth = true, $align = 'top', $autopadding = true);
        // Set font
        $this->SetFont('helvetica', 'I', 8);
        // Page number
        $this->Cell(0, 10, 'Page '.$this->getAliasNumPage().'/'.$this->getAliasNbPages(), 0, false, 'C', 0, '', 0, false, 'T', 'M');
    }
}

$pdf = new MYPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

// set document information
$pdf->SetCreator(PDF_CREATOR);
$pdf->SetAuthor('UMK FKP');
$pdf->SetTitle('All-Result');
$pdf->SetSubject('All-Result');
$pdf->SetKeywords('');

// set default header data
$pdf->SetHeaderData(PDF_HEADER_LOGO, PDF_HEADER_LOGO_WIDTH, PDF_HEADER_TITLE, PDF_HEADER_STRING);
//$pdf->writeHTML("<strong>hai</strong>", true, 0, true, true);
// set header and footer fonts
$pdf->setHeaderFont(Array(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN));
$pdf->setFooterFont(Array(PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA));

// set default monospaced font
$pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);

// set margins
$pdf->SetMargins(PDF_MARGIN_LEFT, 10, PDF_MARGIN_RIGHT);
//$pdf->SetMargins(0, 0, 0);
$pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
//$pdf->SetHeaderMargin(0);

 //$pdf->SetHeaderMargin(0, 0, 0);
$pdf->SetFooterMargin(PDF_MARGIN_FOOTER);

// set auto page breaks
$pdf->SetAutoPageBreak(TRUE, 30); //margin bottom

// set image scale factor
$pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);

// set some language-dependent strings (optional)
if (@file_exists(dirname(__FILE__).'/lang/eng.php')) {
    require_once(dirname(__FILE__).'/lang/eng.php');
    $pdf->setLanguageArray($l);
}

// ---------------------------------------------------------



// add a page
$pdf->AddPage("P");

for($i=1;$i<=9;$i++){
	
}
$w2=32;
$w3= 10.5;
$w4= 8;
$pdf->Cell(0, 10, 'KEPUTUSAN UJIAN PSIKOMETRIK', 0, false, 'C', 0, '', 0, false, 'M', 'M');


$wid = [10, 8, 9, 11, 8.5, 12];

$pdf->SetFont('helvetica', '', 8);
$html ='<br /><br /><br /><table border="1" cellpadding="5" width="620">
	<thead>
	<tr >
		<th width="5%"><strong>#</strong></th>
		<th width="'.$w2.'%"><strong>NAMA</strong><br/><i>(NRIC)</i></th>
		';
			
foreach($this->gcat as $i => $grow){
    $text = ucfirst(strtolower($grow->gcat_text));

    $html .= '<th width="'.$wid[$i].'%"><strong>'. $text .'</strong></th>';
	}

$html .= '<th width="'.$w4.'%"><strong>TOTAL</strong></th>
</tr>
</thead>';

$i=1;
$x=1;
foreach ($this->users as $user) {
	$html .= '<tr nobr="true">
		<td width="5%">'. $x.'. </td>
		<td width="'.$w2.'%">'. $user->can_name .'<br /><i>('. $user->user_name .')</i></td>
		';

		$set_sort = TestModel::getGradeCat();
			foreach($set_sort as $i => $sr){
				$id = "c".$sr->gcat_id;
				$html .= '<td width="'.$wid[$i].'%">'.$user->$id.'</td>';
			}
		$html .= '<td width="'.$w4.'%">'. $user->total .'</td></tr>';
$i++;
$x++;
}
$html .= '</table>'; 



$tbl = <<<EOD
$html
EOD;
$pdf->writeHTML($tbl, true, false, false, false, '');


$pdf->Output('AllResult.pdf', 'I');
?>