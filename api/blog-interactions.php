<?php
session_start();
header('Content-Type: application/json');
require_once('../config/db.php');

$response = ['success' => false, 'message' => 'Invalid request'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get JSON input
    $input = json_decode(file_get_contents('php://input'), true);
    
    $blog_id = intval($input['blog_id'] ?? 0);
    $interaction_type = $input['interaction_type'] ?? '';
    $comment_text = trim($input['comment_text'] ?? '');
    
    // Get user from session (admin or regular user)
    $user_id = $_SESSION['user_id'] ?? $_SESSION['admin_id'] ?? 0;
    
    if (!$user_id) {
        echo json_encode(['success' => false, 'message' => 'Please login to interact with blog posts']);
        exit;
    }
    
    if (!$blog_id) {
        echo json_encode(['success' => false, 'message' => 'Blog ID is required']);
        exit;
    }

    try {
        switch($interaction_type) {
            case 'like':
                $stmt_check = $conn->prepare("SELECT id FROM blog_interactions WHERE blog_id = ? AND user_id = ? AND interaction_type = 'like'");
                $stmt_check->bind_param("ii", $blog_id, $user_id);
                $stmt_check->execute();
                $result = $stmt_check->get_result();
                
                if ($result->num_rows > 0) {
                    // Unlike - remove existing like
                    $stmt_delete = $conn->prepare("DELETE FROM blog_interactions WHERE blog_id = ? AND user_id = ? AND interaction_type = 'like'");
                    $stmt_delete->bind_param("ii", $blog_id, $user_id);
                    if ($stmt_delete->execute()) {
                        $response = ['success' => true, 'message' => 'Like removed', 'action' => 'unliked'];
                    } else {
                        $response = ['success' => false, 'message' => 'Error removing like'];
                    }
                } else {
                    // Like - add new like
                    $stmt_insert = $conn->prepare("INSERT INTO blog_interactions (blog_id, user_id, interaction_type) VALUES (?, ?, 'like')");
                    $stmt_insert->bind_param("ii", $blog_id, $user_id);
                    if ($stmt_insert->execute()) {
                        $response = ['success' => true, 'message' => 'Like added', 'action' => 'liked'];
                    } else {
                        $response = ['success' => false, 'message' => 'Error adding like'];
                    }
                }
                break;

            case 'comment':
                // Check if user has already commented
                $stmt_check_comment = $conn->prepare("SELECT id FROM blog_interactions WHERE blog_id = ? AND user_id = ? AND interaction_type = 'comment'");
                $stmt_check_comment->bind_param("ii", $blog_id, $user_id);
                $stmt_check_comment->execute();
                if ($stmt_check_comment->get_result()->num_rows > 0) {
                    $response = ['success' => false, 'message' => 'You have already commented on this blog. Use edit to update your comment.'];
                    break;
                }
                
                if (!$comment_text) {
                    $response = ['success' => false, 'message' => 'Comment text is required'];
                    break;
                }

                $rating = isset($input['rating']) ? intval($input['rating']) : null;
                
                if ($rating !== null && ($rating < 1 || $rating > 5)) {
                    $rating = null;
                }

                if ($rating !== null) {
                    $stmt_comment = $conn->prepare("INSERT INTO blog_interactions (blog_id, user_id, interaction_type, comment_text, rating) VALUES (?, ?, 'comment', ?, ?)");
                    $stmt_comment->bind_param("iisi", $blog_id, $user_id, $comment_text, $rating);
                } else {
                    $stmt_comment = $conn->prepare("INSERT INTO blog_interactions (blog_id, user_id, interaction_type, comment_text) VALUES (?, ?, 'comment', ?)");
                    $stmt_comment->bind_param("iis", $blog_id, $user_id, $comment_text);
                }
                
                if ($stmt_comment->execute()) {
                    $response = ['success' => true, 'message' => 'Comment posted successfully'];
                } else {
                    $response = ['success' => false, 'message' => 'Error posting comment'];
                }
                break;

            case 'update_comment':
                $comment_id = intval($input['comment_id'] ?? 0);
                
                if (!$comment_id) {
                    $response = ['success' => false, 'message' => 'Comment ID is required'];
                    break;
                }
                
                if (!$comment_text) {
                    $response = ['success' => false, 'message' => 'Comment text is required'];
                    break;
                }
                
                // Verify comment belongs to user
                $stmt_verify = $conn->prepare("SELECT id FROM blog_interactions WHERE id = ? AND user_id = ? AND interaction_type = 'comment'");
                $stmt_verify->bind_param("ii", $comment_id, $user_id);
                $stmt_verify->execute();
                
                if ($stmt_verify->get_result()->num_rows === 0) {
                    $response = ['success' => false, 'message' => 'Unauthorized to edit this comment'];
                    break;
                }
                
                $stmt_update = $conn->prepare("UPDATE blog_interactions SET comment_text = ? WHERE id = ? AND user_id = ?");
                $stmt_update->bind_param("sii", $comment_text, $comment_id, $user_id);
                
                if ($stmt_update->execute()) {
                    $response = ['success' => true, 'message' => 'Comment updated successfully'];
                } else {
                    $response = ['success' => false, 'message' => 'Error updating comment'];
                }
                break;

            case 'share':
                // Allow shares even without login (guest sharing)
                $stmt_share = $conn->prepare("INSERT INTO blog_interactions (blog_id, user_id, interaction_type) VALUES (?, ?, 'share')");
                $stmt_share->bind_param("ii", $blog_id, $user_id);
                
                if ($stmt_share->execute()) {
                    $response = ['success' => true, 'message' => 'Share logged successfully'];
                } else {
                    $response = ['success' => false, 'message' => 'Error logging share'];
                }
                break;

            default:
                $response = ['success' => false, 'message' => 'Invalid interaction type'];
        }
    } catch (Exception $e) {
        $response = ['success' => false, 'message' => 'Server error: ' . $e->getMessage()];
    }
} else {
    $response = ['success' => false, 'message' => 'Invalid request method. Use POST'];
}

echo json_encode($response);
?>
