<?php
error_reporting(0);
ini_set('display_errors', 0);

session_start();
include("connect.php");

header('Content-Type: application/json');

$year  = isset($_GET['year'])  ? (int)$_GET['year']  : (int)date('Y');
$month = isset($_GET['month']) ? (int)$_GET['month'] : (int)date('m');

if ($year < 2020 || $year > 2100 || $month < 1 || $month > 12) {
    echo json_encode(['success' => false, 'message' => 'Invalid year/month']);
    exit;
}

// ── Shared daily capacity (one pool for all 3 slots) ─────────────────────────
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

$slotTimes = [
    'morning'   => '08:00:00',
    'afternoon' => '14:00:00',
    'evening'   => '19:00:00',
];
$timeToSlot = array_flip($slotTimes);
$slotNames  = array_keys($slotTimes);

// ── Date range ────────────────────────────────────────────────────────────────
$monthStart = sprintf('%04d-%02d-01', $year, $month);
$monthEnd   = date('Y-m-t', strtotime($monthStart));

// ── Fetch all active bookings for this month ──────────────────────────────────
try {
    $stmt = $pdo->prepare("
        SELECT
            DATE(booking_datetime)   AS bdate,
            TIME(booking_datetime)   AS btime,
            COALESCE(SUM(guests), 0) AS booked_guests
        FROM bookings
        WHERE DATE(booking_datetime) BETWEEN ? AND ?
          AND status != 'Cancelled'
        GROUP BY DATE(booking_datetime), TIME(booking_datetime)
    ");
    $stmt->execute([$monthStart, $monthEnd]);
    $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (Throwable $e) {
    echo json_encode(['success' => false, 'message' => 'DB error: ' . $e->getMessage()]);
    exit;
}

// ── Build per-slot lookup ─────────────────────────────────────────────────────
$booked = [];
foreach ($rows as $row) {
    $t        = $row['btime'];
    $tPadded  = (strlen($t) === 7) ? '0' . $t : $t;
    $slotName = $timeToSlot[$tPadded] ?? null;
    if ($slotName !== null) {
        $booked[$row['bdate']][$slotName] = (int)$row['booked_guests'];
    }
}

// ── Compute per-day availability ──────────────────────────────────────────────
$todayStr    = date('Y-m-d');
$daysInMonth = (int)date('t', strtotime($monthStart));
$availability = [];

for ($d = 1; $d <= $daysInMonth; $d++) {
    $dateStr = sprintf('%04d-%02d-%02d', $year, $month, $d);

    if ($dateStr < $todayStr) {
        $availability[$dateStr] = ['status' => 'past'];
        continue;
    }

    // Sum ALL guests across ALL slots — single shared pool
    $totalBookedDay = 0;
    foreach ($slotNames as $slot) {
        $totalBookedDay += $booked[$dateStr][$slot] ?? 0;
    }

    $remaining = max(0, $DAILY_LIMIT - $totalBookedDay);

    // Red  = shared pool of 50 is fully used up
    // Orange = some guests booked but pool not empty yet
    // Green  = nobody booked yet
    if ($totalBookedDay >= $DAILY_LIMIT) {
        $availability[$dateStr] = ['status' => 'full',    'remaining' => 0];
    } elseif ($totalBookedDay > 0) {
        $availability[$dateStr] = ['status' => 'partial', 'remaining' => $remaining];
    } else {
        $availability[$dateStr] = ['status' => 'open',    'remaining' => $remaining];
    }
}

echo json_encode(['success' => true, 'availability' => $availability]);