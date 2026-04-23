<?php include 'views/layout/header.php'; ?>
<div class="page-header"><h1><i class="fas fa-user-shield"></i> Create Admin</h1></div>
<?php if (!empty($error)): ?><div class="alert alert-error"><i class="fas fa-exclamation-circle"></i> <?php echo htmlspecialchars($error); ?></div><?php endif; ?>
<?php if (!empty($success)): ?><div class="alert alert-success"><i class="fas fa-check-circle"></i> <?php echo htmlspecialchars($success); ?></div><?php endif; ?>
<div style="max-width:480px; margin:0 auto;" class="booking-card">
    <form method="POST" action="index.php?page=admin_create">
        <div class="form-group"><label><i class="fas fa-user"></i> Full Name</label><input type="text" name="full_name" required></div>
        <div class="form-group"><label><i class="fas fa-envelope"></i> Email</label><input type="email" name="email" required></div>
        <div class="form-group"><label><i class="fas fa-lock"></i> Password</label><input type="password" name="password" required></div>
        <div class="form-group"><label><i class="fas fa-user-shield"></i> Role</label>
            <select name="admin_role">
                <option value="support">Support</option>
                <option value="super_admin">Super Admin</option>
            </select>
        </div>
        <button type="submit" name="create_admin" class="btn btn-primary btn-full"><i class="fas fa-plus"></i> Create Admin Account</button>
    </form>
</div>
<?php include 'views/layout/footer.php'; ?>
