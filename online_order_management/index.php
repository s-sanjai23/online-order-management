<?php include 'db.php'; ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Online Order Management</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f4f7fa;
            color: #333;
            animation: fadeIn 1s ease-in;
        }

        header {
            background-color: #007BFF;
            color: white;
            padding: 20px 40px;
            text-align: center;
            font-size: 28px;
        }

        footer {
            background-color: #f1f1f1;
            color: white;
            text-align: center;
            padding: 10px;
            font-size: 14px;
        }

        .hero {
            padding: 60px 20px;
            text-align: center;
            background: linear-gradient(to right, #e0f7fa, #ffffff);
        }

        .hero h1 {
            font-size: 36px;
            margin-bottom: 15px;
            color: #007BFF;
        }

        .hero p {
            font-size: 18px;
            margin-bottom: 25px;
        }

        .hero a {
            text-decoration: none;
            background-color: #007BFF;
            color: white;
            padding: 12px 25px;
            border-radius: 5px;
            font-size: 16px;
            transition: background 0.3s ease;
        }

        .hero a:hover {
            background-color: #0056b3;
        }

        .illustration {
            text-align: center;
            padding: 40px 20px;
            background: #fefefe;
            animation: fadeIn 1.2s ease-in;
        }

        .illustration img {
            max-width: 90%;
            height: auto;
            border-radius: 10px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
        }

        .illustration p {
            margin-top: 15px;
            font-size: 1.1rem;
            color: #444;
            font-style: italic;
        }

        .about {
            padding: 40px 20px;
            background-color: #f9f9f9;
            text-align: center;
        }

        .about h2 {
            color: #007BFF;
            font-size: 28px;
        }

        .about p {
            font-size: 17px;
            max-width: 700px;
            margin: 10px auto;
            line-height: 1.6;
        }

        @keyframes fadeIn {
            from {opacity: 0;}
            to {opacity: 1;}
        }
    </style>
</head>
<body>

    <header>Online Order Management</header>

    <div class="hero">
        <h1>Welcome to Online Order Management</h1>
        <p>Manage orders easily and efficiently with our platform.</p>
        <a href="register.php">Register</a>
        <a href="login.php" style="margin-left: 15px;">Login</a>
    </div>

    <div class="illustration">
    <img src="track.png" alt="Track your order">
        <p>Track and manage your orders anytime, anywhere.</p>
    </div>

    <div class="about">
        <h2>About Us</h2>
        <p>
            Our Online Order Management System is designed to help businesses handle orders from placement to delivery with ease. 
            Whether you're a customer placing an order or an admin managing them, our platform makes the process smooth and reliable.
        </p>
    </div>

    <footer>
        &copy; <?php echo date("Y"); ?> Online Order Management System. All rights reserved.
    </footer>

</body>
</html>
