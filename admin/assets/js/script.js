"use strict";

function addServiceRequest(data) {
    const tbody = document.querySelector('.table-design table tbody');
    const tr = document.createElement('tr');

    tr.innerHTML = `
        <td>#${data.requestId}</td>
        <td>${data.customerName}</td>
        <td>${data.serviceType}</td>
        <td>${data.date}</td>
        <td>
            <span class="status-badge status-${data.status.toLowerCase()}">
                <ion-icon name="${getStatusIcon(data.status)}"></ion-icon>
                ${data.status}
            </span>
        </td>
        <td>
            <div class="action-buttons">
                <button class="btn-action view" title="View Details" onclick="viewRequest('${data.requestId}')">
                    <ion-icon name="eye-outline"></ion-icon>
                </button>
                <button class="btn-action delete" title="Delete" onclick="deleteRequest('${data.requestId}')">
                    <ion-icon name="trash-outline"></ion-icon>
                </button>
            </div>
        </td>
    `;

    tbody.appendChild(tr);
}

// Function to get status icon
function getStatusIcon(status) {
    const icons = {
        'Pending': 'time-outline',
        'Confirmed': 'checkmark-circle-outline',
        'Completed': 'checkbox-outline',
        'Cancelled': 'close-circle-outline'
    };
    return icons[status] || 'help-outline';
}

// Function to add new activity log
function addActivityLog(time, date, message) {
    const logEntries = document.querySelector('.log-entries');
    const logEntry = document.createElement('div');
    logEntry.className = 'log-entry';

    logEntry.innerHTML = `
        <div class="log-time">
            <span>${time}</span>
            <span class="date">${date}</span>
        </div>
        <div class="log-message">${message}</div>
    `;

    logEntries.insertBefore(logEntry, logEntries.firstChild);

    // Keep only last 20 entries
    const entries = logEntries.querySelectorAll('.log-entry');
    if (entries.length > 20) {
        logEntries.removeChild(entries[entries.length - 1]);
    }
}

// Example functions for handling actions
function viewRequest(requestId) {
    console.log(`Viewing request ${requestId}`);
    // Implement view logic
}

function deleteRequest(requestId) {
    if (confirm(`Are you sure you want to delete request ${requestId}?`)) {
        console.log(`Deleting request ${requestId}`);
        // Implement delete logic
    }
}

// Event listener for view more button
document.querySelector('.view-more-btn').addEventListener('click', () => {
    window.location.href = '/activity-logs';
});


// Main Script
document.querySelectorAll(".navList").forEach(function (element) {
    element.addEventListener('click', function () {
        // Remove active class from all nav items
        document.querySelectorAll(".navList").forEach(function (e) {
            e.classList.remove('active');
        });
        this.classList.add('active');
        const href = this.querySelector('a').getAttribute('href');
        const dashboardSection = document.querySelectorAll(".data-table");

        if (href === '#dashboard') {
            dashboardSection.forEach(table => {
                table.style.display = 'block';
            });
        } else {
            dashboardSection.forEach(table => {
                table.style.display = 'none';
            });
        }

        const requestSection = document.querySelector('.request-data-table');
        if (href === '#requests') {
            requestSection.style.display = 'block';
        } else {
            requestSection.style.display = 'none';
        }

        const menuSection = document.querySelector('.menu-dashboard');
        if (href === '#menu') {
            menuSection.style.display = 'block';
        } else {
            menuSection.style.display = 'none';
        }

        const tableSection = document.querySelector('.table-management-container');
        if (href === '#tables') {
            tableSection.style.display = 'block';
        } else {
            tableSection.style.display = 'none';
        }

        const roomSection = document.querySelector('.room-section');
        if (href === '#rooms') {
            roomSection.style.display = 'block';
        } else {
            roomSection.style.display = 'none';
        }

        const staffSection = document.querySelector('.staff-container');
        if (href === '#staff') {
            staffSection.style.display = 'block';
        } else {
            staffSection.style.display = 'none';
        }

        const blogsSection = document.querySelector('.blogs-data-table');
        if (href === '#blogs') {
            blogsSection.style.display = 'block';
        } else {
            blogsSection.style.display = 'none';
        }

        const customerSection = document.querySelector('.customers-container');
        if (href === '#customers') {
            customerSection.style.display = 'block';
        } else {
            customerSection.style.display = 'none';
        }

        const reviewSection = document.querySelector('.reviews-container');
        if (href === '#reviews') {
            reviewSection.style.display = 'block';
        } else {
            reviewSection.style.display = 'none';
        }

        const contactSection = document.querySelector('.contacts-data-table');
        if (href === '#contacts') {
            contactSection.style.display = 'block';
        } else {
            contactSection.style.display = 'none';
        }
    });
});


