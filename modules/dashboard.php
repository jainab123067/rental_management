<!-- Tambahkan di atas dashboard -->
<?php
$pendingPayments = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as count FROM payments WHERE status = 'Pending'"))['count'];
$upcomingBookings = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as count FROM bookings WHERE status = 'Pending'"))['count'];
$openComplaints = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as count FROM complaints WHERE status IN ('Open', 'In Progress')"))['count'];
?>

<?php if ($pendingPayments > 0 || $upcomingBookings > 0 || $openComplaints > 0): ?>
<div class="row g-3 mb-4">
    <?php if ($pendingPayments > 0): ?>
    <div class="col-md-4">
        <div class="alert alert-warning d-flex align-items-center mb-0">
            <i class="fas fa-exclamation-triangle fa-2x me-3"></i>
            <div>
                <strong><?= $pendingPayments ?> Pembayaran Pending</strong>
                <br><small>Perlu ditindaklanjuti</small>
            </div>
        </div>
    </div>
    <?php endif; ?>
    
    <?php if ($upcomingBookings > 0): ?>
    <div class="col-md-4">
        <div class="alert alert-info d-flex align-items-center mb-0">
            <i class="fas fa-calendar-alt fa-2x me-3"></i>
            <div>
                <strong><?= $upcomingBookings ?> Booking Menunggu</strong>
                <br><small>Perlu konfirmasi</small>
            </div>
        </div>
    </div>
    <?php endif; ?>
    
    <?php if ($openComplaints > 0): ?>
    <div class="col-md-4">
        <div class="alert alert-danger d-flex align-items-center mb-0">
            <i class="fas fa-bell fa-2x me-3"></i>
            <div>
                <strong><?= $openComplaints ?> Komplain Terbuka</strong>
                <br><small>Perlu penanganan</small>
            </div>
        </div>
    </div>
    <?php endif; ?>
</div>
<?php endif; ?>

<!-- Stats Cards -->
<div class="row g-4 mb-4">
    <div class="col-md-3">
        <div class="stat-card">
            <div class="stat-icon bg-gradient-primary">
                <i class="fas fa-door-open"></i>
            </div>
            <div class="stat-title">Total Kamar</div>
            <div class="stat-value"><?= $stats['total_rooms'] ?? 0 ?></div>
            <small class="text-muted"><?= $stats['available_rooms'] ?? 0 ?> Tersedia</small>
        </div>
    </div>
    <div class="col-md-3">
        <div class="stat-card">
            <div class="stat-icon bg-gradient-success">
                <i class="fas fa-users"></i>
            </div>
            <div class="stat-title">Total Penyewa</div>
            <div class="stat-value"><?= $stats['total_tenants'] ?? 0 ?></div>
            <small class="text-muted">Penyewa Aktif</small>
        </div>
    </div>
    <div class="col-md-3">
        <div class="stat-card">
            <div class="stat-icon bg-gradient-warning">
                <i class="fas fa-calendar-check"></i>
            </div>
            <div class="stat-title">Booking Aktif</div>
            <div class="stat-value"><?= $stats['active_bookings'] ?? 0 ?></div>
            <small class="text-muted">Sedang Berjalan</small>
        </div>
    </div>
    <div class="col-md-3">
        <div class="stat-card">
            <div class="stat-icon bg-gradient-info">
                <i class="fas fa-money-bill-wave"></i>
            </div>
            <div class="stat-title">Pendapatan Bulan Ini</div>
            <div class="stat-value">Rp <?= number_format($stats['monthly_revenue'] ?? 0, 0, ',', '.') ?></div>
            <small class="text-muted">Total Pembayaran</small>
        </div>
    </div>
</div>

