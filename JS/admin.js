// ================= GLOBAL STATE =================
let orders         = [];
let upcomingEvents = [];
let notificationHistory = JSON.parse(sessionStorage.getItem("notifHistory") || "[]");

const ordersTable = document.querySelector("#ordersTable tbody");

// ================= FETCH BOOKINGS FROM DATABASE =================
async function fetchOrders() {
  try {
    const response = await fetch("admin_bookings.php?action=list");
    const data = await response.json();

    if (data.success) {
      orders         = data.bookings       || [];
      upcomingEvents = data.upcomingEvents || [];

      loadOrders();
      renderUpcomingEvents();
      checkEventNotifications();
    } else {
      console.error("Failed to fetch bookings:", data);
      const container = document.getElementById("upcomingEventsContainer");
      if (container) {
        container.innerHTML = `
          <div class="events-empty">
            <div class="events-empty-icon"></div>
            <p>Could not load bookings.<br>Check that admin_bookings.php is reachable.</p>
          </div>`;
      }
    }
  } catch (error) {
    console.error("Fetch error:", error);
    const container = document.getElementById("upcomingEventsContainer");
    if (container) {
      container.innerHTML = `
        <div class="events-empty">
          <div class="events-empty-icon"></div>
          <p>Connection error:<br>${error.message}</p>
        </div>`;
    }
  }
}

// ================= LOAD ORDERS =================
function loadOrders() {
  if (!ordersTable) return;

  ordersTable.innerHTML = "";

  let totalRevenue = 0;
  let pending      = 0;
  let approved     = 0;
  let completed    = 0;
  let cancelled    = 0;
  let totalGuests  = 0;

  const searchValue = document.getElementById("searchOrder")?.value.toLowerCase() || "";
  const filterValue = document.getElementById("filterStatus")?.value || "all";

  let displayIndex = 0;

  orders.forEach((order) => {
    if (order.name && !order.name.toLowerCase().includes(searchValue)) return;

    if (order.status === "Pending")   pending++;
    if (order.status === "Approved")  approved++;
    if (order.status === "Completed") {
      completed++;
      totalRevenue += Number(order.amount || 0);
    }
    if (order.status === "Cancelled") cancelled++;

    if (order.status === "Pending" || order.status === "Approved") {
      if (order.booking_datetime) {
        const today       = new Date();
        const bookingDate = parseBookingDate(order.booking_datetime);
        if (bookingDate && today.toDateString() === bookingDate.toDateString()) {
          totalGuests += Number(order.guests || 0);
        }
      }
    }

    if (filterValue !== "all" && order.status !== filterValue) return;
    if (order.status === "Completed" && filterValue !== "Completed") return;

    let actionButtons = "";

    if (order.status === "Pending") {
      actionButtons = `
        <button class="btn-action btn-approve"
                onclick="approveOrder(${order.id})"
                title="Approve this booking">
          Approve
        </button>
        <button class="btn-action btn-complete btn-disabled"
                disabled
                title="You must approve the booking before completing it">
          Complete
        </button>
        <button class="btn-action btn-cancel"
                onclick="cancelOrder(${order.id})"
                title="Cancel this booking">
          Cancel
        </button>`;

    } else if (order.status === "Approved") {
      actionButtons = `
        <button class="btn-action btn-complete"
                onclick="completeOrder(${order.id})"
                title="Mark this booking as completed">
          Complete
        </button>
        <button class="btn-action btn-cancel"
                onclick="cancelOrder(${order.id})"
                title="Cancel this booking">
          Cancel
        </button>`;

    } else {
      actionButtons = `<span class="btn-action btn-done">—</span>`;
    }

    displayIndex++;

    const row = document.createElement("tr");
    row.innerHTML = `
      <td>${displayIndex}</td>
      <td>${order.name || "—"}</td>
      <td>${order.phone || "—"}<br><small>${order.email || ""}</small></td>
      <td>${order.occasion || "—"}</td>
      <td>${order.guests || 0} pax</td>
      <td>₱${Number(order.amount || 0).toLocaleString()}</td>
      <td>${order.payment_method || "—"}</td>
      <td>
        <span class="status ${order.status.toLowerCase()}">
          ${order.status}
        </span>
      </td>
      <td class="action-cell">${actionButtons}</td>
    `;

    ordersTable.appendChild(row);
  });

  if (displayIndex === 0) {
    const emptyRow = document.createElement("tr");
    emptyRow.innerHTML = `
      <td colspan="9" style="text-align:center; padding:24px; color:#999;">
        ${filterValue === "Completed"
          ? "No completed bookings found."
          : "No active bookings found."}
      </td>`;
    ordersTable.appendChild(emptyRow);
  }

  // ================= DASHBOARD STATS =================
  const _el = (id) => document.getElementById(id);
  if (_el("ov_totalBookings"))     _el("ov_totalBookings").textContent     = orders.length;
  if (_el("ov_pendingBookings"))   _el("ov_pendingBookings").textContent   = pending;
  if (_el("ov_approvedBookings"))  _el("ov_approvedBookings").textContent  = approved;
  if (_el("ov_completedBookings")) _el("ov_completedBookings").textContent = completed;
  if (_el("ov_cancelledBookings")) _el("ov_cancelledBookings").textContent = cancelled;
  if (_el("ov_totalRevenue"))      _el("ov_totalRevenue").textContent      = totalRevenue.toLocaleString();

  if (_el("ov_pendingFlow"))   _el("ov_pendingFlow").textContent   = pending;
  if (_el("ov_approvedFlow"))  _el("ov_approvedFlow").textContent  = approved;
  if (_el("ov_completedFlow")) _el("ov_completedFlow").textContent = completed;

  // ================= CAPACITY =================
  let maxCap = parseInt(localStorage.getItem("dailyCapacity")) || 100;
  if (_el("maxCapacity"))    _el("maxCapacity").textContent    = maxCap;
  if (_el("currentBooked"))  _el("currentBooked").textContent  = totalGuests;
  if (_el("slotsAvailable")) _el("slotsAvailable").textContent = maxCap - totalGuests;

  const fillEl = _el("capacityFill");
  if (fillEl) {
    let percentage = (totalGuests / maxCap) * 100;
    if (percentage > 100) percentage = 100;
    fillEl.style.width = percentage + "%";
  }

  loadSpecialNotes();
}

