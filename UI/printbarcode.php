<?php
include_once ('connectdb.php');
session_start();
if ($_SESSION['email']=="" OR $_SESSION['role']=="user") {
  header('location: ../index.php');
}


include('header.php');

?>


<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
  <!-- Content Header (Page header) -->
  <div class="content-header">
    <div class="container-fluid">
      <div class="row mb-2">
        <div class="col-sm-6">
          <h1 class="m-0"></h1>
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
              <h5 class="m-0">Print Barcode Stickers:</h5>
            </div>
            <div class="card-body">
              <form class="form-horizontal" method="post" action="php_barcode/barcode.php" target="_blank">
                <?php

                  $product_id = $_GET['id'];
                  $select = $pdo->prepare("SELECT * FROM tbl_product WHERE product_id=:product_id");
                  $select->bindParam(':product_id', $product_id);

                  $select->execute();
                  while($row = $select->fetch(PDO::FETCH_OBJ)){
                    echo '
                      <div class="row">
                        <div class="col-md-6">
                          <p class="list-group-item list-group-item-info"><b>Print Barcode</b></p>
                          <div class="form-group">
                            <label class="control-label col-sm-2" for="product">Product:</label>
                            <div class="col-sm-10">
                              <input autocomplete="OFF" type="text" class="form-control" id="product" name="product" value="'.$row->product.'" readonly>
                            </div>
                          </div>
                          <div class="form-group">
                            <label class="control-label col-sm-2" for="product_id">Barcode:</label>
                            <div class="col-sm-10">
                              <input autocomplete="OFF" type="text" class="form-control" id="barcode" name="barcode" value="'.$row->barcode.'" readonly>
                            </div>
                          </div>
                          <div class="form-group">
                            <label class="control-label col-sm-2" for="rate">Price</label>
                            <div class="col-sm-10">
                              <input autocomplete="OFF" type="text" class="form-control" id="price"  name="sale_price" value="$'.$row->sale_price.'" readonly>
                            </div>
                          </div>
                          <div class="form-group">
                            <label class="control-label col-sm-2" for="print_qty">Barcode Quantity</label>
                            <div class="col-sm-10">
                              <input autocomplete="OFF" type="print_qty" class="form-control" id="print_qty"  name="print_qty" autofocus required>
                            </div>
                          </div>

                          <div class="form-group">
                            <div class="col-sm-offset-2 col-sm-10">
                              <button type="submit" class="btn btn-success">Submit</button>
                            </div>
                          </div>

                        </div>
                        <div class="col-md-6">
                            <p class="list-group-item list-group-item-info"  ><b>Product Image</b></p>
                            <img src="../'.$row->image_path.'" class="img img-thumbnail img-responsive">
                        </div>
                      </div>
                    ';
                  }

                ?>

              </form>
            </div>
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
