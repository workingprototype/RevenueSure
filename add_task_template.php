<?php
ini_set('display_errors' , 1);
error_reporting(E_ALL);
session_start();
require 'db.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit();
}

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = trim($_POST['name']);
    $description = trim($_POST['description']);
    $fields = $_POST['fields'] ?? [];

    // Validate inputs
    if (empty($name)) {
        $error = "Template name is required.";
    } else {
       // Insert the task template
        $stmt = $conn->prepare("INSERT INTO task_templates (name, description) VALUES (:name, :description)");
        $stmt->bindParam(':name', $name);
        $stmt->bindParam(':description', $description);
        if ($stmt->execute()) {
             $template_id = $conn->lastInsertId();
             foreach($fields as $field){
                if (!empty($field['field_name']) && !empty($field['field_type'])){
                      $stmt = $conn->prepare("INSERT INTO task_template_fields (template_id, field_name, field_type, options) VALUES (:template_id, :field_name, :field_type, :options)");
                      $stmt->bindParam(':template_id', $template_id);
                      $stmt->bindParam(':field_name', $field['field_name']);
                        $stmt->bindParam(':field_type', $field['field_type']);
                        
                          $options = $field['options'] ?? null;
                         $stmt->bindParam(':options', $options);
                      $stmt->execute();
                 }
            }
            $success = "Task template added successfully!";
            header("Location: manage_task_templates.php?success=true");
            exit();
        } else {
            $error = "Error adding task template.";
        }
    }
}

// Include header
require 'header.php';
?>
<div class="container mx-auto p-6 fade-in">
    <h1 class="text-4xl font-bold text-gray-900 mb-6">Add Task Template</h1>
        <?php if ($error): ?>
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-lg mb-6">
                <?php echo $error; ?>
            </div>
        <?php endif; ?>

        <?php if ($success): ?>
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded-lg mb-6">
                <?php echo $success; ?>
            </div>
        <?php endif; ?>
    <div class="bg-white p-6 rounded-2xl shadow-xl">
        <form method="POST" action="">
            <div class="mb-4">
                <label for="name" class="block text-gray-700">Template Name</label>
                 <input type="text" name="name" id="name" class="w-full px-4 py-3 border rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-600" required>
            </div>
              <div class="mb-4">
                <label for="description" class="block text-gray-700">Description</label>
                 <textarea name="description" id="description" class="w-full px-4 py-3 border rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-600"></textarea>
             </div>
               <div class="mb-4">
                    <h3 class="text-xl font-semibold text-gray-800 mb-4">Custom Fields</h3>
                     <div id="custom_fields_container">
                         <div class="flex gap-4 mb-4 border-b-2 border-gray-200 pb-4" data-field-id="0">
                             <div class="flex-1">
                                <label for="field_name_0" class="block text-gray-700">Field Name</label>
                                 <input type="text" name="fields[0][field_name]" id="field_name_0" class="w-full px-4 py-3 border rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-600">
                            </div>
                              <div class="flex-1">
                                 <label for="field_type_0" class="block text-gray-700">Field Type</label>
                                    <select name="fields[0][field_type]" id="field_type_0" class="w-full px-4 py-3 border rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-600 appearance-none" onchange="toggleOptions(this)">
                                        <option value="text">Text</option>
                                       <option value="select">Dropdown</option>
                                        <option value="checkbox">Checkbox</option>
                                       <option value="date">Date</option>
                                     </select>
                           </div>
                           <div class="flex-1 hidden" id="options_container_0">
                                <label for="options_0" class="block text-gray-700">Options (comma separated)</label>
                              <input type="text" name="fields[0][options]" id="options_0" class="w-full px-4 py-3 border rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-600" >
                           </div>
                        </div>
                    </div>
                    <button type="button" id="add_field" class="bg-blue-600 text-white px-4 py-3 rounded-xl hover:bg-blue-700 transition duration-300">Add Field</button>
              </div>
               <button type="submit" class="bg-blue-700 text-white px-6 py-3 rounded-xl hover:bg-blue-900 transition duration-300 shadow-md">Create Template</button>
        </form>
    </div>
</div>
<script>
        let field_count = 1;
    document.getElementById('add_field').addEventListener('click', function () {
           const container = document.getElementById('custom_fields_container');
          const newItem = document.createElement('div');
        newItem.classList.add('flex', 'gap-4', 'mb-4', 'border-b-2', 'border-gray-200', 'pb-4');
         newItem.dataset.field_id = field_count;
          newItem.innerHTML = `
               <div class="flex-1">
                     <label for="field_name_${field_count}" class="block text-gray-700">Field Name</label>
                    <input type="text" name="fields[${field_count}][field_name]" id="field_name_${field_count}" class="w-full px-4 py-3 border rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-600">
              </div>
                <div class="flex-1">
                    <label for="field_type_${field_count}" class="block text-gray-700">Field Type</label>
                       <select name="fields[${field_count}][field_type]" id="field_type_${field_count}" class="w-full px-4 py-3 border rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-600 appearance-none" onchange="toggleOptions(this)">
                             <option value="text">Text</option>
                             <option value="select">Dropdown</option>
                            <option value="checkbox">Checkbox</option>
                             <option value="date">Date</option>
                       </select>
                 </div>
                 <div class="flex-1 hidden" id="options_container_${field_count}">
                    <label for="options_${field_count}" class="block text-gray-700">Options (comma separated)</label>
                       <input type="text" name="fields[${field_count}][options]" id="options_${field_count}" class="w-full px-4 py-3 border rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-600">
                </div>
            `;
           container.appendChild(newItem);
           field_count++;
        });
  function toggleOptions(select){
    const fieldId = select.closest('[data-field-id]').dataset.field_id;
        const optionsContainer = document.getElementById(`options_container_${fieldId}`);
         if(select.value == 'select'){
           optionsContainer.classList.remove('hidden');
         }else {
               optionsContainer.classList.add('hidden');
                 document.getElementById(`options_${fieldId}`).value = '';
         }
  }
  document.addEventListener('DOMContentLoaded', function() {
      const container = document.getElementById('custom_fields_container');
      container.querySelectorAll('[data-field-id]').forEach(item => {
           const select = item.querySelector('select');
         toggleOptions(select);
    });
 });
</script>
<?php
// Include footer
require 'footer.php';
?>