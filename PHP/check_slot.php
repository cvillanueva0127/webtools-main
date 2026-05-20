<?php
/**
 * check_slot.php
 *
 * Shared daily pool — all 3 slots (morning/afternoon/evening) draw from
 * the same capacity (default 50). Booking 25 guests in morning leaves
 * only 25 for the rest of the day across afternoon and evening combined.
 *
 * MODE A — calendar slot check:
 *   GET ?date=2026-05-28&slot=morning
 *   Returns: { available, remaining, booked, capacity }
 *
 * MODE B — legacy datetime conflict check:
 *   GET ?datetime=2026-05-28 08:00:00
 *   Returns: { available, message }
 */

error_reporting(0);
ini_set('display_errors', 0);

header('Content-Type: application/json');
include("connect.php");

define('BUFFER_HOURS', 2);

$slotTimes = [
    'morning'   => '08:00:00',
    'afternoon' => '14:00:00',
    'evening'   => '19:00:00',
];

// ── Shared daily capacity ─────────────────────────────────────────────────────
$DAILY_LIMIT = 50;
try {
    $limitStmt = $pdo->query("SELECT setting_value FROM settings WHERE setting_key = 'daily_capacity' LIMIT 1");
    if ($limitStmt) {
        $val = $limitStmt->fetchColumn();
        if ($val !== false && (int)$val > 0) {
            $DAILY_LIMIT = (int)$val;
        }
    }
} catch (Throwable $e) {
    $DAILY_LIMIT = 50;
}

$hasDateSlot = isset($_GET['date'], $_GET['slot']);
$hasDatetime = !$hasDateSlot && isset($_GET['datetime']) && trim($_GET['datetime']) !== '';

// ══════════════════════════════════════════════════════════════════════════════
// MODE A — date + slot
// ══════════════════════════════════════════════════════════════════════════════
if ($hasDateSlot) {

    $date = trim($_GET['date']);
    $slot = strtolower(trim($_GET['slot']));

    if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $date)) {
        echo json_encode(['available' => false, 'remaining' => 0, 'message' => 'Invalid date format']);
        exit;
    }
    if ($date < date('Y-m-d')) {
        echo json_encode(['available' => false, 'remaining' => 0, 'message' => 'Date is in the past']);
        exit;
    }
    if (!array_key_exists($slot, $slotTimes)) {
        echo json_encode(['available' => false, 'remaining' => 0, 'message' => 'Invalid slot']);
        exit;
    }

    try {
        // Guests booked in THIS specific slot
        $slotTime = $slotTimes[$slot];
        $slotStmt = $pdo->prepare("
            SELECT COALESCE(SUM(guests), 0) AS booked_slot
            FROM bookings
            WHERE DATE(booking_datetime) = ?
              AND TIME(booking_datetime) = ?
              AND status != 'Cancelled'
        ");
        $slotStmt->execute([$date, $slotTime]);
        $bookedSlot = (int)$slotStmt->fetchColumn();

        // Total guests across ALL slots that day (shared pool)
        $dayStmt = $pdo->prepare("
            SELECT COALESCE(SUM(guests), 0) AS booked_day
            FROM bookings
            WHERE DATE(booking_datetime) = ?
              AND status != 'Cancelled'
        ");
        $dayStmt->execute([$date]);
        $bookedDay = (int)$dayStmt->fetchColumn();

    } catch (Throwable $e) {
        echo json_encode(['available' => false, 'remaining' => 0, 'message' => 'DB error: ' . $e->getMessage()]);
        exit;
    }

    // Remaining = what's left in the shared daily pool
    $remaining = max(0, $DAILY_LIMIT - $bookedDay);

    // Slot is available if the shared daily pool still has room
    $available = ($remaining > 0);

    echo json_encode([
        'available' => $available,
        'remaining' => $remaining,   // shown on slot card: "X guest slots left"
        'booked'    => $bookedSlot,  // guests in this specific slot
        'capacity'  => $DAILY_LIMIT,
    ]);
    exit;
}

// ══════════════════════════════════════════════════════════════════════════════
// MODE B — legacy datetime conflict check
// ══════════════════════════════════════════════════════════════════════════════
if ($hasDatetime) {

    $datetime = trim($_GET['datetime']);
    $ts       = strtotime($datetime);

    if (!$ts) {
        echo json_encode(['available' => false, 'message' => 'Invalid date/time.']);
        exit;
    }

    $bufferSecs  = BUFFER_HOURS * 3600;
    $windowStart = date('Y-m-d H:i:s', $ts - $bufferSecs);
    $windowEnd   = date('Y-m-d H:i:s', $ts + $bufferSecs);

    try {
        $stmt = $pdo->prepare("
            SELECT booking_datetime
            FROM bookings
            WHERE booking_datetime BETWEEN :start AND :end
              AND status != 'Cancelled'
            ORDER BY booking_datetime ASC
            LIMIT 1
        ");
        $stmt->execute([':start' => $windowStart, ':end' => $windowEnd]);
        $conflict = $stmt->fetch();
    } catch (Throwable $e) {
        echo json_encode(['available' => false, 'message' => 'DB error: ' . $e->getMessage()]);
        exit;
    }

    if ($conflict) {
        $conflictTime = date('F j, Y \a\t g:i A', strtotime($conflict['booking_datetime']));
        echo json_encode([
            'available' => false,
            'message'   => "That time slot is unavailable. There is already a booking near {$conflictTime}. Please choose a time at least 2 hours apart."
        ]);
    } else {
        echo json_encode(['available' => true, 'message' => 'Time slot is available.']);
    }
    exit;
}

echo json_encode(['available' => false, 'message' => 'No valid parameters provided.']);