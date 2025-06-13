<?php
include_once ('connectdb.php');
session_start();
if ($_SESSION['email']=="" OR $_SESSION['role']=="user") {
  header('location: ../index.php');
}

include_once('header.php');

if(isset($_POST['btn_save'])){
  $barcode = $_POST['barcode'];
  $product_name = $_POST['product_name'];
  $category = $_POST['selected_option'];
  $description = $_POST['description'];
  $stock = $_POST['stock'];
  $purchase_price = $_POST['purchase_price'];
  $sale_price = $_POST['sale_price'];

  //handle upload file
  $f_name = $_FILES['product_image']['name'];
  $f_size = $_FILES['product_image']['size'];
  $f_tmp = $_FILES['product_image']['tmp_name'];

  $f_extension = strtolower(pathinfo($f_name, PATHINFO_EXTENSION));

  $f_new_file = uniqid().".".$f_extension;

  $store = "uploads/".$f_new_file;

  if($f_extension=="jpg" or $f_extension=="png" or $f_extension=="jpeg" or $f_extension=="gif"){
    if($f_size >= 10000000){
      $_SESSION['status'] = "File can only be under 10MBs";
      $_SESSION['status_color'] = "error";
    }else{
        if (move_uploaded_file($f_tmp, "../".$store)) {
          $insert = $pdo->prepare("Insert into tbl_product(barcode, product, category, description, stock, purchase_price, sale_price, image_path)
                values(:barcode, :product_name, :category, :description, :stock, :purchase_price, :sale_price, :image_path)");
          if(empty($barcode)){
            //use GEN at the start to specify as the generated barcode, you may change it accordingly
            //use date as the barcode number
            $barcode = "GEN" . date("YmdHis") . sprintf("%03d", (microtime(true) - floor(microtime(true))) * 1000);
          }

          $insert->bindParam(':barcode', $barcode);
          $insert->bindParam(':product_name', $product_name);
          $insert->bindParam(':category', $category);
          $insert->bindParam(':description', $description);
          $insert->bindParam(':stock', $stock);
          $insert->bindParam(':purchase_price', $purchase_price);
          $insert->bindParam(':sale_price', $sale_price);
          $insert->bindParam(':image_path', $store);

          if ($insert->execute()) {
            $_SESSION['status'] = "Product added successfully";
            $_SESSION['status_color'] = "success";
          } else {
            $_SESSION['status'] = "Something went wrong";
            $_SESSION['status_color'] = "error";
          }
        }
    }

  }else{
    $_SESSION['status'] = "File type not allowed";
    $_SESSION['status_color'] = "error";
  }

}

?>


<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
  <!-- Content Header (Page header) -->
  <div class="content-header">
    <div class="container-fluid">
      <div class="row mb-2">
        <div class="col-sm-6">
          <h1 class="m-0">Add Product</h1>
        </div><!-- /.col -->
      </div><!-- /.row -->
    </div><!-- /.container-fluid -->
  </div>
  <!-- /.content-header -->

  <!-- Main content -->
  <div class="content">
    <div class="container-fluid">
      <div class="row">
        <div class="col-lg-12 ">

          <div class="card card-primary card-outline">
            <div class="card-header">
              <h5 class="m-0">Product</h5>
            </div>
            <form action="" method="POST" enctype="multipart/form-data">
              <div class="card-body">
                <div class="row">
                  <div class="col-md-6">
                    <div class="form-group">
                      <label for="barcode">Barcode</label>
                      <input type="text" class="form-control" id="barcode" name="barcode" placeholder="Enter Barcode">
                    </div>
                    <div class="form-group">
                      <label for="product_name">Product Name</label>
                      <input type="text" class="form-control" id="product_name" name="product_name" placeholder="Enter Product Name" required>
                    </div>
                    <div class="form-group">
                      <label >Category</label>
                      <select name="selected_option" class="form-control" required>
                        <option value="" disabled selected>Select Role</option>
                        <?php
                        $select =$pdo->prepare("SELECT * FROM tbl_category");
                        $select->execute();

                        while($row=$select->fetch(PDO::FETCH_ASSOC)){
                          extract($row);

                        ?>
                          <option><?php echo $row['category']; ?>
                        <?php } ?>
                          <!--close the while loop -->
                      </select>
                    </div>
                    <div class="form-group">
                      <label>Description</label>
                      <textarea class="form-control" placeholder="Enter Description" rows="4" name="description" required ></textarea>
                    </div>
                  </div>
                  <div class="col-md-6">
                    <div class="form-group">
                      <label>Stock Quantity</label>
                      <input type="number" min="1" step="any" class="form-control" name="stock" placeholder="Enter Stock Quantity" required>
                    </div>
                    <div class="form-group">
                      <label>Purchase Price</label>
                      <input type="number" min="0.01" step="any" class="form-control" name="purchase_price" placeholder="Enter Purchase Price" required>
                    </div>
                    <div class="form-group">
                      <label>Sale Price</label>
                      <input type="number" min="0.01" step="any" class="form-control" name="sale_price" placeholder="Enter Sale Price" required>
                    </div>
                    <div class="form-group">
                      <label>Product Image</label>
                      <input type="file"  class="input-group" name="product_image" required>
<!--                      <p>Upload Image</p>-->
                    </div>
                  </div>
                </div>
              </div>
              <div class="card-footer">
                <div class="text-center">
                  <button type="submit" class="btn btn-primary" name="btn_save">Save Product</button>
                </div>
              </div>
            </form>
          </div>
        </div>
        <!-- /.col-md-6 -->
      </div>
      <!-- /.row -->
    </div><!-- /.container-fluid -->
  </div>
  <!-- /.content -->
</div>
<!-- /.content-wrapper -->

<?php
include('footer.php');
?>

<?php
if(isset($_SESSION['status']) && $_SESSION['status_code'] != "") {
  ?>
  <script>

    Swal.fire({
      icon: '<?php echo $_SESSION['status_code']; ?>',
      title: '<?php echo $_SESSION['status']; ?>'
    })

  </script>
  <?php
  unset($_SESSION['status']);
}
?>