/* ------------------------------------------------------------------------------------- */
// Request section JS
document.addEventListener('DOMContentLoaded', () => {
    const tabs = document.querySelectorAll('.service-tab');
    const tables = document.querySelectorAll('.service-table');
    const searchInput = document.querySelector('.service-search-input');
    const searchButton = document.querySelector('.service-search-button');
    const updateContainer = document.querySelector('.service-update-container');

    // Tab switching
    tabs.forEach(tab => {
        tab.addEventListener('click', () => {
            tabs.forEach(t => t.classList.remove('active'));
            tables.forEach(table => table.classList.remove('active'));

            tab.classList.add('active');
            const tableId = tab.getAttribute('data-tab') + '-table';
            document.getElementById(tableId).classList.add('active');
        });
    });

    // Search functionality
    searchButton.addEventListener('click', () => {
        const searchTerm = searchInput.value.toLowerCase();
        const activeTable = document.querySelector('.service-table.active');
        const rows = activeTable.querySelectorAll('tbody tr');

        rows.forEach(row => {
            const matchFound = Array.from(row.cells).some(cell =>
                cell.textContent.toLowerCase().includes(searchTerm)
            );
            row.style.display = matchFound ? '' : 'none';
        });

        // Show update container if search is successful
        updateContainer.style.display = 'block';
    });
});




/* -----------------------------------MENU SECTION ------------------------------- */
let currentMenuType = '';
let currentEditingItem = null;

// Modal and Form Elements
const menuModal = document.getElementById('menu-modal');
const confirmModal = document.getElementById('confirm-modal');
const modalTitle = document.getElementById('modal-title');
const menuItemForm = document.getElementById('menu-item-form');
const specialDayGroup = document.getElementById('special-day-group');
const deleteButton = document.getElementById('delete-button');

// Form Input Elements
const foodNameInput = document.getElementById('food-name');
const priceInput = document.getElementById('price');
const discountPriceInput = document.getElementById('discount-price');
const imageUploadInput = document.getElementById('image-upload');
const availableDaySelect = document.getElementById('available-day');

// Function to open Add/Edit Modal
function openAddModal(menuType) {
    currentMenuType = menuType;
    modalTitle.textContent = `Add ${menuType.replace('-', ' ').replace(/\b\w/g, l => l.toUpperCase())} Item`;

    // Reset form
    menuItemForm.reset();
    deleteButton.style.display = 'none';

    // Show/hide special day dropdown
    specialDayGroup.style.display = menuType === 'special' ? 'block' : 'none';

    // Reset current editing item
    currentEditingItem = null;

    // Open modal
    menuModal.style.display = 'block';
}

// Function to open Edit Modal
function openEditModal(menuType, item) {
    currentMenuType = menuType;
    modalTitle.textContent = `Edit ${menuType.replace('-', ' ').replace(/\b\w/g, l => l.toUpperCase())} Item`;

    // Populate form with item details
    foodNameInput.value = item.foodName;
    priceInput.value = item.price;
    discountPriceInput.value = item.discountPrice;

    // Show delete button
    deleteButton.style.display = 'block';

    // Show/hide special day dropdown
    specialDayGroup.style.display = menuType === 'special' ? 'block' : 'none';
    if (menuType === 'special') {
        availableDaySelect.value = item.availableDay;
    }

    // Store current editing item
    currentEditingItem = item;

    // Open modal
    menuModal.style.display = 'block';
}

// Function to save menu item
function saveMenuItem() {
    // Validate form
    if (!validateForm()) return;

    const newItem = {
        foodName: foodNameInput.value,
        price: priceInput.value,
        discountPrice: discountPriceInput.value,
        availableDay: currentMenuType === 'special' ? availableDaySelect.value : null
    };

    // Handle image upload
    const imageFile = imageUploadInput.files[0];
    if (imageFile) {
        newItem.image = URL.createObjectURL(imageFile);
    }

    // Determine action: add or update
    if (currentEditingItem) {
        // Update existing item (in a real app, this would be an API call)
        updateMenuItem(newItem);
    } else {
        // Add new item (in a real app, this would be an API call)
        addNewMenuItem(newItem);
    }

    // Close modal
    closeModal();
}

// Function to add new menu item
function addNewMenuItem(item) {
    const tableBody = document.querySelector(`#${currentMenuType}-menu tbody`);
    const newRow = tableBody.insertRow();

    newRow.innerHTML = `
        <td>${tableBody.rows.length}</td>
        <td><img src="${item.image || '/api/placeholder/80/80'}" alt="${item.foodName}"></td>
        <td>${item.foodName}</td>
        <td>₹${item.price}</td>
        <td>₹${item.discountPrice}</td>
        ${currentMenuType === 'special' ? `<td>${item.availableDay}</td>` : ''}
        <td>
            <div class="menu-action-icons">
                <i class="fas fa-edit edit" onclick="openEditModal('${currentMenuType}', ${JSON.stringify(item)})"></i>
                <i class="fas fa-trash-alt delete" onclick="openDeleteConfirm(${JSON.stringify(item)})"></i>
            </div>
        </td>
    `;
}

