<?php 
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$isLoggedIn = isset($_SESSION['logged_in']) && $_SESSION['logged_in'];

require_once('includes/header.php'); 
?>

<link rel="stylesheet" href="assets/css/cart.css">
<script type="module" src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.esm.js"></script>
<script nomodule src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.js"></script>
    <div class="cart-hero">
        <div class="cart-hero-content">
            <h1>🛒 Your Shopping Cart</h1>
            <p>Review your items and proceed to checkout</p>
        </div>
    </div>

    <div class="cart-container">
        <div class="cart-sections-wrapper">
            <div class="cart-items-section">
                <div class="cart-section-header">
                    <h2>
                        <span>🍽️ Your Items</span>
                        <span class="cart-item-count" id="totalItemsCount">0</span>
                    </h2>
                </div>
                <div id="cartItemsContainer" class="cart-items-list">
                    <div class="cart-empty-state">
                        <div class="cart-empty-icon">🛍️</div>
                        <p class="cart-empty-text">Your cart is empty</p>
                        <a href="menu.php" class="cart-empty-btn">Continue Shopping</a>
                    </div>
                </div>
            </div>

            <div class="cart-summary">
                <h3 class="cart-summary-title">Order Summary</h3>

                <div class="cart-summary-row subtotal">
                    <span class="cart-summary-label">Subtotal</span>
                    <span class="cart-summary-value" id="subtotalValue">Rs. 0.00</span>
                </div>

                <div class="cart-summary-row discount">
                    <span class="cart-summary-label">Discount</span>
                    <span class="cart-summary-value" id="discountValue">-Rs. 0.00</span>
                </div>

                <div class="cart-summary-row total">
                    <span class="cart-summary-label">Total</span>
                    <span class="cart-summary-value" id="totalValue">Rs. 0.00</span>
                </div>

                <div class="cart-coupon-section">
                    <input type="text" id="couponInput" class="cart-coupon-input" placeholder="Enter coupon code">
                    <button class="cart-coupon-btn" onclick="applyCoupon()">Apply</button>
                </div>
                <div id="couponMessage"></div>

                <div class="cart-action-buttons">
                    <button class="cart-btn cart-btn-primary" onclick="proceedCheckout()">Proceed to Checkout</button>
                    <a href="menu.php" class="cart-btn cart-btn-secondary" style="text-align:center; text-decoration:none;">Continue Shopping</a>
                </div>
            </div>
        </div>
    </div>

    <script>
        const STORAGE_KEY = 'hotelCart';
        const IS_LOGGED_IN = <?php echo $isLoggedIn ? 'true' : 'false'; ?>;

        class CartManager {
            constructor() {
                this.cart = { foods: [], rooms: [], tables: [] };
                this.currentDiscount = 0;
                this.appliedCoupon = null;
                this.init();
            }

            async init() {
                if (IS_LOGGED_IN) {
                    await this.syncLocalStorageToDatabase();
                    await this.loadFromDatabase();
                } else {
                    this.loadFromLocalStorage();
                }
            }

            loadFromLocalStorage() {
                const stored = localStorage.getItem(STORAGE_KEY);
                const parsedCart = stored ? JSON.parse(stored) : { food: [], rooms: [], tables: [] };
                // Normalize cart structure for backward compatibility
                this.cart = {
                    food: parsedCart.food || parsedCart.foods || [],
                    rooms: parsedCart.rooms || [],
                    tables: parsedCart.tables || []
                };
                this.render();
                this.updateSummary();
            }

            async syncLocalStorageToDatabase() {
                const stored = localStorage.getItem(STORAGE_KEY);
                if (!stored) return;
                
                try {
                    const localCart = JSON.parse(stored);
                    const hasItems = (localCart.food?.length || localCart.foods?.length) || localCart.rooms?.length || localCart.tables?.length;
                    
                    if (!hasItems) {
                        localStorage.removeItem(STORAGE_KEY);
                        return;
                    }

                    console.log('Syncing cart to database:', localCart);

                    const response = await fetch('api/cart-handler.php?action=sync', {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/json' },
                        body: JSON.stringify(localCart)
                    });
                    const data = await response.json();
                    
                    console.log('Sync response:', data);
                    
                    if (data.success) {
                        localStorage.removeItem(STORAGE_KEY);
                        console.log('✓ Cart synced to database successfully');
                    } else {
                        console.error('Sync failed:', data.message);
                    }
                } catch (error) {
                    console.error('Failed to sync cart:', error);
                }
            }

            async loadFromDatabase() {
                try {
                    const response = await fetch('api/cart-handler.php?action=get');
                    const data = await response.json();
                    
                    if (data.success) {
                        this.cart = data.cart;
                        this.render();
                        this.updateSummary();
                    }
                } catch (error) {
                    console.error('Failed to load cart from database:', error);
                    this.loadFromLocalStorage();
                }
            }

            saveCart() {
                if (!IS_LOGGED_IN) {
                    localStorage.setItem(STORAGE_KEY, JSON.stringify(this.cart));
                }
                this.render();
                this.updateSummary();
            }

            addFood(food) {
                const existing = this.cart.food.find(f => f.id === food.id);
                if (existing) existing.quantity += food.quantity || 1;
                else this.cart.food.push({ ...food, quantity: food.quantity || 1, type: 'food' });
                this.saveCart();
            }

            addRoom(room) {
                const existing = this.cart.rooms.find(r => r.id === room.id && r.checkIn === room.checkIn);
                if (existing) existing.nights += room.nights || 1;
                else this.cart.rooms.push({ ...room, nights: room.nights || 1, type: 'room' });
                this.saveCart();
            }

            addTable(table) {
                const existing = this.cart.tables.find(t => t.id === table.id && t.date === table.date);
                if (existing) existing.quantity += table.quantity || 1;
                else this.cart.tables.push({ ...table, quantity: table.quantity || 1, type: 'table' });
                this.saveCart();
            }

            async removeItem(type, id, extra = null) {
                if (IS_LOGGED_IN) {
                    let cartId = null;
                    
                    if (type === 'food') {
                        const item = this.cart.food.find(f => f.id === id);
                        cartId = item?.cart_id;
                    } else if (type === 'room') {
                        const item = this.cart.rooms.find(r => r.id === id && r.checkIn === extra);
                        cartId = item?.cart_id;
                    } else if (type === 'table') {
                        const item = this.cart.tables.find(t => t.id === id && t.date === extra);
                        cartId = item?.cart_id;
                    }
                    
                    console.log('Removing item:', { type, id, extra, cartId });
                    
                    if (cartId) {
                        try {
                            const formData = new FormData();
                            formData.append('action', 'remove');
                            formData.append('cart_id', cartId);
                            
                            const response = await fetch('api/cart-handler.php', {
                                method: 'POST',
                                body: formData
                            });
                            const data = await response.json();
                            
                            console.log('Remove response:', data);
                            
                            if (data.success) {
                                this.cart = data.cart;
                                this.saveCart();
                            } else {
                                alert('Failed to remove item: ' + data.message);
                            }
                        } catch (error) {
                            console.error('Failed to remove item:', error);
                            alert('Error removing item. Please try again.');
                        }
                    } else {
                        console.error('Cart ID not found for item');
                        alert('Cannot remove item: Cart ID not found');
                    }
                } else {
                    if (type === 'food') this.cart.food = this.cart.food.filter(f => f.id !== id);
                    else if (type === 'room') this.cart.rooms = this.cart.rooms.filter(r => !(r.id === id && r.checkIn === extra));
                    else if (type === 'table') this.cart.tables = this.cart.tables.filter(t => !(t.id === id && t.date === extra));
                    this.saveCart();
                }
            }

            async updateQuantity(type, id, quantity, extra = null) {
                quantity = Math.max(1, quantity);
                
                if (IS_LOGGED_IN) {
                    let cartId = null;
                    
                    if (type === 'food') {
                        const item = this.cart.food.find(f => f.id === id);
                        cartId = item?.cart_id;
                    } else if (type === 'room') {
                        const item = this.cart.rooms.find(r => r.id === id && r.checkIn === extra);
                        cartId = item?.cart_id;
                    } else if (type === 'table') {
                        const item = this.cart.tables.find(t => t.id === id && t.date === extra);
                        cartId = item?.cart_id;
                    }
                    
                    console.log('Updating quantity:', { type, id, quantity, cartId });
                    
                    if (cartId) {
                        try {
                            const formData = new FormData();
                            formData.append('action', 'update');
                            formData.append('cart_id', cartId);
                            formData.append('quantity', quantity);
                            
                            const response = await fetch('api/cart-handler.php', {
                                method: 'POST',
                                body: formData
                            });
                            const data = await response.json();
                            
                            console.log('Update response:', data);
                            
                            if (data.success) {
                                this.cart = data.cart;
                                this.saveCart();
                            } else {
                                alert('Failed to update quantity: ' + data.message);
                            }
                        } catch (error) {
                            console.error('Failed to update quantity:', error);
                        }
                    }
                } else {
                    if (type === 'food') {
                        const food = this.cart.food.find(f => f.id === id);
                        if (food) food.quantity = quantity;
                    } else if (type === 'room') {
                        const room = this.cart.rooms.find(r => r.id === id && r.checkIn === extra);
                        if (room) room.nights = quantity;
                    } else if (type === 'table') {
                        const table = this.cart.tables.find(t => t.id === id && t.date === extra);
                        if (table) table.quantity = quantity;
                    }
                    this.saveCart();
                }
            }

            calculateTotals() {
                let subtotal = 0;
                this.cart.food.forEach(food => subtotal += parseFloat(food.discount_price || food.price || 0) * (food.quantity || 1));
                this.cart.rooms.forEach(room => subtotal += parseFloat(room.price_today || room.price || 0) * (room.nights || 1));
                this.cart.tables.forEach(table => subtotal += parseFloat(table.price_today || table.price_main || 0) * (table.quantity || 1));
                const discount = subtotal * this.currentDiscount;
                const total = subtotal - discount;
                return { subtotal, discount, total };
            }

            render() {
                const container = document.getElementById('cartItemsContainer');
                const allItems = [
                    ...this.cart.food.map(f => ({ ...f, type: 'food' })),
                    ...this.cart.rooms.map(r => ({ ...r, type: 'room' })),
                    ...this.cart.tables.map(t => ({ ...t, type: 'table' }))
                ];

                if (allItems.length === 0) {
                    container.innerHTML = `
                        <div class="cart-empty-state">
                            <div class="cart-empty-icon">🛍️</div>
                            <p class="cart-empty-text">Your cart is empty</p>
                            <a href="menu.php" class="cart-empty-btn">Continue Shopping</a>
                        </div>`;
                    document.getElementById('totalItemsCount').textContent = 0;
                    return;
                }

                container.innerHTML = allItems.map(item => this.renderItem(item)).join('');
                document.getElementById('totalItemsCount').textContent = allItems.length;
            }

            renderItem(item) {
                const getPriceInfo = (original, current) => `
                    <div class="cart-item-price-info">
                        <span class="cart-price-original">Rs. ${original.toFixed(2)}</span>
                        <span class="cart-price-current">Rs. ${current.toFixed(2)}</span>
                    </div>`;

                if (item.type === 'food') {
                    const price = parseFloat(item.discount_price || item.price || 0);
                    const original = parseFloat(item.price || 0);
                    const itemName = item.food_name || item.name || 'Unknown Item';
                    const itemImage = item.image_url || item.image || 'images/menu/demoFood.jpg';
                    return `
                        <div class="cart-item">
                            <img src="${itemImage}" class="cart-item-image" alt="${itemName}">
                            <div class="cart-item-details">
                                <div class="cart-item-title">${itemName}</div>
                                <div class="cart-item-type">Food Item</div>
                                ${getPriceInfo(original, price)}
                            </div>
                            <div class="cart-item-controls">
                                <div class="cart-item-quantity">
                                    <button class="cart-qty-btn" onclick="cartManager.updateQuantity('food', ${item.id}, ${item.quantity - 1})">-</button>
                                    <input type="number" class="cart-qty-input" value="${item.quantity}" readonly>
                                    <button class="cart-qty-btn" onclick="cartManager.updateQuantity('food', ${item.id}, ${item.quantity + 1})">+</button>
                                </div>
                                <button class="cart-remove-btn" onclick="cartManager.removeItem('food', ${item.id})">
                                    <ion-icon name="trash"></ion-icon> Remove
                                </button>
                            </div>
                        </div>`;
                }

                if (item.type === 'room') {
                    const price = parseFloat(item.price_today || item.price || 0);
                    const original = parseFloat(item.price || 0);
                    const itemName = item.room_type || item.name || 'Unknown Room';
                    const itemImage = item.image_url || item.image || 'images/rooms/demoRoom.jpg';
                    const roomNo = item.room_no || 'N/A';
                    return `
                        <div class="cart-item">
                            <img src="${itemImage}" class="cart-item-image" alt="${itemName}">
                            <div class="cart-item-details">
                                <div class="cart-item-title">${itemName} - Room ${roomNo}</div>
                                <div class="cart-item-type">Hotel Room • ${item.nights} Night${item.nights > 1 ? 's' : ''}</div>
                                ${getPriceInfo(original, price)}
                            </div>
                            <div class="cart-item-controls">
                                <div class="cart-item-quantity">
                                    <button class="cart-qty-btn" onclick="cartManager.updateQuantity('room', ${item.id}, ${item.nights - 1}, '${item.checkIn}')">-</button>
                                    <input type="number" class="cart-qty-input" value="${item.nights}" readonly>
                                    <button class="cart-qty-btn" onclick="cartManager.updateQuantity('room', ${item.id}, ${item.nights + 1}, '${item.checkIn}')">+</button>
                                </div>
                                <button class="cart-remove-btn" onclick="cartManager.removeItem('room', ${item.id}, '${item.checkIn}')">
                                    <ion-icon name="trash"></ion-icon> Remove
                                </button>
                            </div>
                        </div>`;
                }

                if (item.type === 'table') {
                    const price = parseFloat(item.price_today || item.price_main || 0);
                    const original = parseFloat(item.price_main || 0);
                    const itemImage = item.image_url || item.image || 'images/tables/demoTable.jpg';
                    const tableNo = item.table_no || 'N/A';
                    const location = item.location || 'Unknown Location';
                    const chairs = item.total_chairs || 'N/A';
                    const date = item.date || '';
                    return `
                        <div class="cart-item">
                            <img src="${itemImage}" class="cart-item-image" alt="Table ${tableNo}">
                            <div class="cart-item-details">
                                <div class="cart-item-title">${location} - Table ${tableNo}</div>
                                <div class="cart-item-type">Dining Table • ${chairs} Seats</div>
                                ${getPriceInfo(original, price)}
                            </div>
                            <div class="cart-item-controls">
                                <div class="cart-item-quantity">
                                    <button class="cart-qty-btn" onclick="cartManager.updateQuantity('table', ${item.id}, ${item.quantity - 1}, '${date}')">-</button>
                                    <input type="number" class="cart-qty-input" value="${item.quantity}" readonly>
                                    <button class="cart-qty-btn" onclick="cartManager.updateQuantity('table', ${item.id}, ${item.quantity + 1}, '${date}')">+</button>
                                </div>
                                <button class="cart-remove-btn" onclick="cartManager.removeItem('table', ${item.id}, '${date}')">
                                    <ion-icon name="trash"></ion-icon> Remove
                                </button>
                            </div>
                        </div>`;
                }
            }

            updateSummary() {
                const { subtotal, discount, total } = this.calculateTotals();
                document.getElementById('subtotalValue').textContent = `Rs. ${subtotal.toFixed(2)}`;
                document.getElementById('discountValue').textContent = `-Rs. ${discount.toFixed(2)}`;
                document.getElementById('totalValue').textContent = `Rs. ${total.toFixed(2)}`;
            }
        }

        const cartManager = new CartManager();

        async function applyCoupon() {
            const input = document.getElementById('couponInput');
            const messageDiv = document.getElementById('couponMessage');
            const code = input.value.toUpperCase().trim();

            if (!code) {
                showCouponMessage('Please enter a coupon code', false);
                return;
            }

            const { subtotal } = cartManager.calculateTotals();

            try {
                const response = await fetch('api/validate-coupon.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ code, subtotal })
                });

                const result = await response.json();

                if (result.success) {
                    cartManager.appliedCoupon = result.coupon;
                    cartManager.currentDiscount = result.coupon.discount_amount / subtotal;
                    input.classList.remove('invalid');
                    input.classList.add('valid');
                    showCouponMessage(`✓ ${result.message} - You save Rs. ${result.coupon.discount_amount.toFixed(2)}`, true);
                    cartManager.updateSummary();
                } else {
                    cartManager.currentDiscount = 0;
                    cartManager.appliedCoupon = null;
                    input.classList.remove('valid');
                    input.classList.add('invalid');
                    showCouponMessage(`✗ ${result.message}`, false);
                    cartManager.updateSummary();
                }
            } catch (error) {
                console.error('Coupon validation error:', error);
                showCouponMessage('✗ Error validating coupon', false);
            }
        }

        function showCouponMessage(message, isSuccess) {
            const messageDiv = document.getElementById('couponMessage');
            messageDiv.textContent = message;
            messageDiv.className = `coupon-message ${isSuccess ? 'success' : 'error'}`;
            
            setTimeout(() => {
                if (!isSuccess) {
                    messageDiv.textContent = '';
                    messageDiv.className = '';
                }
            }, 3000);
        }

        function proceedCheckout() {
            const allItems = [...cartManager.cart.food, ...cartManager.cart.rooms, ...cartManager.cart.tables];
            if (allItems.length === 0) {
                alert('Your cart is empty!');
                return;
            }
            
            if (!IS_LOGGED_IN) {
                if (confirm('Please login to proceed with checkout. Would you like to login now?')) {
                    window.location.href = 'login.php';
                }
                return;
            }
            
            const { subtotal, discount, total } = cartManager.calculateTotals();
            const checkoutData = {
                cart: cartManager.cart,
                subtotal: subtotal.toFixed(2),
                discount: discount.toFixed(2),
                total: total.toFixed(2),
                coupon: cartManager.appliedCoupon
            };
            
            sessionStorage.setItem('checkoutData', JSON.stringify(checkoutData));
            window.location.href = 'payment.php?from=cart';
        }

        window.addEventListener('storage', () => {
            if (!IS_LOGGED_IN) {
                cartManager.loadFromLocalStorage();
            }
        });
    </script>

<?php require_once('includes/footer.php'); ?>
