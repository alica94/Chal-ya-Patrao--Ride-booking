<?php include 'views/layout/header.php'; ?>

<div class="page-header">
    <h1><i class="fas fa-tachometer-alt"></i> Driver Dashboard</h1>
    <p>Welcome, <?php echo htmlspecialchars($driver['full_name']); ?>!</p>
</div>

<?php if (!empty($success)): ?><div class="alert alert-success"><i class="fas fa-check-circle"></i> <?php echo htmlspecialchars($success); ?></div><?php endif; ?>
<?php if (!empty($error)):   ?><div class="alert alert-error"><i class="fas fa-exclamation-circle"></i> <?php echo htmlspecialchars($error); ?></div><?php endif; ?>

<div class="driver-dash-grid">

    <!-- ── LEFT SIDEBAR ── -->
    <div>
        <div class="ride-info-card" style="margin-bottom:16px; text-align:center;">
            <div class="driver-avatar" style="margin:0 auto 14px; width:70px; height:70px; font-size:2rem;">
                <i class="fas fa-user-tie"></i>
            </div>
            <h3 style="font-weight:800; margin-bottom:4px;"><?php echo htmlspecialchars($driver['full_name']); ?></h3>
            <div style="color:var(--neon-yellow); font-size:1.1rem; margin-bottom:8px;">
                <?php for ($s=1;$s<=5;$s++) echo $s<=round($driver['avg_rating']) ? '★' : '☆'; ?>
                <span style="font-size:0.85rem; color:var(--text-secondary);"><?php echo number_format(floatval($driver['avg_rating']),1); ?></span>
            </div>

            <!-- ✅ Online/Offline status badge -->
            <span class="status-badge <?php echo $driver['is_online'] ? 'status-accepted' : 'status-cancelled'; ?>">
                <?php echo $driver['is_online'] ? '● Online' : '● Offline'; ?>
            </span>

            <!-- ✅ Toggle Online/Offline button -->
            <form method="POST" action="index.php?page=driver_toggle_status" style="margin-top:10px;">
                <button type="submit"
                        class="btn <?php echo $driver['is_online'] ? 'btn-danger' : 'btn-primary'; ?>"
                        style="width:100%; font-size:0.85rem;">
                    <?php if ($driver['is_online']): ?>
                        <i class="fas fa-toggle-off"></i> Go Offline
                    <?php else: ?>
                        <i class="fas fa-toggle-on"></i> Go Online
                    <?php endif; ?>
                </button>
            </form>
        </div>

        <?php if ($vehicle): ?>
        <div class="ride-info-card" style="margin-bottom:16px;">
            <h4><i class="fas fa-car"></i> My Vehicle</h4>
            <div class="info-row"><i class="fas fa-car"></i><span><?php echo htmlspecialchars($vehicle['model']); ?></span></div>
            <div class="info-row"><i class="fas fa-palette"></i><span><?php echo htmlspecialchars($vehicle['color']); ?></span></div>
            <div class="info-row"><i class="fas fa-id-card"></i><span><?php echo htmlspecialchars($vehicle['registration_number']); ?></span></div>
            <div class="info-row"><i class="fas fa-users"></i><span><?php echo isset($vehicle['seats']) ? htmlspecialchars($vehicle['seats']) : 'N/A'; ?> seats</span></div>
            <?php if ($vehicle['is_pet_friendly']): ?>
            <span class="driver-tag" style="color:var(--neon-green); border-color:var(--neon-green); margin-top:6px;">
                <i class="fas fa-paw"></i> Pet-Friendly Vehicle
            </span>
            <?php else: ?>
            <span class="driver-tag" style="color:var(--text-muted); margin-top:6px;">
                <i class="fas fa-ban"></i> Not Pet-Friendly
            </span>
            <?php endif; ?>
        </div>
        <?php endif; ?>

        <div style="display:flex; flex-direction:column; gap:10px;">
            <a href="index.php?page=driver_map" class="btn btn-primary"><i class="fas fa-map-marked-alt"></i> Open Map Navigation</a>
            <a href="index.php?page=driver_ratings" class="btn btn-secondary"><i class="fas fa-star"></i> My Ratings</a>
            <a href="index.php?page=driver_complaint" class="btn btn-secondary"><i class="fas fa-flag"></i> File Complaint</a>
        </div>
    </div>

    <!-- ── MAIN AREA ── -->
    <div>

        <!-- ══ RIDE REQUEST NOTIFICATIONS ══════════════════════════════════ -->
        <div style="margin-bottom:32px;">
            <h2 style="font-size:1.15rem; font-weight:700; margin-bottom:14px; color:var(--neon-yellow); display:flex; align-items:center; gap:10px;">
                <i class="fas fa-bell"></i> Incoming Ride Requests
                <?php if (!empty($pending_requests)): ?>
                <span style="background:var(--neon-pink); color:white; border-radius:50%;
                             width:24px; height:24px; display:inline-flex; align-items:center;
                             justify-content:center; font-size:0.8rem; animation:ping 1s infinite;">
                    <?php echo count($pending_requests); ?>
                </span>
                <?php endif; ?>
            </h2>

            <?php if (empty($pending_requests)): ?>
            <div class="alert alert-info">
                <i class="fas fa-inbox"></i>
                No pending ride requests. Passengers who select you as their preferred driver will appear here.
            </div>
            <?php else: ?>
            <div style="display:flex; flex-direction:column; gap:14px;">
                <?php foreach ($pending_requests as $req): ?>
                <div class="complaint-card" style="border-color:var(--neon-yellow); background:rgba(255,215,0,0.04);">
                    <!-- Request header -->
                    <div style="display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:8px; margin-bottom:12px;">
                        <div>
                            <span style="font-size:0.72rem; text-transform:uppercase; letter-spacing:1px; color:var(--neon-yellow); font-weight:700;">
                                <i class="fas fa-bell"></i> New Ride Request — #<?php echo intval($req['ride_id']); ?>
                            </span>
                            <div style="font-size:0.8rem; color:var(--text-muted);">
                                Received: <?php echo date('d M Y, g:ia', strtotime($req['notified_at'])); ?>
                            </div>
                        </div>
                        <span class="status-badge status-open">Awaiting Your Response</span>
                    </div>

                    <!-- Journey info -->
                    <div style="display:grid; grid-template-columns:1fr 1fr; gap:10px; margin-bottom:14px;">
                        <div>
                            <div class="info-row" style="margin-bottom:6px;">
                                <i class="fas fa-circle" style="color:var(--neon-green); font-size:0.6rem;"></i>
                                <span style="font-size:0.88rem;"><strong>From:</strong> <?php echo htmlspecialchars($req['pickup_location']); ?></span>
                            </div>
                            <div class="info-row">
                                <i class="fas fa-flag-checkered" style="color:var(--neon-pink);"></i>
                                <span style="font-size:0.88rem;"><strong>To:</strong> <?php echo htmlspecialchars($req['dropoff_location']); ?></span>
                            </div>
                        </div>
                        <div>
                            <div class="info-row" style="margin-bottom:6px;">
                                <i class="fas fa-user"></i>
                                <span style="font-size:0.88rem;"><?php echo htmlspecialchars($req['user_name']); ?></span>
                            </div>
                            <div class="info-row" style="margin-bottom:6px;">
                                <i class="fas fa-phone"></i>
                                <span style="font-size:0.88rem;"><?php echo htmlspecialchars($req['user_phone']); ?></span>
                            </div>
                            <div class="info-row">
                                <i class="fas fa-rupee-sign" style="color:var(--neon-yellow);"></i>
                                <span style="font-size:0.95rem; font-weight:800; color:var(--neon-yellow);">₹<?php echo number_format(floatval($req['fare']),0); ?></span>
                            </div>
                        </div>
                    </div>

                    <!-- Tags -->
                    <div style="display:flex; flex-wrap:wrap; gap:6px; margin-bottom:14px;">
                        <span class="driver-tag"><?php echo strtoupper(htmlspecialchars($req['ride_type'])); ?></span>
                        <span class="driver-tag"><?php echo htmlspecialchars($req['payment_mode']); ?></span>
                        <?php if ($req['pet_friendly_required']): ?>
                        <span class="driver-tag" style="color:var(--neon-green); border-color:var(--neon-green);">
                            <i class="fas fa-paw"></i> Pet on board
                        </span>
                        <?php endif; ?>
                    </div>

                    <!-- Accept / Reject buttons -->
                    <div style="display:flex; gap:10px; flex-wrap:wrap;">
                        <form method="POST" action="index.php?page=driver_ride_action" style="display:inline;">
                            <input type="hidden" name="request_id" value="<?php echo intval($req['request_id']); ?>">
                            <button type="submit" name="accept_request" class="btn btn-primary">
                                <i class="fas fa-check"></i> Accept Ride
                            </button>
                        </form>
                        <form method="POST" action="index.php?page=driver_ride_action" style="display:inline;">
                            <input type="hidden" name="request_id" value="<?php echo intval($req['request_id']); ?>">
                            <button type="submit" name="reject_request" class="btn btn-danger">
                                <i class="fas fa-times"></i> Reject Ride
                            </button>
                        </form>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
            <?php endif; ?>
        </div>

        <!-- ══ OPEN POOL RIDES (auto-assign) ════════════════════════════════ -->
        <?php if (!empty($pending_rides)): ?>
        <div style="margin-bottom:32px;">
            <h2 style="font-size:1.05rem; font-weight:700; margin-bottom:14px; color:var(--accent);">
                <i class="fas fa-globe"></i> Open Rides (Auto-Assign Pool)
                <span style="font-size:0.78rem; color:var(--text-muted); font-weight:400;">— these riders chose auto-assign</span>
            </h2>
            <div style="display:flex; flex-direction:column; gap:12px;">
                <?php foreach ($pending_rides as $r): ?>
                <div class="complaint-card">
                    <div style="display:flex; justify-content:space-between; align-items:flex-start; flex-wrap:wrap; gap:10px;">
                        <div>
                            <div style="font-weight:700; margin-bottom:6px;">Ride #<?php echo intval($r['ride_id']); ?> — <?php echo strtoupper(htmlspecialchars($r['ride_type'])); ?></div>
                            <div class="info-row" style="margin-bottom:4px;"><i class="fas fa-circle" style="color:var(--neon-green); font-size:0.6rem;"></i><span style="font-size:0.86rem;"><?php echo htmlspecialchars($r['pickup_location']); ?></span></div>
                            <div class="info-row"><i class="fas fa-flag-checkered" style="color:var(--neon-pink);"></i><span style="font-size:0.86rem;"><?php echo htmlspecialchars($r['dropoff_location']); ?></span></div>
                            <div style="margin-top:6px; color:var(--neon-yellow); font-weight:700;">₹<?php echo number_format(floatval($r['fare']),0); ?> · <?php echo htmlspecialchars($r['payment_mode']); ?></div>
                            <?php if ($r['pet_friendly_required']): ?><span class="driver-tag" style="color:var(--neon-green); border-color:var(--neon-green); margin-top:6px;"><i class="fas fa-paw"></i> Pet on board</span><?php endif; ?>
                        </div>
                        <form method="POST" action="index.php?page=driver_ride_action">
                            <input type="hidden" name="ride_id" value="<?php echo $r['ride_id']; ?>">
                            <button type="submit" name="accept_ride" class="btn btn-primary"><i class="fas fa-check"></i> Accept</button>
                        </form>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
        <?php endif; ?>

        <!-- ══ MY ASSIGNED RIDES ═════════════════════════════════════════════ -->
        <h2 style="font-size:1.05rem; font-weight:700; margin-bottom:14px; color:var(--accent);">
            <i class="fas fa-car"></i> My Current & Past Rides
        </h2>
        <?php if (empty($my_rides)): ?>
        <div class="alert alert-info"><i class="fas fa-info-circle"></i> No rides assigned yet.</div>
        <?php else: ?>
        <div class="table-wrap">
            <table class="data-table">
                <thead>
                    <tr><th>#</th><th>Pickup</th><th>Drop</th><th>Fare</th><th>Passenger</th><th>Pet</th><th>Status</th><th>Update</th></tr>
                </thead>
                <tbody>
                    <?php foreach ($my_rides as $r): ?>
                    <tr>
                        <td><strong>#<?php echo intval($r['ride_id']); ?></strong></td>
                        <td style="font-size:0.85rem;"><?php echo htmlspecialchars($r['pickup_location']); ?></td>
                        <td style="font-size:0.85rem;"><?php echo htmlspecialchars($r['dropoff_location']); ?></td>
                        <td style="color:var(--neon-yellow); font-weight:700;">₹<?php echo number_format(floatval($r['fare']),0); ?></td>
                        <td style="font-size:0.85rem;"><?php echo htmlspecialchars($r['user_name']); ?></td>
                        <td><?php echo $r['pet_friendly_required'] ? '<span style="color:var(--neon-green);">🐾</span>' : '—'; ?></td>
                        <td><span class="status-badge status-<?php echo htmlspecialchars($r['status']); ?>"><?php echo htmlspecialchars($r['status']); ?></span></td>
                        <td>
                            <form method="POST" action="index.php?page=driver_ride_action" style="display:flex; gap:6px; flex-wrap:wrap;">
                                <input type="hidden" name="ride_id" value="<?php echo $r['ride_id']; ?>">
                                <select name="new_status" style="padding:5px 8px; border-radius:6px; background:var(--bg-input); border:1px solid var(--border-color); color:var(--text-primary); font-size:0.8rem; outline:none;">
                                    <option value="accepted">Accepted</option>
                                    <option value="completed">Completed</option>
                                    <option value="cancelled">Cancelled</option>
                                </select>
                                <button type="submit" name="update_status" class="btn btn-secondary" style="padding:5px 10px; font-size:0.78rem;"><i class="fas fa-sync"></i></button>
                            </form>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <?php endif; ?>
    </div>
</div>

<style>
@keyframes ping {
    0%,100% { transform:scale(1); }
    50%      { transform:scale(1.15); }
}
</style>

<?php include 'views/layout/footer.php'; ?>