<div class="row g-4">
    <!-- GRAFIK CSS PURE - PASTI MUNCUL! -->
    <div class="col-md-6">
        <div class="content-card">
            <h5 class="mb-4"><i class="fas fa-chart-bar me-2"></i> Status Kamar</h5>
            
            <?php
            $available = (int)($stats['available_rooms'] ?? 0);
            $occupied = (int)($stats['occupied_rooms'] ?? 0);
            $total = (int)($stats['total_rooms'] ?? 0);
            $maintenance = max(0, $total - $available - $occupied);
            $maxValue = max($available, $occupied, $maintenance, 1);
            ?>
            
            <!-- Bar Chart dengan CSS -->
            <div class="css-bar-chart" style="padding: 20px 0;">
                
                <!-- Available -->
                <div class="bar-item mb-4">
                    <div class="d-flex justify-content-between mb-2">
                        <span class="fw-bold"><i class="fas fa-circle text-success me-2"></i>Tersedia</span>
                        <span class="badge bg-success"><?= $available ?> kamar</span>
                    </div>
                    <div class="progress" style="height: 30px; border-radius: 10px;">
                        <div class="progress-bar bg-success" 
                             style="width: <?= ($available / $maxValue * 100) ?>%; 
                                    background: linear-gradient(90deg, #28a745, #56ab2f) !important;
                                    display: flex; align-items: center; justify-content: center;
                                    font-weight: bold; font-size: 14px;">
                            <?= $available ?>
                        </div>
                    </div>
                </div>
                
                <!-- Occupied -->
                <div class="bar-item mb-4">
                    <div class="d-flex justify-content-between mb-2">
                        <span class="fw-bold"><i class="fas fa-circle text-danger me-2"></i>Terisi</span>
                        <span class="badge bg-danger"><?= $occupied ?> kamar</span>
                    </div>
                    <div class="progress" style="height: 30px; border-radius: 10px;">
                        <div class="progress-bar bg-danger" 
                             style="width: <?= ($occupied / $maxValue * 100) ?>%; 
                                    background: linear-gradient(90deg, #dc3545, #fa709a) !important;
                                    display: flex; align-items: center; justify-content: center;
                                    font-weight: bold; font-size: 14px;">
                            <?= $occupied ?>
                        </div>
                    </div>
                </div>
                
                <!-- Maintenance -->
                <div class="bar-item mb-4">
                    <div class="d-flex justify-content-between mb-2">
                        <span class="fw-bold"><i class="fas fa-circle text-warning me-2"></i>Maintenance</span>
                        <span class="badge bg-warning text-dark"><?= $maintenance ?> kamar</span>
                    </div>
                    <div class="progress" style="height: 30px; border-radius: 10px;">
                        <div class="progress-bar bg-warning" 
                             style="width: <?= ($maintenance / $maxValue * 100) ?>%; 
                                    background: linear-gradient(90deg, #ffc107, #f093fb) !important;
                                    display: flex; align-items: center; justify-content: center;
                                    font-weight: bold; font-size: 14px;">
                            <?= $maintenance ?>
                        </div>
                    </div>
                </div>
                
                <!-- Total -->
                <div class="bar-item mt-4 pt-3 border-top">
                    <div class="d-flex justify-content-between align-items-center">
                        <span class="fw-bold fs-5"><i class="fas fa-layer-group me-2"></i>Total</span>
                        <span class="badge bg-primary fs-6 px-3 py-2"><?= $total ?> kamar</span>
                    </div>
                </div>
                
            </div>
            
        </div>
    </div>
    
    <!-- Komplain Terbaru -->
    <div class="col-md-6">
        <div class="content-card">
            <h5 class="mb-4"><i class="fas fa-clock me-2"></i> Komplain Terbaru</h5>
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>Subjek</th>
                            <th>Priority</th>
                            <th>Status</th>
                            <th>Tanggal</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $complaints = mysqli_query($conn, "SELECT c.*, t.full_name FROM complaints c JOIN tenants t ON c.tenant_id = t.id ORDER BY c.created_at DESC LIMIT 5");
                        if (mysqli_num_rows($complaints) > 0):
                            while ($c = mysqli_fetch_assoc($complaints)):
                                $badgeClass = $c['priority'] == 'High' ? 'danger' : ($c['priority'] == 'Medium' ? 'warning' : 'info');
                        ?>
                        <tr>
                            <td><?= htmlspecialchars($c['subject']) ?></td>
                            <td><span class="badge bg-<?= $badgeClass ?>"><?= $c['priority'] ?></span></td>
                            <td><span class="badge bg-secondary"><?= $c['status'] ?></span></td>
                            <td><?= date('d/m/Y', strtotime($c['created_at'])) ?></td>
                        </tr>
                        <?php endwhile; else: ?>
                        <tr><td colspan="4" class="text-center text-muted py-3">Tidak ada komplain terbaru</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- CSS untuk Bar Chart -->
<style>
.css-bar-chart .bar-item {
    transition: transform 0.3s;
}
.css-bar-chart .bar-item:hover {
    transform: translateX(5px);
}
.css-bar-chart .progress {
    box-shadow: 0 2px 8px rgba(0,0,0,0.1);
    background: #f0f0f0;
}
.css-bar-chart .progress-bar {
    transition: width 1s ease-in-out;
}
</style>