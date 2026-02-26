    <?php
// Calendar View untuk Booking
// Akses: http://localhost/rental_management/modules/bookings/calendar.php

require_once '../../config/database.php';
requireLogin();

// Get year and month from URL or use current
$year = isset($_GET['year']) ? (int)$_GET['year'] : date('Y');
$month = isset($_GET['month']) ? (int)$_GET['month'] : date('m');

// Validate month/year
if ($month < 1) $month = 1;
if ($month > 12) $month = 12;
if ($year < 2000) $year = date('Y');

// Get bookings for selected month
$bookings_query = mysqli_query($conn, "
    SELECT b.*, t.full_name, t.phone, r.room_number, r.room_name 
    FROM bookings b 
    JOIN tenants t ON b.tenant_id = t.id 
    JOIN rooms r ON b.room_id = r.id 
    WHERE YEAR(b.check_in) = $year AND MONTH(b.check_in) = $month
    ORDER BY b.check_in ASC
");

$bookings_by_date = [];
while ($b = mysqli_fetch_assoc($bookings_query)) {
    $date_key = $b['check_in'];
    if (!isset($bookings_by_date[$date_key])) {
        $bookings_by_date[$date_key] = [];
    }
    $bookings_by_date[$date_key][] = $b;
}

// Calendar config
$days_in_month = cal_days_in_month(CAL_GREGORIAN, $month, $year);
$first_day_of_month = date('w', mktime(0, 0, 0, $month, 1, $year)); // 0 = Sunday
$month_name = date('F', mktime(0, 0, 0, $month, 1, $year));

// Navigation
$prev_month = $month - 1;
$prev_year = $year;
if ($prev_month < 1) { $prev_month = 12; $prev_year--; }

$next_month = $month + 1;
$next_year = $year;
if ($next_month > 12) { $next_month = 1; $next_year++; }

// Today's date for highlighting
$today = date('Y-m-d');
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kalender Booking - Rental Management</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <style>
        body {
            background: #f8f9fa;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            padding: 20px;
        }
        .calendar-container {
            max-width: 1400px;
            margin: 0 auto;
            background: white;
            border-radius: 15px;
            box-shadow: 0 5px 20px rgba(0,0,0,0.1);
            padding: 25px;
        }
        .calendar-header {
            text-align: center;
            padding: 20px 0;
            border-bottom: 2px solid #eee;
            margin-bottom: 20px;
        }
        .calendar-header h2 {
            color: #667eea;
            margin: 0;
            font-weight: 700;
        }
        .calendar-nav {
            margin-top: 15px;
        }
        .calendar-nav .btn {
            min-width: 150px;
        }
        .calendar-grid {
            display: grid;
            grid-template-columns: repeat(7, 1fr);
            gap: 8px;
        }
        .calendar-day-header {
            text-align: center;
            font-weight: 600;
            color: #666;
            padding: 10px;
            background: #f8f9fa;
            border-radius: 8px;
            font-size: 14px;
        }
        .calendar-day {
            min-height: 140px;
            border: 1px solid #e0e0e0;
            border-radius: 10px;
            padding: 8px;
            background: white;
            transition: all 0.2s;
            position: relative;
        }
        .calendar-day:hover {
            border-color: #667eea;
            box-shadow: 0 3px 10px rgba(102, 126, 234, 0.2);
        }
        .calendar-day.empty {
            background: #fafafa;
        }
        .calendar-day.today {
            border: 2px solid #667eea;
            background: #f0f4ff;
        }
        .day-number {
            font-weight: 700;
            font-size: 16px;
            color: #333;
            margin-bottom: 5px;
            display: flex;
            align-items: center;
            gap: 5px;
        }
        .calendar-day.today .day-number {
            color: #667eea;
        }
        .booking-item {
            background: linear-gradient(135deg, #667eea, #764ba2);
            color: white;
            padding: 6px 10px;
            margin: 3px 0;
            border-radius: 6px;
            font-size: 11px;
            cursor: pointer;
            transition: transform 0.2s;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }
        .booking-item:hover {
            transform: scale(1.02);
            color: white;
        }
        .booking-item.confirmed {
            background: linear-gradient(135deg, #28a745, #56ab2f);
        }
        .booking-item.checked-in {
            background: linear-gradient(135deg, #17a2b8, #4facfe);
        }
        .booking-item.pending {
            background: linear-gradient(135deg, #ffc107, #f093fb);
            color: #333;
        }
        .booking-item.cancelled {
            background: linear-gradient(135deg, #dc3545, #fa709a);
        }
        .booking-count {
            position: absolute;
            top: 5px;
            right: 8px;
            background: #667eea;
            color: white;
            border-radius: 50%;
            width: 20px;
            height: 20px;
            font-size: 11px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: bold;
        }
        .legend {
            display: flex;
            gap: 15px;
            justify-content: center;
            flex-wrap: wrap;
            margin: 20px 0;
            padding: 15px;
            background: #f8f9fa;
            border-radius: 10px;
        }
        .legend-item {
            display: flex;
            align-items: center;
            gap: 5px;
            font-size: 13px;
        }
        .legend-color {
            width: 15px;
            height: 15px;
            border-radius: 4px;
        }
        .modal-body {
            max-height: 400px;
            overflow-y: auto;
        }
        .booking-detail {
            padding: 10px;
            border-bottom: 1px solid #eee;
        }
        .booking-detail:last-child {
            border-bottom: none;
        }
        .btn-back {
            background: linear-gradient(135deg, #667eea, #764ba2);
            color: white;
            border: none;
        }
        @media (max-width: 768px) {
            .calendar-grid {
                grid-template-columns: repeat(7, 1fr);
                gap: 4px;
            }
            .calendar-day {
                min-height: 100px;
                padding: 5px;
            }
            .day-number {
                font-size: 14px;
            }
            .booking-item {
                font-size: 10px;
                padding: 4px 8px;
            }
        }
    </style>
</head>
<body>

<div class="calendar-container">
    
    <!-- Header -->
    <div class="calendar-header">
        <h2><i class="fas fa-calendar-alt me-2"></i>Kalender Booking</h2>
        <p class="text-muted mb-0"><?= $month_name ?> <?= $year ?></p>
        
        <div class="calendar-nav">
            <a href="?year=<?= $prev_year ?>&month=<?= $prev_month ?>" class="btn btn-outline-primary">
                <i class="fas fa-chevron-left me-2"></i>Bulan Sebelumnya
            </a>
            <a href="?year=<?= date('Y') ?>&month=<?= date('m') ?>" class="btn btn-outline-secondary mx-2">
                <i class="fas fa-calendar-day me-2"></i>Hari Ini
            </a>
            <a href="?year=<?= $next_year ?>&month=<?= $next_month ?>" class="btn btn-outline-primary">
                Bulan Berikutnya <i class="fas fa-chevron-right ms-2"></i>
            </a>
        </div>
        
        <div class="mt-3">
            <a href="../../index.php?page=bookings" class="btn btn-back">
                <i class="fas fa-arrow-left me-2"></i>Kembali ke Booking
            </a>
        </div>
    </div>
    
    <!-- Legend -->
    <div class="legend">
        <div class="legend-item">
            <div class="legend-color" style="background: linear-gradient(135deg, #667eea, #764ba2);"></div>
            <span>Pending</span>
        </div>
        <div class="legend-item">
            <div class="legend-color" style="background: linear-gradient(135deg, #28a745, #56ab2f);"></div>
            <span>Confirmed</span>
        </div>
        <div class="legend-item">
            <div class="legend-color" style="background: linear-gradient(135deg, #17a2b8, #4facfe);"></div>
            <span>Checked In</span>
        </div>
        <div class="legend-item">
            <div class="legend-color" style="background: linear-gradient(135deg, #dc3545, #fa709a);"></div>
            <span>Cancelled</span>
        </div>
    </div>
    
    <!-- Calendar Grid -->
    <div class="calendar-grid">
        <!-- Day Headers -->
        <div class="calendar-day-header">Minggu</div>
        <div class="calendar-day-header">Senin</div>
        <div class="calendar-day-header">Selasa</div>
        <div class="calendar-day-header">Rabu</div>
        <div class="calendar-day-header">Kamis</div>
        <div class="calendar-day-header">Jumat</div>
        <div class="calendar-day-header">Sabtu</div>
        
        <!-- Empty days before first day of month -->
        <?php for ($i = 0; $i < $first_day_of_month; $i++): ?>
            <div class="calendar-day empty"></div>
        <?php endfor; ?>
        
        <!-- Days of month -->
        <?php for ($day = 1; $day <= $days_in_month; $day++): 
            $current_date = date('Y-m-d', mktime(0, 0, 0, $month, $day, $year));
            $is_today = ($current_date == $today);
            $has_bookings = isset($bookings_by_date[$current_date]) && count($bookings_by_date[$current_date]) > 0;
            $booking_count = $has_bookings ? count($bookings_by_date[$current_date]) : 0;
        ?>
            <div class="calendar-day <?= $is_today ? 'today' : '' ?>" 
                 <?= $has_bookings ? 'onclick="showBookings(\'' . $current_date . '\')"' : '' ?>
                 style="<?= $has_bookings ? 'cursor: pointer;' : '' ?>">
                
                <div class="day-number">
                    <?= $day ?>
                    <?php if ($is_today): ?>
                        <i class="fas fa-circle" style="font-size: 8px; color: #667eea;"></i>
                    <?php endif; ?>
                </div>
                
                <?php if ($has_bookings): ?>
                    <div class="booking-count"><?= $booking_count ?></div>
                    
                    <?php foreach (array_slice($bookings_by_date[$current_date], 0, 3) as $booking): 
                        $badge_class = match($booking['status']) {
                            'Confirmed' => 'confirmed',
                            'Checked In' => 'checked-in',
                            'Cancelled' => 'cancelled',
                            default => 'pending'
                        };
                    ?>
                        <div class="booking-item <?= $badge_class ?>" 
                             onclick="event.stopPropagation(); showBookingDetail(<?= $booking['id'] ?>)">
                            <strong><?= htmlspecialchars($booking['full_name']) ?></strong><br>
                            🚪 <?= htmlspecialchars($booking['room_number']) ?>
                        </div>
                    <?php endforeach; ?>
                    
                    <?php if ($booking_count > 3): ?>
                        <div class="booking-item" style="background: #6c757d;" onclick="showBookings('<?= $current_date ?>')">
                            +<?= $booking_count - 3 ?> lainnya
                        </div>
                    <?php endif; ?>
                <?php endif; ?>
                
            </div>
        <?php endfor; ?>
    </div>
    
</div>

<!-- Modal untuk Detail Booking per Tanggal -->
<div class="modal fade" id="bookingsModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="fas fa-calendar-day me-2"></i>
                    Booking: <span id="modalDate"></span>
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" id="modalBody">
                <!-- Content will be loaded here -->
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>

<!-- Modal untuk Detail Single Booking -->
<div class="modal fade" id="bookingDetailModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="fas fa-info-circle me-2"></i>Detail Booking</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" id="bookingDetailBody">
                <!-- Content will be loaded here -->
            </div>
            <div class="modal-footer">
                <a href="#" id="editBookingBtn" class="btn btn-warning btn-sm">
                    <i class="fas fa-edit me-1"></i>Edit
                </a>
                <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>

<!-- Scripts -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
// Data bookings dari PHP
const bookingsData = <?= json_encode($bookings_by_date, JSON_NUMERIC_CHECK | JSON_UNESCAPED_UNICODE) ?>;

// Show all bookings for a date
function showBookings(date) {
    const bookings = bookingsData[date] || [];
    const modalDate = document.getElementById('modalDate');
    const modalBody = document.getElementById('modalBody');
    
    // Format date for display
    const dateObj = new Date(date + 'T00:00:00');
    const options = { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' };
    modalDate.textContent = dateObj.toLocaleDateString('id-ID', options);
    
    if (bookings.length === 0) {
        modalBody.innerHTML = '<p class="text-muted text-center">Tidak ada booking pada tanggal ini.</p>';
    } else {
        let html = '';
        bookings.forEach(booking => {
            const statusBadge = {
                'Pending': 'bg-warning text-dark',
                'Confirmed': 'bg-success',
                'Checked In': 'bg-info',
                'Checked Out': 'bg-secondary',
                'Cancelled': 'bg-danger'
            }[booking.status] || 'bg-secondary';
            
            html += `
                <div class="booking-detail">
                    <div class="d-flex justify-content-between align-items-start mb-2">
                        <strong>${booking.full_name}</strong>
                        <span class="badge ${statusBadge}">${booking.status}</span>
                    </div>
                    <p class="mb-1"><i class="fas fa-door-open me-2"></i>Kamar: ${booking.room_number} - ${booking.room_name}</p>
                    <p class="mb-1"><i class="fas fa-phone me-2"></i>${booking.phone || '-'}</p>
                    <p class="mb-1"><i class="fas fa-tag me-2"></i>Total: Rp ${parseInt(booking.total_price).toLocaleString('id-ID')}</p>
                    <p class="mb-0"><i class="fas fa-sticky-note me-2"></i>${booking.notes || 'Tidak ada catatan'}</p>
                    <div class="mt-2">
                        <a href="../../index.php?page=bookings&action=edit&id=${booking.id}" class="btn btn-sm btn-warning">
                            <i class="fas fa-edit me-1"></i>Edit
                        </a>
                    </div>
                </div>
            `;
        });
        modalBody.innerHTML = html;
    }
    
    new bootstrap.Modal(document.getElementById('bookingsModal')).show();
}

// Show single booking detail
function showBookingDetail(bookingId) {
    // Find booking in all dates
    let booking = null;
    for (const date in bookingsData) {
        const found = bookingsData[date].find(b => b.id == bookingId);
        if (found) {
            booking = found;
            break;
        }
    }
    
    if (!booking) return;
    
    const modalBody = document.getElementById('bookingDetailBody');
    const editBtn = document.getElementById('editBookingBtn');
    
    const statusBadge = {
        'Pending': 'bg-warning text-dark',
        'Confirmed': 'bg-success',
        'Checked In': 'bg-info',
        'Checked Out': 'bg-secondary',
        'Cancelled': 'bg-danger'
    }[booking.status] || 'bg-secondary';
    
    modalBody.innerHTML = `
        <div class="mb-3">
            <label class="text-muted small">PENYEWA</label>
            <p class="mb-0 fw-bold">${booking.full_name}</p>
            <small class="text-muted">${booking.phone || 'No phone'}</small>
        </div>
        <div class="mb-3">
            <label class="text-muted small">KAMAR</label>
            <p class="mb-0">${booking.room_number} - ${booking.room_name}</p>
        </div>
        <div class="mb-3">
            <label class="text-muted small">TANGGAL</label>
            <p class="mb-0">Check-in: ${formatDate(booking.check_in)}</p>
            ${booking.check_out ? `<p class="mb-0">Check-out: ${formatDate(booking.check_out)}</p>` : ''}
        </div>
        <div class="mb-3">
            <label class="text-muted small">STATUS</label>
            <p class="mb-0"><span class="badge ${statusBadge}">${booking.status}</span></p>
        </div>
        <div class="mb-3">
            <label class="text-muted small">TOTAL HARGA</label>
            <p class="mb-0 fw-bold">Rp ${parseInt(booking.total_price).toLocaleString('id-ID')}</p>
        </div>
        ${booking.notes ? `
        <div>
            <label class="text-muted small">CATATAN</label>
            <p class="mb-0">${booking.notes}</p>
        </div>
        ` : ''}
    `;
    
    editBtn.href = `../../index.php?page=bookings&action=edit&id=${booking.id}`;
    
    new bootstrap.Modal(document.getElementById('bookingDetailModal')).show();
}

// Format date helper
function formatDate(dateString) {
    const date = new Date(dateString + 'T00:00:00');
    return date.toLocaleDateString('id-ID', { 
        weekday: 'long', 
        year: 'numeric', 
        month: 'long', 
        day: 'numeric' 
    });
}

// Keyboard navigation
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        bootstrap.Modal.getInstance(document.getElementById('bookingsModal'))?.hide();
        bootstrap.Modal.getInstance(document.getElementById('bookingDetailModal'))?.hide();
    }
});
</script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>