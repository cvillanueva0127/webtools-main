<?php
session_start();
header('Content-Type: application/json');

// Auth check
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    http_response_code(403);
    echo json_encode(['error' => 'Unauthorized']);
    exit;
}

// include FIRST before any DB queries
include 'connect.php';

$action = $_GET['action'] ?? 'auto';

// AUTO CLEANUP — called silently by admin.js on page load
// Deletes Completed + Cancelled bookings older than 30 days
if ($action === 'auto') {
    $stmt = $conn->prepare("
        DELETE FROM bookings
        WHERE status IN ('Completed', 'Cancelled')
        AND updated_at < DATE_SUB(NOW(), INTERVAL 30 DAY)
    ");
    $stmt->execute();
    $deleted = $stmt->affected_rows;
    $stmt->close();

    echo json_encode([
        'success' => true,
        'deleted' => $deleted,
        'message' => "$deleted old booking(s) automatically removed."
    ]);
    exit;
}

// MANUAL CLEANUP — called by cleanupAll() button in admin.js
// Deletes ALL cancelled bookings regardless of age
if ($action === 'delete_cancelled') {
    $stmt = $conn->prepare("
        DELETE FROM bookings
        WHERE status = 'Cancelled'
    ");
    $stmt->execute();
    $deleted = $stmt->affected_rows;
    $stmt->close();

    echo json_encode([
        'success' => true,
        'deleted' => $deleted,
        'message' => "$deleted cancelled booking(s) permanently removed."
    ]);
    exit;
}

echo json_encode(['error' => 'Unknown action.']);