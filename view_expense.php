<?php
session_start();
require 'db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$expense_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

// Fetch expense details
$stmt = $conn->prepare("SELECT expenses.*, expense_categories.name as category_name, users.username as user_name, projects.name as project_name, invoices.invoice_number as invoice_number, expense_approvals.approver_id, users2.username as approver_name
                        FROM expenses
                        LEFT JOIN expense_categories ON expenses.category_id = expense_categories.id
                        LEFT JOIN users ON expenses.user_id = users.id
                        LEFT JOIN projects ON expenses.project_id = projects.id
                        LEFT JOIN invoices ON expenses.invoice_id = invoices.id
                         LEFT JOIN expense_approvals ON expenses.id = expense_approvals.expense_id
                        LEFT JOIN users as users2 ON expense_approvals.approver_id = users2.id
                        WHERE expenses.id = :id");
$stmt->bindParam(':id', $expense_id);
$stmt->execute();
$expense = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$expense) {
    header("Location: manage_expenses.php");
    exit();
}
$error = '';
$success = '';

// Include header
require 'header.php';
?>
<style>
.receipt {
    font-family: monospace;
    max-width: 400px;
    margin: 20px auto;
    padding: 20px 30px;
    background-color: #fff;
    border: 1px solid #ddd;
     box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
    position: relative;
        font-size: 14px;

}
.receipt::before {
     content: "";
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    z-index: -1;
    background-image: url('assets/bill-paper-bg.png');
     background-size: cover;
     transform: rotate(0.5deg);
}
.receipt h2 {
    text-align: center;
    margin-bottom: 10px;
        font-size: 1.3rem;

}

.receipt .info {
      display: flex;
    justify-content: space-between;
   font-size: 0.8rem;
}
.receipt .item {
    display: flex;
   justify-content: space-between;
       border-bottom: 1px dotted #ccc;
       padding-bottom: 3px;
       margin-bottom: 3px;
}
.receipt .item span:last-child {
    text-align: right;
      white-space: nowrap;
       
}

.receipt hr {
    border-top: 1px dashed #ddd;
        margin: 10px 0;
}

.receipt .total {
    display: flex;
    justify-content: space-between;
    font-size: 1.2rem;
    font-weight: bold;
      margin-top: 10px;

}
.receipt .total span:last-child {
     text-align: right;
}
.receipt p{
    font-size: 0.8rem;
      margin-bottom: 4px;
}
    .receipt .notes{
        font-size: 0.8rem;
        white-space: pre-line;

    }
    .receipt .tax {
       display: flex;
     justify-content: space-between;
        font-size: 0.9rem;

    }
    .expense-tag {
           display: inline-flex;
            align-items: center;
            padding: 0.25rem 0.5rem;
            border-radius: 0.375rem;
            font-size: 0.75rem;
            font-weight: 500;
            white-space: nowrap;
        }

</style>
<div class="container mx-auto p-6 fade-in">
    <h1 class="text-3xl font-bold text-gray-800 mb-6 text-center">Expense Details</h1>
    <?php if ($error): ?>
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-lg mb-6">
            <?php echo $error; ?>
        </div>
    <?php endif; ?>
    <?php if ($success || (isset($_GET['success']) && $_GET['success'] == 'true')): ?>
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded-lg mb-6">
            Expense record updated successfully!
        </div>
    <?php endif; ?>
    <div class="receipt">
        <h2 class="uppercase">
            <?php
            if ($expense['payment_mode'] == 'Cash') {
                echo 'Cash Receipt';
            } elseif ($expense['payment_mode'] == 'Credit Card') {
                echo 'Credit Card Receipt';
            } elseif ($expense['payment_mode'] == 'Bank Transfer') {
                echo 'Bank Transfer Receipt';
            } elseif ($expense['payment_mode'] == 'Online Payment') {
                echo 'Online Payment Receipt';
            } elseif ($expense['payment_mode'] == 'Check') {
                echo 'Check Receipt';
            } else {
                echo "Expense Receipt";
            }
            ?>
        </h2>
        <div class="info mb-2">
            <p>Expense Category:</p>
            <p><?php echo htmlspecialchars($expense['category_name'] ? $expense['category_name'] : 'Uncategorized'); ?></p>
        </div>
        <div class="info mb-2">
                <p>Nature of Transaction:</p>
               <p>
                   <span class="expense-tag <?php
                      switch ($expense['transaction_nature']) {
                          case 'Reimbursable':
                             echo 'bg-blue-100 text-blue-800';
                              break;
                          case 'Business Expense':
                             echo 'bg-green-100 text-green-800';
                             break;
                          case 'Personal Expense':
                              echo 'bg-yellow-100 text-yellow-800';
                              break;
                         default:
                            echo 'bg-gray-100 text-gray-800';
                              break;
                    }
                 ?>"><?php echo htmlspecialchars($expense['transaction_nature'] ? $expense['transaction_nature'] : 'N/A'); ?></span>
                 </p>
         </div>
        <div class="info mb-2">
            <p>Purchase Date:</p>
            <p><?php echo htmlspecialchars(date('m/d/Y', strtotime($expense['expense_date']))); ?></p>
        </div>
        <div class="info mb-2">
            <p>Expense Filed on:</p>
            <p><?php echo htmlspecialchars(date('m/d/Y', strtotime($expense['created_at']))); ?></p>
        </div>
        
        <hr>
        <div class="flex justify-between mb-2">
            <p class="font-bold">Description</p>
            <p class="font-bold">Price</p>
        </div>
        <div class="item">
            <span><?php echo htmlspecialchars($expense['name']); ?></span>
            <span>$<?php echo htmlspecialchars($expense['amount']); ?></span>
        </div>
        <hr>
        <div class="tax">
            <span>Tax</span>
            <span>$<?php echo '0.00'; ?></span>
        </div>
        <div class="total">
            <span>Total</span>
            <span>$<?php echo htmlspecialchars($expense['amount']); ?></span>
        </div>
        <hr>
        <?php if ($expense['invoice_number']): ?>
            <div class="mb-2 text-center">
                <p>
                    Associated Invoice ID : <a href="view_invoice.php?id=<?php echo $expense['invoice_id'] ?>" class="text-blue-600 hover:underline">#<?php echo htmlspecialchars($expense['invoice_number']); ?></a>
                </p>
            </div>
            <hr>
        <?php endif; ?>

        <?php if ($expense['approver_id'] && $expense['approver_name']): ?>
            <div class="mb-2 text-center">
                <p>
                    <strong>Approved By:</strong> <?php echo htmlspecialchars($expense['approver_name']); ?>
                </p>
            </div>
        <?php endif; ?>
        <?php if ($expense['notes']): ?>
            <div class="mt-2 mb-4">
                <p class="text-left font-bold text-gray-700">Notes</p>
                <p class="notes">
                    <?php echo nl2br(htmlspecialchars($expense['notes'])); ?>
                </p>
            </div>
        <?php endif; ?>
    </div>
    <div class="mt-4 flex justify-center gap-2">
        <a href="manage_expenses.php" class="bg-gray-600 text-white px-4 py-2 rounded-lg hover:bg-gray-700 transition duration-300 inline-block">Back to Expenses</a>
        <a href="edit_expense.php?id=<?php echo $expense['id']; ?>" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition duration-300 inline-block">Edit Expense</a>
    </div>
</div>
<?php
// Include footer
require 'footer.php';
?>