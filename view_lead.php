<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

session_start();

require 'db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$lead_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

// Fetch lead details
$stmt = $conn->prepare("SELECT leads.*, employees.name AS assigned_employee_name
                        FROM leads
                        LEFT JOIN employees ON leads.assigned_to = employees.id
                        WHERE leads.id = :id");
$stmt->bindParam(':id', $lead_id);
$stmt->execute();
$lead = $stmt->fetch(PDO::FETCH_ASSOC);

// Fetch attachments for the lead
$stmt = $conn->prepare("SELECT * FROM attachments WHERE lead_id = :lead_id");
$stmt->bindParam(':lead_id', $lead_id);
$stmt->execute();
$attachments = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Fetch lead score
$stmt = $conn->prepare("SELECT * FROM lead_scores WHERE lead_id = :lead_id");
$stmt->bindParam(':lead_id', $lead_id);
$stmt->execute();
$lead_score = $stmt->fetch(PDO::FETCH_ASSOC);

// Define the categorize_lead function
function categorize_lead($score)
{
    if ($score >= 10) {
        return "Hot";
    } elseif ($score >= 5) {
        return "Warm";
    } else {
        return "Cold";
    }
}

$lead_category = $lead_score ? categorize_lead($lead_score['total_score']) : "Cold";

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['convert_to_customer'])) {
    $attribution_type = $_POST['attribution_type'];
    $attributed_employee = null;

    if ($attribution_type == 'self') {
        $attributed_employee = $_SESSION['user_id'];
    } elseif ($attribution_type == 'assigned_employee') {
        $attributed_employee = $lead['assigned_to'];
    } elseif ($attribution_type == 'other_employee') {
        $attributed_employee_name = isset($_POST['other_employee_name']) ? trim($_POST['other_employee_name']) : null;
        // Check if another employee is provided, else skip insertion of this field
         if ($attributed_employee_name) {
            // Check if this employee name exists
            $stmt = $conn->prepare("SELECT id FROM employees WHERE name = :name");
            $stmt->bindParam(':name', $attributed_employee_name);
            $stmt->execute();
            $employee = $stmt->fetch(PDO::FETCH_ASSOC);
            if ($employee) {
                $attributed_employee = $employee['id'];
            } else {
                 echo "<script>alert('Employee with this name does not exists.'); window.location.href='view_lead.php?id=$lead_id';</script>";
                exit();
            }
        }
    }
    
     $stmt = $conn->prepare("UPDATE leads SET status = 'Converted', converted_by = :converted_by WHERE id = :id");
    $stmt->bindParam(':id', $lead_id);
     $stmt->bindParam(':converted_by', $attributed_employee);
    if ($stmt->execute()) {
        echo "<script>alert('Lead converted successfully!'); window.location.href='view_lead.php?id=$lead_id';</script>";
    } else {
        echo "<script>alert('Error converting lead.'); window.location.href='view_lead.php?id=$lead_id';</script>";
    }
}


// Include header
require 'header.php';
?>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        ClassicEditor
            .create(document.querySelector('#content'), {
                toolbar: ['heading', '|', 'bold', 'italic', 'link', 'bulletedList', 'numberedList', 'blockQuote'],
                heading: {
                    options: [
                        { model: 'paragraph', title: 'Paragraph', class: 'ck-heading_paragraph' },
                        { model: 'heading1', view: 'h1', title: 'Heading 1', class: 'ck-heading_heading1' },
                        { model: 'heading2', view: 'h2', title: 'Heading 2', class: 'ck-heading_heading2' }
                    ]
                }
            })
            .catch(error => {
                console.error(error);
            });
    });
</script>
<h1 class="text-3xl font-bold text-gray-800 mb-6">Lead Details: <?php echo htmlspecialchars($lead['name']); ?></h1>

<!-- Lead Details -->
<div class="bg-white p-6 rounded-lg shadow-md mb-8">
    <p><strong>Email:</strong> <?php echo htmlspecialchars($lead['email']); ?></p>
    <p><strong>Phone:</strong> <?php echo htmlspecialchars($lead['phone']); ?></p>
    <p><strong>Category:</strong> <?php echo htmlspecialchars($lead['category_id']); ?></p>
</div>
<div class="bg-white p-6 rounded-lg shadow-md mb-8">
    <p><strong>Status:</strong> <?php echo htmlspecialchars($lead['status']); ?></p>
