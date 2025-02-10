<?php
require_once ROOT_PATH . 'helper/core.php';
redirectIfUnauthorized(true); // Requires user to be logged in

$error = '';
$success = '';
$query = "SELECT knowledge_base_articles.*, knowledge_base_categories.name as category_name, users.username as author_name
          FROM knowledge_base_articles
            LEFT JOIN knowledge_base_categories ON knowledge_base_articles.category_id = knowledge_base_categories.id
            LEFT JOIN users ON knowledge_base_articles.user_id = users.id";
$where_conditions = [];
$params = [];
// Search by name, content or tag
if(isset($_GET['search']) && !empty($_GET['search'])){
       $search_term = trim($_GET['search']);
       $where_conditions[] = " (knowledge_base_articles.title LIKE :search OR knowledge_base_articles.content LIKE :search OR knowledge_base_articles.id IN (SELECT article_id FROM knowledge_base_article_tags WHERE tag LIKE :search))";
       $params[':search'] = "%$search_term%";
}
  // Category
if(isset($_GET['category_id']) && !empty($_GET['category_id'])){
     $where_conditions[] = "knowledge_base_articles.category_id = :category_id";
   $params[':category_id'] = $_GET['category_id'];
}
if(!empty($where_conditions)){
    $query .= " WHERE " . implode(" AND ", $where_conditions);
}
// Fetch all articles
$query .= " ORDER BY created_at DESC";
$stmt = $conn->prepare($query);
foreach ($params as $key => $value) {
    $stmt->bindValue($key, $value);
}
$stmt->execute();
$articles = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Fetch all categories
$stmt = $conn->prepare("SELECT * FROM knowledge_base_categories");
$stmt->execute();
$categories = $stmt->fetchAll(PDO::FETCH_ASSOC);


?>
<div class="container mx-auto p-6 fade-in">
   <h1 class="text-3xl font-bold text-gray-800 mb-6 uppercase tracking-wide border-b-2 border-gray-400 pb-2">Manage Knowledge Base</h1>
    <div class="flex justify-between items-center mb-8">
      <a href="<?php echo BASE_URL; ?>knowledge_base/add" class="bg-blue-700 text-white px-6 py-3 rounded-xl hover:bg-blue-900 transition duration-300 shadow-md uppercase tracking-wide">
         <i class="fas fa-plus-circle mr-2"></i> Add Article
           </a>
     <div class="flex flex-wrap gap-2">
          <form method="GET" action="" class="flex gap-2 flex-wrap">
              <input type="text" name="search" id="search" placeholder="Search by Title, Content, or Tag" class="px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-600 flex-1" value="<?php echo isset($_GET['search']) ? $_GET['search'] : ''; ?>">
               <select name="category_id" id="category_id" class="px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-600 appearance-none">
                   <option value="">Select Category</option>
                    <?php foreach ($categories as $category): ?>
                         <option value="<?php echo $category['id']; ?>" <?php if(isset($_GET['category_id']) && $_GET['category_id'] == $category['id']) echo 'selected'; ?>><?php echo htmlspecialchars($category['name']); ?></option>
                      <?php endforeach; ?>
                </select>
              <button type="submit" class="bg-blue-700 text-white px-4 py-2 rounded-lg hover:bg-blue-900 transition duration-300 shadow-md">Filter</button>
             </form>
                <?php
                 $hasFilters = !empty($_GET['search']) || !empty($_GET['category_id']);
                 ?>
                    <?php if ($hasFilters): ?>
                     <a href="<?php echo BASE_URL; ?>knowledge_base/manage" class="bg-red-700 text-white px-4 py-2 rounded-lg hover:bg-gray-900 transition duration-300 shadow-md">
                        <i class="fas fa-times-circle mr-2"></i> Clear Filters
                       </a>
                 <?php endif; ?>
       </div>
   </div>
    <!-- Article Table -->
        <div class="bg-white p-6 rounded-lg shadow-md">
           <table class="w-full text-left">
                <thead>
                   <tr>
                      <th class="px-4 py-2">Title</th>
                         <th class="px-4 py-2">Category</th>
                        <th class="px-4 py-2">Author</th>
                          <th class="px-4 py-2">Tags</th>
                        <th class="px-4 py-2">Visibility</th>
                         <th class="px-4 py-2">Created At</th>
                       <th class="px-4 py-2">Updated At</th>
                       <th class="px-4 py-2">Views</th>
                         <th class="px-4 py-2">Actions</th>
                    </tr>
                 </thead>
                <tbody>
                    <?php if ($articles): ?>
                        <?php foreach ($articles as $article): ?>
                         <tr class="border-b">
                               <td class="px-4 py-2"><a href="<?php echo BASE_URL; ?>knowledge_base/view?id=<?php echo $article['id']; ?>" class="text-blue-600 hover:underline"><?php echo htmlspecialchars($article['title']); ?></a></td>
                              <td class="px-4 py-2"><?php echo htmlspecialchars($article['category_name'] ? $article['category_name'] : "Uncategorized"); ?></td>
                                <td class="px-4 py-2"><?php echo htmlspecialchars($article['author_name']); ?></td>
                              <td class="px-4 py-2">
                                  <?php
                                        $stmt = $conn->prepare("SELECT tag FROM knowledge_base_article_tags WHERE article_id = :article_id");
                                         $stmt->bindParam(':article_id', $article['id']);
                                           $stmt->execute();
                                        $tags = $stmt->fetchAll(PDO::FETCH_COLUMN);
                                      if($tags):
                                           foreach($tags as $tag){
                                             echo '<span class="inline-block bg-gray-200 px-2 py-1 m-1 rounded-full text-gray-700 text-sm">' . htmlspecialchars($tag) . '</span>';
                                            }
                                         else :
                                                echo "No Tags Added";
                                        endif;
                                 ?>
                             </td>
                            <td class="px-4 py-2"><?php echo htmlspecialchars($article['visibility']); ?></td>
                              <td class="px-4 py-2"><?php echo htmlspecialchars($article['created_at']); ?></td>
                                <td class="px-4 py-2"><?php echo htmlspecialchars($article['updated_at'] ? $article['updated_at'] : 'N/A'); ?></td>
                               <td class="px-4 py-2"><?php echo htmlspecialchars($article['view_count']); ?></td>
                                <td class="px-4 py-2">
                                   <a href="<?php echo BASE_URL; ?>knowledge_base/edit?id=<?php echo $article['id']; ?>" class="text-blue-600 hover:underline">Edit</a>
                                   <a href="<?php echo BASE_URL; ?>knowledge_base/delete?id=<?php echo $article['id']; ?>" class="text-red-600 hover:underline ml-2">Delete</a>
                                </td>
                           </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="8" class="px-4 py-2 text-center text-gray-600">No articles found.</td>
                        </tr>
                   <?php endif; ?>
               </tbody>
         </table>
      </div>
</div>
