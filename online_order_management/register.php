<?php
include 'db.php';

$message = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = trim($_POST['username']);
    $email = trim($_POST['email']);
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $role = $_POST['role'];

    $sql = "INSERT INTO users (username, email, password, role) 
            VALUES ('$username', '$email', '$password', '$role')";
    
    if ($conn->query($sql) === TRUE) {
        $message = "✅ Registration successful!";
    } else {
        $message = "❌ Error: " . $conn->error;
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Register</title>
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
            max-width: 500px;
            margin: 60px auto;
            background: #fff;
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

        input, select {
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 6px;
            margin-top: 5px;
            font-size: 1rem;
        }

        input:focus, select:focus {
            border-color: #4e54c8;
            outline: none;
            box-shadow: 0 0 5px rgba(78,84,200,0.3);
        }

        input[type="submit"], .login-button {
            background-color: #4e54c8;
            color: white;
            border: none;
            margin-top: 20px;
            cursor: pointer;
            transition: background 0.3s ease;
            padding: 10px;
            border-radius: 6px;
            font-size: 1rem;
            text-align: center;
        }

        input[type="submit"]:hover, .login-button:hover {
            background-color: #3a40a1;
        }

        .message {
            text-align: center;
            color: green;
            font-weight: bold;
            margin-top: 10px;
        }

        .login-link {
            text-align: center;
            margin-top: 15px;
        }

        .login-link a {
            text-decoration: none;
            color: white;
            display: inline-block;
            padding: 10px 20px;
            background-color: #4e54c8;
            border-radius: 6px;
            transition: background-color 0.3s;
        }

        .login-link a:hover {
            background-color: #3a40a1;
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
                font-size: 1.2rem;
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
    User Registration
</header>

<div class="container">
    <h2>Create Account</h2>

    <?php if (!empty($message)) echo "<p class='message'>$message</p>"; ?>

    <form method="POST">
        <label for="username">Username:</label>
        <input type="text" name="username" required>

        <label for="email">Email:</label>
        <input type="email" name="email" required>

        <label for="password">Password:</label>
        <input type="password" name="password" required>

        <label for="role">Select Role:</label>
        <select name="role">
            <option value="customer">Customer</option>
            <option value="admin">Admin</option>
        </select>

        <input type="submit" name="register" value="Register">
    </form>

    <div class="login-link">
        <a href="login.php">Already have an account? Login</a>
    </div>
</div>

<footer>
    &copy; <?php echo date("Y"); ?> Online Order Management. All rights reserved.
</footer>

</body>
</html>
