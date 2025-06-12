<?php
include_once ('connectdb.php');
session_start();
if ($_SESSION['email']=="" OR $_SESSION['role']=="user") {
  header('location: ../index.php');
}

include('header.php');

error_reporting(0);

$id = $_GET['id'];
if (isset($id)) {
  $delete = $pdo->prepare("DELETE FROM tbl_user WHERE user_id=:id");
  $delete->bindParam(':id', $id);

  if ($delete->execute()) {
    $_SESSION['status'] = "User has been deleted successfully";
    $_SESSION['status_code'] = "success";
  }else{
    $_SESSION['status'] = "User is not deleted";
    $_SESSION['status_code'] = "danger";
  }
}

if (isset($_POST['btn_save'])) {
  $username = $_POST['username'];
  $email = $_POST['email'];
  $password = $_POST['password'];
  $role = $_POST['selected_option'];

  if (isset($_POST['email'])){
    $select = $pdo->prepare("SELECT * FROM tbl_user WHERE email=:email");
    $select->bindParam(':email', $email);
    $select->execute();

    if($select->rowCount()>0){
      $_SESSION['status'] = 'Email Already Exists. Try Another Email';
      $_SESSION['status_code'] = 'Warning';
    }else{
      $insert = $pdo -> prepare("insert into tbl_user(email, password, role, username) values (:email, :password, :role, :username)");
      $insert->bindParam(':email', $email);
      $insert->bindParam(':password', $password);
      $insert->bindParam(':role', $role);
      $insert->bindParam(':username', $username);

      if ($insert->execute()) {
        $_SESSION['status_code'] = "success";
        $_SESSION['status'] = "User saved successfully";
      }else{
        $_SESSION['status_code'] = "error";
        $_SESSION['status'] = "User save failed";
      }
    }
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
              <h5 class="m-0">Featured</h5>
            </div>
            <div class="card-body">
              <div class="row" >
                <div class="col-md-4">
                  <!-- form start -->
                  <form action="" method="POSt">
                    <div class="card-body">
                      <div class="form-group">
                        <label for="UserName">Username</label>
                        <input type="text" class="form-control" id="UserName" name="username" placeholder="Enter Username" required>
                      </div>
                      <div class="form-group">
                        <label for="exampleInputEmail1">Email address</label>
                        <input type="email" class="form-control" id="exampleInputEmail1" name="email" placeholder="Enter email" required>
                      </div>
                      <div class="form-group">
                        <label for="exampleInputPassword1">Password</label>
                        <input type="password" class="form-control" id="exampleInputPassword1" name="password" placeholder="Password" required>
                      </div>
                      <div class="form-group">
                        <label >Role</label>
                        <select name="selected_option" class="form-control" required>
                          <option value="" disabled selected>Select Role</option>
                          <option value="admin">Admin</option>
                          <option value="user">User</option>
                        </select>
                      </div>
                    </div>
                    <!-- /.card-body -->

                    <div class="card-footer">
                      <button type="submit" name="btn_save" class="btn btn-primary">Save</button>
                    </div>
                  </form>
                  <!-- form end -->
                </div>
                <div class="col-md-8">
                  <table class="table table-striped">
                    <thead>
                      <tr>
                        <td>#</td>
                        <td>Username</td>
                        <td>Email</td>
                        <td>Password</td>
                        <td>Role</td>
                        <td>Delete</td>
                      </tr>
                    </thead>
                    <tbody>
                    <?php
                      $select = $pdo->prepare("SELECT * FROM tbl_user order by user_id asc ");
                      $select->execute();

                      while($row=$select->fetch(PDO::FETCH_OBJ)){
                        echo '
                         <tr>
                           <td>'.$row->user_id.'</td>
                           <td>'.$row->username.'</td>
                           <td>'.$row->email.'</td>
                           <td>'.$row->password.'</td>
                           <td>'.$row->role.'</td>
                           <td>
                            <a href="registration.php?id='.$row->user_id.'" class="btn btn-danger"><i class="fa fa-trash-alt"></i> </a>
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

