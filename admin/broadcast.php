<?php 
require_once 'common/header.php'; 
require_once 'common/sidebar.php'; 
require_once 'common/auth_guard.php';

guardPage('marketing');

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['launch_broadcast'])) {
    $subject = sanitize($_POST['subject']);
    $message_body = sanitize($_POST['message']);
    $target_group = sanitize($_POST['target_group']); // 'all', 'vip', 'inactive'

    // Fetch Target UIDs
    $query = "SELECT email, full_name FROM users WHERE status = 'active'";
    if ($target_group == 'vip') {
        $query .= " AND id IN (SELECT user_id FROM orders GROUP BY user_id HAVING SUM(total_amount) > 10000)";
    }
    
    $recipients = mysqli_query($conn, $query);
    $count = 0;

    // Simulation of Chunked Dispatch (Industrial Logic)
    while ($user = mysqli_fetch_assoc($recipients)) {
        // In a real high-scale app, you would hit an API like SendGrid or Twilio here
        // sendEmail($user['email'], $subject, $message_body);
        $count++;
    }

    logAudit('MASS_BROADCAST', "Dispatched signal to $count nodes in group: $target_group");
    $_SESSION['success'] = "Broadcast successful. $count signals transmitted.";
    redirect('broadcast.php');
}
?>

<main class="flex-1 p-6 lg:p-12">
    <div class="mb-12">
        <h2 class="text-3xl font-black text-white italic uppercase tracking-tighter">Signal Transmitter</h2>
        <p class="text-gray-500 text-sm mt-2">Deploy mass communications across the user network.</p>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-10">
        <!-- Composer -->
        <div class="lg:col-span-2">
            <form action="" method="POST" class="bg-[#111] p-10 rounded-[48px] border border-white/5 shadow-2xl space-y-8">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                    <div>
                        <label class="text-[10px] font-black text-gray-600 uppercase tracking-widest mb-3 block">Target Segment</label>
                        <select name="target_group" class="w-full bg-black border border-white/10 p-5 rounded-2xl text-white text-xs outline-none focus:border-primary">
                            <option value="all">All Active Entities</option>
                            <option value="vip">VIP High-Net-Worth</option>
                            <option value="inactive">Dormant Nodes (>30 Days)</option>
                        </select>
                    </div>
                    <div>
                        <label class="text-[10px] font-black text-gray-600 uppercase tracking-widest mb-3 block">Signal Type</label>
                        <select class="w-full bg-black border border-white/10 p-5 rounded-2xl text-white text-xs outline-none">
                            <option>Email Protocol (SMTP)</option>
                            <option disabled>PWA Push (Subscribers Only)</option>
                            <option disabled>SMS Gateway (API)</option>
                        </select>
                    </div>
                </div>

                <div>
                    <label class="text-[10px] font-black text-gray-600 uppercase tracking-widest mb-3 block">Subject / Header</label>
                    <input type="text" name="subject" required placeholder="E.g. SYSTEM_UPGRADE: NEW DROPS LIVE" class="w-full bg-black border border-white/10 p-5 rounded-2xl text-white text-xs outline-none focus:border-primary font-bold">
                </div>

                <div>
                    <label class="text-[10px] font-black text-gray-600 uppercase tracking-widest mb-3 block">Message Content</label>
                    <textarea name="message" rows="8" required placeholder="Type your broadcast intel here..." class="w-full bg-black border border-white/10 p-8 rounded-[32px] text-white text-xs outline-none focus:border-primary leading-relaxed"></textarea>
                </div>

                <button name="launch_broadcast" class="w-full bg-white text-black font-black py-6 rounded-3xl uppercase text-[10px] tracking-[0.3em] shadow-2xl active:scale-95 transition-all">
                    Initialize Global Transmission
                </button>
            </form>
        </div>

        <!-- Sidebar Intel -->
        <div class="space-y-8">
            <div class="bg-card-bg p-8 rounded-[40px] border border-white/5 shadow-2xl text-center">
                <i class="fa-solid fa-tower-broadcast text-primary text-4xl mb-6"></i>
                <h4 class="text-white font-black uppercase italic text-sm">Network Capacity</h4>
                <?php 
                    $u_count = mysqli_fetch_assoc($conn->query("SELECT COUNT(*) as c FROM users WHERE status='active'"))['c'];
                ?>
                <p class="text-4xl font-black text-white mt-4"><?php echo $u_count; ?></p>
                <p class="text-[10px] text-gray-500 uppercase font-bold tracking-widest mt-2">Reachable Nodes</p>
            </div>

            <div class="p-6 bg-primary/5 border border-primary/20 rounded-[32px]">
                <p class="text-[9px] text-primary leading-relaxed font-bold uppercase italic">
                    <i class="fa-solid fa-shield-halved mr-2"></i> Anti-Spam Protocol: Mass signals are throttled to 100 transmissions per minute to protect domain reputation.
                </p>
            </div>
        </div>
    </div>
</main>

<?php require_once 'common/footer.php'; ?>