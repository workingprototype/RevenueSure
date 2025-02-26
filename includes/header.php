<?php
require_once __DIR__ . '/../helper/core.php'; // Include core functions
require_once __DIR__ . '/../helper/cache.php'; // Include cache functions

$cacheKey = 'header_' . (isset($_SESSION['user_id']) ? 'user_' . $_SESSION['user_id'] : 'anonymous');

$cacheExpiration = 3600; // Cache for 1 hour

if (ENABLE_CACHE && isCacheValid($cacheKey, $cacheExpiration)) {
    echo getCache($cacheKey);
} else {
    ob_start(); // Start output buffering
    ?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>RevenueSure</title>
  <meta name="description" content="A platform to manage leads and businesses efficiently." />
  <meta name="keywords" content="leads, businesses, management, platform" />
  <meta name="author" content="Your Name" />

  <?php if (USE_CDN_ASSETS): ?>
        <!-- CDN Assets -->
        <script src="https://cdn.tailwindcss.com"></script>
        <script src="https://cdn.ckeditor.com/ckeditor5/41.1.0/classic/ckeditor.js"></script>
        <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet" />
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css" />
        <link href="https://fonts.googleapis.com/css2?family=Roboto+Slab:wght@400;700&display=swap" rel="stylesheet" />
        <script src="https://cdnjs.cloudflare.com/ajax/libs/flowbite/2.3.0/flowbite.min.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/marked/1.1.1/marked.min.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/frappe-gantt/dist/frappe-gantt.umd.js"></script>
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/frappe-gantt/dist/frappe-gantt.css">
        

    <?php else: ?>
        <!-- Local Assets -->
        <link href="<?= BASE_URL; ?>assets/css/all.min.css" rel="stylesheet">
        <link href="<?= BASE_URL; ?>assets/css/animate.min.css" rel="stylesheet">
        <link href="<?= BASE_URL; ?>assets/fonts/google-fonts.css?family=Roboto+Slab:wght@400;700&display=swap" rel="stylesheet"/>
        <script src="<?= BASE_URL; ?>assets/js/tailwindcss.js"></script>
        <script src="<?= BASE_URL; ?>assets/js/ckeditor.js"></script>
        <script src="<?= BASE_URL; ?>assets/js/font-awesome.js" crossorigin="anonymous"></script>
        <script src="<?= BASE_URL; ?>assets/js/chartjs-4.4.8.js"></script>
        <script src="<?= BASE_URL; ?>assets/js/frappe-gantt.umd.js"></script>
        <link rel="stylesheet" href="<?= BASE_URL; ?>assets/css/frappe-gantt.umd.css">
        
    <?php endif; ?>


  <style>
    /* Brutalist Reset and Variables */
    :root {
      --bg-color: #f0f0f0;
      --text-color: #222;
      --primary-color: #000;
      --secondary-color: #333;
      --accent-color: #f00;
      --border-color: #222;
    }

    body {
      font-family: monospace, sans-serif; /* Fixed font */
      background-color: var(--bg-color);
      color: var(--text-color);
      line-height: 1.4;
      margin: 0;
      padding: 0;
      min-height: 100vh;  /* Ensure the body takes up at least the full viewport height */
      display: flex;
      flex-direction: column;
      flex: 1;
}

    h1, h2, h3, h4, h5, h6 {
      font-family: monospace, sans-serif;
      font-weight: bold;
      margin-bottom: 0.5em;
      line-height: 1.2;
    }

    a {
      color: var(--primary-color);
      text-decoration: none;
      border-bottom: 2px solid var(--primary-color);
      transition: all 0.2s ease-in-out;
    }

    a:hover {
      background-color: var(--primary-color);
      color: var(--bg-color);
      border-bottom: 2px solid black; /* Keep the border, but change color */
      box-shadow: 6px 6px 0px black; /* Retain or slightly reduce shadow */
    }

    /* Layout and Structure */
    .container {
      width: 90%;
      max-width: 1200px;
      margin: 0 auto;
      padding: 1em;
    }

    /* Navigation */
    nav {
      background-color: #fff;
      color: var(--bg-color);
      padding: 1em 0;
      border-bottom: 4px solid var(--primary-color);
    }

    nav ul {
      list-style: none;
      padding: 0;
      margin: 0;
      display: flex;
      justify-content: space-around;
    }

    nav li a {
      color: var(--bg-color);
      border: none; /* Remove underline from nav links */
      padding: 0.5em 1em;
      display: block;
      transition: all 0.2s ease-in-out;
    }

    nav li a:hover {
      background-color: var(--primary-color);
      color: var(--bg-color);
    }

    /* Sidebar */
    aside {
      width: 100%;
  padding: 15px;
  font-size: 18px;
  font-weight: bold;
  color: #000;
  background-color: #fff;
  border: 4px solid #000;
  position: relative;
  overflow: hidden;
  border-radius: 0;
  outline: none;
  transition: all 0.3s cubic-bezier(0.25, 0.8, 0.25, 1);
  box-shadow: 5px 5px 0 #000, 10px 10px 0 #4a90e2;
    }

    /* Typography adjustments (simplified) */
    p {
      margin-bottom: 1em;
    }

    /* Table styling (optional, simplified) */
    table {
      width: 100%;
      border-collapse: collapse;
    }

    th, td {
      border: 2px solid var(--border-color);
      padding: 0.5em;
      text-align: left;
    }

    th {
      background-color: var(--secondary-color);
      color: var(--bg-color);
    }
    

    /* Example specific styles - Replace with your actual styles */
    .brutalist-header {
      background-color: #333;
      color: #fff;
      padding: 1rem;
      text-align: center;
      margin-bottom: 1rem;
      border-bottom: 4px solid black;
    }
    /* Brutalist menu styling */
    aside {
      min-width: 280px;
      transition: all 0.3s ease-in-out;
      border-right: 4px solid #000; /* Thick black border */
      background: #ddd;
    }
    aside nav {
      display: flex;
      flex-direction: column;
      justify-content: flex-start;
    }
    .menu-item a {
      display: flex;
      align-items: center;
      padding: 12px 20px;
      margin: 0; /* No margin */
      transition: background-color 0.2s ease; /* Just background color */
      border-bottom: 2px solid #000; /* Bottom border for each */
      color: #222;
      font-size: 1rem;
      text-decoration: none; /* No underline */
      text-transform: uppercase;
      font-weight: bold;
    }
    .menu-item .submenu a:hover {
      background-color: #bbb;
      color: #000;
    }
    .menu-item .submenu a {
      padding: 12px 30px;
      font-size: 0.9rem;
      border-bottom: 1px solid #000; /* Lighter border */
      transition: background-color 0.2s ease; /* Just background color */
      text-transform: uppercase;
    }
    /* Only style the direct child for active parent items */
    .menu-item.active > a {
      color: #fff;
      background-color: #000;
    }
    /* Highlight active submenu links */
    .submenu a.active {
      color: #fff;
      background-color: #000;
      border-left: none; /* Remove left border */
    }
    .submenu {
      padding-left: 10px;
      transition: max-height 0.4s ease;
      margin-top: 0px; /* Reduced margin */
      border-left: none; /* Removed nested borders */
      overflow: hidden;
      max-height: 0;
    }
    /* Keep submenu open when hovering OR when the parent menu item is active */
    .menu-item:hover .submenu,
    .menu-item.active .submenu {
      max-height: 1000px;
    }
    /* Remove animations */
    .fade-in-up {
      /* No animation */
    }
    .fade-in {
      /* No animation */
    }
    button {
      transition: background-color 0.3s ease, color 0.3s ease, transform 0.1s ease;
      border: 2px solid #000; /* Black border */
    }
    button:active {
      transform: scale(0.98);
    }
    input:focus, select:focus, textarea:focus {
      border-color: #000; /* Black border */
      box-shadow: none; /* Remove box-shadow */
    }
    .bg-white {
      background-color: #fff;
      border: 2px solid #000; /* Black border */
      box-shadow: none; /* Remove box-shadow */
      transition: none; /* Remove transitions */
    }
    input, select, textarea {
      border: 2px solid #000; /* Black border */
      box-shadow: none; /* Remove box-shadow */
      border-radius: 0px; /* Square corners */
      transition: none; /* Remove transitions */
    }
    
    /* Top Navigation Profile Picture */
    .top-nav-button #profileButton img {
      width: 40px;
      height: 40px;
      border-radius: 50%;
      border: 3px solid #fff;
      object-fit: cover;
      box-shadow: 2px 2px 5px rgba(0, 0, 0, 0.3); /* Add a subtle shadow */
      transition: all 0.3s ease;
    }

    /* On Hover  - Scale up image*/
    .top-nav-button:hover #profileButton img {
      transform: scale(1.1); /* Enlarge on hover */
      box-shadow: 3px 3px 7px rgba(0, 0, 0, 0.4); /* Shadow effect */
    }
    .top-nav-dropdown {
      z-index: 50;
      border: 4px solid #000; /* Thick black border */
      border-radius: 0px; /* Square corners */
      box-shadow: none; /* Remove box-shadow */
      background-color: #fff;
    }
    .top-nav-dropdown a {
      padding: 10px;
      display: block;
      transition: background-color 0.2s ease; /* Just background color */
      border-bottom: 2px solid #000;
      color: #222;
      text-decoration: none;
    }
    .top-nav-dropdown a:hover {
      background: #ddd; /* Light gray */
    }
    /* "Paper" Document Style */
    .paper-doc {
      font-family: monospace, sans-serif;
      max-width: 800px;
      margin: 20px auto;
      padding: 40px 60px;
      background-color: #fff;
      border: 4px solid #000;
      box-shadow: none; /* Remove box-shadow */
      border-radius: 0px;
      line-height: 1.5;
      font-size: 16px;
    }
    .paper-doc h1, .paper-doc h2, .paper-doc h3, .paper-doc h4, .paper-doc h5, .paper-doc h6 {
      font-family: monospace, sans-serif;
      margin-bottom: 15px;
      line-height: 1.3;
      color: #000;
      font-weight: bold;
      border-bottom: 2px solid #000;
      padding-bottom: 5px;
    }
    .paper-doc h1 {
      font-size: 2.2rem;
    }
    .paper-doc h2 {
      font-size: 1.8rem;
      border-bottom: 2px solid #000;
      padding-bottom: 6px;
    }
    .paper-doc h3 {
      font-size: 1.6rem;
    }
    .paper-doc h4 {
      font-size: 1.4rem;
    }
    .paper-doc h5 {
      font-size: 1.2rem;
    }
    .paper-doc h6 {
      font-size: 1.1rem;
    }
    .paper-doc a {
      color: #000;
      text-decoration: none;
      border-bottom: 2px solid #000;
      transition: background-color 0.2s ease;
    }
    .paper-doc a:hover {
      background-color: #000;
      color: #fff;
    }
    .paper-doc p {
      margin-bottom: 15px;
    }
    .paper-doc ol, .paper-doc ul {
      padding-left: 25px;
      margin-bottom: 15px;
    }
    .paper-doc ul li {
      list-style-type: square;
      margin-bottom: 10px;
    }
    .paper-doc ol li {
      list-style-type: decimal;
      margin-bottom: 10px;
    }
    .paper-doc blockquote {
      margin: 20px 0;
      padding: 15px 20px;
      border-left: 6px solid #000;
      font-style: italic;
      color: #333;
      background-color: #eee;
    }
    .paper-doc .rating-bookmark-container {
      display: flex;
      justify-content: space-between;
      align-items: center;
      margin-top: 30px;
      border-top: 2px solid #000;
      padding-top: 15px;
    }
    .paper-doc .rating-bookmark-container button {
      background-color: #000;
      color: #fff;
      padding: 10px 15px;
      border: 2px solid #000;
      transition: background-color 0.2s ease, color 0.2s ease;
      cursor: pointer;
    }
    .paper-doc .rating-bookmark-container button:hover {
      background-color: #fff;
      color: #000;
    }
    .paper-doc .rating-bookmark-container textarea {
      margin-top: 5px;
      border: 2px solid #000;
      border-radius: 0;
      padding: 5px;
      width: 100%;
    }
    /* Global overrides for FontAwesome */
    .fa-solid {
      font-weight: 900;
    }



    /* Table Heading Color Override */
    th .text-gray-700 {
      color: white !important;
    }
    div.bg-white {
      border: 3px solid black;
      box-shadow: 6px 6px 0px black;
    }
    div.bg-gray-100 text-gray-700 w-64 p-4 hidden md:block border-r-4 border-black {
      border: 3px solid black;
      box-shadow: 6px 6px 0px black;
    }
    div.text-2xl font-bold block mb-4 pt-4 pl-4 text-gray-800 uppercase{
      border: 3px solid black;
      box-shadow: 6px 6px 0px black;
    }
    /* General footer styling */