// ================= UPDATE STATUS =================
async function updateStatus(id, status) {
  try {
    await fetch("admin_bookings.php?action=update_status", {
      method:  "POST",
      headers: { "Content-Type": "application/x-www-form-urlencoded" },
      body:    `id=${id}&status=${status}`,
    });
    fetchOrders();
  } catch (error) {
    console.error(error);
  }
}

// ================= BUTTON FUNCTIONS =================
window.approveOrder = function (id) {
  if (!confirm("Approve this booking?")) return;
  updateStatus(id, "Approved");
};

window.completeOrder = function (id) {
  const order = orders.find(o => String(o.id) === String(id));
  if (!order) return;

  if (order.status !== "Approved") {
    showActionToast(
      "This booking must be Approved before it can be Completed.",
      "warning"
    );
    return;
  }

  if (!confirm("Mark this booking as Completed?")) return;
  updateStatus(id, "Completed");
};

window.cancelOrder = function (id) {
  if (!confirm("Cancel this booking?")) return;
  updateStatus(id, "Cancelled");
};

// ================= TOAST NOTIFICATION =================
function showActionToast(msg, type = "info") {
  document.querySelectorAll(".action-toast").forEach(t => t.remove());

  const toast = document.createElement("div");
  toast.className   = "action-toast action-toast--" + type;
  toast.textContent = msg;
  document.body.appendChild(toast);
  setTimeout(() => toast.remove(), 4000);
}

// ================= SEARCH/FILTER =================
document.getElementById("searchOrder")?.addEventListener("input",  loadOrders);
document.getElementById("filterStatus")?.addEventListener("change", loadOrders);

// ================= CAPACITY =================
window.setNewLimit = function () {
  let currentLimit = parseInt(localStorage.getItem("dailyCapacity")) || 100;
  let input        = prompt("Enter new maximum guests per day:", currentLimit);
  if (input !== null) {
    let newLimit = parseInt(input);
    if (!isNaN(newLimit) && newLimit > 0) {
      localStorage.setItem("dailyCapacity", newLimit);
      loadOrders();
    }
  }
};

// ================= CALENDAR =================
let nav = 0;

