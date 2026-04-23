<?php include 'views/layout/header.php'; ?>

<div class="page-header">
    <h1><i class="fas fa-map-marked-alt"></i> Track Ride</h1>
    <p>Live ride tracking and details</p>
</div>

<?php if (!$ride): ?>
    <div class="alert alert-error"><i class="fas fa-exclamation-circle"></i> Ride not found. <a href="index.php?page=my_rides" class="auth-link">Back to My Rides</a></div>
<?php else: ?>

<div class="booking-wrap">
    <!-- Map -->
    <div>
        <div class="map-box" style="height:420px;">
            <?php
            $pickup_enc = urlencode($ride['pickup_location'].' Goa India');
            $drop_enc   = urlencode($ride['dropoff_location'].' Goa India');
            $map_src    = "https://maps.google.com/maps?q={$pickup_enc}&output=embed";
            ?>
            <iframe src="<?php echo $map_src; ?>" allowfullscreen loading="lazy"></iframe>
        </div>
        <div style="text-align:center; margin-top:10px; font-size:0.8rem; color:var(--text-muted);">
            <i class="fas fa-info-circle"></i> Map shows pickup area. Live driver tracking requires GPS integration.
        </div>
    </div>

    <!-- Info Sidebar -->
    <div>
        <div class="ride-info-card" style="margin-bottom:14px;">
            <h4><i class="fas fa-route"></i> Journey</h4>
            <div class="info-row"><i class="fas fa-circle" style="color:var(--neon-green); font-size:0.7rem;"></i><span><?php echo htmlspecialchars($ride['pickup_location']); ?></span></div>
            <div class="info-row"><i class="fas fa-flag-checkered" style="color:var(--neon-pink);"></i><span><?php echo htmlspecialchars($ride['dropoff_location']); ?></span></div>
            <div class="info-row"><i class="fas fa-car"></i><span>Type: <?php echo strtoupper(htmlspecialchars($ride['ride_type'])); ?></span></div>
            <div class="info-row"><i class="fas fa-rupee-sign" style="color:var(--neon-yellow);"></i><span>₹<?php echo number_format(floatval($ride['fare']),0); ?></span></div>
        </div>

        <?php if ($ride['driver_name']): ?>
        <div class="ride-info-card" style="margin-bottom:14px;">
            <h4><i class="fas fa-user"></i> Driver</h4>
            <div class="info-row"><i class="fas fa-id-badge"></i><span><?php echo htmlspecialchars($ride['driver_name']); ?></span></div>
            <?php if ($ride['driver_phone']): ?>
            <div class="info-row"><i class="fas fa-phone"></i><span><?php echo htmlspecialchars($ride['driver_phone']); ?></span></div>
            <?php endif; ?>
            <?php if ($ride['vehicle_model']): ?>
            <div class="info-row"><i class="fas fa-car"></i><span><?php echo htmlspecialchars($ride['vehicle_model']); ?> · <?php echo htmlspecialchars($ride['vehicle_color']); ?></span></div>
            <div class="info-row"><i class="fas fa-id-card"></i><span><?php echo htmlspecialchars($ride['vehicle_reg']); ?></span></div>
            <?php endif; ?>
        </div>
        <?php endif; ?>

        <div class="ride-info-card" style="margin-bottom:14px;">
            <h4><i class="fas fa-info-circle"></i> Status</h4>
            <span class="status-badge status-<?php echo htmlspecialchars($ride['status']); ?>" style="font-size:0.9rem; padding:8px 18px;">
                <?php echo strtoupper(htmlspecialchars($ride['status'])); ?>
            </span>
        </div>

        <div style="display:flex; flex-direction:column; gap:10px;">
            <?php if ($ride['status'] === 'completed' && !$already_rated): ?>
            <a href="index.php?page=rate_ride&ride_id=<?php echo $ride['ride_id']; ?>" class="btn btn-primary">
                <i class="fas fa-star"></i> Rate This Ride
            </a>
            <?php endif; ?>
            <a href="index.php?page=complaint&ride_id=<?php echo $ride['ride_id']; ?>" class="btn btn-danger">
                <i class="fas fa-flag"></i> File Complaint
            </a>
            <a href="index.php?page=my_rides" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> My Rides
            </a>
        </div>
    </div>
</div>
<?php endif; ?>

<?php include 'views/layout/footer.php'; ?>
