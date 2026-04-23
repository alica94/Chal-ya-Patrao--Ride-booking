<?php include 'views/layout/header.php'; ?>
<div class="page-header"><h1><i class="fas fa-times-circle"></i> Cancel Ride</h1></div>
<?php if (!empty($error)): ?><div class="alert alert-error"><i class="fas fa-exclamation-circle"></i> <?php echo htmlspecialchars($error); ?></div><?php endif; ?>
<?php if (!empty($success)): ?><div class="alert alert-success"><i class="fas fa-check-circle"></i> <?php echo htmlspecialchars($success); ?></div><?php endif; ?>
<div style="max-width:420px; margin:0 auto;" class="booking-card">
    <h3 style="margin-bottom:20px; font-size:1.1rem;">Enter your Ride ID to cancel</h3>
    <form method="POST" action="index.php?page=cancel">
        <div class="form-group">
            <label><i class="fas fa-hashtag"></i> Ride ID</label>
            <input type="number" name="ride_id" placeholder="e.g. 42" required min="1">
        </div>
        <button type="submit" name="cancel_ride" class="btn btn-danger btn-full"><i class="fas fa-times"></i> Cancel This Ride</button>
    </form>
    <div style="margin-top:16px; text-align:center;"><a href="index.php?page=my_rides" class="auth-link"><i class="fas fa-list"></i> View My Rides</a></div>
</div>
<?php include 'views/layout/footer.php'; ?>