footer {
  font-family: monospace, sans-serif;
  background: var(--bg-color);
  color: var(--text-color);
  padding: 1em;
  text-align: center;
  border-top: 4px solid #000; /* Brutalist border */
  position: sticky;
  bottom: 0;
  width: 100%;
  z-index: 100; /* Ensure it stays on top of other elements */
/* Neumorphism */
  box-shadow:  7px 7px 14px #bebebe,
              -7px -7px 14px #ffffff;
}
/* Reusable Button Style */
.brutalist-button {
    background-color: var(--bg-color) !important;
    color: var(--primary-color) !important;
    border: 2px solid var(--primary-color) !important;
    padding: 0.5em 1em !important;
    text-decoration: none !important;
    display: inline-block !important;
    transition: all 0.3s ease !important;
}

.brutalist-button:hover {
    background-color: var(--primary-color) !important;
    color: var(--bg-color) !important;
    transform: translateY(-2px) !important; /* Lift the button slightly */
    box-shadow: 3px 3px 0 var(--secondary-color) !important;
}

/* Brutalist Card Style */
.bg-white.p-6.rounded-2xl.shadow-xl.overflow-hidden > div.grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
    gap: 1.5rem;
}

.bg-white.p-6.rounded-2xl.shadow-xl.overflow-hidden {
    border: 4px solid var(--primary-color);
    box-shadow: 5px 5px 0 var(--secondary-color);
    transition: transform 0.3s ease, box-shadow 0.3s ease;
}

.bg-white.p-6.rounded-2xl.shadow-xl.overflow-hidden:hover {
    transform: translateY(-5px); /* Card moves up a bit */
    box-shadow: 8px 8px 0 var(--accent-color); /* Adjusted to accent color */
}

.bg-white.p-6.rounded-2xl.shadow-xl.overflow-hidden a {
    background-color: var(--bg-color) !important;
    color: var(--primary-color) !important;
    border: 2px solid var(--primary-color) !important;
    padding: 0.5em 1em !important;
    text-decoration: none !important;
    display: inline-block !important;
    transition: all 0.3s ease !important;
}

