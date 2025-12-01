<?php
require_once 'includes/db_connect.php';
session_start();

// 1. Check Login
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

if (isset($_GET['id'])) {
    $ad_id = $_GET['id'];
    $user_id = $_SESSION['user_id'];

    // 2. Security: Ensure the user OWNS this ad before deleting
    //

    $check = $pdo->prepare("SELECT ads.ad_id, images.image_url FROM ads LEFT JOIN images ON images.ad_id = ads.ad_id WHERE ads.ad_id = ? AND ads.user_id = ?");
    $check->execute([$ad_id, $user_id]);

    $ad = $check->fetch();

    if ($check->rowCount() > 0) {
        // 3. Delete
        $sql = "DELETE FROM ads ";

        if(isset($ad['image_url']))
        {
            $sql .= "LEFT JOIN images ON ads.ad_id = images.ad_id ";
            unlink("uploads/".$ad['image_url']);
        }

        $sql .= "WHERE ad_id = ?";
        
        $delete = $pdo->prepare("DELETE FROM ads WHERE ad_id = ?");
        $delete->execute([$ad_id]);
        
        header("Location: my_account.php?msg=Ad deleted successfully");
        exit;
    } else {
        die("Error: You do not have permission to delete this ad.");
    }
} else {
    header("Location: my_account.php");
    exit;
}
?>
