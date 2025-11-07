<?php require_once('includes/header.php'); ?>

<body class="checkout-body">
    <div class="checkout-hero">
        <div class="checkout-container">
            <h1>Checkout</h1>
            <p>Complete your order with our secure checkout process</p>
        </div>
    </div>

    <div class="checkout-container">
        <!-- Food Orders Table -->
        <div class="checkout-table-container">
            <div class="checkout-table-header">
                <h2>Food Orders</h2>
            </div>
            <table id="foodTable" class="checkout-table">
                <thead>
                    <tr>
                        <th>Image</th>
                        <th>Item</th>
                        <th>Quantity</th>
                        <th>Price/Item</th>
                        <th>Discounted Price/Item</th>
                        <th>Total Price</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <!-- Dynamic content -->
                </tbody>
            </table>
            <div class="checkout-padding">
                <button class="checkout-btn checkout-btn-primary" onclick="addFoodItem()">Add More Items</button>
            </div>
        </div>

        <!-- Restaurant Tables -->
        <div class="checkout-table-container">
            <div class="checkout-table-header">
                <h2>Reserved Tables</h2>
            </div>
            <table id="tableReservations" class="checkout-table">
                <thead>
                    <tr>
                        <th>Image</th>
                        <th>Table Number</th>
                        <th>Date</th>
                        <th>Time</th>
                        <th>Price</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <!-- Dynamic content -->
                </tbody>
            </table>
            <div class="checkout-padding">
                <button class="checkout-btn checkout-btn-primary" onclick="addTableReservation()">Add More Tables</button>
            </div>
        </div>

        <!-- Hotel Rooms -->
        <div class="checkout-table-container">
            <div class="checkout-table-header">
                <h2>Hotel Rooms</h2>
            </div>
            <table id="hotelRooms" class="checkout-table">
                <thead>
                    <tr>
                        <th>Image</th>
                        <th>Room Type</th>
                        <th>Dates</th>
                        <th>Nights</th>
                        <th>Price per Night</th>
                        <th>Total</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <!-- Dynamic content -->
                </tbody>
            </table>
            <div class="checkout-padding">
                <button class="checkout-btn checkout-btn-primary" onclick="addHotelRoom()">Add More Rooms</button>
            </div>
        </div>

        <!-- Checkout Summary -->
        <div class="checkout-summary-container">
            <div class="checkout-coupon-section">
                <input type="text" id="couponCode" class="checkout-coupon-input" placeholder="Enter coupon code">
                <button class="checkout-btn checkout-btn-primary" onclick="applyCoupon()">Apply Coupon</button>
            </div>
            <div class="checkout-total-section">
                <div class="checkout-total-labels">
                    <h3>Subtotal:</h3>
                    <h3>Discount:</h3>
                    <h2>Total:</h2>
                </div>
                <div class="checkout-total-values">
                    <h3 id="subtotal">$0.00</h3>
                    <h3 id="discount">$0.00</h3>
                    <h2 id="total">$0.00</h2>
                </div>
            </div>
        </div>

        <!-- Checkout Actions -->
        <div class="checkout-summary-container">
            <div class="checkout-actions">
                <button class="checkout-btn checkout-btn-danger checkout-btn-large" onclick="handleCancel()">
                    Cancel
                </button>
                <button class="checkout-btn checkout-btn-primary checkout-btn-large" onclick="handleCheckout()">
                    Proceed to Payment
                </button>
            </div>
        </div>
    </div>
    <script>
        let cart = {
            foods: [],
            tables: [],
            rooms: []
        };

        // Food Item Management
        function addFoodItem() {
            const foodRow = {
                id: Date.now(),
                image: '/api/placeholder/70/70', // Updated size to match new CSS
                name: 'Sample Food Item',
                quantity: 1,
                price: 19.99,
                discount: 0.1
            };
            cart.foods.push(foodRow);
            updateFoodTable();
            calculateTotal();
        }

        function updateFoodTable() {
            const tbody = document.querySelector('#foodTable tbody');
            tbody.innerHTML = cart.foods.map(item => {
                const discountedPrice = item.price * (1 - item.discount);
                const totalPrice = discountedPrice * item.quantity;
                return `
            <tr>
                <td><img src="${item.image}" alt="${item.name}" class="checkout-image"></td>
                <td>${item.name}</td>
                <td>
                    <div class="checkout-quantity-control">
                        <button class="checkout-quantity-btn" onclick="updateQuantity(${item.id}, -1)">-</button>
                        <span>${item.quantity}</span>
                        <button class="checkout-quantity-btn" onclick="updateQuantity(${item.id}, 1)">+</button>
                    </div>
                </td>
                <td>$${item.price.toFixed(2)}</td>
                <td>$${discountedPrice.toFixed(2)}</td>
                <td class="checkout-price-total">$${totalPrice.toFixed(2)}</td>
                <td>
                    <button class="checkout-btn checkout-btn-danger" onclick="removeItem('foods', ${item.id})">Remove</button>
                </td>
            </tr>
        `;
            }).join('');
        }

        // Table Reservation Management
        function addTableReservation() {
            const tableRow = {
                id: Date.now(),
                image: '/api/placeholder/70/70',
                number: Math.floor(Math.random() * 20) + 1,
                date: '2024-02-02',
                time: '19:00',
                price: 50.00
            };
            cart.tables.push(tableRow);
            updateTableReservations();
            calculateTotal();
        }

        function updateTableReservations() {
            const tbody = document.querySelector('#tableReservations tbody');
            tbody.innerHTML = cart.tables.map(table => `
        <tr>
            <td><img src="${table.image}" alt="Table ${table.number}" class="checkout-image"></td>
            <td>Table ${table.number}</td>
            <td>
                <input type="date" class="checkout-date-picker" value="${table.date}" 
                    onchange="updateTableDate(${table.id}, this.value)">
            </td>
            <td>
                <input type="time" class="checkout-time-picker" value="${table.time}"
                    onchange="updateTableTime(${table.id}, this.value)">
            </td>
            <td class="checkout-price-total">$${table.price.toFixed(2)}</td>
            <td>
                <button class="checkout-btn checkout-btn-danger" onclick="removeItem('tables', ${table.id})">Remove</button>
            </td>
        </tr>
    `).join('');
        }

        // Hotel Room Management
        function addHotelRoom() {
            const roomRow = {
                id: Date.now(),
                image: '/api/placeholder/70/70',
                type: 'Deluxe Room',
                checkIn: '2024-02-02',
                checkOut: '2024-02-03',
                nights: 1,
                pricePerNight: 199.99
            };
            cart.rooms.push(roomRow);
            updateHotelRooms();
            calculateTotal();
        }

        function updateHotelRooms() {
            const tbody = document.querySelector('#hotelRooms tbody');
            tbody.innerHTML = cart.rooms.map(room => `
        <tr>
            <td><img src="${room.image}" alt="${room.type}" class="checkout-image"></td>
            <td>${room.type}</td>
            <td>
                <div class="checkout-room-dates">
                    <div class="checkout-date-group">
                        <span class="checkout-date-label">Check-in:</span>
                        <input type="date" class="checkout-date-picker" value="${room.checkIn}" 
                            onchange="updateRoomDates(${room.id}, 'checkIn', this.value)">
                    </div>
                    <div class="checkout-date-group">
                        <span class="checkout-date-label">Check-out:</span>
                        <input type="date" class="checkout-date-picker" value="${room.checkOut}"
                            onchange="updateRoomDates(${room.id}, 'checkOut', this.value)">
                    </div>
                </div>
            </td>
            <td>
                <div class="checkout-quantity-control">
                    <button class="checkout-quantity-btn" onclick="updateNights(${room.id}, -1)">-</button>
                    <span>${room.nights}</span>
                    <button class="checkout-quantity-btn" onclick="updateNights(${room.id}, 1)">+</button>
                </div>
            </td>
            <td>$${room.pricePerNight.toFixed(2)}</td>
            <td class="checkout-price-total">$${(room.pricePerNight * room.nights).toFixed(2)}</td>
            <td>
                <button class="checkout-btn checkout-btn-danger" onclick="removeItem('rooms', ${room.id})">Remove</button>
            </td>
        </tr>
    `).join('');
        }

        // Add new helper functions for table management
        function updateTableDate(id, value) {
            const table = cart.tables.find(t => t.id === id);
            if (table) {
                table.date = value;
            }
        }

        function updateTableTime(id, value) {
            const table = cart.tables.find(t => t.id === id);
            if (table) {
                table.time = value;
            }
        }

        // Existing functions with updated class names
        function updateRoomDates(id, type, value) {
            const room = cart.rooms.find(r => r.id === id);
            if (room) {
                room[type] = value;
                if (type === 'checkOut' || type === 'checkIn') {
                    const checkIn = new Date(room.checkIn);
                    const checkOut = new Date(room.checkOut);
                    const nights = Math.ceil((checkOut - checkIn) / (1000 * 60 * 60 * 24));
                    room.nights = Math.max(1, nights);
                    updateHotelRooms();
                    calculateTotal();
                }
            }
        }

        function updateQuantity(id, change) {
            const item = cart.foods.find(f => f.id === id);
            if (item) {
                item.quantity = Math.max(1, item.quantity + change);
                updateFoodTable();
                calculateTotal();
            }
        }

        function removeItem(type, id) {
            cart[type] = cart[type].filter(item => item.id !== id);
            updateTables();
            calculateTotal();
        }

        function updateNights(id, change) {
            const room = cart.rooms.find(r => r.id === id);
            if (room) {
                room.nights = Math.max(1, room.nights + change);
                const checkIn = new Date(room.checkIn);
                const newCheckOut = new Date(checkIn);
                newCheckOut.setDate(checkIn.getDate() + room.nights);
                room.checkOut = newCheckOut.toISOString().split('T')[0];
                updateHotelRooms();
                calculateTotal();
            }
        }

        function handleCheckout() {
            if (cart.foods.length === 0 && cart.tables.length === 0 && cart.rooms.length === 0) {
                alert('Please add items to your cart before proceeding to payment.');
                return;
            }
            alert('Proceeding to payment...');
        }

        function handleCancel() {
            if (cart.foods.length === 0 && cart.tables.length === 0 && cart.rooms.length === 0) {
                alert('Your cart is already empty.');
                return;
            }

            if (confirm('Are you sure you want to cancel this checkout?')) {
                cart.foods = [];
                cart.tables = [];
                cart.rooms = [];
                updateTables();
                calculateTotal();
                alert('Checkout cancelled');
            }
        }

        // Enhanced coupon system
        let currentDiscount = 0;
        const coupons = {
            'SAVE10': {
                discount: 0.10,
                description: '10% off your entire order'
            },
            'SAVE20': {
                discount: 0.20,
                description: '20% off your entire order'
            },
            'WELCOME': {
                discount: 0.15,
                description: '15% welcome discount'
            }
        };

        function applyCoupon() {
            const couponInput = document.getElementById('couponCode');
            const couponCode = couponInput.value.toUpperCase();

            if (coupons[couponCode]) {
                currentDiscount = coupons[couponCode].discount;
                alert(`Coupon applied! ${coupons[couponCode].description}`);
                couponInput.style.borderColor = 'var(--checkout-success)';
            } else {
                alert('Invalid coupon code');
                currentDiscount = 0;
                couponInput.style.borderColor = 'var(--checkout-danger)';
            }
            calculateTotal();
        }

        function calculateTotal() {
            const foodTotal = cart.foods.reduce((sum, item) =>
                sum + (item.price * item.quantity * (1 - item.discount)), 0);
            const tableTotal = cart.tables.reduce((sum, table) =>
                sum + table.price, 0);
            const roomTotal = cart.rooms.reduce((sum, room) =>
                sum + (room.pricePerNight * room.nights), 0);

            const subtotal = foodTotal + tableTotal + roomTotal;
            const discountAmount = subtotal * currentDiscount;
            const total = subtotal - discountAmount;

            document.getElementById('subtotal').textContent = `$${subtotal.toFixed(2)}`;
            document.getElementById('discount').textContent = `$${discountAmount.toFixed(2)}`;
            document.getElementById('total').textContent = `$${total.toFixed(2)}`;
        }

        function updateTables() {
            updateFoodTable();
            updateTableReservations();
            updateHotelRooms();
        }

        // Initialize with sample data
        window.onload = function() {
            addFoodItem();
            addTableReservation();
            addHotelRoom();
        };
    </script>
<?php require_once('includes/footer.php'); ?>