function renderCalendar() {
  const calendarGrid = document.getElementById("calendarGrid");
  const monthDisplay = document.getElementById("monthDisplay");
  if (!calendarGrid || !monthDisplay) return;

  const dt = new Date();
  if (nav !== 0) dt.setMonth(new Date().getMonth() + nav);

  const month           = dt.getMonth();
  const year            = dt.getFullYear();
  const firstDayOfMonth = new Date(year, month, 1);
  const daysInMonth     = new Date(year, month + 1, 0).getDate();
  const paddingDays     = firstDayOfMonth.getDay();

  monthDisplay.textContent = dt.toLocaleDateString("en-us", {
    month: "long",
    year:  "numeric",
  });

  calendarGrid.innerHTML = "";

  const ordersByDate = {};
  orders.forEach((order) => {
    if (order.booking_datetime) {
      const orderDate = parseBookingDate(order.booking_datetime);
      if (!orderDate) return;
      const dateKey = `${orderDate.getFullYear()}-${orderDate.getMonth()}-${orderDate.getDate()}`;
      if (!ordersByDate[dateKey]) ordersByDate[dateKey] = [];
      ordersByDate[dateKey].push(order);
    }
  });

  for (let i = 1; i <= paddingDays + daysInMonth; i++) {
    const daySquare = document.createElement("div");
    if (i > paddingDays) {
      const dayNumber        = i - paddingDays;
      daySquare.textContent  = dayNumber;
      const currentSquareKey = `${year}-${month}-${dayNumber}`;
      const daysBookings     = ordersByDate[currentSquareKey];
      if (daysBookings && daysBookings.length > 0) {
        const indicator       = document.createElement("div");
        indicator.textContent = `${daysBookings.length}`;
        indicator.style.fontSize  = "12px";
        indicator.style.marginTop = "5px";
        daySquare.appendChild(indicator);
      }
    }
    calendarGrid.appendChild(daySquare);
  }
}

// ================= HELPERS =================
function parseBookingDate(str) {
  if (!str) return null;
  const fixed = str.replace(" ", "T");
  const dt    = new Date(fixed);
  return isNaN(dt.getTime()) ? null : dt;
}

// ================= UPCOMING EVENTS PANEL =================
function renderUpcomingEvents() {
  const container = document.getElementById("upcomingEventsContainer");
  if (!container) return;

  console.log("upcomingEvents received:", upcomingEvents);

  const upcoming = upcomingEvents
    .filter(o => {
      if (!o.booking_datetime) return false;
      if (o.status === "Completed" || o.status === "Cancelled") return false;
      const dt = parseBookingDate(o.booking_datetime);
      return dt !== null;
    })
    .sort((a, b) => parseBookingDate(a.booking_datetime) - parseBookingDate(b.booking_datetime));

  console.log("Filtered upcoming count:", upcoming.length);

  const badge = document.getElementById("upcomingBadge");
  if (badge) {
    badge.textContent   = upcoming.length;
    badge.style.display = upcoming.length > 0 ? "flex" : "none";
  }

  container.innerHTML = "";

  if (upcoming.length === 0) {
    container.innerHTML = `
      <div class="events-empty">
        <div class="events-empty-icon"></div>
        <p>No upcoming events in the next 7 days</p>
      </div>`;
    return;
  }

  const now = new Date();

  upcoming.forEach(order => {
    const dt        = parseBookingDate(order.booking_datetime);
    const diffMs    = dt - now;
    const diffMins  = Math.round(diffMs / 60000);
    const diffHours = Math.round(diffMs / 3600000);

    let timeLabel  = "";
    let timeClass  = "";
    let eventState = "upcoming";

    if (diffMins < 0) {
      timeLabel  = `Started ${Math.abs(diffMins)} min ago`;
      timeClass  = "time-past";
      eventState = "ongoing";
    } else if (diffMins <= 30) {
      timeLabel  = diffMins <= 5 ? "Starting now!" : `In ${diffMins} min`;
      timeClass  = "time-imminent";
      eventState = "starting-soon";
    } else if (diffHours < 24) {
      timeLabel  = `In ${diffHours}h ${diffMins % 60}m`;
      timeClass  = "time-today";
      eventState = "today";
    } else {
      const days = Math.ceil(diffMs / (1000 * 60 * 60 * 24));
      timeLabel  = `In ${days} day${days > 1 ? "s" : ""}`;
      timeClass  = "time-future";
      eventState = "upcoming";
    }

    const formattedDate = dt.toLocaleDateString("en-PH", {
      weekday: "short", month: "short", day: "numeric"
    });
    const formattedTime = dt.toLocaleTimeString("en-PH", {
      hour: "2-digit", minute: "2-digit"
    });

    const card = document.createElement("div");
    card.className = `event-card event-state-${eventState}`;
    card.innerHTML = `
      <div class="event-card-header">
        <div class="event-time-badge ${timeClass}">${timeLabel}</div>
        <span class="event-status-dot status-dot-${order.status.toLowerCase()}"></span>
      </div>
      <div class="event-card-body">
        <div class="event-name">${order.name || "—"}</div>
        <div class="event-occasion">${order.occasion || "General Event"}</div>
        <div class="event-meta">
          <span>${order.guests || 0} guests</span>
          <span>₱${Number(order.amount || 0).toLocaleString()}</span>
        </div>
        <div class="event-datetime">
          <span class="event-date-str">${formattedDate}</span>
          <span class="event-time-str"> ${formattedTime}</span>
        </div>
      </div>
      <div class="event-card-footer">
        <span class="event-booking-status ${order.status.toLowerCase()}">${order.status}</span>
        ${order.status === "Pending"  ? `<button class="evt-btn evt-approve"  onclick="approveOrder(${order.id})">Approve</button>`  : ""}
        ${order.status === "Approved" ? `<button class="evt-btn evt-complete" onclick="completeOrder(${order.id})">Complete</button>` : ""}
      </div>
    `;
    container.appendChild(card);
  });
}

