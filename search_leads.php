<?php
session_start();
require 'db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Include header
require 'header.php';
?>
<div class="container mx-auto p-6 fade-in">
    <h1 class="text-4xl font-bold text-gray-900 mb-6">Search Leads</h1>

    <!-- Search Form -->
    <div class="bg-white p-6 rounded-2xl shadow-xl mb-8">
            <!-- Search by Name, Email, or Phone -->
         <div class="mb-4">
              <input type="text" id="search_input" placeholder="Search by name, email, or phone" class="w-full px-4 py-3 border rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-600">
        </div>
      
        <!-- Filters -->
        <div class="flex flex-wrap gap-4 mb-4">
            <select id="category_id" class="px-4 py-3 border rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-600">
                <option value="">All Categories</option>
                <?php
                $stmt = $conn->prepare("SELECT * FROM categories");
                $stmt->execute();
                $categories = $stmt->fetchAll(PDO::FETCH_ASSOC);
                foreach ($categories as $category) : ?>
                    <option value="<?php echo $category['id']; ?>">
                        <?php echo htmlspecialchars($category['name']); ?>
                    </option>
                <?php endforeach; ?>
            </select>
             <select id="status" class="px-4 py-3 border rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-600">
                <option value="">All Statuses</option>
                <option value="New">New</option>
                <option value="Contacted">Contacted</option>
                <option value="Converted">Converted</option>
             </select>
             <input type="date" id="start_date" placeholder="Start Date" class="px-4 py-3 border rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-600">
             <input type="date" id="end_date" placeholder="End Date" class="px-4 py-3 border rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-600">
             <input type="text" id="city" placeholder="City" class="px-4 py-3 border rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-600">
              <input type="text" id="state" placeholder="State" class="px-4 py-3 border rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-600">
               <input type="text" id="country" placeholder="Country" class="px-4 py-3 border rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-600">
        </div>
    </div>
         <div id="delete_button_container" class="hidden mb-4">
            <form method="POST" action="mass_delete_leads.php">
                <button type="submit" name="delete_selected" class="bg-red-600 text-white px-4 py-2 rounded-lg hover:bg-red-700 transition duration-300">Delete Selected</button>
             </form>
        </div>
    <!-- Search Results -->
       <div id="search_results">
           <p class="text-gray-600 text-center">Start searching to view leads here.</p>
        </div>
      <div id="pagination-container" class="mt-4 flex justify-center"></div>
</div>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const searchInput = document.getElementById('search_input');
         const categoryFilter = document.getElementById('category_id');
        const statusFilter = document.getElementById('status');
        const startDateFilter = document.getElementById('start_date');
        const endDateFilter = document.getElementById('end_date');
         const cityFilter = document.getElementById('city');
         const stateFilter = document.getElementById('state');
        const countryFilter = document.getElementById('country');
        const searchResults = document.getElementById('search_results');
        const paginationContainer = document.getElementById('pagination-container');
          const deleteButtonContainer = document.getElementById('delete_button_container');
        let currentPage = 1;
         const perPage = 10;
        let debounceTimer;

       function fetchLeads(page = 1) {
            const search = searchInput.value.trim();
            const category = categoryFilter.value;
            const status = statusFilter.value;
            const startDate = startDateFilter.value;
            const endDate = endDateFilter.value;
             const city = cityFilter.value;
             const state = stateFilter.value;
             const country = countryFilter.value;
           currentPage = page;

            fetch(`leads_list.php?search=${search}&category_id=${category}&status=${status}&start_date=${startDate}&end_date=${endDate}&page=${page}&city=${city}&state=${state}&country=${country}&per_page=${perPage}`)
                .then(response => response.text())
                .then(data => {
                    searchResults.innerHTML = data;
                     // Update pagination
                        updatePagination();
                       updateDeleteButtonVisibility();
                       const selectAllCheckbox = searchResults.querySelector('#select_all');
                    if(selectAllCheckbox){
                         selectAllCheckbox.addEventListener('click', function() {
                            const checkboxes = searchResults.querySelectorAll('input[name="selected_leads[]"]');
                            checkboxes.forEach(checkbox => checkbox.checked = this.checked);
                              updateDeleteButtonVisibility();
                        });
                     }
                })
                  .catch(error => {
                        console.error('Error fetching leads:', error);
                         searchResults.innerHTML = '<p class="text-gray-600 text-center">Error fetching leads.</p>';
                    });
            }

         function updatePagination() {
             const totalPages = searchResults.querySelector('[data-total-pages]')?.dataset?.totalPages;
              if(!totalPages || parseInt(totalPages) <= 1) {
                 paginationContainer.innerHTML = "";
                 return;
              }
            paginationContainer.innerHTML = ""; // Clear existing pagination

            for (let i = 1; i <= totalPages; i++) {
                const pageButton = document.createElement('button');
                pageButton.textContent = i;
                  pageButton.classList.add('px-4', 'py-2', 'mx-1', 'rounded-lg', 'hover:bg-blue-700', 'text-white', 'transition', 'duration-300')
                    if(i === parseInt(currentPage)) {
                          pageButton.classList.add('bg-blue-800');
                    } else {
                       pageButton.classList.add('bg-blue-600');
                    }

                  pageButton.addEventListener('click', () => fetchLeads(i));
                  paginationContainer.appendChild(pageButton);
              }
            }

        const handleSearch = () => {
            clearTimeout(debounceTimer);
            debounceTimer = setTimeout(() => {
              fetchLeads();
            }, 300);
        };
         
         searchInput.addEventListener('input', handleSearch);
            categoryFilter.addEventListener('change', handleSearch);
            statusFilter.addEventListener('change', handleSearch);
             startDateFilter.addEventListener('change', handleSearch);
            endDateFilter.addEventListener('change', handleSearch);
             cityFilter.addEventListener('input', handleSearch);
            stateFilter.addEventListener('input', handleSearch);
            countryFilter.addEventListener('input', handleSearch);
           fetchLeads();
              function updateDeleteButtonVisibility() {
              const checkboxes = searchResults.querySelectorAll('input[name="selected_leads[]"]');
             let checkedCount = 0;
                checkboxes.forEach(checkbox => {
                   if (checkbox.checked) {
                       checkedCount++;
                    }
                });
                 if(checkedCount > 0) {
                     deleteButtonContainer.classList.remove('hidden');
                } else {
                     deleteButtonContainer.classList.add('hidden');
                 }
         }
          searchResults.addEventListener('change', (event) => {
                if (event.target.matches('input[name="selected_leads[]"]')) {
                   updateDeleteButtonVisibility();
                }
            });
    });
</script>
<?php
// Include footer
require 'footer.php';
?>