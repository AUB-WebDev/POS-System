<?php
include_once('connectdb.php');
session_start();

// Check if user is authorized
//if ($_SESSION['email']=="" OR $_SESSION['role']=="user") {
//  echo json_encode(['success' => false, 'message' => 'Unauthorized access']);
//  exit();
//}

// Check if invoice_id is provided
if (!isset($_POST['invoice_id'])) {
  echo json_encode(['success' => false, 'message' => 'No order specified']);
  exit();
}

$invoice_id = $_POST['invoice_id'];

try {
  // Begin transaction
  $pdo->beginTransaction();

  //get all products in this order to restore stock
  $select_items = $pdo->prepare("SELECT product_id, qty FROM tbl_invoice_details WHERE invoice_id = :invoice_id");
  $select_items->bindParam(':invoice_id', $invoice_id);
  $select_items->execute();
  $items = $select_items->fetchAll(PDO::FETCH_ASSOC);

  //restore stock for each product
  foreach ($items as $item) {
    $update_stock = $pdo->prepare("UPDATE tbl_product SET stock = stock + :qty WHERE product_id = :product_id");
    $update_stock->bindParam(':qty', $item['qty']);
    $update_stock->bindParam(':product_id', $item['product_id']);
    $update_stock->execute();
  }

  //delete order details
  $delete_details = $pdo->prepare("DELETE FROM tbl_invoice_details WHERE invoice_id = :invoice_id");
  $delete_details->bindParam(':invoice_id', $invoice_id);
  $delete_details->execute();

  //delete the main order
  $delete_order = $pdo->prepare("DELETE FROM tbl_invoice WHERE invoice_id = :invoice_id");
  $delete_order->bindParam(':invoice_id', $invoice_id);
  $delete_order->execute();

  $pdo->commit();

  echo json_encode(['success' => true, 'message' => 'Order deleted successfully']);

} catch (PDOException $e) {
  // Rollback transaction on error
  $pdo->rollBack();
  echo json_encode(['success' => false, 'message' => 'Error deleting order: ' . $e->getMessage()]);
}
?>
