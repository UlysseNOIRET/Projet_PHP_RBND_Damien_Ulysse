<?php
require_once 'config.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: index.php");
    exit;
}

$name = trim($_POST['name']);
$picture = trim($_POST['picture_url']);
$host = trim($_POST['host_name']);
$avatar = trim($_POST['host_thumbnail_url']);
$price = intval($_POST['price']);
$city = trim($_POST['neighbourhood_group_cleansed']);
$note = $_POST['review_scores_value'] !== "" ? floatval($_POST['review_scores_value']) : null;
 
$max = $dbh->query("SELECT MAX(id) AS m FROM listings")->fetch()['m'];
$newId = $max + 1;