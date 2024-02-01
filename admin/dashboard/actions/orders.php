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
  }
?>
