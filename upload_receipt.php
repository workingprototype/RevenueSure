<?php
session_start();
require 'db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_FILES['receipt']) && $_FILES['receipt']['error'] === 0) {
    $expense_id = $_POST['expense_id'];
    $file_name = basename($_FILES['receipt']['name']);
    $file_tmp = $_FILES['receipt']['tmp_name'];
    $file_path = "uploads/receipts/" . uniqid() . "_" . $file_name;

    if (!is_dir('uploads/receipts')) {
        mkdir('uploads/receipts', 0777, true);
    }

    if (move_uploaded_file($file_tmp, $file_path)) {
        $stmt = $conn->prepare("UPDATE expenses SET receipt_path = :receipt_path WHERE id = :expense_id");
        $stmt->bindParam(':receipt_path', $file_path);
        $stmt->bindParam(':expense_id', $expense_id);

        if ($stmt->execute()) {
            echo "<script>alert('Receipt uploaded successfully!'); window.location.href='view_expense.php?id=$expense_id';</script>";
            exit();
        } else {
            echo "<script>alert('Error updating receipt path.'); window.location.href='view_expense.php?id=$expense_id';</script>";
            exit();
        }
    } else {
        echo "<script>alert('Error moving receipt file.'); window.location.href='view_expense.php?id=$expense_id';</script>";
        exit();
    }
} else {
      echo "<script>alert('Error uploading receipt, please try again!'); window.location.href='view_expense.php?id=$expense_id';</script>";
       exit();
}
?>