<?php

include_once("connectdb.php");

$invoice_id = $_GET['id'];
$select = $pdo->prepare("select * from tbl_invoice_details a INNER JOIN tbl_product b on a.product_id=b.product_id where a.invoice_id = :invoice_id");
$select->bindParam(":invoice_id", $invoice_id);

$select->execute();
$row_invoice_details = $select->fetchAll(PDO::FETCH_ASSOC);


$response = $row_invoice_details ;

header("Content-type: application/json");

echo json_encode($row_invoice_details);

?>