.bg-white.p-6.rounded-2xl.shadow-xl.overflow-hidden a:hover {
    background-color: var(--primary-color) !important;
    color: var(--bg-color) !important;
    transform: translateY(-2px) !important; /* Lift the button slightly */
    box-shadow: 3px 3px 0 var(--secondary-color) !important;
}

/* Target the container div */
.bg-white.p-4.rounded-lg.shadow {
  border: 4px solid var(--primary-color) !important;
  box-shadow: 5px 5px 0 var(--secondary-color) !important;
  transition: transform 0.3s ease, box-shadow 0.3s ease !important;
  background-color: var(--bg-color) !important; /* Set the background */
  overflow-x: auto !important; /* Enable horizontal scrolling for table */
  overflow-y: hidden !important; /* Disable the horizontal scrolling for table */
}

/* Container Hover Effects*/
.bg-white.p-4.rounded-lg.shadow:hover {
  transform: translateY(-5px) !important;
  box-shadow: 8px 8px 0 var(--accent-color) !important;
}

/* Style Table Headers */
.bg-white.p-4.rounded-lg.shadow table thead th {
  background-color: var(--secondary-color) !important;
  color: var(--bg-color) !important;
  font-weight: bold !important;
  white-space: nowrap !important; /* Prevent header text from wrapping */
}

/* Style Table Data Cells */
.bg-white.p-4.rounded-lg.shadow table td {
  border: 2px solid var(--border-color) !important;
  padding: 0.5em !important;
  text-align: left !important;
  white-space: nowrap !important; /* Prevent cell content from wrapping */
}

/* Style No Ledger Entries */
.bg-white.p-4.rounded-lg.shadow table td[colspan="6"] {
  text-align: center !important;
  font-style: italic !important;
  color: var(--secondary-color) !important;
}

/* Table width adjustements to ensure 100% */
.bg-white.p-4.rounded-lg.shadow table {
  width: 100% !important;
}

/* Target table container */
.bg-white.p-6.rounded-2xl.shadow-xl.overflow-hidden > table {
    width: 100% !important;
    border-collapse: collapse !important;
    font-family: monospace, sans-serif !important;
}

/* Target table header */
.bg-white.p-6.rounded-2xl.shadow-xl.overflow-hidden > table thead {
    background-color: var(--secondary-color) !important;
    color: var(--bg-color) !important;
}

/* Target table header cells */
.bg-white.p-6.rounded-2xl.shadow-xl.overflow-hidden > table th {
    padding: 0.75em !important;
    text-align: left !important;
    border-bottom: 3px solid var(--primary-color) !important; /* Highlight the border */
    font-weight: bold !important;
    text-transform: uppercase !important;
}

/* Target table body rows */
.bg-white.p-6.rounded-2xl.shadow-xl.overflow-hidden > table tbody tr {
    border-bottom: 2px solid var(--border-color) !important;
    transition: background-color 0.3s ease !important; /* Smooth transition */
}

/* Target table body cells */
.bg-white.p-6.rounded-2xl.shadow-xl.overflow-hidden > table td {
    padding: 0.75em !important;
}

/* Target table body rows on hover */
.bg-white.p-6.rounded-2xl.shadow-xl.overflow-hidden > table tbody tr:hover {
    background-color: #ddd !important; /* Lighter gray for hover */
}

/* Remove underline on links and set the color of the link to the desired values */
.bg-white.p-6.rounded-2xl.shadow-xl.overflow-hidden > table tbody a {
    text-decoration: none;
    border-bottom: 2px solid var(--primary-color);
    transition: color 0.3s ease, border-color 0.3s ease; /* Smooth transition */
    color: var(--primary-color) !important; /* Keep the color consistent */
}

/* Hover styles to the a links */
.bg-white.p-6.rounded-2xl.shadow-xl.overflow-hidden > table tbody a:hover {
    color: var(--bg-color) !important;
    background-color: var(--primary-color) !important;
    border-bottom-color: var(--primary-color) !important;
}

/* General scrollbar styling */
::-webkit-scrollbar {
    width: 20px; /* Increased width for bolder appearance */
    height: 20px; /* Increased height for bolder appearance (especially for horizontal scrollbars) */
}

/* Scrollbar track */
::-webkit-scrollbar-track {
    background-color: #ddd; /* Neutral background color */
    border: 4px solid var(--primary-color); /* Colored border */
    box-shadow: inset 0 0 5px rgba(0, 0, 0, 0.2); /* Subtle inner shadow */
}

/* Scrollbar thumb */
::-webkit-scrollbar-thumb {
    background-color: var(--secondary-color); /* Darker thumb color */
    border: 4px solid var(--primary-color); /* Colored border */
    border-radius: 0px; /* Square edges */
    box-shadow: 4px 4px 0 var(--primary-color); /* Offset shadow */
}

/* Scrollbar thumb on hover */
::-webkit-scrollbar-thumb:hover {
    background-color: #555; /* Even darker thumb on hover */
    box-shadow: inset 0 0 5px rgba(0, 0, 0, 0.5); /* Inner shadow on hover */
}

/* Scrollbar corner (optional) */
::-webkit-scrollbar-corner {
    background-color: var(--primary-color); /* Match a dominant color */
}

/* Styling the scrollbar buttons (arrows) */
::-webkit-scrollbar-button {
    background-color: var(--secondary-color);
    display: block;
    height: 24px;  /* Adjust to your needs */
    width: 24px;   /* Adjust to your needs */
    border: 2px solid var(--primary-color);
    cursor: pointer;
}

/* Styling the up/left arrow */
::-webkit-scrollbar-button:vertical:start:decrement,
::-webkit-scrollbar-button:horizontal:start:decrement {
    background-image: url('data:image/svg+xml;utf8,<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="%23f0f0f0" viewBox="0 0 16 16"><path d="M8 11.354l-5.757-5.757.707-.707L8 10.293l5.05-5.05.707.707z"/></svg>'); /* Increased resolution, SVG fill */
    background-repeat: no-repeat;
    background-position: center;
}

/* Styling the down/right arrow */
::-webkit-scrollbar-button:vertical:end:increment,
::-webkit-scrollbar-button:horizontal:end:increment {
    background-image: url('data:image/svg+xml;utf8,<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="%23f0f0f0" viewBox="0 0 16 16"><path d="M8 4.646l5.757 5.757-.707.707L8 5.707l-5.05 5.05-.707-.707z"/></svg>'); /* Increased resolution, SVG fill */
    background-repeat: no-repeat;
    background-position: center;
}

 /* The style for the whole chart content */
 .mb-8.border.border-gray-400.bg-gray-100.rounded-lg.p-6 {
    border: 4px solid black !important; /* Define the border for the card */
    box-shadow: 6px 6px 0 black !important;  /* Set a shadow for a bold appearance */
    border-radius: 0 !important;              /* Ensure the design is without rounded corners */
    background-color: #f0f0f0 !important;        /* Use an off-white for the background */
    transition: transform 0.3s ease, box-shadow 0.3s ease; /* Smooth transitions for the look */
}

/* Adjust to a more brutalist effect */
.mb-8.border.border-gray-400.bg-gray-100.rounded-lg.p-6:hover {
     transform: translateY(-5px) !important;
     box-shadow: 8px 8px 0 black !important;
}

/* The cards */
.mb-8.border.border-gray-400.bg-gray-100.rounded-lg.p-6 > table tbody tr {
    display: block !important;            /* Set the structure */
    border: 4px solid black !important;  /* Set the style */
    padding: 1rem !important;       /* Set padding for the elements*/
    border-radius: 0px !important; /* No card border */
    background-color: var(--bg-color) !important;
    margin-bottom: 0.5rem !important;  /* Block any additional margin */
}

