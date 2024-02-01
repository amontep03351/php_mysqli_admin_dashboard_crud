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
    $sql = "SELECT a.*
    FROM Customers a ";

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
  }elseif ($typeaction == "checkingduplicate") {
    // Check for duplicate product name
    $customer_name = $_POST['customer_name'];

    $checkDuplicateSql = "SELECT COUNT(*) as count FROM Customers WHERE customer_name = '$customer_name'";
    $result = $conn->query($checkDuplicateSql);

    if ($result && $result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $count = $row['count'];

        if ($count > 0) {
            echo "duplicate";
        } else {
            echo "not_duplicate";
        }
    } else {
        echo "error";
    }

    $conn->close();
  }elseif ($typeaction =="Create") {
    // Get form data
    $customer_name = $_POST['customer_name'];
    // Insert Customers data into the database
    $insertSql = "INSERT INTO Customers (customer_name)
                   VALUES ('$customer_name')";

    if ($conn->query($insertSql) === TRUE) {
        echo "Customers created successfully";
    } else {
        echo "Error: " . $insertSql . "<br>" . $conn->error;
    }

    $conn->close();
  }elseif ($typeaction =="edit") {
    // Get product_id from the AJAX request
    $customer_id  = $_POST['customer_id'];
    // Query to retrieve data for the specified product_id
    $selectSql = "SELECT * FROM Customers WHERE customer_id  = '$customer_id' ";
    $result = $conn->query($selectSql);
    $text ='';
    if ($result && $result->num_rows > 0) {
        $row = $result->fetch_assoc();

        // Output the HTML for the edit form
        $text = '
            <form id="editTableForm">
                <div class="form-group">
                    <label for="editTable">Table Number:</label>
                    <input type="hidden" name="typeaction" value="update">
                    <input type="hidden"  id="editcustomer_id" name="customer_id" value="' . $row['customer_id'] . '" required>
                    <input type="text" class="form-control" id="editcustomer_name" name="customer_name" value="' . $row['customer_name'] . '" required>
                </div>

                <button type="button" class="btn btn-primary" onclick="function_update();" id="updateBtn">Update</button>
            </form>
        ';
        echo $text;
    } else {
        echo "Error: Customers not found.";
    }

    $conn->close();
  }elseif ($typeaction == "checkingduplicate_update") {
    // Check for duplicate product name
    $editcustomer_id = $_POST['editcustomer_id'];
    $new_customer_name = $_POST['new_customer_name'];


    // Check for duplicate product by name excluding the current product being updated
    $check_duplicate_sql = "SELECT COUNT(*) as count FROM Customers WHERE customer_name = '$new_customer_name' AND customer_id != '$editcustomer_id'";

    $result = $conn->query($check_duplicate_sql);

    if ($result && $result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $count = $row['count'];

        if ($count > 0) {
            echo "duplicate";
        } else {
            echo "not_duplicate";
        }
    } else {
        echo "error";
    }

    $conn->close();
  }elseif($typeaction =="update") {
    // Get form data
    $customer_id  = $_POST['customer_id'];
    $customer_name = $_POST['customer_name'];

    $update_sql = "UPDATE Customers SET
                    customer_name ='$customer_name'
                   WHERE customer_id  ='$customer_id'";
    if ($conn->query($update_sql) === TRUE) {
        echo "Customers UPDATE successfully";
    } else {
        echo "Error: " . $update_sql . "<br>" . $conn->error;
    }

    $conn->close();
  }



?>
