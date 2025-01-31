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
$show_success_toast = false;

// Fetch comments for the expense
$stmt = $conn->prepare("SELECT expense_comments.*, users.username FROM expense_comments INNER JOIN users ON expense_comments.user_id = users.id WHERE expense_id = :expense_id ORDER BY created_at ASC");
$stmt->bindParam(':expense_id', $expense_id);
$stmt->execute();
$comments = $stmt->fetchAll(PDO::FETCH_ASSOC);

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['add_comment'])) {
        $comment = $_POST['comment'];
        if (!empty($comment)) {
            $stmt = $conn->prepare("INSERT INTO expense_comments (expense_id, user_id, comment) VALUES (:expense_id, :user_id, :comment)");
            $stmt->bindParam(':expense_id', $expense_id);
             $stmt->bindParam(':user_id', $_SESSION['user_id']);
             $stmt->bindParam(':comment', $comment);

            if($stmt->execute()) {
              $success = "Comment added successfully!";
              $show_success_toast = true;
                  
            } else {
              $error = "Error adding comment.";
            }
         } else {
            $error = "Comment cannot be empty.";
        }
    }
}

// Include header
require 'header.php';
?>
<style>
    .receipt {
        font-family: monospace;
        max-width: 400px;
        margin: 20px auto;
        padding: 20px 30px;
        background-color: rgba(255, 255, 255, 0.3);
        border: 1px solid rgba(255, 255, 255, 0.2);
         box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        position: relative;
            font-size: 14px;
    backdrop-filter: blur(10px);
        border-radius: 15px;
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
    <?php if ($success && !$show_success_toast && (!isset($_GET['success']) || $_GET['success'] != 'true')): ?>
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded-lg mb-6">
              Expense record updated successfully!
          </div>
    <?php endif; ?>
       <div class="flex flex-wrap -mx-4 justify-center">
          <div class="w-full md:w-3/4 px-4 mb-6">
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
                    <div class="bg-white border-2 border-gray-100 p-6 rounded-lg mt-8 backdrop-filter backdrop-blur-sm bg-opacity-30">
                      <h2 class="text-xl font-bold text-gray-800 mb-4 relative">
                            Comments
                        </h2>
                         <form method="POST" action="" class="mb-4">
                              <textarea name="comment" id="comment" class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-600" placeholder="Write a comment..."></textarea>
                             <button type="submit" name="add_comment" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition duration-300 mt-4">Add Comment</button>
                       </form>
                          <?php if($comments): ?>
                                <h2 class="text-xl font-bold text-gray-800 mt-6 mb-4">Comments</h2>
                                <ul id="comment-list">
                                  <?php
                                      foreach ($comments as $comment) {
                                               echo '<li class="p-4 border-b border-gray-100 my-2 bg-gray-50 rounded-lg">
                                                        <div class="flex justify-between items-center mb-2">
                                                            <p class="text-gray-800">' . htmlspecialchars($comment['comment']) . '</p>
                                                       </div>
                                                        <div class="text-right">
                                                            <p class="text-gray-500 text-sm">
                                                                 <i class="fas fa-user-circle mr-1"></i> '. htmlspecialchars($comment['username']) .' - '. htmlspecialchars(date('Y-m-d H:i', strtotime($comment['created_at']))) . '</p>
                                                       </div>
                                                 ';
                                            echo '</li>';
                                        }
                                     ?>
                            </ul>
                         <?php else: ?>
                               <p class="text-gray-600">No comments yet!</p>
                            <?php endif; ?>
                  </div>
           </div>
          <div class="w-full md:w-1/4 px-4 mb-6">
              <div class="bg-white border-2 border-gray-100 p-6 rounded-lg backdrop-filter backdrop-blur-sm bg-opacity-30">
                    <h2 class="text-xl font-bold text-gray-800 mb-4">Receipt</h2>
                       <?php if ($expense['receipt_path']) : ?>
                           <div class="flex flex-col items-center">
                              <img src="<?php echo $expense['receipt_path']; ?>" alt="Expense Receipt" class="max-w-full h-auto object-contain max-h-48 mb-4">
                                  <a href="<?php echo $expense['receipt_path']; ?>" download class="text-blue-600 hover:underline">
                                     Download Receipt
                                  </a>
                           </div>
                       <?php else : ?>
                           <p class="text-gray-600">No receipt attached.</p>
                       <?php endif; ?>
                </div>
           </div>
     </div>
      <div class="mt-4 flex justify-center gap-2">
         <a href="manage_expenses.php" class="bg-gray-600 text-white px-4 py-2 rounded-lg hover:bg-gray-700 transition duration-300 inline-block">Back to Expenses</a>
         <a href="edit_expense.php?id=<?php echo $expense['id']; ?>" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition duration-300 inline-block">Edit Expense</a>
       </div>
</div>
   <!-- Success Toast -->
     <div
           id="toast"
           class="fixed top-12 left-1/2 transform -translate-x-1/2 z-50 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded-xl mb-4 hidden"
             role="alert"
              >
             <div class="flex items-center">
                  <span class="mr-2"><i class="fas fa-check-circle"></i></span>
                  <span>Comment added successfully!</span>
                      <button onclick="closeToast()" class="ml-2 text-gray-600 hover:text-gray-800" > <i class="fas fa-times"></i></button>
               </div>
        </div>
<script>
    function closeToast(){
        document.getElementById('toast').classList.add('hidden');
    }
    <?php if ($show_success_toast): ?>
         document.addEventListener('DOMContentLoaded', function() {
              document.getElementById('toast').classList.remove('hidden');
               setTimeout(() => {
                      document.getElementById('toast').classList.add('hidden');
                    }, 3000);
            });
      <?php endif; ?>
</script>
<?php
// Include footer
require 'footer.php';
?>