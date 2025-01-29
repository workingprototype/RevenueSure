<?php
session_start();
require 'db.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['invoice_id'])) {
    $invoice_id = $_POST['invoice_id'];
    $payment_method = $_POST['payment_method'];
     $transaction_id = $_POST['transaction_id'];
    $amount = $_POST['amount'];

    // Fetch current invoice total and paid amounts
    $stmt = $conn->prepare("SELECT total, paid_amount FROM invoices WHERE id = :invoice_id");
    $stmt->bindParam(':invoice_id', $invoice_id);
    $stmt->execute();
    $invoice = $stmt->fetch(PDO::FETCH_ASSOC);

   if ($invoice) {
            $total = $invoice['total'];
            $paid_amount = $invoice['paid_amount'];
            $remaining_balance = $total - $paid_amount;
            if ($amount > $remaining_balance) {
                echo "<script>alert('Payment amount cannot be greater than remaining balance. Please try again.'); window.location.href='view_invoice.php?id=$invoice_id';</script>";
                exit;
            }

       // Record payment
        $stmt = $conn->prepare("INSERT INTO payments (invoice_id, payment_method, transaction_id, amount) VALUES (:invoice_id, :payment_method, :transaction_id, :amount)");
        $stmt->bindParam(':invoice_id', $invoice_id);
         $stmt->bindParam(':payment_method', $payment_method);
           $stmt->bindParam(':transaction_id', $transaction_id);
        $stmt->bindParam(':amount', $amount);
       if ($stmt->execute()) {
                //Update paid amount and status for invoice

               $new_paid_amount = $paid_amount + $amount;
               $invoice_status = 'Partially Paid';
                  if($new_paid_amount >= $total){
                         $invoice_status = 'Paid';
                  }

                    $stmt = $conn->prepare("UPDATE invoices SET paid_amount = :paid_amount, status = :status, payment_date = NOW() WHERE id = :invoice_id");
                  $stmt->bindParam(':invoice_id', $invoice_id);
                 $stmt->bindParam(':paid_amount', $new_paid_amount);
                 $stmt->bindParam(':status', $invoice_status);

                    if($stmt->execute()){
                        // Send payment confirmation
                          $stmt = $conn->prepare("SELECT bill_to_email FROM invoices WHERE id = :invoice_id");
                        $stmt->bindParam(':invoice_id', $invoice_id);
                           $stmt->execute();
                         $email = $stmt->fetch(PDO::FETCH_ASSOC)['bill_to_email'];
                        if($email) {
                            $to = $email;
                            $subject = "Payment Confirmation";
                            $message = "Your payment of $amount has been received successfully.";
                            $headers = "From: noreply@revenuesure.com";
                            if(mail($to, $subject, $message, $headers)) {
                                 echo "<script>alert('Payment recorded, and confirmation email sent!'); window.location.href='view_invoice.php?id=$invoice_id&success=true';</script>";
                                exit;
                            }
                         }
                            echo "<script>alert('Payment recorded!'); window.location.href='view_invoice.php?id=$invoice_id&success=true';</script>";
                                exit;
                    }else{
                        echo "<script>alert('Error updating payment information on the invoice.'); window.location.href='view_invoice.php?id=$invoice_id';</script>";
                       exit;
                    }
         }  else{
            echo "<script>alert('Error recording payment.'); window.location.href='view_invoice.php?id=$invoice_id';</script>";
            exit;
        }
   } else {
        echo "<script>alert('Invoice not found.');</script>";
         header("Location: manage_invoices.php");
        exit();
    }
} else {
   header("Location: manage_invoices.php");
        exit();
}

?>