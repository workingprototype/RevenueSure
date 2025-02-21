<div class="bg-white p-6 rounded-2xl shadow-xl overflow-hidden">
        <table class="w-full text-left">
             <thead class="bg-gray-50">
                 <tr>
                      <th class="px-4 py-3">Name</th>
                      <th class="px-4 py-3">Category</th>
                     <th class="px-4 py-3">Date</th>
                        <th class="px-4 py-3">User</th>
                      <th class="px-4 py-3">Payment Mode</th>
                       <th class="px-4 py-3">Nature</th>
                       <th class="px-4 py-3">Amount</th>
                      <th class="px-4 py-3">Actions</th>
                  </tr>
              </thead>
            <tbody>
                 <?php if ($expenses): ?>
                    <?php foreach ($expenses as $expense): ?>
                       <tr class="border-b transition hover:bg-gray-100">
                            <td class="px-4 py-3"><?php echo htmlspecialchars($expense['name']); ?></td>
                             <td class="px-4 py-3"><?php echo htmlspecialchars($expense['category_name'] ? $expense['category_name'] : 'Uncategorized'); ?></td>
                            <td class="px-4 py-3"><?php echo htmlspecialchars($expense['expense_date']); ?></td>
                              <td class="px-4 py-3"><?php echo htmlspecialchars($expense['user_name']); ?></td>
                             <td class="px-4 py-3"> <span class="px-2 py-1 rounded-full <?php
                                    switch ($expense['payment_mode']) {
                                       case 'Cash':
                                            echo 'bg-green-100 text-green-800';
                                            break;
                                         case 'Credit Card':
                                            echo 'bg-yellow-100 text-yellow-800';
                                             break;
                                         case 'Bank Transfer':
                                            echo 'bg-purple-100 text-purple-800';
                                               break;
                                         case 'Online Payment':
                                           echo 'bg-blue-100 text-blue-800';
                                              break;
                                        case 'Check':
                                            echo 'bg-gray-100 text-gray-800';
                                               break;
                                       default:
                                           echo 'bg-gray-100 text-gray-800';
                                            break;
                                     }
                                 ?>"><?php echo htmlspecialchars($expense['payment_mode']); ?></span></td>
                            <td class="px-4 py-3"><span class="px-2 py-1 rounded-full <?php
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
                             ?>"><?php echo htmlspecialchars($expense['transaction_nature']); ?></span></td>
                            <td class="px-4 py-3">$<?php echo htmlspecialchars($expense['amount']); ?></td>
                             <td class="px-4 py-3 flex gap-2">
                                <a href="<?php echo BASE_URL; ?>expenses/view?id=<?php echo $expense['id']; ?>" class="text-purple-600 hover:underline"> <i class="fas fa-eye"></i></a>
                                  <a href="<?php echo BASE_URL; ?>expenses/edit?id=<?php echo $expense['id']; ?>" class="text-blue-600 hover:underline"> <i class="fas fa-edit"></i></a>
                                 <a href="<?php echo BASE_URL; ?>expenses/delete?id=<?php echo $expense['id']; ?>" class="text-red-600 hover:underline"> <i class="fas fa-trash-alt"></i></a>
                             </td>
                        </tr>
                    <?php endforeach; ?>
               <?php else: ?>
                     <tr>
                         <td colspan="8" class="px-4 py-2 text-center text-gray-600">No expenses found.</td>
                     </tr>
                   <?php endif; ?>
          </tbody>
       </table>
    </div>