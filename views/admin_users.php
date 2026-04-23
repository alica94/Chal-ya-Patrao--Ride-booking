<?php include 'views/layout/header.php'; ?>

<div class="page-header">
    <h1><i class="fas fa-users"></i> Manage Users</h1>
    <p>All registered users — including date of birth for age verification</p>
</div>

<?php if (!empty($success)): ?>
    <div class="alert alert-success"><i class="fas fa-check-circle"></i> <?php echo htmlspecialchars($success); ?></div>
<?php endif; ?>

<div class="table-wrap">
    <table class="data-table">
        <thead>
            <tr>
                <th>#</th>
                <th>Name</th>
                <th>Email</th>
                <th>Phone</th>
                <th>Date of Birth</th>
                <th>Age</th>
                <th>Type</th>
                <th>Status</th>
                <th>Joined</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($users as $u):
                // Compute age
                $age = '—';
                if (!empty($u['date_of_birth'])) {
                    $birth = new DateTime($u['date_of_birth']);
                    $age   = (new DateTime())->diff($birth)->y;
                }
            ?>
            <tr>
                <td><?php echo intval($u['user_id']); ?></td>
                <td><strong><?php echo htmlspecialchars($u['full_name']); ?></strong></td>
                <td style="font-size:0.85rem;"><?php echo htmlspecialchars($u['email']); ?></td>
                <td><?php echo htmlspecialchars($u['phone_number']); ?></td>
                <td style="font-size:0.85rem; color:var(--text-secondary);">
                    <?php echo !empty($u['date_of_birth']) ? date('d M Y', strtotime($u['date_of_birth'])) : '<span style="color:var(--neon-pink);">Not provided</span>'; ?>
                </td>
                <td>
                    <?php if ($age !== '—'): ?>
                    <span style="font-weight:700; color:<?php echo $age < 18 ? 'var(--neon-yellow)' : 'var(--neon-green)'; ?>">
                        <?php echo $age; ?> yrs<?php echo $age < 18 ? ' ⚠' : ''; ?>
                    </span>
                    <?php else: ?>
                    <span style="color:var(--text-muted);">—</span>
                    <?php endif; ?>
                </td>
                <td><span class="driver-tag"><?php echo htmlspecialchars($u['user_type']); ?></span></td>
                <td>
                    <span class="status-badge <?php echo $u['is_active'] ? 'status-accepted' : 'status-cancelled'; ?>">
                        <?php echo $u['is_active'] ? 'Active' : 'Blocked'; ?>
                    </span>
                </td>
                <td style="font-size:0.82rem; color:var(--text-secondary);">
                    <?php echo date('d M Y', strtotime($u['created_at'])); ?>
                </td>
                <td>
                    <form method="POST" action="index.php?page=admin_users">
                        <input type="hidden" name="user_id" value="<?php echo intval($u['user_id']); ?>">
                        <?php if ($u['is_active']): ?>
                            <button type="submit" name="block" class="btn btn-danger" style="padding:5px 12px; font-size:0.78rem;">
                                <i class="fas fa-ban"></i> Block
                            </button>
                        <?php else: ?>
                            <button type="submit" name="unblock" class="btn btn-secondary" style="padding:5px 12px; font-size:0.78rem;">
                                <i class="fas fa-check"></i> Unblock
                            </button>
                        <?php endif; ?>
                    </form>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<?php include 'views/layout/footer.php'; ?>
