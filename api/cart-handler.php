<?php
session_start();
header('Content-Type: application/json');

require_once __DIR__ . '/../config/db.php';

if (!isset($_SESSION['logged_in']) || !$_SESSION['logged_in']) {
    echo json_encode(['success' => false, 'message' => 'User not logged in']);
    exit();
}

$user_id = $_SESSION['user_id'];
$action = $_POST['action'] ?? $_GET['action'] ?? '';

function getCartItems($conn, $user_id) {
    $stmt = $conn->prepare("SELECT * FROM cart_items WHERE user_id = ?");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    $cart = ['food' => [], 'rooms' => [], 'tables' => []];
    while ($row = $result->fetch_assoc()) {
        $item_data = json_decode($row['item_data'], true);
        $item_data['cart_id'] = $row['id'];
        
        if ($row['item_type'] === 'food') {
            $item_data['quantity'] = $row['quantity'];
            $cart['food'][] = $item_data;
        } elseif ($row['item_type'] === 'room') {
            $item_data['nights'] = $row['quantity'];
            $cart['rooms'][] = $item_data;
        } elseif ($row['item_type'] === 'table') {
            $item_data['quantity'] = $row['quantity'];
            $cart['tables'][] = $item_data;
        }
    }
    
    return $cart;
}

if ($action === 'sync') {
    $input = file_get_contents('php://input');
    $localStorage = json_decode($input, true);
    
    if (!$localStorage || !is_array($localStorage)) {
        echo json_encode(['success' => false, 'message' => 'No cart data provided', 'debug' => $input]);
        exit();
    }
    
    $synced = 0;
    $errors = [];
    
    // Handle both 'food' and 'foods' keys for backward compatibility
    $foodItems = $localStorage['food'] ?? $localStorage['foods'] ?? [];
    if (is_array($foodItems)) {
        foreach ($foodItems as $food) {
            $item_data = json_encode($food);
            $quantity = $food['quantity'] ?? 1;
            $item_id = $food['id'];
            
            $check_sql = "SELECT id, quantity FROM cart_items WHERE user_id = ? AND item_type = 'food' AND item_id = ?";
            $check_stmt = $conn->prepare($check_sql);
            $check_stmt->bind_param("ii", $user_id, $item_id);
            $check_stmt->execute();
            $result = $check_stmt->get_result();
            
            if ($result->num_rows > 0) {
                $row = $result->fetch_assoc();
                $new_quantity = $row['quantity'] + $quantity;
                $update_sql = "UPDATE cart_items SET quantity = ?, item_data = ?, updated_at = CURRENT_TIMESTAMP WHERE id = ?";
                $update_stmt = $conn->prepare($update_sql);
                $update_stmt->bind_param("isi", $new_quantity, $item_data, $row['id']);
                if ($update_stmt->execute()) $synced++;
            } else {
                $insert_sql = "INSERT INTO cart_items (user_id, item_type, item_id, item_data, quantity) VALUES (?, 'food', ?, ?, ?)";
                $insert_stmt = $conn->prepare($insert_sql);
                $insert_stmt->bind_param("iisi", $user_id, $item_id, $item_data, $quantity);
                if ($insert_stmt->execute()) {
                    $synced++;
                } else {
                    $errors[] = "Failed to insert food item " . $item_id . ": " . $conn->error;
                }
            }
        }
    }
    
    if (isset($localStorage['rooms']) && is_array($localStorage['rooms'])) {
        foreach ($localStorage['rooms'] as $room) {
            $item_data = json_encode($room);
            $quantity = $room['nights'] ?? 1;
            $item_id = $room['id'];
            
            $check_sql = "SELECT id, quantity FROM cart_items WHERE user_id = ? AND item_type = 'room' AND item_id = ?";
            $check_stmt = $conn->prepare($check_sql);
            $check_stmt->bind_param("ii", $user_id, $item_id);
            $check_stmt->execute();
            $result = $check_stmt->get_result();
            
            if ($result->num_rows > 0) {
                $row = $result->fetch_assoc();
                $new_quantity = $row['quantity'] + $quantity;
                $update_sql = "UPDATE cart_items SET quantity = ?, item_data = ?, updated_at = CURRENT_TIMESTAMP WHERE id = ?";
                $update_stmt = $conn->prepare($update_sql);
                $update_stmt->bind_param("isi", $new_quantity, $item_data, $row['id']);
                if ($update_stmt->execute()) $synced++;
            } else {
                $insert_sql = "INSERT INTO cart_items (user_id, item_type, item_id, item_data, quantity) VALUES (?, 'room', ?, ?, ?)";
                $insert_stmt = $conn->prepare($insert_sql);
                $insert_stmt->bind_param("iisi", $user_id, $item_id, $item_data, $quantity);
                if ($insert_stmt->execute()) $synced++;
            }
        }
    }
    
    if (isset($localStorage['tables']) && is_array($localStorage['tables'])) {
        foreach ($localStorage['tables'] as $table) {
            $item_data = json_encode($table);
            $quantity = $table['quantity'] ?? 1;
            $item_id = $table['id'];
            
            $check_sql = "SELECT id, quantity FROM cart_items WHERE user_id = ? AND item_type = 'table' AND item_id = ?";
            $check_stmt = $conn->prepare($check_sql);
            $check_stmt->bind_param("ii", $user_id, $item_id);
            $check_stmt->execute();
            $result = $check_stmt->get_result();
            
            if ($result->num_rows > 0) {
                $row = $result->fetch_assoc();
                $new_quantity = $row['quantity'] + $quantity;
                $update_sql = "UPDATE cart_items SET quantity = ?, item_data = ?, updated_at = CURRENT_TIMESTAMP WHERE id = ?";
                $update_stmt = $conn->prepare($update_sql);
                $update_stmt->bind_param("isi", $new_quantity, $item_data, $row['id']);
                if ($update_stmt->execute()) $synced++;
            } else {
                $insert_sql = "INSERT INTO cart_items (user_id, item_type, item_id, item_data, quantity) VALUES (?, 'table', ?, ?, ?)";
                $insert_stmt = $conn->prepare($insert_sql);
                $insert_stmt->bind_param("iisi", $user_id, $item_id, $item_data, $quantity);
                if ($insert_stmt->execute()) $synced++;
            }
        }
    }
    
    echo json_encode([
        'success' => true, 
        'message' => "$synced items synced to database",
        'cart' => getCartItems($conn, $user_id),
        'errors' => $errors
    ]);
    exit();
}

