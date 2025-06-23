<?php

ob_start();

include_once ('connectdb.php');
session_start();

if ($_SESSION['role']=="user") {
    include('headeruser.php');
}elseif ($_SESSION['role']=="admin") {
    include('header.php');
}else{
    header('location: ../index.php');
}




ob_end_flush();;

function fill_product($pdo){
  $output = '';
  $select = $pdo->prepare("SELECT * from tbl_product order by product asc");

  $select->execute();

  $result = $select->fetchAll();
  foreach($result as $row){
    $output .='<option value="'.$row["product_id"].'">'.$row["product"].'</option>';
  }
  return $output;
}


$select  = $pdo->prepare("SELECT * from tbl_taxdis where taxdis_id = 1");

$select->execute();
$row = $select->fetch(PDO::FETCH_OBJ);


if (isset($_POST['btn_save_order'])){
  //check if the table is empty
  if (empty($_POST['pid_arr'])){
    $_SESSION['status']="Please Choose Product";
    $_SESSION['status_code'] = "error";
  }else{
    $subtotal = $_POST['txt_subtotal'];
    $discount = $_POST['txt_discount'];
    $sgst     = $_POST['txt_sgst'];
    $cgst     = $_POST['txt_cgst'];
    $total    = $_POST['txt_total'];
    $due      = $_POST['txt_due'];
    $paid     = $_POST['txt_paid'];
    $order_date = date("Y-m-d");
    $payment_type = $_POST['r3'];

    $arr_pid     = $_POST['pid_arr'];
    $arr_barcode = $_POST['barcode_arr'];
    $arr_name    = $_POST['product_arr'];
    $arr_stock   = $_POST['stock_c_arr'];
    $arr_qty     = $_POST['quantity_arr'];
    $arr_price   = $_POST['price_c_arr'];
    $arr_total    = $_POST['total_c_arr'];

    $insert = $pdo->prepare("insert into tbl_invoice( order_date, subtotal, discount, sgst, cgst, total, payment_type, due, paid) values( :order_date, :subtotal, :discount, :sgst, :cgst, :total, :payment_type ,:due, :paid)");
    $insert->bindParam(':order_date', $order_date);
    $insert->bindParam(':subtotal', $subtotal);
    $insert->bindParam(':discount', $discount);
    $insert->bindParam(':sgst', $sgst);
    $insert->bindParam(':cgst', $cgst);
    $insert->bindParam(':total', $total);
    $insert->bindParam(':payment_type', $payment_type);
    $insert->bindParam(':due', $due);
    $insert->bindParam(':paid', $paid);

    $insert->execute();

    $invoice_id = $pdo->lastInsertId();
    if($insert != NULL){
      for($i=0; $i<count($arr_pid); $i++){
        $rem_qty = $arr_stock[$i] - $arr_qty[$i];
        if($rem_qty < 0){
          return "Order is not complete!";
        }else{

          $update = $pdo->prepare("update tbl_product set stock =:stock where product_id =:product_id");
          $update->bindParam(':product_id', $arr_pid[$i]);
          $update->bindParam(':stock', $rem_qty);
          $update->execute();

        }
        $product_net_price = $arr_price[$i] * $arr_qty[$i];
        $insert = $pdo->prepare("insert into tbl_invoice_details(invoice_id, barcode, product_id, product_name, qty, rate, saleprice, order_date) values (:invoice_id, :barcode, :product_id, :product_name, :qty, :rate, :saleprice, :order_date)");
        $insert->bindParam(':invoice_id', $invoice_id);
        $insert->bindParam(':barcode', $arr_barcode[$i]);
        $insert->bindParam(':product_id', $arr_pid[$i]);
        $insert->bindParam(':product_name', $arr_name[$i]);
        $insert->bindParam(':qty', $arr_qty[$i]);
        $insert->bindParam(':rate', $arr_price[$i]);
        $insert->bindParam(':saleprice', $product_net_price);
        $insert->bindParam(':order_date', $order_date);

        if(!$insert->execute()){
          print_r($insert->errorInfo());
        }else{
          $_SESSION['status']="Order Added Successfully!";
          $_SESSION['status_code'] = "success";
        }
      }
    }

  }

}