// ================= EVENT NOTIFICATIONS =================
function checkEventNotifications() {
  const now = new Date();

  orders.forEach(order => {
    if (!order.booking_datetime) return;
    if (order.status === "Cancelled") return;

    const dt       = parseBookingDate(order.booking_datetime);
    if (!dt) return;
    const diffMins = (dt - now) / 60000;

    // --- FIX: Notify immediately for every new Pending booking ---
    const keyNew = `notif-new-${order.id}`;
    if (order.status === "Pending" && !notificationHistory.includes(keyNew)) {
      notificationHistory.push(keyNew);
      sessionStorage.setItem("notifHistory", JSON.stringify(notificationHistory));
      addNotification({
        type:    "info",
        icon:    "🗓️",
        title:   "New Booking Request",
        message: `${order.name} — ${order.occasion || "booking"} on ${dt.toLocaleDateString("en-PH", { month: "short", day: "numeric" })}`,
        orderId: order.id,
      });
    }

    const key30   = `notif-30-${order.id}`;
    const key0    = `notif-0-${order.id}`;
    const keyDone = `notif-done-${order.id}`;

    if (diffMins > 0 && diffMins <= 30 && !notificationHistory.includes(key30)) {
      notificationHistory.push(key30);
      sessionStorage.setItem("notifHistory", JSON.stringify(notificationHistory));
      addNotification({
        type:    "warning",
        icon:    "⏰",
        title:   "Event Starting Soon",
        message: `${order.name}'s ${order.occasion || "booking"} starts in ~${Math.round(diffMins)} min`,
        orderId: order.id,
      });
    }

    if (diffMins >= -5 && diffMins <= 5 && !notificationHistory.includes(key0)) {
      notificationHistory.push(key0);
      sessionStorage.setItem("notifHistory", JSON.stringify(notificationHistory));
      addNotification({
        type:    "info",
        icon:    "🎉",
        title:   "Event Starting Now",
        message: `${order.name}'s ${order.occasion || "event"} is beginning now!`,
        orderId: order.id,
      });
    }

    if (diffMins <= -60 && order.status === "Approved" && !notificationHistory.includes(keyDone)) {
      notificationHistory.push(keyDone);
      sessionStorage.setItem("notifHistory", JSON.stringify(notificationHistory));
      addNotification({
        type:    "success",
        icon:    "✅",
        title:   "Event Likely Finished",
        message: `${order.name}'s event may have ended. Mark as Completed?`,
        orderId: order.id,
        action:  { label: "Mark Complete", fn: () => completeOrder(order.id) },
      });
    }
  });
}

