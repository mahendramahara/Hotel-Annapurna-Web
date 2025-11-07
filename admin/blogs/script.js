document.addEventListener('DOMContentLoaded', function() {
    // Data structure for blog posts
    let blogPosts = JSON.parse(localStorage.getItem('blogPosts')) || [];
    let currentPostId = null;

    // Current user and time information
    const currentUser = {
        login: 'mahendramahara',
        currentDateTime: '2025-02-07 12:08:12'
    };

    // Sample post data structure
    const samplePost = {
        id: 1,
        title: "Sample Blog Post",
        content: "Welcome to your first blog post!",
        category: "technology",
        tags: ["welcome", "first-post"],
        image: null,
        author: currentUser.login,
        createdAt: '2025-02-07 11:55:13',
        lastEdited: '2025-02-07 12:08:12'
    };

    // If no posts exist, add sample post
    if (blogPosts.length === 0) {
        blogPosts.push(samplePost);
        localStorage.setItem('blogPosts', JSON.stringify(blogPosts));
    }

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
        resize: false
    });

    // Load existing post data if editing
    function loadPostData(postId) {
        const post = blogPosts.find(p => p.id === postId);
        if (post) {
            currentPostId = post.id;
            document.getElementById('blogTitle').value = post.title;
            document.getElementById('blogCategory').value = post.category;
            tinymce.get('blogContent').setContent(post.content);
            document.getElementById('lastEdited').textContent = post.lastEdited;
            document.getElementById('authorName').textContent = post.author;

            // Load tags
            post.tags.forEach(tag => addTag(tag));

            // Load image if exists
            if (post.image) {
                displayImage(post.image);
            }
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
                currentImage = e.target.result; // Store base64 image
                displayImage(currentImage);
            };
            reader.readAsDataURL(file);
        }
    });

    function displayImage(imageData) {
        imagePreview.innerHTML = `
            <img src="${imageData}" alt="Blog image">
            <input type="file" id="blogImage" accept="image/*">
        `;
        removeImageBtn.style.display = 'block';
    }

    removeImageBtn.addEventListener('click', function() {
        currentImage = null;
        imagePreview.innerHTML = `
            <input type="file" id="blogImage" accept="image/*">
            <label for="blogImage" class="upload-label">
                <i class="fas fa-cloud-upload-alt"></i>
                <span>Click to upload image</span>
            </label>
        `;
        removeImageBtn.style.display = 'none';
        document.getElementById('blogImage').addEventListener('change', imageInput.onchange);
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
        const newPost = {
            id: currentPostId || Date.now(),
            title: document.getElementById('blogTitle').value,
            content: tinymce.get('blogContent').getContent(),
            category: document.getElementById('blogCategory').value,
            tags: Array.from(tags),
            image: currentImage,
            author: currentUser.login,
            createdAt: currentPostId ? findPost(currentPostId).createdAt : currentUser.currentDateTime,
            lastEdited: currentUser.currentDateTime
        };

        if (currentPostId) {
            // Update existing post
            const index = blogPosts.findIndex(p => p.id === currentPostId);
            if (index !== -1) {
                blogPosts[index] = newPost;
            }
        } else {
            // Add new post
            blogPosts.push(newPost);
        }

        // Save to localStorage
        localStorage.setItem('blogPosts', JSON.stringify(blogPosts));
        showError('Changes saved successfully!');
        console.log('Current Blog Posts:', blogPosts);
    }

    // Delete Post
    function deletePost() {
        if (currentPostId) {
            blogPosts = blogPosts.filter(p => p.id !== currentPostId);
            localStorage.setItem('blogPosts', JSON.stringify(blogPosts));
            showError('Post deleted successfully!');
            // Redirect to posts list or clear form
            clearForm();
        }
    }

    // Helper Functions
    function findPost(id) {
        return blogPosts.find(p => p.id === id);
    }

    function clearForm() {
        form.reset();
        tinymce.get('blogContent').setContent('');
        tags.clear();
        tagsList.innerHTML = '';
        currentImage = null;
        removeImageBtn.click();
        currentPostId = null;
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
                clearForm();
                break;
        }
        hideModal();
    });

    modalCancel.addEventListener('click', hideModal);

    // Delete Button Event
    document.getElementById('deletePost').addEventListener('click', function() {
        if (currentPostId) {
            showModal('delete', 'Are you sure you want to delete this post? This action cannot be undone.');
        } else {
            showError('No post to delete');
        }
    });

    // Cancel Button Event
    document.getElementById('cancelEdit').addEventListener('click', function() {
        if (hasUnsavedChanges()) {
            showModal('cancel', 'You have unsaved changes. Are you sure you want to cancel?');
        } else {
            clearForm();
        }
    });

    function hasUnsavedChanges() {
        // Implementation of checking for unsaved changes
        return document.getElementById('blogTitle').value !== '' || 
               tinymce.get('blogContent').getContent() !== '' ||
               tags.size > 0 ||
               currentImage !== null;
    }

    // Load initial post data if editing (you would typically get the ID from the URL)
    // For demonstration, load the sample post
    loadPostData(1);

    // Log the initial data
    console.log('Initial Blog Posts:', blogPosts);
});