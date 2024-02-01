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
    FROM tables a ";

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
    $table_number = $_POST['table_number'];

    $checkDuplicateSql = "SELECT COUNT(*) as count FROM tables WHERE table_number = '$table_number'";
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
    $table_number = $_POST['table_number'];
    // Insert tables data into the database
    $insertSql = "INSERT INTO tables (table_number)
                   VALUES ('$table_number')";

    if ($conn->query($insertSql) === TRUE) {
        echo "Tables created successfully";
    } else {
        echo "Error: " . $insertSql . "<br>" . $conn->error;
    }

    $conn->close();
  }elseif ($typeaction =="edit") {
    // Get product_id from the AJAX request
    $table_id  = $_POST['table_id'];
    // Query to retrieve data for the specified product_id
    $selectSql = "SELECT * FROM tables WHERE table_id  = '$table_id' ";
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
                    <input type="hidden"  id="edittable_id" name="table_id" value="' . $row['table_id'] . '" required>
                    <input type="text" class="form-control" id="edittable_number" name="table_number" value="' . $row['table_number'] . '" required>
                </div>

                <button type="button" class="btn btn-primary" onclick="function_update();" id="updateBtn">Update</button>
            </form>
        ';
        echo $text;
    } else {
        echo "Error: Tables not found.";
    }

    $conn->close();
  }elseif ($typeaction == "checkingduplicate_update") {
    // Check for duplicate product name
    $edittable_id = $_POST['edittable_id'];
    $new_table_number = $_POST['new_table_number'];


    // Check for duplicate product by name excluding the current product being updated
    $check_duplicate_sql = "SELECT COUNT(*) as count FROM tables WHERE table_number = '$new_table_number' AND table_id != '$edittable_id'";

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
    $table_id  = $_POST['table_id'];
    $table_number = $_POST['table_number'];

    $update_sql = "UPDATE tables SET
                    table_number ='$table_number'
                   WHERE table_id  ='$table_id'";
    if ($conn->query($update_sql) === TRUE) {
        echo "Tables UPDATE successfully";
    } else {
        echo "Error: " . $update_sql . "<br>" . $conn->error;
    }

    $conn->close();
  }



?>
