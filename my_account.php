<?php
require_once 'includes/db_connect.php';
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION['user_id'];
$username = htmlspecialchars($_SESSION['username']);

// Fetch Assets
$asset_stmt = $pdo->prepare("SELECT * FROM ads WHERE user_id = ? AND listing_type = 'OFFER' ORDER BY create_date DESC");
$asset_stmt->execute([$user_id]);
$my_assets = $asset_stmt->fetchAll();

// Fetch Wants
$want_stmt = $pdo->prepare("SELECT * FROM ads WHERE user_id = ? AND listing_type = 'WANTED' ORDER BY create_date DESC");
$want_stmt->execute([$user_id]);
$my_wants = $want_stmt->fetchAll();

function buildList($items, $is_asset) {
    $html = "";
    if (count($items) > 0) {
        $html .= '<div class="list-group">';
        foreach ($items as $item) {
            $title = htmlspecialchars($item['post_title']);
            $price = htmlspecialchars($item['price']);
            $id = $item['ad_id'];
            
            $extra_btn = "";
            if (!$is_asset) {
                $extra_btn = "<a href='index.php?q=$title' class='btn btn-sm btn-primary me-1'>Find Matches</a>";
            }
            
            $html .= <<<_ITEM
            <div class="list-group-item d-flex justify-content-between align-items-center">
                <div>
                    <strong class="d-block">$title</strong>
                    <small class="text-muted">$$price</small>
                </div>
                <div>
                    $extra_btn
                    <a href="edit_ad.php?id=$id" class="btn btn-sm btn-outline-secondary">Edit</a>
                    <a href="delete_ad.php?id=$id" class="btn btn-sm btn-outline-danger" onclick="return confirm('Remove this listing?')">Remove</a>
                </div>
            </div>
_ITEM;
        }
        $html .= '</div>';
    } else {
        $html = "<p class='text-muted text-center mt-3'>No listings found.</p>";
    }
    return $html;
}

$assets_html = buildList($my_assets, true);
$wants_html = buildList($my_wants, false);

include 'includes/header.php';

echo <<<_END
<div class="container mt-5">
    <div class="row mb-4">
        <div class="col-12 text-center">
            <h2>My Trade Profile</h2>
            <p class="text-muted">Welcome back, $username</p>
        </div>
    </div>

    <div class="row">
        <div class="col-md-6 mb-4">
            <div class="card border-success h-100 shadow-sm">
                <div class="card-header bg-success text-white d-flex justify-content-between align-items-center">
                    <h4 class="mb-0">My Assets (Haves)</h4>
                    <a href="post_ad.php" class="btn btn-sm btn-light text-success fw-bold">+ Add Asset</a>
                </div>
                <div class="card-body">
                    <p class="small text-muted">Items you own and are selling.</p>
                    $assets_html
                </div>
            </div>
        </div>

        <div class="col-md-6 mb-4">
            <div class="card border-warning h-100 shadow-sm">
                <div class="card-header bg-warning text-dark d-flex justify-content-between align-items-center">
                    <h4 class="mb-0">My Wants (Looking For)</h4>
                    <a href="post_ad.php" class="btn btn-sm btn-dark">+ Add Want</a>
                </div>
                <div class="card-body">
                    <p class="small text-muted">Items you are trying to find.</p>
                    $wants_html
                </div>
            </div>
        </div>
    </div>
</div>
_END;

include 'includes/footer.php';
?>