"use strict";

document.addEventListener('DOMContentLoaded', function() {
    initializeDashboard();
    setupSectionNavigation();
    setupModalHandlers();
    setupProfileHandlers();
    loadDashboardData();
});

function initializeDashboard() {
    const isIndexPage = window.location.pathname.includes('index.php') || 
                        window.location.pathname.endsWith('/admin/') || 
                        window.location.pathname.endsWith('/admin');
    
    if (isIndexPage) {
        const hash = window.location.hash.substring(1) || 'dashboard';
        showSection(hash);
        updateActiveNavLink(hash);
        
        window.addEventListener('hashchange', () => {
            const newSection = window.location.hash.substring(1) || 'dashboard';
            showSection(newSection);
            updateActiveNavLink(newSection);
        });
    }
}

function updateActiveNavLink(sectionName) {
    document.querySelectorAll('.navList').forEach(item => item.classList.remove('active'));
    
    document.querySelectorAll('.navList a').forEach(link => {
        const href = link.getAttribute('href');
        if (href && href.includes('#')) {
            const linkHash = href.split('#')[1];
            if (linkHash === sectionName) {
                link.closest('.navList').classList.add('active');
            }
        }
    });
}

function setupSectionNavigation() {
    const currentHash = window.location.hash.substring(1) || 'dashboard';
    
    document.querySelectorAll('.navList').forEach(item => item.classList.remove('active'));
    
    document.querySelectorAll('.navList a').forEach(link => {
        const href = link.getAttribute('href');
        if (href && href.includes('#')) {
            const linkHash = href.split('#')[1];
            if (linkHash === currentHash) {
                link.closest('.navList').classList.add('active');
            }
        }
    });
    
    document.querySelectorAll(".navList").forEach(element => {
        element.addEventListener('click', function(e) {
            document.querySelectorAll('.navList').forEach(item => item.classList.remove('active'));
            this.classList.add('active');
        });
    });
}

function showSection(sectionName) {
    const allSections = {
        'dashboard': '.dashboard-section',
        'requests': '.request-data-table',
        'menu': '.menu-dashboard',
        'tables': '.table-management-container',
        'rooms': '.room-section',
        'staff': '.staff-container',
        'blogs': '.blogs-data-table',
        'customers': '.customers-container',
        'reviews': '.reviews-container',
        'contacts': '.contacts-data-table',
        'coupons': '.coupon-section',
        'profile': '.profile-container'
    };
    
    Object.values(allSections).forEach(selector => {
        const element = document.querySelector(selector);
        if (element) element.style.display = 'none';
    });
    
    const targetSelector = allSections[sectionName];
    if (targetSelector) {
        const element = document.querySelector(targetSelector);
        if (element) {
            element.style.display = 'block';
            loadSectionData(sectionName);
        }
    }
}

function loadSectionData(section) {
    switch(section) {
        case 'menu':
            break;
        case 'rooms':
            loadRooms();
            break;
        case 'tables':
            loadTables();
            break;
        case 'staff':
            break;
        case 'customers':
            break;
        case 'contacts':
            break;
        case 'reviews':
            break;
        case 'coupons':
            if(typeof loadCoupons === 'function') {
                loadCoupons();
            }
            break;
        default:
            break;
    }
}

function setupModalHandlers() {
    document.addEventListener('click', function(e) {
        if (e.target.classList.contains('modal-overlay')) {
            e.target.classList.remove('active');
        }
    });
    
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            document.querySelectorAll('.modal-overlay.active').forEach(modal => {
                modal.classList.remove('active');
            });
        }
    });
}

function openModal(modalId) {
    const modal = document.getElementById(modalId);
    if (modal) {
        modal.classList.add('active');
    }
}

function closeModal(modalId) {
    const modal = document.getElementById(modalId);
    if (modal) {
        modal.classList.remove('active');
    }
}

function setupProfileHandlers() {
    const editProfileForm = document.getElementById('editProfileForm');
    if (editProfileForm) {
        editProfileForm.addEventListener('submit', updateProfile);
    }
    
    const passwordChangeForm = document.getElementById('passwordChangeForm');
    if (passwordChangeForm) {
        passwordChangeForm.addEventListener('submit', changePassword);
    }
    
    const profileImageForm = document.getElementById('profileImageForm');
    if (profileImageForm) {
        profileImageForm.addEventListener('submit', uploadProfileImage);
    }
    
    const profileImage = document.getElementById('profileImage');
    if (profileImage) {
        profileImage.addEventListener('change', previewProfileImage);
    }
    
    const passwordToggles = document.querySelectorAll('.profile-password-toggle');
    passwordToggles.forEach(toggle => {
        toggle.addEventListener('click', function() {
            const input = this.previousElementSibling;
            const icon = this.querySelector('ion-icon');
            
            if (input.type === 'password') {
                input.type = 'text';
                icon.setAttribute('name', 'eye-off-outline');
            } else {
                input.type = 'password';
                icon.setAttribute('name', 'eye-outline');
            }
        });
    });
    
    const newPasswordInput = document.getElementById('newPassword');
    if (newPasswordInput) {
        newPasswordInput.addEventListener('input', function() {
            const strength = calculatePasswordStrength(this.value);
            updatePasswordStrength(strength);
        });
    }
}

function openEditProfileModal() {
    // Get address from data attributes
    const addressField = document.querySelector('.profile-info-item.full-width p');
    if (addressField) {
        document.getElementById('editTole').value = addressField.dataset.addressTole || '';
        document.getElementById('editWard').value = addressField.dataset.addressWard || '';
        document.getElementById('editRural').value = addressField.dataset.addressRural || '';
        document.getElementById('editDistrict').value = addressField.dataset.addressDistrict || '';
        document.getElementById('editCountry').value = addressField.dataset.addressCountry || '';
    }
    openModal('editProfileModal');
}

