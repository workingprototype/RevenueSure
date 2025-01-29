<?php
session_start();
require 'db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}
$query = isset($_GET['query']) ? trim($_GET['query']) : '';
header('Content-Type: application/json');

// In a real application, you might fetch products/services from the database.
// Here, let's provide sample product data for demonstration
    if(!empty($query)){
        $products = [
            ['id' => 1, 'name' => 'Website Development', 'unit_price' => 1500.00],
            ['id' => 2, 'name' => 'SEO Services', 'unit_price' => 800.00],
            ['id' => 3, 'name' => 'Marketing Package', 'unit_price' => 1200.00],
            ['id' => 4, 'name' => 'Consulting', 'unit_price' => 100.00],
              ['id' => 5, 'name' => 'Mobile App Development', 'unit_price' => 1500.00],
            ['id' => 6, 'name' => 'Content Writing', 'unit_price' => 500.00],
            ['id' => 7, 'name' => 'Social Media Management', 'unit_price' => 900.00],
            ['id' => 8, 'name' => 'Graphic Design', 'unit_price' => 300.00],
            ['id' => 9, 'name' => 'Email Marketing', 'unit_price' => 500.00],
            ['id' => 10, 'name' => 'Training', 'unit_price' => 250.00],
        ];
        $results = array_filter($products, function($product) use ($query){
            return stripos($product['name'], $query) !== false;
        });
       echo json_encode(array_values($results));
    } else {
       echo json_encode([]);
    }