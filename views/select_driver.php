<?php include 'views/layout/header.php'; ?>

<div class="page-header">
    <h1><i class="fas fa-id-card"></i> Choose Your Driver</h1>
    <p>Select a driver — they'll receive a notification and must accept your ride</p>
</div>

<!-- Ride Summary -->
<div class="alert alert-info" style="margin-bottom:24px; flex-wrap:wrap; gap:12px;">
    <i class="fas fa-route"></i>
    <span>
        <strong><?php echo htmlspecialchars($_SESSION['pending_pickup'] ?? '—'); ?></strong>
        &rarr; <strong><?php echo htmlspecialchars($_SESSION['pending_dropoff'] ?? '—'); ?></strong>
        &nbsp;|&nbsp; <?php echo strtoupper($_SESSION['pending_ride_type'] ?? '—'); ?>
        &nbsp;|&nbsp; <strong>₹<?php echo $_SESSION['pending_fare'] ?? '—'; ?></strong>
        <?php if (!empty($_SESSION['pending_pet_friendly'])): ?>
        &nbsp;|&nbsp; <span style="color:var(--neon-green);"><i class="fas fa-paw"></i> Pet-friendly only</span>
        <?php endif; ?>
    </span>
</div>

<!-- How it works -->
<div class="alert alert-info" style="margin-bottom:28px; background:rgba(191,0,255,0.08); border-color:var(--neon-purple); color:var(--neon-purple);">
    <i class="fas fa-bell"></i>
    <span>
        <strong>How it works:</strong> After you confirm payment, the selected driver receives a
        <strong>ride request notification</strong> on their dashboard.
        They can <strong>Accept</strong> or <strong>Reject</strong> the ride.
        You'll see the status update on your <em>My Rides</em> page.
        If rejected, you can choose another driver.
    </span>
</div>

<?php if (!empty($error)): ?>
    <div class="alert alert-error"><i class="fas fa-exclamation-circle"></i> <?php echo htmlspecialchars($error); ?></div>
<?php endif; ?>

<form method="POST" action="index.php?page=select_driver">

    <?php if (!empty($drivers)): ?>
    <div class="driver-cards-grid" id="driverGrid">
        <?php foreach ($drivers as $i => $d): ?>
        <div class="driver-card <?php echo $i===0 ? 'selected' : ''; ?>"
             id="dcard_<?php echo $d['driver_id']; ?>"
             onclick="selectDriver(<?php echo $d['driver_id']; ?>, this)">

            <!-- Online indicator -->
            <div style="position:absolute; top:12px; right:12px;">
                <span style="font-size:0.72rem; font-weight:700;
                    color:<?php echo $d['is_online'] ? 'var(--neon-green)' : 'var(--text-muted)'; ?>;">
                    <?php echo $d['is_online'] ? '● Online' : '○ Offline'; ?>
                </span>
            </div>

            <div class="driver-meta">
                <div class="driver-avatar">
                    <?php echo $d['gender']==='female' ? '<i class="fas fa-female"></i>' : '<i class="fas fa-male"></i>'; ?>
                </div>
                <div class="driver-info">
                    <h3><?php echo htmlspecialchars($d['full_name']); ?></h3>
                    <div class="driver-stars">
                        <?php
                        $st = floatval($d['avg_rating']);
                        for ($s=1; $s<=5; $s++) echo $s<=$st ? '★' : '☆';
                        echo ' '.number_format($st,1);
                        ?>
                    </div>
                </div>
            </div>

            <div class="driver-vehicle-info">
                <span class="driver-tag"><i class="fas fa-car"></i> <?php echo htmlspecialchars($d['model']); ?></span>
                <span class="driver-tag"><i class="fas fa-palette"></i> <?php echo htmlspecialchars($d['color']); ?></span>
                <span class="driver-tag"><i class="fas fa-users"></i> <?php echo htmlspecialchars($d['seats']); ?> seats</span>
                <span class="driver-tag"><?php echo strtoupper(htmlspecialchars($d['car_category'])); ?></span>
                <?php if ($d['is_pet_friendly']): ?>
                <span class="driver-tag" style="color:var(--neon-green); border-color:var(--neon-green);">
                    <i class="fas fa-paw"></i> Pet OK
                </span>
                <?php endif; ?>
            </div>

            <?php if (!$d['is_online']): ?>
            <div style="margin-top:10px; font-size:0.78rem; color:var(--neon-yellow);">
                <i class="fas fa-exclamation-triangle"></i> Driver is currently offline — request may take longer
            </div>
            <?php endif; ?>
        </div>
        <?php endforeach; ?>
    </div>

    <input type="hidden" name="selected_driver_id" id="selectedDriverId"
           value="<?php echo !empty($drivers) ? $drivers[0]['driver_id'] : 0; ?>">

    <?php else: ?>
    <div class="alert alert-error">
        <i class="fas fa-exclamation-triangle"></i>
        <span>
            <?php if (!empty($_SESSION['pending_pet_friendly'])): ?>
                No pet-friendly drivers are available right now. Try removing the pet-friendly filter,
                or use <strong>Auto Assign</strong> and we'll notify you when one is available.
            <?php else: ?>
                No drivers available right now. Please try Auto Assign or check back shortly.
            <?php endif; ?>
        </span>
    </div>
    <input type="hidden" name="selected_driver_id" value="0">
    <?php endif; ?>

    <div style="display:flex; gap:12px; flex-wrap:wrap; margin-top:24px; align-items:center;">
        <a href="index.php?page=booking" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Back
        </a>
        <button type="submit" name="proceed_to_payment" class="btn btn-primary btn-lg">
            <i class="fas fa-bell"></i> Send Request &amp; Proceed to Payment
        </button>
        <span style="font-size:0.82rem; color:var(--text-muted);">
            <i class="fas fa-info-circle"></i> Driver will be notified after payment
        </span>
    </div>
</form>

<script>
function selectDriver(id, card) {
    document.querySelectorAll('.driver-card').forEach(c => c.classList.remove('selected'));
    card.classList.add('selected');
    document.getElementById('selectedDriverId').value = id;
}
</script>

<style>
.driver-card { position: relative; }
</style>

<?php include 'views/layout/footer.php'; ?>
