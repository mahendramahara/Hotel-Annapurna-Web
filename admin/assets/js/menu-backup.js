class MenuManagement {
    constructor() {
        this.menuItemModal = document.getElementById('menu-item-modal');
        this.menuItemForm = document.getElementById('menu-item-form');
        this.closeModalBtn = document.querySelector('.close-modal');
        this.availableOnContainer = document.getElementById('available-on-container');
        this.deleteBtn = document.getElementById('btn-delete-item');
        this.clearBtn = document.getElementById('btn-clear-form');
        this.menuData = {
            vegetarian: [],
            'non-vegetarian': [],
            special: []
        };
        this.setupEventListeners();
        this.loadInitialData();
    }

    setupEventListeners() {
        // Add new item buttons
        document.querySelectorAll('.btn-add-item').forEach(btn => {
            btn.addEventListener('click', (e) => {
                e.preventDefault();
                this.openAddModal(e);
            });
        });

        // Close modal button
        this.closeModalBtn.addEventListener('click', () => this.closeModal());

        // Form submission
        this.menuItemForm.addEventListener('submit', (e) => this.saveMenuItem(e));

        // Clear form button
        this.clearBtn.addEventListener('click', () => this.clearForm());

        // Delete item button (only visible in edit mode)
        this.deleteBtn.addEventListener('click', () => this.deleteMenuItem());
    }

    loadInitialData() {
        // Simulated initial data - in a real app, this would come from a backend API
        this.menuData = {
            vegetarian: [
                {
                    id: 'veg1',
                    foodName: 'Vegetable Biryani',
                    price: 250,
                    discountPrice: 200,
                    image: 'path/to/biryani.jpg',
                    category: 'vegetarian'
                },
                {
                    id: 'veg2',
                    foodName: 'Paneer Tikka',
                    price: 300,
                    discountPrice: 250,
                    image: 'path/to/paneer.jpg',
                    category: 'vegetarian'
                }
            ],
            'non-vegetarian': [
                {
                    id: 'nveg1',
                    foodName: 'Chicken Curry',
                    price: 350,
                    discountPrice: 300,
                    image: 'path/to/chicken.jpg',
                    category: 'non-vegetarian'
                }
            ],
            special: [
                {
                    id: 'special1',
                    foodName: 'Chef\'s Special Thali',
                    price: 500,
                    discountPrice: 450,
                    availableOn: 'Sunday',
                    image: 'path/to/thali.jpg',
                    category: 'special'
                }
            ]
        };

        // Populate tables
        this.renderTables();
    }

    renderTables() {
        const categories = ['vegetarian', 'non-vegetarian', 'special'];

        categories.forEach(category => {
            const tableBody = document.getElementById(`${category}-menu-items`);
            tableBody.innerHTML = ''; // Clear existing rows

            this.menuData[category].forEach((item, index) => {
                const row = document.createElement('tr');
                row.innerHTML = `
                    <td>${index + 1}</td>
                    <td><img src="${item.image || 'path/to/placeholder.jpg'}" alt="${item.foodName}"></td>
                    <td>${item.foodName}</td>
                    <td>₹${item.price}</td>
                    <td>₹${item.discountPrice}</td>
                    ${category === 'special' ? `<td>${item.availableOn || 'N/A'}</td>` : ''}
                    <td>
                        <button class="btn-edit" data-id="${item.id}" data-category="${category}">
                            <ion-icon name="create-outline"></ion-icon>
                        </button>
                        <button class="btn-delete" data-id="${item.id}" data-category="${category}">
                            <ion-icon name="trash-outline"></ion-icon>
                        </button>
                    </td>
                `;
                tableBody.appendChild(row);
            });

            // Add event listeners for edit and delete buttons
            tableBody.querySelectorAll('.btn-edit').forEach(btn => {
                btn.addEventListener('click', (e) => this.handleEditItem(e));
            });

            tableBody.querySelectorAll('.btn-delete').forEach(btn => {
                btn.addEventListener('click', (e) => this.handleDeleteItem(e));
            });
        });
    }

    handleEditItem(e) {
        const id = e.currentTarget.dataset.id;
        const category = e.currentTarget.dataset.category;

        // Find the item in the corresponding category
        const item = this.menuData[category].find(item => item.id === id);

        if (item) {
            this.openEditModal({
                ...item,
                category: category
            });
        }
    }

    handleDeleteItem(e) {
        const id = e.currentTarget.dataset.id;
        const category = e.currentTarget.dataset.category;

        // Confirm deletion
        if (confirm('Are you sure you want to delete this item?')) {
            // Remove item from local data
            this.menuData[category] = this.menuData[category].filter(item => item.id !== id);

            // Re-render the table
            this.renderTables();
        }
    }

    clearForm() {
        this.menuItemForm.reset();
        document.getElementById('menu-item-id').value = '';
    }

    openAddModal(e) {
        const category = e.currentTarget.dataset.category;
        this.resetModal();

        document.getElementById('menu-item-category').value = category;
        document.getElementById('modal-title').textContent = `Add ${this.formatCategory(category)} Item`;

        // Show/hide available on dropdown for special items
        this.availableOnContainer.style.display =
            category === 'special' ? 'block' : 'none';

        // In add mode: show save and clear, hide delete
        this.deleteBtn.style.display = 'none';
        document.querySelector('.btn-save').style.display = 'block';
        this.clearBtn.style.display = 'block';

        this.menuItemModal.style.display = 'block';
    }

    openEditModal(item) {
        this.resetModal();

        // Populate form with item details
        document.getElementById('menu-item-id').value = item.id;
        document.getElementById('menu-item-category').value = item.category;
        document.getElementById('food-name').value = item.foodName;
        document.getElementById('price').value = item.price;
        document.getElementById('discount-price').value = item.discountPrice;

        document.getElementById('modal-title').textContent = `Edit ${this.formatCategory(item.category)} Item`;

        // Show/hide available on dropdown for special items
        this.availableOnContainer.style.display =
            item.category === 'special' ? 'block' : 'none';

        if (item.category === 'special') {
            document.getElementById('available-on').value = item.availableOn || '';
        }

        // In edit mode: show all buttons
        this.deleteBtn.style.display = 'block';
        document.querySelector('.btn-save').style.display = 'block';
        this.clearBtn.style.display = 'block';

        this.menuItemModal.style.display = 'block';
    }

    saveMenuItem(e) {
        e.preventDefault();

        const category = document.getElementById('menu-item-category').value;
        const id = document.getElementById('menu-item-id').value;
        const foodName = document.getElementById('food-name').value;
        const price = parseFloat(document.getElementById('price').value);
        const discountPrice = parseFloat(document.getElementById('discount-price').value);
        const availableOn = category === 'special'
            ? document.getElementById('available-on').value
            : null;

        // Validate required fields for special items
        if (category === 'special' && !availableOn) {
            alert('Please select available day for special items');
            return;
        }

        // Handle file upload (simplified)
        const imageFile = document.getElementById('food-image').files[0];
        const image = imageFile
            ? URL.createObjectURL(imageFile)
            : (id ? this.findItemById(id, category).image : 'path/to/placeholder.jpg');

        // Prepare menu item object
        const menuItem = {
            id: id || `${category}${this.menuData[category].length + 1}`,
            foodName,
            price,
            discountPrice,
            image,
            category
        };

        // Add special item specific property
        if (category === 'special') {
            menuItem.availableOn = availableOn;
        }

        // Update or add item
        if (id) {
            // Update existing item
            const index = this.menuData[category].findIndex(item => item.id === id);
            this.menuData[category][index] = menuItem;
            /* 
            if (index !== -1) {
                this.menuData[category][index] = menuItem;
                this.renderTables();
                this.closeModal();
            } */
        } else {
            // Add new item
            this.menuData[category].push(menuItem);
            this.renderTables();
            this.closeModal();
        }

        // Re-render tables and close modal
        this.renderTables();
        this.closeModal();
    }

    findItemById(id, category) {
        return this.menuData[category].find(item => item.id === id) || {};
    }

    deleteMenuItem() {
        const category = document.getElementById('menu-item-category').value;
        const id = document.getElementById('menu-item-id').value;

        if (!id) return;

        /* if (index !== -1) {
            this.menuData[category].splice(index, 1);
            this.renderTables();
            this.closeModal();
        } */
        // Confirm deletion
        if (confirm('Are you sure you want to delete this item?')) {
            // Remove item from local data
            this.menuData[category] = this.menuData[category].filter(item => item.id !== id);

            // Re-render the table
            this.renderTables();

            // Close modal
            this.closeModal();
        }
    }

    closeModal() {
        this.menuItemModal.style.display = 'none';
        this.menuItemForm.reset();
        //  this.clearForm();
    }

    resetModal() {
        // Reset all form fields
        document.getElementById('menu-item-id').value = '';
        document.getElementById('food-name').value = '';
        document.getElementById('price').value = '';
        document.getElementById('discount-price').value = '';
        document.getElementById('food-image').value = '';
        document.getElementById('available-on').value = '';
    }

    formatCategory(category) {
        return category.split('-').map(word =>
            word.charAt(0).toUpperCase() + word.slice(1)
        ).join(' ');
    }

    /* resetModal() {
        this.clearForm();
        this.deleteBtn.style.display = 'none';
        document.querySelector('.btn-save').style.display = 'block';
        this.clearBtn.style.display = 'block';
    }

    formatCategory(category) {
        return category.split('-').map(word => 
            word.charAt(0).toUpperCase() + word.slice(1)
        ).join(' ');
    }

    findItemById(id, category) {
        return this.menuData[category].find(item => item.id === id);
    } */
}

// Initialize the Menu Management system
window.menuManagement = null;
document.addEventListener('DOMContentLoaded', () => {
    window.menuManagement = new MenuManagement();
});