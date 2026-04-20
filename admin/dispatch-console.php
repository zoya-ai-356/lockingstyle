<?php 
require_once 'common/header.php'; 
require_once 'common/sidebar.php'; 
require_once 'common/auth_guard.php';

guardPage('orders');

// Fetch orders ready for packing
$pending = mysqli_query($conn, "SELECT o.*, u.full_name FROM orders o 
                                LEFT JOIN users u ON o.user_id = u.id 
                                WHERE o.order_status = 'pending' 
                                ORDER BY o.id ASC");
?>

<main class="flex-1 p-6 lg:p-12 bg-black min-h-screen">
    <div class="mb-12 flex justify-between items-center">
        <div>
            <h2 class="text-4xl font-black text-white italic uppercase tracking-tighter">Dispatch Console</h2>
            <p class="text-[#00df81] text-xs font-black uppercase tracking-widest mt-1 animate-pulse">Station 01: Active</p>
        </div>
        <div class="flex gap-4">
            <div class="bg-[#111] px-6 py-4 rounded-3xl border border-white/10 text-center">
                <p class="text-[8px] font-black text-gray-500 uppercase mb-1">Queue Load</p>
                <p class="text-2xl font-black text-white"><?php echo mysqli_num_rows($pending); ?></p>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
        <?php while($o = mysqli_fetch_assoc($pending)): ?>
        <div class="bg-[#111] p-8 rounded-[40px] border border-white/10 shadow-2xl flex flex-col h-full group hover:border-[#00df81]/50 transition-all">
            <div class="flex justify-between items-start mb-8">
                <span class="bg-primary text-black text-[10px] font-black px-4 py-1.5 rounded-full uppercase tracking-tighter italic shadow-lg shadow-emerald-500/20">Ready to Pack</span>
                <p class="text-xs font-mono text-gray-600">#<?php echo $o['order_number']; ?></p>
            </div>

            <h3 class="text-2xl font-black text-white italic uppercase mb-2"><?php echo $o['full_name'] ?? 'Guest Node'; ?></h3>
            <p class="text-[10px] text-gray-500 font-bold uppercase tracking-widest mb-6"><i class="fa-solid fa-location-dot mr-1"></i> <?php echo $o['shipping_address']; ?></p>

            <div class="flex-1 space-y-4 mb-8">
                <?php 
                    $oid = $o['id'];
                    $items = mysqli_query($conn, "SELECT product_name, quantity, variations FROM order_items WHERE order_id = '$oid'");
                    while($item = mysqli_fetch_assoc($items)):
                ?>
                <div class="p-4 bg-black rounded-2xl border border-white/5 flex justify-between items-center">
                    <div>
                        <p class="text-xs font-black text-white uppercase italic"><?php echo $item['product_name']; ?></p>
                        <p class="text-[9px] text-[#00df81] font-bold"><?php echo $item['variations']; ?></p>
                    </div>
                    <span class="w-8 h-8 rounded-lg bg-[#111] flex items-center justify-center text-xs font-black text-white border border-white/10">x<?php echo $item['quantity']; ?></span>
                </div>
                <?php endwhile; ?>
            </div>

            <form action="orders.php" method="POST" class="mt-auto">
                <input type="hidden" name="order_id" value="<?php echo $o['id']; ?>">
                <input type="hidden" name="status" value="packing">
                <button type="submit" name="update_status" class="w-full bg-white text-black font-black py-5 rounded-2xl uppercase text-[10px] tracking-[0.2em] shadow-xl hover:bg-primary transition-all">
                    Initialize Consolidation
                </button>
            </form>
        </div>
        <?php endwhile; ?>

        <?php if(mysqli_num_rows($pending) == 0): ?>
            <div class="col-span-full py-40 text-center">
                <i class="fa-solid fa-check-double text-gray-800 text-6xl mb-6"></i>
                <h3 class="text-xl font-black text-gray-600 uppercase italic tracking-widest">Queue Status: Zero</h3>
            </div>
        <?php endif; ?>
    </div>
</main>

<?php require_once 'common/footer.php'; ?>