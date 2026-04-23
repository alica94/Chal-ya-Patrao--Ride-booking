<?php include 'views/layout/header.php'; ?>

<div class="page-header">
    <h1><i class="fas fa-list"></i> My Rides</h1>
    <p>All your bookings — check driver request status here</p>
</div>

<?php if (empty($bookings)): ?>
    <div class="alert alert-info">
        <i class="fas fa-info-circle"></i> No rides yet.
        <a href="index.php?page=booking" class="auth-link">Book one now!</a>
    </div>
<?php else: ?>

<!-- Legend -->
<div style="display:flex; flex-wrap:wrap; gap:8px; margin-bottom:20px; font-size:0.82rem; color:var(--text-secondary);">
    <span><span class="status-badge status-open">pending</span> = Waiting for driver response</span>
    <span><span class="status-badge status-accepted">accepted</span> = Driver confirmed</span>
    <span><span class="status-badge status-completed">completed</span> = Ride done</span>
    <span><span class="status-badge status-cancelled">cancelled / rejected</span> = Choose another driver</span>
</div>

<div class="table-wrap">
    <table class="data-table">
        <thead>
            <tr>
                <th>#</th><th>Pickup</th><th>Drop</th><th>Type</th>
                <th>Pet</th><th>Fare</th><th>Driver</th>
                <th>Request Status</th><th>Ride Status</th><th>Date</th><th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($bookings as $b):
                $req_status = $b['request_status'] ?? null;
                $has_req    = !empty($b['requested_driver_id']);
            ?>
            <tr>
                <td><strong>#<?php echo intval($b['ride_id']); ?></strong></td>
                <td style="font-size:0.83rem;"><?php echo htmlspecialchars($b['pickup_location']); ?></td>
                <td style="font-size:0.83rem;"><?php echo htmlspecialchars($b['dropoff_location']); ?></td>
                <td><span class="driver-tag" style="font-size:0.72rem;"><?php echo strtoupper(htmlspecialchars($b['ride_type'])); ?></span></td>
                <td><?php echo $b['pet_friendly_required'] ? '<span style="color:var(--neon-green); font-size:1rem;" title="Pet-friendly ride">🐾</span>' : '—'; ?></td>
                <td style="color:var(--neon-yellow); font-weight:700;">₹<?php echo number_format(floatval($b['fare']),0); ?></td>
                <td style="font-size:0.85rem;">
                    <?php if ($b['driver_name']): ?>
                        <?php echo htmlspecialchars($b['driver_name']); ?>
                    <?php elseif ($b['driver_assign_mode'] === 'auto'): ?>
                        <span style="color:var(--text-muted);">Auto-searching…</span>
                    <?php else: ?>
                        <span style="color:var(--text-muted);">Awaiting</span>
                    <?php endif; ?>
                </td>

                <!-- Driver Request Status -->
                <td>
                    <?php if (!$has_req): ?>
                        <?php if ($b['driver_assign_mode'] === 'auto'): ?>
                            <span class="driver-tag" style="font-size:0.72rem;"><i class="fas fa-magic"></i> Auto</span>
                        <?php else: ?>
                            <span style="color:var(--text-muted); font-size:0.8rem;">—</span>
                        <?php endif; ?>
                    <?php elseif ($req_status === 'pending'): ?>
                        <span class="status-badge status-open" style="font-size:0.72rem;">
                            <i class="fas fa-clock"></i> Awaiting driver
                        </span>
                    <?php elseif ($req_status === 'accepted'): ?>
                        <span class="status-badge status-accepted" style="font-size:0.72rem;">
                            <i class="fas fa-check"></i> Driver accepted
                        </span>
                    <?php elseif ($req_status === 'rejected'): ?>
                        <span class="status-badge status-cancelled" style="font-size:0.72rem;">
                            <i class="fas fa-times"></i> Rejected
                        </span>
                        <br>
                        <!-- ✅ Pass ride_id so we can restore session data -->
                        <a href="index.php?page=reselect_driver&ride_id=<?php echo intval($b['ride_id']); ?>"
                           class="auth-link" style="font-size:0.75rem;">
                            Choose another →
                        </a>
                    <?php endif; ?>
                </td>

                <td><span class="status-badge status-<?php echo htmlspecialchars($b['status']); ?>"><?php echo htmlspecialchars($b['status']); ?></span></td>
                <td style="font-size:0.78rem; color:var(--text-secondary);">
                    <?php echo date('d M y, g:ia', strtotime($b['booked_at'])); ?>
                </td>
                <td>
                    <div style="display:flex; gap:5px; flex-wrap:wrap;">
                        <a href="index.php?page=track_ride&ride_id=<?php echo $b['ride_id']; ?>"
                           class="btn btn-secondary" style="padding:5px 9px; font-size:0.75rem;"
                           title="Track"><i class="fas fa-map"></i></a>
                        <?php if ($b['status'] === 'completed'): ?>
                        <a href="index.php?page=rate_ride&ride_id=<?php echo $b['ride_id']; ?>"
                           class="btn btn-secondary" style="padding:5px 9px; font-size:0.75rem; border-color:var(--neon-yellow); color:var(--neon-yellow);"
                           title="Rate"><i class="fas fa-star"></i></a>
                        <?php endif; ?>
                        <a href="index.php?page=complaint&ride_id=<?php echo $b['ride_id']; ?>"
                           class="btn btn-danger" style="padding:5px 9px; font-size:0.75rem;"
                           title="Complaint"><i class="fas fa-flag"></i></a>
                    </div>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>
<?php endif; ?>

<?php include 'views/layout/footer.php'; ?>