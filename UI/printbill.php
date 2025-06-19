<?php
require_once('connectdb.php');
require __DIR__.'/../vendor/autoload.php';
use Fpdf\Fpdf;

// Get invoice ID from URL
$id = $_GET['id'] ?? 0;

// Fetch invoice header
$select = $pdo->prepare("SELECT * FROM tbl_invoice WHERE invoice_id = :id");
$select->bindParam(":id", $id);
$select->execute();
$row = $select->fetch(PDO::FETCH_OBJ);

if (!$row) {
  die("Invoice not found!");
}

// Create PDF (80mm width receipt-style format)
$pdf = new FPDF("P", "mm", array(80, 200));
$pdf->AddPage();

// Set document properties
$pdf->SetTitle("Invoice #" . $row->invoice_id);
$pdf->SetAuthor("POS System");

// Header
$pdf->SetFont('Arial', 'B', 16);
$pdf->Cell(60, 8, 'POS-System', 0, 1, 'C');

$pdf->SetFont('Arial', 'B', 8);
$pdf->Cell(60, 5, 'PHONE: 010 xxx xxx', 0, 1, 'C');
$pdf->Cell(60, 5, 'WEBSITE: www.mypos.local', 0, 1, 'C');
$pdf->Line(7, 28, 72, 28);

// Invoice info
$pdf->SetFont('Arial', 'BI', 8);
$pdf->Cell(20, 5, 'Invoice No:', 0, 0);
$pdf->SetFont('Courier', 'BI', 8);
$pdf->Cell(40, 5, $row->invoice_id, 0, 1);

$pdf->SetFont('Arial', 'BI', 8);
$pdf->Cell(20, 5, 'Date:', 0, 0);
$pdf->SetFont('Courier', 'BI', 8);
$pdf->Cell(40, 5, date('d/m/Y', strtotime($row->order_date)), 0, 1);

$pdf->SetFont('Arial', 'BI', 8);
$pdf->Cell(20, 5, 'Wifi:', 0, 0);
$pdf->SetFont('Courier', 'BI', 8);
$pdf->Cell(40, 5,'mypos', 0, 1);

$pdf->SetFont('Arial', 'BI', 8);
$pdf->Cell(20, 5, 'Password:', 0, 0);
$pdf->SetFont('Courier', 'BI', 8);
$pdf->Cell(40, 5, '77778888', 0, 1);

$pdf->Cell(60, 4, '', 0, 1);
$pdf->Line(7, 50, 72, 50);

// Products header
$pdf->SetX(7);
$pdf->SetFont('Courier', 'B', 8);
$pdf->Cell(34, 7, 'PRODUCT', 1, 0, 'C');
$pdf->Cell(8, 7, 'QTY', 1, 0, 'C');
$pdf->Cell(11, 7, 'PRICE', 1, 0, 'C');
$pdf->Cell(12, 7, 'TOTAL', 1, 1, 'C');

// Fetch and display products
$select = $pdo->prepare("SELECT * FROM tbl_invoice_details WHERE invoice_id = :id");
$select->bindParam(":id", $id);
$select->execute();

while ($product = $select->fetch(PDO::FETCH_OBJ)) {
  $pdf->SetX(7);
  $pdf->SetFont('Helvetica', '', 8);
  $pdf->Cell(34, 5, substr($product->product_name, 0, 20), 1, 0, 'L'); // Limit product name length
  $pdf->Cell(8, 5, $product->qty, 1, 0, 'C');
  $pdf->Cell(11, 5, number_format($product->rate, 2), 1, 0, 'C');
  $pdf->Cell(12, 5, number_format($product->saleprice, 2), 1, 1, 'C');
}

// Calculations
$discount_amount = $row->subtotal * ($row->discount / 100);
$sgst_amount = $row->subtotal * ($row->sgst / 100);
$cgst_amount = $row->subtotal * ($row->cgst / 100);
$total_before_tax = $row->subtotal - $discount_amount;

// Summary
$pdf->SetX(7);
$pdf->SetFont('Courier', 'B', 8);
$pdf->Cell(45, 5, 'SUBTOTAL:', 1, 0, 'R');
$pdf->Cell(20, 5, number_format($row->subtotal, 2), 1, 1, 'C');

$pdf->SetX(7);
$pdf->Cell(45, 5, 'DISCOUNT (' . $row->discount . '%):', 1, 0, 'R');
$pdf->Cell(20, 5, number_format($discount_amount, 2), 1, 1, 'C');

$pdf->SetX(7);
$pdf->Cell(45, 5, 'SGST (' . $row->sgst . '%):', 1, 0, 'R');
$pdf->Cell(20, 5, number_format($sgst_amount, 2), 1, 1, 'C');

$pdf->SetX(7);
$pdf->Cell(45, 5, 'CGST (' . $row->cgst . '%):', 1, 0, 'R');
$pdf->Cell(20, 5, number_format($cgst_amount, 2), 1, 1, 'C');

$pdf->SetX(7);
$pdf->SetFont('Courier', 'B', 10);
$pdf->Cell(45, 7, 'TOTAL:', 1, 0, 'R');
$pdf->Cell(20, 7, number_format($row->total, 2), 1, 1, 'C');

$pdf->SetX(7);
$pdf->SetFont('Courier', 'B', 8);
$pdf->Cell(45, 5, 'PAID:', 1, 0, 'R');
$pdf->Cell(20, 5, number_format($row->paid, 2), 1, 1, 'C');

$pdf->SetX(7);
$pdf->Cell(45, 5, 'DUE:', 1, 0, 'R');
$pdf->Cell(20, 5, number_format($row->due, 2), 1, 1, 'C');

$pdf->SetX(7);
$pdf->SetFont('Arial', 'I', 6);
$pdf->Cell(65, 5, 'Payment Method: ' . $row->payment_type, 0, 1, 'C');
$pdf->Cell(58, 5, 'Thank you for your supporting!', 0 , 0, 'C');

// Output PDF
$pdf->Output('I', 'Invoice_' . $row->invoice_id . '.pdf');
?>
