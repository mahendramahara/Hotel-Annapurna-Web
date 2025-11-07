<?php require_once('includes/header.php') ?>

<div class="menu-container">
    <header class="menu-header">
        <h1>Hotel Annapurna</h1>
        <p>Discover our curated selection of delightful dishes</p>
    </header>

    <!-- Veg Items Section -->
    <div class="menu-section">
        <h2 class="menu-section-title">Vegetarian Delights</h2>
        <table class="menu-table">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Select</th>
                    <th>Image</th>
                    <th>Food Name</th>
                    <th>Price</th>
                    <th>Special Price</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>1</td>
                    <td><input type="checkbox" class="menu-checkbox" data-id="1" data-name="Paneer Butter Masala" data-price="12.99" data-discount="10.99"></td>
                    <td><img src="/api/placeholder/100/100" alt="Paneer Butter Masala"></td>
                    <td class="menu-item-name">Paneer Butter Masala</td>
                    <td class="price-column">$12.99</td>
                    <td class="discount-column">$10.99</td>
                </tr>
            </tbody>
        </table>
    </div>

    <!-- Non-Veg Items Section -->
    <div class="menu-section">
        <h2 class="menu-section-title">Non-Vegetarian Specialties</h2>
        <table class="menu-table">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Select</th>
                    <th>Image</th>
                    <th>Food Name</th>
                    <th>Price</th>
                    <th>Special Price</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>1</td>
                    <td><input type="checkbox" class="menu-checkbox" data-id="2" data-name="Chicken Biryani" data-price="14.99" data-discount="12.99"></td>
                    <td><img src="/api/placeholder/100/100" alt="Chicken Biryani"></td>
                    <td class="menu-item-name">Chicken Biryani</td>
                    <td class="price-column">$14.99</td>
                    <td class="discount-column">$12.99</td>
                </tr>
            </tbody>
        </table>
    </div>

    <!-- Special Items Section -->
    <div class="menu-section todays-special">
        <h2 class="menu-section-title">Chef's Special</h2>
        <table class="menu-table">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Select</th>
                    <th>Image</th>
                    <th>Food Name</th>
                    <th>Price</th>
                    <th>Special Price</th>
                    <th>Available On</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>1</td>
                    <td><input type="checkbox" class="menu-checkbox" data-id="3" data-name="Weekend Thali" data-price="19.99" data-discount="16.99"></td>
                    <td><img src="/api/placeholder/100/100" alt="Weekend Thali"></td>
                    <td class="menu-item-name">Weekend Thali</td>
                    <td class="price-column">$19.99</td>
                    <td class="discount-column">$16.99</td>
                    <td>Saturday</td>
                </tr>
                <tr>
                    <td>2</td>
                    <td><input type="checkbox" class="menu-checkbox" data-id="4" data-name="Midweek Platter" data-price="17.99" data-discount="15.49"></td>
                    <td><img src="/api/placeholder/100/100" alt="Midweek Platter"></td>
                    <td class="menu-item-name">Midweek Platter</td>
                    <td class="price-column">$17.99</td>
                    <td class="discount-column">$15.49</td>
                    <td>Wednesday</td>
                </tr>
            </tbody>
        </table>
    </div>

    <div class="selected-items-container">
        <div class="selected-items-header">
            <h2>Selected Items</h2>
        </div>
        <table class="menu-table" id="menu-selected-table">
            <thead>
                <tr>
                    <th>Item</th>
                    <th>Price</th>
                    <th>Special Price</th>
                    <th>You Save</th>
                </tr>
            </thead>
            <tbody>
                <!-- Selected items will appear here -->
            </tbody>
            <tfoot>
                <tr class="menu-total-row">
                    <td>Total</td>
                    <td id="menu-total-price">$0.00</td>
                    <td id="menu-total-discount">$0.00</td>
                    <td id="menu-total-savings">$0.00</td>
                </tr>
            </tfoot>
        </table>
        <div class="menu-buttons">
            <button class="menu-button menu-button-cart" id="menu-add-cart">Add to Cart</button>
            <button class="menu-button menu-button-buy" id="menu-buy-now">Buy Now</button>
        </div>
    </div>
</div>
<script>
    const menuCheckboxes = document.querySelectorAll('.menu-checkbox');
const menuSelectedTable = document.querySelector('#menu-selected-table tbody');
const menuTotalPrice = document.getElementById('menu-total-price');
const menuTotalDiscount = document.getElementById('menu-total-discount');
const menuTotalSavings = document.getElementById('menu-total-savings');
let menuSelectedItems = [];

function updateMenuTotals() {
    const totals = menuSelectedItems.reduce((acc, item) => ({
        price: acc.price + parseFloat(item.price),
        discount: acc.discount + parseFloat(item.discount)
    }), { price: 0, discount: 0 });

    const savings = totals.price - totals.discount;

    menuTotalPrice.textContent = `$${totals.price.toFixed(2)}`;
    menuTotalDiscount.textContent = `$${totals.discount.toFixed(2)}`;
    menuTotalSavings.textContent = `$${savings.toFixed(2)}`;
}

function updateMenuSelectedTable() {
    menuSelectedTable.innerHTML = '';
    menuSelectedItems.forEach(item => {
        const savings = (parseFloat(item.price) - parseFloat(item.discount)).toFixed(2);
        const row = document.createElement('tr');
        row.innerHTML = `
            <td class="menu-item-name">${item.name}</td>
            <td class="price-column">$${item.price}</td>
            <td class="discount-column">$${item.discount}</td>
            <td class="menu-price">$${savings}</td>
        `;
        menuSelectedTable.appendChild(row);
    });
    updateMenuTotals();
}

menuCheckboxes.forEach(checkbox => {
    checkbox.addEventListener('change', (e) => {
        const checkbox = e.target;
        const itemData = {
            id: checkbox.dataset.id,
            name: checkbox.dataset.name,
            price: checkbox.dataset.price,
            discount: checkbox.dataset.discount
        };

        if (checkbox.checked) {
            menuSelectedItems.push(itemData);
        } else {
            menuSelectedItems = menuSelectedItems.filter(item => item.id !== itemData.id);
        }

        updateMenuSelectedTable();
    });
});

document.getElementById('menu-add-cart').addEventListener('click', () => {
    if (menuSelectedItems.length === 0) {
        alert('Please select items to add to cart!');
        return;
    }
    console.log('Cart Items:', menuSelectedItems);
    alert('Items added to cart successfully!');
});

document.getElementById('menu-buy-now').addEventListener('click', () => {
    if (menuSelectedItems.length === 0) {
        alert('Please select items to purchase!');
        return;
    }
    console.log('Purchase Items:', menuSelectedItems);
    alert('Proceeding to checkout!');
});
</script>
<?php require_once('includes/footer.php') ?>