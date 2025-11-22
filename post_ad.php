<?php
require_once 'includes/db_connect.php';
session_start();

// Verify authentication
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$error_msg = "";
$success_msg = "";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $title = trim($_POST['title']);
    $price = $_POST['price'];
    $details = trim($_POST['details']);
    $category = $_POST['category'];
    $type = $_POST['type'];
    $user_id = $_SESSION['user_id'];

    if (empty($title) || empty($price)) {
        $error_msg = "<div class='alert alert-danger'>Title and Price are required.</div>";
    } else {
        try {
            // Insert record
            $sql = "INSERT INTO ads (user_id, category_id, post_title, post_detail, price, listing_type) 
                    VALUES (?, ?, ?, ?, ?, ?)";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([$user_id, $category, $title, $details, $price, $type]);
            
            $ad_id = $pdo->lastInsertId();

            // Process file upload
            if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
                $allowed = ['jpg', 'jpeg', 'png', 'gif'];
                $filename = $_FILES['image']['name'];
                $ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));

                if (in_array($ext, $allowed)) {
                    $new_name = uniqid() . "." . $ext;
                    $dest = "uploads/" . $new_name;

                    if (move_uploaded_file($_FILES['image']['tmp_name'], $dest)) {
                        $img_sql = "INSERT INTO images (ad_id, image_url, is_main_image) VALUES (?, ?, 1)";
                        $pdo->prepare($img_sql)->execute([$ad_id, $new_name]);
                    }
                }
            }

            $success_msg = "<div class='alert alert-success'>Ad posted successfully! <a href='index.php'>View it here</a>.</div>";

        } catch (PDOException $e) {
            $error_msg = "<div class='alert alert-danger'>Database Error: " . $e->getMessage() . "</div>";
        }
    }
}

// Populate categories
$cat_stmt = $pdo->query("SELECT * FROM categories");
$categories = $cat_stmt->fetchAll();

$cat_options = "";
foreach ($categories as $cat) {
    $cat_options .= "<option value='{$cat['category_id']}'>{$cat['category_name']}</option>";
}

include 'includes/header.php';

echo <<<_END
<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow">
                <div class="card-header bg-primary text-white">
                    <h4 class="mb-0">Post a New Ad</h4>
                </div>
                <div class="card-body">
                    $error_msg
                    $success_msg

                    <form action="post_ad.php" method="POST" enctype="multipart/form-data">
                        <div class="mb-4 p-3 bg-light border rounded">
                            <label class="form-label fw-bold">What is this listing?</label>
                            <select name="type" class="form-select">
                                <option value="OFFER">I HAVE this item (Selling)</option>
                                <option value="WANTED">I WANT this item (Buying)</option>
                                <option value="EXCHANGE">I want to TRADE (Barter)</option>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Ad Title</label>
                            <input type="text" name="title" class="form-control" required placeholder="e.g. Calculus Textbook 3rd Ed">
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Category</label>
                                <select name="category" class="form-select">
                                    $cat_options
                                </select>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Price ($)</label>
                                <input type="number" name="price" class="form-control" step="0.01" required>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Description</label>
                            <textarea name="details" class="form-control" rows="4" placeholder="Describe condition, age, etc."></textarea>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Upload Image</label>
                            <input type="file" name="image" class="form-control" accept="image/*">
                            <small class="text-muted">Max size 2MB. JPG/PNG only.</small>
                        </div>

                        <button type="submit" class="btn btn-success w-100 transition-btn">Post Ad</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
_END;

include 'includes/footer.php';
?>