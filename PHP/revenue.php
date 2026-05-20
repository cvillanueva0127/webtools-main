<?php
session_start();

if(!isset($_SESSION['role']) || $_SESSION['role'] != 'admin'){
    header("Location: register.php");
    exit();
}

include "connect.php";

/* =========================
   TOTAL REVENUE
========================= */

// Total Revenue — only confirmed payments
$totalRevenueQuery = mysqli_query($conn,"
    SELECT SUM(amount) as totalRevenue
    FROM bookings
    WHERE status='Completed' AND payment_status='Confirmed'
");
$totalRevenueData = mysqli_fetch_assoc($totalRevenueQuery);
$totalRevenue = $totalRevenueData['totalRevenue'] ?? 0;

// Completed Revenue (pie chart)
$q1 = mysqli_query($conn,"
    SELECT SUM(amount) as total FROM bookings
    WHERE status='Completed' AND payment_status='Confirmed'
");
$completedRevenue = mysqli_fetch_assoc($q1)['total'] ?? 0;

// Pending Revenue (pie chart)
$q2 = mysqli_query($conn,"
    SELECT SUM(amount) as total FROM bookings WHERE status='Pending'
");
$pendingRevenue = mysqli_fetch_assoc($q2)['total'] ?? 0;

// Approved Revenue (pie chart)
$q3 = mysqli_query($conn,"
    SELECT SUM(amount) as total FROM bookings WHERE status='Approved'
");
$approvedRevenue = mysqli_fetch_assoc($q3)['total'] ?? 0;

// Cancelled Revenue (pie chart)
$q4 = mysqli_query($conn,"
    SELECT SUM(amount) as total FROM bookings WHERE status='Cancelled'
");
$cancelledRevenue = mysqli_fetch_assoc($q4)['total'] ?? 0;

// Total Completed (confirmed only)
$totalCompletedQuery = mysqli_query($conn,"
    SELECT COUNT(*) as totalCompleted FROM bookings
    WHERE status='Completed' AND payment_status='Confirmed'
");
$totalCompleted = mysqli_fetch_assoc($totalCompletedQuery)['totalCompleted'] ?? 0;

// Monthly Revenue (confirmed only)
$monthlyRevenueQuery = mysqli_query($conn,"
    SELECT SUM(amount) as monthlyRevenue FROM bookings
    WHERE status='Completed'
    AND payment_status='Confirmed'
    AND MONTH(booking_datetime)=MONTH(CURRENT_DATE())
");
$monthlyRevenue = mysqli_fetch_assoc($monthlyRevenueQuery)['monthlyRevenue'] ?? 0;

$totalRevenueData = mysqli_fetch_assoc($totalRevenueQuery);

$totalRevenue = $totalRevenueData['totalRevenue'] ?? 0;

/* =========================
   PIE CHART VALUE
========================= */

$completedRevenue = 0;
$pendingRevenue = 0;
$approvedRevenue = 0;
$cancelledRevenue = 0;

/* COMPLETED */
$q1 = mysqli_query($conn,"
SELECT SUM(amount) as total
FROM bookings
WHERE status='Completed'
");

$completedRevenue =
mysqli_fetch_assoc($q1)['total'] ?? 0;

/* PENDING */
$q2 = mysqli_query($conn,"
SELECT SUM(amount) as total
FROM bookings
WHERE status='Pending'
");

$pendingRevenue =
mysqli_fetch_assoc($q2)['total'] ?? 0;

/* APPROVED */
$q3 = mysqli_query($conn,"
SELECT SUM(amount) as total
FROM bookings
WHERE status='Approved'
");

$approvedRevenue =
mysqli_fetch_assoc($q3)['total'] ?? 0;

/* CANCELLED */
$q4 = mysqli_query($conn,"
SELECT SUM(amount) as total
FROM bookings
WHERE status='Cancelled'
");

$cancelledRevenue =
mysqli_fetch_assoc($q4)['total'] ?? 0;

/* =========================
   TOTAL COMPLETED
========================= */

$totalCompletedQuery = mysqli_query($conn,"
SELECT COUNT(*) as totalCompleted
FROM bookings
WHERE status='Completed'
");

$totalCompletedData = mysqli_fetch_assoc($totalCompletedQuery);

$totalCompleted =
$totalCompletedData['totalCompleted'] ?? 0;

/* =========================
   MONTHLY REVENUE
========================= */

$monthlyRevenueQuery = mysqli_query($conn,"
SELECT SUM(amount) as monthlyRevenue
FROM bookings
WHERE status='Completed'
AND MONTH(booking_datetime)=MONTH(CURRENT_DATE())
");

$monthlyRevenueData =
mysqli_fetch_assoc($monthlyRevenueQuery);

$monthlyRevenue =
$monthlyRevenueData['monthlyRevenue'] ?? 0;

/* =========================
   RECENT PAYMENTS
========================= */

$paymentsQuery = mysqli_query($conn,"
SELECT *
FROM bookings
WHERE status='Completed'
ORDER BY booking_datetime DESC
");

?>

<!DOCTYPE html>
<html lang="en">

<head>

<meta charset="UTF-8">

<meta name="viewport"
content="width=device-width, initial-scale=1.0">

<title>Revenue Dashboard</title>

<link rel="stylesheet"
href="../CSS/adminsamp.css">
<link rel="icon" type="image/jpg" href="../IMAGES/logo.jpg">

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<style>

/* =========================
   REVENUE PAGE
========================= */

.revenue-grid{
    display:grid;
    grid-template-columns:
    repeat(auto-fit,minmax(250px,1fr));
    gap:25px;
    margin-bottom:40px;
}

.revenue-card{
    background:#fff;
    padding:30px;
    border-radius:24px;
    box-shadow:0 10px 25px rgba(0,0,0,0.06);
    transition:0.3s;
}

.revenue-card:hover{
    transform:translateY(-5px);
}

.revenue-card h3{
    color:#777;
    margin-bottom:12px;
    font-size:1rem;
}

.revenue-card p{
    font-size:2rem;
    font-weight:700;
    color:#bc6c25;
}

/* =========================
   PIE CHART
========================= */

.revenue-chart-card{
    background:#fff;
    padding:30px;
    border-radius:25px;
    box-shadow:0 10px 25px rgba(0,0,0,0.06);
    margin-bottom:40px;
}

.chart-header{
    margin-bottom:25px;
}

.chart-header h2{
    color:#283618;
    font-size:1.5rem;
    margin-bottom:5px;
}

.chart-header p{
    color:#777;
    font-size:0.9rem;
}

.chart-container{
    width:100%;
    max-width:500px;
    margin:auto;
}

/* =========================
   TABLE
========================= */

.revenue-table{
    background:#fff;
    padding:30px;
    border-radius:24px;
    box-shadow:0 10px 25px rgba(0,0,0,0.06);
}

.revenue-table h2{
    margin-bottom:25px;
    color:#283618;
}

table{
    width:100%;
    border-collapse:collapse;
}

table th{
    text-align:left;
    padding:15px;
    color:#777;
    font-size:0.9rem;
}

table td{
    padding:15px;
    border-top:1px solid #eee;
}

.status-paid{
    background:#4caf50;
    color:white;
    padding:6px 14px;
    border-radius:20px;
    font-size:0.75rem;
}

@media(max-width:768px){

    table{
        display:block;
        overflow-x:auto;
        white-space:nowrap;
    }

}

.status-unpaid {
    background: #ff9800;
    color: white;
    padding: 6px 14px;
    border-radius: 20px;
    font-size: 0.75rem;
}

</style>

</head>

<body>

<!-- SIDEBAR -->

<aside class="sidebar">

  <div class="sidebar-logo">
    <img src="../IMAGES/logo.jpg" alt="Cubiertos logo">
    <h2>Cubiertos</h2>
  </div>

  <ul class="sidebar-menu">

    <li>
      <a href="admin.php">
        <span class="nav-icon"></span>
        <span class="nav-label">Dashboard</span>
      </a>
    </li>

    <li class="active">
      <a href="revenue.php">
        <span class="nav-icon"></span>
        <span class="nav-label">Revenue</span>
      </a>
    </li>

    <li>
      <a href="calendar.php">
        <span class="nav-icon"></span>
        <span class="nav-label">Calendar</span>
      </a>
    </li>

    <li>
      <a href="customer.php">
        <span class="nav-icon"></span>
        <span class="nav-label">Customers</span>
      </a>
    </li>

    <li>
  <a href="payment_admin.php">
    <span class="nav-icon"></span>
    <span class="nav-label">Payments</span></a>
  </li>

    <li>
      <a href="report.php">
        <span class="nav-icon"></span>
        <span class="nav-label">Reports</span>
      </a>
    </li>

  </ul>

  <div class="sidebar-footer">
    <p>Cubiertos Food Hub &copy; 2025</p>
  </div>

</aside>

<!-- HEADER -->

<header id="adminHeader">

    <div class="logo">

        <h1>
            <span>Revenue</span> Dashboard
        </h1>

    </div>

    <nav>


        <a href="logout.php"
        class="btn logout">
            Logout
        </a>

    </nav>

</header>

<!-- MAIN -->

<main class="dashboard-container">

<section class="dashboard-overview">

    <div class="overview-title">

        <h2>Revenue Overview</h2>

        <p>
            Track all earnings and completed transactions
        </p>

    </div>

    <!-- CARDS -->

    <div class="revenue-grid">

        <div class="revenue-card">

            <h3>Total Revenue</h3>

            <p>
                ₱<?php echo number_format($totalRevenue); ?>
            </p>

        </div>

        <div class="revenue-card">

            <h3>Monthly Revenue</h3>

            <p>
                ₱<?php echo number_format($monthlyRevenue); ?>
            </p>

        </div>

        <div class="revenue-card">

            <h3>Completed Bookings</h3>

            <p>
                <?php echo $totalCompleted; ?>
            </p>

        </div>

    </div>

    <!-- PIE CHART -->

    <div class="revenue-chart-card">

        <div class="chart-header">

            <h2>Revenue Analytics</h2>

            <p>Booking Revenue Distribution</p>

        </div>

        <div class="chart-container">

            <canvas id="revenueChart"></canvas>

        </div>

    </div>

    <!-- TABLE -->

    <!-- TABLE -->
<div class="revenue-table">

    <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:25px; flex-wrap:wrap; gap:12px;">
        <h2 style="margin:0;">Recent Transactions</h2>
        <div style="position:relative;">
            <span style="position:absolute;left:10px;top:50%;transform:translateY(-50%);color:#aaa;font-size:14px;">⌕</span>
            <input
                type="text"
                id="searchTransactions"
                placeholder="Search customer…"
                style="padding:9px 14px 9px 32px; border:1px solid #e0e0e0; border-radius:10px; font-size:0.875rem; outline:none; width:220px; font-family:inherit;"
            >
        </div>
    </div>

    <table>
        <thead>
            <tr>
                <th>#</th>
                <th>Customer</th>
                <th>Service</th>
                <th>Payment Method</th>
                <th>Amount</th>
                <th>Date</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody id="transactionTableBody"></tbody>
    </table>

</div>

</section>

</main>

<!-- =========================
     CHART JS
========================= -->

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>

// ── PIE CHART ──────────────────────────────────
const completedRevenue  = <?php echo $completedRevenue; ?>;
const pendingRevenue    = <?php echo $pendingRevenue; ?>;
const approvedRevenue   = <?php echo $approvedRevenue; ?>;
const cancelledRevenue  = <?php echo $cancelledRevenue; ?>;

const ctx = document.getElementById("revenueChart");
new Chart(ctx, {
    type: "pie",
    data: {
        labels: ["Completed","Pending","Approved","Cancelled"],
        datasets: [{
            data: [completedRevenue, pendingRevenue, approvedRevenue, cancelledRevenue],
            backgroundColor: ["#4caf50","#ff9800","#2196f3","#f44336"],
            borderWidth: 0
        }]
    },
    options: {
        responsive: true,
        plugins: { legend: { position: "bottom" } }
    }
});

// ── TRANSACTIONS TABLE ─────────────────────────
const allTransactions = [
    <?php
    // Re-run query to get all rows as JSON
$allPayments = mysqli_query($conn,"
    SELECT name, occasion, payment_method, amount, booking_datetime, payment_status
    FROM bookings
    WHERE status='Completed'
    ORDER BY booking_datetime DESC
");
    $rows = [];
    while($r = mysqli_fetch_assoc($allPayments)){
    $rows[] = json_encode([
    'name'           => $r['name'],
    'occasion'       => $r['occasion'],
    'payment_method' => $r['payment_method'],
    'amount'         => $r['amount'],
    'date'           => date("M d, Y", strtotime($r['booking_datetime'])),
    'payment_status' => $r['payment_status'] ?? 'Pending'
]);
    }
    echo implode(",\n    ", $rows);
    ?>
];

function renderTransactions() {
    const tbody      = document.getElementById("transactionTableBody");
    const search     = document.getElementById("searchTransactions").value.toLowerCase().trim();
    const isSearching = search !== "";

    tbody.innerHTML = "";

    let displayCount = 0;
    let rowNumber    = 0;

    allTransactions.forEach((t) => {
        if (!t.name.toLowerCase().includes(search)) return;

        rowNumber++;

        // Limit to 10 only when NOT searching
        if (!isSearching && displayCount >= 10) return;

        displayCount++;

        const tr = document.createElement("tr");
        tr.innerHTML = `
            <td>${rowNumber}</td>
            <td>${t.name}</td>
            <td>${t.occasion}</td>
            <td>${t.payment_method}</td>
            <td>₱${Number(t.amount).toLocaleString()}</td>
            <td>${t.date}</td>
            <td>
    <span class="${t.payment_status === 'Confirmed' ? 'status-paid' : 'status-unpaid'}">
        ${t.payment_status === 'Confirmed' ? 'Paid' : 'Unpaid'}
    </span>
</td>
        `;
        tbody.appendChild(tr);
    });

    if (displayCount === 0) {
        tbody.innerHTML = `
            <tr>
                <td colspan="7" style="text-align:center;padding:24px;color:#aaa;">
                    No transactions found.
                </td>
            </tr>`;
    }
}

// Search listener
document.getElementById("searchTransactions").addEventListener("input", renderTransactions);

// Initial render
renderTransactions();

</script>
</body>
</html>