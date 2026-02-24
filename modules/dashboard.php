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
    <div class="col-md-6">
        <div class="content-card">
            <h5 class="mb-4"><i class="fas fa-chart-pie me-2"></i> Status Kamar</h5>
            <div style="position: relative; height: 300px;">
                <canvas id="roomChart"></canvas>
            </div>
        </div>
    </div>
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
                        $complaints = mysqli_query($conn, "SELECT c.*, t.full_name FROM complaints c 
                            JOIN tenants t ON c.tenant_id = t.id 
                            ORDER BY c.created_at DESC LIMIT 5");
                        
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
                        <?php 
                            endwhile;
                        else:
                        ?>
                        <tr>
                            <td colspan="4" class="text-center text-muted py-3">
                                <i class="fas fa-check-circle me-2"></i>Tidak ada komplain terbaru
                            </td>
                        </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
(function() {
    // Debug: Check if data exists
    var availableRooms = <?= (int)($stats['available_rooms'] ?? 0) ?>;
    var occupiedRooms = <?= (int)($stats['occupied_rooms'] ?? 0) ?>;
    var totalRooms = <?= (int)($stats['total_rooms'] ?? 0) ?>;
    var maintenanceRooms = totalRooms - availableRooms - occupiedRooms;
    
    console.log('Room Stats:', {
        available: availableRooms,
        occupied: occupiedRooms,
        maintenance: maintenanceRooms,
        total: totalRooms
    });
    
    // Get canvas context
    var canvas = document.getElementById('roomChart');
    if (!canvas) {
        console.error('Canvas element not found!');
        return;
    }
    
    var ctx = canvas.getContext('2d');
    
    // Create chart
    var roomChart = new Chart(ctx, {
        type: 'doughnut',
         {
            labels: ['Tersedia', 'Terisi', 'Maintenance'],
            datasets: [{
                 [availableRooms, occupiedRooms, maintenanceRooms],
                backgroundColor: [
                    '#28a745', // Green - Available
                    '#dc3545', // Red - Occupied
                    '#ffc107'  // Yellow - Maintenance
                ],
                borderWidth: 2,
                borderColor: '#ffffff',
                hoverOffset: 4
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'bottom',
                    labels: {
                        padding: 15,
                        usePointStyle: true,
                        boxWidth: 10,
                        font: {
                            size: 11
                        }
                    }
                },
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            var label = context.label || '';
                            var value = context.parsed || 0;
                            var total = context.dataset.data.reduce((a, b) => a + b, 0);
                            var percentage = total > 0 ? Math.round((value / total) * 100) : 0;
                            return label + ': ' + value + ' kamar (' + percentage + '%)';
                        }
                    }
                }
            },
            cutout: '70%',
            animation: {
                animateRotate: true,
                animateScale: true
            }
        }
    });
    
    console.log('Chart created successfully!');
})();
</script>