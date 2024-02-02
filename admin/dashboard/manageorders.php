<div class="container-fluid mt-5">
    <h3>Manage Orders</h3>
    <table id="ordersTable" class="table table-bordered table-striped">
        <thead>
            <tr>
                <th>Order ID</th>
                <th>Customer Name</th>
                <th>Product</th>
                <th>Quantity</th>
                <th>Total Price</th>
                <th>Order Date</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            <!-- Order data will be loaded here dynamically -->
        </tbody>
    </table>
</div>
<script>
    $(document).ready(function () {
        // Initialize DataTable
        var table = $('#ordersTable').DataTable({
            ajax: {
                url: 'actions/orders.php', // Replace with the actual PHP file fetching order data
                type: 'POST',
                data: {
                'typeaction': 'select'
                },
                dataSrc: ''
            },
            columns: [
                { data: 'order_id' },
                { data: 'customer_name' },
                { data: 'product_name' },
                { data: 'quantity' },
                { data: 'total_price' },
                { data: 'order_date' },
                {
                    data: null,
                    render: function (data, type, row) {
                        return '<button class="btn btn-info btn-sm viewBtn" data-id="' + row.order_id + '">View</button>' +
                            ' <button class="btn btn-warning btn-sm editBtn" data-id="' + row.order_id + '">Edit</button>' +
                            ' <button class="btn btn-danger btn-sm deleteBtn" data-id="' + row.order_id + '">Delete</button>';
                    }
                }
            ]
        });

        // Handle "View" button click
        $('#ordersTable tbody').on('click', '.viewBtn', function () {
            var data = table.row($(this).parents('tr')).data();
            console.log('View clicked for order ID: ' + data.order_id);
            // Implement view logic (e.g., show modal with order details)
        });

        // Handle "Edit" button click
        $('#ordersTable tbody').on('click', '.editBtn', function () {
            var data = table.row($(this).parents('tr')).data();
            console.log('Edit clicked for order ID: ' + data.order_id);
            // Implement edit logic (e.g., redirect to edit page with order ID)
        });

        // Handle "Delete" button click
        $('#ordersTable tbody').on('click', '.deleteBtn', function () {
            var data = table.row($(this).parents('tr')).data();
            console.log('Delete clicked for order ID: ' + data.order_id);
            // Implement delete logic (e.g., show confirmation modal)
        });
    });
</script>