// Function to update existing menu item
function updateMenuItem(item) {
    // In a real app, this would be an API call to update the item
    // For this example, we'll just update the table row
    const rows = document.querySelectorAll(`#${currentMenuType}-menu tbody tr`);
    const rowToUpdate = Array.from(rows).find(row =>
        row.cells[2].textContent === currentEditingItem.foodName
    );

    if (rowToUpdate) {
        rowToUpdate.cells[2].textContent = item.foodName;
        rowToUpdate.cells[3].textContent = `₹${item.price}`;
        rowToUpdate.cells[4].textContent = `₹${item.discountPrice}`;

        if (currentMenuType === 'special') {
            rowToUpdate.cells[5].textContent = item.availableDay;
        }

        // Update edit icon's onclick to pass new item details
        const editIcon = rowToUpdate.querySelector('.fa-edit');
        editIcon.setAttribute('onclick', `openEditModal('${currentMenuType}', ${JSON.stringify(item)})`);
    }
}

// Function to open delete confirmation
function openDeleteConfirm(item) {
    currentEditingItem = item;
    document.getElementById('confirm-message').textContent = `Are you sure you want to delete ${item.foodName}?`;
    confirmModal.style.display = 'block';
}

// Function to confirm delete action
function confirmAction() {
    if (currentEditingItem) {
        deleteMenuItem();
    }
    closeConfirmModal();
}

// Function to delete menu item
function deleteMenuItem() {
    const rows = document.querySelectorAll(`#${currentMenuType}-menu tbody tr`);
    const rowToDelete = Array.from(rows).find(row =>
        row.cells[2].textContent === currentEditingItem.foodName
    );

    if (rowToDelete) {
        rowToDelete.remove();
    }

    // Close modals
    closeModal();
}

// Function to validate form
function validateForm() {
    if (!foodNameInput.value) {
        alert('Please enter a food name');
        return false;
    }

    if (!priceInput.value) {
        alert('Please enter a price');
        return false;
    }

    if (currentMenuType === 'special' && !availableDaySelect.value) {
        alert('Please select an available day');
        return false;
    }

    return true;
}

// Function to clear form data
function clearForm() {
    menuItemForm.reset();
    currentEditingItem = null;
}

// Function to close modal
function closeModal() {
    menuModal.style.display = 'none';
    currentMenuType = '';
    currentEditingItem = null;
}

// Function to close confirmation modal
function closeConfirmModal() {
    confirmModal.style.display = 'none';
    currentEditingItem = null;
}

// Event Listeners
document.querySelector('.menu-modal-close').addEventListener('click', closeModal);

