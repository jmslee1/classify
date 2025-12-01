<?php
/*
 * CST8238 - Final Project: Classify Marketplace
 * Description: Main landing page displaying active ad listings.
 */

require_once 'includes/db_connect.php';
include 'includes/header.php';

// Handle Search logic
$search_term = isset($_GET['q']) ? $_GET['q'] : '';
$sql = "SELECT ads.*, images.image_url FROM ads LEFT JOIN  images ON ads.ad_id = images.ad_id WHERE is_active = 1";
$params = [];

if ($search_term) {
    $sql .= " AND (post_title LIKE ? OR post_detail LIKE ?)";
    $params[] = "%$search_term%";
}

$sql .= " ORDER BY create_date DESC";
$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$ads = $stmt->fetchAll();

// Generate grid items
$ads_html = "";
foreach($ads as $ad) {
    $id = $ad['ad_id'];
    $title = htmlspecialchars($ad['post_title']);
    $price = htmlspecialchars($ad['price']);
    $desc = htmlspecialchars(substr($ad['post_detail'], 0, 80)) . "...";
    
    if(isset($ad['image_url']))
        $image = "uploads/".$ad['image_url'];
    else
        $image = "uploads/no-image.jpg";
    
    // Badge logic
    if ($ad['listing_type'] == 'WANTED') {
        $badge = "<span class='badge bg-warning text-dark'>WANTED</span>";
    } else {
        $badge = "<span class='badge bg-success'>SELLING</span>";
    }

    // Construct Card HTML
    $ads_html .= <<<_CARD
    <div class="col-12 col-md-6 col-lg-4 mb-4">
        <div class="card h-100 shadow-sm ad-card-custom">
            <div class="card-header bg-transparent border-bottom-0 pt-3">
                $badge
            </div>
            <img src="$image" class="card-img-top" alt="$title">
            <div class="card-body d-flex flex-column">
                <h5 class="card-title">$title</h5>
                <h6 class="text-primary fw-bold">$$price</h6>
                <p class="card-text text-muted flex-grow-1">$desc</p>
                <a href="ad_details.php?id=$id" class="btn btn-outline-primary mt-auto transition-btn">View Details</a>
            </div>
        </div>
    </div>
_CARD;
}

if (empty($ads_html)) {
    $ads_html = "<div class='col-12 text-center'><p>No active listings found.</p></div>";
}

// Page Output
echo <<<_END
<div class="bg-custom-hero text-white text-center py-5 mb-5">
    <div class="container">
        <h1 class="display-4 fw-bold">Classify Marketplace</h1>
        <p class="lead">Buy, Sell, and Trade on Campus</p>
        <form class="d-flex justify-content-center mt-4" action="index.php" method="get">
            <input class="form-control w-50 me-2" type="search" name="q" placeholder="Search items..." value="$search_term">
            <button class="btn btn-light text-primary" type="submit">Search</button>
        </form>
    </div>
</div>

<div class="container">
    <h3 class="mb-4 border-bottom pb-2">Latest Listings</h3>
    <div class="row">
        $ads_html
    </div>
</div>
_END;

include 'includes/footer.php';
?>
