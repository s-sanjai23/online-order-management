<?php
session_start();
include 'db.php'; // Database connection

if (!isset($_SESSION['username']) || $_SESSION['role'] != 'admin') {
    header("Location: login.php");
    exit();
}

$message = "";

// Handle order update
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['update_order'])) {
    $order_id = $_POST['order_id'];
    $new_status = $_POST['status'];

    $update_sql = "UPDATE orders SET status = ? WHERE order_id = ?";
    $stmt = $conn->prepare($update_sql);
    $stmt->bind_param("si", $new_status, $order_id);

    if ($stmt->execute()) {
        $message = "✅ Order updated successfully!";
    } else {
        $message = "❌ Error updating order: " . $conn->error;
    }
    $stmt->close();
}

$order_query = "SELECT * FROM orders ORDER BY order_date DESC";
$order_result = $conn->query($order_query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin Dashboard</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style>
        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            font-family: 'Segoe UI', sans-serif;
            background: linear-gradient(120deg, #f6f9fc, #dbe9f4);
            color: #333;
            animation: fadeIn 1s ease-in-out;
        }

        header {
            background: linear-gradient(to right, #4e54c8, #8f94fb);
            color: white;
            padding: 12px;
            text-align: center;
            font-size: 1.5rem;
            box-shadow: 0 2px 6px rgba(0,0,0,0.1);
        }

        footer {
            background: #f1f1f1;
            text-align: center;
            padding: 8px;
            font-size: 0.9rem;
            color: #666;
            position: fixed;
            bottom: 0;
            width: 100%;
        }

        .container {
            max-width: 1000px;
            margin: 60px auto 100px auto;
            background: #fff;
            padding: 30px;
            border-radius: 12px;
            box-shadow: 0 4px 20px rgba(0,0,0,0.1);
            animation: slideUp 0.8s ease;
        }

        h2, h3 {
            color: #4e54c8;
        }

        .message {
            color: green;
            font-weight: bold;
            margin-bottom: 15px;
        }

        table {
            width: 100%;
            margin-top: 20px;
            border-collapse: collapse;
            animation: fadeIn 1s ease;
        }

        table, th, td {
            border: 1px solid #ddd;
            padding: 10px;
        }

        th {
            background-color: #f0f0f0;
        }

        select, button {
            padding: 6px 10px;
            font-size: 1rem;
            border-radius: 6px;
            border: 1px solid #ccc;
        }

        button {
            background-color: #4e54c8;
            color: white;
            cursor: pointer;
            transition: background-color 0.3s ease;
            margin-left: 5px;
        }

        button:hover {
            background-color: #3a40a1;
        }

        .logout-link {
            display: inline-block;
            margin-top: 20px;
            padding: 8px 16px;
            background-color: #8f94fb;
            color: white;
            text-decoration: none;
            border-radius: 5px;
        }

        .logout-link:hover {
            background-color: #5c61d3;
        }

        @keyframes fadeIn {
            from { opacity: 0 }
            to { opacity: 1 }
        }

        @keyframes slideUp {
            from { transform: translateY(30px); opacity: 0 }
            to { transform: translateY(0); opacity: 1 }
        }

        @media (max-width: 768px) {
            .container {
                margin: 20px;
                padding: 20px;
            }

            header {
                font-size: 1.2rem;
                padding: 10px;
            }

            footer {
                font-size: 0.8rem;
                padding: 6px;
            }

            table {
                font-size: 0.9rem;
            }
        }
    </style>
</head>
<body>

<header>
    Admin Dashboard
</header>

<div class="container">
    <h2>Welcome, <?php echo htmlspecialchars($_SESSION['username']); ?>!</h2>
    <h3>Manage Orders</h3>

    <?php if (!empty($message)) echo "<p class='message'>$message</p>"; ?>

    <table>
        <tr>
            <th>Order ID</th>
            <th>User ID</th>
            <th>Product</th>
            <th>Qty</th>
            <th>Amount</th>
            <th>Status</th>
            <th>Order Date</th>
            <th>Action</th>
        </tr>
        <?php
        if ($order_result && $order_result->num_rows > 0) {
            while ($row = $order_result->fetch_assoc()) {
                echo "<tr>";
                echo "<td>" . $row["order_id"] . "</td>";
                echo "<td>" . $row["user_id"] . "</td>";
                echo "<td>" . $row["product_name"] . "</td>";
                echo "<td>" . $row["quantity"] . "</td>";
                echo "<td>₹" . $row["total_amount"] . "</td>";

                echo "<td>
                        <form method='POST' style='display:flex; align-items:center;'>
                            <input type='hidden' name='order_id' value='" . $row["order_id"] . "'>
                            <select name='status'>
                                <option value='Pending' " . ($row["status"] == "Pending" ? "selected" : "") . ">Pending</option>
                                <option value='Shipped' " . ($row["status"] == "Shipped" ? "selected" : "") . ">Shipped</option>
                                <option value='Delivered' " . ($row["status"] == "Delivered" ? "selected" : "") . ">Delivered</option>
                            </select>
                            <button type='submit' name='update_order'>Update</button>
                        </form>
                      </td>";

                echo "<td>" . $row["order_date"] . "</td>";
                echo "<td><strong>" . $row["status"] . "</strong></td>";
                echo "</tr>";
            }
        } else {
            echo "<tr><td colspan='8'>No orders found</td></tr>";
        }
        ?>
    </table>

    <a href="logout.php" class="logout-link">Logout</a>
</div>

<footer>
    &copy; <?php echo date("Y"); ?> Online Order Management. All rights reserved.
</footer>

</body>
</html>