if ($action === 'get') {
    echo json_encode(['success' => true, 'cart' => getCartItems($conn, $user_id)]);
    exit();
}

if ($action === 'add') {
    $item_type = $_POST['item_type'] ?? '';
    $item_id = $_POST['item_id'] ?? 0;
    $item_data = $_POST['item_data'] ?? '';
    $quantity = $_POST['quantity'] ?? 1;
    
    if (!$item_type || !$item_id || !$item_data) {
        echo json_encode(['success' => false, 'message' => 'Missing required fields']);
        exit();
    }
    
    $sql = "INSERT INTO cart_items (user_id, item_type, item_id, item_data, quantity) 
            VALUES (?, ?, ?, ?, ?) 
            ON DUPLICATE KEY UPDATE quantity = quantity + VALUES(quantity), updated_at = CURRENT_TIMESTAMP";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("isisi", $user_id, $item_type, $item_id, $item_data, $quantity);
    
    if ($stmt->execute()) {
        echo json_encode(['success' => true, 'message' => 'Item added to cart', 'cart' => getCartItems($conn, $user_id)]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Failed to add item']);
    }
    exit();
}

if ($action === 'update') {
    $cart_id = $_POST['cart_id'] ?? 0;
    $quantity = $_POST['quantity'] ?? 1;
    
    $sql = "UPDATE cart_items SET quantity = ?, updated_at = CURRENT_TIMESTAMP WHERE id = ? AND user_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("iii", $quantity, $cart_id, $user_id);
    
    if ($stmt->execute()) {
        echo json_encode(['success' => true, 'message' => 'Quantity updated', 'cart' => getCartItems($conn, $user_id)]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Failed to update quantity']);
    }
    exit();
}

if ($action === 'remove') {
    $cart_id = $_POST['cart_id'] ?? $_GET['cart_id'] ?? 0;
    
    $sql = "DELETE FROM cart_items WHERE id = ? AND user_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ii", $cart_id, $user_id);
    
    if ($stmt->execute()) {
        echo json_encode(['success' => true, 'message' => 'Item removed from cart', 'cart' => getCartItems($conn, $user_id)]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Failed to remove item']);
    }
    exit();
}

if ($action === 'clear') {
    $sql = "DELETE FROM cart_items WHERE user_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $user_id);
    
    if ($stmt->execute()) {
        echo json_encode(['success' => true, 'message' => 'Cart cleared']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Failed to clear cart']);
    }
    exit();
}

echo json_encode(['success' => false, 'message' => 'Invalid action']);
?>
