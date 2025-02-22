<?php
require_once ROOT_PATH . 'helper/core.php';
redirectIfUnauthorized(true);

$error = '';
$success = '';

// --- Template Handling ---
$templates = [
    'default' => [
        'title' => 'Default',
        'content' => '<p class="default-text">This is a <b>basic</b> document template.</p>',
    ],
    'report' => [
        'title' => 'Report Template',
        'content' => '<h1 class="report-title">Report Title</h1>
        <p class="report-intro">Introduction with <strong>key findings</strong>...</p>
        <h2 class="report-section">Section 1</h2>
        <p class="report-details">Details and analysis...</p>',
    ],
    'proposal' => [
        'title' => 'Proposal Template',
        'content' => '<h1 class="proposal-title">Proposal</h1>
        <p class="proposal-summary">Executive Summary...</p>
        <h2 class="proposal-problem">Problem Statement</h2>
        <p class="proposal-desc">Clear description of the problem...</p>
        <h2 class="proposal-solution">Proposed Solution</h2>
        <p class="proposal-desc">Our innovative approach...</p>',
    ],
    'meeting_notes' => [
        'title' => 'Meeting Notes Template',
        'content' => '<h1 class="meeting-title">Meeting Notes</h1>
        <h2 class="meeting-section">Attendees</h2>
        <ul class="meeting-list">
            <li>Name, Title</li>
        </ul>
        <h2 class="meeting-section">Agenda</h2>
        <ul class="meeting-list">
            <li>Topic 1</li>
            <li>Topic 2</li>
        </ul>
        <h2 class="meeting-section">Discussion</h2>
        <p class="meeting-desc">Key points and action items...</p>',
    ],
    'invoice' => [
        'title' => 'Invoice Template',
        'content' => '<div class="invoice-container">
            <div class="invoice-header"><h1>Invoice</h1></div>
            <p class="invoice-date">Date: 2025-02-22</p>
            <table class="invoice-table">
                <thead>
                    <tr>
                        <th>Description</th>
                        <th>Quantity</th>
                        <th>Price</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>Item 1</td>
                        <td>2</td>
                        <td>$50</td>
                    </tr>
                    <tr>
                        <td>Item 2</td>
                        <td>1</td>
                        <td>$30</td>
                    </tr>
                </tbody>
            </table>
            <p class="invoice-total"><strong>Total: $130</strong></p>
        </div>',
    ],
    'newsletter' => [
        'title' => 'Newsletter Template',
        'content' => '<div class="newsletter-container">
            <div class="newsletter-header">
                <h1>Monthly Newsletter</h1>
            </div>
            <div class="newsletter-content">
                <p>Welcome to our monthly newsletter. Here are the highlights...</p>
            </div>
        </div>',
    ],
    'resume' => [
        'title' => 'Resume Template',
        'content' => '<div class="resume-container">
            <div class="resume-header">
                <h1>John Doe</h1>
                <p>Professional Title</p>
            </div>
            <div class="resume-section">
                <h2>Experience</h2>
                <p>Details about work experience...</p>
            </div>
            <div class="resume-section">
                <h2>Education</h2>
                <p>Details about education...</p>
            </div>
        </div>',
    ],
    'portfolio' => [
        'title' => 'Portfolio Template',
        'content' => '<div class="portfolio-container">
            <h1>Portfolio</h1>
            <div class="portfolio-projects">
                <div class="project">
                    <h2>Project 1</h2>
                    <p>Description of project 1...</p>
                </div>
                <div class="project">
                    <h2>Project 2</h2>
                    <p>Description of project 2...</p>
                </div>
                <div class="project">
                    <h2>Project 3</h2>
                    <p>Description of project 3...</p>
                </div>
            </div>
        </div>',
    ],
    'event_flyer' => [
        'title' => 'Event Flyer Template',
        'content' => '<div class="flyer-container">
            <h1 class="flyer-title">Event Name</h1>
            <p class="flyer-date">Date: 2025-03-01</p>
            <p class="flyer-location">Location: Venue Name</p>
            <p class="flyer-desc">Join us for an exciting event!</p>
        </div>',
    ],
    'blog_post' => [
        'title' => 'Blog Post Template',
        'content' => '<div class="blog-container">
            <div class="blog-header">
                <img src="header-image.jpg" alt="Header Image" class="blog-header-image">
                <h1>Blog Post Title</h1>
            </div>
            <div class="blog-content">
                <p>This is the content of the blog post. Start writing your story here...</p>
            </div>
        </div>',
    ],
];

