<?php
session_start();
include 'db.php';

if (!isset($_SESSION['username']) || !isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$message = "";

$product_prices = [
    "Laptop" => 50000,
    "Headphones" => 3000,
    "Mouse" => 800,
    "Keyboard" => 1500
];

// Handle Order Placement
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['place_order'])) {
    $product_name = $_POST['product_name'];

    if (array_key_exists($product_name, $product_prices)) {
        $total_amount = $product_prices[$product_name];
    } else {
        $message = "❌ Invalid product selected!";
    }

    if (!empty($total_amount)) {
        $status = "Pending";

        $sql = "INSERT INTO orders (user_id, product_name, total_amount, status, order_date) VALUES (?, ?, ?, ?, NOW())";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("issd", $user_id, $product_name, $total_amount, $status);

        if ($stmt->execute()) {
            $message = "✅ Order placed successfully!";
        } else {
            $message = "❌ Error placing order: " . $conn->error;
        }
        $stmt->close();
    }
}

// Handle Delete Order
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['delete_order'])) {
    $order_id = $_POST['order_id'];

    // Verify ownership before deleting
    $check_query = "SELECT * FROM orders WHERE order_id = ? AND user_id = ?";
    $stmt = $conn->prepare($check_query);
    $stmt->bind_param("ii", $order_id, $user_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $delete_query = "DELETE FROM orders WHERE order_id = ?";
        $del_stmt = $conn->prepare($delete_query);
        $del_stmt->bind_param("i", $order_id);
        if ($del_stmt->execute()) {
            $message = "✅ Order deleted successfully!";
        } else {
            $message = "❌ Failed to delete order.";
        }
        $del_stmt->close();
    } else {
        $message = "❌ Unauthorized deletion attempt.";
    }
    $stmt->close();
}

$order_query = "SELECT * FROM orders WHERE user_id = ? ORDER BY order_date DESC";
$stmt = $conn->prepare($order_query);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$order_result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Home - Place Order</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style>
        /* Styling same as before, add this new class */
        .delete-btn {
            background-color: #ff4d4d;
            color: white;
            padding: 6px 10px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }

        .delete-btn:hover {
            background-color: #d93636;
        }

        /* Keep all previous styles */
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
            max-width: 700px;
            margin: 60px auto 100px auto;
            background: #fff;
            padding: 30px;
            border-radius: 12px;
            box-shadow: 0 4px 20px rgba(0,0,0,0.1);
            animation: slideUp 0.8s ease;
        }

        h2 {
            color: #4e54c8;
        }

        .message {
            color: green;
            font-weight: bold;
            margin: 10px 0;
        }

        select, button {
            padding: 10px;
            font-size: 1rem;
            border-radius: 6px;
            border: 1px solid #ccc;
            margin: 10px 0;
            width: 100%;
        }

        button.btn {
            background-color: #4e54c8;
            color: white;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }

        button.btn:hover {
            background-color: #3a40a1;
        }

        .nav a {
            display: inline-block;
            margin: 10px;
            padding: 8px 16px;
            background-color: #8f94fb;
            color: white;
            text-decoration: none;
            border-radius: 5px;
            transition: background-color 0.3s ease;
        }

        .nav a:hover {
            background-color: #5c61d3;
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

        @keyframes fadeIn {
            from { opacity: 0 }
            to { opacity: 1 }
        }

        @keyframes slideUp {
            from { transform: translateY(30px); opacity: 0 }
            to { transform: translateY(0); opacity: 1 }
        }

        @media (max-width: 600px) {
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
        }
    </style>
</head>
<body>

<header>
    Online Order Management
</header>

<div class="container">
    <h2>Hello, <?php echo htmlspecialchars($_SESSION['username']); ?>!</h2>
    <p>You can place your order below:</p>

    <?php if (!empty($message)) echo "<p class='message'>$message</p>"; ?>

    <form method="POST">
        <label for="product_name">Select Product:</label>
        <select name="product_name" required>
            <option value="">-- Choose Product --</option>
            <?php
            foreach ($product_prices as $product => $price) {
                echo "<option value='$product'>$product - ₹$price</option>";
            }
            ?>
        </select>

        <button type="submit" name="place_order" class="btn">Place Order</button>
    </form>

    <hr>

    <h3>Your Orders</h3>
    <table>
        <tr>
            <th>Order ID</th>
            <th>Product</th>
            <th>Amount</th>
            <th>Status</th>
            <th>Date</th>
            <th>Action</th>
        </tr>
        <?php
        if ($order_result->num_rows > 0) {
            while ($row = $order_result->fetch_assoc()) {
                echo "<tr>";
                echo "<td>" . $row["order_id"] . "</td>";
                echo "<td>" . $row["product_name"] . "</td>";
                echo "<td>₹" . $row["total_amount"] . "</td>";
                echo "<td>" . $row["status"] . "</td>";
                echo "<td>" . $row["order_date"] . "</td>";
                echo "<td>
                        <form method='POST' onsubmit='return confirm(\"Are you sure you want to delete this order?\");'>
                            <input type='hidden' name='order_id' value='" . $row["order_id"] . "'>
                            <button type='submit' name='delete_order' class='delete-btn'>Delete</button>
                        </form>
                      </td>";
                echo "</tr>";
            }
        } else {
            echo "<tr><td colspan='6'>No orders placed yet</td></tr>";
        }
        ?>
    </table>

    <div class="nav">
        <a href="logout.php">Logout</a>
    </div>
</div>

<footer>
    &copy; <?php echo date("Y"); ?> Online Order Management. All rights reserved.
</footer>

</body>
</html>
