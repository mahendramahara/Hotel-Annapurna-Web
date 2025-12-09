<?php
require_once('includes/auth-guard.php');
require_once('../config/db.php');

$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$limit = 10;
$offset = ($page - 1) * $limit;

$count_stmt = $conn->prepare("SELECT COUNT(*) as total FROM blog_interactions WHERE interaction_type = 'comment'");
$count_stmt->execute();
$total_records = $count_stmt->get_result()->fetch_assoc()['total'];
$total_pages = ceil($total_records / $limit);

$stmt = $conn->prepare("SELECT bi.*, CONCAT(u.first_name, ' ', u.last_name) as customer_name, u.profile_pic, b.title as blog_title FROM blog_interactions bi LEFT JOIN users u ON bi.user_id = u.id LEFT JOIN blogs b ON bi.blog_id = b.id WHERE bi.interaction_type = 'comment' ORDER BY bi.created_at DESC LIMIT ? OFFSET ?");
$stmt->bind_param("ii", $limit, $offset);
$stmt->execute();
$reviews = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
?>

<div class="reviews-container">
        <div class="reviews-title">
            <ion-icon name="chatbubbles-outline"></ion-icon>
            <span>Customer Reviews</span>
        </div>

        <div class="reviews-table-wrapper">
            <table class="reviews-table">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Customer</th>
                        <th>Name</th>
                        <th>Blog/Service</th>
                        <th>Date</th>
                        <th>Rating</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody id="reviewsTableBody">
                    <?php 
                    $sn = $offset + 1;
                    foreach($reviews as $review): 
                    ?>
                    <tr>
                        <td><?= $sn++ ?></td>
                        <td><img src="../<?= htmlspecialchars($review['profile_pic'] ?? 'assets/images/default-avatar.png') ?>" alt="Customer" style="width:40px;height:40px;border-radius:50%;object-fit:cover;"></td>
                        <td><?= htmlspecialchars($review['customer_name'] ?? 'Guest') ?></td>
                        <td><?= htmlspecialchars($review['blog_title'] ?? 'N/A') ?></td>
                        <td><?= date('Y-m-d', strtotime($review['created_at'])) ?></td>
                        <td>
                            <?php 
                            $rating = $review['rating'] ?? 0;
                            for($i = 0; $i < 5; $i++) {
                                echo $i < $rating ? '⭐' : '☆';
                            }
                            ?>
                        </td>
                        <td>
                            <div class="action-buttons">
                                <button class="btn-action view" onclick="viewReview(<?= $review['id'] ?>)" title="View Details">
                                    <ion-icon name="eye-outline"></ion-icon>
                                </button>
                                <button class="btn-action delete" onclick="deleteReview(<?= $review['id'] ?>)" title="Delete">
                                    <ion-icon name="trash-outline"></ion-icon>
                                </button>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                    <?php if(empty($reviews)): ?>
                    <tr>
                        <td colspan="7" style="text-align:center;">No reviews found</td>
                    </tr>
                    <?php endif; ?>
                </tbody>
            </table>
            
            <?php if($total_pages > 1): ?>
            <div class="pagination">
                <?php if($page > 1): ?>
                    <a href="?page=reviews&p=<?= $page-1 ?>" class="page-btn">Previous</a>
                <?php endif; ?>
                
                <?php for($i = 1; $i <= $total_pages; $i++): ?>
                    <a href="?page=reviews&p=<?= $i ?>" class="page-btn <?= $i == $page ? 'active' : '' ?>"><?= $i ?></a>
                <?php endfor; ?>
                
                <?php if($page < $total_pages): ?>
                    <a href="?page=reviews&p=<?= $page+1 ?>" class="page-btn">Next</a>
                <?php endif; ?>
            </div>
            <?php endif; ?>
        </div>
    </div>

    <div id="viewModal" class="reviews-modal">
        <div class="reviews-modal-content">
            <span class="reviews-close-btn" onclick="closeModal('viewModal')">&times;</span>
            <div class="reviews-modal-header">
                <img id="modalCustomerImage" src="" alt="Customer" style="width:60px;height:60px;border-radius:50%;object-fit:cover;">
                <div>
                    <h3 id="modalCustomerName"></h3>
                    <p id="modalServiceInfo"></p>
                </div>
            </div>
            <div class="reviews-rating" id="modalRating"></div>
            <p id="modalReviewText"></p>
            <div class="reviews-modal-actions">
                <button class="reviews-btn reviews-btn-secondary" onclick="closeModal('viewModal')">Close</button>
            </div>
        </div>
    </div>

<script>
function viewReview(id) {
    fetch(`../api/blog-interactions.php?action=get&id=${id}`)
        .then(res => res.json())
        .then(data => {
            console.log('Review data:', data);
            if(data.success) {
                const review = data.interaction;
                document.getElementById('modalCustomerImage').src = '../' + (review.profile_pic || 'assets/images/default-avatar.png');
                document.getElementById('modalCustomerName').textContent = review.customer_name || 'Guest';
                document.getElementById('modalServiceInfo').textContent = review.blog_title || 'N/A';
                
                let ratingHTML = '';
                for(let i = 0; i < 5; i++) {
                    ratingHTML += i < (review.rating || 0) ? '⭐' : '☆';
                }
                document.getElementById('modalRating').innerHTML = ratingHTML;
                document.getElementById('modalReviewText').textContent = review.comment_text || 'No comment';
                
                const modal = document.getElementById('viewModal');
                console.log('Modal element:', modal);
                if(modal) {
                    modal.style.display = 'flex';
                }
            } else {
                console.error('API error:', data.message);
                alert('Error loading review: ' + (data.message || 'Unknown error'));
            }
        })
        .catch(err => {
            console.error('Fetch error:', err);
            alert('Failed to load review details');
        });
}

function closeModal(modalId) {
    document.getElementById(modalId).style.display = 'none';
}

function deleteReview(id) {
    if(!confirm('Are you sure you want to delete this review?')) return;
    
    const formData = new FormData();
    formData.append('action', 'delete');
    formData.append('id', id);
    
    fetch('../api/blog-interactions.php', {
        method: 'POST',
        body: formData
    })
    .then(res => res.json())
    .then(data => {
        if(data.success) {
            alert(data.message);
            location.reload();
        } else {
            alert('Error: ' + data.message);
        }
    });
}

window.onclick = function(event) {
    if (event.target.classList.contains('reviews-modal')) {
        event.target.style.display = 'none';
    }
}
</script>