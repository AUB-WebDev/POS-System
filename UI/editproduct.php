<?php
ob_start();
include_once ('connectdb.php');
session_start();
if ($_SESSION['email']=="" OR $_SESSION['role']=="user") {
  header('location: ../index.php');
}

include('header.php');
//make a select call to database for this first time when opening the page using the given id
$id = $_GET['id'];
$select = $pdo->prepare("SELECT * FROM tbl_product WHERE product_id = :id ");
$select->bindParam(":id", $id);

$select->execute();
$row = $select->fetch(PDO::FETCH_ASSOC);

$product_id_db = $row['product_id'];
$barcode_db = $row['barcode'];
$product_db = $row['product'];
$category_db = $row['category'];
$description_db = $row['description'];
$stock_db = $row['stock'];
$purchase_price_db = $row['purchase_price'];
$sale_price_db = $row['sale_price'];
$image_path_db = $row['image_path'];


//if the button edit is clicked, make an update call to the database by getting the values from the inputs
if(isset($_POST['btn_edit'])) {

  $barcode_txt = $_POST['barcode'];
  $product_txt = $_POST['product_name'];
  $category_txt = $_POST['selected_option'];
  $description_txt = $_POST['description'];
  $stock_txt = $_POST['stock'];
  $purchase_price_txt = $_POST['purchase_price'];
  $sale_price_txt = $_POST['sale_price'];

  //IMPORTANT: Use the image_path_db as the default image_path if we cant get $f_name, but if we can, we change the image_path_db
  //into the new path like shown below, then update to the database.

  $f_name = $_FILES['product_image']['name'];

  if(!empty($f_name)) {
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
          $image_path_db = $store;
        }else{
          $_SESSION['status'] = "Failed to upload file";
          $_SESSION['status_color'] = "error";
        }
      }
    }

  }
  //if there were $f_name, thats mean the user have enter new image, so we update the image_path_db and save!
  $update = $pdo->prepare("UPDATE tbl_product set barcode= :barcode, product= :product, category= :category,
                     description= :description, stock= :stock, purchase_price= :purchase_price,
                     sale_price= :sale_price, image_path= :image_path WHERE product_id = :id");
  $update->bindParam(":barcode", $barcode_txt);
  $update->bindParam(":product", $product_txt);
  $update->bindParam(":category", $category_txt);
  $update->bindParam(":description", $description_txt);
  $update->bindParam(":stock", $stock_txt);
  $update->bindParam(":purchase_price", $purchase_price_txt);
  $update->bindParam(":sale_price", $sale_price_txt);
  $update->bindParam(":image_path", $image_path_db);
  $update->bindParam(":id", $product_id_db);

  if ($update->execute()) {
    $_SESSION['status'] = "Product Updated successfully";
    $_SESSION['status_color'] = "success";
    //refresh the page to update the inputs
    header("Location: " . $_SERVER['REQUEST_URI']);
    exit;

  } else {
    $_SESSION['status'] = "Something went wrong";
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

          <div class="card card-success card-outline">
            <div class="card-header">
              <h5 class="m-0">Edit Product</h5>
            </div>
            <div class="card-body">
              <form action="" method="POST" name="form_edit_product" enctype="multipart/form-data">
                <div class="card-body">
                  <div class="row">
                    <div class="col-md-6">
                      <div class="form-group">
                        <label for="barcode">Barcode</label>
                        <input type="text" class="form-control" id="barcode" name="barcode" value="<?php echo $barcode_db; ?>" placeholder="Enter Barcode">
                      </div>
                      <div class="form-group">
                        <label for="product_name">Product Name</label>
                        <input type="text" class="form-control" id="product_name" name="product_name" value="<?php echo $product_db; ?>" placeholder="Enter Product Name" required>
                      </div>
                      <div class="form-group">
                        <label >Category</label>
                        <select name="selected_option" class="form-control" required>
<!--                          <option value="" disabled selected>Select Role</option>-->
                          <?php
                          $select =$pdo->prepare("SELECT * FROM tbl_category order by category ASC");
                          $select->execute();

                          while($row=$select->fetch(PDO::FETCH_ASSOC)){
                          extract($row);
                          ?>
                            <option value="<?php echo htmlspecialchars($row['category']); ?>"
                              <?php if ($category_db == $row['category']) echo 'selected'; ?>>
                              <?php echo htmlspecialchars($row['category']); ?>
                            </option>

                          <?php } ?>
                            <!--close the while loop -->
                        </select>
                      </div>
                      <div class="form-group">
                        <label>Description</label>
                        <textarea class="form-control" placeholder="Enter Description" rows="4" name="description" required ><?php echo $description_db; ?></textarea>
                      </div>
                    </div>
                    <div class="col-md-6">
                      <div class="form-group">
                        <label>Stock Quantity</label>
                        <input type="number" min="1" step="any" class="form-control" name="stock" value="<?php echo $stock_db; ?>" placeholder="Enter Stock Quantity" required>
                      </div>
                      <div class="form-group">
                        <label>Purchase Price</label>
                        <input type="number" min="0.01" step="any" class="form-control" name="purchase_price" value="<?php echo $purchase_price_db; ?>" placeholder="Enter Purchase Price" required>
                      </div>
                      <div class="form-group">
                        <label>Sale Price</label>
                        <input type="number" min="0.01" step="any" class="form-control" name="sale_price" value= "<?php echo $sale_price_db; ?>" placeholder="Enter Sale Price" required>
                      </div>
                      <div class="form-group">
                        <label>Product Image</label>
                        <br>
                          <img src="<?php  echo '../'.$image_path_db; ?>" class="img-rounded" height="70px" width="70px" style="margin-bottom: 10px">
                        <input type="file"  class="input-group" name="product_image">
                      </div>
                    </div>
                  </div>
                </div>
                <div class="card-footer">
                  <div class="text-center">
                    <button type="submit" class="btn btn-success" name="btn_edit">Edit Product</button>
                  </div>
                </div>
              </form>
            </div>
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

<?php ob_end_flush(); ?>
