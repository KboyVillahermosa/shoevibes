<?php
session_start();

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "shoevibe_db";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Update the SQL query to specifically get the customization image
$sql = "SELECT 
    o.*, 
    CONCAT(o.first_name, ' ', o.last_name) as customer_name,
    p.product_name,
    p.price as unit_price,
    COALESCE(o.status, 'pending') as status,
    o.customization_data
FROM orders o
LEFT JOIN products p ON o.product_id = p.product_id
ORDER BY o.order_date DESC";

$result = $conn->query($sql);

// Handle display of order data
function getOrderStatus($status) {
    return $status ?? 'pending';
}

function getDesignImage($customization_data) {
    return !empty($customization_data) ? htmlspecialchars($customization_data) : '';
}

// Calculate order statistics
$sql_stats = "SELECT 
    COUNT(*) as total_orders,
    SUM(total_price) as total_revenue,
    COUNT(DISTINCT CONCAT(first_name, last_name)) as unique_customers
FROM orders";
$stats = $conn->query($sql_stats)->fetch_assoc();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin - Order Management</title>
    <link href="https://cdn.jsdelivr.net/npm/flowbite@3.1.1/dist/flowbite.min.css" rel="stylesheet">
</head>

<style>
    .admin-orders {
        margin-top: 150px;
    }

    .order-th {
        background-color: black;
        color: white;
    }

    .order-content {
        background-color: white;
        color: black;
    }

    .status-pending {
        background-color: #FEF3C7;
        color: #92400E;
    }

    .status-processing {
        background-color: #DBEAFE;
        color: #1E40AF;
    }

    .status-completed {
        background-color: #D1FAE5;
        color: #065F46;
    }
</style>

