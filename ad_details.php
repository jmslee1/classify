<?php
require_once 'includes/db_connect.php';
include 'includes/header.php';

if (!isset($_GET['id'])) {
    echo "<div class='container mt-5'>Ad ID missing. <a href='index.php'>Go Home</a></div>";
    include 'includes/footer.php';
    exit;
}

$id = $_GET['id'];
$stmt = $pdo->prepare("SELECT ads.*, users.username, users.email FROM ads JOIN users ON ads.user_id = users.user_id WHERE ad_id = ?");
$stmt->execute([$id]);
$ad = $stmt->fetch();

if (!$ad) {
    echo "<div class='container mt-5'>Ad not found.</div>";
    include 'includes/footer.php';
    exit;
}

// Format display data
$title = htmlspecialchars($ad['post_title']);
$price = htmlspecialchars($ad['price']);
$detail = nl2br(htmlspecialchars($ad['post_detail']));
$seller = htmlspecialchars($ad['username']);
$date = date("F j, Y", strtotime($ad['create_date']));

// Determine user permissions
$is_owner = (isset($_SESSION['user_id']) && $_SESSION['user_id'] == $ad['user_id']);
$action_buttons = "";

if ($is_owner) {
    $action_buttons = <<<_BTNS
    <div class="alert alert-info">This is your listing.</div>
    <a href="edit_ad.php?id=$id" class="btn btn-warning">Edit Ad</a> 
    <a href="delete_ad.php?id=$id" class="btn btn-danger" onclick="return confirm('Delete this?');">Delete</a>
_BTNS;
} else {
    if ($ad['listing_type'] == 'OFFER') {
        $action_buttons .= "<a href='checkout.php?id=$id' class='btn btn-success btn-lg w-100 mb-2 transition-btn'>Buy Now</a>";
    }
    $action_buttons .= "<a href='mailto:{$ad['email']}' class='btn btn-outline-secondary w-100'>Contact Seller</a>";
}

echo <<<_END
<div class="container mt-5">
    <div class="row">
        <div class="col-md-6 mb-4">
            <img src="https://via.placeholder.com/600x400" class="img-fluid rounded shadow-sm" alt="Product Image">
        </div>
        <div class="col-md-6">
            <div class="mb-2">
                <span class="badge bg-secondary">$date</span>
                <span class="badge bg-primary">{$ad['listing_type']}</span>
            </div>
            <h1 class="display-5 fw-bold">$title</h1>
            <h2 class="text-success my-3">$$price</h2>
            <p class="text-muted">Posted by <strong>$seller</strong></p>
            <hr>
            <div class="lead fs-6 mb-5">
                $detail
            </div>
            
            <div class="card bg-light">
                <div class="card-body">
                    $action_buttons
                </div>
            </div>
        </div>
    </div>
</div>
_END;

include 'includes/footer.php';
?>