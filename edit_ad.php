<?php
require_once 'includes/db_connect.php';
include 'includes/header.php';

// 1. SECURITY: Gatekeeper
// If the user isn't logged in, kick them to the login page immediately.
if (!isset($_SESSION['user_id'])) {
    echo "<script>window.location = 'login.php';</script>";
    exit;
}

$ad_id = $_GET['id'] ?? null;
$user_id = $_SESSION['user_id'];
$msg = "";

// 2. LOGIC: Handling the "Save Changes" Click (POST Request)
// This block only runs when the user hits the Submit button.
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $ad_id = $_POST['ad_id']; // Get the ID from the hidden input
    $title = trim($_POST['title']);
    $price = $_POST['price'];
    $details = trim($_POST['details']);
    
    // 2a. SECURITY: Ownership Check
    // We check AGAIN to make sure the user actually owns the ad they are trying to update.
    // This prevents hackers from using tools to force an update on someone else's ad.
    $check = $pdo->prepare("SELECT user_id FROM ads WHERE ad_id = ? AND user_id = ?");
    $check->execute([$ad_id, $user_id]);
    
    if ($check->rowCount() > 0) {
        // 2b. The Update
        $sql = "UPDATE ads SET post_title = ?, price = ?, post_detail = ? WHERE ad_id = ?";
        $stmt = $pdo->prepare($sql);
        if ($stmt->execute([$title, $price, $details, $ad_id])) {
            $msg = "<div class='alert alert-success'>Ad updated successfully! <a href='ad_details.php?id=$ad_id'>View it here</a></div>";
        } else {
            $msg = "<div class='alert alert-danger'>Update failed. Database error.</div>";
        }
    } else {
        $msg = "<div class='alert alert-danger'>Security Error: You do not own this ad.</div>";
    }
}

// 3. LOGIC: Fetching Data to Pre-fill the Form (GET Request)
// We need to get the current title/price from the DB to show it in the boxes.
$stmt = $pdo->prepare("SELECT * FROM ads WHERE ad_id = ? AND user_id = ?");
$stmt->execute([$ad_id, $user_id]);
$ad = $stmt->fetch();

// If ad doesn't exist or doesn't belong to user, stop.
if (!$ad) {
    echo "<div class='container mt-5'><div class='alert alert-danger'>Ad not found or access denied.</div></div>";
    include 'includes/footer.php';
    exit;
}

// Prepare variables for the HTML below (Sanitize for display)
$title_val = htmlspecialchars($ad['post_title']);
$price_val = htmlspecialchars($ad['price']);
$detail_val = htmlspecialchars($ad['post_detail']);

// 4. VIEW: The HTML Form (Heredoc Syntax)
echo <<<_END
<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow-sm">
                <div class="card-header bg-warning text-dark">
                    <h4 class="mb-0">Edit Listing: $title_val</h4>
                </div>
                <div class="card-body">
                    $msg
                    
                    <form method="POST" action="edit_ad.php?id=$ad_id">
                        
                        <input type="hidden" name="ad_id" value="$ad_id">
                        
                        <div class="mb-3">
                            <label class="form-label">Title</label>
                            <input type="text" name="title" class="form-control" value="$title_val" required>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">Price ($)</label>
                            <input type="number" name="price" class="form-control" step="0.01" value="$price_val" required>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">Details</label>
                            <textarea name="details" class="form-control" rows="5">$detail_val</textarea>
                        </div>
                        
                        <div class="d-flex justify-content-between">
                            <a href="my_account.php" class="btn btn-outline-secondary">Cancel</a>
                            <button type="submit" class="btn btn-primary px-4">Save Changes</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
_END;

include 'includes/footer.php';
?>