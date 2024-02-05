<?php include '../checklogin.php'; ?>
<?php
  include '../../../actions/connect.php';
  //check type actions
  if (isset($_POST["typeaction"])) {
    $typeaction = trim($_POST["typeaction"]);
  }else {
    //redirect
    $typeaction = "redie";
    exit();
  }
  if ($typeaction =="select") {
    // Select data from the Products table
    $sql = "SELECT a.*,b.*
    FROM orders a
    LEFT JOIN order_detail b ON a.order_id = b.order_id ";

    $result = $conn->query($sql);

    $data = array();

    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $data[] = $row;
        }
    }

    // Close the database connection
    $conn->close();

    // Return the data as JSON
    header('Content-Type: application/json');
    echo json_encode($data);
  }elseif ($typeaction =="get_realtime_data") {

    // Select data from the Products table
    $sql = "SELECT a.order_id, a.customer_name, c.table_number , a.order_status
            FROM orders a
            INNER JOIN order_detail b ON a.order_id = b.order_id
            INNER JOIN tables c ON b.table_id = c.table_id
            WHERE a.order_read = '1'    GROUP BY a.order_id, a.customer_name, c.table_number";

    $result = $conn->query($sql);

    $data = array();

    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $data[] = $row;
        }
        $update_sql = "UPDATE orders SET
                        order_read ='0'
                       WHERE order_read ='1'";
        if ($conn->query($update_sql) === TRUE) { }else { }
    }

    // Close the database connection
    $conn->close();

    // Return the data as JSON
    header('Content-Type: application/json');
    echo json_encode($data);
  }elseif ($typeaction =="get_realtime_dataold") {
    // Select data from the Products table
    $sql = "SELECT a.order_id, a.customer_name, c.table_number , a.order_status
            FROM orders a
            INNER JOIN order_detail b ON a.order_id = b.order_id
            INNER JOIN tables c ON b.table_id = c.table_id
            WHERE a.order_read = '0'   GROUP BY a.order_id, a.customer_name, c.table_number ";

    $result = $conn->query($sql);

    $data = array();

    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $data[] = $row;
        }
    }

    // Close the database connection
    $conn->close();

    // Return the data as JSON
    header('Content-Type: application/json');
    echo json_encode($data);
  }elseif ($typeaction =="get_order_detail") {
    $id = $_POST["id"];
    $sql = "SELECT a.order_id,b.order_detail_id ,a.customer_name,a.order_date,b.product_name,b.quantity,b.total_price ,c.table_number,e.product_image
            FROM orders a
            INNER JOIN order_detail b ON a.order_id = b.order_id
            INNER JOIN products e ON b.product_id = e.product_id
            INNER JOIN tables c ON b.table_id = c.table_id
            WHERE a.order_id = '$id';   ";

    $result = $conn->query($sql);

    $data = array();

    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $data[] = $row;
        }
    }

    // Close the database connection
    $conn->close();

    // Return the data as JSON
    header('Content-Type: application/json');
    echo json_encode($data);
  }elseif ($typeaction =="get_update_order") {
    $id = $_POST["id"];
    $status = $_POST["status"];
    $update_sql = "UPDATE orders SET
                    order_status ='$status'
                   WHERE order_id ='$id'";
    if ($conn->query($update_sql) === TRUE) { }else { }
    $conn->close();
  }elseif ($typeaction=="update_order_detail") {
    $order_detail_id = $_POST["order_detail_id"];
    $detailQty = $_POST["detailQty"];
    $update_sql = "UPDATE order_detail SET
                      quantity ='$detailQty'
                   WHERE order_detail_id  ='$order_detail_id'";
    if ($conn->query($update_sql) === TRUE) { }else { }
    $conn->close();
  }
?>
