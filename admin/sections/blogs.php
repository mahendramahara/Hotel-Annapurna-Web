<?php
require_once('../config/db.php');

$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$limit = 10;
$offset = ($page - 1) * $limit;

$count_stmt = $conn->prepare("SELECT COUNT(*) as total FROM blogs");
$count_stmt->execute();
$total_records = $count_stmt->get_result()->fetch_assoc()['total'];
$total_pages = ceil($total_records / $limit);

$stmt = $conn->prepare("SELECT b.*, CONCAT(u.first_name, ' ', u.last_name) as author_name FROM blogs b LEFT JOIN users u ON b.author_id = u.id ORDER BY b.created_at DESC LIMIT ? OFFSET ?");
$stmt->bind_param("ii", $limit, $offset);
$stmt->execute();
$blogs = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
?>

<div class="blogs-data-table">
    <div class="title">
        <ion-icon name="newspaper-outline" class="title-icon"></ion-icon>
        <span class="text">Blog Management</span>
    </div>

    <div class="blogs-container">
        <table class="blogs-table">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Preview</th>
                    <th>Title</th>
                    <th>Category</th>
                    <th>Author</th>
                    <th>Status</th>
                    <th>Created Date</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php 
                $sn = $offset + 1;
                foreach($blogs as $blog): 
                ?>
                <tr>
                    <td><?= $sn++ ?></td>
                    <td>
                        <?php if (!empty($blog['featured_image'])): ?>
                            <img src="../<?= htmlspecialchars($blog['featured_image']) ?>" 
                                 alt="Blog preview" 
                                 style="width:50px;height:50px;object-fit:cover;border-radius:4px;background:#f0f0f0;" 
                                 loading="lazy"
                                 onerror="this.onerror=null; this.style.display='none'; this.nextElementSibling.style.display='inline-block';">
                            <span style="display:none;width:50px;height:50px;background:#e0e0e0;border-radius:4px;text-align:center;line-height:50px;color:#999;font-size:20px;">📷</span>
                        <?php else: ?>
                            <span style="display:inline-block;width:50px;height:50px;background:#e0e0e0;border-radius:4px;text-align:center;line-height:50px;color:#999;font-size:20px;">📄</span>
                        <?php endif; ?>
                    </td>
                    <td style="max-width:300px;"><?= htmlspecialchars($blog['title']) ?></td>
                    <td><span class="blog-category"><?= htmlspecialchars($blog['category']) ?></span></td>
                    <td><?= htmlspecialchars($blog['author_name'] ?? 'Unknown') ?></td>
                    <td><span class="blog-status <?= strtolower($blog['status']) ?>"><?= htmlspecialchars($blog['status']) ?></span></td>
                    <td><?= date('Y-m-d', strtotime($blog['created_at'])) ?></td>
                    <td>
                        <div class="blogs-actions">
                            <button class="action-btn view" onclick="viewBlog(<?= $blog['id'] ?>)" title="View Blog">
                                <ion-icon name="eye-outline"></ion-icon>
                            </button>
                            <button class="action-btn edit" onclick="editBlog(<?= $blog['id'] ?>)" title="Edit Blog">
                                <ion-icon name="create-outline"></ion-icon>
                            </button>
                            <button class="action-btn delete" onclick="deleteBlog(<?= $blog['id'] ?>)" title="Delete Blog">
                                <ion-icon name="trash-outline"></ion-icon>
                            </button>
                        </div>
                    </td>
                </tr>
                <?php endforeach; ?>
                <?php if(empty($blogs)): ?>
                <tr>
                    <td colspan="8" style="text-align:center;padding:20px;">No blogs found</td>
                </tr>
                <?php endif; ?>
            </tbody>
        </table>
        
        <?php if($total_pages > 1): ?>
        <div class="pagination">
            <?php if($page > 1): ?>
                <a href="?page=blogs&p=<?= $page-1 ?>" class="page-btn">Previous</a>
            <?php endif; ?>
            
            <?php for($i = 1; $i <= $total_pages; $i++): ?>
                <a href="?page=blogs&p=<?= $i ?>" class="page-btn <?= $i == $page ? 'active' : '' ?>"><?= $i ?></a>
            <?php endfor; ?>
            
            <?php if($page < $total_pages): ?>
                <a href="?page=blogs&p=<?= $page+1 ?>" class="page-btn">Next</a>
            <?php endif; ?>
        </div>
        <?php endif; ?>
    </div>

    <button class="add-blog-btn" onclick="location.href='blogs/blog.php?mode=add'">
        <ion-icon name="add-outline"></ion-icon>
        Add New Blog
    </button>
</div>

<script>
function viewBlog(id) {
    window.open('../blog-read-new.php?id=' + id, '_blank');
}

function editBlog(id) {
    window.location.href = 'blogs/blog.php?mode=edit&id=' + id;
}

function deleteBlog(id) {
    if(!confirm('Are you sure you want to delete this blog? This action cannot be undone.')) return;
    
    fetch('../api/admin-blogs.php?action=delete&id=' + id, {
        method: 'POST'
    })
    .then(res => res.json())
    .then(data => {
        if(data.success) {
            alert('Blog deleted successfully!');
            location.reload();
        } else {
            alert('Error: ' + data.message);
        }
    })
    .catch(err => {
        alert('Failed to delete blog');
    });
}
</script>