</div>
<div class="bg-white p-6 rounded-lg shadow-md mb-8">
    <p><strong>Lead Score:</strong> <?php echo $lead_score ? $lead_score['total_score'] : 0; ?></p>
    <p><strong>Lead Category:</strong> <?php echo $lead_category; ?></p>
    <!-- Convert to Customer Button -->
    <?php if ($lead['status'] !== 'Converted'): ?>
        <form method="POST" action="">
            <div class="mb-4">
                <label for="attribution_type" class="block text-gray-700">Convert to Customer By:</label>
                <select name="attribution_type" id="attribution_type" class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-600" onchange="showOtherEmployeeInput(this.value)">
                    <option value="self">Self</option>
                    <option value="assigned_employee">Assigned Employee (<?php echo htmlspecialchars($lead['assigned_employee_name'] ? $lead['assigned_employee_name'] : 'Unassigned'); ?>)</option>
                    <option value="other_employee">Other Employee</option>
                </select>
            </div>
            <div id="other_employee_input" class="mb-4 hidden">
                <label for="other_employee_name" class="block text-gray-700">Employee Name</label>
                <input type="text" name="other_employee_name" id="other_employee_name" class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-600" data-autocomplete-id="employee-autocomplete">
                    <div id="employee-autocomplete-suggestions" class="absolute z-10 mt-2 bg-white border rounded shadow-md w-full hidden"></div>
            </div>
            <button type="submit" name="convert_to_customer" class="bg-green-600 text-white px-4 py-2 rounded-lg hover:bg-green-700 transition duration-300">Convert to Customer</button>
        </form>
    <?php endif; ?>
</div>

<div class="bg-white p-6 rounded-lg shadow-md mb-8">
    <h3 class="text-xl font-bold text-gray-800 mb-4">Track Behavior</h3>
    <a href="track_behavior.php?lead_id=<?php echo $lead_id; ?>&action=website_visit" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition duration-300">Website Visit</a>
    <a href="track_behavior.php?lead_id=<?php echo $lead_id; ?>&action=email_open" class="bg-green-600 text-white px-4 py-2 rounded-lg hover:bg-green-700 transition duration-300">Email Open</a>
    <a href="track_behavior.php?lead_id=<?php echo $lead_id; ?>&action=form_submission" class="bg-purple-600 text-white px-4 py-2 rounded-lg hover:bg-purple-700 transition duration-300">Form Submission</a>
</div>

<!-- Attachments Section -->
<h2 class="text-2xl font-bold text-gray-800 mb-4">Attachments</h2>

<!-- Attachment Type Dropdown -->
<div class="mb-4">
    <label for="attachment_type" class="block text-gray-700">Attachment Type</label>
    <select name="attachment_type" id="attachment_type" class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-600" onchange="showAttachmentForm(this.value)">
        <option value="file">File</option>
        <option value="contract">Contract</option>
        <option value="proposal">Proposal</option>
        <option value="notes">Notes</option>
    </select>
</div>

<!-- File Upload Form (Default) -->
<div id="file_form" class="bg-white p-6 rounded-lg shadow-md mb-8">
    <form method="POST" action="upload_attachment.php" enctype="multipart/form-data">
        <input type="hidden" name="lead_id" value="<?php echo $lead_id; ?>">
        <input type="hidden" name="file_type" value="file">
        <div class="mb-4">
            <label for="file" class="block text-gray-700">Choose File</label>
            <input type="file" name="file" id="file" class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-600" required>
        </div>
        <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition duration-300">Upload</button>
    </form>
</div>

<!-- Contract/Proposal Form (Hidden by Default) -->
<div id="contract_proposal_form" class="bg-white p-6 rounded-lg shadow-md mb-8 hidden">
    <form method="POST" action="upload_attachment.php" enctype="multipart/form-data">
        <input type="hidden" name="lead_id" value="<?php echo $lead_id; ?>">
        <input type="hidden" name="file_type" id="contract_proposal_type" value="contract">
        <!-- WYSIWYG Editor -->
        <div class="mb-4">
            <label for="content" class="block text-gray-700">Content</label>
            <textarea name="content" id="content" class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-600"></textarea>
        </div>
        <!-- Optional File Upload -->
        <div class="mb-4">
            <label for="file" class="block text-gray-700">Optional File Upload</label>
            <input type="file" name="file" id="file" class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-600">
        </div>
        <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition duration-300">Save</button>
    </form>
</div>

<!-- Notes Form (Hidden by Default) -->
<div id="notes_form" class="bg-white p-6 rounded-lg shadow-md mb-8 hidden">
    <form method="POST" action="upload_attachment.php">
        <input type="hidden" name="lead_id" value="<?php echo $lead_id; ?>">
        <input type="hidden" name="file_type" value="notes">
        <!-- Sticky Notes Interface -->
        <div class="mb-4">
            <label for="note" class="block text-gray-700">Add Note</label>
            <textarea name="note" id="note" class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-600" placeholder="Write your note here..."></textarea>
        </div>
        <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition duration-300">Add Note</button>
    </form>
    <!-- Display Existing Notes -->
    <div class="mt-6">
        <h3 class="text-xl font-bold text-gray-800 mb-4">Notes</h3>
        <?php
        $stmt = $conn->prepare("SELECT * FROM attachments WHERE lead_id = :lead_id AND file_type = 'notes' ORDER BY uploaded_at DESC");
        $stmt->bindParam(':lead_id', $lead_id);
        $stmt->execute();
        $notes = $stmt->fetchAll(PDO::FETCH_ASSOC);

        if ($notes) : ?>
            <?php foreach ($notes as $note) : ?>
                <div class="bg-yellow-100 p-4 rounded-lg shadow-md mb-4">
                    <p class="text-gray-800"><?php echo htmlspecialchars($note['file_name']); ?></p>
                    <p class="text-gray-600 text-sm"><?php echo date('M d, Y H:i', strtotime($note['uploaded_at'])); ?></p>
                    <a href="delete_attachment.php?id=<?php echo $note['id']; ?>" class="text-red-600 hover:underline">Delete</a>
                </div>
            <?php endforeach; ?>
        <?php else : ?>
            <p class="text-gray-600">No notes found.</p>
        <?php endif; ?>
    </div>
