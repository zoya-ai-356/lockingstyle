<?php 
require_once 'common/header.php'; 
checkUserAuth();

$user_id = $_SESSION['user_id'];
$u = $conn->query("SELECT * FROM users WHERE id = $user_id")->fetch_assoc();

// Wallet Transactions Log (Assuming a wallet_logs table exists)
mysqli_query($conn, "CREATE TABLE IF NOT EXISTS wallet_logs (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT,
    amount DECIMAL(10,2),
    type ENUM('credit', 'debit'),
    reason VARCHAR(255),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
)");

$logs = $conn->query("SELECT * FROM wallet_logs WHERE user_id = $user_id ORDER BY id DESC");
?>

<div class="p-8 max-w-4xl mx-auto pb-32 pt-10">
    <div class="flex justify-between items-center mb-12">
        <h2 class="text-4xl font-black text-white italic uppercase tracking-tighter">Vault <span class="text-primary">Credit</span></h2>
        <div class="w-12 h-12 bg-[#111] rounded-2xl flex items-center justify-center text-primary border border-white/10"><i class="fa-solid fa-wallet"></i></div>
    </div>

    <!-- Balance Card -->
    <div class="bg-gradient-to-br from-[#111] to-[#050505] p-12 rounded-[60px] border border-white/10 shadow-2xl text-center relative overflow-hidden mb-12">
        <div class="absolute inset-0 opacity-10 bg-[url('https://www.transparenttextures.com/patterns/carbon-fibre.png')]"></div>
        <p class="text-[10px] font-black text-gray-500 uppercase tracking-[0.4em] mb-6 relative z-10">Available Liquidity</p>
        <h3 class="text-6xl font-black text-white italic relative z-10"><?php echo formatPrice($u['wallet_balance']); ?></h3>
        <button class="mt-10 bg-[#00df81] text-black font-black px-10 py-4 rounded-2xl uppercase text-[10px] tracking-widest shadow-xl shadow-emerald-500/20 relative z-10">Redeem Gift Card</button>
    </div>

    <h3 class="text-xs font-black text-gray-500 uppercase tracking-widest mb-8 ml-4">Transaction Ledger</h3>
    
    <div class="space-y-4">
        <?php while($l = $logs->fetch_assoc()): 
            $is_credit = ($l['type'] == 'credit');
        ?>
        <div class="bg-[#111] p-6 rounded-[32px] border border-white/5 flex justify-between items-center group hover:border-white/10 transition-all">
            <div class="flex items-center gap-6">
                <div class="w-12 h-12 rounded-2xl flex items-center justify-center <?php echo $is_credit ? 'bg-emerald-500/10 text-emerald-500' : 'bg-red-500/10 text-red-500'; ?> border border-white/5">
                    <i class="fa-solid <?php echo $is_credit ? 'fa-arrow-trend-up' : 'fa-arrow-trend-down'; ?>"></i>
                </div>
                <div>
                    <h4 class="text-sm font-black text-white uppercase italic"><?php echo $l['reason']; ?></h4>
                    <p class="text-[9px] text-gray-600 font-bold uppercase mt-1"><?php echo date('d M Y, H:i', strtotime($l['created_at'])); ?></p>
                </div>
            </div>
            <p class="text-lg font-black <?php echo $is_credit ? 'text-emerald-500' : 'text-white'; ?>">
                <?php echo $is_credit ? '+' : '-'; ?> <?php echo formatPrice($l['amount']); ?>
            </p>
        </div>
        <?php endwhile; ?>

        <?php if($logs->num_rows == 0): ?>
            <div class="py-20 text-center text-gray-700 italic border border-dashed border-white/5 rounded-[40px]">No ledger entries found.</div>
        <?php endif; ?>
    </div>
</div>

<?php require_once 'common/bottom.php'; ?>