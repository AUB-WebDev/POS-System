<?php
include_once ('connectdb.php');
session_start();
if ($_SESSION['email']=="" OR $_SESSION['role']=="user") {
  header('location: ../index.php');
}

include('header.php');

if (isset($_POST['btn_save'])) {
  $sgst_txt = $_POST['sgst_txt'];
  $cgst_txt = $_POST['cgst_txt'];
  $discount_txt = $_POST['discount_txt'];

  if (empty($sgst_txt) || empty($cgst_txt) || empty($discount_txt)) {
    $_SESSION['status'] = "Please fill all the fields";
    $_SESSION['status_code'] = 'danger';
  }else{
    $insert = $pdo->prepare("INSERT INTO tbl_taxdis (sgst, cgst, discount) VALUES (:sgst, :cgst, :discount)");
    $insert->bindParam(':sgst', $sgst_txt);
    $insert->bindParam(':cgst', $cgst_txt);
    $insert->bindParam(':discount', $discount_txt);

    if ($insert->execute()) {
      $_SESSION['status'] = "Tax Added Successfully";
      $_SESSION['status_code'] = 'success';
    }else{
      $_SESSION['status'] = "Tax Not Added";
      $_SESSION['status_code'] = 'danger';
    }
  }


}

if (isset($_POST['btn_update'])) {
  $sgst_txt = $_POST['sgst_txt'];
  $cgst_txt = $_POST['cgst_txt'];
  $discount_txt = $_POST['discount_txt'];

  if (empty($sgst_txt) || empty($cgst_txt) || empty($discount_txt)) {
    $_SESSION['status'] = "Please fill all the fields";
    $_SESSION['status_code'] = 'danger';

  }else{
    $update = $pdo->prepare("update tbl_taxdis set sgst=:sgst, cgst=:cgst, discount=:discount where taxdis_id=:taxdis_id");
    $update->bindParam(':sgst', $sgst_txt);
    $update->bindParam(':cgst', $cgst_txt);
    $update->bindParam(':discount', $discount_txt);
    $update->bindParam(':taxdis_id', $_POST['taxdis_id']);

    if ($update->execute()) {
      $_SESSION['status'] = "Tax Updated Successfully";
      $_SESSION['status_code'] = 'success';
    }else{
      $_SESSION['status'] = "Tax Not Updated";
      $_SESSION['status_code'] = 'danger';
    }
  }



}

if (isset($_POST['btn_delete'])) {

  $delete = $pdo->prepare("DELETE FROM tbl_taxdis WHERE taxdis_id=:taxdis_id");
  $delete->bindParam(':taxdis_id', $_POST['btn_delete']);
  if ($delete->execute()) {
    $_SESSION['status'] = "Tax Deleted Successfully";
    $_SESSION['status_code'] = 'success';
  }else{
    $_SESSION['status'] = "Tax Not Deleted";
    $_SESSION['status_code'] = 'danger';
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
          <h1 class="m-0">TAX AND DISCOUNT</h1>
        </div><!-- /.col -->
      </div><!-- /.row -->
    </div><!-- /.container-fluid -->
  </div>
  <!-- /.content-header -->

  <!-- Main content -->
  <div class="content">
    <div class="container-fluid">
      <!-- card start -->
      <div class="card card-primary card-outline">
        <div class="card-header">
          <h5 class="m-0">Tax Form</h5>
        </div>
        <div class="card-body">
          <!-- form start -->
          <form action="" method="POST">
            <div class="row" >
              <?php
              if (isset($_POST['btn_edit'])){

                $select = $pdo->prepare("SELECT * FROM tbl_taxdis WHERE taxdis_id = :id");
                $select->bindParam(':id', $_POST['btn_edit']);
                $select->execute();

                if ($select) {
                  $row = $select->fetch(PDO::FETCH_OBJ);
                  echo '
                <div class="col-md-4">

                    <div class="card-body">
                      <div class="form-group">
                        <label for="sgst">SGST(%)</label>
                        <input type="hidden" name="taxdis_id" value="'.$row->taxdis_id.'">
                        <input type="text" class="form-control" id="sgst" name="sgst_txt" placeholder="Enter SGST" value="'.$row->sgst.'" >
                      </div>
                      <div class="form-group">
                        <label for="cgst">CGST(%)</label>
                        <input type="text" class="form-control" id="cgst" name="cgst_txt" placeholder="Enter CGST" value="'.$row->cgst.'" >
                      </div>
                      <div class="form-group">
                        <label for="discount">Discount</label>
                        <input type="text" class="form-control" id="discount" name="discount_txt" placeholder="Enter Discount" value="'.$row->discount.'" >
                      </div>
                    </div>
                    <!-- /.card-body -->

                    <div class="card-footer">
                      <button type="submit" name="btn_update" class="btn btn-primary">Update</button>
                    </div>

                </div>';
                }

              }else{
                echo '
                <div class="col-md-4">

                    <div class="card-body">
                      <div class="form-group">
                        <label for="sgst">SGST(%)</label>
                        <input type="text" class="form-control" id="sgst" name="sgst_txt" placeholder="Enter SGST" value="" >
                      </div>
                      <div class="form-group">
                        <label for="cgst">CGST(%)</label>
                        <input type="text" class="form-control" id="cgst" name="cgst_txt" placeholder="Enter CGST" value="" >
                      </div>
                      <div class="form-group">
                        <label for="discount">Discount</label>
                        <input type="text" class="form-control" id="discount" name="discount_txt" placeholder="Enter Discount" value="" >
                      </div>
                    </div>
                    <!-- /.card-body -->

                    <div class="card-footer">
                      <button type="submit" name="btn_save" class="btn btn-primary">Save</button>
                    </div>

                </div>';
              }


              ?>
              <div class="col-md-8">
                <table id="table_tax" class="table table-striped">
                  <thead>
                  <tr>
                    <td>#</td>
                    <td>SGST(%)</td>
                    <td>CGST(%)</td>
                    <td>Discount</td>
                    <td>Edit</td>
                    <td>Delete</td>
                  </tr>
                  </thead>
                  <tbody>
                  <?php
                  $select = $pdo->prepare("SELECT * FROM tbl_taxdis order by taxdis_id asc ");
                  $select->execute();

                  while($row=$select->fetch(PDO::FETCH_OBJ)){
                    echo '
                         <tr>
                           <td>'.$row->taxdis_id.'</td>
                           <td>'.$row->sgst.'</td>
                           <td>'.$row->cgst.'</td>
                           <td>'.$row->discount.'</td>
                           <td>

                            <button type="submit" name="btn_edit" class="btn btn-primary" value="'.$row->taxdis_id.'">Edit</button>
                           </td>
                           <td>
                            <button type="submit" name="btn_delete" class="btn btn-danger" value="'.$row->taxdis_id.'">Delete</button>
                           </td>
                         </tr>
                        ';
                  }
                  ?>
                  </tbody>
                  <tfoot>
                  <tr>
                    <td>#</td>
                    <td>SGST(%)</td>
                    <td>CGST(%)</td>
                    <td>Discount</td>
                    <td>Edit</td>
                    <td>Delete</td>
                  </tr>
                  </tfoot>
                </table>
              </div>
            </div>
          </form>
          <!-- form end -->
        </div>

      </div>
      <!-- card end -->
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

<script>
  $(document).ready(function (){
    $('#table_tax').DataTable();
  });
</script>