// --- Form Handling ---
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $title = trim($_POST['title']);
    $template = isset($_POST['template']) ? trim($_POST['template']) : 'default';
    $content = '';

    // Load content from selected template
    if (array_key_exists($template, $templates)) {
        $content = $templates[$template]['content'];
    }

    // Allow the user to overwrite the template.
    if (!empty($_POST['content'])) {
        $content = $_POST['content'];
    }

    if (empty($title)) {
        $error = "Title is required.";
    } else {
        $stmt = $conn->prepare("INSERT INTO documents (title, content, created_by) VALUES (:title, :content, :user_id)");
        $stmt->bindParam(':title', $title);
        $stmt->bindParam(':content', $content);
        $stmt->bindParam(':user_id', $_SESSION['user_id']);

        if ($stmt->execute()) {
            $document_id = $conn->lastInsertId();
            header("Location: " . BASE_URL . "documents/edit?id=$document_id");
            exit();
        } else {
            $error = "Error creating document.";
        }
    }
}

// Preselect the default template.
if (empty($_POST['template'])) {
    $template = "default";
} else {
    $template = $_POST['template'];
}
?>
<link rel="stylesheet" href="<?php echo BASE_URL; ?>assets/css/document_templates.css"> <!-- Link to stylesheet -->
<div class="container mx-auto p-6 fade-in">
    <h1 class="text-3xl font-bold text-gray-800 mb-6">Create New Document</h1>

    <?php if ($error): ?>
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-lg mb-6">
            <?php echo $error; ?>
        </div>
    <?php endif; ?>

    <div class="bg-white p-6 rounded-lg shadow-md">
        <form method="POST" action="">
            <?php echo csrfTokenInput(); ?>
            <div class="mb-4">
                <label for="title" class="block text-gray-700">Title</label>
                <input type="text" name="title" id="title" class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-600" required>
            </div>

            <!-- --- Template Selection --- -->
            <div class="mb-4">
                <label for="template" class="block text-gray-700">Select Template</label>
                <select name="template" id="template" class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-600">
                    <?php foreach ($templates as $key => $tmpl): ?>
                        <option value="<?php echo htmlspecialchars($key); ?>" <?php echo ($key == $template) ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($tmpl['title']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <!-- --- CKEditor Container --- -->
            <div class="mb-4">
                <label for="content" class="block text-gray-700">Content</label>
                <div id="ckeditor"><?php
                    if (array_key_exists($template, $templates)) {
                        echo $templates[$template]['content'];
                    }
                ?></div>
                <input type="hidden" name="content" id="content_hidden">
            </div>

            <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition duration-300">Create Document</button>
        </form>
    </div>
</div>
<script>
document.addEventListener('DOMContentLoaded', function () {
    let editor;

    ClassicEditor
        .create(document.querySelector('#ckeditor'), {
            htmlSupport: {
                allow: [
                    {
                        name: /.*/,
                        styles: true,
                        classes: true,
                    },
                    {
                        name: 'img',
                        attributes: {
                            'src': true,
                            'alt': true,
                            'style': true,
                            'class': true,
                        }
                    }
                ]
            },
            toolbar: [ 'heading', '|', 'bold', 'italic', 'link', 'bulletedList', 'numberedList', 'blockQuote', 'imageUpload', '|', 'undo', 'redo' ],
            image: {
                toolbar: [ 'imageStyle:full', 'imageStyle:side', '|', 'imageTextAlternative' ]
            },
            contentsCss: [
                '<?php echo BASE_URL; ?>assets/css/document_templates.css', // Ensure this path is correct
                'https://cdn.ckeditor.com/ckeditor5/44.2.1/ckeditor5-content.css' // Include CKEditor content styles
            ]
        })
        .then(newEditor => {
            editor = newEditor;

            // Set initial content from the template
            const templateSelect = document.getElementById('template');
            const contentHiddenInput = document.getElementById('content_hidden');

            templateSelect.addEventListener('change', function() {
                const selectedTemplate = this.value;
                const templates = <?php echo json_encode($templates); ?>;
                if (templates[selectedTemplate]) {
                    editor.setData(templates[selectedTemplate].content);
                }
            });

            // Update hidden input before form submission
            const form = document.querySelector('form');
            form.addEventListener('submit', function(event) {
                contentHiddenInput.value = editor.getData();
            });
        })
        .catch(error => {
            console.error(error);
        });
});

</script>
