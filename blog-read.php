<?php 
require_once('includes/header.php');
require_once('config/db.php');

$blog_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($blog_id == 0) {
    header('Location: blogs.php');
    exit;
}

$stmt_blog = $conn->prepare("SELECT * FROM blogs WHERE id = ?");
$stmt_blog->bind_param("i", $blog_id);
$stmt_blog->execute();
$blog_result = $stmt_blog->get_result();

if ($blog_result->num_rows == 0) {
    header('Location: blogs.php');
    exit;
}

$blog = $blog_result->fetch_assoc();

// Get author information
$author = null;
if($blog['author_id']) {
    $stmt_author = $conn->prepare("SELECT id, CONCAT(first_name, ' ', last_name) as name, email FROM users WHERE id = ?");
    $stmt_author->bind_param("i", $blog['author_id']);
    $stmt_author->execute();
    $author_result = $stmt_author->get_result();
    if($author_result->num_rows > 0) {
        $author = $author_result->fetch_assoc();
    }
}

$stmt_related = $conn->prepare("SELECT * FROM blogs WHERE id != ? AND category = ? ORDER BY created_at DESC LIMIT 4");
$stmt_related->bind_param("is", $blog_id, $blog['category']);
$stmt_related->execute();
$related_result = $stmt_related->get_result();
$related_blogs = [];
while($row = $related_result->fetch_assoc()) {
    $related_blogs[] = $row;
}

$stmt_likes = $conn->prepare("SELECT COUNT(*) as count FROM blog_interactions WHERE blog_id = ? AND interaction_type = 'like'");
$stmt_likes->bind_param("i", $blog_id);
$stmt_likes->execute();
$likes_count = $stmt_likes->get_result()->fetch_assoc()['count'];

$stmt_comments = $conn->prepare("SELECT COUNT(*) as count FROM blog_interactions WHERE blog_id = ? AND interaction_type = 'comment'");
$stmt_comments->bind_param("i", $blog_id);
$stmt_comments->execute();
$comments_count = $stmt_comments->get_result()->fetch_assoc()['count'];

$stmt_shares = $conn->prepare("SELECT COUNT(*) as count FROM blog_interactions WHERE blog_id = ? AND interaction_type = 'share'");
$stmt_shares->bind_param("i", $blog_id);
$stmt_shares->execute();
$shares_count = $stmt_shares->get_result()->fetch_assoc()['count'];

$user_id = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : null;
$user_has_liked = false;
$user_has_rated = false;
$user_rating = null;
$user_comment_id = null;
$user_comment_text = '';

if($user_id) {
    $stmt_user_like = $conn->prepare("SELECT id FROM blog_interactions WHERE blog_id = ? AND user_id = ? AND interaction_type = 'like'");
    $stmt_user_like->bind_param("ii", $blog_id, $user_id);
    $stmt_user_like->execute();
    $user_has_liked = $stmt_user_like->get_result()->num_rows > 0;
    
    // Check if user has already rated/commented
    $stmt_user_comment = $conn->prepare("SELECT id, comment_text, rating FROM blog_interactions WHERE blog_id = ? AND user_id = ? AND interaction_type = 'comment'");
    $stmt_user_comment->bind_param("ii", $blog_id, $user_id);
    $stmt_user_comment->execute();
    $user_comment_result = $stmt_user_comment->get_result();
    if($user_comment_result->num_rows > 0) {
        $user_comment_data = $user_comment_result->fetch_assoc();
        $user_has_rated = true;
        $user_rating = $user_comment_data['rating'];
        $user_comment_id = $user_comment_data['id'];
        $user_comment_text = $user_comment_data['comment_text'];
    }
}

$interactions_query = "SELECT bi.*, CONCAT(u.first_name, ' ', u.last_name) as user_name, u.email FROM blog_interactions bi 
                      LEFT JOIN users u ON bi.user_id = u.id 
                      WHERE bi.blog_id = $blog_id AND bi.interaction_type = 'comment'
                      ORDER BY bi.created_at DESC";
$interactions_result = mysqli_query($conn, $interactions_query);
$interactions = [];
while($row = mysqli_fetch_assoc($interactions_result)) {
    $interactions[] = $row;
}

$stmt_update_views = $conn->prepare("UPDATE blogs SET views = COALESCE(views, 0) + 1 WHERE id = ?");
$stmt_update_views->bind_param("i", $blog_id);
$stmt_update_views->execute();
?>

<link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
<link rel="stylesheet" href="assets/css/blog-read.css">

