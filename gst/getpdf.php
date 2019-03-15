<?php
include("../config.php");
$invoiceid = isset($_GET['id']) && $_GET['id']!='' ? mysql_real_escape_string($_GET['id']) : '0';
$type = isset($_GET['type']) && $_GET['type']!='' ? mysql_real_escape_string($_GET['type']) : '1';
$action = isset($_GET['a']) && $_GET['a']=='S' ? 's' : 'd';
$invoice = $db->queryUniqueObject("SELECT * FROM gstinvoices WHERE id='".$invoiceid."'");
if(!$invoice) {
	header("location:../index.php");
	exit();
}
$inv_num = $invoice->invoice_num;
$inv_month = date("F_Y", strtotime($invoice->invoice_month));
$user = $db->queryUniqueObject("SELECT company_name FROM apps_user WHERE uid='".$invoice->uid."' ");
if(!$user) {
	exit();
}
$company_name = str_replace("","_",$user->company_name);
// Include the main TCPDF library (search for installation path).
require_once('../tcpdf/tcpdf_include.php');

// create new PDF document
$pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

// set document information
$pdf->SetCreator(PDF_CREATOR);
$pdf->SetAuthor('All_API_Solutions');
$pdf->SetTitle('Invoice # '.$invoiceid);
$pdf->SetSubject('Invoice');
$pdf->SetKeywords('Invoice');

// remove default header/footer
$pdf->setPrintHeader(false);
$pdf->setPrintFooter(false);

// set default monospaced font
$pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);

// set margins
$pdf->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);

// set auto page breaks
$pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);

// set image scale factor
$pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);

// set some language-dependent strings (optional)
if (@file_exists(dirname(__FILE__).'/lang/eng.php')) {
  require_once(dirname(__FILE__).'/lang/eng.php');
  $pdf->setLanguageArray($l);
}

/*
* Generate Invoice
* @sunil 
*/
$pdf->SetFont('helvetica', '', 12);
// add a page
$pdf->AddPage();
/*
* Get invoice html output
* @sunil
*/
if($type=='1') {
	$inv_name = "P2P";
	$html = file_get_contents(HTTP."/gst/invoice-p2p.php?id=".$invoiceid);
}elseif($type=='2'){
	$inv_name = "P2A";
	$html = file_get_contents(HTTP."/gst/invoice-p2a.php?id=".$invoiceid);
}elseif($type=='3') {
	$inv_name = "Surcharge";
	$html = file_get_contents(HTTP."/gst/invoice-p2s.php?id=".$invoiceid);
}

// output the HTML content
$pdf->writeHTML($html, true, false, true, false, '');

//Close and output PDF document
if($action=='s') {
	$pdf->Output($company_name.'_'.$inv_name.'_'.$inv_month.'_'.$inv_num.'.pdf', 'S');
} else {
	$pdf->Output($company_name.'_'.$inv_name.'_'.$inv_month.'_'.$inv_num.'.pdf', 'D');
}
?>