function addNotification({ type, icon, title, message, orderId, action }) {
  const panel = document.getElementById("notifList");
  if (!panel) return;

  const placeholder = panel.querySelector(".notif-placeholder");
  if (placeholder) placeholder.remove();

  updateNotifBadge(1);

  const item = document.createElement("div");
  item.className       = `notif-item notif-${type}`;
  item.dataset.orderId = orderId;
  item.innerHTML = `
    <div class="notif-icon">${icon || ""}</div>
    <div class="notif-body">
      <div class="notif-title">${title}</div>
      <div class="notif-msg">${message}</div>
      <div class="notif-time">${new Date().toLocaleTimeString("en-PH", { hour: "2-digit", minute: "2-digit" })}</div>
      ${action ? `<button class="notif-action-btn">${action.label}</button>` : ""}
    </div>
    <button class="notif-dismiss" onclick="dismissNotif(this)" title="Dismiss">✕</button>
  `;

  if (action) {
    const btn = item.querySelector(".notif-action-btn");
    if (btn) btn.addEventListener("click", action.fn);
  }

  panel.prepend(item);
  showEventToast({ type, icon, title, message });
}

let notifCount = 0;
function updateNotifBadge(delta) {
  notifCount = Math.max(0, notifCount + delta);
  const badge = document.getElementById("notifBadge");
  if (!badge) return;
  badge.textContent   = notifCount;
  badge.style.display = notifCount > 0 ? "flex" : "none";
}

window.dismissNotif = function (btn) {
  const item = btn.closest(".notif-item");
  if (item) {
    item.style.animation = "notifFadeOut 0.3s ease forwards";
    setTimeout(() => {
      item.remove();
      updateNotifBadge(-1);
      const panel = document.getElementById("notifList");
      if (panel && panel.children.length === 0) {
        panel.innerHTML = `<div class="notif-placeholder">No new notifications</div>`;
      }
    }, 300);
  }
};

// --- FIX: clearAllNotifications also resets sessionStorage ---
window.clearAllNotifications = function () {
  const panel = document.getElementById("notifList");
  if (!panel) return;
  panel.innerHTML = `<div class="notif-placeholder">No new notifications</div>`;
  notifCount = 0;
  notificationHistory = [];
  sessionStorage.removeItem("notifHistory");
  updateNotifBadge(0);
};

function showEventToast({ type, icon, title, message }) {
  const toast = document.createElement("div");
  toast.className = `event-toast event-toast--${type}`;
  toast.innerHTML = `
    <div class="et-icon">${icon || ""}</div>
    <div class="et-content">
      <strong>${title}</strong>
      <span>${message}</span>
    </div>
    <button onclick="this.parentElement.remove()">✕</button>
  `;
  document.body.appendChild(toast);
  setTimeout(() => {
    toast.style.animation = "toastSlideOut 0.4s ease forwards";
    setTimeout(() => toast.remove(), 400);
  }, 6000);
}

// --- FIX: toggleNotifPanel accepts event to stop propagation ---
window.toggleNotifPanel = function (e) {
  if (e) e.stopPropagation();
  const panel = document.getElementById("notifDropdown");
  if (!panel) return;
  panel.classList.toggle("open");
};

window.toggleEventsPanel = function () {
  const panel = document.getElementById("eventsPanel");
  if (!panel) return;
  panel.classList.toggle("collapsed");
};

// --- FIX: close notif panel only when clicking truly outside bell + dropdown ---
document.addEventListener("click", function (e) {
  const dropdown = document.getElementById("notifDropdown");
  const bell     = document.getElementById("notifBell");
  if (
    dropdown &&
    dropdown.classList.contains("open") &&
    !dropdown.contains(e.target) &&
    bell &&
    !bell.contains(e.target)
  ) {
    dropdown.classList.remove("open");
  }
});

// ================= SPECIAL NOTES =================
function loadSpecialNotes() {
  const container = document.getElementById("notesContainer");
  if (!container) return;

  const search = document.getElementById("searchNotes")?.value.toLowerCase() || "";

  const notesData = orders.filter(o =>
    o.special_notes &&
    o.special_notes.trim() !== "" &&
    o.status !== "Completed" &&
    o.status !== "Cancelled"
  );

  container.innerHTML = "";

  if (notesData.length === 0) {
    container.innerHTML = `<p class="empty-note">No special notes available.</p>`;
    return;
  }

  let hasResult = false;

  notesData.forEach((note) => {
    const customer = (note.name          || "").toLowerCase();
    const message  = (note.special_notes || "").toLowerCase();
    if (!customer.includes(search) && !message.includes(search)) return;

    hasResult = true;

    const completeBtn = note.status === "Approved"
      ? `<button
             class="note-complete-btn"
             onclick="completeFromNotes(${note.id}, this)"
             title="Mark as completed">
           Complete
         </button>`
      : `<span class="note-status-tag note-status-${note.status.toLowerCase()}">${note.status}</span>`;

    const noteEl      = document.createElement("div");
    noteEl.className  = "note-box";
    noteEl.dataset.id = note.id;
    noteEl.innerHTML  = `
      <div class="note-top">
        <h3>${note.name}</h3>
        <span>${new Date(note.booking_datetime.replace(" ", "T")).toLocaleString()}</span>
      </div>
      <div class="note-body">
        <p><strong>Occasion:</strong> ${note.occasion || "—"}</p>
        <p><strong>Guests:</strong> ${note.guests || 0}</p>
        <p><strong>Status:</strong> ${note.status || "Pending"}</p>
        <div class="special-message">
          ${note.special_notes || "No special note"}
        </div>
      </div>
      <div class="note-footer">
        ${completeBtn}
      </div>`;

    container.appendChild(noteEl);
  });

  if (!hasResult) {
    container.innerHTML = `<p class="empty-note">No matching notes found.</p>`;
  }
}

