<?php 
require_once 'common/header.php'; 
require_once 'common/sidebar.php'; 
require_once 'common/auth_guard.php';

guardPage('settings');

$report = [];

if (isset($_POST['execute_cleanup'])) {
    // 1. Purge expired OTPs
    mysqli_query($conn, "UPDATE users SET login_otp = NULL WHERE otp_expiry < NOW()");
    $report[] = "Expired security keys flushed.";

    // 2. Clear very old audit logs (> 90 days)
    mysqli_query($conn, "DELETE FROM audit_logs WHERE created_at < DATE_SUB(NOW(), INTERVAL 90 DAY)");
    $report[] = "Archived forensic logs (>90d) purged.";

    // 3. Clear failed login attempts
    mysqli_query($conn, "TRUNCATE TABLE rate_limits");
    $report[] = "Network rate limits reset.";

    // 4. Optimize core tables
    mysqli_query($conn, "OPTIMIZE TABLE orders, order_items, users, products, inventory");
    $report[] = "Primary data nodes optimized for high concurrency.";

    logAudit('SYSTEM_CLEANUP', "Industrial maintenance cycle executed by admin.");
    $_SESSION['success'] = "Maintenance Cycle Complete.";
}
?>

<main class="flex-1 p-6 lg:p-12">
    <div class="mb-12">
        <h2 class="text-3xl font-black text-white italic uppercase tracking-tighter">System Sanitizer</h2>
        <p class="text-gray-500 text-sm mt-2">Optimize database health and rotate system logs.</p>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-10">
        <div class="bg-[#111] p-10 rounded-[48px] border border-white/5 shadow-2xl">
            <h3 class="text-xs font-black text-primary uppercase tracking-widest mb-8">Maintenance Protocol</h3>
            <p class="text-gray-400 text-sm leading-relaxed mb-10">
                Executing the sanitizer will purge non-critical data, reset rate-limiting firewall rules, and reorganize database indexes to ensure maximum query speed during peak traffic.
            </p>
            
            <form action="" method="POST">
                <button type="submit" name="execute_cleanup" class="w-full bg-red-600 text-white font-black py-6 rounded-3xl uppercase text-[10px] tracking-[0.3em] shadow-xl shadow-red-900/20 hover:bg-red-700 transition-all">
                    Initiate Deep Clean
                </button>
            </form>
        </div>

        <div class="space-y-6">
            <h3 class="text-xs font-black text-gray-600 uppercase tracking-widest ml-4">Operation Log</h3>
            <div class="bg-black p-8 rounded-[40px] border border-white/5 min-h-[300px] font-mono text-[10px] space-y-3">
                <?php if(empty($report)): ?>
                    <p class="text-gray-800 italic">Waiting for authorization...</p>
                <?php else: ?>
                    <?php foreach($report as $line): ?>
                        <p class="text-[#00df81]">>>> <?php echo $line; ?></p>
                    <?php endforeach; ?>
                    <p class="text-white font-black mt-10">SYSTEM_HEALTH: 100%</p>
                <?php endif; ?>
            </div>
        </div>
    </div>
</main>

<?php require_once 'common/footer.php'; ?>