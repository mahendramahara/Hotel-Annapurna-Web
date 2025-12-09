document.addEventListener('DOMContentLoaded', function() {
    let currentBlogId = null;

    // Initialize TinyMCE
    tinymce.init({
        selector: '#blogContent',
        height: 500,
        menubar: true,
        plugins: [
            'advlist', 'autolink', 'lists', 'link', 'image', 'charmap', 'preview',
            'anchor', 'searchreplace', 'visualblocks', 'code', 'fullscreen',
            'insertdatetime', 'media', 'table', 'help', 'wordcount'
        ],
        toolbar: 'undo redo | formatselect | ' +
            'bold italic underline strikethrough | alignleft aligncenter ' +
            'alignright alignjustify | bullist numlist outdent indent | ' +
            'removeformat | link image code | help',
        content_style: 'body { font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Helvetica, Arial, sans-serif; font-size: 16px; line-height: 1.6; }',
        width: '100%',
        resize: false,
        setup: function(editor) {
            editor.on('init', function() {
                console.log('TinyMCE initialized successfully');
                // If there's pending content, set it now
                if (window.pendingBlogContent) {
                    console.log('Loading pending blog content into editor');
                    editor.setContent(window.pendingBlogContent);
                    window.pendingBlogContent = null;
                }
            });
        }
    });

    // Load existing post data if editing
    function loadPostData() {
        const urlParams = new URLSearchParams(window.location.search);
        const postId = urlParams.get('id');
        const mode = urlParams.get('mode');
        
        if (mode === 'edit' && postId) {
            currentBlogId = postId;
            document.querySelector('header h1').innerHTML = '<i class="fas fa-edit"></i> Edit Blog Post';
            
            fetch(`../../api/admin-blogs.php?action=get_by_id&id=${postId}`)
                .then(res => res.json())
                .then(response => {
                    if (response.success && response.data) {
                        const post = response.data;
                        document.getElementById('blogTitle').value = post.title || '';
                        document.getElementById('blogCategory').value = post.category || '';
                        document.getElementById('blogStatus').value = post.status || 'draft';
                        
                        // Store content to be set after TinyMCE initializes
                        window.pendingBlogContent = post.content || '';
                        
                        // Wait for TinyMCE to be ready
                        const setContent = () => {
                            if (tinymce.get('blogContent')) {
                                console.log('Setting TinyMCE content:', window.pendingBlogContent.substring(0, 100) + '...');
                                tinymce.get('blogContent').setContent(window.pendingBlogContent);
                            } else {
                                console.log('TinyMCE not ready yet, waiting...');
                                setTimeout(setContent, 100);
                            }
                        };
                        setTimeout(setContent, 500);
                        
                        document.getElementById('lastEdited').textContent = post.updated_at || post.created_at;
                        document.getElementById('authorName').textContent = post.author_name || 'Unknown';
                        
                        // Load tags
                        if (post.tags) {
                            post.tags.split(',').forEach(tag => {
                                if (tag.trim()) addTag(tag.trim());
                            });
                        }

                        // Load image if exists
                        if (post.featured_image) {
                            const imagePath = '../../' + post.featured_image;
                            currentImage = imagePath;
                            displayImage(imagePath);
                        }
                    } else {
                        showError('Failed to load blog: ' + (response.message || 'Unknown error'));
                    }
                })
                .catch(error => {
                    console.error('Error loading blog:', error);
                    showError('Failed to load blog data');
                });
        } else if (mode === 'add') {
            document.querySelector('header h1').innerHTML = '<i class="fas fa-plus"></i> Add New Blog Post';
            document.getElementById('deletePost').style.display = 'none';
        }
    }

    // Tags Management
    const tagInput = document.getElementById('tagInput');
    const tagsList = document.getElementById('tagsList');
    let tags = new Set();

    tagInput.addEventListener('keydown', function(e) {
        if (e.key === 'Enter') {
            e.preventDefault();
            const tag = this.value.trim();
            if (tag && !tags.has(tag)) {
                addTag(tag);
                this.value = '';
            }
        }
    });

    function addTag(tag) {
        tags.add(tag);
        const tagElement = document.createElement('span');
        tagElement.className = 'tag';
        tagElement.innerHTML = `
            ${tag}
            <span class="remove-tag" data-tag="${tag}">×</span>
        `;
        tagsList.appendChild(tagElement);

        tagElement.querySelector('.remove-tag').addEventListener('click', function() {
            const tagToRemove = this.getAttribute('data-tag');
            tags.delete(tagToRemove);
            this.parentElement.remove();
        });
    }

    // Image Upload and Remove
    const imageInput = document.getElementById('blogImage');
    const imagePreview = document.getElementById('imagePreview');
    const removeImageBtn = document.getElementById('removeImage');
    let currentImage = null;

    imageInput.addEventListener('change', function(e) {
        const file = e.target.files[0];
        if (file && validateImage(file)) {
            const reader = new FileReader();
            reader.onload = function(e) {
                currentImage = e.target.result;
                displayImage(currentImage);
            };
            reader.readAsDataURL(file);
        }
    });

    function displayImage(imageData) {
        imagePreview.innerHTML = `
            <img src="${imageData}" alt="Blog image" onerror="this.src='../../assets/images/no-image.jpg'">
            <label for="blogImage" class="upload-label" style="margin-top: 10px;">
                <i class="fas fa-sync-alt"></i>
                <span>Change image</span>
            </label>
        `;
        removeImageBtn.style.display = 'block';
    }

    removeImageBtn.addEventListener('click', function() {
        currentImage = null;
        imageInput.value = ''; // Clear the file input
        imagePreview.innerHTML = `
            <label for="blogImage" class="upload-label">
                <i class="fas fa-cloud-upload-alt"></i>
                <span>Click to upload image</span>
            </label>
        `;
        removeImageBtn.style.display = 'none';
    });

    // Form Validation and Submission
    const form = document.getElementById('blogEditForm');
    
    form.addEventListener('submit', function(e) {
        e.preventDefault();
        if (validateForm()) {
            showModal('save', 'Are you sure you want to save these changes?');
        }
    });

    function validateForm() {
        const title = document.getElementById('blogTitle').value.trim();
        const content = tinymce.get('blogContent').getContent();
        const category = document.getElementById('blogCategory').value;

        if (!title) {
            showError('Please enter a title');
            return false;
        }

        if (!category) {
            showError('Please select a category');
            return false;
        }

        if (!content) {
            showError('Please enter some content');
            return false;
        }

        return true;
    }

    function validateImage(file) {
        const validTypes = ['image/jpeg', 'image/png', 'image/gif'];
        const maxSize = 5 * 1024 * 1024; // 5MB

        if (!validTypes.includes(file.type)) {
            showError('Please upload an image file (JPEG, PNG, or GIF)');
            return false;
        }

        if (file.size > maxSize) {
            showError('Image size should be less than 5MB');
            return false;
        }

        return true;
    }

    // Save Changes
    function saveChanges() {
        const formData = new FormData();
        const blogId = currentBlogId || new URLSearchParams(window.location.search).get('id');
        
        const title = document.getElementById('blogTitle').value.trim();
        const category = document.getElementById('blogCategory').value;
        const status = document.getElementById('blogStatus').value;
        const content = tinymce.get('blogContent') ? tinymce.get('blogContent').getContent() : '';
        const tagsString = Array.from(tags).join(',');
        
        formData.append('action', blogId ? 'update' : 'add');
        if (blogId) formData.append('id', blogId);
        formData.append('title', title);
        formData.append('content', content);
        formData.append('category', category);
        formData.append('tags', tagsString);
        formData.append('status', status);
        
        // Get file directly from the input element
        const imageFile = imageInput.files[0];
        if (imageFile) {
            formData.append('featured_image', imageFile);
        }

        // Show loading message
        showError('Saving blog...');

        fetch('../../api/admin-blogs.php', {
            method: 'POST',
            body: formData
        })
        .then(res => {
            if (!res.ok) {
                throw new Error('Network response was not ok: ' + res.status);
            }
            return res.text();
        })
        .then(text => {
            
            // Try to find JSON in the response
            let jsonText = text.trim();
            
            // If response starts with BOM or whitespace, clean it
            if (jsonText.charCodeAt(0) === 0xFEFF) {
                jsonText = jsonText.substring(1);
            }
            
            try {
                const data = JSON.parse(jsonText);
                if (data.success) {
                    showError(blogId ? 'Blog updated successfully!' : 'Blog created successfully!');
                    setTimeout(() => {
                        window.location.href = '../index.php#blogs';
                    }, 1500);
                } else {
                    showError('Error: ' + (data.message || 'Failed to save blog'));
                }
            } catch (e) {
                console.error('JSON parse error:', e);
                showError('Server returned invalid response');
            }
        })
        .catch(error => {
            showError('Failed to save blog: ' + error.message);
        });
    }

    // Delete Post
    function deletePost() {
        const blogId = currentBlogId || new URLSearchParams(window.location.search).get('id');
        if (!blogId) {
            showError('No blog to delete');
            return;
        }
        
        const formData = new FormData();
        formData.append('action', 'delete');
        formData.append('id', blogId);
        
        fetch('../../api/admin-blogs.php', {
            method: 'POST',
            body: formData
        })
        .then(res => {
            if (!res.ok) {
                throw new Error('Network response was not ok');
            }
            return res.json();
        })
        .then(data => {
            if (data.success) {
                showError('Blog deleted successfully!');
                setTimeout(() => {
                    window.location.href = '../index.php#blogs';
                }, 1500);
            } else {
                showError('Error: ' + (data.message || 'Failed to delete blog'));
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showError('Failed to delete blog: ' + error.message);
        });
    }

    // Error Handling
    function showError(message) {
        const errorContainer = document.getElementById('errorContainer');
        errorContainer.textContent = message;
        errorContainer.style.display = 'block';
        setTimeout(() => {
            errorContainer.style.display = 'none';
        }, 5000);
    }

    // Modal Management
    const confirmModal = document.getElementById('confirmModal');
    const modalConfirm = document.getElementById('modalConfirm');
    const modalCancel = document.getElementById('modalCancel');
    const modalMessage = document.getElementById('modalMessage');
    let currentAction = '';

    function showModal(action, message) {
        currentAction = action;
        modalMessage.textContent = message;
        confirmModal.style.display = 'block';
    }

    function hideModal() {
        confirmModal.style.display = 'none';
    }

    modalConfirm.addEventListener('click', function() {
        switch (currentAction) {
            case 'save':
                saveChanges();
                break;
            case 'delete':
                deletePost();
                break;
            case 'cancel':
                window.location.href = '../index.php#blogs';
                break;
        }
        hideModal();
    });

    modalCancel.addEventListener('click', hideModal);

    // Delete Button Event
    document.getElementById('deletePost').addEventListener('click', function() {
        const blogId = currentBlogId || new URLSearchParams(window.location.search).get('id');
        if (blogId) {
            showModal('delete', 'Are you sure you want to delete this blog? This action cannot be undone.');
        } else {
            showError('No blog to delete');
        }
    });

    document.getElementById('cancelEdit').addEventListener('click', function() {
        showModal('cancel', 'Are you sure you want to cancel? Any unsaved changes will be lost.');
    });

    function hasUnsavedChanges() {
        // Implementation of checking for unsaved changes
        return document.getElementById('blogTitle').value !== '' || 
               tinymce.get('blogContent').getContent() !== '' ||
               tags.size > 0 ||
               currentImage !== null;
    }

    // Load initial post data if editing
    setTimeout(() => {
        loadPostData();
    }, 500);
});