?>

<style type="text/css">
  .tableFixHead{
    overflow: scroll;
    height: 520px;
  }
  .tableFixHead thead tr{
    position: sticky;
    top: 0;
    z-index: 1;

    table {
      border-collapse: collapse;
      width: 100px;
    }
    th, td {
      padding: 8px 16px;
    }
    th{
      background: #eee;
    }
  }

</style>


<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
  <!-- Content Header (Page header) -->
  <div class="content-header">
    <div class="container-fluid">
      <div class="row mb-2">
        <div class="col-sm-6">
          <h1 class="m-0">Point of Sale</h1>
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
              <h5 class="m-0">POS</h5>
            </div>

            <div class="card-body">
              <div class="row">
                <div class="col-md-8">
                  <div class="input-group mb-3">
                    <div class="input-group-prepend">
                      <span class="input-group-text"><i class="fa fa-barcode"></i> </span>
                    </div>
                    <input type="text" class="form-control" id="txt_barcode_id" placeholder="Scan Barcode" autofocus>
                  </div>
                  <form action="" method="POST" name="">
                  <select class="form-control select2" data-dropdown-css-class="select2-purple" style="width: 100%; margin-bottom: 10px" id="select2_select">
                    <option selected disabled >Select Or Search</option>
                    <?php echo fill_product($pdo); ?>
                  </select>
                  <br>
                  <div class="tableFixHead">
                    <table id="product_table" class="table table-bordered table-hover">
                      <thead>
                      <tr>
                        <th>Product</th>
                        <th>Stock</th>
                        <th>Price</th>
                        <th>Quantity</th>
                        <th>Total</th>
                        <th>Del</th>
                      </tr>
                      </thead>
                      <tbody class="details" id="item_table"  >
