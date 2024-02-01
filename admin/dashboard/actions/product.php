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
    $sql = "SELECT a.product_id, a.product_name, a.product_type_id, a.product_price, a.product_image, a.product_status , b.product_type_name
    FROM products a
    LEFT JOIN producttypes b ON a.product_type_id = b.product_type_id  ";

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
  }elseif ($typeaction == "checkingduplicate_update") {
    // Check for duplicate product name
    $product_id_to_update = $_POST['product_id'];
    $new_product_name = $_POST['new_product_name'];

    // Check for duplicate product by name excluding the current product being updated
    $check_duplicate_sql = "SELECT COUNT(*) as count FROM products WHERE product_name = '$new_product_name' AND product_id != '$product_id_to_update'";

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
  }elseif ($typeaction =="Create") {
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
  }elseif ($typeaction =="edit") {
    // Get product_id from the AJAX request
    $product_id = $_POST['product_id'];
    // Query to retrieve data for the specified product_id
    $selectSql = "SELECT * FROM products WHERE product_id = $product_id";
    $result = $conn->query($selectSql);
    $text ='';
    if ($result && $result->num_rows > 0) {
        $row = $result->fetch_assoc();

        // Output the HTML for the edit form
        $text = '
            <form id="editProductForm">
                <div class="form-group">
                    <label for="editProductName">Product Name:</label>
                    <input type="hidden" name="typeaction" value="update">
                    <input type="hidden"  id="editproduct_id" name="product_id" value="' . $row['product_id'] . '" required>
                    <input type="text" class="form-control" id="editProductName" name="product_name" value="' . $row['product_name'] . '" required>
                </div>
                <div class="form-group">
                    <label for="editProductTypeId">Product Type :</label>
                    <select class="form-control" id="editproductTypeId" name="product_type_id" required>';
                    $sql2 = "SELECT product_type_id , product_type_name FROM producttypes";

                    $result2 = $conn->query($sql2);


                    if ($result2->num_rows > 0) {
                        while ($row2 = $result2->fetch_assoc()) {
                            $product_type_id = $row2['product_type_id'];
                            $product_type_name = $row2['product_type_name'];
                            if ($row['product_type_id'] == $product_type_id) {
                              $selected = "selected";
                            }else {
                              $selected = "";
                            }
                            $text .= "<option  ".$selected." value='".$product_type_id."'>".$product_type_name."</option>";
                        }
                    }
                    $text .= '</select>
                </div>
                <div class="form-group">
                    <label for="editProductPrice">Product Price:</label>
                    <input type="text" class="form-control" id="editProductPrice" name="product_price" value="' . $row['product_price'] . '" required>
                </div>
                <div class="form-group">
                    <label for="editProductImage">Product Image:</label>
                    <input type="file" class="form-control-file" id="editProductImage" name="product_image" accept="image/*">
                    <img src="http://localhost/ProjectPOS/admin/dashboard/actions/' . $row['product_image'] . '" alt="Product Image" style="max-width: 100px; max-height: 100px;">
                </div>
                <div class="form-group">
                    <label for="editProductStatus">Product Status:</label>
                    <select class="form-control" id="editProductStatus" name="product_status" required>
                        <option value="0" ' . ($row['product_status'] == 0 ? 'selected' : '') . '>Inactive</option>
                        <option value="1" ' . ($row['product_status'] == 1 ? 'selected' : '') . '>Active</option>
                    </select>
                </div>
                <button type="button" class="btn btn-primary" onclick="function_update();" id="updateBtn">Update</button>
            </form>
        ';
        echo $text;
    } else {
        echo "Error: Product not found.";
    }

    $conn->close();
  }elseif($typeaction =="update") {
    // Get form data
    $product_id_to_update = $_POST['product_id'];
    $productName = $_POST['product_name'];
    $productTypeId = $_POST['product_type_id'];
    $productPrice = $_POST['product_price'];
    $productStatus = $_POST['product_status'];
    if ($_FILES["product_image"]["name"]=='') {
      $update_sql = "UPDATE products SET
                      product_name ='$productName',
                      product_type_id ='$productTypeId',
                      product_price ='$productPrice',
                      product_status ='$productStatus'
                     WHERE product_id ='$product_id_to_update'";
      if ($conn->query($update_sql) === TRUE) {
          echo "Product UPDATE successfully";
      } else {
          echo "Error: " . $update_sql . "<br>" . $conn->error;
      }
    }else{
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
              $update_sql = "UPDATE products SET
                              product_name ='$productName',
                              product_type_id ='$productTypeId',
                              product_price ='$productPrice',
                              product_status ='$productStatus' ,
                              product_image='$productImage' ,

                             WHERE product_id='$product_id_to_update'";
                             echo $update_sql;
              if ($conn->query($update_sql) === TRUE) {
                  echo "Product created successfully";
              } else {
                  echo "Error: " . $update_sql . "<br>" . $conn->error;
              }
          } else {
              echo "Error: There was an error uploading your file.";
          }
      }
    }


    $conn->close();
  }
 ?>