<body class="bg-gray-100">
    <?php include_once "admin_nav.php"; ?>
    
    <section class="admin-orders p-4 sm:ml-64">
        <div class="relative overflow-x-auto shadow-md sm:rounded-lg">
            <h2 class="text-3xl font-bold mb-4">Order Management</h2>

            <!-- Statistics Cards -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
                <div class="bg-white p-6 rounded-lg shadow-md">
                    <h3 class="text-xl font-semibold mb-2">Total Orders</h3>
                    <p class="text-3xl font-bold"><?php echo $stats['total_orders']; ?></p>
                </div>
                <div class="bg-white p-6 rounded-lg shadow-md">
                    <h3 class="text-xl font-semibold mb-2">Total Revenue</h3>
                    <p class="text-3xl font-bold">₱<?php echo number_format($stats['total_revenue'], 2); ?></p>
                </div>
                <div class="bg-white p-6 rounded-lg shadow-md">
                    <h3 class="text-xl font-semibold mb-2">Unique Customers</h3>
                    <p class="text-3xl font-bold"><?php echo $stats['unique_customers']; ?></p>
                </div>
            </div>

            <!-- Orders Table -->
            <div class="bg-white shadow-md rounded-lg overflow-hidden">
                <table class="w-full text-sm text-left">
                    <thead class="order-th">
                        <tr>
                            <th class="px-6 py-3">Order ID</th>
                            <th class="px-6 py-3">Customer</th>
                            <th class="px-6 py-3">Product</th>
                            <th class="px-6 py-3">3D Design</th>
                            <th class="px-6 py-3">Details</th>
                            <th class="px-6 py-3">Total Price</th>
                            <th class="px-6 py-3">Status</th>
                            <th class="px-6 py-3">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($row = $result->fetch_assoc()): ?>
                            <tr class="order-content border-b hover:bg-gray-50">
                                <td class="px-6 py-4">#<?php echo $row['order_id']; ?></td>
                                <td class="px-6 py-4">
                                    <div class="font-medium">
                                        <?php echo htmlspecialchars($row['customer_name'] ?? 'N/A'); ?>
                                    </div>
                                    <div class="text-sm text-gray-500">
                                        <?php echo htmlspecialchars($row['email'] ?? 'N/A'); ?>
                                    </div>
                                    <div class="text-sm text-gray-500">
                                        <?php echo htmlspecialchars($row['phone'] ?? 'N/A'); ?>
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    <div><?php echo htmlspecialchars($row['product_name']); ?></div>
                                    <div class="text-sm text-gray-500">
                                        Size: <?php echo $row['size']; ?><br>
                                        Qty: <?php echo $row['quantity']; ?>
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    <?php 
                                    $designPath = $row['customization_data'];
                                    if (!empty($designPath)): 
                                        // Extract the filename from the path
                                        $filename = basename($designPath);
                                        // Check both potential file locations
                                        $fullPath = "../shoes-preview/customizations/{$filename}";
                                    ?>
                                        <?php if (file_exists($fullPath)): ?>
                                            <img src="<?php echo $fullPath; ?>" 
                                                 alt="3D Design" 
                                                 class="w-20 h-20 object-cover rounded-lg cursor-pointer"
                                                 onclick="viewDesign('<?php echo htmlspecialchars($filename); ?>')">
                                        <?php else: ?>
                                            <span class="text-gray-400">Image file not found (<?php echo htmlspecialchars($filename); ?>)</span>
                                        <?php endif; ?>
                                    <?php else: ?>
                                        <span class="text-gray-400">No design available</span>
                                    <?php endif; ?>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="text-sm">
                                        <?php echo htmlspecialchars($row['street'] ?? ''); ?>,<br>
                                        <?php echo htmlspecialchars($row['barangay'] ?? ''); ?>,<br>
                                        <?php echo htmlspecialchars($row['city'] ?? ''); ?>,<br>
                                        <?php echo htmlspecialchars($row['province'] ?? ''); ?> 
                                        <?php echo htmlspecialchars($row['postal_code'] ?? ''); ?>
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="font-bold">
                                        ₱<?php echo number_format($row['total_price'] ?? 0, 2); ?>
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    <span class="px-3 py-1 rounded-full text-sm status-<?php echo getOrderStatus($row['status']); ?>">
                                        <?php echo ucfirst(getOrderStatus($row['status'])); ?>
                                    </span>
                                </td>
                                <td class="px-6 py-4">
                                    <select onchange="updateStatus(<?php echo $row['order_id']; ?>, this.value)" 
                                            class="rounded border-gray-300 text-sm">
                                        <?php $currentStatus = getOrderStatus($row['status']); ?>
                                        <option value="pending" <?php echo $currentStatus == 'pending' ? 'selected' : ''; ?>>Pending</option>
                                        <option value="processing" <?php echo $currentStatus == 'processing' ? 'selected' : ''; ?>>Processing</option>
                                        <option value="completed" <?php echo $currentStatus == 'completed' ? 'selected' : ''; ?>>Completed</option>
                                    </select>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </section>

    <!-- Design Preview Modal -->
    <div id="designModal" class="fixed inset-0 bg-black bg-opacity-50 hidden flex items-center justify-center z-50">
        <div class="bg-white p-4 rounded-lg max-w-2xl w-full">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-xl font-bold">3D Design Preview</h3>
                <button onclick="closeDesignModal()" class="text-gray-500 hover:text-gray-700">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>
            <img id="designPreview" src="" alt="3D Design Preview" class="w-full h-auto">
        </div>
    </div>

    <script>
    function updateStatus(orderId, status) {
        fetch('update_order_status.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: `order_id=${orderId}&status=${status}`
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                location.reload();
            } else {
                alert('Error updating status');
            }
        });
    }

    function viewDesign(imagePath) {
        const modal = document.getElementById('designModal');
        const preview = document.getElementById('designPreview');
        preview.src = `../shoes-preview/customizations/${imagePath}`;
        modal.classList.remove('hidden');
    }

    function closeDesignModal() {
        document.getElementById('designModal').classList.add('hidden');
    }
    </script>

    <script src="https://cdn.jsdelivr.net/npm/flowbite@3.1.2/dist/flowbite.min.js"></script>
</body>
</html>