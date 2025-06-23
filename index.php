<?php

  include_once 'UI\connectdb.php';
  session_start();

  if(isset($_POST['btn_login'])) {
    $email = $_POST['txt_email'];
    $password = $_POST['txt_password'];

    $select = $pdo->prepare("SELECT * FROM tbl_user WHERE email = :email AND password = :password");
    $select->bindParam(':email', $email);
    $select->bindParam(':password', $password);
    $select->execute();

    $row = $select->fetch(PDO::FETCH_ASSOC);
    if (is_array($row)) {

      if ($row['email'] == $email && $row['password'] == $password && $row['role'] == 'admin') {

        $_SESSION['email'] = $row['email'];
        $_SESSION['password'] = $row['password'];
        $_SESSION['role'] = $row['role'];
        $_SESSION['userid'] = $row['user_id'];
        $_SESSION['username'] = $row['username'];
        $_SESSION['status'] =  "Login Successful as Admin";
        $_SESSION['status_code'] = 'success';
        header('refresh:1; UI/productlist.php');
      }
      elseif ($row['email'] == $email && $row['password'] == $password && $row['role'] == 'user') {

        $_SESSION['email'] = $row['email'];
        $_SESSION['password'] = $row['password'];
        $_SESSION['role'] = $row['role'];
        $_SESSION['userid'] = $row['user_id'];
        $_SESSION['username'] = $row['username'];
        $_SESSION['status'] =  "Login Successful as User";
        $_SESSION['status_code'] = 'success';
        header('refresh:1; UI/productlist.php');
      }
      else { //since mysql is not case sensitive, use this
        $_SESSION['status_code'] = 'error';
        $_SESSION['status'] = "Incorrect Email or Password";
      }

    } else {
      $_SESSION['status_code'] = 'error';
      $_SESSION['status'] = "Incorrect Email or Password";
    }

  }
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>POS Barcode | Log in </title>

  <!-- Google Font: Source Sans Pro -->
  <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
  <!-- Font Awesome -->
  <link rel="stylesheet" href="plugins/fontawesome-free/css/all.min.css">
  <!-- icheck bootstrap -->
  <link rel="stylesheet" href="plugins/icheck-bootstrap/icheck-bootstrap.min.css">
  <!-- Theme style -->
  <link rel="stylesheet" href="dist/css/adminlte.min.css">

  <!-- SweetAlert2 -->
  <link rel="stylesheet" href="plugins/sweetalert2-theme-bootstrap-4/bootstrap-4.min.css">
  <!-- Toastr -->
  <link rel="stylesheet" href="plugins/toastr/toastr.min.css">

</head>
<body class="hold-transition login-page">
<div class="login-box">
  <!-- /.login-logo -->
  <div class="card card-outline card-primary">
    <div class="card-header text-center">
      <a href="#" class="h1"><b>POS</b>Barcode</a>
    </div>
    <div class="card-body">
      <p class="login-box-msg">Login to start your session</p>

      <form action="" method="POST">
        <div class="input-group mb-3">
          <input type="email" class="form-control" placeholder="Email" name="txt_email" autofocus required>
          <div class="input-group-append">
            <div class="input-group-text">
              <span class="fas fa-envelope"></span>
            </div>
          </div>
        </div>
        <div class="input-group mb-3">
          <input type="password" class="form-control" placeholder="Password" name="txt_password" required>
          <div class="input-group-append">
            <div class="input-group-text">
              <span class="fas fa-lock"></span>
            </div>
          </div>
        </div>
        <div class="row">
          <div class="col-8">
            <p class="mb-1">
              <a href="forgot-password-v2.php">forgot password?</a>
            </p>
          </div>
          <!-- /.col -->
          <div class="col-4">
            <button type="submit" class="btn btn-primary btn-block" name="btn_login">Login</button>
          </div>
          <!-- /.col -->
        </div>
      </form>


    </div>
    <!-- /.card-body -->
  </div>
  <!-- /.card -->
</div>
<!-- /.login-box -->

<!-- jQuery -->
<script src="plugins/jquery/jquery.min.js"></script>
<!-- Bootstrap 4 -->
<script src="plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
<!-- AdminLTE App -->
<script src="dist/js/adminlte.min.js"></script>

<!-- SweetAlert2 -->
<script src="plugins/sweetalert2/sweetalert2.min.js"></script>
<!-- Toastr -->
<script src="plugins/toastr/toastr.min.js"></script>
</body>
</html>

<?php
if(isset($_SESSION['status']) && $_SESSION['status_code'] != "") {
  ?>
<script>
  $(function() {
    var Toast = Swal.mixin({
      toast: true,
      position: 'top',
      showConfirmButton: false,
      timer: 3000
    });
      Toast.fire({
        icon: '<?php echo $_SESSION['status_code']; ?>',
        title: '<?php echo $_SESSION['status']; ?>'
      })
    });
</script>
<?php
  unset($_SESSION['status']);
}
?>