function closeEditProfileModal() {
    closeModal('editProfileModal');
}

function openProfileImageModal() {
    openModal('profileImageModal');
}

function closeProfileImageModal() {
    closeModal('profileImageModal');
}

function updateProfile(e) {
    e.preventDefault();
    
    // Get form values with trim
    const firstName = (document.getElementById('editFirstName')?.value || '').trim();
    const lastName = (document.getElementById('editLastName')?.value || '').trim();
    const email = (document.getElementById('editEmail')?.value || '').trim();
    const contact = (document.getElementById('editContact')?.value || '').trim();
    
    // Validate required fields
    if (!firstName || !lastName || !email) {
        alert('First name, last name, and email are required!');
        return;
    }
    
    // Combine address fields
    const tole = (document.getElementById('editTole')?.value || '').trim();
    const ward = (document.getElementById('editWard')?.value || '').trim();
    const rural = (document.getElementById('editRural')?.value || '').trim();
    const district = (document.getElementById('editDistrict')?.value || '').trim();
    const country = (document.getElementById('editCountry')?.value || '').trim();
    const combinedAddress = `${tole}|${ward}|${rural}|${district}|${country}`;
    
    const formData = new FormData();
    formData.append('action', 'update_profile');
    formData.append('first_name', firstName);
    formData.append('last_name', lastName);
    formData.append('email', email);
    formData.append('contact', contact);
    formData.append('address', combinedAddress);
    
    fetch('../api/profile-handler.php', {
        method: 'POST',
        body: formData
    })
    .then(res => res.json())
    .then(data => {
        if (data.success) {
            alert(data.message);
            closeEditProfileModal();
            location.reload();
        } else {
            alert('Error: ' + data.message);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Failed to update profile');
    });
}

function changePassword(e) {
    e.preventDefault();
    
    const newPassword = document.getElementById('newPassword').value;
    const confirmPassword = document.getElementById('confirmPassword').value;
    
    if (newPassword !== confirmPassword) {
        alert('New passwords do not match!');
        return;
    }
    
    if (calculatePasswordStrength(newPassword) < 75) {
        alert('Please choose a stronger password!');
        return;
    }
    
    const formData = new FormData();
    formData.append('action', 'change_password');
    formData.append('current_password', document.getElementById('currentPassword').value);
    formData.append('new_password', newPassword);
    
    fetch('../api/profile-handler.php', {
        method: 'POST',
        body: formData
    })
    .then(res => res.json())
    .then(data => {
        if (data.success) {
            alert(data.message);
            e.target.reset();
        } else {
            alert('Error: ' + data.message);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Failed to change password');
    });
}

function uploadProfileImage(e) {
    e.preventDefault();
    
    const formData = new FormData();
    formData.append('action', 'upload_profile_image');
    formData.append('image', document.getElementById('profileImage').files[0]);
    
    fetch('../api/profile-handler.php', {
        method: 'POST',
        body: formData
    })
    .then(res => res.json())
    .then(data => {
        if (data.success) {
            alert(data.message);
            closeProfileImageModal();
            location.reload();
        } else {
            alert('Error: ' + data.message);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Failed to upload image');
    });
}

function previewProfileImage(e) {
    const file = e.target.files[0];
    if (file) {
        const reader = new FileReader();
        reader.onload = function(e) {
            document.getElementById('imagePreview').src = e.target.result;
        };
        reader.readAsDataURL(file);
    }
}

function calculatePasswordStrength(password) {
    let strength = 0;
    
    if (password.length >= 8) strength += 25;
    if (password.match(/[a-z]/)) strength += 25;
    if (password.match(/[A-Z]/)) strength += 25;
    if (password.match(/[0-9]/)) strength += 25;
    
    return strength;
}

function updatePasswordStrength(strength) {
    const indicator = document.querySelector('.profile-password-strength');
    if (!indicator) return;
    
    let color;
    if (strength <= 25) color = '#ff4444';
    else if (strength <= 50) color = '#ffbb33';
    else if (strength <= 75) color = '#00C851';
    else color = '#007E33';
    
    indicator.style.width = strength + '%';
    indicator.style.backgroundColor = color;
}

function loadDashboardData() {
    fetch('../api/admin-dashboard.php?action=get_stats')
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                updateDashboardStats(data.stats);
            }
        })
        .catch(error => console.error('Error loading dashboard:', error));
}

function updateDashboardStats(stats) {
    const elements = {
        totalOrders: document.querySelector('.box1 .number'),
        totalRevenue: document.querySelector('.box2 .number'),
        totalCustomers: document.querySelector('.box3 .number'),
        pendingRequests: document.querySelector('.box4 .number')
    };
    
    if (elements.totalOrders && stats.total_orders) {
        elements.totalOrders.textContent = stats.total_orders;
    }
    if (elements.totalRevenue && stats.total_revenue) {
        elements.totalRevenue.textContent = '₹' + parseFloat(stats.total_revenue).toFixed(2);
    }
    if (elements.totalCustomers && stats.total_customers) {
        elements.totalCustomers.textContent = stats.total_customers;
    }
    if (elements.pendingRequests && stats.pending_requests) {
        elements.pendingRequests.textContent = stats.pending_requests;
    }
}

function loadRooms() {
    fetch('../api/room-handler.php?action=get_all')
        .then(res => res.json())
        .then(data => {
            if (data.success && data.data) {
                displayRooms(data.data);
            }
        })
        .catch(error => console.error('Error loading rooms:', error));
}

