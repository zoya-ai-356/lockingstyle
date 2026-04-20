<?php 
require_once 'common/header.php'; 
checkUserAuth();

$order_id = (int)$_GET['id'];
$user_id = $_SESSION['user_id'];

// Eligibility Check: Order must be 'delivered' and within 7 days
$res = $conn->query("SELECT * FROM orders WHERE id = $order_id AND user_id = $user_id AND order_status = 'delivered'");
$order = $res->fetch_assoc();

if (!$order) {
    $_SESSION['error'] = "ORDER_NOT_ELIGIBLE_FOR_RETURN";
    redirect('profile.php');
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['submit_return'])) {
    $reason = sanitize($_POST['reason']);
    $details = sanitize($_POST['details']);
    
    // Check if return record exists (Migration safety)
    $conn->query("CREATE TABLE IF NOT EXISTS order_returns (
        id INT AUTO_INCREMENT PRIMARY KEY,
        order_id INT,
        user_id INT,
        reason VARCHAR(255),
        details TEXT,
        status ENUM('pending', 'approved', 'rejected') DEFAULT 'pending',
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )");

    $sql = "INSERT INTO order_returns (order_id, user_id, reason, details) VALUES ('$order_id', '$user_id', '$reason', '$details')";
    if ($conn->query($sql)) {
        // Soft status update
        $conn->query("UPDATE orders SET order_status = 'pending' WHERE id = $order_id"); 
        logAudit('RETURN_REQUESTED', "User requested return for Order #{$order['order_number']}");
        $_SESSION['success'] = "Return Authorization Initiated.";
        redirect('profile.php');
    }
}
?>

<div class="p-8 max-w-2xl mx-auto pb-32">
    <div class="mb-10 flex items-center gap-4">
        <a href="profile.php" class="w-10 h-10 bg-[#111] rounded-2xl flex items-center justify-center text-gray-500 border border-white/5"><i class="fa-solid fa-arrow-left"></i></a>
        <h2 class="text-3xl font-black text-white italic uppercase tracking-tighter">Request Return</h2>
    </div>

    <div class="bg-[#111] p-10 rounded-[48px] border border-white/5 shadow-2xl">
        <div class="mb-8 p-6 bg-black rounded-3xl border border-white/5">
            <p class="text-[9px] font-black text-gray-600 uppercase mb-1">Authenticated Order</p>
            <h4 class="text-xl font-black text-white italic">#<?php echo $order['order_number']; ?></h4>
        </div>

        <form action="" method="POST" class="space-y-8">
            <div>
                <label class="text-[10px] font-black text-gray-500 uppercase tracking-widest mb-3 block">Primary Reason</label>
                <select name="reason" required class="w-full bg-black border border-white/10 p-5 rounded-2xl text-white text-xs outline-none focus:border-[#00df81]">
                    <option value="Size mismatch">Size mismatch / Poor Fit</option>
                    <option value="Damaged artifact">Artifact arrived damaged</option>
                    <option value="Not as described">Item not as described</option>
                    <option value="Changed mind">Aesthetic choice change</option>
                </select>
            </div>

            <div>
                <label class="text-[10px] font-black text-gray-500 uppercase tracking-widest mb-3 block">Extended Intel (Details)</label>
                <textarea name="details" rows="5" required placeholder="Describe the issue for the logistics team..." class="w-full bg-black border border-white/10 p-5 rounded-2xl text-white text-xs outline-none focus:border-[#00df81]"></textarea>
            </div>

            <div class="p-5 bg-orange-500/5 border border-orange-500/10 rounded-2xl">
                <p class="text-[10px] text-orange-500 leading-relaxed font-bold uppercase tracking-tighter">
                    <i class="fa-solid fa-circle-info mr-2"></i> Returns are processed within 72 hours of node arrival.
                </p>
            </div>

            <button name="submit_return" class="w-full bg-white text-black font-black py-6 rounded-3xl uppercase text-[10px] tracking-[0.2em] shadow-2xl active:scale-95 transition-all">
                Submit Request
            </button>
        </form>
    </div>
</div>

<?php require_once 'common/bottom.php'; ?>