/* ------------------------------------------------------Table Management ------------------------- */
document.addEventListener('DOMContentLoaded', () => {
    const tableListBody = document.getElementById('table-list-body');
    const tableModal = document.getElementById('table-modal');
    const tableModalClose = document.querySelector('.table-modal-close');
    const tableModalCancel = document.getElementById('table-modal-cancel');
    const tableAddNewBtn = document.getElementById('table-add-new-btn');
    const tableForm = document.getElementById('table-form');
    const tableModalTitle = document.getElementById('table-modal-title');
    const tableEditId = document.getElementById('table-edit-id');

    let tables = JSON.parse(localStorage.getItem('restaurantTables')) || [];

    function renderTables() {
        tableListBody.innerHTML = '';
        tables.forEach((table, index) => {
            const row = document.createElement('tr');
            row.innerHTML = `
                <td>${index + 1}</td>
                <td><img src="${table.image || ''}" class="table-thumbnail" alt="Table ${table.number}" /></td>
                <td>${table.number}</td>
                <td>${table.capacity}</td>
                <td>${table.status}</td>
                <td>$${table.priceStandard}</td>
                <td>$${table.priceToday || table.priceStandard}</td>
                <td>${table.location}</td>
                <td>${table.lastUpdated}</td>
                <td class="table-list-actions">
                    <ion-icon name="create-outline" class="table-edit" data-id="${index}"></ion-icon>
                    <ion-icon name="trash-outline" class="table-delete" data-id="${index}"></ion-icon>
                </td>
            `;
            tableListBody.appendChild(row);
        });
        attachTableActionListeners();
    }

    function attachTableActionListeners() {
        document.querySelectorAll('.table-edit').forEach(editBtn => {
            editBtn.addEventListener('click', () => openEditModal(editBtn.dataset.id));
        });

        document.querySelectorAll('.table-delete').forEach(deleteBtn => {
            deleteBtn.addEventListener('click', () => deleteTable(deleteBtn.dataset.id));
        });
    }

    function openModal() {
        tableModal.style.display = 'block';
    }

    function closeModal() {
        tableModal.style.display = 'none';
        tableForm.reset();
    }

    function openEditModal(index) {
        const table = tables[index];
        tableModalTitle.textContent = 'Edit Table';
        tableEditId.value = index;

        document.getElementById('table-number').value = table.number;
        document.getElementById('table-capacity').value = table.capacity;
        document.getElementById('table-status').value = table.status;
        document.getElementById('table-price-standard').value = table.priceStandard;
        document.getElementById('table-price-today').value = table.priceToday;
        document.getElementById('table-location').value = table.location;

        openModal();
    }

    function saveTable(e) {
        e.preventDefault();
        const editIndex = tableEditId.value;
        const newTable = {
            number: document.getElementById('table-number').value,
            capacity: document.getElementById('table-capacity').value,
            status: document.getElementById('table-status').value,
            priceStandard: document.getElementById('table-price-standard').value,
            priceToday: document.getElementById('table-price-today').value,
            location: document.getElementById('table-location').value,
            lastUpdated: new Date().toLocaleString()
        };

        if (confirm('Are you sure you want to save this table?')) {
            if (editIndex === '') {
                tables.push(newTable);
            } else {
                tables[editIndex] = newTable;
            }
            localStorage.setItem('restaurantTables', JSON.stringify(tables));
            renderTables();
            closeModal();
        }
    }

    function deleteTable(index) {
        if (confirm('Are you sure you want to delete this table?')) {
            tables.splice(index, 1);
            localStorage.setItem('restaurantTables', JSON.stringify(tables));
            renderTables();
        }
    }

    tableAddNewBtn.addEventListener('click', () => {
        tableModalTitle.textContent = 'Add New Table';
        tableEditId.value = '';
        openModal();
    });

    tableModalClose.addEventListener('click', closeModal);
    tableModalCancel.addEventListener('click', closeModal);
    tableForm.addEventListener('submit', saveTable);

    renderTables();
});


/* -----------------------------------Rooms Section ------------------------------- */
function openModal(type, roomId = null) {
    const modal = document.getElementById('roomModal');
    const modalTitle = document.getElementById('modalTitle');
    const roomNumber = document.getElementById('roomNumber');

    modal.style.display = 'block';
    modalTitle.textContent = type === 'add' ? 'Add New Room' : 'Edit Room';

    if (type === 'edit') {
        // In a real application, you would fetch room data here
        // This is just for demonstration
        roomNumber.value = '101';
        roomNumber.disabled = true;
    } else {
        roomNumber.value = '';
        roomNumber.disabled = false;
    }
}

function closeModal() {
    document.getElementById('roomModal').style.display = 'none';
}

function handleSubmit(event) {
    event.preventDefault();

    if (confirm('Are you sure you want to save these changes?')) {
        // Here you would handle the form submission
        alert('Room details saved successfully!');
        closeModal();
    }
}

function deleteRoom(roomId) {
    if (confirm('Are you sure you want to delete this room?')) {
        // Here you would handle the deletion
        alert('Room deleted successfully!');
    }
}

// Close modal when clicking outside
window.onclick = function (event) {
    const modal = document.getElementById('roomModal');
    if (event.target === modal) {
        closeModal();
    }
}


