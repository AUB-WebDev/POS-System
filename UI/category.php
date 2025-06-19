<?php
include_once ('connectdb.php');
session_start();
if ($_SESSION['email']=="" OR $_SESSION['role']=="user") {
  header('location: ../index.php');
}

include('header.php');

if (isset($_POST['btn_save'])) {
  $category = $_POST['category'];

  if(empty($category)){
    $_SESSION['status'] = "Category is required";
    $_SESSION['status_code'] = "error";
  }else{
    $insert = $pdo->prepare("INSERT INTO tbl_category (category) VALUES (:category_value)");
    $insert->bindParam(':category_value', $category);

    if ($insert->execute()) {
      $_SESSION['status'] = "Category Added Successfully";
      $_SESSION['status_code'] = 'success';
    }else{
      $_SESSION['status'] = "Category Not Added";
      $_SESSION['status_code'] = 'danger';
    }
  }
}

if (isset($_POST['btn_update'])) {
  $category = $_POST['category'];
  $category_id = $_POST['category_id'];

  if(empty($category)){
    $_SESSION['status'] = "Category is required";
    $_SESSION['status_code'] = "error";
  }else{
    $update = $pdo->prepare("update tbl_category set category=:category where category_id=:category_id");
    $update->bindParam(':category', $category);
    $update->bindParam(':category_id', $category_id);

    if ($update->execute()) {
      $_SESSION['status'] = "Category Updated Successfully";
      $_SESSION['status_code'] = 'success';
    }else{
      $_SESSION['status'] = "Category Not Updated";
      $_SESSION['status_code'] = 'danger';
    }
  }
}

if (isset($_POST['btn_delete'])) {
  $delete = $pdo->prepare("DELETE FROM tbl_category WHERE category_id=".$_POST['btn_delete']);
  if ($delete->execute()) {
    $_SESSION['status'] = "Category Deleted Successfully";
    $_SESSION['status_code'] = 'success';
  }else{
    $_SESSION['status'] = "Category Not Deleted";
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
          <h1 class="m-0">Registration</h1>
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
          <h5 class="m-0">Category Form</h5>
        </div>
        <div class="card-body">
          <!-- form start -->
          <form action="" method="POST">
          <div class="row" >
            <?php
            if (isset($_POST['btn_edit'])){

              $select = $pdo->prepare("SELECT * FROM tbl_category WHERE category_id = :id");
              $select->bindParam(':id', $_POST['btn_edit']);
              $select->execute();

              if ($select) {
                $row = $select->fetch(PDO::FETCH_OBJ);
                echo '
                <div class="col-md-4">

                    <div class="card-body">
                      <div class="form-group">
                        <label for="category">Category</label>
                        <input type="hidden" class="form-control" id="category" name="category_id" placeholder="Enter Category" value="'.$row->category_id.'">
                        <input type="text" class="form-control" id="category" name="category" placeholder="Enter Category" value="'.$row->category.'">
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
                        <label for="category">Category</label>
                        <input type="text" class="form-control" id="category" name="category" placeholder="Enter Category" >
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
              <table id="table_category" class="table table-striped">
                <thead>
                <tr>
                  <td>#</td>
                  <td>Category</td>
                  <td>Edit</td>
                  <td>Delete</td>
                </tr>
                </thead>
                <tbody>
                <?php
                $select = $pdo->prepare("SELECT * FROM tbl_category order by category_id asc ");
                $select->execute();

                while($row=$select->fetch(PDO::FETCH_OBJ)){
                  echo '
                         <tr>
                           <td>'.$row->category_id.'</td>
                           <td>'.$row->category.'</td>
                           <td>
                            <button type="submit" name="btn_edit" class="btn btn-primary" value="'.$row->category_id.'">Edit</button>
                           </td>
                           <td>
                            <button type="submit" name="btn_delete" class="btn btn-danger" value="'.$row->category_id.'">Delete</button>
                           </td>
                         </tr>
                        ';
                }
                ?>
                </tbody>
                <tfoot>
                  <tr>
                    <td>#</td>
                    <td>Category</td>
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
    $('#table_category').DataTable();
  });
</script>
