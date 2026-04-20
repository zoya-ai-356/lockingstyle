<?php 
require_once 'common/header.php'; 
checkUserAuth();

$user_id = $_SESSION['user_id'];
$u = $conn->query("SELECT * FROM users WHERE id = $user_id")->fetch_assoc();

// AI Style Stylist logic integration
require_once 'common/stylist-engine.php';
$recom_res = getStyleRecommendations($conn, $user_id, 4);
?>

<div class="p-6 pb-32 max-w-4xl mx-auto pt-10">
    <!-- Identity Card -->
    <div class="bg-[#111] p-10 rounded-[48px] border border-white/5 shadow-2xl flex flex-col items-center text-center relative overflow-hidden mb-12">
        <div class="absolute -right-10 -top-10 w-40 h-40 bg-[#00df81]/5 rounded-full blur-3xl"></div>
        
        <div class="w-24 h-24 rounded-[40px] bg-[#00df81] text-black flex items-center justify-center text-4xl font-black italic mb-6 shadow-xl shadow-emerald-500/20">
            <?php echo strtoupper(substr($u['full_name'], 0, 1)); ?>
        </div>
        
        <h2 class="text-2xl font-black text-white italic uppercase"><?php echo $u['full_name']; ?></h2>
        <p class="text-[10px] text-gray-500 font-bold uppercase tracking-widest mt-2">Vault Member ID: LS-0<?php echo $u['id']; ?></p>
        
        <div class="mt-8 flex gap-6">
            <div class="text-center">
                <p class="text-xl font-black text-white"><?php echo formatPrice($u['wallet_balance']); ?></p>
                <p class="text-[8px] font-black text-gray-600 uppercase tracking-widest mt-1">Wallet Credit</p>
            </div>
            <div class="w-px h-10 bg-white/5"></div>
            <div class="text-center">
                <?php 
                    $o_count = $conn->query("SELECT count(id) as c FROM orders WHERE user_id = $user_id")->fetch_assoc()['c'];
                ?>
                <p class="text-xl font-black text-white"><?php echo $o_count; ?></p>
                <p class="text-[8px] font-black text-gray-600 uppercase tracking-widest mt-1">Orders</p>
            </div>
        </div>
    </div>

    <!-- AI Stylist Grid -->
    <div class="mb-12">
        <div class="flex justify-between items-center mb-8 px-2">
            <h3 class="text-xl font-black text-white italic uppercase tracking-tighter">AI Recommended</h3>
            <i class="fa-solid fa-wand-magic-sparkles text-[#00df81]"></i>
        </div>
        <div class="grid grid-cols-2 gap-4">
            <?php while($rp = $recom_res->fetch_assoc()): ?>
            <a href="product-details.php?id=<?php echo $rp['id']; ?>" class="bg-[#111] p-3 rounded-3xl border border-white/5 block group">
                <div class="aspect-square bg-black rounded-2xl overflow-hidden mb-3">
                    <img src="uploads/products/<?php echo $rp['image_main']; ?>" class="w-full h-full object-cover group-hover:scale-110 transition-all duration-700">
                </div>
                <h4 class="text-[10px] font-black text-white uppercase truncate px-1"><?php echo $rp['name']; ?></h4>
                <p class="text-[#00df81] font-black text-[10px] mt-1 px-1"><?php echo formatPrice($rp['base_price']); ?></p>
            </a>
            <?php endwhile; ?>
        </div>
    </div>

    <!-- Action Links -->
    <div class="grid grid-cols-1 gap-4">
        <a href="track-order.php" class="p-6 bg-[#111] border border-white/5 rounded-3xl flex justify-between items-center hover:border-[#00df81]/30 transition-all">
            <div class="flex items-center gap-4">
                <i class="fa-solid fa-box text-primary"></i>
                <span class="text-xs font-black text-white uppercase tracking-widest">Active Consignments</span>
            </div>
            <i class="fa-solid fa-chevron-right text-gray-700 text-xs"></i>
        </a>
        <a href="wishlist.php" class="p-6 bg-[#111] border border-white/5 rounded-3xl flex justify-between items-center">
            <div class="flex items-center gap-4">
                <i class="fa-solid fa-heart text-red-500"></i>
                <span class="text-xs font-black text-white uppercase tracking-widest">Saved Artifacts</span>
            </div>
            <i class="fa-solid fa-chevron-right text-gray-700 text-xs"></i>
        </a>
        <a href="logout.php" class="p-6 bg-red-500/5 border border-red-500/10 rounded-3xl flex justify-between items-center text-red-500">
            <span class="text-xs font-black uppercase tracking-widest">Terminate Session</span>
            <i class="fa-solid fa-power-off"></i>
        </a>
    </div>
</div>

<?php require_once 'common/bottom.php'; ?>