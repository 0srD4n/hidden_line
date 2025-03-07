<?php 
include 'template/navbar.php';
print_header('Hidden Line');

// Initialize variables
$errors = [];
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$per_page = 5; // Number of posts per page
$offset = ($page - 1) * $per_page;


// Fetch posts with pagination
try {
    global $db;
    
    $countQuery = "SELECT COUNT(*) FROM Post";
    $query = "SELECT p.*, u.username FROM Post p 
              JOIN User u ON p.user_id = u.id 
              ORDER BY p.created_at DESC LIMIT ? OFFSET ?";
    $countStmt = $db->prepare($countQuery);
    $countStmt->execute();
    
    $stmt = $db->prepare($query);
    $stmt->bindParam(1, $per_page, PDO::PARAM_INT);
    $stmt->bindParam(2, $offset, PDO::PARAM_INT);
    $stmt->execute();
    
    $posts = $stmt->fetchAll(PDO::FETCH_ASSOC);
    $total_posts = $countStmt->fetchColumn();
    $total_pages = ceil($total_posts / $per_page);
    
} catch (PDOException $e) {
    $errors[] = "Error fetching posts: " . $e->getMessage();
}

// Group posts by category
$postsByCategory = [];
foreach ($posts as $post) {
    $category = $post['category'];
    if (!isset($postsByCategory[$category])) {
        $postsByCategory[$category] = [];
    }
    $postsByCategory[$category][] = $post;
}
?>

<body style="background: #1a1a1a;">
<div class="container mt-4 text-light">
    <div class="row mb-4">
        <div class="col-md-12">
            <h1>Hidden Line - Discover and Share Links</h1>
            <p class="lead">Share your links with the Hidden Line community. Register to start sharing. Found a bug or having issues? Contact us at <a href="mailto:XplDan@proton.me" style="text-decoration: none; color: #007bff;">XplDan@proton.me</a> or join our <a href="https://t.me/+K4H6i81jmAU4NTk1" style="text-decoration: none; color: #007bff;">Telegram Group</a></p>
        </div>
    </div>
    
    <?php if (!empty($errors)): ?>
        <div class="alert alert-danger">
            <ul class="mb-0">
                <?php foreach ($errors as $error): ?>
                    <li><?= htmlspecialchars($error) ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>
    
    <?php if (empty($posts)): ?>
        <div class="alert alert-info">
            <p class="mb-0">No links found. Check back later!</p>
        </div>
    <?php else: ?>
        <?php foreach ($postsByCategory as $category => $categoryPosts): ?>
            <div class="row mt-4">
                <div class="col-12">
                    <h3 class="border-bottom pb-2 mb-3">
                        <span class="badge bg-<?php 
                            switch($category) {
                                case 'Chat': echo 'success'; break;
                                case 'Forum': echo 'info'; break;
                                case 'Search': echo 'warning'; break;
                                case 'Mail': echo 'danger'; break;
                                default: echo 'secondary';
                            }
                        ?>"><?= htmlspecialchars($category) ?></span>
                        Links <span class="badge bg-dark"><?= count($categoryPosts) ?> links</span>
                    </h3>
                </div>
            </div>
            <div class="row">
                <?php foreach ($categoryPosts as $post): ?>
                    <div class="col-md-6 mb-4">
                        <div class="card h-100 bg-dark text-light border-secondary">
                            <div class="card-header d-flex justify-content-between align-items-center">
                                <span class="badge bg-<?php 
                                    switch($post['category']) {
                                        case 'Chat': echo 'success'; break;
                                        case 'Forum': echo 'info'; break;
                                        case 'Search': echo 'warning'; break;
                                        case 'Mail': echo 'danger'; break;
                                        default: echo 'secondary';
                                    }
                                ?>"><?= htmlspecialchars($post['category']) ?></span>
                                <small class=" text-light text-light"><?= htmlspecialchars(date('M j, Y', strtotime($post['created_at']))) ?></small>
                            </div>
                            <div class="card-body">
                                <h5 class="card-title">
                                    <a href="<?= htmlspecialchars($post['link']) ?>" target="_blank" class="text-info text-decoration-none">
                                        <?= htmlspecialchars($post['title']) ?>
                                        <i class="bi bi-box-arrow-up-right ms-1 small"></i>
                                    </a>
                                </h5>
                                <p class="card-text"><?= nl2br(htmlspecialchars($post['description'])) ?></p>
                            </div>
                            <div class="card-footer text-light">
                                <small>Shared by: <?= htmlspecialchars($post['username']) ?></small>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endforeach; ?>
        
        <!-- Pagination -->
        <?php if ($total_pages > 1): ?>
            <nav aria-label="Page navigation" class="mt-4">
                <ul class="pagination justify-content-center">
                    <?php if ($page > 1): ?>
                        <li class="page-item">
                            <a class="page-link bg-dark text-light border-secondary" 
                               href="?page=<?= $page-1 ?>">
                                Previous
                            </a>
                        </li>
                    <?php else: ?>
                        <li class="page-item disabled">
                            <span class="page-link bg-dark text-secondary border-secondary">Previous</span>
                        </li>
                    <?php endif; ?>
                    
                    <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                        <li class="page-item <?= $i === $page ? 'active' : '' ?>">
                            <a class="page-link <?= $i === $page ? 'bg-primary' : 'bg-dark' ?> text-light border-secondary" 
                               href="?page=<?= $i ?>">
                                <?= $i ?>
                            </a>
                        </li>
                    <?php endfor; ?>
                    
                    <?php if ($page < $total_pages): ?>
                        <li class="page-item">
                            <a class="page-link bg-dark text-light border-secondary" 
                               href="?page=<?= $page+1 ?>">
                                Next
                            </a>
                        </li>
                    <?php else: ?>
                        <li class="page-item disabled">
                            <span class="page-link bg-dark text-secondary border-secondary">Next</span>
                        </li>
                    <?php endif; ?>
                </ul>
            </nav>
        <?php endif; ?>
    <?php endif; ?>
</div>
</body>
<?php 
include 'template/footer.php';
?>