<?php
session_start();

if (!isset($_SESSION['logged_in']) || !$_SESSION['logged_in']) {
    header("Location: login.php");
    exit();
}

require_once('config/db.php');
require_once('includes/esewa-helper.php');

$user_id = $_SESSION['user_id'];
$from_cart = isset($_GET['from']) && $_GET['from'] === 'cart';
$booking_id = isset($_GET['booking_id']) ? (int)$_GET['booking_id'] : 0;

if ($from_cart) {
    $booking = null;
    $amount = 0;
    $tax_amount = 0;
    $total_amount = 0;
    $esewa_data = null;
} else {
    if ($booking_id === 0) {
        header("Location: my-bookings.php");
        exit();
    }

    $stmt = $conn->prepare("SELECT * FROM orders WHERE id = ? AND user_id = ?");
    $stmt->bind_param("ii", $booking_id, $user_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 0) {
        header("Location: my-bookings.php");
        exit();
    }

    $booking = $result->fetch_assoc();
    $amount = floatval($booking['price'] ?? 0);
    $tax_amount = 0;
    $total_amount = floatval($booking['price'] ?? 0);
    $esewa_data = prepareEsewaPayment($booking_id, $amount, $tax_amount, $total_amount);
}

include 'includes/header.php';
?>

<div class="payment-container">
    <div class="payment-card">
        <div class="payment-header">
            <h1>Complete Your Payment</h1>
            <p><?php echo $from_cart ? 'Cart Checkout' : 'Booking ID: #' . $booking_id; ?></p>
        </div>
        
        <div class="payment-body">
            <div class="booking-summary" id="bookingSummary">
                <h3><?php echo $from_cart ? 'Order Summary' : 'Booking Summary'; ?></h3>
                <?php if ($from_cart): ?>
                    <div id="cartSummaryContent"></div>
                <?php else: ?>
                    <div class="summary-row">
                        <span>Item:</span>
                        <span><?php echo htmlspecialchars($booking['item_name']); ?></span>
                    </div>
                    <div class="summary-row">
                        <span>Type:</span>
                        <span><?php echo ucfirst($booking['order_type']); ?></span>
                    </div>
                    <div class="summary-row">
                        <span>Quantity:</span>
                        <span><?php echo $booking['quantity']; ?></span>
                    </div>
                    <div class="summary-row">
                        <span>Total Amount:</span>
                        <span>RS <?php echo number_format($booking['price'], 2); ?></span>
                    </div>
                <?php endif; ?>
            </div>
            
            <div class="payment-methods">
                <h3>Select Payment Method</h3>
                
                <!-- Cash Payment -->
                <label class="payment-method" data-method="cash">
                    <input type="radio" name="payment_method" value="cash" checked>
                    <div class="payment-icon">💵</div>
                    <div class="payment-details">
                        <strong>Cash at Counter</strong>
                        <p>Pay when you arrive at the hotel counter</p>
                    </div>
                </label>
                
                <!-- eSewa Payment -->
                <label class="payment-method" data-method="esewa">
                    <input type="radio" name="payment_method" value="esewa">
                    <div class="payment-icon">
                        <img src="https://esewa.com.np/common/images/esewa-icon-large.png" alt="eSewa" style="width: 40px; height: 40px;">
                    </div>
                    <div class="payment-details">
                        <strong>eSewa</strong>
                        <p>Digital wallet payment (Nepal)</p>
                    </div>
                </label>
                
                <!-- Stripe Payment (Demo) -->
                <label class="payment-method" data-method="stripe">
                    <input type="radio" name="payment_method" value="stripe">
                    <div class="payment-icon">💳</div>
                    <div class="payment-details">
                        <strong>Credit/Debit Card (Stripe Demo)</strong>
                        <p>International card payment</p>
                    </div>
                </label>
            </div>
            
            <!-- Stripe Card Form (Hidden by default) -->
            <div id="stripe-form" style="display: none; margin: 20px 0; padding: 20px; background: #f8f9fa; border-radius: 10px;">
                <h4>Card Details (Test Mode)</h4>
                <div class="form-group">
                    <label>Card Number</label>
                    <input type="text" id="card-number" placeholder="4242 4242 4242 4242" maxlength="19" class="payment-input">
                </div>
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px;">
                    <div class="form-group">
                        <label>Expiry (MM/YY)</label>
                        <input type="text" id="card-expiry" placeholder="12/25" maxlength="5" class="payment-input">
                    </div>
                    <div class="form-group">
                        <label>CVC</label>
                        <input type="text" id="card-cvc" placeholder="123" maxlength="3" class="payment-input">
                    </div>
                </div>
                <p style="color: #666; font-size: 13px; margin-top: 10px;">
                    <strong>Test Card:</strong> 4242 4242 4242 4242 | Any future date | Any 3 digits
                </p>
            </div>
            
            <div class="payment-actions">
                <button class="btn btn-secondary" onclick="window.location.href='<?php echo $from_cart ? 'cart.php' : 'my-bookings.php'; ?>'">Cancel</button>
                <button class="btn btn-primary" onclick="confirmPayment()">Confirm <?php echo $from_cart ? 'Order' : 'Booking'; ?></button>
            </div>
        </div>
    </div>