/* The rows to the table */
.mb-8.border.border-gray-400.bg-gray-100.rounded-lg.p-6 > table tbody {
  display: grid !important; /* Set a grid layout */
    grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));   /* Columns layout */
    gap: 1rem !important;      /* Spacing */
}

/* The head */
.mb-8.border.border-gray-400.bg-gray-100.rounded-lg.p-6 > table thead {
     display: none !important;  /* Block the table */
}

/* This has the goal to make it take the whole elements */
.mb-8.border.border-gray-400.bg-gray-100.rounded-lg.p-6 > table th,
.mb-8.border.border-gray-400.bg-gray-100.rounded-lg.p-6 > table td {
   display: block !important;
   border: none !important;
   padding: 0.5rem !important; /* Reduce the spacing between the tables */
}
/* Set the colour */
.mb-8.border.border-gray-400.bg-gray-100.rounded-lg.p-6 > table tbody tr td a {
  color: var(--bg-color); /* Set the font color*/
   background-color: var(--primary-color) !important;     /* Set to a colour with a good effect */
   box-shadow: 3px 3px 0 var(--secondary-color);   /* Makes the component popping out */
}

/* This will take action in all elements when hovered */
.mb-8.border.border-gray-400.bg-gray-100.rounded-lg.p-6 > table tbody tr td a:hover {
   text-decoration: underline; /* Make the text visible */
   background-color: var(--secondary-color); /* Change to the new colour */
}

/* Applied Brutalist styling to elements of the header to keep consistence */
.flex.justify-between.items-center.mb-8 > .flex.flex-wrap.gap-2 > form {
        border: 4px solid black;
        box-shadow: 6px 6px 0 black;
        padding: 10px;
        border-radius: 0;
        transition: all 0.3s ease;
    }
    input[type="file"] {
    font-family: monospace, sans-serif;
    padding: 0.5em;
    margin-bottom: 1em;
    border: 3px solid var(--border-color);
    background-color: var(--bg-color);
    color: var(--text-color);
    width: 100%;
    box-sizing: border-box; /* Important for width consistency */
    outline: none;
    border-radius: 0px;
    box-shadow: 6px 6px 0px var(--border-color); /* Add the brutalist shadow */
    transition: box-shadow 0.2s ease; /* Smooth transition for hover */
}

/* Style the "Browse" button inside the file picker */
input[type="file"]::-webkit-file-upload-button {
    font-family: monospace, sans-serif;
    padding: 0.5em 1em;
    background-color: var(--primary-color);
    color: var(--bg-color);
    border: 3px solid var(--border-color);
    cursor: pointer;
    transition: all 0.2s ease-in-out;
    border-radius: 0px;
}

input[type="file"]::-webkit-file-upload-button:hover {
    background-color: var(--bg-color);
    color: var(--primary-color);
}

/* Style file name display (optional) */
input[type="file"]:focus {
   box-shadow: none; /* Remove the box-shadow */
}

.mb-8.border.border-gray-400.bg-gray-100.rounded-lg.p-6 {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
  gap: 1.5rem;
}

.mb-8.border.border-gray-400.bg-gray-100.rounded-lg.p-6>table {
  width: 100%;
  border-collapse: collapse;
}

.mb-8.border.border-gray-400.bg-gray-100.rounded-lg.p-6>table,
.mb-8.border.border-gray-400.bg-gray-100.rounded-lg.p-6>table tr,
.mb-8.border.border-gray-400.bg-gray-100.rounded-lg.p-6>table td,
.mb-8.border.border-gray-400.bg-gray-100.rounded-lg.p-6>table th {
  border: none !important;
}

.mb-8.border.border-gray-400.bg-gray-100.rounded-lg.p-6>table tbody tr {
  display: flex;
  flex-direction: column;
  border: 4px solid black;
  padding: 1rem;
  background-color: var(--bg-color);
}

.mb-8.border.border-gray-400.bg-gray-100.rounded-lg.p-6>table thead {
  display: none;
}

.mb-8.border.border-gray-400.bg-gray-100.rounded-lg.p-6>table tbody td {
  display: block;
  padding: 0.5rem;
  border-bottom: 1px solid #ccc;
}

.mb-8.border.border-gray-400.bg-gray-100.rounded-lg.p-6>table tbody td:last-child {
  border-bottom: none;
}
.flex.flex-wrap.gap-2 {
    gap: 0.5rem !important;
    justify-content: space-evenly;
}

.bg-white.p-6.rounded-2xl.shadow-xl.overflow-hidden {
    overflow-x: auto !important; /* Allow horizontal scrolling */
    overflow-y: hidden !important;  /*Keep current state */
}
.bg-white.p-6.rounded-2xl.shadow-xl.overflow-hidden table {
    min-width: 100% !important; /* Ensure table takes full container width initially */
}

.bg-white.p-6.rounded-2xl.shadow-xl.overflow-hidden table thead th,
.bg-white.p-6.rounded-2xl.shadow-xl.overflow-hidden table td {
   white-space: nowrap !important;
}

        .remember-me {
            margin-bottom: 1.5em;
        }

        .remember-me input[type="checkbox"] {
            margin-right: 0.5em;
            vertical-align: middle;
        }

        .recover-password {
            text-align: right;
        }

        .recover-password a {
            color: var(--primary-color);
            text-decoration: none;
            border-bottom: 2px solid var(--primary-color);
        }

        .separator {
            text-align: center;
            margin: 1.5em 0;
            font-weight: bold;
        }

        .qr-code-login {
            text-align: center;
        }

        .qr-code-login button {
            background-color: #fff;
            color: var(--primary-color);
            border: 3px solid var(--border-color);
            box-shadow: 6px 6px 0 var(--border-color);
            width: 100%;
        }

        .create-account {
            text-align: center;
            margin-top: 2em;
        }

        /* Style adjustments to match the image */
        .login-container {
            width: 400px;
            padding: 2em;
            background-color: #fff;
            border: 4px solid #000;
            box-shadow: 8px 8px 0 #000;
            border-radius: 0;
        }

        /* Additional styles for checkbox */
        input[type="checkbox"] {
            appearance: none;
            background-color: #fff;
            border: 2px solid #000;
            width: 16px;
            height: 16px;
            border-radius: 0;
            cursor: pointer;
            position: relative;
            margin-right: 0.5em;
            vertical-align: middle;
        }

        input[type="checkbox"]:checked {
    background-color: #000; /* Dark background for checked state */
    border-color: #fff; /* Inverted border color */
}

input[type="checkbox"]:checked::after {
    content: '✓'; /* Checkmark symbol */
    color: #fff; /* White checkmark */
    font-size: 16px;
    position: absolute;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
}

input[type="checkbox"]:focus {
    outline: none;
    box-shadow: 0 0 0 3px #000; /* Strong focus outline */
}

input[type="checkbox"]:hover {
    transform: scale(1.1); /* Slightly enlarge on hover */
}
        
/* Highlighted Notes Section */
.highlighted-notes {
    border: 2px solid #007bff; /* Add a blue border */
    box-shadow: 0 0 10px rgba(0, 123, 255, 0.5); /* Add a blue glow */
    transition: border-color 0.3s ease, box-shadow 0.3s ease; /* Smooth transition */
}
   

    
  </style>

  <style>
    /* Media Queries */
