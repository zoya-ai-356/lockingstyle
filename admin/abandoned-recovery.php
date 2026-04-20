<?php 
require_once 'common/header.php'; 
require_once 'common/sidebar.php'; 
require_once 'common/auth_guard.php';

guardPage('marketing');

// Query: Find users who added to cart in last 48 hours but didn't order
$query = "SELECT u.full_name, u.phone, u.email, c.last_updated, c.cart_data 
          FROM carts c 
          JOIN users u ON c.user_id = u.id 
          WHERE c.last_updated >= DATE_SUB(NOW(), INTERVAL 48 HOUR)
          AND u.id NOT IN (SELECT user_id FROM orders WHERE created_at >= c.last_updated)
          ORDER BY c.last_updated DESC";
$res = $conn->query($query);
?>

<main class="flex-1 p-6 lg:p-10">
    <div class="mb-10">
        <h2 class="text-3xl font-black italic text-white uppercase tracking-tighter">Cart Recovery Node</h2>
        <p class="text-gray-500 text-xs font-bold uppercase tracking-widest mt-1">Identify and Re-engage Lost Leads</p>
    </div>

    <div class="grid grid-cols-1 gap-6">
        <?php while($c = $res->fetch_assoc()): 
            $items = json_decode($c['cart_data'], true);
            $item_names = [];
            foreach($items as $i) { $item_names[] = $i['name']; }
            $list = implode(', ', $item_names);
            
            $wa_msg = "Hello " . $c['full_name'] . ", we noticed you left [" . $list . "] in your LOCKINGSTYLE bag. We have reserved them for you! Complete your order here: " . SITE_URL . "/cart.php";
            $wa_link = "https://wa.me/" . preg_replace('/[^0-9]/', '', $c['phone']) . "?text=" . urlencode($wa_msg);
        ?>
        <div class="bg-[#111] p-8 rounded-[40px] border border-white/5 flex flex-col lg:flex-row justify-between items-center gap-8 group hover:border-orange-500/30 transition-all">
            <div class="flex items-center gap-6 flex-1">
                <div class="w-16 h-16 bg-orange-500/10 rounded-2xl flex items-center justify-center text-orange-500 text-2xl border border-orange-500/20">
                    <i class="fa-solid fa-cart-arrow-down"></i>
                </div>
                <div>
                    <h4 class="text-xl font-black text-white uppercase italic"><?php echo $c['full_name']; ?></h4>
                    <p class="text-[10px] text-gray-500 font-bold uppercase tracking-widest"><?php echo $c['email']; ?> • Idle for: <?php echo floor((time() - strtotime($c['last_updated']))/3600); ?> Hours</p>
                </div>
            </div>

            <div class="flex items-center gap-10">
                <div class="text-center">
                    <p class="text-[8px] font-black text-gray-600 uppercase mb-1">Items Stalled</p>
                    <p class="text-lg font-black text-white"><?php echo count($items); ?></p>
                </div>
                <a href="<?php echo $wa_link; ?>" target="_blank" class="bg-[#25D366] text-white font-black px-8 py-4 rounded-2xl flex items-center justify-center gap-3 shadow-xl text-[10px] uppercase hover:scale-105 transition-transform">
                    <i class="fa-brands fa-whatsapp text-xl"></i> Dispatch Reminder
                </a>
            </div>
        </div>
        <?php endwhile; ?>

        <?php if($res->num_rows == 0): ?>
            <div class="py-40 text-center bg-[#111] rounded-[60px] border border-gray-800 border-dashed">
                <i class="fa-solid fa-ghost text-6xl text-gray-800 mb-6"></i>
                <h3 class="text-xl font-black text-white uppercase">All Carts Converted</h3>
                <p class="text-gray-500 text-xs mt-2 uppercase tracking-widest">No abandoned nodes detected in 48h.</p>
            </div>
        <?php endif; ?>
    </div>
</main>

<?php require_once 'common/footer.php'; ?>
