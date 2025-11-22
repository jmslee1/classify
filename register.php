<?php
require_once 'includes/db_connect.php';
// Session start is required if we want to auto-login later or handle state messages
session_start();

$message_html = "";

if (isset($_POST['username']) && isset($_POST['password'])) {
    $username = trim($_POST['username']);
    $email = trim($_POST['email']);
    $password = $_POST['password'];

    // 1. Check if user exists
    $stmt = $pdo->prepare("SELECT user_id FROM users WHERE username = ? OR email = ?");
    $stmt->execute([$username, $email]);

    if ($stmt->rowCount() > 0) {
        $message_html = "<div class='alert alert-danger'>Username or Email already taken.</div>";
    } else {
        // 2. Hash Password (Security requirement)
        $hash = password_hash($password, PASSWORD_DEFAULT);
        
        // 3. Insert User
        $sql = "INSERT INTO users (username, email, password_hash) VALUES (?, ?, ?)";
        $insert = $pdo->prepare($sql);
        
        if ($insert->execute([$username, $email, $hash])) {
            $message_html = "<div class='alert alert-success'>Account created! <a href='login.php'>Login here</a>.</div>";
        } else {
            $message_html = "<div class='alert alert-danger'>Error creating account.</div>";
        }
    }
}

// Output Page
include 'includes/header.php';

echo <<<_END
<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-6 col-lg-4">
            <div class="card shadow-sm">
                <div class="card-body">
                    <h3 class="text-center mb-4">Create Account</h3>
                    $message_html
                    <form method="post" action="register.php">
                        <div class="mb-3">
                            <label>Username</label>
                            <input type="text" name="username" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label>Email</label>
                            <input type="email" name="email" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label>Password</label>
                            <input type="password" name="password" class="form-control" required>
                        </div>
                        <button type="submit" class="btn btn-primary w-100 transition-btn">Sign Up</button>
                    </form>
                    <div class="text-center mt-3">
                        <a href="login.php">Already have an account? Login</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
_END;

include 'includes/footer.php';
?>