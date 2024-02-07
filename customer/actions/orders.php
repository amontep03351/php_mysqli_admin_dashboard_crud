<?php
  session_start();
  include '../../actions/connect.php';
  //check type actions
  if (isset($_POST["typeaction"])) {
    $typeaction = trim($_POST["typeaction"]);
  }else {
    //redirect
    $typeaction = "redie";
    exit();
  }
  if ($typeaction =="get_list") {
    $id = $_POST["id"];
    $sql = "SELECT a.*
    FROM products a WHERE product_type_id ='$id' ";

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
  }elseif ($typeaction =="Find_list") {
    $keyword = $_POST["keyword"];
    $sql = "SELECT a.*
    FROM products a WHERE product_name  LIKE '%$keyword%' ";

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
  }elseif ($typeaction =="Find_all") {
    $sql = "SELECT a.*
    FROM products a WHERE product_status ='1' ";

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
  }elseif ($typeaction =="add_to_cart") {
    $prd_id = $_POST["prd_id"];
    if (isset($_SESSION['cart'])) {
      if (isset($_SESSION['cart'][$prd_id])) {
        $_SESSION['cart'][$prd_id] = $_SESSION['cart'][$prd_id] + 1;

      }else {
        $_SESSION['cart'][$prd_id] = 0;
        $_SESSION['cart'][$prd_id] = $_SESSION['cart'][$prd_id] + 1;
      }

      echo count($_SESSION['cart']);
    }else {
      $_SESSION['cart'] = array();
      if (isset($_SESSION['cart'][$prd_id])) {
        $_SESSION['cart'][$prd_id] = $_SESSION['cart'][$prd_id] + 1;

      }else {
        $_SESSION['cart'][$prd_id] = 0;
        $_SESSION['cart'][$prd_id] = $_SESSION['cart'][$prd_id] + 1;
      }

      echo count($_SESSION['cart']);
    }
  }elseif ($typeaction =="my_cart") {
     $data= [];
     if (isset($_SESSION['cart'])) {
        if (count($_SESSION['cart'])>=1) {
          $pd_id = "('".implode("','",array_keys($_SESSION['cart']))."')";
          $sql = "SELECT a.*
          FROM products a WHERE product_id IN $pd_id ";

          $result = $conn->query($sql);

          $data = array();

          if ($result->num_rows > 0) {
              while ($row = $result->fetch_assoc()) {
                  if (isset($_SESSION['cart'][$row['product_id']])) {
                      $data[] = array(
                        'order_id'=>0,
                        'order_detail_id'=>0,
                        'product_id' => $row['product_id'],
                        'product_name' => $row['product_name'],
                        'quantity' => $_SESSION['cart'][$row['product_id']],
                        'total_price' =>$row['product_price'] ,
                        'product_image' =>$row['product_image']
                      );
                  }
              }
          }
          // Close the database connection
          $conn->close();
        }else {
          $data= [];
        }

    }else {
       $data= [];
    }
    header('Content-Type: application/json');
    echo json_encode($data);
  }elseif ($typeaction =="update_cart") {
    $detailQty = $_POST["detailQty"];
    $product_id = $_POST["product_id"];
    $_SESSION['cart'][$product_id] = $detailQty;
  }elseif ($typeaction =="update_del_cart") {
    $product_id = $_POST["product_id"];
    unset($_SESSION['cart'][$product_id]);
    if (count($_SESSION['cart'])==0) {
      echo "0";
    }
  }elseif($typeaction =="send_order") {
    // $_SESSION['cart'] = [];
    $product_id = '';
    $product_name = '';
    $customer_name =  $_POST["input_customer"];
    $table_id  = $_POST["input_table"];
    $order_read =1;
    $quantity = 0;
    $total_price= 0;
    $order_status = 'warning';
    $price = array();
    $data = array();
    $pd_id = "('".implode("','",array_keys($_SESSION['cart']))."')";
    $sql = "SELECT a.product_id,a.product_name,a.product_price
    FROM products a WHERE product_id IN $pd_id ";

    $result = $conn->query($sql);

    $data = array();

    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $data[$row['product_id']] = $row['product_name'];
            $price[$row['product_id']] = $row['product_price'];
        }
    }

    // SQL query to insert data
    $sql = "INSERT INTO orders (customer_name, order_read, order_status) VALUES ('$customer_name', '$order_read','$order_status')";
   
    // Execute query
    if ($conn->query($sql) === TRUE) {
        // Get the last inserted ID
        $last_inserted_id = $conn->insert_id;
        foreach ($_SESSION['cart'] as $key => $value) {
          $product_id = trim($key);
          $product_name = $data[$product_id];
          $quantity = $value;
          $total_price = $price[$product_id];
          $sqlorder_detail = "INSERT INTO order_detail (order_id,table_id ,product_id,product_name,quantity,total_price) VALUES ('$last_inserted_id', '$table_id','$product_id','$product_name','$quantity','$total_price')";
          if ($conn->query($sqlorder_detail) === TRUE) {

          }else {
            echo "Error: " . $sqlorder_detail . "<br>" . $conn->error;
          }
        }
        $_SESSION['cart'] = [];
    } else {
        echo "Error: " . $sql . "<br>" . $conn->error;
    }
    // Close connection
    $conn->close();
  }
?>