function displayRooms(rooms) {
    const tbody = document.querySelector('.rooms-section table tbody');
    if (!tbody) return;
    
    if (rooms.length === 0) {
        tbody.innerHTML = '<tr><td colspan="10" style="text-align:center;">No rooms found</td></tr>';
        return;
    }
    
    tbody.innerHTML = rooms.map((room, index) => `
        <tr>
            <td>${index + 1}</td>
            <td><img src="../${room.image_path || 'images/rooms/default.jpg'}" alt="Room" style="width:50px;height:50px;object-fit:cover;border-radius:5px;"></td>
            <td>${room.room_no}</td>
            <td>${room.room_type}</td>
            <td>${room.total_beds}</td>
            <td>${room.bed_size}</td>
            <td><span class="status-badge status-${room.status}">${room.status}</span></td>
            <td>₹${parseFloat(room.price).toFixed(2)}</td>
            <td>${room.price_today ? '₹' + parseFloat(room.price_today).toFixed(2) : 'N/A'}</td>
            <td>
                <div class="action-buttons">
                    <button class="action-btn edit-btn" onclick="openRoomModal('edit', ${room.id})">
                        <ion-icon name="create-outline"></ion-icon>
                    </button>
                    <button class="action-btn delete-btn" onclick="deleteRoom(${room.id})">
                        <ion-icon name="trash-outline"></ion-icon>
                    </button>
                </div>
            </td>
        </tr>
    `).join('');
}

function loadTables() {
    fetch('../api/table-handler.php?action=get_all')
        .then(res => res.json())
        .then(data => {
            if (data.success && data.data) {
                displayTables(data.data);
            }
        })
        .catch(error => console.error('Error loading tables:', error));
}

function displayTables(tables) {
    const tbody = document.getElementById('table-list-body');
    if (!tbody) return;
    
    if (tables.length === 0) {
        tbody.innerHTML = '<tr><td colspan="10" style="text-align:center;">No tables found</td></tr>';
        return;
    }
    
    tbody.innerHTML = tables.map((table, index) => `
        <tr>
            <td>${index + 1}</td>
            <td><img src="../${table.image_path || 'images/tables/default.jpg'}" alt="Table" style="width:50px;height:50px;object-fit:cover;border-radius:5px;"></td>
            <td>${table.table_no}</td>
            <td>${table.total_chairs}</td>
            <td><span class="status-badge status-${table.booking_status}">${table.booking_status}</span></td>
            <td>₹${parseFloat(table.price_main).toFixed(2)}</td>
            <td>${table.price_today ? '₹' + parseFloat(table.price_today).toFixed(2) : 'N/A'}</td>
            <td>${table.location}</td>
            <td>${new Date(table.updated_at).toLocaleDateString()}</td>
            <td>
                <div class="action-buttons">
                    <button class="action-btn edit-btn" onclick="openTableModal('edit', ${table.id})">
                        <ion-icon name="create-outline"></ion-icon>
                    </button>
                    <button class="action-btn delete-btn" onclick="deleteTable(${table.id})">
                        <ion-icon name="trash-outline"></ion-icon>
                    </button>
                </div>
            </td>
        </tr>
    `).join('');
}

