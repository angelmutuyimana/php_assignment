<?php
// login.php - User login page (processed on same file)
// Includes global header (starts session) then handles POST for authentication.
include __DIR__ . '/includes/header.php';
include __DIR__ . '/includes/data.php';

$message = '';
if (isset($_GET['registered'])) {
    $message = 'Signup successful. Please login.';
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $password = trim($_POST['password'] ?? '');
    if ($email === '' || $password === '') {
        $message = 'Enter email and password.';
    } else {
        $user = authenticateUser($email, $password);
        if ($user) {
            $_SESSION['user'] = $user;
            if ($user['role'] === 'admin') {
                header('Location: admin_dashboard.php');
            } else {
                header('Location: dashboard.php');
            }
            exit;
        } else {
            $message = 'Invalid email or password.';
        }
    }
}
?>
<style>
        body {
            margin: 0;
            padding: 0;
            background: #f0f2f5;
            font-family: Arial, sans-serif;
        }

        .container {
            width: 100%;
            height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 20px;
        }

        .left {
            flex: 1;
            padding-left: 80px;
        }

        .left h1 {
            font-size: 55px;
            color: #1877f2;
            margin-bottom: 0;
        }

        .left p {
            font-size: 28px;
            margin-top: 10px;
            color: #1c1e21;
            max-width: 500px;
        }

        .right {
            width: 400px;
            background: white;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.2);
            margin-left:100px;
        }

        .right input {
            width: 100%;
            padding: 8px;
            margin-top: 10px;
            border-radius: 6px;
            border: 1px solid #dadde1;
            font-size: 16px;
        }

        .login-btn {
            width: 100%;
            background: #1877f2;
            color: white;
            border: none;
            padding: 16px;
            font-size: 20px;
            border-radius: 6px;
            margin-top: 15px;
            cursor: pointer;
        }

        .login-btn:hover {
            background: #166fe5;
        }

        .forgot {
            display: block;
            text-align: center;
            margin-top: 12px;
            color: #1877f2;
            font-size: 14px;
            text-decoration: none;
        }

        .divider {
            width: 100%;
            height: 1px;
            background: #ddd;
            margin: 18px 0;
        }

        .create-btn {
            width: 70%;
            background: #42b72a;
            color: white;
            border: none;
            padding: 14px;
            font-size: 17px;
            border-radius: 6px;
            display: block;
            margin: 0 auto;
            cursor: pointer;
        }

        .create-btn:hover {
            background: #36a420;
        }

        .bottom-text {
            margin-top: 25px;
            text-align: center;
            font-size: 14px;
        }

        .bottom-text a {
            font-weight: bold;
            color: black;
        }

        /* Mobile styling */
        @media (max-width: 900px) {
            .container {
                flex-direction: column;
                text-align: center;
            }
            .left {
                padding-left: 0;
                margin-bottom: 30px;
            }
            .left h1 {
                font-size: 45px;
            }
            .left p {
                font-size: 20px;
            }
        }
    </style>
<main>
    <div class="container">
        <div class="left">
            <h1>AngelBookStore</h1>
            <p>Login to access your dashboard and manage books.</p>
        </div>
        <div class="right">
            <?php if($message): ?>
                <div style="color:#d32f2f; font-size:14px; margin-bottom:6px;">
                    <?php echo htmlspecialchars($message); ?>
                </div>
            <?php endif; ?>
            <form action="login.php" method="POST">
                <input type="text" name="email" placeholder="Email" value="<?php echo isset($email)?htmlspecialchars($email):''; ?>" required>
                <input type="password" name="password" placeholder="Password" required>
                <button type="submit" class="login-btn">Log In</button>
                <a href="#" class="forgot">Forgot password?</a>
                <div class="divider"></div>
                <a href="signup.php" class="create-btn" style="text-align:center; text-decoration:none; display:block;">Create New Account</a>
            </form>
        </div>
    </div>
    <div class="bottom-text">
        <p>Need an account? <a href="signup.php">Sign up</a>.</p>
    </div>
</main>
<?php include __DIR__ . '/includes/footer.php'; ?>