/*-----------------------------------Staff section ----------------------------------------------*/
document.addEventListener('DOMContentLoaded', function () {
    const adminName = "Admin"; // Default admin name
    let staffData = JSON.parse(localStorage.getItem('staffData')) || [];
    let changesCount = JSON.parse(localStorage.getItem('changesCount')) || 0;

    function openStaffModal(isEdit = false, staffId = null) {
        const modal = document.getElementById('staffModal');
        const modalTitle = document.getElementById('modalTitle');
        const staffForm = document.getElementById('staffForm');

        if (isEdit && staffId !== null) {
            const staff = staffData.find(s => s.id === staffId);
            if (staff) {
                modalTitle.textContent = "Edit Staff";
                fillForm(staff);
                staffForm.dataset.editing = staffId;
            }
        } else {
            modalTitle.textContent = "Add New Staff";
            staffForm.reset();
            delete staffForm.dataset.editing;
        }
        modal.style.display = 'flex';
    }

    function closeStaffModal() {
        const modal = document.getElementById('staffModal');
        modal.style.display = 'none';
    }

    function fillForm(staff) {
        document.getElementById('fullName').value = staff.fullName;
        document.getElementById('role').value = staff.role;
        document.getElementById('shift').value = staff.shift;
        document.getElementById('contact').value = staff.contact;
        document.getElementById('email').value = staff.email;
        document.getElementById('joinedDate').value = staff.joinedDate;
        document.getElementById('contract').value = staff.contract;
        document.getElementById('salary').value = staff.salary;
        document.getElementById('status').checked = staff.status;
    }

    function handleStaffSubmit(event) {
        event.preventDefault();
        const staffForm = event.target;

        const fullName = document.getElementById('fullName').value.trim();
        if (fullName.length < 3) {
            alert("Full Name must be at least 3 characters long.");
            return;
        }

        const staff = {
            id: staffForm.dataset.editing ? parseInt(staffForm.dataset.editing) : Date.now(),
            fullName,
            role: document.getElementById('role').value,
            shift: document.getElementById('shift').value,
            contact: document.getElementById('contact').value,
            email: document.getElementById('email').value,
            joinedDate: document.getElementById('joinedDate').value,
            contract: document.getElementById('contract').value,
            salary: document.getElementById('salary').value,
            status: document.getElementById('status').checked,
            admin: adminName,
            timestamp: new Date().toISOString()
        };

        if (staffForm.dataset.editing) {
            if (confirm("Are you sure you want to update this staff member?")) {
                updateStaff(staff);
            }
        } else {
            if (confirm("Are you sure you want to add this new staff member?")) {
                addStaff(staff);
            }
        }

        closeStaffModal();
    }

    function addStaff(staff) {
        staffData.push(staff);
        changesCount++;
        saveData();
        renderStaffTable();
        alert("Staff member added successfully.");
    }

    function updateStaff(updatedStaff) {
        staffData = staffData.map(staff => staff.id === updatedStaff.id ? updatedStaff : staff);
        changesCount++;
        saveData();
        renderStaffTable();
        alert("Staff member updated successfully.");
    }

    function deleteStaff(staffId) {
        if (confirm("Are you sure you want to delete this staff member?")) {
            staffData = staffData.filter(staff => staff.id !== staffId);
            changesCount++;
            saveData();
            renderStaffTable();
            alert("Staff member deleted successfully.");
        }
    }

    function saveData() {
        localStorage.setItem('staffData', JSON.stringify(staffData));
        localStorage.setItem('changesCount', JSON.stringify(changesCount));
    }

    function renderStaffTable() {
        const staffTableBody = document.getElementById('staffTableBody');
        staffTableBody.innerHTML = staffData.map((staff, index) => `
            <tr>
                <td>${index + 1}</td>
                <td><img src="/api/placeholder/40/40" alt="Staff" class="staff-profile-img"></td>
                <td>${staff.fullName}</td>
                <td>${staff.role}</td>
                <td>${staff.contact}</td>
                <td>${staff.email}</td>
                <td>${staff.joinedDate}</td>
                <td>${staff.contract}</td>
                <td>${staff.shift}</td>
                <td>${staff.salary}</td>
                <td><span class="staff-status ${staff.status ? 'active' : 'inactive'}">${staff.status ? 'Active' : 'Inactive'}</span></td>
                <td class="staff-actions">
                    <button class="staff-action-btn edit" onclick="editStaff(${staff.id})">
                        <ion-icon name="create-outline"></ion-icon>
                    </button>
                    <button class="staff-action-btn delete" onclick="deleteStaff(${staff.id})">
                        <ion-icon name="trash-outline"></ion-icon>
                    </button>
                </td>
            </tr>
        `).join('');
    }

    window.openStaffModal = openStaffModal;
    window.closeStaffModal = closeStaffModal;
    window.editStaff = (staffId) => openStaffModal(true, staffId);
    window.deleteStaff = deleteStaff;

    document.getElementById('staffForm').addEventListener('submit', handleStaffSubmit);

    // Initial render
    renderStaffTable();
});
/* ----------------------------- Blogs Page -------------------------------------- */
function editBlog(blogId) {
    window.location.href = `blogs/blog.php?ID=${blogId}&mode=edit`;
}

function deleteBlog(blogId) {
    if (confirm('Are you sure you want to delete this blog post? This action cannot be undone.')) {
        // Add your AJAX call here to delete the blog
        console.log(`Deleting blog ${blogId}`);
    }
}

/* -------------------------- Customers Section ------------------------------*/
let currentPage = 1;
const customersPerPage = 10;

// Sample data - Replace with actual API calls
const customers = [
    {
        id: 1,
        name: "John Doe",
        email: "john@example.com",
        contact: "+1234567890",
        status: "active",
        registered: "2024-01-15",
        lastLogin: "2024-02-06",
        orders: 5,
        image: "/api/placeholder/50/50"
    }
    // Add more sample data as needed
];