</div>

<!-- JavaScript to Toggle Forms -->
<script>
    function showAttachmentForm(type) {
        // Hide all forms
        document.getElementById('file_form').classList.add('hidden');
        document.getElementById('contract_proposal_form').classList.add('hidden');
        document.getElementById('notes_form').classList.add('hidden');

        // Show the selected form
        if (type === 'file') {
            document.getElementById('file_form').classList.remove('hidden');
        } else if (type === 'contract' || type === 'proposal') {
            document.getElementById('contract_proposal_form').classList.remove('hidden');
            document.getElementById('contract_proposal_type').value = type;
        } else if (type === 'notes') {
            document.getElementById('notes_form').classList.remove('hidden');
        }
    }
      function showOtherEmployeeInput(type) {
        const otherEmployeeInput = document.getElementById('other_employee_input');
        if (type === 'other_employee') {
            otherEmployeeInput.classList.remove('hidden');
        } else {
            otherEmployeeInput.classList.add('hidden');
        }
    }

    // Initialize the form based on the default selection
    document.addEventListener('DOMContentLoaded', function() {
        showAttachmentForm(document.getElementById('attachment_type').value);
        showOtherEmployeeInput(document.getElementById('attribution_type').value);
    });
</script>

<!-- Attachments List -->
<div class="bg-white p-6 rounded-lg shadow-md">
    <table class="w-full text-left">
        <thead>
            <tr>
                <th class="px-4 py-2">File Name</th>
                <th class="px-4 py-2">Type</th>
                <th class="px-4 py-2">Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php if ($attachments) : ?>
                <?php foreach ($attachments as $attachment) : ?>
                    <tr class="border-b">
                        <td class="px-4 py-2"><?php echo htmlspecialchars($attachment['file_name']); ?></td>
                        <td class="px-4 py-2"><?php echo htmlspecialchars($attachment['file_type']); ?></td>
                        <td class="px-4 py-2">
                            <a href="<?php echo $attachment['file_path']; ?>" class="text-blue-600 hover:underline" download>Download</a>
                            <a href="delete_attachment.php?id=<?php echo $attachment['id']; ?>" class="text-red-600 hover:underline ml-2">Delete</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php else : ?>
                <tr>
                    <td colspan="3" class="px-4 py-2 text-center text-gray-600">No attachments found.</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>
<script>
        document.addEventListener('DOMContentLoaded', function () {
            const employeeInput = document.querySelector('input[data-autocomplete-id="employee-autocomplete"]');
            const suggestionsDiv = document.getElementById('employee-autocomplete-suggestions');
           let selectedEmployeeId = null;
            employeeInput.addEventListener('input', function () {
                const query = this.value.trim();

                if (query.length < 1) {
                    suggestionsDiv.classList.add('hidden');
                    return;
                }

                fetch(`search_employees.php?search=${query}`)
                    .then(response => response.json())
                    .then(data => {
                        suggestionsDiv.innerHTML = '';
                        if(data.length > 0){
                            data.slice(0, 10).forEach(employee => {
                                const suggestion = document.createElement('div');
                                suggestion.textContent = employee.name;
                                suggestion.classList.add('px-4', 'py-2', 'hover:bg-gray-100', 'cursor-pointer');
                                suggestion.addEventListener('click', function () {
                                    employeeInput.value = employee.name;
                                   selectedEmployeeId = employee.id;
                                    suggestionsDiv.classList.add('hidden');
                                });
                                suggestionsDiv.appendChild(suggestion);
                            });
                            suggestionsDiv.classList.remove('hidden');
                        } else {
                                suggestionsDiv.classList.add('hidden');
                        }

                    })
                    .catch(error => {
                        console.error('Error fetching employees:', error);
                        suggestionsDiv.classList.add('hidden');
                    });

            });
            document.addEventListener('click', function (event) {
                if (!employeeInput.contains(event.target) && !suggestionsDiv.contains(event.target)) {
                    suggestionsDiv.classList.add('hidden');
                }
            });
            
           const form =  employeeInput.closest('form');
           form.addEventListener('submit', function(event) {
               if (employeeInput.value.trim() && selectedEmployeeId == null ) {
                    event.preventDefault();
                    alert("Please select a valid employee from the suggestions before saving.");
                }
                 if (employeeInput.value.trim() && selectedEmployeeId != null ) {
                    const hiddenInput = document.createElement('input');
                    hiddenInput.type = 'hidden';
                    hiddenInput.name = 'other_employee_name';
                    hiddenInput.value =  employeeInput.value;
                    form.appendChild(hiddenInput);
               }
           });
        });
    </script>
<?php
// Include footer
require 'footer.php';
?>