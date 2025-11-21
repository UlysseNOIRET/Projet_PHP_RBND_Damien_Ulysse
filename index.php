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
<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="utf-8">
<title>Listings</title>
<style>
    body { font-family: Arial; margin:20px; }
    .listing { border:1px solid #ccc; padding:10px; margin-bottom:10px; }
    .listing img { width:120px; height:90px; object-fit:cover; }
    .pagination a { margin:0 4px; text-decoration:none; padding:4px 8px; border:1px solid #aaa; }
    .current { background:#d00; color:white; }
    .message { background:#e0ffe0; border:1px solid #8f8; padding:8px; margin-bottom:10px; }
    .sort { margin-bottom:15px; }
    .add-form { border:1px dashed #aaa; padding:10px; margin-top:20px; }
</style>
</head>
<body>
 
<h1>Liste des logements</h1>
 
<?php if ($message): ?>
<div class="message"><?= htmlspecialchars($message) ?></div>
<?php endif; ?>
 
<div class="sort">
<strong>Trier par :</strong>
<a href="<?= urlWithSort('name', $sort==='name'?$nextOrder:'asc', 1) ?>">Nom <?= $sort==='name' ? $arrow : '' ?></a> |
<a href="<?= urlWithSort('city', $sort==='city'?$nextOrder:'asc', 1) ?>">Ville <?= $sort==='city' ? $arrow : '' ?></a> |
<a href="<?= urlWithSort('price', $sort==='price'?$nextOrder:'asc', 1) ?>">Prix <?= $sort==='price' ? $arrow : '' ?></a> |
<a href="<?= urlWithSort('host', $sort==='host'?$nextOrder:'asc', 1) ?>">Propriétaire <?= $sort==='host' ? $arrow : '' ?></a>
</div>

<?php foreach ($listings as $l): ?>
<div class="listing">
    <img src="<?= htmlspecialchars($l['picture_url']) ?>" alt="">
    <h3><?= htmlspecialchars($l['name']) ?></h3>
    <p>Hôte : <?= htmlspecialchars($l['host_name']) ?></p>
    <p>Ville : <?= htmlspecialchars($l['neighbourhood_group_cleansed']) ?></p>
    <p>Prix : <?= htmlspecialchars($l['price']) ?> € / nuit</p>
</div>
<?php endforeach; ?>

<div class="pagination">
    <?php if ($page > 1): ?>
        <a href="<?= urlWithSort($sort, $order, $page-1) ?>">Précédent</a>
    <?php endif; ?>

    <?php for ($i = 1; $i <= $pages; $i++): ?>
        <a class="<?= $i==$page?'current':'' ?>" href="<?= urlWithSort($sort, $order, $i) ?>"><?= $i ?></a>
    <?php endfor; ?>

    <?php if ($page < $pages): ?>
        <a href="<?= urlWithSort($sort, $order, $page+1) ?>">Suivant</a>
    <?php endif; ?>
</div>

<div class="add-form">
    <h2>Ajouter une annonce</h2>
    <form method="post" action="add.php">
        <p><label>Nom : </label> <input type="text" name="name" required></p>
        <p><label>Image : </label> <input type="text" name="picture_url" required></p>
        <p><label>Propriétaire : </label> <input type="text" name="host_name" required></p>
        <p><label>Avatar : </label> <input type="text" name="host_thumbnail_url"></p>
        <p><label>Prix : </label> <input type="number" name="price" required></p>
        <p><label>Ville : </label> <input type="text" name="neighbourhood_group_cleansed" required></p>
        <p><label>Note : </label> <input type="number" name="review_scores_value"></p>
        <button type="submit">Ajouter</button>
    </form>
</div>

</body>
</html>
