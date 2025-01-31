<?php
    // Fetch all categories
    $stmt = $conn->prepare("SELECT * FROM expense_categories");
    $stmt->execute();
    $categories = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Fetch all users
    $stmt = $conn->prepare("SELECT id, username FROM users WHERE role = 'user' OR role = 'admin'");
    $stmt->execute();
    $users = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Fetch projects for dropdown
    $stmt = $conn->prepare("SELECT id, name FROM projects");
    $stmt->execute();
    $projects = $stmt->fetchAll(PDO::FETCH_ASSOC);
    // Fetch invoices for dropdown
    $stmt = $conn->prepare("SELECT id, invoice_number FROM invoices");
    $stmt->execute();
    $invoices = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
 <div class="flex flex-wrap gap-2">
     <form method="GET" action="" class="flex gap-2 flex-wrap">
           <input type="text" name="search" id="search" placeholder="Search" class="px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-600 flex-1" value="<?php echo isset($_GET['search']) ? $_GET['search'] : '' ?>">
                <select name="category_id" id="category_id" class="px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-600 appearance-none">
                    <option value="">Select Category</option>
                        <?php foreach ($categories as $category): ?>
                              <option value="<?php echo $category['id']; ?>" <?php if(isset($_GET['category_id']) && $_GET['category_id'] == $category['id']) echo 'selected'; ?> ><?php echo htmlspecialchars($category['name']); ?></option>
                           <?php endforeach; ?>
                 </select>
                  <select name="user_id" id="user_id" class="px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-600 appearance-none">
                      <option value="">Select User</option>
                      <?php foreach ($users as $user): ?>
                           <option value="<?php echo $user['id']; ?>" <?php if(isset($_GET['user_id']) && $_GET['user_id'] == $user['id']) echo 'selected'; ?> ><?php echo htmlspecialchars($user['username']); ?></option>
                       <?php endforeach; ?>
                  </select>
                    <select name="project_id" id="project_id" class="px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-600 appearance-none">
                         <option value="">Select Project</option>
                         <?php foreach ($projects as $project): ?>
                            <option value="<?php echo $project['id']; ?>" <?php if(isset($_GET['project_id']) && $_GET['project_id'] == $project['id']) echo 'selected'; ?>><?php echo htmlspecialchars($project['name']); ?></option>
                          <?php endforeach; ?>
                 </select>
                    <select name="invoice_id" id="invoice_id" class="px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-600 appearance-none">
                             <option value="">Select Invoice</option>
                                 <?php foreach ($invoices as $invoice): ?>
                                     <option value="<?php echo $invoice['id']; ?>"  <?php if(isset($_GET['invoice_id']) && $_GET['invoice_id'] == $invoice['id']) echo 'selected'; ?>><?php echo htmlspecialchars($invoice['invoice_number']); ?></option>
                                <?php endforeach; ?>
                       </select>
                       <select name="payment_mode" id="payment_mode" class="px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-600 appearance-none">
                            <option value="">Select Payment Mode</option>
                            <option value="Cash" <?php if(isset($_GET['payment_mode']) && $_GET['payment_mode'] == 'Cash') echo 'selected'; ?>>Cash</option>
                           <option value="Credit Card" <?php if(isset($_GET['payment_mode']) && $_GET['payment_mode'] == 'Credit Card') echo 'selected'; ?>>Credit Card</option>
                            <option value="Bank Transfer" <?php if(isset($_GET['payment_mode']) && $_GET['payment_mode'] == 'Bank Transfer') echo 'selected'; ?>>Bank Transfer</option>
                              <option value="Online Payment" <?php if(isset($_GET['payment_mode']) && $_GET['payment_mode'] == 'Online Payment') echo 'selected'; ?>>Online Payment</option>
                             <option value="Check" <?php if(isset($_GET['payment_mode']) && $_GET['payment_mode'] == 'Check') echo 'selected'; ?>>Check</option>
                      </select>
                        <select name="transaction_nature" id="transaction_nature" class="px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-600 appearance-none">
                            <option value="">Select Transaction</option>
                             <option value="Reimbursable" <?php if(isset($_GET['transaction_nature']) && $_GET['transaction_nature'] == 'Reimbursable') echo 'selected'; ?>>Reimbursable</option>
                           <option value="Business Expense" <?php if(isset($_GET['transaction_nature']) && $_GET['transaction_nature'] == 'Business Expense') echo 'selected'; ?>>Business Expense</option>
                             <option value="Personal Expense" <?php if(isset($_GET['transaction_nature']) && $_GET['transaction_nature'] == 'Personal Expense') echo 'selected'; ?>>Personal Expense</option>
                         </select>
                         <input type="date" name="start_date" id="start_date" class="px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-600" placeholder="Start Date" value="<?php echo isset($_GET['start_date']) ? $_GET['start_date'] : '' ?>">
                         <input type="date" name="end_date" id="end_date" class="px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-600" placeholder="End Date" value="<?php echo isset($_GET['end_date']) ? $_GET['end_date'] : '' ?>">
                       <button type="submit" class="bg-blue-700 text-white px-4 py-2 rounded-lg hover:bg-blue-900 transition duration-300 shadow-md">Filter</button>
                 </form>
                 <a href="manage_expenses.php" class="bg-gray-700 text-white px-4 py-2 rounded-lg hover:bg-gray-900 transition duration-300 shadow-md">Clear Filter</a>
           </div>