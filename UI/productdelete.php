<?php
  include_once 'connectdb.php';

  $id = $_POST['product_id'];
  $delete = $pdo->prepare("DELETE FROM tbl_product WHERE product_id=:id");
  $delete->bindParam(':id', $id);

  if($delete->execute()){
    echo "<script>alert('Product Deleted Successfully');</script>";
  }else{
    echo "Something went wrong";
  }
?>
