<?php include 'views/layout/header.php'; ?>
<div class="page-header"><h1><i class="fas fa-user"></i> My Profile</h1></div>
<?php if (!empty($error)): ?><div class="alert alert-error"><i class="fas fa-exclamation-circle"></i> <?php echo htmlspecialchars($error); ?></div><?php endif; ?>
<?php if (!empty($success)): ?><div class="alert alert-success"><i class="fas fa-check-circle"></i> <?php echo htmlspecialchars($success); ?></div><?php endif; ?>
<div style="max-width:520px; margin:0 auto;" class="booking-card">
    <form method="POST" action="index.php?page=profile">
        <div class="form-group"><label><i class="fas fa-user"></i> Full Name</label><input type="text" name="full_name" value="<?php echo htmlspecialchars($user['full_name']); ?>" required></div>
        <div class="form-group"><label><i class="fas fa-envelope"></i> Email</label><input type="email" value="<?php echo htmlspecialchars($user['email']); ?>" disabled style="opacity:0.6;"></div>
        <div class="form-group"><label><i class="fas fa-phone"></i> Phone</label><input type="tel" name="phone" value="<?php echo htmlspecialchars($user['phone_number']); ?>" required></div>
        <div class="form-group"><label><i class="fas fa-users"></i> User Type</label>
            <select name="user_type">
                <option value="student" <?php echo $user['user_type']==='student'?'selected':''; ?>>Student</option>
                <option value="resident" <?php echo $user['user_type']==='resident'?'selected':''; ?>>Working Professional</option>
                <option value="tourist" <?php echo $user['user_type']==='tourist'?'selected':''; ?>>Tourist</option>
                <option value="elderly" <?php echo $user['user_type']==='elderly'?'selected':''; ?>>Senior Citizen</option>
            </select>
        </div>
        <div class="form-group"><label><i class="fas fa-language"></i> Language</label>
            <select name="preferred_language">
                <option value="english" <?php echo $user['preferred_language']==='english'?'selected':''; ?>>English</option>
                <option value="hindi" <?php echo $user['preferred_language']==='hindi'?'selected':''; ?>>Hindi</option>
                <option value="konkani" <?php echo $user['preferred_language']==='konkani'?'selected':''; ?>>Konkani</option>
            </select>
        </div>
        <button type="submit" name="update_profile" class="btn btn-primary btn-full"><i class="fas fa-save"></i> Save Profile</button>
    </form>
</div>
<?php include 'views/layout/footer.php'; ?>
