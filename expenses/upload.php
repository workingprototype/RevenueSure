<?php
require_once ROOT_PATH . 'helper/core.php';
redirectIfUnauthorized(true); // Requires user to be logged in

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_FILES['receipt']) && $_FILES['receipt']['error'] === 0) {
    $expense_id = $_POST['expense_id'];
    $file_name = basename($_FILES['receipt']['name']);
    $file_tmp = $_FILES['receipt']['tmp_name'];
    $file_path = "public/uploads/receipts/" . uniqid() . "_" . $file_name;

    if (!is_dir('public/uploads/receipts')) {
        mkdir('public/uploads/receipts', 0777, true);
    }

    if (move_uploaded_file($file_tmp, $file_path)) {
        $stmt = $conn->prepare("UPDATE expenses SET receipt_path = :receipt_path WHERE id = :expense_id");
        $stmt->bindParam(':receipt_path', $file_path);
        $stmt->bindParam(':expense_id', $expense_id);

        if ($stmt->execute()) {
            echo "<script>alert('Receipt uploaded successfully!'); window.location.href='<?php echo BASE_URL; ?>expenses/view?id=$expense_id';</script>";
            exit();
        } else {
            echo "<script>alert('Error updating receipt path.'); window.location.href='<?php echo BASE_URL; ?>expenses/view?id=$expense_id';</script>";
            exit();
        }
    } else {
        echo "<script>alert('Error moving receipt file.'); window.location.href='<?php echo BASE_URL; ?>expenses/view?id=$expense_id';</script>";
        exit();
    }
} else {
      echo "<script>alert('Error uploading receipt, please try again!'); window.location.href='<?php echo BASE_URL; ?>expenses/view?id=$expense_id';</script>";
       exit();
}
?>