function renderCustomers() {
    const tbody = document.getElementById('customersTableBody');
    const startIndex = (currentPage - 1) * customersPerPage;
    const endIndex = startIndex + customersPerPage;

    customers.slice(startIndex, endIndex).forEach((customer, index) => {
        const tr = document.createElement('tr');
        tr.innerHTML = `
                    <td>${startIndex + index + 1}</td>
                    <td><img src="${customer.image}" alt="${customer.name}" class="customers-avatar"></td>
                    <td>${customer.name}</td>
                    <td>${customer.email}</td>
                    <td>${customer.contact || '-'}</td>
                    <td><span class="customers-status customers-status-${customer.status}">${customer.status}</span></td>
                    <td>${customer.registered}</td>
                    <td>${customer.lastLogin}</td>
                    <td>${customer.orders}</td>
                    <td class="customers-actions">
                        <button class="customers-action-btn" onclick="viewOrders(${customer.id})">
                            <ion-icon name="eye-outline"></ion-icon>
                        </button>
                        <button class="customers-action-btn" onclick="editCustomer(${customer.id})">
                            <ion-icon name="create-outline"></ion-icon>
                        </button>
                        <button class="customers-action-btn" onclick="deleteCustomer(${customer.id})">
                            <ion-icon name="trash-outline"></ion-icon>
                        </button>
                    </td>
                `;
        tbody.appendChild(tr);
    });
}

function loadMoreCustomers() {
    currentPage++;
    renderCustomers();
}

function editCustomer(id) {
    const modal = document.getElementById('editModal');
    modal.style.display = 'flex';
    // Populate form with customer data
    const customer = customers.find(c => c.id === id);
    if (customer) {
        document.getElementById('editName').value = customer.name;
        document.getElementById('editContact').value = customer.contact || '';
        document.getElementById('editStatus').value = customer.status;
        document.getElementById('editNotes').value = customer.notes || '';
    }
}

function closeModal() {
    document.getElementById('editModal').style.display = 'none';
}

function deleteCustomer(id) {
    if (confirm('Are you sure you want to delete this customer?')) {
        // Update customer status to deleted
        const customer = customers.find(c => c.id === id);
        if (customer) {
            customer.status = 'deleted';
            renderCustomers();
        }
    }
}

function viewOrders(id) {
    // Implement view orders functionality
    window.location.href = `/admin/customers/${id}/orders`;
}

// Initial render
renderCustomers();

// Close modal when clicking outside
window.onclick = function (event) {
    const modal = document.getElementById('editModal');
    if (event.target === modal) {
        closeModal();
    }
}

// Handle form submission
document.getElementById('editCustomerForm').onsubmit = function (e) {
    e.preventDefault();
    if (confirm('Save changes to customer details?')) {
        // Implement save functionality
        closeModal();
    }
}

/* ----------------------------- Reviews Page --------------------------------------------------- */
// Sample data - Replace with actual API calls
const reviews = [
    {
        id: 1,
        customerName: "John Doe",
        customerImage: "/api/placeholder/50/50",
        service: "Table Booking",
        date: "2024-02-07",
        rating: 4,
        description: "Great experience overall! The food was delicious and service was excellent.",
        images: ["/api/placeholder/150/150", "/api/placeholder/150/150"]
    }
    // Add more sample data as needed
];

function renderReviews() {
    const tbody = document.getElementById('reviewsTableBody');
    tbody.innerHTML = '';
    
    reviews.forEach((review, index) => {
        const tr = document.createElement('tr');
        tr.innerHTML = `
            <td>${index + 1}</td>
            <td><img src="${review.customerImage}" alt="${review.customerName}" class="reviews-avatar"></td>
            <td>${review.customerName}</td>
            <td>${review.service}</td>
            <td>${review.date}</td>
            <td>${'★'.repeat(review.rating)}${'☆'.repeat(5-review.rating)}</td>
            <td class="reviews-actions">
                <button class="reviews-action-btn" onclick="viewReview(${review.id})">
                    <ion-icon name="eye-outline"></ion-icon>
                </button>
                <button class="reviews-action-btn" onclick="respondToReview(${review.id})">
                    <ion-icon name="chatbubble-outline"></ion-icon>
                </button>
            </td>
        `;
        tbody.appendChild(tr);
    });
}

function viewReview(id) {
    const review = reviews.find(r => r.id === id);
    if (review) {
        document.getElementById('modalCustomerImage').src = review.customerImage;
        document.getElementById('modalCustomerName').textContent = review.customerName;
        document.getElementById('modalServiceInfo').textContent = `${review.service} - ${review.date}`;
        document.getElementById('modalRating').innerHTML = '★'.repeat(review.rating) + '☆'.repeat(5-review.rating);
        document.getElementById('modalReviewText').textContent = review.description;
        
        const imageGrid = document.getElementById('modalImages');
        imageGrid.innerHTML = review.images.map(img => 
            `<img src="${img}" alt="Review image">`
        ).join('');
        
        document.getElementById('viewModal').style.display = 'flex';
    }
}

function respondToReview(id) {
    const review = reviews.find(r => r.id === id);
    if (review) {
        document.getElementById('respondCustomerInfo').textContent = 
            `Responding to ${review.customerName}'s review of ${review.service}`;
        document.getElementById('respondModal').style.display = 'flex';
    }
}

