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
          <h1 class="m-0">Order List</h1>
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
              <h5 class="m-0">Orders</h5>
            </div>
            <div class="card-body">
              <table class="table table-striped" id="order_table">
                <thead>
                <tr>
                  <td>Invoice ID</td>
                  <td>Order Date</td>
                  <td>Total</td>
                  <td>Paid</td>
                  <td>Due</td>
                  <td>Payment Type</td>
                  <td>Actions</td>
                </tr>
                </thead>
                <tbody>
                <?php
                $select = $pdo->prepare("SELECT * FROM tbl_invoice order by invoice_id desc ");
                $select->execute();

                while($row=$select->fetch(PDO::FETCH_OBJ)){
                  echo '
                         <tr xmlns="http://www.w3.org/1999/html">
                           <td>'.$row->invoice_id.'</td>
                           <td>'.$row->order_date.'</td>
                           <td>'.$row->total.'</td>
                           <td>'.$row->paid.'</td>
                           <td>'.$row->due.'</td>
                           <td>'.$row->payment_type.'</td>
                           <td>
                            <div class="btn-group">
                                <a href="printbill.php?id='.$row->invoice_id.'" class="btn btn-warning" role="button">
                                    <span class="fa fa-print" style="color: #FFFFFF" data-toggle="tooltip" title="Print Bill" "></span>
                                </a>
                                <a href="editorder.php?id='.$row->invoice_id.'" class="btn btn-success" role="button">
                                    <span class="fa fa-edit" style="color: #FFFFFF" data-toggle="tooltip" title="Edit Order" "></span>
                                </a>
                                <button id="'.$row->invoice_id.'" class="btn btn-danger btndelete" >
                                    <span class="fa fa-trash" style="color: #FFFFFF" data-toggle="tooltip" title="Delete Order" "></span>
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
  $(document).ready(function(){
    $('#order_table').DataTable({
      "order": [[0, "desc"]]
    });
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
        title: "Do you want to delete this order?",
        text: "This will permanently delete the order and all its items!",
        icon: "warning",
        showCancelButton: true,
        confirmButtonColor: "#3085d6",
        cancelButtonColor: "#d33",
        confirmButtonText: "Yes, delete it!"
      }).then((result) => {
        if (result.isConfirmed) {
          $.ajax({
            url: "orderdelete.php",
            type: "POST",
            data: {
              invoice_id: id
            },
            dataType: "json",
            success: function(response) {
              if(response.success) {
                tdt.parents('tr').fadeOut('slow', function(){
                  $(this).remove();
                });
                Swal.fire({
                  title: "Deleted!",
                  text: response.message,
                  icon: "success"
                });
              } else {
                Swal.fire({
                  title: "Error!",
                  text: response.message,
                  icon: "error"
                });
              }
            },
            error: function() {
              Swal.fire({
                title: "Error!",
                text: "Failed to communicate with server",
                icon: "error"
              });
            }
          });
        }
      });
    });
  });
</script>
