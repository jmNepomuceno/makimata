<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MIKAMATA Admin Login</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="login.css">
    <?php include("../scripts_links/header_links.php") ?>
</head>
<body>
    <div class="login-container">
        <div class="login-card">
            <div class="logo-container">
                <img src="mik/logooo.png" alt="MIKAMATA Logo" class="login-logo">
                <h1 class="title">Admin Login</h1>
            </div>

            <form id="login-form" class="login-form">
                <div id="error-message" class="error-message">Invalid username or password.</div>

                <div class="input-group">
                    <input type="text" id="username" name="username" placeholder="Username" required>
                    <i class="fas fa-user input-icon"></i>
                </div>

                <div class="input-group">
                    <input type="password" id="password" name="password" placeholder="Password" required>
                    <i class="fas fa-lock input-icon"></i>
                </div>

                <button type="submit" class="login-btn">Login</button>

                <div class="extra-links">
                    <a href="#">Forgot Password?</a>
                </div>
            </form>
        </div>
    </div>
    <script src="login.js"></script>
</body>
</html>
