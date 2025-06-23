<?php
include_once ('connectdb.php');
session_start();
if ($_SESSION['email']==""){
  header('location: ../index.php');
}

if ($_SESSION['role']=="admin"){
  include('header.php');
}else{
  include('headeruser.php');
}

if(isset($_POST['btn_change'])){
  $old_password = $_POST['txt_old_password'];
  $new_password = $_POST['txt_new_password'];
  $confirm_password = $_POST['txt_confirm_password'];

  $email = $_SESSION['email'];
  $select = $pdo->prepare("SELECT * FROM tbl_user WHERE email='$email'");
  $select->execute();

  $row = $select->fetch(PDO::FETCH_ASSOC);

  $password_db = $row['password'];

  if($password_db == $old_password){
    if($new_password == $confirm_password){
      $update = $pdo->prepare("UPDATE tbl_user SET password=:password WHERE email=:email");
      $update->bindParam(":email", $email);
      $update->bindParam(":password", $new_password);

      if($update->execute()){
        $_SESSION['status'] = "Password Updated Successfully";
        $_SESSION['status_code'] = 'success';
      }else {
        $_SESSION['status'] = "Password Update Failed";
        $_SESSION['status_code'] = 'error';
      }
    }
    else{
      $_SESSION['status'] = "New Password Not Matched";
      $_SESSION['status_code'] = 'error';
    }
  }else{
    $_SESSION['status'] = "Password Incorrect";
    $_SESSION['status_code'] = 'error';
  }

}


//$_SESSION['status'] =  "Password Changed Successfully";
//$_SESSION['status_code'] = 'success';

?>


<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
  <!-- Content Header (Page header) -->
  <div class="content-header">
    <div class="container-fluid">
      <div class="row mb-2">
        <div class="col-sm-6">
          <h1 class="m-0">Change Password</h1>
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
            <!-- Horizontal Form -->
            <div class="card card-info">
              <div class="card-header">
                <h3 class="card-title">Change your password</h3>
              </div>
              <!-- /.card-header -->
              <!-- form start -->
              <form class="form-horizontal" method="POST">
                <div class="card-body">
                  <div class="form-group row">
                    <label for="inputPassword3" class="col-sm-2 col-form-label">Old Password</label>
                    <div class="col-sm-10">
                      <input type="password" class="form-control" id="inputPassword3" name="txt_old_password" placeholder="Old Password" required>
                    </div>
                  </div>
                  <div class="form-group row">
                    <label for="inputPassword3" class="col-sm-2 col-form-label">New Password</label>
                    <div class="col-sm-10">
                      <input type="password" class="form-control" id="inputPassword3" name="txt_new_password" placeholder="New Password" required>
                    </div>
                  </div>
                  <div class="form-group row">
                    <label for="inputPassword3" class="col-sm-2 col-form-label">Confirm New Password</label>
                    <div class="col-sm-10">
                      <input type="password" class="form-control" id="inputPassword3" name="txt_confirm_password" placeholder="Confirm New Password" required>
                    </div>
                  </div>
                    <div class="form-group row">
<!--                        <label for="inputPassword3" class="col-sm-2 col-form-label">Confirm New Password</label>-->
<!--                        <div class="col-sm-10">-->
<!--                            <input type="password" class="form-control" id="inputPassword3" name="txt_confirm_password" placeholder="Confirm New Password" required>-->
<!--                        </div>-->
                        <a href="../forgot-password-v2.php" style="margin-left: 5px">forgot password?</a>
                    </div>
                </div>
                <!-- /.card-body -->
                <div class="card-footer">
                  <button type="submit" class="btn btn-info" name="btn_change">Change Password</button>
                </div>
                <!-- /.card-footer -->
              </form>
            </div>
            <!-- /.card -->
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
