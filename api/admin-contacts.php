<?php
session_start();
header('Content-Type: application/json');
require_once __DIR__ . '/../config/db.php';

// Admin authorization check
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true || $_SESSION['admin_role'] !== 'admin') {
    echo json_encode(['success' => false, 'message' => 'Unauthorized access. Admin login required.']);
    exit();
}

$action = $_POST['action'] ?? $_GET['action'] ?? '';

if ($action === 'get') {
    $id = intval($_GET['id'] ?? 0);
    
    if (!$id) {
        echo json_encode(['success' => false, 'message' => 'Invalid ID']);
        exit();
    }
    
    $stmt = $conn->prepare("SELECT * FROM contact_requests WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($contact = $result->fetch_assoc()) {
        echo json_encode(['success' => true, 'contact' => $contact]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Contact not found']);
    }
    exit();
}

if ($action === 'get_all') {
    $status = $_GET['status'] ?? '';
    
    $query = "SELECT * FROM contact_requests WHERE 1=1";
    $params = [];
    $types = "";
    
    if ($status) {
        $query .= " AND status = ?";
        $params[] = $status;
        $types .= "s";
    }
    
    $query .= " ORDER BY created_at DESC";
    
    $stmt = $conn->prepare($query);
    
    if ($params) {
        $stmt->bind_param($types, ...$params);
    }
    
    $stmt->execute();
    $result = $stmt->get_result();
    
    $contacts = [];
    while ($row = $result->fetch_assoc()) {
        $contacts[] = $row;
    }
    
    echo json_encode(['success' => true, 'data' => $contacts]);
    exit();
}

if ($action === 'update_status') {
    $id = intval($_POST['id'] ?? 0);
    $status = $_POST['status'] ?? '';
    
    if (!$id || !$status) {
        echo json_encode(['success' => false, 'message' => 'Missing required fields']);
        exit();
    }
    
    $valid_statuses = ['pending', 'in-progress', 'resolved'];
    if (!in_array($status, $valid_statuses)) {
        echo json_encode(['success' => false, 'message' => 'Invalid status']);
        exit();
    }
    
    $stmt = $conn->prepare("UPDATE contact_requests SET status = ? WHERE id = ?");
    $stmt->bind_param("si", $status, $id);
    
    if ($stmt->execute()) {
        echo json_encode(['success' => true, 'message' => 'Status updated successfully']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Failed to update status']);
    }
    exit();
}

if ($action === 'send_reply') {
    require_once __DIR__ . '/../config/setup_mailer.php';
    
    $id = intval($_POST['id'] ?? 0);
    $email = $_POST['email'] ?? '';
    $subject = $_POST['subject'] ?? '';
    $message = $_POST['message'] ?? '';
    
    if (!$id || !$email || !$subject || !$message) {
        echo json_encode(['success' => false, 'message' => 'Missing required fields']);
        exit();
    }
    
    // Validate email
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        echo json_encode(['success' => false, 'message' => 'Invalid email address']);
        exit();
    }
    
    // Create HTML email body
    $emailBody = "
    <html>
    <head>
        <style>
            body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
            .container { max-width: 600px; margin: 0 auto; padding: 20px; }
            .header { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; padding: 20px; text-align: center; border-radius: 10px 10px 0 0; }
            .content { background: #f9f9f9; padding: 30px; border-radius: 0 0 10px 10px; }
            .message { background: white; padding: 20px; border-left: 4px solid #667eea; margin: 20px 0; }
            .footer { text-align: center; margin-top: 20px; font-size: 12px; color: #666; }
        </style>
    </head>
    <body>
        <div class='container'>
            <div class='header'>
                <h2>Annapurna Hotel - Response to Your Contact Request</h2>
            </div>
            <div class='content'>
                <p>Dear Customer,</p>
                <p>Thank you for contacting Annapurna Hotel. Here is our response to your inquiry:</p>
                <div class='message'>
                    " . nl2br(htmlspecialchars($message)) . "
                </div>
                <p>If you have any further questions, please don't hesitate to contact us.</p>
                <p>Best regards,<br>Annapurna Hotel Team</p>
            </div>
            <div class='footer'>
                <p>&copy; 2024 Annapurna Hotel. All rights reserved.</p>
            </div>
        </div>
    </body>
    </html>
    ";
    
    // Send email
    $result = sendMail($email, $subject, $emailBody);
    
    if ($result === true) {
        // Update contact status to 'resolved'
        $stmt = $conn->prepare("UPDATE contact_requests SET status = 'resolved' WHERE id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        
        echo json_encode(['success' => true, 'message' => 'Email reply sent successfully']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Failed to send email: ' . $result]);
    }
    exit();
}

if ($action === 'delete') {
    $id = intval($_POST['id'] ?? 0);
    
    if (!$id) {
        echo json_encode(['success' => false, 'message' => 'Invalid ID']);
        exit();
    }
    
    $stmt = $conn->prepare("DELETE FROM contact_requests WHERE id = ?");
    $stmt->bind_param("i", $id);
    
    if ($stmt->execute()) {
        echo json_encode(['success' => true, 'message' => 'Contact request deleted successfully']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Failed to delete contact request']);
    }
    exit();
}

echo json_encode(['success' => false, 'message' => 'Invalid action']);
?>
