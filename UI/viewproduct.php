<?php
include_once ('connectdb.php');
include_once 'php_barcode/barcode128.php';

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
              <h5 class="m-0">View Product</h5>
            </div>
            <div class="card-body">
              <?php
              $id = $_GET['id'];

              $select = $pdo->prepare("SELECT * FROM tbl_product WHERE product_id=$id");
              $select->execute();

              while ($row = $select->fetch(PDO::FETCH_OBJ)) {

                echo '
                  <div class="row">
                    <div class="col-md-6">
                      <p class="list-group-item list-group-item-info"><b>Product Details</b></p>
                      <ul class="list-group">
                        <li class="list-group-item"><b>Barcode</b> <span class="badge badge-light float-right">'.bar128($row->barcode).'</span></li>
                        <li class="list-group-item"><b>Product</b> <span class="badge badge-warning float-right">'. $row->product.'</span></li>
                        <li class="list-group-item"><b>Category</b> <span class="badge  badge-success float-right">'.$row->category.'</span></li>
                        <li class="list-group-item"><b>Description </b><span class="badge badge-primary float-right">'. $row->description.'</span></li>
                        <li class="list-group-item"><b>Stock </b><span class="badge badge-danger float-right">'. $row->stock .'</span></li>
                        <li class="list-group-item"><b>Purchase Price</b> <span class="badge badge-secondary float-right">'.$row->purchase_price.'</span></li>
                        <li class="list-group-item"><b>Sale Price </b> <span class="badge badge-dark float-right">'.$row->sale_price.'</span></li>
                        <li class="list-group-item"><b>Product Profit </b> <span class="badge badge-success float-right">'.($row->sale_price - $row->purchase_price).'</span></li>

                      </ul>
                    </div>
                    <div class="col-md-6">
                      <p class="list-group-item list-group-item-info"  ><b>Product Image</b></p>
                      <img src="../'.$row->image_path.'" class="img img-thumbnail img-responsive">
                    </div>
                  </div>';

              }

              ?>

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
