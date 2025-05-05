<?php
// Include authentication check
require_once 'auth.php';

// Include database configuration
require_once 'config.php';

// Get all blog posts
try {
    $sql = "SELECT * FROM blog_posts ORDER BY published_date DESC";
    $stmt = $pdo->prepare($sql);
    $stmt->execute();
    $posts = $stmt->fetchAll();
} catch(PDOException $e) {
    die("ERROR: Could not execute query. " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Posts - Blog Backend</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { padding: 20px; }
        .container { max-width: 1000px; }
        .post-image { max-width: 150px; max-height: 100px; object-fit: cover; }
    </style>
</head>
<body>
    <div class="container">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1>Manage Blog Posts</h1>
            <div>
                <a href="index.php" class="btn btn-primary me-2">Add New Post</a>
                <a href="logout.php" class="btn btn-outline-danger">Logout</a>
            </div>
        </div>
        
        <?php if(isset($_GET['status_changed'])): ?>
        <div class="alert alert-success">
            Post status updated successfully!
        </div>
        <?php endif; ?>
        
        <?php if(isset($_GET['deleted'])): ?>
        <div class="alert alert-success">
            Post deleted successfully!
        </div>
        <?php endif; ?>
        
        <?php if(isset($_GET['error'])): ?>
        <div class="alert alert-danger">
            Error: <?= htmlspecialchars($_GET['error']) ?>
        </div>
        <?php endif; ?>
        
        <?php if(count($posts) > 0): ?>
        <div class="table-responsive">
            <table class="table table-striped table-bordered">
                <thead class="table-dark">
                    <tr>
                        <th>Title</th>
                        <th>Image</th>
                        <th>Excerpt</th>
                        <th>Published Date</th>
                        <th>Tags</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach($posts as $post): ?>
                    <tr>
                        <td>
                            <strong><?= htmlspecialchars($post['title']) ?></strong>
                            <div class="text-muted small">Slug: <?= htmlspecialchars($post['slug']) ?></div>
                        </td>
                        <td>
                            <img src="<?= htmlspecialchars($post['featured_image']) ?>" alt="<?= htmlspecialchars($post['title']) ?>" class="post-image">
                        </td>
                        <td><?= htmlspecialchars($post['excerpt']) ?></td>
                        <td><?= date('Y-m-d', strtotime($post['published_date'])) ?></td>
                        <td>
                            <?php 
                            $tags = json_decode($post['tags'], true);
                            if(is_array($tags)) {
                                foreach($tags as $tag) {
                                    echo '<span class="badge bg-secondary me-1">' . htmlspecialchars($tag) . '</span>';
                                }
                            }
                            ?>
                        </td>
                        <td>
                            <div class="d-flex flex-column align-items-center">
                                <?php if($post['published']): ?>
                                    <span class="badge bg-success mb-2">Published</span>
                                <?php else: ?>
                                    <span class="badge bg-warning text-dark mb-2">Draft</span>
                                <?php endif; ?>
                                
                                <a href="toggle_status.php?slug=<?= urlencode($post['slug']) ?>" 
                                   class="btn btn-sm <?= $post['published'] ? 'btn-outline-warning' : 'btn-outline-success' ?>">
                                    <?= $post['published'] ? 'Set to Draft' : 'Publish' ?>
                                </a>
                            </div>
                        </td>
                        <td class="text-center">
                            <a href="delete_post.php?slug=<?= urlencode($post['slug']) ?>" 
                               class="btn btn-sm btn-danger"
                               onclick="return confirm('Are you sure you want to delete this post?');">
                                Delete
                            </a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <?php else: ?>
        <div class="alert alert-info">
            No blog posts found. <a href="index.php">Create your first post</a>.
        </div>
        <?php endif; ?>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html> 