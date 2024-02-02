<div class="container-fluid mt-5">
    <h3>Manage Products</h3>
    <!-- Button trigger modal -->
    <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#exampleModal">
      New Product
    </button>
    <hr>
    <div class="table-responsive">
      <table id="productTable" class="table table-bordered table-striped">
          <thead>
              <tr>
                  <th>Product Name</th>
                  <th>Product Type</th>
                  <th>Product Price</th>
                  <th>Product Image</th>
                  <th>Product Status</th>
                  <th>Action</th>
              </tr>
          </thead>
          <tbody>
              <!-- Product data will be loaded here dynamically -->
          </tbody>
      </table>
    </div>
</div>

<!-- Modal -->
<div class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel">Modal New Product</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <div class="container mt-5">
            <form id="productForm" enctype="multipart/form-data">
                <input type="hidden" name="typeaction" value="Create">
                <div class="form-group">
                    <label for="productName">Product Name:</label>
                    <input type="text" class="form-control" id="productName" name="product_name" required>
                    <div id="nameError" class="text-danger"></div>
                </div>
                <div class="form-group">
                    <label for="productTypeId">Product Type :</label>
                    <select class="form-control" id="productTypeId" name="product_type_id" required>
                    <?php
                      include '../../actions/connect.php';
                      $sql = "SELECT product_type_id , product_type_name FROM producttypes";

                      $result = $conn->query($sql);

                      $data = array();

                      if ($result->num_rows > 0) {
                          while ($row = $result->fetch_assoc()) {
                              $product_type_id = $row['product_type_id'];
                              $product_type_name = $row['product_type_name'];
                              echo "<option value='".$product_type_id."'>".$product_type_name."</option>";
                          }
                      }

                      // Close the database connection
                      $conn->close();

                     ?>
                    </select>
                    <div id="typeIdError" class="text-danger"></div>
                </div>
                <div class="form-group">
                    <label for="productPrice">Product Price:</label>
                    <input type="number" class="form-control" id="productPrice" name="product_price" required>
                    <div id="priceError" class="text-danger"></div>
                </div>
                <div class="form-group">
                    <label for="productImage">Product Image:</label>
                    <input type="file" class="form-control-file" id="productImage" name="product_image" accept="image/*" required>
                    <div id="imageError" class="text-danger"></div>
                </div>
                <div class="form-group">
                    <label for="productStatus">Product Status:</label>
                    <select class="form-control" id="productStatus" name="product_status" required>
                        <option value="0">Inactive</option>
                        <option value="1">Active</option>
                    </select>
                    <div id="statusError" class="text-danger"></div>
                </div>
                <button type="button" class="btn btn-primary" id="submitBtn">Submit</button>
            </form>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal" >Close</button>
      </div>
    </div>
  </div>
</div>

<!-- Modal for Edit Form -->
<div class="modal fade" id="editModal" tabindex="-1" role="dialog" aria-labelledby="editModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editModalLabel">Edit Product</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <!-- Edit Form will be loaded here dynamically -->
            </div>
        </div>
    </div>
