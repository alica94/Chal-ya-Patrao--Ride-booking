<?php include 'views/layout/header.php'; ?>

<div style="max-width:520px; margin:0 auto;">
    <div class="page-header">
        <h1><i class="fas fa-star" style="color:var(--neon-yellow);"></i> Rate Your Ride</h1>
        <p>Your feedback helps us improve</p>
    </div>

    <?php if (!empty($error)): ?><div class="alert alert-error"><i class="fas fa-exclamation-circle"></i> <?php echo htmlspecialchars($error); ?></div><?php endif; ?>
    <?php if (!empty($success)): ?>
        <div class="alert alert-success"><i class="fas fa-check-circle"></i> <?php echo htmlspecialchars($success); ?></div>
        <div style="text-align:center; margin-top:16px;">
            <a href="index.php?page=my_rides" class="btn btn-primary"><i class="fas fa-list"></i> Back to My Rides</a>
        </div>
    <?php else: ?>

    <?php if ($ride): ?>
    <div class="booking-card">
        <div style="margin-bottom:20px; padding-bottom:16px; border-bottom:1px solid var(--border-color);">
            <div class="info-row"><i class="fas fa-circle" style="color:var(--neon-green); font-size:0.7rem;"></i><span><?php echo htmlspecialchars($ride['pickup_location']); ?></span></div>
            <div class="info-row"><i class="fas fa-flag-checkered" style="color:var(--neon-pink);"></i><span><?php echo htmlspecialchars($ride['dropoff_location']); ?></span></div>
            <div class="info-row"><i class="fas fa-user"></i><span>Driver: <?php echo htmlspecialchars($ride['driver_name'] ?? 'Unknown'); ?></span></div>
        </div>

        <form method="POST" action="index.php?page=rate_ride&ride_id=<?php echo $ride['ride_id']; ?>">
            <input type="hidden" name="ride_id" value="<?php echo $ride['ride_id']; ?>">
            <input type="hidden" name="driver_id" value="<?php echo intval($ride['driver_id']); ?>">

            <div class="form-group" style="text-align:center;">
                <label style="justify-content:center; font-size:1rem;">Rate the Ride</label>
                <div class="star-rating" id="starRating" style="justify-content:center; font-size:2.5rem; margin-top:10px;">
                    <i class="fas fa-star" data-v="1" onclick="setStars(1)" onmouseover="hoverStars(1)" onmouseout="resetStars()"></i>
                    <i class="fas fa-star" data-v="2" onclick="setStars(2)" onmouseover="hoverStars(2)" onmouseout="resetStars()"></i>
                    <i class="fas fa-star" data-v="3" onclick="setStars(3)" onmouseover="hoverStars(3)" onmouseout="resetStars()"></i>
                    <i class="fas fa-star" data-v="4" onclick="setStars(4)" onmouseover="hoverStars(4)" onmouseout="resetStars()"></i>
                    <i class="fas fa-star" data-v="5" onclick="setStars(5)" onmouseover="hoverStars(5)" onmouseout="resetStars()"></i>
                </div>
                <input type="hidden" name="stars" id="starsInput" value="0">
            </div>

            <div class="form-group">
                <label><i class="fas fa-comment"></i> Feedback (optional)</label>
                <textarea name="feedback_text" rows="4" placeholder="Tell us about your experience..."></textarea>
            </div>

            <button type="submit" name="submit_rating" class="btn btn-primary btn-full" id="submitRating" disabled>
                <i class="fas fa-paper-plane"></i> Submit Rating
            </button>
        </form>
    </div>
    <?php endif; ?>
    <?php endif; ?>
</div>

<script>
let chosen = 0;
function setStars(n) {
    chosen = n;
    document.getElementById('starsInput').value = n;
    document.getElementById('submitRating').disabled = false;
    renderStars(n);
}
function hoverStars(n) { renderStars(n); }
function resetStars() { renderStars(chosen); }
function renderStars(n) {
    document.querySelectorAll('#starRating i').forEach((el,i) => {
        el.style.color = i < n ? 'var(--neon-yellow)' : 'var(--border-color)';
    });
}
</script>

<?php include 'views/layout/footer.php'; ?>
