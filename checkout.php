<?php
require_once 'includes/db_connect.php';
include 'includes/header.php';

// Verify authentication
if (!isset($_SESSION['user_id'])) {
    echo "<script>window.location = 'login.php';</script>";
    exit;
}

$id = isset($_GET['id']) ? $_GET['id'] : 0;
$status_msg = "";

// Process Checkout
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $ad_id = $_POST['ad_id'];
    $address = htmlspecialchars($_POST['address']);
    
    // Set item to sold
    $update = $pdo->prepare("UPDATE ads SET is_active = 0 WHERE ad_id = ?");
    if ($update->execute([$ad_id])) {
        $status_msg = <<<_SUCCESS
        <div class="alert alert-success text-center">
            <h4>Payment Successful!</h4>
            <p>Your item is being shipped to: <strong>$address</strong></p>
            <a href="index.php" class="btn btn-primary">Return to Marketplace</a>
        </div>
_SUCCESS;
    } else {
        $status_msg = "<div class='alert alert-danger'>Transaction failed. Try again.</div>";
    }
}

$form_html = "";
if (empty($status_msg)) {
    // Get item details for summary
    $stmt = $pdo->prepare("SELECT post_title, price FROM ads WHERE ad_id = ?");
    $stmt->execute([$id]);
    $item = $stmt->fetch();
    
    if ($item) {
        $item_name = htmlspecialchars($item['post_title']);
        $item_price = htmlspecialchars($item['price']);
        
        $form_html = <<<_FORM
        <div class="card shadow">
            <div class="card-header bg-success text-white">
                <h4 class="mb-0">Secure Checkout</h4>
            </div>
            <div class="card-body">
                <h5 class="card-title">Item: $item_name</h5>
                <h3 class="text-primary mb-4">Total: $$item_price</h3>
                
                <form method="post" action="checkout.php">
                    <input type="hidden" name="ad_id" value="$id">
                    
                    <div class="mb-3">
                        <label class="form-label">Shipping Address</label>
                        <textarea name="address" class="form-control" rows="3" required placeholder="123 Campus Dr..."></textarea>
                    </div>
                    
                    <hr>
                    
                    <div class="mb-3">
                        <label class="form-label">Credit Card (Mock)</label>
                        <input type="text" class="form-control" placeholder="0000 0000 0000 0000" required>
                    </div>
                    
                    <div class="row">
                        <div class="col-6 mb-3">
                            <label class="form-label">Expiry</label>
                            <input type="text" class="form-control" placeholder="MM/YY" required>
                        </div>
                        <div class="col-6 mb-3">
                            <label class="form-label">CVV</label>
                            <input type="text" class="form-control" placeholder="123" required>
                        </div>
                    </div>
                    
                    <button type="submit" class="btn btn-success w-100 btn-lg transition-btn">Pay Now</button>
                </form>
            </div>
        </div>
_FORM;
    } else {
        $status_msg = "<div class='alert alert-warning'>Item not found.</div>";
    }
}

echo <<<_END
<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-6">
            $status_msg
            $form_html
        </div>
    </div>
</div>
_END;

include 'includes/footer.php';
?>