<?php include 'views/layout/header.php'; ?>

<div class="page-header">
    <h1><i class="fas fa-tachometer-alt"></i> Admin Dashboard</h1>
    <p>Chal Ya Patrao control panel</p>
</div>

<div class="admin-stats">
    <div class="stat-card">
        <div class="stat-value"><?php echo count($users); ?></div>
        <div class="stat-label"><i class="fas fa-users"></i> Total Users</div>
    </div>
    <div class="stat-card">
        <div class="stat-value"><?php echo count($drivers); ?></div>
        <div class="stat-label"><i class="fas fa-car"></i> Total Drivers</div>
    </div>
    <div class="stat-card">
        <div class="stat-value"><?php echo count($rides); ?></div>
        <div class="stat-label"><i class="fas fa-route"></i> Total Rides</div>
    </div>
    <div class="stat-card">
        <div class="stat-value" style="color:var(--neon-green);">
            ₹<?php echo number_format(array_sum(array_column($rides,'fare')),0); ?>
        </div>
        <div class="stat-label"><i class="fas fa-rupee-sign"></i> Total Revenue</div>
    </div>
    <div class="stat-card">
        <div class="stat-value" style="color:var(--neon-yellow);">
            <?php echo count(array_filter($drivers, fn($d)=>$d['approval_status']==='pending')); ?>
        </div>
        <div class="stat-label"><i class="fas fa-clock"></i> Pending Drivers</div>
    </div>
    <div class="stat-card">
        <div class="stat-value" style="color:var(--neon-pink);">
            <?php echo count(array_filter($rides, fn($r)=>$r['status']==='pending')); ?>
        </div>
        <div class="stat-label"><i class="fas fa-hourglass"></i> Pending Rides</div>
    </div>
</div>

<!-- ✅ PENDING RIDE REQUESTS NOTIFICATION TABLE -->
<?php if (!empty($pending_requests)): ?>
<div style="margin-bottom:30px;">
    <h2 class="section-title" style="font-size:1.2rem; margin-bottom:16px; text-align:left;">
        <i class="fas fa-bell" style="color:var(--neon-yellow);"></i>
        Pending Ride Requests
        <span style="background:var(--neon-pink); color:#fff; font-size:0.75rem;
              padding:2px 8px; border-radius:20px; margin-left:8px;">
            <?php echo count($pending_requests); ?>
        </span>
    </h2>
    <div class="table-wrap">
        <table class="data-table">
            <thead>
                <tr>
                    <th>Req#</th>
                    <th>Ride#</th>
                    <th>User</th>
                    <th>Phone</th>
                    <th>Pickup</th>
                    <th>Drop</th>
                    <th>Type</th>
                    <th>Fare</th>
                    <th>Driver</th>
                    <th>Received</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($pending_requests as $req): ?>
                <tr>
                    <td>#<?php echo intval($req['request_id']); ?></td>
                    <td>#<?php echo intval($req['ride_id']); ?></td>
                    <td><?php echo htmlspecialchars($req['user_name']); ?></td>
                    <td><?php echo htmlspecialchars($req['user_phone']); ?></td>
                    <td><?php echo htmlspecialchars($req['pickup_location']); ?></td>
                    <td><?php echo htmlspecialchars($req['dropoff_location']); ?></td>
                    <td><?php echo strtoupper(htmlspecialchars($req['ride_type'])); ?></td>
                    <td style="color:var(--neon-yellow);">
                        ₹<?php echo number_format(floatval($req['fare']),0); ?>
                    </td>
                    <td><?php echo htmlspecialchars($req['driver_name']); ?></td>
                    <td style="font-size:0.82rem; color:var(--text-secondary);">
                        <?php echo date('d M, g:ia', strtotime($req['notified_at'])); ?>
                    </td>
                    <td>
                        <span style="background:var(--neon-yellow); color:#000; font-size:0.75rem;
                              padding:3px 10px; border-radius:20px; font-weight:700;">
                            AWAITING DRIVER
                        </span>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>
<?php else: ?>
<div style="margin-bottom:30px; padding:16px 20px; background:var(--bg-card);
     border:1px solid var(--border-color); border-radius:12px; color:var(--text-secondary);
     font-size:0.9rem;">
    <i class="fas fa-check-circle" style="color:var(--neon-green);"></i>
    No pending ride requests right now.
</div>
<?php endif; ?>

<!-- EXISTING GRID -->
<div style="display:grid; grid-template-columns:1fr 1fr; gap:20px; align-items:start;">
    <div>
        <h2 class="section-title" style="font-size:1.2rem; margin-bottom:16px; text-align:left;">
            Recent Rides
        </h2>
        <div class="table-wrap">
            <table class="data-table">
                <thead>
                    <tr><th>#</th><th>User</th><th>Type</th><th>Fare</th><th>Status</th></tr>
                </thead>
                <tbody>
                    <?php foreach (array_slice($rides,0,10) as $r): ?>
                    <tr>
                        <td>#<?php echo intval($r['ride_id']); ?></td>
                        <td><?php echo htmlspecialchars($r['user_name']); ?></td>
                        <td><?php echo strtoupper(htmlspecialchars($r['ride_type'])); ?></td>
                        <td style="color:var(--neon-yellow);">
                            ₹<?php echo number_format(floatval($r['fare']),0); ?>
                        </td>
                        <td>
                            <span class="status-badge status-<?php echo htmlspecialchars($r['status']); ?>">
                                <?php echo htmlspecialchars($r['status']); ?>
                            </span>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
    <div>
        <h2 class="section-title" style="font-size:1.2rem; margin-bottom:16px; text-align:left;">
            Quick Actions
        </h2>
        <div style="display:flex; flex-direction:column; gap:12px;">
            <a href="index.php?page=admin_users"      class="btn btn-primary"><i class="fas fa-users"></i> Manage Users</a>
            <a href="index.php?page=admin_drivers"    class="btn btn-primary"><i class="fas fa-id-card"></i> Manage Drivers</a>
            <a href="index.php?page=admin_complaints" class="btn btn-primary"><i class="fas fa-flag"></i> View Complaints</a>
            <a href="index.php?page=admin_create"     class="btn btn-secondary"><i class="fas fa-user-shield"></i> Add Admin</a>
        </div>
    </div>
</div>

<style>
@media(max-width:768px){
    div[style*="grid-template-columns:1fr 1fr"]{grid-template-columns:1fr !important;}
}
</style>

<?php include 'views/layout/footer.php'; ?>