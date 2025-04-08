<?php
require_once 'config.php';


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);
    
    if (empty($username) || empty($password)) {
        $error = "Please enter both username and password";
    } else {
        $stmt = $conn->prepare("SELECT id, username, password, full_name, role FROM users WHERE username = ?");
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows === 1) {
            $user = $result->fetch_assoc();
            if ($password == $user['password']) {
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['username'] = $user['username'];
                $_SESSION['full_name'] = $user['full_name'];
                $_SESSION['role'] = $user['role'];
                
                if ($user['role'] === 'admin') {
                    echo "<script>alert('Welcome back Admin!'); window.location.href='admin.php'</script>";
                } else {
                    echo "<script>alert('Welcome back Staff!'); window.location.href='staff.php'</script>";
                }
            } else {
                echo "<script>alert('Invalid username or password'); window.location.href='index.php'</script>";
            }
        } else {
            echo "<script>alert('Invalid username or password'); window.location.href='index.php'</script>";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>AttendancePro - Staff Attendance System</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        /* General Styles */
        body {
            font-family: 'Poppins', sans-serif;
            margin: 0;
            padding: 0;
            background: linear-gradient(to right, #0056b3, #00c6ff);
            color: #fff;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            height: 100vh;
            text-align: center;
            overflow: hidden;
        }

        /* Header */
        header {
            width: 100%;
            background-color: #003366;
            padding: 15px 0;
            position: absolute;
            top: 0;
            text-align: center;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.2);
            animation: slideDown 1s ease-in-out;
        }

        .logo {
            font-size: 2rem;
            font-weight: 600;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            color: #fff;
        }

        .logo i {
            font-size: 2rem;
        }

        /* Hero Section */
        .hero {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            text-align: center;
            width: 100%;
            max-width: 800px;
            animation: fadeIn 2s ease-in-out;
            transition: transform 1s ease-in-out, opacity 1s ease-in-out;
        }

        .hero h1 {
            font-size: 3.5rem;
            font-weight: bold;
            text-shadow: 2px 2px 5px rgba(0, 0, 0, 0.3);
            margin-bottom: 20px;
            animation: slideIn 1.5s ease-in-out, float 3s ease-in-out infinite;
        }

        .hero p {
            font-size: 1.5rem;
            max-width: 600px;
            margin-bottom: 30px;
            animation: slideIn 2s ease-in-out, float 3s ease-in-out infinite 0.5s;
        }

        .btn-primary {
            background-color: #fff;
            color: #0056b3;
            padding: 15px 30px;
            border: none;
            border-radius: 50px;
            cursor: pointer;
            font-size: 1.2rem;
            transition: background-color 0.3s, transform 0.3s;
            animation: fadeIn 2.5s ease-in-out, pulse 2s ease-in-out infinite;
            text-decoration: none;
            font-weight: bold;
        }

        .btn-primary:hover {
            background-color: #e0e0e0;
            transform: scale(1.1);
        }

        /* Login Container */
        .login-container {
            display: flex;
            justify-content: center;
            align-items: center;
            width: 100%;
            height: 100vh;
            position: absolute;
            top: 100%; /* Start below the viewport */
            left: 0;
            transition: transform 1s ease-in-out;
        }

        .login-box {
            background: #ffffff;
            padding: 40px;
            border-radius: 12px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.2);
            width: 100%;
            max-width: 400px;
            text-align: center;
            color: #333;
            opacity: 0; /* Start hidden */
            transform: translateY(20px);
            transition: opacity 1s ease-in-out, transform 1s ease-in-out;
        }

        .login-box h2 {
            font-size: 2rem;
            margin-bottom: 20px;
            color: #003366;
        }

        /* Input Fields */
        .input-group {
            display: flex;
            align-items: center;
            margin-bottom: 20px;
            border: 2px solid #003366;
            border-radius: 8px;
            padding: 12px;
            background: #f9f9f9;
        }

        .input-group i {
            margin-right: 12px;
            color: #003366;
        }

        .input-group input {
            width: 100%;
            border: none;
            outline: none;
            font-size: 1rem;
            background: none;
        }

        /* Login Button */
        .btn-login {
            background-color: #003366;
            color: #fff;
            padding: 15px 30px;
            border: none;
            border-radius: 50px;
            cursor: pointer;
            font-size: 1rem;
            transition: background-color 0.3s, transform 0.3s;
            width: 100%;
            font-weight: bold;
        }

        .btn-login:hover {
            background-color: #0056b3;
            transform: scale(1.05);
        }

        /* Error Message */
        #loginMessage {
            margin-top: 20px;
            color: #ff0000;
            font-size: 0.9rem;
        }

        /* Extra Links */
        .extra-links {
            margin-top: 15px;
        }

        .extra-links a {
            text-decoration: none;
            color: #003366;
            font-size: 0.9rem;
        }

        .extra-links a:hover {
            text-decoration: underline;
        }

        /* Animations */
        @keyframes fadeIn {
            from { opacity: 0; }
            to { opacity: 1; }
        }

        @keyframes slideIn {
            from { transform: translateY(50px); opacity: 0; }
            to { transform: translateY(0); opacity: 1; }
        }

        @keyframes slideDown {
            from { transform: translateY(-100%); }
            to { transform: translateY(0); }
        }

        @keyframes float {
            0%, 100% { transform: translateY(0); }
            50% { transform: translateY(-10px); }
        }

        @keyframes pulse {
            0%, 100% { transform: scale(1); }
            50% { transform: scale(1.05); }
        }
    </style>
</head>
<body>
    <!-- Header -->
    <header>
        <div class="logo">
            <i class="fas fa-user-clock"></i>
            <span>AttendancePro</span>
        </div>
    </header>

    <!-- Hero Section -->
    <section class="hero" id="hero">
        <h1>Welcome to AttendancePro</h1>
        <p>Efficiently manage your staff attendance with our intuitive system.</p>
        <a href="#" class="btn-primary" id="getStartedBtn">Get Started</a>
    </section>

    <!-- Login Form -->
    <div class="login-container" id="loginContainer">
        <div class="login-box" id="loginBox">
            <h2>Login</h2>
            <?php if (isset($error)): ?>
                <p id="loginMessage" style="color: red;"><?php echo htmlspecialchars($error); ?></p>
            <?php endif; ?>
            <form id="loginForm" method="POST" action="">
                <div class="input-group">
                    <i class="fas fa-user"></i>
                    <input type="text" id="username" name="username" placeholder="Username" required>
                </div>
                <div class="input-group">
                    <i class="fas fa-lock"></i>
                    <input type="password" id="password" name="password" placeholder="Password" required>
                </div>
                <button type="submit" class="btn-login">Login</button>
                <div class="extra-links">
                    <a href="#">Forgot Password?</a>
                </div>
            </form>
        </div>
    </div>

    <script>
        document.getElementById('getStartedBtn').addEventListener('click', function(event) {
            event.preventDefault();

            const hero = document.getElementById('hero');
            hero.style.transform = 'translateY(-100%)';
            hero.style.opacity = '0';

            const loginContainer = document.getElementById('loginContainer');
            const loginBox = document.getElementById('loginBox');
            loginContainer.style.transform = 'translateY(-100%)';
            setTimeout(() => {
                loginBox.style.opacity = '1';
                loginBox.style.transform = 'translateY(0)';
            }, 500);
        });
    </script>
</body>
</html>