</div>

<?php if (!$from_cart): ?>
<form id="esewa-payment-form" action="<?php echo $esewa_data['action_url']; ?>" method="POST" style="display: none;">
    <input type="hidden" name="amount" value="<?php echo $esewa_data['amount']; ?>">
    <input type="hidden" name="tax_amount" value="<?php echo $esewa_data['tax_amount']; ?>">
    <input type="hidden" name="total_amount" value="<?php echo $esewa_data['total_amount']; ?>">
    <input type="hidden" name="transaction_uuid" value="<?php echo $esewa_data['transaction_uuid']; ?>">
    <input type="hidden" name="product_code" value="<?php echo $esewa_data['product_code']; ?>">
    <input type="hidden" name="product_service_charge" value="<?php echo $esewa_data['product_service_charge']; ?>">
    <input type="hidden" name="product_delivery_charge" value="<?php echo $esewa_data['product_delivery_charge']; ?>">
    <input type="hidden" name="success_url" value="<?php echo $esewa_data['success_url']; ?>">
    <input type="hidden" name="failure_url" value="<?php echo $esewa_data['failure_url']; ?>">
    <input type="hidden" name="signed_field_names" value="<?php echo $esewa_data['signed_field_names']; ?>">
    <input type="hidden" name="signature" value="<?php echo $esewa_data['signature']; ?>">
</form>
<?php else: ?>
<form id="esewa-payment-form" action="" method="POST" style="display: none;">
</form>
<?php endif; ?>

