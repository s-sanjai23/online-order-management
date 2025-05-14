<?php
session_start();
include 'db.php';

$error_message = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);

    $sql = "SELECT * FROM users WHERE username = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows == 1) {
        $row = $result->fetch_assoc();

        if (password_verify($password, $row['password'])) {
            $_SESSION['username'] = $username;
            $_SESSION['role'] = $row['role'];
            $_SESSION['user_id'] = $row['user_id'];

            if ($row['role'] == 'admin') {
                header("Location: dashboard.php");
            } else {
                header("Location: home.php");
            }
            exit();
        } else {
            $error_message = "❌ Wrong password or user ID!";
        }
    } else {
        $error_message = "❌ Wrong password or user ID!";
    }
    $stmt->close();
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Login</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style>
        * { box-sizing: border-box; }

        body {
            font-family: 'Segoe UI', sans-serif;
            margin: 0;
            background: linear-gradient(to right, #dbe9f4, #f6f9fc);
            animation: fadeIn 1s ease-in-out;
        }

        header {
            background: linear-gradient(to right, #4e54c8, #8f94fb);
            color: white;
            padding: 15px;
            text-align: center;
            font-size: 1.6rem;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        }

        .container {
            max-width: 450px;
            margin: 60px auto;
            background: white;
            padding: 30px;
            border-radius: 12px;
            box-shadow: 0 4px 20px rgba(0,0,0,0.1);
            animation: slideUp 0.8s ease;
        }

        h2 {
            text-align: center;
            color: #4e54c8;
        }

        form {
            display: flex;
            flex-direction: column;
        }

        label {
            margin-top: 15px;
            font-weight: bold;
        }

        input {
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 6px;
            margin-top: 5px;
            font-size: 1rem;
        }

        input:focus {
            border-color: #4e54c8;
            outline: none;
            box-shadow: 0 0 5px rgba(78,84,200,0.3);
        }

        input[type="submit"] {
            background-color: #4e54c8;
            color: white;
            border: none;
            margin-top: 20px;
            cursor: pointer;
            transition: background 0.3s ease;
        }

        input[type="submit"]:hover {
            background-color: #3a40a1;
        }

        .error-message {
            color: red;
            text-align: center;
            font-weight: bold;
            margin-bottom: 10px;
        }

        footer {
            background: #f1f1f1;
            text-align: center;
            padding: 10px;
            font-size: 0.9rem;
            color: #666;
            position: fixed;
            width: 100%;
            bottom: 0;
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
                margin: 40px 20px;
                padding: 20px;
            }

            header {
                font-size: 1.3rem;
                padding: 12px;
            }

            footer {
                font-size: 0.8rem;
            }
        }
    </style>
</head>
<body>

<header>
    Login to Your Account
</header>

<div class="container">
    <h2>Welcome Back</h2>

    <?php if (!empty($error_message)): ?>
        <p class="error-message"><?php echo htmlspecialchars($error_message); ?></p>
    <?php endif; ?>

    <form method="POST">
        <label for="username">Username:</label>
        <input type="text" name="username" required>

        <label for="password">Password:</label>
        <input type="password" name="password" required>

        <input type="submit" value="Login">
    </form>
</div>

<footer>
    &copy; <?php echo date("Y"); ?> Online Order Management. All rights reserved.
</footer>

</body>
</html>
