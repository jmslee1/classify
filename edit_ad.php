<?php
require_once 'includes/db_connect.php';
include 'includes/header.php';

// Verify authentication
if (!isset($_SESSION['user_id'])) {
    echo "<script>window.location = 'login.php';</script>";
    exit;
}

$ad_id = $_GET['id'] ?? null;
$user_id = $_SESSION['user_id'];
$msg = "";

// Process Update
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $ad_id = $_POST['ad_id']; 
    $title = trim($_POST['title']);
    $price = $_POST['price'];
    $details = trim($_POST['details']);
    
    // Verify ownership before updating
    $check = $pdo->prepare("SELECT user_id FROM ads WHERE ad_id = ? AND user_id = ?");
    $check->execute([$ad_id, $user_id]);
    
    if ($check->rowCount() > 0) {
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

// Fetch current data
$stmt = $pdo->prepare("SELECT * FROM ads WHERE ad_id = ? AND user_id = ?");
$stmt->execute([$ad_id, $user_id]);
$ad = $stmt->fetch();

if (!$ad) {
    echo "<div class='container mt-5'><div class='alert alert-danger'>Ad not found or access denied.</div></div>";
    include 'includes/footer.php';
    exit;
}

$title_val = htmlspecialchars($ad['post_title']);
$price_val = htmlspecialchars($ad['price']);
$detail_val = htmlspecialchars($ad['post_detail']);

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