<script>
    const fromCart = <?php echo $from_cart ? 'true' : 'false'; ?>;
    const bookingId = <?php echo $from_cart ? '0' : $booking_id; ?>;
    const totalAmount = <?php echo $from_cart ? '0' : (isset($booking['price']) ? $booking['price'] : 0); ?>;
    let cartCheckoutData = null;
    
    if (fromCart) {
        const storedData = sessionStorage.getItem('checkoutData');
        if (!storedData) {
            alert('No checkout data found. Redirecting to cart...');
            window.location.href = 'cart.php';
        } else {
            cartCheckoutData = JSON.parse(storedData);
            displayCartSummary();
        }
    }
    
    function displayCartSummary() {
        if (!cartCheckoutData) return;
        
        const container = document.getElementById('cartSummaryContent');
        let html = '';
        
        const allItems = [
            ...(cartCheckoutData.cart.food || cartCheckoutData.cart.foods || []).map(f => ({...f, type: 'Food'})),
            ...(cartCheckoutData.cart.rooms || []).map(r => ({...r, type: 'Room'})),
            ...(cartCheckoutData.cart.tables || []).map(t => ({...t, type: 'Table'}))
        ];
        
        allItems.forEach(item => {
            let itemName = item.food_name || item.room_name || item.table_name || 'Unknown';
            let price = item.discount_price || item.price_today || item.price || item.price_main || 0;
            let qty = item.quantity || item.nights || 1;
            
            html += `<div class="summary-row">
                <span>${itemName} (${item.type})</span>
                <span>Rs. ${(price * qty).toFixed(2)}</span>
            </div>`;
        });
        
        html += `<div class="summary-row">
            <span>Subtotal:</span>
            <span>Rs. ${cartCheckoutData.subtotal}</span>
        </div>`;
        
        if (parseFloat(cartCheckoutData.discount) > 0) {
            html += `<div class="summary-row" style="color: #28a745;">
                <span>Discount ${cartCheckoutData.coupon ? '(' + cartCheckoutData.coupon.code + ')' : ''}:</span>
                <span>-Rs. ${cartCheckoutData.discount}</span>
            </div>`;
        }
        
        html += `<div class="summary-row">
            <span>Total Amount:</span>
            <span>Rs. ${cartCheckoutData.total}</span>
        </div>`;
        
        container.innerHTML = html;
    }
    
    document.querySelectorAll('.payment-method').forEach(method => {
        method.addEventListener('click', function() {
            document.querySelectorAll('.payment-method').forEach(m => m.classList.remove('selected'));
            
            this.classList.add('selected');
            this.querySelector('input[type="radio"]').checked = true;
            
            const stripeForm = document.getElementById('stripe-form');
            const selectedMethod = this.getAttribute('data-method');
            stripeForm.style.display = (selectedMethod === 'stripe') ? 'block' : 'none';
        });
    });
    
    const cardNumberInput = document.getElementById('card-number');
    if (cardNumberInput) {
        cardNumberInput.addEventListener('input', function(e) {
            let value = e.target.value.replace(/\s/g, '');
            let formattedValue = value.match(/.{1,4}/g)?.join(' ') || value;
            e.target.value = formattedValue;
        });
    }
    
    const cardExpiryInput = document.getElementById('card-expiry');
    if (cardExpiryInput) {
        cardExpiryInput.addEventListener('input', function(e) {
            let value = e.target.value.replace(/\D/g, '');
            if (value.length >= 2) {
                value = value.substring(0, 2) + '/' + value.substring(2, 4);
            }
            e.target.value = value;
        });
    }
    
    function confirmPayment() {
        const paymentMethod = document.querySelector('input[name="payment_method"]:checked').value;
        
        if (paymentMethod === 'stripe') {
            const cardNumber = document.getElementById('card-number').value.replace(/\s/g, '');
            const cardExpiry = document.getElementById('card-expiry').value;
            const cardCvc = document.getElementById('card-cvc').value;
            
            if (!cardNumber || cardNumber.length !== 16) {
                alert('Please enter a valid 16-digit card number');
                return;
            }
            
            if (!cardExpiry || !cardExpiry.match(/^\d{2}\/\d{2}$/)) {
                alert('Please enter expiry date in MM/YY format');
                return;
            }
            
            if (!cardCvc || cardCvc.length !== 3) {
                alert('Please enter a valid 3-digit CVC');
                return;
            }
        }
        
        switch(paymentMethod) {
            case 'esewa':
                processEsewaPayment();
                break;
            case 'stripe':
                processStripePayment();
                break;
            case 'cash':
            default:
                processCashPayment();
                break;
        }
    }
    
    function processCashPayment() {
        if (fromCart) {
            createCartOrder('cash', 'pending');
        } else {
            confirmBooking('cash', 'pending');
        }
    }
    
    async function processEsewaPayment() {
        if (fromCart) {
            if (!cartCheckoutData) {
                alert('Cart data not found');
                return;
            }
            
            const orderData = {
                cart: cartCheckoutData.cart,
                subtotal: cartCheckoutData.subtotal,
                discount: cartCheckoutData.discount,
                total: cartCheckoutData.total,
                coupon: cartCheckoutData.coupon,
                payment_method: 'esewa',
                payment_status: 'pending'
            };
            
            try {
                const response = await fetch('api/create-cart-order.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify(orderData)
                });
                const data = await response.json();
                
                if (data.success && data.order_id) {
                    initiateCartEsewaPayment(data.order_id, parseFloat(cartCheckoutData.total));
                } else {
                    alert('Error: ' + (data.message || 'Failed to create order'));
                }
            } catch (error) {
                console.error('Error:', error);
                alert('An error occurred while processing your order');
            }
        } else {
            document.getElementById('esewa-payment-form').submit();
        }
    }
    
    function initiateCartEsewaPayment(orderId, amount) {
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = 'https://rc-epay.esewa.com.np/api/epay/main/v2/form';
        
        const now = new Date();
        const year = now.getFullYear();
        const month = String(now.getMonth() + 1).padStart(2, '0');
        const day = String(now.getDate()).padStart(2, '0');
        const hours = String(now.getHours()).padStart(2, '0');
        const minutes = String(now.getMinutes()).padStart(2, '0');
        const seconds = String(now.getSeconds()).padStart(2, '0');
        const transactionUuid = 'cart_' + orderId + '_' + year + month + day + hours + minutes + seconds;
        
        const taxAmount = 0;
        const productServiceCharge = 0;
        const productDeliveryCharge = 0;
        const totalAmount = parseFloat(amount).toFixed(2);
        const amountValue = parseFloat(amount).toFixed(2);
        
        const fields = {
            amount: amountValue,
            tax_amount: taxAmount,
            total_amount: totalAmount,
            transaction_uuid: transactionUuid,
            product_code: 'EPAYTEST',
            product_service_charge: productServiceCharge,
            product_delivery_charge: productDeliveryCharge,
            success_url: window.location.origin + '/Hotel-Annapurna-Web/esewa-success.php',
            failure_url: window.location.origin + '/Hotel-Annapurna-Web/esewa-failure.php',
            signed_field_names: 'total_amount,transaction_uuid,product_code'
        };
        
        const message = 'total_amount=' + fields.total_amount + ',transaction_uuid=' + fields.transaction_uuid + ',product_code=' + fields.product_code;
        
        fetch('api/esewa-status-check.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ message: message })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success && data.signature) {
                fields.signature = data.signature;
                
                Object.keys(fields).forEach(key => {
                    const input = document.createElement('input');
                    input.type = 'hidden';
                    input.name = key;
                    input.value = fields[key];
                    form.appendChild(input);
                });
                
                document.body.appendChild(form);
                form.submit();
            } else {
                alert('Error generating signature');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Error processing payment');
        });
    }
    
    function processStripePayment() {
        const cardNumber = document.getElementById('card-number').value;
        
        if (fromCart) {
            createCartOrder('stripe', 'paid');
        } else {
            confirmBooking('stripe', 'paid');
        }
    }
    
    function createCartOrder(method, paymentStatus) {
        if (!cartCheckoutData) {
            alert('Cart data not found');
            return;
        }
        
        const orderData = {
            cart: cartCheckoutData.cart,
            subtotal: cartCheckoutData.subtotal,
            discount: cartCheckoutData.discount,
            total: cartCheckoutData.total,
            coupon: cartCheckoutData.coupon,
            payment_method: method,
            payment_status: paymentStatus
        };
        
        fetch('api/create-cart-order.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify(orderData)
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                sessionStorage.removeItem('checkoutData');
                localStorage.removeItem('hotelCart');
                alert('✓ Order placed successfully!');
                
                const hasFoodOnly = (cartCheckoutData.cart.food?.length > 0 || cartCheckoutData.cart.foods?.length > 0) && 
                                    (!cartCheckoutData.cart.rooms || cartCheckoutData.cart.rooms.length === 0) && 
                                    (!cartCheckoutData.cart.tables || cartCheckoutData.cart.tables.length === 0);
                
                window.location.href = hasFoodOnly ? 'my-orders.php' : 'my-bookings.php';
            } else {
                alert('Error: ' + (data.message || 'Failed to create order'));
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('An error occurred while processing your order');
        });
    }
    
    function confirmBooking(method, paymentStatus) {
        const formData = new FormData();
        formData.append('booking_id', bookingId);
        formData.append('payment_method', method);
        formData.append('payment_status', paymentStatus);
        
        fetch('api/confirm-booking.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert('✓ Booking confirmed successfully!');
                window.location.href = 'my-bookings.php';
            } else {
                alert('Error: ' + (data.message || 'Failed to confirm booking'));
            }
        })
        .catch(error => {
            console.error('Booking Error:', error);
            alert('An error occurred. Please try again.');
        });
    }
</script>

<?php include 'includes/footer.php'; ?>
