<?php include 'views/layout/header.php'; ?>

<div class="auth-container">
    <div class="auth-card">
        <div class="auth-header">
            <h2><i class="fas fa-car-side" style="font-size:1.4rem;"></i> Welcome Back!</h2>
            <p>Login to book your next ride across Goa</p>
        </div>

        <?php if (!empty($error)): ?>
            <div class="alert alert-error"><i class="fas fa-exclamation-circle"></i> <?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>

        <form class="auth-form" method="POST" action="index.php?page=login">
            <div class="form-group">
                <label><i class="fas fa-envelope"></i> Email Address</label>
                <input type="email" name="email" placeholder="Enter your email" required>
            </div>
            <div class="form-group">
                <label><i class="fas fa-lock"></i> Password</label>
                <input type="password" name="password" placeholder="Enter your password" required>
            </div>
            <button type="submit" name="login" class="cta-button"><i class="fas fa-sign-in-alt"></i> Login to your Account</button>
            <div style="text-align:center; margin-top:16px; color:var(--text-secondary); font-size:0.9rem;">
                Don't have an account? <a href="index.php?page=register" class="auth-link">Create one now</a>
            </div>
            <div style="text-align:center; margin-top:8px;">
                <a href="index.php?page=driver_login" class="auth-link" style="font-size:0.85rem;"><i class="fas fa-car"></i> Login as Driver instead</a>
            </div>
        </form>
    </div>
</div>

<?php include 'views/layout/footer.php'; ?>
