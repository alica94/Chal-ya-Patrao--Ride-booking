<?php include 'views/layout/header.php'; ?>
<div class="page-header"><h1><i class="fas fa-id-card"></i> Manage Drivers</h1></div>
<?php if (!empty($success)): ?><div class="alert alert-success"><i class="fas fa-check-circle"></i> <?php echo htmlspecialchars($success); ?></div><?php endif; ?>
<div class="table-wrap">
<table class="data-table">
<thead><tr><th>#</th><th>Name</th><th>Phone</th><th>Gender</th><th>Rating</th><th>Status</th><th>Joined</th><th>Action</th></tr></thead>
<tbody>
<?php foreach($drivers as $d): ?>
<tr>
    <td><?php echo intval($d['driver_id']); ?></td>
    <td><?php echo htmlspecialchars($d['full_name']); ?></td>
    <td><?php echo htmlspecialchars($d['phone_number']); ?></td>
    <td><?php echo htmlspecialchars($d['gender']); ?></td>
    <td style="color:var(--neon-yellow);"><?php echo number_format(floatval($d['avg_rating']),1); ?> ★</td>
    <td><span class="status-badge status-<?php echo $d['approval_status']==='approved'?'accepted':($d['approval_status']==='pending'?'open':'cancelled'); ?>"><?php echo htmlspecialchars($d['approval_status']); ?></span></td>
    <td style="font-size:0.82rem; color:var(--text-secondary);"><?php echo date('d M Y', strtotime($d['created_at'])); ?></td>
    <td>
        <form method="POST" action="index.php?page=admin_drivers" style="display:flex; gap:6px;">
            <input type="hidden" name="driver_id" value="<?php echo intval($d['driver_id']); ?>">
            <?php if ($d['approval_status'] !== 'approved'): ?>
                <button type="submit" name="approve" class="btn btn-primary" style="padding:5px 10px; font-size:0.78rem;"><i class="fas fa-check"></i> Approve</button>
            <?php endif; ?>
            <?php if ($d['approval_status'] !== 'rejected'): ?>
                <button type="submit" name="reject" class="btn btn-danger" style="padding:5px 10px; font-size:0.78rem;"><i class="fas fa-times"></i> Reject</button>
            <?php endif; ?>
        </form>
    </td>
</tr>
<?php endforeach; ?>
</tbody>
</table>
</div>
<?php include 'views/layout/footer.php'; ?>
