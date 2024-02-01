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
    $sql = "SELECT product_name, product_type_id, product_price, product_image, product_status FROM products";

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
    $productName = $_POST['product_name'];

    $checkDuplicateSql = "SELECT COUNT(*) as count FROM products WHERE product_name = '$productName'";
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
  }else if ($typeaction =="Create") {
    // Get form data
    $productName = $_POST['product_name'];
    $productTypeId = $_POST['product_type_id'];
    $productPrice = $_POST['product_price'];
    $productStatus = $_POST['product_status'];

    // File upload handling
    $target_dir = "uploads/"; // Directory where you want to store uploaded images
    $target_file = $target_dir . basename($_FILES["product_image"]["name"]);
    $uploadOk = 1;
    $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

    // Check if image file is a actual image or fake image
    $check = getimagesize($_FILES["product_image"]["tmp_name"]);
    if ($check === false) {
        echo "Error: File is not an image.";
        $uploadOk = 0;
    }

    // Check if file already exists
    if (file_exists($target_file)) {
        echo "Error: Sorry, file already exists.";
        $uploadOk = 0;
    }

    // Check file size
    if ($_FILES["product_image"]["size"] > 500000) {
        echo "Error: Sorry, your file is too large.";
        $uploadOk = 0;
    }

    // Allow certain file formats
    $allowed_image_types = array("jpg", "jpeg", "png", "gif");
    if (!in_array($imageFileType, $allowed_image_types)) {
        echo "Error: Sorry, only JPG, JPEG, PNG, and GIF files are allowed.";
        $uploadOk = 0;
    }

    // Check if $uploadOk is set to 0 by an error
    if ($uploadOk == 0) {
        echo "Error: Your file was not uploaded.";
    } else {
        if (move_uploaded_file($_FILES["product_image"]["tmp_name"], $target_file)) {
            // File uploaded successfully, proceed with database insertion
            $productImage = $target_file; // Store the file path in the database

            // Insert product data into the database
            $insertSql = "INSERT INTO products (product_name, product_type_id, product_price, product_image, product_status)
                           VALUES ('$productName', '$productTypeId', '$productPrice', '$productImage', '$productStatus')";

            if ($conn->query($insertSql) === TRUE) {
                echo "Product created successfully";
            } else {
                echo "Error: " . $insertSql . "<br>" . $conn->error;
            }
        } else {
            echo "Error: There was an error uploading your file.";
        }
    }

    $conn->close();
  }elseif ($typeaction =="update") {
    // Assume you have received form data for the update
    $product_id_to_update = $_POST['product_id'];
    $new_product_name = $_POST['new_product_name'];

    // Check for duplicate product by name excluding the current product being updated
    $check_duplicate_sql = "SELECT * FROM Products WHERE product_name = '$new_product_name' AND product_id != $product_id_to_update";
    $result = $conn->query($check_duplicate_sql);

    if ($result->num_rows > 0) {
        // Product with the same name already exists (excluding the current product being updated)
        echo "Error: Product with the same name already exists";
    } else {
        // No duplicate found, proceed with update
        $update_sql = "UPDATE Products SET product_name='$new_product_name' WHERE product_id=$product_id_to_update";

        if ($conn->query($update_sql) === TRUE) {
            echo "Product updated successfully";
        } else {
            echo "Error updating product: " . $conn->error;
        }
    }

    $conn->close();
  }
 ?>
