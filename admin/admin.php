<?php
session_start();
include '../database.php';

// Check if the admin is logged in (Modify this part based on your login system)
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Fetch all users from the database
$result = $conn->query("SELECT id, first_name, last_name, email FROM users");
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/flowbite@3.1.1/dist/flowbite.min.css" rel="stylesheet">
   
</head>

<body class="bg-gray-100">
    <style>
      .delete-btn{
        background: black;
        padding: 8px;
      }
      .name{
        background-color: white;
        color: black;
      }
      .name-header {
        background-color: black;
        color: white;
      }
    </style>
     <?php include_once"admin_nav.php"; ?>
        <section class="admin-header p-4 sm:ml-64">
        <div class="relative overflow-x-auto shadow-md sm:rounded-lg">

        <?php if (isset($_SESSION['message'])): ?>
            <div class="bg-green-100 text-green-700 p-3 rounded mb-4">
                <?php echo $_SESSION['message'];
                unset($_SESSION['message']); ?>
            </div>
        <?php endif; ?>
  
        <table class="w-full text-sm text-left rtl:text-right text-gray-500 dark:text-gray-100">
        <thead class="name-header text-xs text-gray-700 uppercase bg-gray-100 dark:bg-gray-700 dark:text-gray-400">
                <tr>
                    <th class="px-6 py-3">ID</th>
                    <th class="px-6 py-3">First Name</th>
                    <th class="px-6 py-3">Last Name</th>
                    <th class="px-6 py-3">Email</th>
                    <th class="px-6 py-3">Action</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = $result->fetch_assoc()): ?>
                    <tr class="name border-b dark:bg-gray-800 dark:border-gray-700 border-gray-200 hover:bg-gray-50 dark:hover:bg-gray-600">
                        <td class="px-6 py-4"><?php echo $row['id']; ?></td>
                        <td class="px-6 py-4"><?php echo $row['first_name']; ?></td>
                        <td class="px-6 py-4"><?php echo $row['last_name']; ?></td>
                        <td class="px-6 py-4"><?php echo $row['email']; ?></td>
                        <td class="px-6 py-4">
                            <form action="delete.php" method="POST" onsubmit="return confirm('Are you sure?');">
                                <input type="hidden" name="user_id" value="<?php echo $row['id']; ?>">
                                <button type="submit"
                                    class="delete-btn bg-black text-white px-4 py-1 rounded hover:bg-red-700">Delete</button>
                            </form>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
        </section>

    </section>

  




    <script src="https://cdn.jsdelivr.net/npm/flowbite@3.1.2/dist/flowbite.min.js"></script>
</body>

</html>