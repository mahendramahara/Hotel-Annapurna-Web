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
                    <th>Created Date</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>1</td>
                    <td><img src="/api/placeholder/50/50" alt="Blog preview"></td>
                    <td>New Summer Menu Launch</td>
                    <td>Restaurant Updates</td>
                    <td>John Doe</td>
                    <td>2024-02-07</td>
                    <td><span class="blog-status status-public">Public</span></td>
                    <td>
                        <div class="blogs-actions">
                            <button class="action-btn edit" onclick="editBlog(1)">
                                <ion-icon name="create-outline"></ion-icon>
                            </button>
                            <button class="action-btn delete" onclick="deleteBlog(1)">
                                <ion-icon name="trash-outline"></ion-icon>
                            </button>
                        </div>
                    </td>
                </tr>
                <!-- More rows would be dynamically added here -->
            </tbody>
        </table>
    </div>

    <button class="add-blog-btn" onclick="location.href='blogs/blog.php?mode=add'">
        <ion-icon name="add-outline"></ion-icon>
        Add New Blog
    </button>
</div>