<div class="blog-hero" style="background-image:linear-gradient(rgba(0,0,0,0.4), rgba(0,0,0,0.6)), url('<?php echo htmlspecialchars($blog['featured_image'] ?? ''); ?>');background-size:cover;background-position:center">
    <div class="blog-hero-overlay">
        <div class="blog-hero-content">
            <span class="blog-category">📂 <?php echo htmlspecialchars($blog['category']); ?></span>
            <h1 class="blog-hero-title"><?php echo htmlspecialchars($blog['title']); ?></h1>
            <div class="blog-stats">
                <span class="blog-stat">📅 <?php echo date('M d, Y', strtotime($blog['created_at'])); ?></span>
                <span class="blog-stat">👁️ <?php echo $blog['views'] ?? 0; ?> views</span>
                <span class="blog-stat">❤️ <?php echo $likes_count; ?> likes</span>
                <span class="blog-stat">💬 <?php echo $comments_count; ?> comments</span>
            </div>
        </div>
    </div>
</div>

<div class="blog-wrap">
    <article class="blog-main">
        <div class="blog-body">
            <?php 
            $content = $blog['content'];
            $content = preg_replace('/<h2>(.*?)<\/h2>/', '<h2>$1</h2>', $content);
            $content = preg_replace('/<h3>(.*?)<\/h3>/', '<h3>$1</h3>', $content);
            $content = preg_replace('/<p>(.*?)<\/p>/', '<p>$1</p>', $content);
            $content = preg_replace('/(?<!>)\n(?!<)/', '<br>', $content);
            echo $content;
            ?>

            <?php if($author): ?>
            <div class="author-card">
                <div class="author-img"><?php echo strtoupper(substr($author['name'], 0, 1)); ?></div>
                <div class="author-info">
                    <h4><?php echo htmlspecialchars($author['name']); ?></h4>
                    <p>✍️ Author • <?php echo htmlspecialchars($author['email']); ?></p>
                </div>
            </div>
            <?php endif; ?>

            <?php if($blog['tags']): ?>
            <div class="blog-tags-wrap">
                <?php 
                $tags = array_map('trim', explode(',', $blog['tags']));
                foreach($tags as $tag): ?>
                    <span class="tag-item">#<?php echo htmlspecialchars($tag); ?></span>
                <?php endforeach; ?>
            </div>
            <?php endif; ?>

            <div class="engagement-bar">
                <button class="engage-btn <?php echo $user_has_liked ? 'liked' : ''; ?>" onclick="likePost(<?php echo $blog_id; ?>)">
                    <span><?php echo $user_has_liked ? '❤️' : '🤍'; ?></span>
                    <span><?php echo $user_has_liked ? 'Liked' : 'Like'; ?> (<?php echo $likes_count; ?>)</span>
                </button>
                <button class="engage-btn" onclick="sharePost()">
                    <span>🔗</span>
                    <span>Share</span>
                </button>
            </div>
        </div>

        <div class="comments-area">
            <h2 class="comments-header">💬 Comments (<?php echo $comments_count; ?>)</h2>
            
            <div class="new-comment">
                <textarea id="commentText" class="comment-textarea" placeholder="<?php echo $user_has_rated ? 'Edit your comment...' : 'Share your thoughts...'; ?>"><?php echo $user_has_rated ? htmlspecialchars($user_comment_text) : ''; ?></textarea>
                
                <?php if(!$user_has_rated): ?>
                <div class="rating-stars">
                    <span class="rating-star" data-rating="1">★</span>
                    <span class="rating-star" data-rating="2">★</span>
                    <span class="rating-star" data-rating="3">★</span>
                    <span class="rating-star" data-rating="4">★</span>
                    <span class="rating-star" data-rating="5">★</span>
                </div>
                <button class="submit-comment-btn" onclick="postComment(<?php echo $blog_id; ?>)">Post Comment & Rate</button>
                <?php else: ?>
                <div style="text-align:center;padding:15px;background:#f0f4ff;border-radius:8px;margin:15px 0">
                    <p style="color:#667eea;font-weight:600;margin-bottom:8px">Your Rating: <?php echo str_repeat('★', $user_rating ?? 0); ?></p>
                    <small style="color:#718096">You can only rate once, but you can edit your comment below</small>
                </div>
                <input type="hidden" id="commentId" value="<?php echo $user_comment_id; ?>">
                <button class="submit-comment-btn" onclick="updateComment(<?php echo $blog_id; ?>, <?php echo $user_comment_id; ?>)">Update Comment</button>
                <?php endif; ?>
            </div>

            <?php foreach($interactions as $interaction): ?>
            <div class="comment-item">
                <div class="commenter-avatar"><?php echo strtoupper(substr($interaction['user_name'] ?? 'A', 0, 1)); ?></div>
                <div class="comment-details">
                    <div class="commenter-name"><?php echo htmlspecialchars($interaction['user_name'] ?? 'Anonymous'); ?></div>
                    <div class="comment-time"><?php echo date('M d, Y \a\t h:i A', strtotime($interaction['created_at'])); ?></div>
                    <?php if(isset($interaction['rating']) && $interaction['rating']): ?>
                    <div class="comment-rating">
                        <?php echo str_repeat('★', intval($interaction['rating'])); ?>
                    </div>
                    <?php endif; ?>
                    <div class="comment-message"><?php echo htmlspecialchars($interaction['comment_text']); ?></div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </article>

    <?php if(!empty($related_blogs)): ?>
    <section class="related-section">
        <h2 class="related-title">📖 Related Articles</h2>
        <div class="related-grid">
            <?php foreach($related_blogs as $related): ?>
            <div class="related-card" onclick="window.location.href='blog-read.php?id=<?php echo $related['id']; ?>'">
                <div class="related-card-title"><?php echo htmlspecialchars($related['title']); ?></div>
                <div class="related-card-date"><?php echo date('M d, Y', strtotime($related['created_at'])); ?></div>
            </div>
            <?php endforeach; ?>
        </div>
    </section>
    <?php endif; ?>