function openRoomModal(type, id) {
    const modal = document.getElementById('roomModal');
    if (modal) {
        if (type === 'add') {
            document.getElementById('roomForm').reset();
            document.getElementById('roomEditId').value = '';
            document.getElementById('modalTitle').textContent = 'Add New Room';
        } else if (type === 'edit' && id) {
            fetch(`../api/room-handler.php?action=get_by_id&id=${id}`)
                .then(res => res.json())
                .then(data => {
                    if (data.success && data.data) {
                        const room = data.data;
                        document.getElementById('roomEditId').value = room.id;
                        document.getElementById('roomNumber').value = room.room_no;
                        document.getElementById('roomType').value = room.room_type;
                        document.getElementById('totalBeds').value = room.total_beds;
                        document.getElementById('bedSize').value = room.bed_size;
                        document.getElementById('roomPrice').value = room.price;
                        document.getElementById('roomStatus').value = room.status;
                        document.getElementById('todayPrice').value = room.price_today || room.price;
                        document.getElementById('amenities').value = room.amenities || '';
                        document.getElementById('shortDescription').value = room.short_description || '';
                        document.getElementById('modalTitle').textContent = 'Edit Room';
                    } else {
                        alert('Failed to load room data: ' + (data.message || 'Unknown error'));
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Failed to load room data');
                });
        }
        modal.style.display = 'block';
    }
}

function closeRoomModal() {
    const modal = document.getElementById('roomModal');
    if (modal) {
        modal.style.display = 'none';
    }
}

const roomForm = document.getElementById('roomForm');
if (roomForm) {
    roomForm.addEventListener('submit', function(e) {
        e.preventDefault();
        saveRoom();
    });
}

function saveRoom() {
    const roomId = document.getElementById('roomEditId').value;
    const roomNumber = document.getElementById('roomNumber').value;
    const roomType = document.getElementById('roomType').value;
    const totalBeds = document.getElementById('totalBeds').value;
    const bedSize = document.getElementById('bedSize').value;
    const roomPrice = document.getElementById('roomPrice').value;
    const roomStatus = document.getElementById('roomStatus').value;
    const todayPrice = document.getElementById('todayPrice').value;
    const amenities = document.getElementById('amenities').value;
    const shortDescription = document.getElementById('shortDescription').value;
    const imageFile = document.getElementById('roomImage').files[0];

    if (!roomNumber || !roomType || !totalBeds || !bedSize || !roomPrice || !todayPrice) {
        alert('Please fill all required fields!');
        return;
    }

    const formData = new FormData();
    formData.append('action', roomId ? 'update' : 'add');
    formData.append('room_no', roomNumber);
    formData.append('room_type', roomType.toLowerCase());
    formData.append('total_beds', totalBeds);
    formData.append('bed_size', bedSize.toLowerCase());
    formData.append('price', roomPrice);
    formData.append('status', roomStatus);
    formData.append('price_today', todayPrice);
    formData.append('amenities', amenities);
    formData.append('short_description', shortDescription);

    if (imageFile) {
        formData.append('image', imageFile);
    }

    if (roomId) {
        formData.append('id', roomId);
    }

    fetch('../api/room-handler.php', {
        method: 'POST',
        body: formData
    })
    .then(res => res.json())
    .then(data => {
        if (data.success) {
            alert(data.message || 'Room saved successfully!');
            closeRoomModal();
            location.reload();
        } else {
            alert('Error: ' + data.message);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Failed to save room');
    });
}

function deleteRoom(id) {
    if (!confirm('Are you sure you want to delete this room?')) return;
    
    const formData = new FormData();
    formData.append('action', 'delete');
    formData.append('id', id);
    
    fetch('../api/room-handler.php', {
        method: 'POST',
        body: formData
    })
    .then(res => res.json())
    .then(data => {
        if (data.success) {
            alert(data.message);
            location.reload();
        } else {
            alert('Error: ' + data.message);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Failed to delete room');
    });
}

function viewRoomDetails(id) {
    fetch(`../api/room-handler.php?action=get_by_id&id=${id}`)
        .then(res => res.json())
        .then(data => {
            if (data.success && data.data) {
                const room = data.data;
                
                // Set image
                document.getElementById('viewRoomImage').src = '../' + (room.image_path || 'assets/images/default-room.jpg');
                
                // Set basic info
                document.getElementById('viewRoomNumber').textContent = room.room_no;
                document.getElementById('viewRoomType').innerHTML = `<span class="room-type-badge room-type-${room.room_type.toLowerCase()}">${room.room_type.charAt(0).toUpperCase() + room.room_type.slice(1)}</span>`;
                document.getElementById('viewTotalBeds').textContent = room.total_beds;
                document.getElementById('viewBedSize').textContent = room.bed_size.charAt(0).toUpperCase() + room.bed_size.slice(1);
                document.getElementById('viewStatus').innerHTML = `<span class="room-status room-status-${room.status.toLowerCase()}">${room.status.charAt(0).toUpperCase() + room.status.slice(1)}</span>`;
                
                // Set prices
                document.getElementById('viewPrice').textContent = 'RS ' + parseFloat(room.price).toLocaleString();
                document.getElementById('viewTodayPrice').textContent = room.price_today ? 'RS ' + parseFloat(room.price_today).toLocaleString() : 'RS ' + parseFloat(room.price).toLocaleString();
                
                // Set amenities and description
                document.getElementById('viewAmenities').textContent = room.amenities || 'Not specified';
                document.getElementById('viewDescription').textContent = room.short_description || 'No description available';
                
                // Set timestamps
                document.getElementById('viewCreatedAt').textContent = new Date(room.created_at).toLocaleString('en-US', {
                    year: 'numeric',
                    month: 'short',
                    day: 'numeric',
                    hour: '2-digit',
                    minute: '2-digit'
                });
                document.getElementById('viewUpdatedAt').textContent = new Date(room.updated_at).toLocaleString('en-US', {
                    year: 'numeric',
                    month: 'short',
                    day: 'numeric',
                    hour: '2-digit',
                    minute: '2-digit'
                });
                
                // Show modal
                document.getElementById('roomViewModal').style.display = 'block';
            } else {
                alert('Failed to load room details: ' + (data.message || 'Unknown error'));
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Failed to load room details');
        });
}

function closeRoomViewModal() {
    document.getElementById('roomViewModal').style.display = 'none';
}

function openTableModal(type, id) {
    const modal = document.getElementById('table-modal');
    if (modal) {
        if (type === 'add') {
            document.getElementById('table-form').reset();
            document.getElementById('table-edit-id').value = '';
            document.getElementById('table-modal-title').textContent = 'Add New Table';
        } else if (type === 'edit' && id) {
            fetch(`../api/table-handler.php?action=get_by_id&id=${id}`)
                .then(res => res.json())
                .then(data => {
                    if (data.success && data.data) {
                        const table = data.data;
                        document.getElementById('table-edit-id').value = table.id;
                        document.getElementById('table-number').value = table.table_no;
                        document.getElementById('table-capacity').value = table.total_chairs;
                        document.getElementById('table-status').value = table.booking_status;
                        document.getElementById('table-price-standard').value = table.price_main;
                        document.getElementById('table-price-today').value = table.price_today || '';
                        document.getElementById('table-location').value = table.location;
                        document.getElementById('table-modal-title').textContent = 'Edit Table';
                    } else {
                        alert('Failed to load table data: ' + (data.message || 'Unknown error'));
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Failed to load table data');
                });
        }
        modal.style.display = 'block';
    }
}

function closeTableModal() {
    const modal = document.getElementById('table-modal');
    if (modal) {
        modal.style.display = 'none';
    }
}

function deleteTable(id) {
    if (!confirm('Are you sure you want to delete this table?')) return;
    
    const formData = new FormData();
    formData.append('action', 'delete');
    formData.append('id', id);
    
    fetch('../api/table-handler.php', {
        method: 'POST',
        body: formData
    })
    .then(res => res.json())
    .then(data => {
        if (data.success) {
            alert(data.message);
            loadTables();
        } else {
            alert('Error: ' + data.message);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Failed to delete table');
    });
}

function deleteBlog(id) {
    if (!confirm('Are you sure you want to delete this blog?')) return;
    
    fetch('../api/admin-blogs.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ action: 'delete', id: id })
    })
    .then(res => res.json())
    .then(data => {
        if (data.success) {
            alert('Blog deleted successfully');
            location.reload();
        } else {
            alert('Error: ' + (data.message || 'Delete failed'));
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Error deleting blog');
    });
}

function openAddModal(category) {
    const modal = document.getElementById('menu-modal');
    const specialDayGroup = document.getElementById('special-day-group');
    const itemCategory = document.getElementById('item-category');
    const modalTitle = document.getElementById('modal-title');
    
    if (modal) {
        modal.style.display = 'block';
        document.getElementById('menu-item-form').reset();
        document.getElementById('item-id').value = '';
        
        if (itemCategory) {
            if (category === 'vegetarian') {
                itemCategory.value = 'veg';
                modalTitle.textContent = 'Add Vegetarian Item';
            } else if (category === 'non-vegetarian') {
                itemCategory.value = 'non-veg';
                modalTitle.textContent = 'Add Non-Vegetarian Item';
            } else if (category === 'special') {
                itemCategory.value = 'special';
                modalTitle.textContent = 'Add Special Item';
            }
        }
        
        if (specialDayGroup) {
            specialDayGroup.style.display = category === 'special' ? 'block' : 'none';
        }
    }
}

function editMenuItem(id) {
    fetch(`../api/menu-handler.php?action=get_by_id&id=${id}`)
        .then(res => res.json())
        .then(data => {
            if (data.success && data.data) {
                const item = data.data;
                const modal = document.getElementById('menu-modal');
                const specialDayGroup = document.getElementById('special-day-group');
                const modalTitle = document.getElementById('modal-title');
                
                document.getElementById('item-id').value = item.id;
                document.getElementById('food-name').value = item.food_name;
                document.getElementById('price').value = item.price;
                document.getElementById('discount-price').value = item.discount_price || '';
                document.getElementById('item-category').value = item.category;
                
                document.querySelectorAll('input[name="available-days[]"]').forEach(checkbox => {
                    checkbox.checked = false;
                });
                
                if (item.category === 'veg') {
                    modalTitle.textContent = 'Edit Vegetarian Item';
                    specialDayGroup.style.display = 'none';
                } else if (item.category === 'non-veg') {
                    modalTitle.textContent = 'Edit Non-Vegetarian Item';
                    specialDayGroup.style.display = 'none';
                } else if (item.category === 'special') {
                    modalTitle.textContent = 'Edit Special Item';
                    specialDayGroup.style.display = 'block';
                    
                    if (item.available_days && item.available_days !== 'All Day') {
                        const days = item.available_days.split(',').map(d => d.trim());
                        document.querySelectorAll('input[name="available-days[]"]').forEach(checkbox => {
                            if (checkbox.id !== 'all-days-checkbox') {
                                checkbox.checked = days.includes(checkbox.value);
                            }
                        });
                    }
                }
                
                modal.style.display = 'block';
            } else {
                alert('Failed to load menu item: ' + (data.message || 'Unknown error'));
            }
        })
        .catch(error => {
            alert('Failed to load menu item. Please try again.');
        });
}

function saveMenuItem() {
    const itemId = document.getElementById('item-id').value;
    const category = document.getElementById('item-category').value;
    const foodName = document.getElementById('food-name').value;
    const price = document.getElementById('price').value;
    const discountPrice = document.getElementById('discount-price').value;
    const imageFile = document.getElementById('image-upload').files[0];
    
    if (!category || !foodName || !price) {
        alert('Please fill all required fields!');
        return;
    }
    
    const formData = new FormData();
    formData.append('action', itemId ? 'update' : 'add');
    formData.append('category', category);
    formData.append('food_name', foodName);
    formData.append('price', price);
    
    if (discountPrice) {
        formData.append('discount_price', discountPrice);
    }
    
    if (imageFile) {
        formData.append('image', imageFile);
    }
    
    if (itemId) {
        formData.append('id', itemId);
    }
    
    if (category === 'special') {
        const allDaysCheckbox = document.getElementById('all-days-checkbox');
        const checkedDays = [];
        
        document.querySelectorAll('input[name="available-days[]"]:checked').forEach(cb => {
            if (cb.id !== 'all-days-checkbox') {
                checkedDays.push(cb.value);
            }
        });
        
        if (allDaysCheckbox && allDaysCheckbox.checked) {
            formData.append('available_days', 'All Days');
        } else if (checkedDays.length > 0) {
            formData.append('available_days', checkedDays.join(','));
        } else {
            formData.append('available_days', 'All Days');
        }
    } else {
        formData.append('available_days', 'All Days');
    }
    
    fetch('../api/menu-handler.php', {
        method: 'POST',
        body: formData
    })
    .then(res => res.json())
    .then(data => {
        if (data.success) {
            alert(data.message || 'Menu item saved successfully!');
            closeMenuModal();
            location.reload();
        } else {
            alert('Error: ' + data.message);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Failed to save menu item');
    });
}

function deleteMenuItem(id) {
    if (!confirm('Are you sure you want to delete this menu item?')) return;
    
    const formData = new FormData();
    formData.append('action', 'delete');
    formData.append('id', id);
    
    fetch('../api/menu-handler.php', {
        method: 'POST',
        body: formData
    })
    .then(res => res.json())
    .then(data => {
        if (data.success) {
            alert(data.message);
            location.reload();
        } else {
            alert('Error: ' + data.message);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Failed to delete menu item');
    });
}

function closeMenuModal() {
    const modal = document.getElementById('menu-modal');
    if (modal) {
        modal.style.display = 'none';
    }
}

function closeModal() {
    closeMenuModal();
}

function clearForm() {
    const form = document.getElementById('menu-item-form');
    if (form) {
        form.reset();
        document.getElementById('item-id').value = '';
    }
}

function toggleAllDays(checkbox) {
    const dayCheckboxes = document.querySelectorAll('input[name="available-days[]"]:not(#all-days-checkbox)');
    dayCheckboxes.forEach(cb => {
        cb.checked = checkbox.checked;
    });
}

document.addEventListener('DOMContentLoaded', function() {
    const menuModal = document.getElementById('menu-modal');
    const menuClose = document.querySelector('.menu-modal-close');
    
    if (menuClose) {
        menuClose.onclick = function() {
            closeMenuModal();
        };
    }
    
    if (menuModal) {
        window.onclick = function(event) {
            if (event.target === menuModal) {
                closeMenuModal();
            }
        };
    }
});

function toggleAllDays(checkbox) {
    const dayCheckboxes = document.querySelectorAll('input[name="available-days[]"]');
    dayCheckboxes.forEach(cb => {
        if (cb.id !== 'all-days-checkbox') {
            cb.checked = checkbox.checked;
        }
    });
}

function openStaffModal() {
    const modal = document.getElementById('staffModal');
    if (modal) {
        modal.style.display = 'block';
        document.getElementById('staffForm').reset();
        document.getElementById('staffId').value = '';
        document.getElementById('staffModalTitle').textContent = 'Add New Staff';
        document.getElementById('passwordGroup').style.display = 'block';
        document.getElementById('profilePicPreview').style.display = 'none';
    }
}

function closeStaffModal() {
    const modal = document.getElementById('staffModal');
    if (modal) {
        modal.style.display = 'none';
        document.getElementById('profilePicPreview').style.display = 'none';
    }
}

function previewProfilePic(input) {
    const previewDiv = document.getElementById('profilePicPreview');
    const previewImg = document.getElementById('profilePicImg');
    
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        reader.onload = function(e) {
            previewImg.src = e.target.result;
            previewDiv.style.display = 'block';
        };
        reader.readAsDataURL(input.files[0]);
    } else {
        previewDiv.style.display = 'none';
    }
}

function editStaff(id) {
    fetch(`../api/admin-users.php?action=get&id=${id}`)
        .then(res => res.json())
        .then(data => {
            if (data.success && data.user) {
                const user = data.user;
                
                // Open modal first without resetting
                const modal = document.getElementById('staffModal');
                if (modal) {
                    modal.style.display = 'block';
                }
                
                // Then fill the form with user data
                document.getElementById('staffId').value = user.id;
                document.getElementById('firstName').value = user.first_name || '';
                document.getElementById('lastName').value = user.last_name || '';
                document.getElementById('staffEmail').value = user.email || '';
                document.getElementById('staffContact').value = user.contact || '';
                document.getElementById('staffAddress').value = user.address || '';
                document.getElementById('staffStatus').value = user.status || 'verified';
                document.getElementById('staffSalary').value = user.salary || '';
                document.getElementById('staffModalTitle').textContent = 'Edit Staff';
                document.getElementById('passwordGroup').style.display = 'none';
                
                // Show existing profile picture
                if (user.profile_pic) {
                    const previewDiv = document.getElementById('profilePicPreview');
                    const previewImg = document.getElementById('profilePicImg');
                    previewImg.src = '../' + user.profile_pic;
                    previewDiv.style.display = 'block';
                } else {
                    document.getElementById('profilePicPreview').style.display = 'none';
                }
            } else {
                alert('Failed to load staff data: ' + (data.message || 'Unknown error'));
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Failed to load staff data');
        });
}

function deleteStaff(id) {
    if (!confirm('Are you sure you want to delete this staff member? This action cannot be undone.')) return;
    
    fetch('../api/admin-users.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ action: 'delete', id: parseInt(id) })
    })
    .then(res => {
        if (!res.ok) {
            throw new Error('Network response was not ok');
        }
        return res.json();
    })
    .then(data => {
        if (data.success) {
            alert('Staff deleted successfully!');
            location.reload();
        } else {
            alert('Error: ' + (data.message || 'Failed to delete staff'));
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Failed to delete staff. Please try again.');
    });
}

let currentViewStaffId = null;

function viewStaffDetails(id) {
    currentViewStaffId = id;
    fetch(`../api/admin-users.php?action=get&id=${id}`)
        .then(res => res.json())
        .then(data => {
            if (data.success && data.user) {
                const user = data.user;
                
                // Set profile picture
                const profilePic = document.getElementById('viewStaffProfilePic');
                profilePic.src = user.profile_pic ? '../' + user.profile_pic : '../assets/images/default-avatar.png';
                
                // Set text fields
                document.getElementById('viewFirstName').textContent = user.first_name || 'N/A';
                document.getElementById('viewLastName').textContent = user.last_name || 'N/A';
                document.getElementById('viewEmail').textContent = user.email || 'N/A';
                document.getElementById('viewContact').textContent = user.contact || 'N/A';
                document.getElementById('viewAddress').textContent = user.address || 'N/A';
                document.getElementById('viewSalary').textContent = user.salary ? 'RS ' + parseFloat(user.salary).toFixed(2) : 'Not Set';
                document.getElementById('viewRole').textContent = user.role ? user.role.charAt(0).toUpperCase() + user.role.slice(1) : 'N/A';
                document.getElementById('viewJoinedDate').textContent = user.created_at ? new Date(user.created_at).toLocaleDateString() : 'N/A';
                
                // Set status with color
                const statusElem = document.getElementById('viewStatus');
                statusElem.innerHTML = `<span class="staff-status ${user.status ? user.status.toLowerCase() : ''}">${user.status || 'N/A'}</span>`;
                
                // Open modal
                document.getElementById('staffViewModal').style.display = 'block';
            } else {
                alert('Failed to load staff details');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Failed to load staff details');
        });
}

function closeStaffViewModal() {
    document.getElementById('staffViewModal').style.display = 'none';
    currentViewStaffId = null;
}

function editFromView() {
    if (currentViewStaffId) {
        closeStaffViewModal();
        editStaff(currentViewStaffId);
    }
}

const menuModalClose = document.querySelector('.menu-modal-close');
if (menuModalClose) {
    menuModalClose.addEventListener('click', closeMenuModal);
}

const staffCloseBtn = document.querySelector('.staff-close-btn');
if (staffCloseBtn) {
    staffCloseBtn.addEventListener('click', closeStaffModal);
}

const staffForm = document.getElementById('staffForm');
if (staffForm) {
    staffForm.addEventListener('submit', function(e) {
        e.preventDefault();
        saveStaff();
    });
}

function saveStaff() {
    const staffId = document.getElementById('staffId').value;
    const firstName = document.getElementById('firstName').value;
    const lastName = document.getElementById('lastName').value;
    const email = document.getElementById('staffEmail').value;
    const contact = document.getElementById('staffContact').value;
    const address = document.getElementById('staffAddress').value;
    const status = document.getElementById('staffStatus').value;
    const salary = document.getElementById('staffSalary').value;
    const password = document.getElementById('staffPassword') ? document.getElementById('staffPassword').value : '';
    const profilePicInput = document.getElementById('profilePic');
    const profilePic = profilePicInput ? profilePicInput.files[0] : null;

    // Validation
    if (!firstName || !lastName || !email || !contact) {
        alert('Please fill all required fields!');
        return;
    }

    // Password required for new staff only
    if (!staffId && !password) {
        alert('Password is required for new staff!');
        return;
    }

    const formData = new FormData();
    formData.append('action', staffId ? 'update' : 'create');
    formData.append('first_name', firstName);
    formData.append('last_name', lastName);
    formData.append('email', email);
    formData.append('contact', contact);
    formData.append('address', address);
    formData.append('status', status);
    formData.append('salary', salary || '');
    formData.append('role', 'staff');

    if (password) {
        formData.append('password', password);
    }

    if (profilePic) {
        formData.append('profile_pic', profilePic);
    }

    if (staffId) {
        formData.append('id', staffId);
    }

    fetch('../api/admin-users.php', {
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
            alert(staffId ? 'Staff updated successfully!' : 'Staff added successfully!');
            closeStaffModal();
            location.reload();
        } else {
            alert('Error: ' + (data.message || 'Failed to save staff'));
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Failed to save staff. Please try again.');
    });
}

const roomModalClose = document.querySelector('.room-modal-close');
if (roomModalClose) {
    roomModalClose.addEventListener('click', closeRoomModal);
}

const tableModalClose = document.querySelector('.table-modal-close');
if (tableModalClose) {
    tableModalClose.addEventListener('click', closeTableModal);
}

const tableModalCancel = document.getElementById('table-modal-cancel');
if (tableModalCancel) {
    tableModalCancel.addEventListener('click', closeTableModal);
}

const tableAddNewBtn = document.getElementById('table-add-new-btn');
if (tableAddNewBtn) {
    tableAddNewBtn.addEventListener('click', function() {
        openTableModal('add');
    });
}

const tableForm = document.getElementById('table-form');
if (tableForm) {
    tableForm.addEventListener('submit', function(e) {
        e.preventDefault();
        saveTable();
    });
}

function saveTable() {
    const tableId = document.getElementById('table-edit-id').value;
    const tableNumber = document.getElementById('table-number').value;
    const tableCapacity = document.getElementById('table-capacity').value;
    const tableStatus = document.getElementById('table-status').value;
    const priceStandard = document.getElementById('table-price-standard').value;
    const priceToday = document.getElementById('table-price-today').value;
    const tableLocation = document.getElementById('table-location').value;
    const imageFile = document.getElementById('table-image').files[0];

    if (!tableNumber || !tableCapacity || !priceStandard) {
        alert('Please fill all required fields!');
        return;
    }

    const formData = new FormData();
    formData.append('action', tableId ? 'update' : 'add');
    formData.append('table_no', tableNumber);
    formData.append('total_chairs', tableCapacity);
    formData.append('booking_status', tableStatus);
    formData.append('price_main', priceStandard);
    
    if (priceToday) {
        formData.append('price_today', priceToday);
    }
    
    formData.append('location', tableLocation);

    if (imageFile) {
        formData.append('image', imageFile);
    }

    if (tableId) {
        formData.append('id', tableId);
    }

    fetch('../api/table-handler.php', {
        method: 'POST',
        body: formData
    })
    .then(res => res.json())
    .then(data => {
        if (data.success) {
            alert(data.message || 'Table saved successfully!');
            closeTableModal();
            location.reload();
        } else {
            alert('Error: ' + data.message);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Failed to save table');
    });
}

let confirmCallback = null;

function closeConfirmModal() {
    const modal = document.getElementById('confirm-modal');
    if (modal) {
        modal.style.display = 'none';
    }
    confirmCallback = null;
}

function confirmAction() {
    if (confirmCallback && typeof confirmCallback === 'function') {
        confirmCallback();
    }
    closeConfirmModal();
}

function showConfirmModal(message, callback) {
    const modal = document.getElementById('confirm-modal');
    const messageElement = document.getElementById('confirm-message');
    
    if (modal && messageElement) {
        messageElement.textContent = message;
        confirmCallback = callback;
        modal.style.display = 'block';
    } else {
        if (confirm(message)) {
            callback();
        }
    }
}

window.addEventListener('click', function(e) {
    const menuModal = document.getElementById('menu-modal');
    if (e.target === menuModal) {
        closeMenuModal();
    }
    
    const staffModal = document.getElementById('staffModal');
    if (e.target === staffModal) {
        closeStaffModal();
    }
    
    const roomModal = document.getElementById('roomModal');
    if (e.target === roomModal) {
        closeRoomModal();
    }
    
    const tableModal = document.getElementById('table-modal');
    if (e.target === tableModal) {
        closeTableModal();
    }
    
    const confirmModal = document.getElementById('confirm-modal');
    if (e.target === confirmModal) {
        closeConfirmModal();
    }
    
    // Customer modals
    const addCustomerModal = document.getElementById('addModal');
    if (e.target === addCustomerModal) {
        closeAddCustomerModal();
    }
    
    const editCustomerModal = document.getElementById('editModal');
    if (e.target === editCustomerModal) {
        closeEditCustomerModal();
    }
    
    const viewCustomerModal = document.getElementById('viewModal');
    if (e.target === viewCustomerModal) {
        closeViewCustomerModal();
    }
});

// ============================================
// CUSTOMER MANAGEMENT FUNCTIONS
// ============================================
let currentCustomerData = null;

// Add Customer Modal
function openAddCustomerModal() {
    const modal = document.getElementById('addModal');
    if (modal) {
        modal.style.display = 'block';
        document.getElementById('addCustomerForm').reset();
    }
}

function closeAddModal() {
    const modal = document.getElementById('addModal');
    if (modal) {
        modal.style.display = 'none';
        document.getElementById('addCustomerForm').reset();
    }
}

function closeAddCustomerModal() {
    closeAddModal();
}

// View Customer
function viewCustomer(id) {
    console.log('Fetching customer with ID:', id);
    fetch(`../api/admin-users.php?action=get&id=${id}`)
        .then(res => {
            console.log('Response status:', res.status);
            return res.json();
        })
        .then(data => {
            console.log('Customer data received:', data);
            if(data.success) {
                const user = data.user;
                currentCustomerData = user;
                
                const profilePic = document.getElementById('viewProfilePic');
                if(profilePic) {
                    profilePic.src = user.profile_pic ? '../' + user.profile_pic : '../assets/images/default-avatar.png';
                }
                
                const fullNameEl = document.getElementById('viewFullName');
                if(fullNameEl) fullNameEl.textContent = (user.first_name || '') + ' ' + (user.last_name || '');
                
                const emailEl = document.getElementById('viewEmail');
                if(emailEl) emailEl.textContent = user.email || 'N/A';
                
                const statusBadge = document.getElementById('viewStatusBadge');
                if(statusBadge) {
                    statusBadge.textContent = user.status || 'unknown';
                    statusBadge.className = `status-badge status-${(user.status || 'unknown').toLowerCase()}`;
                }
                
                const contactEl = document.getElementById('viewContact');
                if(contactEl) contactEl.textContent = user.contact || 'N/A';
                
                const createdEl = document.getElementById('viewCreatedAt');
                if(createdEl && user.created_at) {
                    createdEl.textContent = new Date(user.created_at).toLocaleDateString();
                }
                
                const lastLoginEl = document.getElementById('viewLastLogin');
                if(lastLoginEl && user.updated_at) {
                    lastLoginEl.textContent = new Date(user.updated_at).toLocaleString();
                }
                
                const orderCountEl = document.getElementById('viewOrderCount');
                if(orderCountEl) orderCountEl.textContent = user.order_count || '0';
                
                const addressEl = document.getElementById('viewAddress');
                if(addressEl) addressEl.textContent = user.address || 'No address provided';
                
                const modal = document.getElementById('viewModal');
                console.log('Modal element:', modal);
                if(modal) {
                    modal.style.display = 'flex';
                    console.log('Modal display set to flex');
                } else {
                    console.error('Modal element not found!');
                }
            } else {
                console.error('API Error:', data.message);
                alert('Error loading customer: ' + (data.message || 'Unknown error'));
            }
        })
        .catch(err => {
            console.error('Fetch error:', err);
            alert('Failed to load customer details: ' + err.message);
        });
}

function closeViewModal() {
    const modal = document.getElementById('viewModal');
    if (modal) {
        modal.style.display = 'none';
        currentCustomerData = null;
    }
}

function closeViewCustomerModal() {
    closeViewModal();
}

function editFromView() {
    closeViewModal();
    if(currentCustomerData) {
        editCustomer(currentCustomerData.id);
    }
}

// Edit Customer
function editCustomer(id) {
    fetch(`../api/admin-users.php?action=get&id=${id}`)
        .then(res => res.json())
        .then(data => {
            if(data.success) {
                document.getElementById('editId').value = data.user.id;
                document.getElementById('editFirstName').value = data.user.first_name;
                document.getElementById('editLastName').value = data.user.last_name;
                document.getElementById('editEmail').value = data.user.email;
                document.getElementById('editContact').value = data.user.contact;
                document.getElementById('editAddress').value = data.user.address || '';
                document.getElementById('editStatus').value = data.user.status;
                document.getElementById('editModal').style.display = 'block';
            }
        });
}

function closeEditModal() {
    const modal = document.getElementById('editModal');
    if (modal) {
        modal.style.display = 'none';
    }
}

function closeEditCustomerModal() {
    closeEditModal();
}

// Delete Customer
function deleteCustomer(id) {
    if(!confirm('Are you sure you want to delete this customer? This action cannot be undone.')) return;
    
    const formData = new FormData();
    formData.append('action', 'delete');
    formData.append('id', id);
    
    fetch('../api/admin-users.php', {
        method: 'POST',
        body: formData
    })
    .then(res => res.json())
    .then(data => {
        if(data.success) {
            alert(data.message || 'Customer deleted successfully!');
            location.reload();
        } else {
            alert('Error: ' + data.message);
        }
    });
}