@media (max-width: 768px) {
    .flex.h-screen {
        flex-direction: column; /* Stack sidebar and content */
    }

    aside {
        width: 100%; /* Full width */
        position: fixed;
        top: 0;
        left: 0;
        height: 100vh;
        background-color: rgba(0, 0, 0, 0.8);
        color: white;
        transform: translateX(-100%); /* Hide off-screen */
        transition: transform 0.3s ease-in-out;
        z-index: 50;
        padding: 0.1rem;
        overflow-y: auto;
        box-shadow: 5px 0 10px rgba(0, 0, 0, 0.3);
        display: block !important; /* Override Tailwind's hidden class */
    }

    aside.open {
        transform: translateX(0); /* Slide in when open */
    }

    .flex-1 {
        padding: 1rem;
    }

    .mobile-menu-button {
        display: block; /* Show the button */
    }
}


/* Apply a Brutalist design to forms, buttons, and fields */

input,
textarea,
button,
select,
option,
optgroup,
fieldset,
legend,
datalist,
keygen,
output,
progress,
meter,
input[type="file"],
input[type="range"],
input[type="date"],
input[type="time"],
input[type="datetime-local"],
input[type="month"],
input[type="week"],
input[type="url"],
input[type="search"],
input[type="tel"],
input[type="color"],
input[type="image"],
input[type="submit"],
input[type="reset"],
input[type="button"],
textarea,
button[type="submit"],
button[type="reset"],
button[type="button"] {
   font-family: monospace, sans-serif !important; /* Monospace font */
   border: 0.1px solid var(--border-color) !important; /* Set default for all */
   box-shadow: 6px 6px 0 var(--border-color) !important; /* 3px shadow for more effect */
   border-radius: 0 !important;          /* To remove curve corners */
   padding: 0.5em !important;          /* Some whitespace */
    margin-bottom: 1em !important;
    box-sizing: border-box !important; /* Important for width consistency */
   outline: none !important;
   transition: all 0.2s ease-in-out !important;
}

/* Special cases */
input[type="checkbox"],
input[type="radio"] {
    appearance: none !important;   /* Better control */
    width: 1.2em !important;
    height: 1.2em !important;
    border: 3px solid var(--border-color) !important;
    background-color: var(--bg-color) !important;
    box-shadow: none !important;   /* Remove default shadow */
    cursor: pointer !important;
    vertical-align: middle !important;
}

input[type="checkbox"]:checked,
input[type="radio"]:checked {
    background-color: var(--secondary-color) !important;
    border-color: var(--border-color) !important;
}
input[type="file"] {
  box-shadow: none !important;
}
/* Make the layout more interactive */
textarea {
    resize: vertical !important;
}

select {
    appearance: none !important;  /* No default */
    background-image: url('data:image/svg+xml;utf8,<svg fill="%23222" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path d="M7 10l5 5 5-5z"/><path d="M0 0h24v24H0z" fill="none"/></svg>') !important;
    background-repeat: no-repeat !important;
    background-position: right 0.5em top 50%, 0 0 !important;
    background-size: 1em auto !important;
    padding-right: 2.5em !important;
}
/* Specific for each hover effects */
input[type="submit"]:hover,
input[type="reset"]:hover,
input[type="button"]:hover,
button[type="submit"]:hover,
button[type="reset"]:hover,
button[type="button"]:hover,
select:hover {
    background-color: var(--primary-color) !important;
    color: var(--bg-color) !important;
    box-shadow: none !important;
    transform: translate(3px, 3px) !important;
}

input:focus,
textarea:focus,
select:focus {
    border-color: var(--accent-color) !important;
    box-shadow: none !important;
}

fieldset {
 border: 2px solid var(--border-color) !important; /* Ensure border exists */
 padding: 1em !important;
 margin-bottom: 1em !important;
}


  </style>
