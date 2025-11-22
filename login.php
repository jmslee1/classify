<?php
require_once 'includes/db_connect.php';
session_start();

$msg_html = "";

if (isset($_POST['username']) && isset($_POST['password'])) {
    $username = trim($_POST['username']);
    $password = $_POST['password'];

    // 1. Fetch User
    $stmt = $pdo->prepare("SELECT * FROM users WHERE username = ?");
    $stmt->execute([$username]);
    $user = $stmt->fetch();

    // 2. Verify Password
    if ($user && password_verify($password, $user['password_hash'])) {
        // 3. Set Session State (Slide 29 of Managing State.pdf)
        $_SESSION['user_id'] = $user['user_id'];
        $_SESSION['username'] = $user['username'];
        
        header("Location: index.php");
        exit;
    } else {
        $msg_html = "<div class='alert alert-danger'>Invalid login credentials.</div>";
    }
}

include 'includes/header.php';

echo <<<_END
<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-6 col-lg-4">
            <div class="card shadow-sm">
                <div class="card-body">
                    <h3 class="text-center mb-4">Login</h3>
                    $msg_html
                    <form method="post" action="login.php">
                        <div class="mb-3">
                            <label>Username</label>
                            <input type="text" name="username" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label>Password</label>
                            <input type="password" name="password" class="form-control" required>
                        </div>
                        <button type="submit" class="btn btn-primary w-100 transition-btn">Login</button>
                    </form>
                    <div class="text-center mt-3">
                        <a href="register.php">Need an account? Register</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
_END;

include 'includes/footer.php';
?>