<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Blog Post</title>
    <link rel="stylesheet" href="styles.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Include TinyMCE -->
    <script src="https://cdn.tiny.cloud/1/io2ux4qga6tpuojnkp3vtao9pq1tosjfljurcm66o4izme7m/tinymce/6/tinymce.min.js" referrerpolicy="origin"></script>
</head>
<body>
    <div class="container">
        <header>
            <h1><i class="fas fa-edit"></i> Edit Blog Post</h1>
            <div class="post-meta">
                <span><i class="far fa-clock"></i> Last edited: <time id="lastEdited">2025-02-07 11:55:13</time></span>
                <span><i class="far fa-user"></i> Author: <span id="authorName">mahendramahara</span></span>
            </div>
        </header>

        <form id="blogEditForm" class="blog-form">
            <div class="form-group">
                <label for="blogTitle">
                    <i class="fas fa-heading"></i> Title
                </label>
                <input type="text" id="blogTitle" required placeholder="Enter blog title">
            </div>

            <div class="form-group">
                <label for="blogCategory">
                    <i class="fas fa-tag"></i> Category
                </label>
                <select id="blogCategory" required>
                    <option value="">Select a category</option>
                    <option value="technology">Technology</option>
                    <option value="lifestyle">Lifestyle</option>
                    <option value="travel">Travel</option>
                    <option value="food">Food</option>
                </select>
            </div>

            <div class="form-group">
                <label for="blogTags">
                    <i class="fas fa-tags"></i> Tags
                </label>
                <div class="tags-input-container">
                    <div class="tags-list" id="tagsList"></div>
                    <input type="text" id="tagInput" placeholder="Add tags (press Enter to add)">
                </div>
            </div>

            <div class="form-group">
                <label for="blogContent">
                    <i class="fas fa-paragraph"></i> Content
                </label>
                <textarea id="blogContent" class="rich-editor"></textarea>
            </div>

            <div class="form-group">
                <label>
                    <i class="fas fa-image"></i> Featured Image
                </label>
                <div class="image-upload-container">
                    <div id="imagePreview" class="image-preview">
                        <input type="file" id="blogImage" accept="image/*">
                        <label for="blogImage" class="upload-label">
                            <i class="fas fa-cloud-upload-alt"></i>
                            <span>Click to upload image</span>
                        </label>
                    </div>
                    <button type="button" id="removeImage" class="remove-image-btn" style="display: none;">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
            </div>

            <div class="error-container" id="errorContainer"></div>

            <div class="button-group">
                <button type="submit" class="btn btn-save">
                    <i class="fas fa-save"></i> Save Changes
                </button>
                <button type="button" class="btn btn-delete" id="deletePost">
                    <i class="fas fa-trash-alt"></i> Delete Post
                </button>
                <button type="button" class="btn btn-cancel" id="cancelEdit">
                    <i class="fas fa-times"></i> Cancel
                </button>
            </div>
        </form>
    </div>

    <!-- Confirmation Modal -->
    <div class="modal" id="confirmModal">
        <div class="modal-content">
            <h2><i class="fas fa-exclamation-triangle"></i> Confirm Action</h2>
            <p id="modalMessage">Are you sure you want to proceed?</p>
            <div class="modal-buttons">
                <button id="modalConfirm" class="btn btn-delete">Confirm</button>
                <button id="modalCancel" class="btn btn-cancel">Cancel</button>
            </div>
        </div>
    </div>

    <script src="script.js"></script>
</body>
</html>