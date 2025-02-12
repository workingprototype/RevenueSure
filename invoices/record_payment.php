<?php
require_once ROOT_PATH . 'helper/core.php';
redirectIfUnauthorized(true);

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['invoice_id'])) {
    $invoice_id = $_POST['invoice_id'];
    $payment_method = $_POST['payment_method'];
    $transaction_id = $_POST['transaction_id'];
    $amount = $_POST['amount'];

    // Fetch current invoice total and paid amounts, and invoice_number, billing_country
    $stmt = $conn->prepare("SELECT total, paid_amount, invoice_number, billing_country FROM invoices WHERE id = :invoice_id");
    $stmt->bindParam(':invoice_id', $invoice_id);
    $stmt->execute();
    $invoice = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($invoice) {
        $total = $invoice['total'];
        $paid_amount = $invoice['paid_amount'];
        $invoice_number = $invoice['invoice_number'];
        $billing_country = $invoice['billing_country'] ?? 'USD';  // Provide a default value

        $remaining_balance = $total - $paid_amount;

        if ($amount > $remaining_balance) {
            echo "<script>alert('Payment amount cannot be greater than remaining balance. Please try again.'); window.location.href='" . BASE_URL . "invoices/view?id=$invoice_id';</script>";
            exit();
        }

         $payment_date = date('Y-m-d H:i:s');
         $credit_amount = $amount;


        // Record payment
        $stmt = $conn->prepare("INSERT INTO payments (invoice_id, payment_method, transaction_id, amount) VALUES (:invoice_id, :payment_method, :transaction_id, :amount)");
        $stmt->bindValue(':invoice_id', $invoice_id);
        $stmt->bindValue(':payment_method', $payment_method);
        $stmt->bindValue(':transaction_id', $transaction_id);
        $stmt->bindValue(':amount', $amount);

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
                      // Create a ledger entry for the invoice payment

                        $stmt = $conn->prepare("INSERT INTO ledger_entries (transaction_date, transaction_id, description, credit_amount, currency, category, invoice_id, transaction_type) VALUES (:transaction_date, :transaction_id, :description, :credit_amount, :currency, :category, :invoice_id, 'Invoice')");
                     $stmt->bindValue(':transaction_date', $payment_date);
                    $stmt->bindValue(':transaction_id', $transaction_id);
                     $stmt->bindValue(':description', "Payment received for Invoice #" . $invoice_number);
                        $stmt->bindValue(':credit_amount', $credit_amount);
                        $stmt->bindValue(':currency', $billing_country);
                       $stmt->bindValue(':category', 'Revenue');  // or use something meaningful to you
                          $stmt->bindValue(':invoice_id', $invoice_id);

                          if ($stmt->execute()) {
                              // Send payment confirmation email
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
                                             echo "<script>alert('Payment recorded, and confirmation email sent!'); window.location.href='" . BASE_URL . "invoices/view?id=$invoice_id&success=true';</script>";
                                                exit();
                                           }
                                      }
                                      echo "<script>alert('Payment recorded!'); window.location.href='" . BASE_URL . "invoices/view?id=$invoice_id&success=true';</script>";
                                     exit();
                           } else {
                             echo "<script>alert('Error adding to ledger.'); window.location.href='" . BASE_URL . "invoices/view?id=$invoice_id';</script>";
                                exit();
                             }
                        }else{
                                echo "<script>alert('Error updating payment information on the invoice.'); window.location.href='" . BASE_URL . "invoices/view?id=$invoice_id';</script>";
                                exit();
                       }
                 }  else{
                    echo "<script>alert('Error recording payment.'); window.location.href='" . BASE_URL . "invoices/view?id=$invoice_id';</script>";
                    exit();
                }
    } else {
        echo "<script>alert('Invoice not found.');</script>";
       header("Location: " . BASE_URL . "invoices/manage");
       exit();
    }
} else {
      header("Location: " . BASE_URL . "invoices/manage");
     exit();
}
?>