function closeModal(modalId) {
    document.getElementById(modalId).style.display = 'none';
}

// Handle form submission
document.getElementById('responseForm').onsubmit = function(e) {
    e.preventDefault();
    // Implement response submission logic
    closeModal('respondModal');
}

// Close modals when clicking outside
window.onclick = function(event) {
    if (event.target.classList.contains('reviews-modal')) {
        event.target.style.display = 'none';
    }
}

// Initial render
renderReviews();



/* ----------------------------- Contact Request Page -------------------------------------- */

class ContactRequestsManager {
    constructor() {
        this.requests = [];
        this.initializeEventListeners();
    }

    initializeEventListeners() {
        // View Modal Close Button
        document.querySelector('#contacts-view-modal .contacts-close-btn')
            .addEventListener('click', () => this.closeModal('contacts-view-modal'));

        // Edit Modal Close Button
        document.querySelector('#contacts-edit-modal .contacts-close-btn')
            .addEventListener('click', () => this.closeModal('contacts-edit-modal'));

        // Respond Modal Close Button
        document.querySelector('#contacts-respond-modal .contacts-close-btn')
            .addEventListener('click', () => this.closeModal('contacts-respond-modal'));

        // Edit Form Submit
        document.getElementById('contacts-edit-form')
            .addEventListener('submit', (e) => this.handleEditSubmit(e));

        // Respond Form Submit
        document.getElementById('contacts-respond-form')
            .addEventListener('submit', (e) => this.handleResponseSubmit(e));

        // Cancel buttons
        document.querySelectorAll('.btn-cancel').forEach(btn => {
            btn.addEventListener('click', () => {
                const modalId = btn.closest('.contacts-modal').id;
                this.closeModal(modalId);
            });
        });
    }

    openModal(modalId) {
        const modal = document.getElementById(modalId);
        if (modal) {
            modal.style.display = 'block';
        }
    }

    closeModal(modalId) {
        const modal = document.getElementById(modalId);
        if (modal) {
            modal.style.display = 'none';
        }
    }

    loadRequests() {
        // Simulate loading requests from backend
        // In a real scenario, this would be an API call
        this.requests = [
            {
                id: 1,
                name: 'John Doe',
                email: 'john@example.com',
                subject: 'Booking Inquiry',
                message: 'I would like to book a room for next month.',
                sentDate: '2024-02-05',
                status: 'Incomplete'
            },
            {
                id: 2,
                name: 'Jane Smith',
                email: 'jane@example.com',
                subject: 'Restaurant Reservation',
                message: 'Can I make a reservation for 4 people on Saturday?',
                sentDate: '2024-02-06',
                status: 'Important'
            }
        ];

        this.renderTable();
    }

    renderTable() {
        const tableBody = document.getElementById('contacts-table-body');
        tableBody.innerHTML = '';

        this.requests.forEach((request, index) => {
            const row = document.createElement('tr');
            row.innerHTML = `
                <td>${index + 1}</td>
                <td>${request.name}</td>
                <td>${request.email}</td>
                <td>${request.subject}</td>
                <td>${request.sentDate}</td>
                <td>
                    <span class="status-badge status-${request.status.toLowerCase()}">
                        ${request.status}
                    </span>
                </td>
                <td class="action-icons">
                    <ion-icon name="eye-outline" onclick="contactManager.viewRequest(${request.id})" title="View Details"></ion-icon>
                    <ion-icon name="pencil-outline" onclick="contactManager.editRequest(${request.id})" title="Edit Status"></ion-icon>
                    <ion-icon name="return-up-forward-outline" onclick="contactManager.respondRequest(${request.id})" title="Respond"></ion-icon>
                </td>
            `;
            tableBody.appendChild(row);
        });
    }

    viewRequest(id) {
        const request = this.requests.find(r => r.id === id);
        if (request) {
            document.getElementById('view-name').textContent = request.name;
            document.getElementById('view-email').textContent = request.email;
            document.getElementById('view-subject').textContent = request.subject;
            document.getElementById('view-message').textContent = request.message;
            document.getElementById('view-date').textContent = request.sentDate;

            this.openModal('contacts-view-modal');
        }
    }

    editRequest(id) {
        const request = this.requests.find(r => r.id === id);
        if (request) {
            document.getElementById('edit-request-id').value = request.id;
            document.getElementById('edit-status').value = request.status;

            this.openModal('contacts-edit-modal');
        }
    }

    respondRequest(id) {
        const request = this.requests.find(r => r.id === id);
        if (request) {
            document.getElementById('respond-request-id').value = request.id;
            document.getElementById('respond-name').value = request.name;
            document.getElementById('respond-email').value = request.email;
            document.getElementById('respond-subject').value = request.subject;

            this.openModal('contacts-respond-modal');
        }
    }

