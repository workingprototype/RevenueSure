<?php
    require_once __DIR__ . '/../helper/cache.php'; // Include cache functions

    $cacheKey = 'footer_' . (isset($_SESSION['user_id']) ? 'user_' . $_SESSION['user_id'] : 'anonymous');
    $cacheExpiration = 3600; // Cache for 1 hour

    if (ENABLE_CACHE && isCacheValid($cacheKey, $cacheExpiration)) {
        echo getCache($cacheKey);
    } else {
        ob_start(); // Start output buffering
        ?>
</div> <!-- Close container -->

<!-- Footer -->
<!-- <footer class="bg-blue-600 p-4 text-white mt-10">
    <div class="container mx-auto text-center">
        <p>&copy; 2025 RevenueSure All rights reserved.</p>
    </div>
</footer> -->
</body>
</html>

<?php
        $footerContent = ob_get_clean(); // Get the buffered content
        if (ENABLE_CACHE){
            setCache($cacheKey, $footerContent); // Save to cache
         }
         echo $footerContent; // Output the content
     }
    ?>
    <script>
    function showToast(message, type = 'success') {
        var toast = document.getElementById('toast');
        toast.textContent = message;

        toast.className = 'fixed top-5 right-5 z-50 px-4 py-3 rounded shadow-md transition-opacity duration-300 ease-in-out';
        toast.classList.add('fade-in', type === 'success' ? 'bg-green-100 border-green-500 text-green-700' : 'bg-red-100 border-red-500 text-red-700');
        toast.style.display = "block";
        // Hide after 3 seconds
        setTimeout(function () {
            toast.style.display = 'none';
        }, 3000);
    }
</script>