</div>

<script>
const blogId = <?php echo $blog_id; ?>;
const isLoggedIn = <?php echo $user_id ? 'true' : 'false'; ?>;
let selectedRating = 0;

document.addEventListener('DOMContentLoaded', function() {
    const stars = document.querySelectorAll('.rating-star');
    
    stars.forEach((star, index) => {
        star.addEventListener('click', function() {
            selectedRating = parseInt(this.getAttribute('data-rating'));
            updateStars();
        });
        
        star.addEventListener('mouseenter', function() {
            const rating = parseInt(this.getAttribute('data-rating'));
            stars.forEach((s, i) => {
                s.classList.toggle('filled', i < rating);
            });
        });
        
        star.addEventListener('mouseleave', function() {
            updateStars();
        });
    });
    
    function updateStars() {
        stars.forEach((s, i) => {
            s.classList.toggle('filled', i < selectedRating);
        });
    }
});

function likePost(blogId) {
    if(!isLoggedIn) {
        alert('Please login to like this post');
        window.location.href = 'login.php';
        return;
    }

    fetch('api/blog-interactions.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify({
            blog_id: blogId,
            interaction_type: 'like'
        })
    })
    .then(response => response.json())
    .then(data => {
        if(data.success) {
            location.reload();
        } else {
            alert('Error: ' + (data.message || 'Failed to like post'));
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Error liking post');
    });
}

function sharePost() {
    const title = document.querySelector('h1').textContent;
    const url = window.location.href;
    
    if(navigator.share) {
        navigator.share({
            title: title,
            text: 'Check out this blog post!',
            url: url
        }).then(() => {
            recordShare(blogId);
        }).catch(error => console.log('Error sharing:', error));
    } else {
        navigator.clipboard.writeText(url).then(() => {
            alert('Link copied to clipboard!');
            recordShare(blogId);
        });
    }
}

function recordShare(blogId) {
    fetch('api/blog-interactions.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify({
            blog_id: blogId,
            interaction_type: 'share'
        })
    })
    .then(response => response.json())
    .then(data => {
        if(data.success) {
            const shareCount = document.querySelector('[data-shares-count]');
            if(shareCount) {
                shareCount.textContent = parseInt(shareCount.textContent) + 1;
            }
        }
    });
}

function postComment(blogId) {
    if(!isLoggedIn) {
        alert('Please login to comment');
        window.location.href = 'login.php';
        return;
    }

    const commentText = document.getElementById('commentText')?.value || '';

    if(!commentText.trim()) {
        alert('Please write a comment');
        return;
    }

    const requestData = {
        blog_id: blogId,
        interaction_type: 'comment',
        comment_text: commentText
    };

    if(selectedRating > 0) {
        requestData.rating = selectedRating;
    }

    fetch('api/blog-interactions.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify(requestData)
    })
    .then(response => response.json())
    .then(data => {
        if(data.success) {
            document.getElementById('commentText').value = '';
            selectedRating = 0;
            document.querySelectorAll('.rating-star').forEach(s => s.classList.remove('filled'));
            setTimeout(() => location.reload(), 500);
        } else {
            alert('Error: ' + (data.message || 'Failed to post comment'));
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Error posting comment');
    });
}

function updateComment(blogId, commentId) {
    if(!isLoggedIn) {
        alert('Please login to update comment');
        window.location.href = 'login.php';
        return;
    }

    const commentText = document.getElementById('commentText')?.value || '';

    if(!commentText.trim()) {
        alert('Please write a comment');
        return;
    }

    fetch('api/blog-interactions.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify({
            blog_id: blogId,
            comment_id: commentId,
            interaction_type: 'update_comment',
            comment_text: commentText
        })
    })
    .then(response => response.json())
    .then(data => {
        if(data.success) {
            alert('Comment updated successfully!');
            setTimeout(() => location.reload(), 500);
        } else {
            alert('Error: ' + (data.message || 'Failed to update comment'));
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Error updating comment');
    });
}
</script>

<?php
include('includes/footer.php');
?>
</body>
</html>