<!--                        <tr data-widget="expandable-table" aria-expanded="false">-->
<!---->
<!--                        </tr>-->
                      </tbody>
                    </table>
                  </div>

                </div>
                <div class="col-md-4">
                  <div class="input-group">
                    <div class="input-group-prepend">
                      <span class="input-group-text" >SUBTOTAL</span>
                    </div>
                    <input type="text" class="form-control" id="txtsubtotal_id" name="txt_subtotal" readonly>
                    <div class="input-group-append">
                      <span class="input-group-text">$</span>
                    </div>
                  </div>
                  <div class="input-group">
                    <div class="input-group-prepend">
                      <span class="input-group-text"  >DISCOUNT</span>
                    </div>
                    <input type="text" class="form-control" value="<?php echo $row->discount; ?>" id="txtdiscount_p" name="txt_discount" required>
                    <div class="input-group-append">
                      <span class="input-group-text">%</span>
                    </div>
                  </div>
                  <div class="input-group">
                    <div class="input-group-prepend">
                      <span class="input-group-text" >DISCOUNT</span>
                    </div>
                    <input type="text" class="form-control" readonly id="txtdiscount_n">
                    <div class="input-group-append">
                      <span class="input-group-text">$</span>
                    </div>
                  </div>
                  <div class="input-group">
                    <div class="input-group-prepend">
                      <span class="input-group-text" >SGST(%)</span>
                    </div>
                    <input type="text" class="form-control" value="<?php echo $row->sgst; ?>"  id="txtsgst_id_p" name="txt_sgst" readonly>
                    <div class="input-group-append">
                      <span class="input-group-text">%</span>
                    </div>
                  </div>
                  <div class="input-group">
                    <div class="input-group-prepend">
                      <span class="input-group-text" >CGST(%)</span>
                    </div>
                    <input type="text" class="form-control" value="<?php echo $row->cgst; ?>" id="txtcgst_id_p" name="txt_cgst" readonly>
                    <div class="input-group-append">
                      <span class="input-group-text">%</span>
                    </div>
                  </div>
                  <div class="input-group">
                    <div class="input-group-prepend">
                      <span class="input-group-text" >SGST( $ )</span>
                    </div>
                    <input type="text" class="form-control"  id="txtsgst_id_n"readonly>
                    <div class="input-group-append">
                      <span class="input-group-text">$</span>
                    </div>
                  </div>
                  <div class="input-group">
                    <div class="input-group-prepend">
                      <span class="input-group-text" >CGST( $ )</span>
                    </div>
                    <input type="text" class="form-control" id="txtcgst_id_n" readonly>
                    <div class="input-group-append">
                      <span class="input-group-text" >$</span>
                    </div>
                  </div>

                  <hr style="height: 2px; border-width: 0; color: black; background-color: black;">

                  <div class="input-group">
                    <div class="input-group-prepend">
                      <span class="input-group-text" >TOTAL</span>
                    </div>
                    <input type="text" class="form-control form-control-lg total" id="txttotal" name="txt_total" readonly>
                    <div class="input-group-append">
                      <span class="input-group-text">$</span>
                    </div>
                  </div>

                  <hr style="height: 2px; border-width: 0; color: black; background-color: black;">

                  <div class="icheck-success d-inline">
                    <input type="radio" name="r3" checked id="radioSuccess1" value="CASH">
                    <label for="radioSuccess1">
                      CASH
                    </label>
                  </div>
                  <div class="icheck-primary d-inline">
                    <input type="radio" name="r3" id="radioSuccess2" value="KHQR">
                    <label for="radioSuccess2">
                        <a target="_blank" href="#" id="khqr_link">KHQR</a>
                    </label>
                  </div>


                  <hr style="height: 2px; border-width: 0; color: black; background-color: black;">
                  <div class="input-group">
                    <div class="input-group-prepend">
                      <span class="input-group-text" >DUE</span>
                    </div>
                    <input type="text" class="form-control form-control-lg " id="txtdue" name="txt_due" readonly>
                    <div class="input-group-append">
                      <span class="input-group-text">$</span>
                    </div>
                  </div>
                  <div class="input-group">
                    <div class="input-group-prepend">
                      <span class="input-group-text" >PAID</span>
                    </div>
                    <input type="text" class="form-control form-control-lg " id="txtpaid" name="txt_paid" required >
                    <div class="input-group-append">
                      <span class="input-group-text">$</span>
                    </div>
                  </div>
                  <hr style="height: 2px; border-width: 0; color: black; background-color: black;">
                  <div class="text-center">
                    <input type="submit" class="btn btn-primary" value="Save Order" name="btn_save_order">
                  </div>

                </div>
              </div>
            </div>
            </form>
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

    // Update KHQR link when total changes
    function updateKhqrLink() {
        var total = $("#txttotal").val();
        if (total) {
            //$("#khqr_link").attr("href", "your aba payway link" + total +"&dynamic=true");
            $("#khqr_link").attr("href", "#"); //Change it to your aba payway link if you want to apply aba KHQR
        }
    }


    $(function(){
      //Initialize Select2 Elements
      $('#select2_select').select2({
        theme: 'bootstrap4'
      })

      $("#txtpaid").val("0.00");
  });

