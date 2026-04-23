<?php include 'views/layout/header.php'; ?>

<div style="max-width:500px; margin:40px auto; text-align:center;">
    <div class="otp-card">
        <div style="font-size:4rem; color:var(--neon-green); text-shadow:0 0 30px var(--neon-green); margin-bottom:20px; animation:pulse 1.5s infinite;">
            <i class="fas fa-check-circle"></i>
        </div>
        <h2 style="font-size:1.8rem; font-weight:800; margin-bottom:10px;">Ride Confirmed!</h2>
        <p style="color:var(--text-secondary); margin-bottom:20px;">Your booking is confirmed and your driver is on the way. Have a safe ride!</p>

        <?php if (isset($_SESSION['last_ride_id'])): ?>
        <div class="alert alert-success" style="margin-bottom:20px;">
            <i class="fas fa-ticket-alt"></i>
            Booking ID: <strong>#<?php echo intval($_SESSION['last_ride_id']); ?></strong>
        </div>
        <?php endif; ?>

        <div style="display:flex; flex-direction:column; gap:12px;">
            <a href="index.php?page=my_rides" class="btn btn-primary btn-full">
                <i class="fas fa-list"></i> View My Rides
            </a>
            <?php if (isset($_SESSION['last_ride_id'])): ?>
            <a href="index.php?page=track_ride&ride_id=<?php echo intval($_SESSION['last_ride_id']); ?>" class="btn btn-secondary btn-full">
                <i class="fas fa-map-marked-alt"></i> Track My Ride
            </a>
            <?php endif; ?>
            <a href="index.php?page=booking" class="btn btn-secondary btn-full">
                <i class="fas fa-plus"></i> Book Another Ride
            </a>
        </div>
    </div>
</div>

<style>
@keyframes pulse {
    0%,100% { transform:scale(1); }
    50% { transform:scale(1.08); }
}
</style>

<?php include 'views/layout/footer.php'; ?>
