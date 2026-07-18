<!-- Dashboard Overview -->

<!-- Stat Cards Row -->
<div class="row row-cards">
    <div class="col-sm-6 col-lg-3">
        <div class="card stat-card">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="subheader">Total Users</div>
                    <div class="ms-auto lh-1">
                        <i class="ti ti-users text-muted" style="font-size: 1.5rem;"></i>
                    </div>
                </div>
                <div class="h1 mb-2 mt-2">12,487</div>
                <div class="d-flex align-items-baseline">
                    <span class="text-success fw-semibold d-inline-flex align-items-center lh-1">
                        <i class="ti ti-trending-up me-1"></i> +12.5%
                    </span>
                    <span class="text-muted ms-2">vs last month</span>
                </div>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-lg-3">
        <div class="card stat-card">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="subheader">Revenue</div>
                    <div class="ms-auto lh-1">
                        <i class="ti ti-currency-dollar text-muted" style="font-size: 1.5rem;"></i>
                    </div>
                </div>
                <div class="h1 mb-2 mt-2">$48,295</div>
                <div class="d-flex align-items-baseline">
                    <span class="text-success fw-semibold d-inline-flex align-items-center lh-1">
                        <i class="ti ti-trending-up me-1"></i> +8.2%
                    </span>
                    <span class="text-muted ms-2">vs last month</span>
                </div>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-lg-3">
        <div class="card stat-card">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="subheader">Active Sessions</div>
                    <div class="ms-auto lh-1">
                        <i class="ti ti-device-analytics text-muted" style="font-size: 1.5rem;"></i>
                    </div>
                </div>
                <div class="h1 mb-2 mt-2">1,893</div>
                <div class="d-flex align-items-baseline">
                    <span class="text-success fw-semibold d-inline-flex align-items-center lh-1">
                        <i class="ti ti-trending-up me-1"></i> +3.1%
                    </span>
                    <span class="text-muted ms-2">vs last month</span>
                </div>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-lg-3">
        <div class="card stat-card">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="subheader">Pending Orders</div>
                    <div class="ms-auto lh-1">
                        <i class="ti ti-shopping-cart text-muted" style="font-size: 1.5rem;"></i>
                    </div>
                </div>
                <div class="h1 mb-2 mt-2">342</div>
                <div class="d-flex align-items-baseline">
                    <span class="text-danger fw-semibold d-inline-flex align-items-center lh-1">
                        <i class="ti ti-trending-down me-1"></i> -2.4%
                    </span>
                    <span class="text-muted ms-2">vs last month</span>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Charts & Activity Row -->
<div class="row row-cards mt-3">
    <!-- Revenue Chart Card -->
    <div class="col-lg-8">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Revenue Overview</h3>
                <div class="card-actions">
                    <div class="btn-group">
                        <button type="button" class="btn btn-outline-primary btn-sm active">Week</button>
                        <button type="button" class="btn btn-outline-primary btn-sm">Month</button>
                        <button type="button" class="btn btn-outline-primary btn-sm">Year</button>
                    </div>
                </div>
            </div>
            <div class="card-body">
                <div class="chart" style="min-height: 250px;">
                    <div id="chart-revenue" style="height: 100%;"></div>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Activity -->
    <div class="col-lg-4">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Recent Activity</h3>
                <div class="card-actions">
                    <a href="#" class="btn btn-sm btn-link">View all</a>
                </div>
            </div>
            <div class="card-body">
                <div class="divide-y">
                    <div class="row">
                        <div class="col-auto">
                            <span class="avatar" style="background-image: url(https://ui-avatars.com/api/?name=John+Doe&background=206bc4&color=fff)"></span>
                        </div>
                        <div class="col">
                            <div class="text-truncate">
                                <strong>John Doe</strong> created a new invoice
                            </div>
                            <div class="text-muted small">2 minutes ago</div>
                        </div>
                    </div>
                    <div class="row mt-3">
                        <div class="col-auto">
                            <span class="avatar" style="background-image: url(https://ui-avatars.com/api/?name=Jane+Smith&background=5eba00&color=fff)"></span>
                        </div>
                        <div class="col">
                            <div class="text-truncate">
                                <strong>Jane Smith</strong> completed the project
                            </div>
                            <div class="text-muted small">15 minutes ago</div>
                        </div>
                    </div>
                    <div class="row mt-3">
                        <div class="col-auto">
                            <span class="avatar" style="background-image: url(https://ui-avatars.com/api/?name=Mike+Johnson&background=f48024&color=fff)"></span>
                        </div>
                        <div class="col">
                            <div class="text-truncate">
                                <strong>Mike Johnson</strong> uploaded 3 files
                            </div>
                            <div class="text-muted small">1 hour ago</div>
                        </div>
                    </div>
                    <div class="row mt-3">
                        <div class="col-auto">
                            <span class="avatar" style="background-image: url(https://ui-avatars.com/api/?name=Sara+Wilson&background=7c3aed&color=fff)"></span>
                        </div>
                        <div class="col">
                            <div class="text-truncate">
                                <strong>Sara Wilson</strong> updated the settings
                            </div>
                            <div class="text-muted small">2 hours ago</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Recent Orders Table -->
