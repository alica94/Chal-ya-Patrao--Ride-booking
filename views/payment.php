<?php include 'views/layout/header.php'; ?>

<div class="page-header">
    <h1><i class="fas fa-credit-card"></i> Payment</h1>
    <p>Review and confirm your ride</p>
</div>

<?php if (!empty($error)): ?>
    <div class="alert alert-error"><i class="fas fa-exclamation-circle"></i> <?php echo htmlspecialchars($error); ?></div>
<?php endif; ?>

<div style="max-width:560px; margin:0 auto;">
    <div class="booking-card">
        <!-- Ride Details -->
        <div class="booking-section">
            <h2><i class="fas fa-route"></i> Ride Details</h2>
            <div style="display:flex; flex-direction:column; gap:12px; font-size:0.95rem;">
                <div class="info-row">
                    <i class="fas fa-circle" style="color:var(--neon-green); font-size:0.7rem;"></i>
                    <span><strong>Pickup:</strong> <?php echo htmlspecialchars($_SESSION['pending_pickup'] ?? '—'); ?></span>
                </div>
                <div class="info-row">
                    <i class="fas fa-flag-checkered" style="color:var(--neon-pink);"></i>
                    <span><strong>Drop:</strong> <?php echo htmlspecialchars($_SESSION['pending_dropoff'] ?? '—'); ?></span>
                </div>
                <div class="info-row">
                    <i class="fas fa-car" style="color:var(--accent);"></i>
                    <span><strong>Ride Type:</strong> <?php echo strtoupper(htmlspecialchars($_SESSION['pending_ride_type'] ?? '—')); ?></span>
                </div>
                <div class="info-row">
                    <i class="fas fa-rupee-sign" style="color:var(--neon-yellow);"></i>
                    <span><strong>Estimated Fare:</strong> <span style="font-size:1.1rem; font-weight:800; color:var(--neon-yellow);">₹<?php echo $_SESSION['pending_fare'] ?? 0; ?></span></span>
                </div>
                <?php if (!empty($_SESSION['coupon_applied'])): ?>
                <div class="info-row">
                    <i class="fas fa-tag" style="color:var(--neon-green);"></i>
                    <span><strong>Discount (STUDENT10):</strong> <span style="color:var(--neon-green);">- ₹<?php echo $_SESSION['discount_amount'] ?? 0; ?></span></span>
                </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Payment Method -->
        <div class="booking-section">
            <h2><i class="fas fa-wallet"></i> Payment Method</h2>
            <form method="POST" action="index.php?page=payment">
                <div class="payment-options" style="margin-bottom:24px;">
                    <label class="payment-option selected" onclick="selectPayment(this)">
                        <input type="radio" name="payment_mode" value="upi" checked>
                        <i class="fas fa-mobile-alt"></i>
                        <span>UPI / GPay</span>
                    </label>
                    <label class="payment-option" onclick="selectPayment(this)">
                        <input type="radio" name="payment_mode" value="card">
                        <i class="fas fa-credit-card"></i>
                        <span>Card</span>
                    </label>
                    <label class="payment-option" onclick="selectPayment(this)">
                        <input type="radio" name="payment_mode" value="cash">
                        <i class="fas fa-money-bill-wave"></i>
                        <span>Cash</span>
                    </label>
                    <label class="payment-option" onclick="selectPayment(this)">
                        <input type="radio" name="payment_mode" value="wallet">
                        <i class="fas fa-wallet"></i>
                        <span>Wallet</span>
                    </label>
                </div>
                <button type="submit" name="confirm_payment" class="book-now-btn">
                    <i class="fas fa-lock"></i> Pay & Confirm Ride
                </button>
            </form>
        </div>
    </div>

    <div style="text-align:center; margin-top:14px;">
        <a href="index.php?page=select_driver" class="btn btn-secondary"><i class="fas fa-arrow-left"></i> Back</a>
    </div>
</div>

<script>
function selectPayment(lbl) {
    document.querySelectorAll('.payment-option').forEach(p => p.classList.remove('selected'));
    lbl.classList.add('selected');
    lbl.querySelector('input').checked = true;
}
</script>

<?php include 'views/layout/footer.php'; ?>
