<div class="container-fluid mt-5">
    <h3>Manage Tables</h3>
    <!-- Button trigger modal -->
    <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#exampleModal">
      New Table
    </button>
    <hr>
    <div class="table-responsive">
      <table id="tablesTable" class="table table-bordered table-striped">
          <thead>
              <tr>
                  <th>Table ID</th>
                  <th>Table Number</th>
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
        <h5 class="modal-title" id="exampleModalLabel">Modal New Table</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <div class="container mt-5">
            <form id="tableForm" enctype="multipart/form-data">
                <input type="hidden" name="typeaction" value="Create">
                <div class="form-group">
                    <label for="productName">Table Number:</label>
                    <input type="text" class="form-control" id="table_number" name="table_number" required>
                    <div id="table_numberError" class="text-danger"></div>
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
                <h5 class="modal-title" id="editModalLabel">Edit Table</h5>
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
          var table = $('#tablesTable').DataTable({
             ajax: {
                 url: 'actions/tables.php', // Replace with the actual PHP file fetching product data
                 type: 'POST',
                 data: {
                 'typeaction': 'select'
                 },
                 dataSrc: ''
             },
             columns: [
                 { data: 'table_id' },
                 { data: 'table_number' },
                 {
                    // Add the "Edit" button
                    data: null,
                    render: function (data, type, row) {
                        return '<button class="btn btn-info btn-sm editBtn" data-id="' + row.table_id + '">Edit</button>';
                    }
                }
             ]
         });



        $("#submitBtn").click(function () {
             if (validateForm()) {

               var formData = new FormData($("#tableForm")[0]);

               // Check for duplicate product by name
               $.ajax({
                   type: "POST",
                   url: "actions/tables.php", // Replace with the actual PHP file for duplicate checking
                   data: { typeaction:'checkingduplicate',table_number: $("#table_number").val() },
                   success: function (response) {
                       if (response === "duplicate") {
                           alert("Error: Product with the same name already exists");
                       } else {
                           // No duplicate found, proceed with product creation
                           $.ajax({
                               type: "POST",
                               url: "actions/tables.php", // Replace with the actual PHP file for creating a product
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
              if ($("#table_number").val().trim() === "") {
                  $("#table_numberError").text("Product name is required");
                  valid = false;
              }

              return valid;
          }
          // Handle "Edit" button click
          $('#tablesTable tbody').on('click', '.editBtn', function () {
              var data = table.row($(this).parents('tr')).data();
              // You can now access the data of the clicked row using the 'data' variable
              // For example: data.product_id, data.product_name, etc.
              console.log('Edit clicked for Table ID: ' + data.table_id);

              // Load the edit form dynamically
              $.ajax({
                  type: 'POST',
                  url: 'actions/tables.php', // Replace with the actual PHP file for the edit form
                  data: { 'typeaction':'edit','table_id': data.table_id },
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
        var formData = new FormData($("#editTableForm")[0]);
        //Check for duplicate product by name
        $.ajax({
            type: "POST",
            url: "actions/tables.php", // Replace with the actual PHP file for duplicate checking
            data: { typeaction:'checkingduplicate_update',new_table_number: $("#edittable_number").val(),'edittable_id':$("#edittable_id").val() },
            success: function (response) {
                if (response === "duplicate") {
                    alert("Error: Table with the same name already exists");
                } else {
                    // No duplicate found, proceed with product creation
                    $.ajax({
                        type: "POST",
                        url: "actions/tables.php", // Replace with the actual PHP file for creating a product
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