    handleEditSubmit(e) {
        e.preventDefault();
        const id = parseInt(document.getElementById('edit-request-id').value);
        const newStatus = document.getElementById('edit-status').value;

        const requestIndex = this.requests.findIndex(r => r.id === id);
        if (requestIndex !== -1) {
            // Update request status
            this.requests[requestIndex].status = newStatus;
            
            // Re-render the table to reflect the changes
            this.renderTable();
            
            // Close the modal
            this.closeModal('contacts-edit-modal');

            // In a real-world scenario, you would send an API call to update the status
            this.sendStatusUpdateToServer(id, newStatus);
        }
    }

    handleResponseSubmit(e) {
        e.preventDefault();
        const id = parseInt(document.getElementById('respond-request-id').value);
        const responseMessage = document.getElementById('respond-message').value;
        const recipientEmail = document.getElementById('respond-email').value;
        const subject = document.getElementById('respond-subject').value;

        // In a real-world scenario, you would:
        // 1. Send the response via an API call
        // 2. Mark the request as responded
        this.sendResponseToServer(id, recipientEmail, subject, responseMessage);

        // Update request status to 'Responded'
        const requestIndex = this.requests.findIndex(r => r.id === id);
        if (requestIndex !== -1) {
            this.requests[requestIndex].status = 'Responded';
            this.renderTable();
        }

        // Clear response textarea
        document.getElementById('respond-message').value = '';

        // Close the modal
        this.closeModal('contacts-respond-modal');
    }

    sendStatusUpdateToServer(id, status) {
        // Placeholder for API call to update status
        console.log(`Updating request ${id} status to ${status}`);
        // In a real implementation, use fetch or axios to send to backend
        // fetch('/api/update-request-status', {
        //     method: 'POST',
        //     body: JSON.stringify({ id, status }),
        //     headers: { 'Content-Type': 'application/json' }
        // });
    }

    sendResponseToServer(id, email, subject, message) {
        // Placeholder for API call to send response
        console.log(`Sending response to request ${id}`, { email, subject, message });
        // In a real implementation, use fetch or axios to send to backend
        // fetch('/api/send-contact-response', {
        //     method: 'POST',
        //     body: JSON.stringify({ id, email, subject, message }),
        //     headers: { 'Content-Type': 'application/json' }
        // });
    }
}

// Initialize the contact manager when the page loads
const contactManager = new ContactRequestsManager();
document.addEventListener('DOMContentLoaded', () => {
    contactManager.loadRequests();
});


/* --------------------------------------- Admin Profile ----------------------- */
document.addEventListener('DOMContentLoaded', function() {
    // Password Toggle Functionality
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

    // Password Strength Indicator
    const newPasswordInput = document.getElementById('newPassword');
    const strengthIndicator = document.querySelector('.profile-password-strength');

    newPasswordInput.addEventListener('input', function() {
        const password = this.value;
        const strength = calculatePasswordStrength(password);
        updateStrengthIndicator(strength);
    });

    function calculatePasswordStrength(password) {
        let strength = 0;
        
        if (password.length >= 8) strength += 25;
        if (password.match(/[a-z]/)) strength += 25;
        if (password.match(/[A-Z]/)) strength += 25;
        if (password.match(/[0-9]/)) strength += 25;
        
        return strength;
    }

    function updateStrengthIndicator(strength) {
        let color;
        if (strength <= 25) color = '#ff4444';
        else if (strength <= 50) color = '#ffbb33';
        else if (strength <= 75) color = '#00C851';
        else color = '#007E33';

        strengthIndicator.style.width = strength + '%';
        strengthIndicator.style.backgroundColor = color;
    }

    // Password Change Form Submission
    const passwordForm = document.getElementById('passwordChangeForm');
    passwordForm.addEventListener('submit', function(e) {
        e.preventDefault();
        
        const currentPassword = document.getElementById('currentPassword').value;
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

        if (confirm('Are you sure you want to change your password?')) {
            // Here you would typically make an API call to update the password
            alert('Password updated successfully!');
            this.reset();
        }
    });

    // Profile Image Upload
    const imageEditButton = document.querySelector('.profile-image-edit');
    imageEditButton.addEventListener('click', function() {
        const input = document.createElement('input');
        input.type = 'file';
        input.accept = 'image/*';
        
        input.onchange = function(e) {
            const file = e.target.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    document.querySelector('.profile-image').src = e.target.result;
                }
                reader.readAsDataURL(file);
            }
        }
        
        input.click();
    });

    // Add animation classes on load
    document.querySelectorAll('.profile-detail-item').forEach((item, index) => {
        item.style.animation = `fadeInUp 0.3s ease forwards ${index * 0.1}s`;
    });
});