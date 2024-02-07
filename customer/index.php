<?php session_start(); include '../actions/connect.php'; ?>
<!doctype html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="">
    <meta name="author" content="Mark Otto, Jacob Thornton, and Bootstrap contributors">
    <meta name="generator" content="Hugo 0.101.0">
    <title>Menu</title>
    <link href="https://fonts.googleapis.com/css2?family=Sarabun:wght@200&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link href="../css/bootstrap.min.css" rel="stylesheet">

    <!-- Bootstrap core CSS -->



    <style>
      body{
        font-family: 'Sarabun', sans-serif;
        font-size: 100%;
      }
      .bd-placeholder-img {
        font-size: 1.125rem;
        text-anchor: middle;
        -webkit-user-select: none;
        -moz-user-select: none;
        -ms-user-select: none;
        user-select: none;
      }

      @media (min-width: 768px) {
        .bd-placeholder-img-lg {
          font-size: 3.5rem;
        }
      }
    </style>


    <!-- Custom styles for this template -->
    <link href="album.css" rel="stylesheet">
  </head>
  <body>

  <header>
    <div class="navbar navbar-dark bg-dark shadow-sm">
      <div class="container d-flex justify-content-between">
        <a href="#" class="navbar-brand d-flex align-items-center">
          <strong>Menu</strong>
        </a>
      </div>
    </div>
  </header>

  <main role="main">

    <section class="jumbotron text-center" style="background-image: url('img/header.gif');">
      <div class="container">

        <div class="row">
          <div class="col-8">
            <div class="shadow-lg p-3 mb-1 bg-white rounded text-center">
              <input type="text" onkeydown="function_findfood(this.value);" class="form-control form-control-lg" id="colFormLabelLg" placeholder="ค้นหาเมนู">
            </div>

          </div>
          <div class="col-4">
            <div class="shadow-lg p-3 mb-1 bg-white rounded text-center">
              <button type="button" class="btn btn-danger" onclick="function_mycart();">
                <img src="img/cart.png" width="32%" alt="">  <span class="badge badge-light" id="cart_qty"><?php if(isset($_SESSION['cart'])){ echo count($_SESSION['cart']); }else{ echo "0"; } ?></span>
                <span class="sr-only">unread messages</span>
              </button>

            </div>

          </div>
        </div>
      </div>
    </section>
    <div class="album py-5 bg-light">
      <div class="container">
        <div class="shadow-lg p-3 mb-5 bg-white rounded text-center">
          <ul class="nav justify-content-center">
            <?php
            $sql = "SELECT a.*
            FROM producttypes a ";

            $result = $conn->query($sql);

            $data = array();

            if ($result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                    $data[] = $row;?>
                    <li class="nav-item">
                      <a class="nav-link btn-outline-danger" href="#" onclick="function_get_list(<?php echo $row['product_type_id']; ?>);"><?php echo $row['product_type_name']; ?></a>
                    </li><?php
                }
            }

            // Close the database connection


           ?>
          </ul>

        </div>
        <div class="row" id="get_lists">

        </div>
      </div>
    </div>

  </main>

  <footer class="text-muted">
    <div class="container">
      <p class="float-right">
        <a href="#">Back to top</a>
      </p>
    </div>
  </footer>

  <div class="modal fade" id="exampleModal" data-backdrop="static" data-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable modal-xl">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title"> <i class="bi bi-bag-heart-fill"></i> รายการอาหาร</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body" >
          <nav class="nav" style="background-color:#FFF86B; font-size:20px;">
            <a class="nav-link active" href="#"> เช็ครายการอาหาร >  </a>
            <a class="nav-link" href="#">ชื่อลูกค้า ></a>
            <a class="nav-link" href="#">ชื่อโต๊ะ ></a>
            <a class="nav-link" href="#">กด ส่งเลย เพื่อส่งออเดอร ์></a>
          </nav>
          <div class="form-group row " >
            <div class="col-sm-5">
              <br>
              <input type="text" class="form-control form-control-lg" id="input_customer" placeholder="ชื่อลูกค้า" >
              <br>
            </div>
            <div class="col-sm-5">
              <br>
              <select class="form-control form-control-lg" name="input_table" id="input_table" >
                <option value="">==ชื่อโต๊ะ==</option>
                <?php
                $sqlq = "SELECT  * FROM tables";
                $resultt = $conn->query($sqlq);

                if ($resultt->num_rows > 0) {
                    while ($rows = $resultt->fetch_assoc()) { ?>
                          <option value="<?php echo $rows["table_id"]; ?>"> <?php echo $rows["table_number"]; ?> </option>
                    <?php
                    }
                }

                // Close the database connection
                $conn->close();

               ?>
              </select>
              <br>
            </div>
          </div>
          <div class="row row-cols-1 row-cols-md-4" id="list_order_details" style="height:540px;overflow-y: scroll;">
          </div>
          <div class="shadow p-3 mb-5 bg-white rounded"><h2>รวม : <b id="dashboard_Total">0</b> บาท</h2>
        </div>
        <div class="modal-footer" style="background-image: url('img/header.gif');">
          <button type="button" class="btn btn-secondary" data-dismiss="modal">เลือกต่อ</button>
          <button type="button" class="btn btn-primary" onclick="fonction_sendorder();">ส่งเลย</button>
        </div>
      </div>
    </div>
  </div>
  <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@10"></script>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.0/dist/js/bootstrap.min.js"></script>
  <script type="text/javascript">
      $(document).ready(function () {
        function_reload();
      });
      function function_reload() {
        $.ajax({
            url: "actions/orders.php",
            data:{typeaction:'Find_all'},
            type: "post",
            beforeSend: function () {
              var text = '';
              text += '<div class="d-flex align-items-center">';
                text += '<strong>Loading...</strong>';
                text += '<div class="spinner-border ml-auto" role="status" aria-hidden="true"></div>';
              text += '</div>';
              $("#get_lists").html(text);
            },
            success: function (response) {
                if (response.length>0) {
                  var text = '';
                  for (var i = 0; i < response.length; i++) {

                    text += '<div class="col-md-3">';
                      text += '<div class="card mb-4 shadow-sm">';
                        text += '<img  src="../admin/dashboard/actions/'+response[i]['product_image']+'" class="card-img-top" alt="...">';
                        text += '<div class="card-body">';
                          text += '<p class="card-text">'+response[i]['product_name']+' <br> ราคา  '+response[i]['product_price']+' บาท.</p>';
                          text += '<div class="d-flex justify-content-between align-items-center">';
                            text += '<div class="btn-group">';
                              text += '<button type="button" class="btn btn-sm btn-outline-secondary" onclick="function_select('+response[i]['product_id']+');">เลือก</button>';
                            text += '</div>';
                            text += '<small class="text-muted"> </small>';
                          text += '</div>';
                        text += '</div>';
                      text += '</div>';
                   text += '</div>';
                  }
                  $("#get_lists").html(text);
                }
            },
            error: function(data) {
               console.log(data);
            }
        });
      }
      function function_findfood(keyword) {
        $.ajax({
            url: "actions/orders.php",
            data:{typeaction:'Find_list',keyword:keyword},
            type: "post",
            beforeSend: function () {
              var text = '';
              text += '<div class="d-flex align-items-center">';
                text += '<strong>Loading...</strong>';
                text += '<div class="spinner-border ml-auto" role="status" aria-hidden="true"></div>';
              text += '</div>';
              $("#get_lists").html(text);
            },
            success: function (response) {
                if (response.length>0) {
                  var text = '';
                  for (var i = 0; i < response.length; i++) {

                    text += '<div class="col-md-3">';
                      text += '<div class="card mb-4 shadow-sm">';
                        text += '<img  src="../admin/dashboard/actions/'+response[i]['product_image']+'" class="card-img-top" alt="...">';
                        text += '<div class="card-body">';
                          text += '<p class="card-text">'+response[i]['product_name']+' <br> ราคา  '+response[i]['product_price']+' บาท.</p>';
                          text += '<div class="d-flex justify-content-between align-items-center">';
                            text += '<div class="btn-group">';
                              text += '<button type="button" class="btn btn-sm btn-outline-secondary" onclick="function_select('+response[i]['product_id']+');">เลือก</button>';
                            text += '</div>';
                            text += '<small class="text-muted"> </small>';
                          text += '</div>';
                        text += '</div>';
                      text += '</div>';
                   text += '</div>';
                  }
                  $("#get_lists").html(text);
                }
            },
            error: function(data) {
               console.log(data);
            }
        });
      }
      function function_get_list(id) {
        $.ajax({
            url: "actions/orders.php",
            data:{typeaction:'get_list',id:id},
            type: "post",
            beforeSend: function () {
              var text = '';
              text += '<div class="d-flex align-items-center">';
                text += '<strong>Loading...</strong>';
                text += '<div class="spinner-border ml-auto" role="status" aria-hidden="true"></div>';
              text += '</div>';
              $("#get_lists").html(text);
            },
            success: function (response) {
                if (response.length>0) {
                  var text = '';
                  for (var i = 0; i < response.length; i++) {

                    text += '<div class="col-md-3">';
                      text += '<div class="card mb-4 shadow-sm">';
                        text += '<img  src="../admin/dashboard/actions/'+response[i]['product_image']+'" class="card-img-top" alt="...">';
                        text += '<div class="card-body">';
                          text += '<p class="card-text">'+response[i]['product_name']+' <br> ราคา  '+response[i]['product_price']+' บาท.</p>';
                          text += '<div class="d-flex justify-content-between align-items-center">';
                            text += '<div class="btn-group">';
                              text += '<button type="button" class="btn btn-sm btn-outline-secondary" onclick="function_select('+response[i]['product_id']+');">เลือก</button>';
                            text += '</div>';
                            text += '<small class="text-muted"> </small>';
                          text += '</div>';
                        text += '</div>';
                      text += '</div>';
                   text += '</div>';
                  }
                  $("#get_lists").html(text);
                }
            },
            error: function(data) {
               console.log(data);
            }
        });
      }

      function function_select(prd_id) {
        $.ajax({
            url: "actions/orders.php",
            data:{typeaction:'add_to_cart',prd_id:prd_id},
            type: "post",
            beforeSend: function () {
              var text = '';
              text += '<div class="d-flex align-items-center">';
                text += '<div class="spinner-border ml-auto" role="status" aria-hidden="true"></div>';
              text += '</div>';
              $("#cart_qty").html(text);
            },
            success: function (response) {
              Swal.fire('เพิ่มรายการเรียบร้อยแล้ว 🍒🍨');
              $("#cart_qty").html(response);
            },
            error: function(data) {
               console.log(data);
            }
        });
      }

      function function_mycart() {
        $("#exampleModal").modal("show");
        update_cart();
      }
      function update_cart() {
        $.ajax({
            url: "actions/orders.php",
            data:{typeaction:'my_cart'},
            type: "post",
            beforeSend: function () {
              var text = '';
              text += '<div class="d-flex align-items-center">';
                text += '<div class="spinner-border ml-auto" role="status" aria-hidden="true"></div>';
              text += '</div>';
              $("#list_order_details").html(text);
            },
            success: function (response) {
              if (response.length>0) {
                var text ='';
                var dashboard_Total = 0;
                for (var i = 0; i < response.length; i++) {
                  //response[i]['order_id']
                  var totala =  Intl.NumberFormat('th-TH', {
                      style: 'currency',
                      currency: 'THB',
                    }).format((response[i]['quantity'] * response[i]['total_price']));
                  text += '<div class="col mb-3">';
                    text += '<div class="card">';
                      text += '<img  src="../admin/dashboard/actions/'+response[i]['product_image']+'" class="card-img-top" alt="...">';
                      text += '<div class="card-body">';
                        text += '<h6 class="card-title">'+response[i]['product_name']+'</h6>';
                        text += '<input type="number" id="detailQty'+response[i]['product_id']+'" value="'+response[i]['quantity']+'" class="form-control form-control-sm" placeholder="quantity">  ';
                        text += '<br>รวม : '+totala+'  ';
                      text += '</div>';
                      text += '<div class="card-footer">';
                       text += '<div class="btn-group btn-group-sm" role="group"  ><button type="button" onclick="fonction_updatedetail('+response[i]['product_id']+');" class="btn btn-warning btn-sm" >แก้ไขจำนวน</button>';
                       text += ' <button type="button" onclick="fonction_delete('+response[i]['product_id']+');" class="btn btn-danger btn-sm" >ลบ</button>';
                      text += '</div></div>';
                    text += '</div>';
                  text += '</div>';
                  dashboard_Total +=( response[i]['quantity'] * response[i]['total_price']);
                }
                var total =  Intl.NumberFormat('th-TH', {
                    style: 'currency',
                    currency: 'THB',
                  }).format(dashboard_Total);

                $("#dashboard_Total").html(total);
                $("#list_order_details").html(text);
              }

            },
            error: function(data) {
               console.log(data);
            }
        });
      }
      function fonction_updatedetail(product_id) {
        var detailQty = $("#detailQty"+product_id).val();
        $.ajax({
            url: "actions/orders.php",
            data:{typeaction:'update_cart',product_id:product_id,detailQty:detailQty},
            type: "post",
            success: function (response) {
              update_cart();
            },
            error: function(data) {
               console.log(data);
            }
        });
      }
      function fonction_sendorder() {
        if ($("#input_customer").val()=="") {
          Swal.fire('ระบุชื่อลูกค้า 🍒🍨');
        }else if ($("#input_table").val()=="") {
          Swal.fire('เลือกโต๊ะ 🍒🍨' );
        }else {
          Swal.fire({
           title: 'ตรวจสอบรายการอาหาร แลกดยืนยันรายการอีกครั้ง ขอบคุณค่ะ',
           confirmButtonText: 'ยืนยัน',
           showCancelButton: true,
           cancelButtonText: 'ยังก่อน',
           }).then((result) => {
             if (result.isConfirmed) {
               var input_customer = $("#input_customer").val();
               var input_table = $("#input_table").val();
               $.ajax({
                   url: "actions/orders.php",
                   data:{typeaction:'send_order',input_customer:input_customer,input_table:input_table},
                   type: "post",
                   success: function (response) {
                       Swal.fire('ส่งรายการอาหารเรียบร้อย 🍒🍨' + input_customer );
                       setTimeout(updateCountdown, 3000);
                   },
                   error: function(data) {
                      console.log(data);
                   }
               });
             }
           });
        }

      }

      function updateCountdown() {
        location.reload();
      }


      function fonction_delete(product_id) {
        $.ajax({
            url: "actions/orders.php",
            data:{typeaction:'update_del_cart',product_id:product_id},
            type: "post",
            success: function (response) {
              if (response=='0') {
                Swal.fire('รีโหลดเพื่อเริ่มรายการใหม่ 🍒🍨');
                setTimeout(updateCountdown, 2000);
              }
              update_cart();
            },
            error: function(data) {
               console.log(data);
            }
        });
      }
  </script>
  </body>
</html>