</div>
<script>
    $(document).ready(function () {
          var table = $('#productTable').DataTable({
             ajax: {
                 url: 'actions/product.php', // Replace with the actual PHP file fetching product data
                 type: 'POST',
                 data: {
                 'typeaction': 'select'
                 },
                 dataSrc: ''
             },
             columns: [
                 { data: 'product_name' },
                 { data: 'product_type_name' },
                 { data: 'product_price' },
                 {
                     data: 'product_image',
                     render: function (data, type, row) {
                         return '<a target="_blank" href="http://localhost/ProjectPOS/admin/dashboard/actions/' + data + '"><img src="http://localhost/ProjectPOS/admin/dashboard/actions/' + data + '" alt="Product Image" style="max-width: 100px; max-height: 100px;"></a>';
                     }
                 },
                 {
                     data: 'product_status',
                     render: function (data) {
                         return data === '1' ? 'Active' : 'Inactive';
                     }
                 },
                 {
                    // Add the "Edit" button
                    data: null,
                    render: function (data, type, row) {
                        return '<button class="btn btn-info btn-sm editBtn" data-id="' + row.product_id + '">Edit</button>';
                    }
                }
             ]
         });
        // Update the label with the selected filename for the custom file input
        $("#productImage").change(function () {
            var fileName = $(this).val().split('\\').pop();
            $(this).next('.custom-file-label').html(fileName);
        });

        $("#submitBtn").click(function () {
             if (validateForm()) {

               var formData = new FormData($("#productForm")[0]);

               // Check for duplicate product by name
               $.ajax({
                   type: "POST",
                   url: "actions/product.php", // Replace with the actual PHP file for duplicate checking
                   data: { typeaction:'checkingduplicate',product_name: $("#productName").val() },
                   success: function (response) {
                       if (response === "duplicate") {
                           alert("Error: Product with the same name already exists");
                       } else {
                           // No duplicate found, proceed with product creation
                           $.ajax({
                               type: "POST",
                               url: "actions/product.php", // Replace with the actual PHP file for creating a product
                               data: formData,
                               contentType: false,
                               processData: false,
                               success: function (createResponse) {
                                   alert(createResponse); // You can handle the response from the server here
                               },
                               error: function (xhr, status, error) {
                                   console.error(xhr.responseText);
                               }
                           });
                       }
                   },
                   error: function (xhr, status, error) {
                       console.error(xhr.responseText);
                   }
               });
              }
           });


          function validateForm() {

              var valid = true;

              // Clear previous error messages
              $(".text-danger").text("");

              // Validate product name
              if ($("#productName").val().trim() === "") {
                  $("#nameError").text("Product name is required");
                  valid = false;
              }

              // Validate product type ID
              if ($("#productTypeId").val().trim() === "") {
                  $("#typeIdError").text("Product type ID is required");
                  valid = false;
              }

              // Validate product price
              var price = parseFloat($("#productPrice").val());
              if (isNaN(price) || price <= 0) {
                  $("#priceError").text("Enter a valid product price");
                  valid = false;
              }

              // Validate product image
              var allowedImageTypes = ["jpg", "jpeg", "png", "gif"];
              var imageExtension = $("#productImage").val().split('.').pop().toLowerCase();
              if ($.inArray(imageExtension, allowedImageTypes) === -1) {
                  $("#imageError").text("Only JPG, JPEG, PNG, and GIF file types are allowed");
                  valid = false;
              }

              // Validate product status
              if ($("#productStatus").val() !== "0" && $("#productStatus").val() !== "1") {
                  $("#statusError").text("Invalid product status");
                  valid = false;
              }

              return valid;
          }
          // Handle "Edit" button click
          $('#productTable tbody').on('click', '.editBtn', function () {
              var data = table.row($(this).parents('tr')).data();
              console.log(data);
              // You can now access the data of the clicked row using the 'data' variable
              // For example: data.product_id, data.product_name, etc.
              console.log('Edit clicked for product ID: ' + data.product_id);

              // Load the edit form dynamically
              $.ajax({
                  type: 'POST',
                  url: 'actions/product.php', // Replace with the actual PHP file for the edit form
                  data: { 'typeaction':'edit','product_id': data.product_id },
                  success: function (editForm) {
                      $("#editModal .modal-body").html(editForm);
                      $("#editModal").modal('show');
                  },
                  error: function (xhr, status, error) {
                      console.error(xhr.responseText);
                  }
              });
          });

    });

    function function_update() {
        var formData = new FormData($("#editProductForm")[0]);
        //Check for duplicate product by name
        $.ajax({
            type: "POST",
            url: "actions/product.php", // Replace with the actual PHP file for duplicate checking
            data: { typeaction:'checkingduplicate_update',new_product_name: $("#editProductName").val(),'product_id':$("#editproduct_id").val() },
            success: function (response) {
                if (response === "duplicate") {
                    alert("Error: Product with the same name already exists");
                } else {
                    // No duplicate found, proceed with product creation
                    $.ajax({
                        type: "POST",
                        url: "actions/product.php", // Replace with the actual PHP file for creating a product
                        data: formData,
                        contentType: false,
                        processData: false,
                        success: function (createResponse) {
                            alert(createResponse); // You can handle the response from the server here
                        },
                        error: function (xhr, status, error) {
                            console.error(xhr.responseText);
                        }
                    });
                }
            },
            error: function (xhr, status, error) {
                console.error(xhr.responseText);
            }
        });
    }
</script>
