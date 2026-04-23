<?php include 'views/layout/header.php'; ?>

<div style="max-width:460px; margin:40px auto;">
    <div class="otp-card">
        <div class="otp-icon"><i class="fas fa-shield-alt"></i></div>
        <div class="otp-title">OTP Verification</div>
        <div class="otp-subtitle">
            Your ride is almost confirmed!<br>
            Enter the 6-digit OTP sent to your mobile to proceed.
        </div>

        <?php if (!empty($error)): ?>
            <div class="alert alert-error" style="text-align:left;"><i class="fas fa-exclamation-circle"></i> <?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>

        <div class="alert alert-info" style="text-align:left; margin-bottom:20px;">
            <i class="fas fa-info-circle"></i>
            <span><strong>Demo mode:</strong> Your OTP is <strong id="demoOtp" style="letter-spacing:3px; font-size:1.1rem;">------</strong><br>
            <small>(Type any 6-digit number to continue)</small></span>
        </div>

        <form method="POST" action="index.php?page=otp_verify" id="otpForm">
            <input type="hidden" name="ride_id" value="<?php echo isset($ride_id) ? intval($ride_id) : 0; ?>">
            <input class="otp-input" type="text" name="otp_code" id="otpInput"
                maxlength="6" placeholder="— — — — — —"
                pattern="[0-9]{6}" autocomplete="one-time-code"
                oninput="formatOtp(this)" required>

            <button type="submit" name="verify_otp" class="book-now-btn" id="submitBtn" disabled>
                <i class="fas fa-check-circle"></i> Verify & Confirm Ride
            </button>
        </form>

        <div class="otp-demo-note">
            <i class="fas fa-lock"></i> This is a demo OTP system.<br>Enter any 6-digit number to proceed.
        </div>
    </div>
</div>

<script>
// Generate a random demo OTP to show the user
const demoOtp = String(Math.floor(100000 + Math.random() * 900000));
document.getElementById('demoOtp').textContent = demoOtp;

function formatOtp(input) {
    input.value = input.value.replace(/[^0-9]/g,'').slice(0,6);
    document.getElementById('submitBtn').disabled = input.value.length < 6;
}
</script>

<?php include 'views/layout/footer.php'; ?>
