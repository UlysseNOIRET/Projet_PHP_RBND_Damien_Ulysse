<?php
require_once 'config.php';
 
$perPage = 10;
$page = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
 
$validSorts = [
    'name' => 'name',
    'city' => 'neighbourhood_group_cleansed',
    'price' => 'price',
    'host' => 'host_name'
];

$sort = isset($_GET['sort']) && isset($validSorts[$_GET['sort']]) ? $_GET['sort'] : 'name';
$sortColumn = $validSorts[$sort];

$order = isset($_GET['order']) && $_GET['order'] === 'desc' ? 'desc' : 'asc';

$totalStmt = $dbh->query("SELECT COUNT(*) AS c FROM listings");
$total = $totalStmt->fetch()['c'];
$pages = max(1, ceil($total / $perPage));

$offset = ($page - 1) * $perPage;
$sql = "SELECT id, name, picture_url, host_name, host_thumbnail_url, price, neighbourhood_group_cleansed, review_scores_value
        FROM listings
        ORDER BY $sortColumn $order
        LIMIT :limit OFFSET :offset";

$stmt = $dbh->prepare($sql);
$stmt->bindValue(':limit', $perPage, PDO::PARAM_INT);
$stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
$stmt->execute();
$listings = $stmt->fetchAll();

$message = "";
if (isset($_SESSION['flash'])) {
    $message = $_SESSION['flash'];
    unset($_SESSION['flash']);
}

function urlWithSort($sort, $order, $page) {
    return "?sort=$sort&order=$order&page=$page";
}

$nextOrder = $order === 'asc' ? 'desc' : 'asc';

$arrow = $order === 'asc' ? "▲" : "▼";
?>
