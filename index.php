<?php 
    include("./assets/connection/connection.php");
    
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Login</title>
    <link rel="stylesheet" href="index.css" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <?php include("./scripts_links/header_links.php") ?>
</head>
<body>
    <div class="container">
        <!-- Left Image Column -->
        <div class="left-column">
            <img src="./customer/mik/b2.png" alt="Bamboo Decoration" class="side-image" />
            <div class="image-overlay">
                <div class="left-column-text">
                    <p>Welcome to Mikamata.</p>
                </div>
            </div>
        </div>

        <!-- Right Column -->
        <div class="right-column">
            <div class="logo">
                <img src="./customer/mik/logo.png" alt="Mikamata Logo" />
            </div>

            <h2>Login to Your Account</h2>

            <form id="loginForm">
                <!-- Email -->
                <div class="form-group">
                    <label for="email"><i class="fa-solid fa-envelope"></i> Email</label>
                    <input type="email" id="email" name="email" required placeholder="your@email.com" />
                </div>

                <!-- Password -->
                <div class="form-group">
                    <label for="password"><i class="fa-solid fa-lock"></i> Password</label>
                    <input type="password" id="password" name="password" required placeholder="Password"/>
                </div>

                <!-- Submit -->
                <div class="full-width">
                    <button type="submit" class="login-btn">Login</button>
                </div>
            </form>

            <p class="signup-link">Don't have an account? <a href="./customer/signup.php">Create one</a></p>
            <a id="admin-link" href="./admin/login.php">Admin?</a>
        </div>
    </div>

    <div id="toast-container"></div>

    <script src="./index.js"></script>
</body>
</html>
