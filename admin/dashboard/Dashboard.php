
<br>
<h2>Order Dashboard</h2> <button type="button" class="btn btn-primary btn-sm" onclick="function_list_orders();">Reload</button><hr>
<div class="container-fluid">
  <div class="row">

    <div class="col-sm-3" style="height:540px;overflow-y: scroll;" id="list_orders">



    </div>
    <div class="col-sm-9"  >
      <div class="row row-cols-1 row-cols-md-4" id="list_order_details" style="height:540px;overflow-y: scroll;">
      </div>

      <div class="shadow p-3 mb-5 bg-white rounded"><h2>Total : <b id="dashboard_Total">0</b></h2>
        <input type="hidden" id="order_detail_id" >
        <button type="button" class="btn btn-warning" onclick="update_status_order('warning');">hold</button>
        <button type="button" class="btn btn-success" onclick="update_status_order('success');">Done</button></div>

    </div>
  </div>

</div>
<script type="text/javascript">
  $(document).ready(function () {

    function_reloaddataold();
    setInterval(function_reloaddata, 30000);
  });
  function function_list_orders() {
    $("#order_detail_id").val("");
    $("#dashboard_Total").html("");
    $("#list_order_details").html("");
    function_reloaddata();
  }
  function function_reloaddata() {
    $.ajax({
        url: "actions/orders.php",
        data:{typeaction:'get_realtime_data'},
        headers: {'X-Requested-With': 'XMLHttpRequest'},
        type: "post",
        success: function (response) {
          if (response.length>0) {
            for (var i = 0; i < response.length; i++) {
              $("#list_orders").prepend('<div id="divOrder'+response[i]['order_id']+'" class="shadow p-3 mb-1 bg-'+response[i]['order_status']+' rounded" onclick="fonction_opendetail('+response[i]['order_id']+');">'+response[i]['customer_name']+'</div>');
            }
          }
        },
        error: function(data) {
           console.log(data);
        }
    });
  }
  function function_reloaddataold() {
    $.ajax({
        url: "actions/orders.php",
        data:{typeaction:'get_realtime_dataold'},
        headers: {'X-Requested-With': 'XMLHttpRequest'},
        type: "post",
        success: function (response) {
          if (response.length>0) {
            for (var i = 0; i < response.length; i++) {
              $("#list_orders").append('<div id="divOrder'+response[i]['order_id']+'" class="shadow-sm p-3 mb-1 bg-'+response[i]['order_status']+' rounded" onclick="fonction_opendetail('+response[i]['order_id']+');">'+response[i]['customer_name']+'</div>');
            }
          }
        },
        error: function(data) {
           console.log(data);
        }
    });
  }
  function fonction_opendetail(id) {
    $("#order_detail_id").val(id);
    $.ajax({
        url: "actions/orders.php",
        data:{typeaction:'get_order_detail',id:id},
        type: "post",
        success: function (response) {
          $("#list_order_details").html("");
          if (response.length>0) {
            var text ='';
            var dashboard_Total = 0;
            for (var i = 0; i < response.length; i++) {
              //response[i]['order_id']
              text += '<div class="col mb-3">';
                text += '<div class="card">';
                  text += '<img  src="actions/'+response[i]['product_image']+'" class="card-img-top" alt="...">';
                  text += '<div class="card-body">';
                    text += '<h6 class="card-title">'+response[i]['product_name']+'</h6>';
                    text += '<input type="number" id="detailQty'+response[i]['order_detail_id']+'" value="'+response[i]['quantity']+'" class="form-control form-control-sm" placeholder="quantity">  ';
                    text += '<br>total price : '+(response[i]['quantity'] * response[i]['total_price'])+'  ';
                    text += '<br><button type="button" onclick="fonction_updatedetail('+response[i]['order_id']+','+response[i]['order_detail_id']+');" class="btn btn-secondary btn-sm" >update</button>';
                  text += '</div>';
                text += '</div>';
              text += '</div>';
              dashboard_Total +=( response[i]['quantity'] * response[i]['total_price']);
            }
            $("#dashboard_Total").html(dashboard_Total);
            $("#list_order_details").html(text);
          }
        },
        error: function(data) {
           console.log(data);
        }
    });
  }
  function update_status_order(status) {
    var id =  $("#order_detail_id").val();
    $.ajax({
        url: "actions/orders.php",
        data:{typeaction:'get_update_order',id:id,status:status},
        type: "post",
        success: function (response) {
          $("#divOrder"+id).removeClass('bg-warning bg-success');
          $("#divOrder"+id).addClass("bg-"+status);
        },
        error: function(data) {
           console.log(data);
        }
    });
  }
  function fonction_updatedetail(order_id,order_detail_id) {
    var detailQty = $("#detailQty"+order_detail_id).val();
    $.ajax({
        url: "actions/orders.php",
        data:{typeaction:'update_order_detail',order_id:order_id,order_detail_id:order_detail_id,detailQty:detailQty},
        type: "post",
        success: function (response) {
          fonction_opendetail(order_id);
        },
        error: function(data) {
           console.log(data);
        }
    });
  }
</script>
