<?php 
require_once('includes/header.php');
require_once('config/db.php');

$limit = 8;
$page = isset($_GET['page']) ? $_GET['page'] : 1;
$total_limit = $limit * $page;

$stmt_blogs = $conn->prepare("SELECT b.*, 
    COALESCE(b.views, 0) as views,
    (SELECT COUNT(*) FROM blog_interactions WHERE blog_id = b.id AND interaction_type = 'like') as likes_count,
    (SELECT COUNT(*) FROM blog_interactions WHERE blog_id = b.id AND interaction_type = 'comment') as comments_count,
    (SELECT AVG(rating) FROM blog_interactions WHERE blog_id = b.id AND rating IS NOT NULL) as avg_rating
    FROM blogs b 
    WHERE b.status = 'published'
    ORDER BY b.created_at DESC LIMIT ?");
$stmt_blogs->bind_param("i", $total_limit);
$stmt_blogs->execute();
$blogs_result = $stmt_blogs->get_result();

$blogs_count_result = $conn->query("SELECT COUNT(*) as total FROM blogs WHERE status = 'published'");

$total_blogs = mysqli_fetch_assoc($blogs_count_result)['total'];

$blogs = [];
while($row = mysqli_fetch_assoc($blogs_result)) {
    $blogs[] = $row;
}
?>
<main class="blogs-container">
        <header class="blogs-hero">
            <div class="blogs-hero-overlay"></div>
            <div class="blogs-hero-content">
                <h1>Our Culinary Journey</h1>
                <p>Discover the latest stories, recipes, and insights from our kitchen to yours.</p>
            </div>
        </header>

        <section class="blogs-grid">
            <?php foreach($blogs as $index => $blog): 
                $tags = explode(',', $blog['tags']);
                $featured_image = !empty($blog['featured_image']) ? htmlspecialchars($blog['featured_image']) : 'assets/images/placeholder.jpg';
                $excerpt = substr(strip_tags($blog['content']), 0, 150) . '...';
            ?>
            <article class="blogs-card">
                <div class="blogs-card-image-wrapper">
                    <img src="<?php echo $featured_image; ?>" alt="<?php echo htmlspecialchars($blog['title']); ?>" class="blogs-image">
                    <div class="blogs-card-overlay"></div>
                </div>
                <header class="blogs-card-header">
                    <span class="blogs-category"><?php echo htmlspecialchars($blog['category']); ?></span>
                    <span class="blogs-date"><?php echo date('M d, Y', strtotime($blog['created_at'])); ?></span>
                </header>
                <div class="blogs-content">
                    <h2 class="blogs-title"><?php echo htmlspecialchars($blog['title']); ?></h2>
                    <p class="blogs-description"><?php echo $excerpt; ?></p>
                    <div class="blogs-tags">
                        <?php foreach(array_slice($tags, 0, 2) as $tag): ?>
                        <span class="blogs-tag"><?php echo htmlspecialchars(trim($tag)); ?></span>
                        <?php endforeach; ?>
                    </div>
                    <div class="blogs-stats">
                        <div class="blogs-stat-item">
                            <ion-icon name="eye"></ion-icon>
                            <span><?php echo number_format($blog['views']); ?></span>
                        </div>
                        <div class="blogs-stat-item">
                            <ion-icon name="heart"></ion-icon>
                            <span><?php echo number_format($blog['likes_count']); ?></span>
                        </div>
                        <div class="blogs-stat-item">
                            <ion-icon name="chatbubble"></ion-icon>
                            <span><?php echo number_format($blog['comments_count']); ?></span>
                        </div>
                        <?php if($blog['avg_rating'] > 0): ?>
                        <div class="blogs-stat-item">
                            <ion-icon name="star"></ion-icon>
                            <span><?php echo number_format($blog['avg_rating'], 1); ?></span>
                        </div>
                        <?php endif; ?>
                    </div>
                    <footer class="blogs-card-footer">
                        <a href="blog-read.php?id=<?php echo $blog['id']; ?>" class="blogs-read-more">Read More</a>
                    </footer>
                </div>
            </article>
            <?php endforeach; ?>
        </section>

        <div class="blogs-pagination">
            <?php if($total_blogs > $page * $limit): ?>
            <a href="?page=<?php echo $page + 1; ?>" class="blogs-see-more-btn">See More Blogs</a>
            <?php endif; ?>
            <?php if($page > 1): ?>
            <a href="?page=<?php echo $page - 1; ?>" class="blogs-see-less-btn">See Less</a>
            <?php endif; ?>
        </div>
    </main>
<?php require_once('includes/footer.php'); ?>