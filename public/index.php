<?php
session_start();
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Đăng nhập - Hệ thống Quản lý Sinh viên</title>
    <link rel="stylesheet" href="../public/asset/css/bootstrap-5.3.3-dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="../public/asset/css/bootstrap-icons-1.11.3/font/bootstrap-icons.css">
    <link rel="stylesheet" href="../public/asset/css/style.css">
    <link rel="shortcut icon" href="../public/asset/images/password.png" type="image/x-icon">
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;500;700&display=swap" rel="stylesheet">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Roboto', Arial, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }
        
        .header-section {
            background: rgba(255, 255, 255, 0.95);
            padding: 20px 0;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            text-align: center;
        }
        
        .header-section .logo {
            max-width: 60px;
            margin-bottom: 10px;
        }
        
        .header-section h1 {
            color: #1e3a8a;
            font-size: 24px;
            font-weight: 700;
            margin: 10px 0 5px 0;
            text-transform: uppercase;
        }
        
        .header-section .slogan {
            color: #64748b;
            font-size: 14px;
            font-weight: 400;
        }
        
        .login-container {
            max-width: 450px;
            width: 90%;
            margin: auto;
            background: white;
            padding: 40px;
            border-radius: 15px;
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.2);
            margin-top: 50px;
            margin-bottom: 50px;
        }
        
        .login-container h2 {
            color: #1e3a8a;
            font-size: 28px;
            font-weight: 700;
            margin-bottom: 10px;
        }
        
        .login-container > p {
            color: #64748b;
            font-size: 15px;
            margin-bottom: 30px;
        }
        
        .form-label {
            font-weight: 500;
            color: #334155;
            font-size: 14px;
            margin-bottom: 8px;
        }
        
        .input-group-custom {
            position: relative;
            margin-bottom: 20px;
        }
        
        .input-group-custom .icon {
            position: absolute;
            left: 15px;
            top: 50%;
            transform: translateY(-50%);
            color: #94a3b8;
            font-size: 18px;
            z-index: 10;
        }
        
        .input-group-custom input {
            padding-left: 45px;
            padding-right: 45px;
            height: 50px;
            border: 2px solid #e2e8f0;
            border-radius: 10px;
            font-size: 15px;
            transition: all 0.3s ease;
        }
        
        .input-group-custom input:focus {
            border-color: #3b82f6;
            box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
        }
        
        .toggle-password {
            position: absolute;
            right: 15px;
            top: 50%;
            transform: translateY(-50%);
            background: none;
            border: none;
            color: #94a3b8;
            cursor: pointer;
            font-size: 18px;
            z-index: 10;
            transition: color 0.3s ease;
        }
        
        .toggle-password:hover {
            color: #3b82f6;
        }
        
        .btn-login {
            width: 100%;
            height: 50px;
            background: #1e3a8a;
            border: none;
            color: white;
            font-size: 16px;
            font-weight: 600;
            border-radius: 10px;
            transition: all 0.3s ease;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        
        .btn-login:hover {
            background: #1e40af;
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(30, 58, 138, 0.3);
        }
        
        .btn-login:active {
            transform: translateY(0);
        }
        
        .links-section {
            text-align: center;
            margin-top: 20px;
        }
        
        .links-section a {
            color: #3b82f6;
            text-decoration: none;
            font-weight: 500;
            transition: color 0.3s ease;
        }
        
        .links-section a:hover {
            color: #1e40af;
            text-decoration: underline;
        }
        
        .flash-messages {
            animation: slideDown 0.3s ease;
        }
        
        @keyframes slideDown {
            from {
                opacity: 0;
                transform: translateY(-10px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        
        .footer-section {
            background: rgba(255, 255, 255, 0.1);
            color: white;
            text-align: center;
            padding: 20px;
            margin-top: auto;
            font-size: 14px;
        }
        
        @media (max-width: 576px) {
            .login-container {
                padding: 30px 20px;
                margin-top: 30px;
            }
            
            .header-section h1 {
                font-size: 18px;
            }
            
            .login-container h2 {
                font-size: 24px;
            }
        }
    </style>
</head>
<body>
    <!-- Header Section -->
    <div class="header-section">
        <img src="../public/asset/images/mortarboard.png" alt="Logo" class="logo">
        <h1>Hệ thống Quản lý Sinh viên</h1>
        <p class="slogan">Trường Đại học XYZ</p>
    </div>

    <!-- Login Container -->
    <div class="login-container">
        <h2 class="text-center">ĐĂNG NHẬP HỆ THỐNG</h2>
        <p class="text-center">Vui lòng nhập thông tin đăng nhập của bạn</p>
        
        <?php
        // Display success message
        if (isset($_SESSION['success'])) {
            echo '<div class="flash-messages">';
            echo '<div class="alert alert-success text-center">';
            echo '<i class="bi bi-check-circle-fill me-2"></i>' . $_SESSION['success'];
            echo '</div></div>';
            unset($_SESSION['success']);
        }

        // Display error message
        if (isset($_SESSION['error'])) {
            echo '<div class="flash-messages">';
            echo '<div class="alert alert-danger text-center">';
            echo '<i class="bi bi-exclamation-triangle-fill me-2"></i>' . $_SESSION['error'];
            echo '</div></div>';
            unset($_SESSION['error']);
        }
        ?>

        <form action="../public/login.php" method="POST" id="loginForm">
            <div class="mb-3">
                <label for="username" class="form-label">Tên đăng nhập</label>
                <div class="input-group-custom">
                    <i class="bi bi-person-circle icon"></i>
                    <input 
                        type="text" 
                        class="form-control" 
                        name="username" 
                        id="username" 
                        placeholder="Nhập mã sinh viên / tài khoản" 
                        required
                        autocomplete="username"
                    >
                </div>
            </div>
            
            <div class="mb-3">
                <label for="password" class="form-label">Mật khẩu</label>
                <div class="input-group-custom">
                    <i class="bi bi-lock-fill icon"></i>
                    <input 
                        type="password" 
                        name="password" 
                        id="password" 
                        class="form-control" 
                        placeholder="Nhập mật khẩu" 
                        required
                        autocomplete="current-password"
                        minlength="6"
                    >
                    <button type="button" class="toggle-password" id="togglePassword">
                        <i class="bi bi-eye"></i>
                    </button>
                </div>
            </div>
            
            <div class="mb-3 form-check">
                <input type="checkbox" class="form-check-input" id="rememberMe" name="remember_me">
                <label class="form-check-label" for="rememberMe">Ghi nhớ đăng nhập</label>
            </div>
            
            <button type="submit" class="btn-login">
                <i class="bi bi-box-arrow-in-right me-2"></i>Đăng nhập
            </button>
        </form>
        
        <div class="links-section">
            <p class="text-muted mb-2">Quên mật khẩu? <a href="../public/reset_request.php">Đặt lại mật khẩu</a></p>
            <p class="text-muted"><a href="#">Liên hệ quản trị hệ thống</a></p>
        </div>
    </div>
    
    <!-- Footer Section -->
    <div class="footer-section">
        <p>&copy; 2026 Trường Đại học XYZ. All rights reserved.</p>
    </div>
    
    <script>
        // Toggle password visibility
        document.getElementById('togglePassword').addEventListener('click', function() {
            const passwordInput = document.getElementById('password');
            const icon = this.querySelector('i');
            
            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                icon.classList.remove('bi-eye');
                icon.classList.add('bi-eye-slash');
            } else {
                passwordInput.type = 'password';
                icon.classList.remove('bi-eye-slash');
                icon.classList.add('bi-eye');
            }
        });
        
        // Auto hide flash messages after 5 seconds
        setTimeout(function() {
            const flashMessages = document.querySelector('.flash-messages');
            if (flashMessages) {
                flashMessages.style.transition = 'opacity 0.5s ease';
                flashMessages.style.opacity = '0';
                setTimeout(() => flashMessages.remove(), 500);
            }
        }, 5000);
    </script>
</body>
</html>