<div class="row row-cards mt-3">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Recent Orders</h3>
                <div class="card-actions">
                    <a href="<?= $basePath ?? '' ?>/tables" class="btn btn-sm btn-link">View all orders</a>
                </div>
            </div>
            <div class="table-responsive">
                <table class="table table-vcenter card-table">
                    <thead>
                        <tr>
                            <th>Order ID</th>
                            <th>Customer</th>
                            <th>Product</th>
                            <th>Amount</th>
                            <th>Status</th>
                            <th class="w-1"></th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td><span class="text-muted">#ORD-001</span></td>
                            <td>John Doe</td>
                            <td>Wireless Headphones</td>
                            <td>$129.99</td>
                            <td><span class="badge bg-success">Completed</span></td>
                            <td><a href="#" class="btn btn-sm btn-outline-primary">View</a></td>
                        </tr>
                        <tr>
                            <td><span class="text-muted">#ORD-002</span></td>
                            <td>Jane Smith</td>
                            <td>USB-C Hub</td>
                            <td>$49.99</td>
                            <td><span class="badge bg-warning">Pending</span></td>
                            <td><a href="#" class="btn btn-sm btn-outline-primary">View</a></td>
                        </tr>
                        <tr>
                            <td><span class="text-muted">#ORD-003</span></td>
                            <td>Mike Johnson</td>
                            <td>Mechanical Keyboard</td>
                            <td>$89.99</td>
                            <td><span class="badge bg-success">Completed</span></td>
                            <td><a href="#" class="btn btn-sm btn-outline-primary">View</a></td>
                        </tr>
                        <tr>
                            <td><span class="text-muted">#ORD-004</span></td>
                            <td>Sara Wilson</td>
                            <td>27" Monitor</td>
                            <td>$349.99</td>
                            <td><span class="badge bg-danger">Cancelled</span></td>
                            <td><a href="#" class="btn btn-sm btn-outline-primary">View</a></td>
                        </tr>
                        <tr>
                            <td><span class="text-muted">#ORD-005</span></td>
                            <td>Alex Brown</td>
                            <td>Webcam HD</td>
                            <td>$79.99</td>
                            <td><span class="badge bg-warning">Pending</span></td>
                            <td><a href="#" class="btn btn-sm btn-outline-primary">View</a></td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Simple Chart Script (using the canvas API for a lightweight inline chart) -->
<script>
    document.addEventListener('DOMContentLoaded', function() {
        var ctx = document.getElementById('chart-revenue');
        if (!ctx) return;
        
        var canvas = document.createElement('canvas');
        canvas.style.width = '100%';
        canvas.style.height = '100%';
        ctx.appendChild(canvas);
        
        var rect = ctx.getBoundingClientRect();
        canvas.width = rect.width * 2;
        canvas.height = rect.height * 2;
        canvas.style.width = rect.width + 'px';
        canvas.style.height = rect.height + 'px';
        
        var c = canvas.getContext('2d');
        var w = canvas.width;
        var h = canvas.height;
        
        // Data points (simulated weekly revenue)
        var data = [3200, 4100, 3800, 5200, 4800, 5900, 6500, 6100, 7200, 6800, 7900, 8500];
        var labels = ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri'];
        
        var padding = 60;
        var chartW = w - padding * 2;
        var chartH = h - padding * 2;
        var maxVal = Math.max(...data) * 1.1;
        
        // Draw grid lines
        c.strokeStyle = 'rgba(255, 255, 255, 0.05)';
        c.lineWidth = 1;
        for (var i = 0; i <= 4; i++) {
            var y = padding + (chartH / 4) * i;
            c.beginPath();
            c.moveTo(padding, y);
            c.lineTo(w - padding, y);
            c.stroke();
            
            // Labels
            c.fillStyle = 'rgba(255, 255, 255, 0.4)';
            c.font = '11px Inter, sans-serif';
            c.textAlign = 'right';
            c.fillText('$' + Math.round(maxVal - (maxVal / 4) * i), padding - 10, y + 4);
        }
        
        // Draw gradient area
        var gradient = c.createLinearGradient(0, padding, 0, h - padding);
        gradient.addColorStop(0, 'rgba(32, 107, 196, 0.3)');
        gradient.addColorStop(1, 'rgba(32, 107, 196, 0)');
        
        c.beginPath();
        data.forEach(function(val, i) {
            var x = padding + (chartW / (data.length - 1)) * i;
            var y = padding + chartH - (val / maxVal) * chartH;
            if (i === 0) c.moveTo(x, y);
            else c.lineTo(x, y);
        });
        data.slice().reverse().forEach(function(val, i) {
            var x = padding + (chartW / (data.length - 1)) * (data.length - 1 - i);
            var y = padding + chartH;
            c.lineTo(x, y);
        });
        c.closePath();
        c.fillStyle = gradient;
        c.fill();
        
        // Draw line
        c.beginPath();
        c.strokeStyle = '#206bc4';
        c.lineWidth = 3;
        c.lineJoin = 'round';
        c.lineCap = 'round';
        data.forEach(function(val, i) {
            var x = padding + (chartW / (data.length - 1)) * i;
            var y = padding + chartH - (val / maxVal) * chartH;
            if (i === 0) c.moveTo(x, y);
            else c.lineTo(x, y);
        });
        c.stroke();
        
        // Draw dots
        data.forEach(function(val, i) {
            var x = padding + (chartW / (data.length - 1)) * i;
            var y = padding + chartH - (val / maxVal) * chartH;
            c.beginPath();
            c.arc(x, y, 5, 0, Math.PI * 2);
            c.fillStyle = '#206bc4';
            c.fill();
            c.strokeStyle = '#fff';
            c.lineWidth = 2;
            c.stroke();
        });
    });
</script>
