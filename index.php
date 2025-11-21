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
?>