// ================= COMPLETE FROM NOTES =================
window.completeFromNotes = async function (id, btn) {
  if (!confirm("Mark this booking as Completed?")) return;

  btn.disabled    = true;
  btn.textContent = "Processing...";

  try {
    const res  = await fetch("admin_bookings.php?action=complete_from_notes", {
      method:  "POST",
      headers: { "Content-Type": "application/x-www-form-urlencoded" },
      body:    `id=${id}`
    });
    const data = await res.json();

    if (data.success) {
      const order = orders.find(o => String(o.id) === String(id));
      if (order) order.status = "Completed";

      const noteBox = btn.closest(".note-box");
      if (noteBox) {
        noteBox.style.transition = "opacity 0.4s ease, transform 0.4s ease";
        noteBox.style.opacity    = "0";
        noteBox.style.transform  = "scale(0.95)";
        setTimeout(() => {
          noteBox.remove();
          const container = document.getElementById("notesContainer");
          if (container && container.querySelectorAll(".note-box").length === 0) {
            container.innerHTML = `<p class="empty-note">No special notes available.</p>`;
          }
        }, 400);
      }

      showActionToast("Booking marked as completed.", "success");
      loadOrders();
    } else {
      btn.disabled    = false;
      btn.textContent = "Complete";
      showActionToast(data.message, "warning");
    }
  } catch (err) {
    btn.disabled    = false;
    btn.textContent = "Complete";
    showActionToast("Connection error.", "warning");
  }
};

document.getElementById("searchNotes")?.addEventListener("input", loadSpecialNotes);

// ================= CLEANUP =================
window.cleanupAll = async function () {
  const confirmed = confirm(
    "This will permanently delete ALL cancelled bookings.\n\nThis cannot be undone. Continue?"
  );
  if (!confirmed) return;

  try {
    const res  = await fetch("cleanup_old_bookings.php?action=delete_cancelled");
    const data = await res.json();

    if (data.success) {
      showActionToast(
        `${data.deleted} cancelled booking(s) permanently deleted.`,
        data.deleted > 0 ? "success" : "info"
      );
      orders = [];
      await fetchOrders();
    } else {
      showActionToast("Failed to delete cancelled bookings.", "warning");
    }
  } catch (err) {
    console.warn("Cleanup error:", err);
    showActionToast("Connection error during cleanup.", "warning");
  }
};

// Auto-cleanup: runs silently once per session (30-day old records only)
if (!sessionStorage.getItem("cleanupDone")) {
  sessionStorage.setItem("cleanupDone", "true");
  fetch("cleanup_old_bookings.php?action=auto")
    .then(r => r.json())
    .then(data => {
      if (data.deleted > 0) {
        console.log(`Auto-cleanup: ${data.deleted} old record(s) removed.`);
        fetchOrders();
      }
    })
    .catch(err => console.warn("Auto-cleanup error:", err));
}

// ================= START =================
fetchOrders();
setInterval(fetchOrders, 60000);

// ================= LOADER (RUN ONCE PER SESSION) =================
window.addEventListener("load", function () {
  const loader = document.getElementById("startup-loader");
  if (!loader) return;

  if (!sessionStorage.getItem("hasSeenLoader")) {
    loader.style.display = "flex";
    sessionStorage.setItem("hasSeenLoader", "true");
    setTimeout(() => { loader.style.display = "none"; }, 2000);
  } else {
    loader.style.display = "none";
  }
});