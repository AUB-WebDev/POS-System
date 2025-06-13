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
          <h1 class="m-0">Product List</h1>
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
              <h5 class="m-0">Product List</h5>
            </div>
            <div class="card-body">
              <table class="table table-striped" id="product_table">
                <thead>
                <tr>
                  <td>Barcode</td>
                  <td>Product</td>
                  <td>Category</td>
                  <td>Description</td>
                  <td>Stock</td>
                  <td>Purchase Price</td>
                  <td>Sale Price</td>
                  <td>Image</td>
                  <td>Actions</td>
                </tr>
                </thead>
                <tbody>
                <?php
                $select = $pdo->prepare("SELECT * FROM tbl_product order by product_id asc ");
                $select->execute();

                while($row=$select->fetch(PDO::FETCH_OBJ)){
                  echo '
                         <tr xmlns="http://www.w3.org/1999/html">
                           <td>' .$row->barcode.'</td>
                           <td>'.$row->product.'</td>
                           <td>'.$row->category.'</td>
                           <td>'.$row->description.'</td>
                           <td>'.$row->stock.'</td>
                           <td>'.$row->purchase_price.'</td>
                           <td>'.$row->sale_price.'</td>
                           <td><img src="../'.$row->image_path.'" class="img-rounded" width="60px" height="60px"> </td>
                           <td>
                            <div class="btn-group">
                                <a href="printbarcode.php?id='.$row->product_id.'" class="btn btn-primary btn-xs" role="button">
                                    <span class="fa fa-barcode" style="color: #FFFFFF" data-toggle="tooltip" title="Print Barcode" "></span>
                                </a>
                                <a href="viewproduct.php?id='.$row->product_id.'" class="btn btn-warning btn-xs" role="button">
                                    <span class="fa fa-eye" style="color: #FFFFFF" data-toggle="tooltip" title="View Product" "></span>
                                </a>
                                <a href="editproduct.php?id='.$row->product_id.'" class="btn btn-success btn-xs" role="button">
                                    <span class="fa fa-edit" style="color: #FFFFFF" data-toggle="tooltip" title="Edit Product" "></span>
                                </a>
                                <button id="'.$row->product_id.'" class="btn btn-danger btn-xs btndelete" >
                                    <span class="fa fa-trash" style="color: #FFFFFF" data-toggle="tooltip" title="Delete Product" "></span>
                                </button>
                            </div>
                           </td>
                         </tr>
                        ';
                }
                ?>

                </tbody>
              </table>
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

<script>
  $(document).ready(function (){
    $('#product_table').DataTable();
  });

  $(document).ready(function (){
    $('[data-toggle="tooltip"]').tooltip();
  });
</script>


<script>
  $(document).ready(function (){
    $('.btndelete').click(function (){

      var tdt = $(this);
      var id = $(this).attr("id");

      Swal.fire({
        title: "Do you want to delete?",
        text: "You won't be able to revert this!",
        icon: "warning",
        showCancelButton: true,
        confirmButtonColor: "#3085d6",
        cancelButtonColor: "#d33",
        confirmButtonText: "Yes, delete it!"
      }).then((result) => {
        if (result.isConfirmed) {
          $.ajax({
            url: "productdelete.php",
            type: "POST",
            data:{
              product_id : id
            },
            success: function(data){
              tdt.parents('tr').hide();
            }
          });
          Swal.fire({
            title: "Deleted!",
            text: "Your product has been deleted.",
            icon: "success"
          });
        }
      });


    });
  });
</script>