//start calculate function
  function calculate(dis=0, paid_amount=0){
    var subtotal = 0;
    var discount = dis;
    var sgst = 0;
    var cgst = 0;
    var total = 0;
    var paid_amt = paid_amount;
    var due = 0;

    // Fix: Use the correct selector for total amounts
    $(".totalamt").each(function (){
      subtotal += parseFloat($(this).text()) || 0;
    });

    $("#txtsubtotal_id").val(subtotal.toFixed(2));

    sgst = parseFloat($("#txtsgst_id_p").val());
    cgst = parseFloat($("#txtcgst_id_p").val());
    discount = parseFloat($("#txtdiscount_p").val());

    sgst = sgst/100;
    sgst = sgst*subtotal;

    cgst = cgst/100;
    cgst = cgst*subtotal;

    discount = discount/100;
    discount = discount*subtotal;

    $("#txtsgst_id_n").val(sgst.toFixed(2));
    $("#txtcgst_id_n").val(cgst.toFixed(2));
    $("#txtdiscount_n").val(discount.toFixed(2));

    total = sgst + cgst + subtotal - discount;
    due = total - paid_amt;

    $("#txttotal").val(total.toFixed(2));
    $("#txtdue").val(due.toFixed(2));

    updateKhqrLink();

  } //end calculate function

  //start addrow function
  function addrow(pid, product, saleprice, stock, barcode) {
    var tr = '<tr>' +
      '<input type="hidden" class="form-control total_c" name="barcode_arr[]" value="' + barcode + '" >'+
      '<input type="hidden" class="form-control total_c" name="product_arr[]" value="' + product + '" >'+
      '<td style="text-align: left; vertical-align: middle; font-size: 17px;">' +
      '<span class="badge badge-dark">' + product + '</span>' +
      '<input type="hidden" class="form-control pid" name="pid_arr[]" value="' + pid + '">' +
      '</td>' +
      '<td style="text-align: left; vertical-align: middle; font-size: 17px;">' +
      '<span class="badge badge-primary  stocklbl" name="stock_arr[]" id="stock_id' + pid + '">' + stock + '</span>' +
      '<input type="hidden" class="form-control stock_c" name="stock_c_arr[]" value="' + stock + '">' +
      '</td>' +
      '<td style="text-align: left; vertical-align: middle; font-size: 17px;">' +
      '<span class="badge badge-warning price" name="price_arr[]" id="price_id' + pid + '">' + saleprice + '</span>' +
      '<input type="hidden" class="form-control price_c" name="price_c_arr[]" value="' + saleprice + '">' +
      '</td>' +
      '<td>' +
      '<input type="text" class="form-control qty" name="quantity_arr[]" id="qty_id' + pid + '" value="1" size="1">' +
      '</td>' +
      '<td style="text-align: left; vertical-align: middle; font-size: 17px;">' +
      '<span class="badge badge-success totalamt" name="netamt_arr[]" id="saleprice_id' + pid + '">' + saleprice + '</span>' +
      '<input type="hidden" class="form-control total_c" name="total_c_arr[]" value="' + saleprice + '">' +
      '</td>' +
      '<td>' +
      '<button class="btn btn-danger btn-sm delete-btn " data-id="' + pid + '"><span class="fa fa-trash" ></span></button>' +
      '</td>' +
      '</tr>';

    $('.details').append(tr);
  }
  //end addrow function

  //catch if the DISCOUNT or PAID is changed

  $("#txtdiscount_p").keyup(function(){

    var discount = $(this).val();
    calculate(discount);

  })

  $("#txtpaid").keyup(function(){

    var discount = $("#txtdiscount_p").val();
    var paid = $(this).val();

    calculate(discount, paid);

  })

  //remove row if the delete button is clicked
  $(document).on('click', ".delete-btn", function (){

    var removed = $(this).attr("data-id");
    productarr = jQuery.grep(productarr, function (value){
      return value !== removed;
    });

    $(this).closest('tr').remove();
      calculate();
  });

  var productarr = [];

  //for barcode search bar
  $(function (){
    $('#txt_barcode_id').on('change',function (){

      var barcode = $('#txt_barcode_id').val();

      $.ajax({
        url : 'getproduct.php',
        method: "GET",
        datatype: "json",
        data: {
          id:barcode //user can enter barcode or product id to get the result, check getproduct.php for better understanding
        },
        success: function (data){

          // First check if product data is valid
          if (!data || !data.product_id || data.product_id === 'undefined') {
            Swal.fire("Error!", "Product not found", "error");
            $("#txt_barcode_id").val("").focus();
            return; // Exit the function
          }

          if(jQuery.inArray(data['product_id'], productarr) !== -1){
            var actualqty = parseInt($('#qty_id' + data['product_id']).val()) + 1;
            $('#qty_id' + data['product_id']).val(actualqty);

            var saleprice = parseInt(actualqty) * data['sale_price'];

            $('#saleprice_id' + data['product_id']).html(saleprice);
            $('#saleprice_idd' + data['product_id']).html(saleprice);

            $('#txt_barcode_id').val("");
            calculate();
          }else{

            addrow(data['product_id'], data['product'], data['sale_price'], data['stock'], data['barcode']);

            productarr.push(data['product_id']);

            $("#txt_barcode_id").val("");

          }

        }
      });
    });

    $("#item_table").delegate(".qty", "keyup change", function(){

      var quantity = $(this);
      var tr = $(this).parent().parent();

      if ((quantity.val()-0)>(tr.find(".stock_c").val() - 0)){
        Swal.fire("WARNING!", "Sorry! This much of quantity is not available", "warning");
        quantity.val(1);

        tr.find(".totalamt").text(quantity.val() * tr.find(".price").text());
        tr.find(".saleprice").text(quantity.val() * tr.find(".price").text());
      }else{
        tr.find(".totalamt").text(quantity.val() * tr.find(".price").text());
        tr.find(".saleprice").text(quantity.val() * tr.find(".price").text());
      }
      calculate();
    });

  });


  //for select options
  $(function (){
    $('.select2').on('change',function (){

      var productid = $('.select2').val();

      $.ajax({
        url : 'getproduct.php',
        method: "GET",
        datatype: "json",
        data: {
          id:productid //user can enter barcode or product id to get the result, check getproduct.php for better understanding
        },
        success: function (data){

          if(jQuery.inArray(data['product_id'], productarr) !== -1){
            var actualqty = parseInt($('#qty_id' + data['product_id']).val()) + 1;
            $('#qty_id' + data['product_id']).val(actualqty);

            var saleprice = parseInt(actualqty) * data['sale_price'];

            $('#saleprice_id' + data['product_id']).html(saleprice);
            $('#saleprice_idd' + data['product_id']).html(saleprice);


            $('#txt_barcode_id').val("");
          }else{

            addrow(data['product_id'], data['product'], data['sale_price'], data['stock'], data['barcode']);

            productarr.push(data['product_id']);

            $("#txt_barcode_id").val("");
            calculate();

          }

        }
      });
    });

    $("#item_table").delegate(".qty", "keyup change", function(){

      var quantity = $(this);
      var tr = $(this).parent().parent();

      if ((quantity.val()-0)>(tr.find(".stock_c").val() - 0)){
        Swal.fire("WARNING!", "Sorry! This much of quantity is not available", "warning");
        quantity.val(1);

        tr.find(".totalamt").text(quantity.val() * tr.find(".price").text());
        tr.find(".saleprice").text(quantity.val() * tr.find(".price").text());
      }else{
        tr.find(".totalamt").text(quantity.val() * tr.find(".price").text());
        tr.find(".saleprice").text(quantity.val() * tr.find(".price").text());
      }
      calculate();
    });


  });

  //disable enter key in PAID to prevent error
  $("#txtpaid").keypress(function (e){
    if(e.which === 13) return false;
  });

  //disable enter key in DISCOUNT to prevent error
  $("#txtdiscount_p").keypress(function (e){
    if(e.which === 13) return false;
  });

</script>

<?php if (isset($_SESSION['status']) && isset($_SESSION['status_code'])): ?>
  <script>
    Swal.fire({
      icon: '<?php echo $_SESSION['status_code']; ?>',
      title: '<?php echo $_SESSION['status']; ?>'
    });
  </script>
  <?php
  unset($_SESSION['status']);
  unset($_SESSION['status_code']);
  ?>
<?php endif; ?>