</head>
<body class="100">
  <!-- Mobile Top Navigation (shows hamburger) -->
  <nav class="bg-black p-4 text-white md:hidden flex justify-between items-center border-b-4 border-white">
    <a href="<?php echo BASE_URL; ?>" class="text-xl font-bold uppercase py-2">RevenueSure</a>
    <button id="mobileMenuButton" class="p-2 focus:outline-none">
      <i class="fa-solid fa-bars fa-lg"></i>
    </button>
  </nav>

  <!-- Main Container -->
  <div class="flex h-screen">
    <!-- Sidebar: hidden on mobile, shown on md+ -->
    <aside id="sidebar" class="bg-gray-100 text-gray-700 w-64 p-4 hidden md:block border-r-4 border-black">
    <a href="<?php echo BASE_URL; ?>" class="text-2xl font-bold block mb-4 pt-4 pl-4 text-gray-800 uppercase" id="headerLogo">RevenueSure</a>
      <nav>
        <?php if (isset($_SESSION['user_id'])): ?>
          <!-- User Menu -->
          <?php
          function isActive($path) {
              $current_page = $_GET['route'] ?? '';
              return strpos($current_page, $path) === 0;
          }
          function isParentActive($path) {
              $current_page = $_GET['route'] ?? '';
              return strpos($current_page, $path) !== false;
          }
          ?>
          <a href="<?php echo BASE_URL; ?>dashboard" class="menu-item block hover: px-4 py-3 <?php echo isActive('dashboard') ? 'active' : ''; ?>">
            <i class="fa-solid fa-house mr-2"></i>Dashboard
          </a>
          <a href="<?php echo BASE_URL; ?>credits/manage" class="menu-item block hover: px-4 py-3 <?php echo isActive('credits/manage') ? 'active' : ''; ?>">
            <i class="fa-solid fa-wallet mr-2"></i>Manage Credits
          </a>
          <a href="<?php echo BASE_URL; ?>notes/manage" class="block py-2 px-4 hover: px-4 py-3 <?php echo isActive('notes/manage') ? 'active' : ''; ?>">
            <i class="fa-solid fa-sticky-note mr-2"></i>Notebook
          </a>
          <a href="<?php echo BASE_URL; ?>documents/manage" class="block py-2 px-4 hover: px-4 py-3 <?php echo isActive('notes/manage') ? 'active' : ''; ?>">
            <i class="fa-regular fa-file-word mr-2"></i>Docs
          </a>
          <a href="<?php echo BASE_URL; ?>ai/index" class="block py-2 px-4 hover: px-4 py-3 <?php echo isActive('ai/index') ? 'active' : ''; ?>">
          <i class="fa-solid fa-magic mr-2"></i>AI
          </a>

          <!-- Admin Menu -->
          <?php if ($_SESSION['role'] === 'admin'): ?>
            <h6 class="text-gray-500 uppercase mt-4 mb-2 px-4">Admin</h6>
            <div class="menu-item <?php if (isParentActive('leads/add') || isParentActive('leads/manage') || isParentActive('leads/import') || isParentActive('leads/yourleads')) echo 'active'; ?>">
              <a class="block py-2 px-4 hover:bg-gray-200 flex items-center">
                <i class="fa-solid fa-address-book mr-2"></i>Manage Leads
              </a>
              <div class="submenu">
                <a href="<?php echo BASE_URL; ?>leads/manage" class="menu-item block hover:bg-gray-200 px-4 py-3 <?php echo isActive('leads/manage') ? 'active' : ''; ?>">
                  <i class="fa-solid fa-user-circle mr-2"></i>Manage Leads
                </a>
                <a href="<?php echo BASE_URL; ?>leads/add" class="block py-2 px-4 hover:bg-gray-200 <?php echo isActive('leads/add') ? 'active' : ''; ?>">
                  <i class="fa-solid fa-plus mr-2"></i>Add Lead
                </a>
                <a href="<?php echo BASE_URL; ?>leads/yourleads" class="menu-item block hover:bg-gray-200 px-4 py-3 <?php echo isActive('leads/yourleads') ? 'active' : ''; ?>">
                  <i class="fa-solid fa-user-circle mr-2"></i>Your Leads
                </a>
                <a href="<?php echo BASE_URL; ?>leads/search" class="menu-item block hover:bg-gray-200 px-4 py-3 <?php echo isActive('leads/search') ? 'active' : ''; ?>">
                  <i class="fa-solid fa-search mr-2"></i>Search Leads
                </a>
                <a href="<?php echo BASE_URL; ?>leads/import" class="block py-2 px-4 hover:bg-gray-200 <?php echo isActive('leads/import') ? 'active' : ''; ?>">
                  <i class="fa-solid fa-file-import mr-2"></i>Import Leads
                </a>
              </div>
            </div>
            <div class="menu-item <?php if (isParentActive('customers/add') || isParentActive('customers/manage') || isParentActive('customers/view')) echo 'active'; ?>">
              <a class="block py-2 px-4 hover:bg-gray-200 flex items-center">
                <i class="fa-solid fa-user-friends mr-2"></i>Manage Customers
              </a>
              <div class="submenu">
                <a href="<?php echo BASE_URL; ?>customers/add" class="block py-2 px-4 hover:bg-gray-200 <?php echo isActive('customers/add') ? 'active' : ''; ?>">
                  <i class="fa-solid fa-plus mr-2"></i> Add Customer
                </a>
                <a href="<?php echo BASE_URL; ?>customers/manage" class="block py-2 px-4 hover:bg-gray-200 <?php echo isActive('customers/manage') ? 'active' : ''; ?>">
                  <i class="fa-solid fa-list-ul mr-2"></i>View Customers
                </a>
                <a href="<?php echo BASE_URL; ?>customers/view" class="block py-2 px-4 hover:bg-gray-200 <?php echo isParentActive('customers/view') ? 'active' : ''; ?>">
                  <i class="fa-solid fa-eye mr-2"></i>Customer Profile
                </a>
              </div>
            </div>
            <div class="menu-item <?php if (isParentActive('employees/add') || isParentActive('employees/manage')) echo 'active'; ?>">
              <a class="block py-2 px-4 hover:bg-gray-200 flex items-center">
                <i class="fa-solid fa-user-tie mr-2"></i>Manage Employees
              </a>
              <div class="submenu">
                <a href="<?php echo BASE_URL; ?>employees/add" class="block py-2 px-4 hover:bg-gray-200 <?php echo isActive('employees/add') ? 'active' : ''; ?>">
                  <i class="fa-solid fa-user-plus mr-2"></i>Add Employee
                </a>
                <a href="<?php echo BASE_URL; ?>employees/manage" class="block py-2 px-4 hover:bg-gray-200 <?php echo isActive('employees/manage') ? 'active' : ''; ?>">
                  <i class="fa-solid fa-users-cog mr-2"></i>View Employees
                </a>
              </div>
            </div>
            <div class="menu-item <?php if (isParentActive('categories/manage') || isParentActive('categories/add')) echo 'active'; ?>">
              <a class="block py-2 px-4 hover:bg-gray-200 flex items-center">
                <i class="fa-solid fa-th-large mr-2"></i>Manage Categories
              </a>
              <div class="submenu">
                <a href="<?php echo BASE_URL; ?>categories/manage" class="block py-2 px-4 hover:bg-gray-200 <?php echo isActive('categories/manage') ? 'active' : ''; ?>">
                  <i class="fa-solid fa-list-alt mr-2"></i>View Categories
                </a>
                <a href="<?php echo BASE_URL; ?>categories/add" class="block py-2 px-4 hover:bg-gray-200 <?php echo isActive('categories/add') ? 'active' : ''; ?>">
                  <i class="fa-solid fa-plus mr-2"></i>Add Category
                </a>
              </div>
            </div>
            <div class="menu-item <?php if (isParentActive('invoices/manage') || isParentActive('invoices/add') || isParentActive('invoices/view')) echo 'active'; ?>">
              <a class="block py-2 px-4 hover:bg-gray-200 flex items-center">
                <i class="fa-solid fa-file-invoice mr-2"></i>Manage Invoices
              </a>
              <div class="submenu">
                <a href="<?php echo BASE_URL; ?>invoices/manage" class="block py-2 px-4 hover:bg-gray-200 <?php echo isActive('invoices/manage') ? 'active' : ''; ?>">
                  <i class="fa-solid fa-list-ul mr-2"></i>View Invoices
                </a>
                <a href="<?php echo BASE_URL; ?>invoices/add" class="block py-2 px-4 hover:bg-gray-200 <?php echo isActive('invoices/add') ? 'active' : ''; ?>">
                  <i class="fa-solid fa-plus mr-2"></i>Add Invoice
                </a>
                <a href="<?php echo BASE_URL; ?>invoices/view" class="block py-2 px-4 hover:bg-gray-200 <?php echo isParentActive('invoices/view') ? 'active' : ''; ?>">
                  <i class="fa-solid fa-eye mr-2"></i>Invoice Details
                </a>
              </div>
            </div>
            <div class="menu-item <?php if (isParentActive('projects/manage') || isParentActive('projects/add') || isParentActive('projects/view') || isParentActive('projects/categories/manage') || isParentActive('discussions/manage') || isParentActive('tasks/viewtasks')) echo 'active'; ?>">
              <a class="block py-2 px-4 hover:bg-gray-200 flex items-center">
                <i class="fa-solid fa-clipboard-check mr-2"></i>Manage Projects & Tasks
              </a>
              <div class="submenu">
                <a href="<?php echo BASE_URL; ?>projects/manage" class="block py-2 px-4 hover:bg-gray-200 <?php echo isActive('projects/manage') ? 'active' : ''; ?>">
                  <i class="fa-solid fa-list-ul mr-2"></i>Projects
                </a>
                 <a href="<?php echo BASE_URL; ?>tasks/viewtasks" class="menu-item block hover:bg-gray-200 px-4 py-3 <?php echo isActive('tasks/viewtasks') ? 'active' : ''; ?>">
                  <i class="fa-solid fa-tasks mr-2"></i>Tasks
                </a>
                <a href="<?php echo BASE_URL; ?>discussions/manage" class="block py-2 px-4 hover:bg-gray-200 <?php echo isActive('discussions/manage') ? 'active' : ''; ?>">
                  <i class="fa-solid fa-plus mr-2"></i>Discussions
                </a>
                <a href="<?php echo BASE_URL; ?>projects/features/manage" class="block py-2 px-4 hover:bg-gray-200 <?php echo isActive('projects/features/manage') ? 'active' : ''; ?>">
                  <i class="fa-solid fa-plus mr-2"></i>Features Tracker
                </a>
                <a href="<?php echo BASE_URL; ?>projects/issues/manage" class="block py-2 px-4 hover:bg-gray-200 <?php echo isActive('projects/issues/manage') ? 'active' : ''; ?>">
                  <i class="fa-solid fa-plus mr-2"></i>Issue Tracker
                </a>
                <a href="<?php echo BASE_URL; ?>projects/categories/manage" class="block py-2 px-4 hover:bg-gray-200 <?php echo isActive('projects/categories/manage') ? 'active' : ''; ?>">
                  <i class="fa-solid fa-list-alt mr-2"></i>Project Categories
                </a>
              </div>
            </div>
            <div class="menu-item <?php if (isParentActive('contracts/manage') || isParentActive('contracts/add')) echo 'active'; ?>">
              <a class="block py-2 px-4 hover:bg-gray-200 flex items-center">
                <i class="fa-solid fa-file-signature mr-2"></i>Manage Contracts
              </a>
              <div class="submenu">
                <a href="<?php echo BASE_URL; ?>contracts/manage" class="block py-2 px-4 hover:bg-gray-200 <?php echo isActive('contracts/manage') ? 'active' : ''; ?>">
                  <i class="fa-solid fa-list-ul mr-2"></i>View Contracts
                </a>
                <a href="<?php echo BASE_URL; ?>contracts/add" class="block py-2 px-4 hover:bg-gray-200 <?php echo isActive('contracts/add') ? 'active' : ''; ?>">
                  <i class="fa-solid fa-plus mr-2"></i> Create Contract
                </a>
              </div>
            </div>
            <div class="menu-item <?php if (isParentActive('support_tickets/manage') || isParentActive('support_tickets/add') || isParentActive('support_tickets/view')) echo 'active'; ?>">
              <a class="block py-2 px-4 hover:bg-gray-200 flex items-center">
                <i class="fa-solid fa-headset mr-2"></i>Support Tickets
              </a>
              <div class="submenu">
                <a href="<?php echo BASE_URL; ?>support_tickets/manage" class="block py-2 px-4 hover:bg-gray-200 <?php echo isActive('support_tickets/manage') ? 'active' : ''; ?>">
                  <i class="fa-solid fa-list-ul mr-2"></i>Manage Tickets
                </a>
                <a href="<?php echo BASE_URL; ?>support_tickets/add" class="block py-2 px-4 hover:bg-gray-200 <?php echo isActive('support_tickets/add') ? 'active' : ''; ?>">
                  <i class="fa-solid fa-plus mr-2"></i>Add Ticket
                </a>
              </div>
            </div>
            <div class="menu-item <?php if (isParentActive('team/manage') || isParentActive('team/add') || isParentActive('team/edit')) echo 'active'; ?>">
              <a class="block py-2 px-4 hover:bg-gray-200 flex items-center">
              <i class="fa-solid fa-user-friends mr-2"></i>Manage Team
              </a>
              <div class="submenu">
                <a href="<?php echo BASE_URL; ?>team/manage" class="block py-2 px-4 hover:bg-gray-200 <?php echo isActive('team/manage') ? 'active' : ''; ?>">
                  <i class="fa-solid fa-list-ul mr-2"></i>View Team
                </a>
                <a href="<?php echo BASE_URL; ?>team/add" class="block py-2 px-4 hover:bg-gray-200 <?php echo isActive('team/add') ? 'active' : ''; ?>">
                  <i class="fa-solid fa-user-plus mr-2"></i>Add Team Member
                </a>
                <a href="<?php echo BASE_URL; ?>team/roles/manage" class="block py-2 px-4 hover:bg-gray-200 <?php echo isParentActive('team/roles/manage') ? 'active' : ''; ?>">
                  <i class="fa-solid fa-user-tag mr-2"></i>Manage Roles
                </a>
                <a href="<?php echo BASE_URL; ?>departments/manage" class="block py-2 px-4 hover:bg-gray-200 <?php echo isParentActive('departments/manage') ? 'active' : ''; ?>">
                  <i class="fa-solid fa-list-alt mr-2"></i>Manage Departments
                </a>
              </div>
            </div>
            <div class="menu-item <?php if (isParentActive('knowledge_base/manage') || isParentActive('knowledge_base/add') || isParentActive('knowledge_base/view')) echo 'active'; ?>">
              <a class="block py-2 px-4 hover:bg-gray-200 flex items-center">
                <i class="fa-solid fa-book-open mr-2"></i>Knowledge Base
              </a>
              <div class="submenu">
                <a href="<?php echo BASE_URL; ?>knowledge_base/manage" class="block py-2 px-4 hover:bg-gray-200 <?php echo isActive('knowledge_base/manage') ? 'active' : ''; ?>">
                  <i class="fa-solid fa-list-ul mr-2"></i> View Articles
                </a>
                <a href="<?php echo BASE_URL; ?>knowledge_base/add" class="block py-2 px-4 hover:bg-gray-200 <?php echo isActive('knowledge_base/add') ? 'active' : ''; ?>">
                  <i class="fa-solid fa-plus mr-2"></i> Add Article
                </a>
                <a href="<?php echo BASE_URL; ?>knowledge_base/categories/manage" class="block py-2 px-4 hover:bg-gray-200 <?php echo isParentActive('knowledge_base/categories/manage') ? 'active' : ''; ?>">
                  <i class="fa-solid fa-list-alt mr-2"></i>Manage KB Categories
                </a>
                <a href="<?php echo BASE_URL; ?>knowledge_base/request/manage" class="block py-2 px-4 hover:bg-gray-200 <?php echo isParentActive('knowledge_base/request/manage') ? 'active' : ''; ?>">
                    <i class="fa-solid fa-list-alt mr-2"></i>Knowledge Base Requests
                </a>
              </div>
            </div>
            <div class="menu-item <?php if (isParentActive('expenses/manage') || isParentActive('expenses/add')) echo 'active'; ?>">
              <a class="block py-2 px-4 hover:bg-gray-200 flex items-center">
                <i class="fa-solid fa-receipt mr-2"></i>Manage Expenses
              </a>
              <div class="submenu">
                <a href="<?php echo BASE_URL; ?>expenses/manage" class="block py-2 px-4 hover:bg-gray-200 <?php echo isActive('expenses/manage') ? 'active' : ''; ?>">
                  <i class="fa-solid fa-list-ul mr-2"></i>View Expenses
                </a>
                <a href="<?php echo BASE_URL; ?>expenses/add" class="block py-2 px-4 hover:bg-gray-200 <?php echo isActive('expenses/add') ? 'active' : ''; ?>">
                  <i class="fa-solid fa-plus mr-2"></i>Record Expense
                </a>
              </div>
            </div>
            <!-- User Mailbox-->
            <div class="menu-item <?php if (isParentActive('mail/index') || isParentActive('mail/compose')) echo 'active'; ?>">
                <a class="block py-2 px-4 hover:bg-gray-200 flex items-center">
                  <i class="fa-solid fa-file-signature mr-2"></i>Mailbox
                </a>
                <div class="submenu">
                  <a href="<?php echo BASE_URL; ?>mail/index" class="block py-2 px-4 hover:bg-gray-200 <?php echo isActive('mail/compose') ? 'active' : ''; ?>">
                    <i class="fa-solid fa-list-ul mr-2"></i>Inbox
                  </a>
                  <a href="<?php echo BASE_URL; ?>mail/compose" class="block py-2 px-4 hover:bg-gray-200 <?php echo isActive('mail/compose') ? 'active' : ''; ?>">
                    <i class="fa-solid fa-plus mr-2"></i> Compose
                  </a>
                  </a>
                  <a href="<?php echo BASE_URL; ?>mail/settings" class="block py-2 px-4 hover:bg-gray-200 <?php echo isActive('mail/settings') ? 'active' : ''; ?>">
                    <i class="fa-solid fa-plus mr-2"></i> E-mail Settings
                  </a>
                </div>
              </div>

              <!-- Accounting Menu -->
            <div class="menu-item <?php if (isParentActive('accounting/dashboard') || isParentActive('accounting/ledger') || isParentActive('accounting/reconciliation') || isParentActive('accounting/manage_accountants')) echo 'active'; ?>">
              <a class="block py-2 px-4 hover:bg-gray-200 flex items-center">
                <i class="fa-solid fa-calculator mr-2"></i>Accounting
              </a>
              <div class="submenu">
                <a href="<?php echo BASE_URL; ?>accounting/dashboard" class="block py-2 px-4 hover:bg-gray-200 <?php echo isActive('accounting/dashboard') ? 'active' : ''; ?>">
                  <i class="fa-solid fa-chart-line mr-2"></i>Dashboard
                </a>
                <a href="<?php echo BASE_URL; ?>accounting/ledger" class="block py-2 px-4 hover:bg-gray-200 <?php echo isActive('accounting/ledger') ? 'active' : ''; ?>">
                  <i class="fa-solid fa-book-open mr-2"></i>Ledger
                </a>
                <a href="<?php echo BASE_URL; ?>accounting/reconciliation" class="block py-2 px-4 hover:bg-gray-200 <?php echo isActive('accounting/reconciliation') ? 'active' : ''; ?>">
                  <i class="fa-solid fa-check-double mr-2"></i>Reconciliation
                </a>
                <a href="<?php echo BASE_URL; ?>accounting/manage_accountants" class="block py-2 px-4 hover:bg-gray-200 <?php echo isActive('accounting/manage_accountants') ? 'active' : ''; ?>">
                  <i class="fa-solid fa-user-cog mr-2"></i>Manage Accountants
                </a>
              </div>
            </div>

            <div>
              <a href="<?php echo BASE_URL; ?>reports/leads/dashboard" class="block py-2 px-4 hover:bg-gray-200 <?php echo isActive('reports/leads/dashboard') ? 'active' : ''; ?>">
                <i class="fa-solid fa-chart-pie mr-2"></i>Reporting
              </a>
              <a href="<?php echo BASE_URL; ?>views/global_dashboard" class="block py-2 px-4 hover:bg-gray-200">
                <i class="fas fa-users mr-2"></i> View All Entities
            </a>
              <a href="<?php echo BASE_URL; ?>settings" class="bg-indigo-700 text-white px-4 py-2 rounded-xl hover:bg-indigo-900 transition duration-300 inline-block mt-4">
                <i class="fa-solid fa-gear mr-2"></i>Settings
              </a>
            </div>
          <?php endif; ?>

          <a href="<?php echo BASE_URL; ?>auth/logout" class="block py-2 px-4 hover:bg-gray-200 mt-4">
            <i class="fa-solid fa-right-from-bracket mr-2"></i>Logout
          </a>
        <?php else: ?>
          <a href="<?php echo BASE_URL; ?>auth/login" class="block py-2 px-4 hover:bg-gray-200">
            <i class="fa-solid fa-right-to-bracket mr-2"></i>Login
          </a>
          <a href="<?php echo BASE_URL; ?>auth/register" class="block py-2 px-4 hover:bg-gray-200">
            <i class="fa-solid fa-user-plus mr-2"></i>Register
          </a>
        <?php endif; ?>
      </nav>
    </aside>

    <!-- Main Content -->
    <div class="flex-1 p-4">
      <!-- Top Navigation (for Notifications) -->
      <nav class="bg-black p-4 text-white mb-6 border-b-4 border-white">
        <div class="container mx-auto flex justify-end items-center">
          <?php if (isset($_SESSION['user_id'])): ?>
            <div class="relative top-nav-button mr-4 hover:bg-gray-500 p-1.5 transition-colors">
              <button id="profileButton" class="relative flex items-center">
                <?php
                $stmt = $conn->prepare("SELECT profile_picture FROM users WHERE id = :user_id");
                $stmt->bindParam(':user_id', $_SESSION['user_id']);
                $stmt->execute();
                $user = $stmt->fetch(PDO::FETCH_ASSOC);
                $profile_picture = $user['profile_picture'];

                if ($profile_picture) : ?>
                  <img src="<?php echo BASE_URL . $profile_picture; ?>" alt="Profile Picture" class="w-8 h-8 object-cover" style="border: 2px solid white;">
                <?php else : ?>
                  <i class="fa-solid fa-user-circle fa-lg"></i>
                <?php endif; ?>
              </button>
              <div id="profileDropdown" class="hidden absolute right-0 mt-2 w-48 bg-white border-4 border-black top-nav-dropdown">
                <a href="<?php echo BASE_URL; ?>profile/view" class="block px-4 py-2 text-gray-800 hover:bg-gray-100">Profile</a>
                <a href="<?php echo BASE_URL; ?>auth/logout" class="block px-4 py-2 text-gray-800 hover:bg-gray-100">Logout</a>
              </div>
            </div>
            <div class="relative top-nav-button hover:bg-gray-500 p-1.5 transition-colors">
              <button id="notificationButton" class="relative">
                <!-- Bell Icon -->
                <i class="fa-solid fa-bell"></i>
                <?php
                $stmt = $conn->prepare("SELECT COUNT(*) as unread FROM notifications WHERE user_id = :user_id AND is_read = 0");
                $stmt->bindParam(':user_id', $_SESSION['user_id']);
                $stmt->execute();
                $unread = $stmt->fetch(PDO::FETCH_ASSOC)['unread'];
                if ($unread > 0) : ?>
                  <!-- Notification Count -->
                  <span class="bg-red-500 text-white text-xs px-2 py-1 absolute -top-2 -right-2" style="border: 1px solid white;"><?php echo $unread; ?></span>
                <?php endif; ?>
              </button>
              <div id="notificationDropdown" class="hidden absolute right-0 mt-2 w-64 bg-white border-4 border-black top-nav-dropdown">
                <?php
                $stmt = $conn->prepare("SELECT * FROM notifications WHERE user_id = :user_id ORDER BY created_at DESC LIMIT 5");
                $stmt->bindParam(':user_id', $_SESSION['user_id']);
                $stmt->execute();
                $notifications = $stmt->fetchAll(PDO::FETCH_ASSOC);
                ?>
                <?php if ($notifications) : ?>
                  <?php foreach ($notifications as $notification) : ?>
                    <a href="<?php echo BASE_URL; ?>notifications/view?id=<?php echo $notification['id']; ?>" class="block px-4 py-2 text-gray-800 hover:bg-gray-100">
                      <?php echo htmlspecialchars($notification['message']); ?>
                      <?php if (!$notification['is_read']) : ?>
                        <span class="text-xs text-blue-600">New</span>
                      <?php endif; ?>
                    </a>
                  <?php endforeach; ?>
                <?php else : ?>
                  <p class="px-4 py-2 text-gray-600">No notifications.</p>
                <?php endif; ?>
              </div>
            </div>
          <?php endif; ?>
        </div>
      </nav>

<?php
   $headerContent = ob_get_clean(); // Get the buffered content
     if (ENABLE_CACHE){
        setCache($cacheKey, $headerContent); // Save to cache
     }
    echo $headerContent; // Output the content
}
?>
      <script>

        document.getElementById('notificationButton').addEventListener('click', function() {
          document.getElementById('notificationDropdown').classList.toggle('hidden');
        });
        document.getElementById('profileButton').addEventListener('click', function() {
          document.getElementById('profileDropdown').classList.toggle('hidden');
        });
      </script>
      <script>
document.addEventListener('DOMContentLoaded', function() {
    const mobileMenuButton = document.getElementById('mobileMenuButton');
    const aside = document.querySelector('aside');

    mobileMenuButton.addEventListener('click', function() {
        aside.classList.toggle('open');
    });
});
</script>
      <!-- Main Content Area -->
      <div class="container mx-auto px-4">
        <!-- Your main content goes here -->