<?php 
require_once 'common/header.php'; 
require_once 'common/sidebar.php'; 

// Fetch Stats for Dashboard
$total_sales = mysqli_fetch_assoc($conn->query("SELECT SUM(total_amount) as total FROM orders WHERE order_status = 'delivered'"))['total'] ?? 0;
$today_orders = mysqli_fetch_assoc($conn->query("SELECT COUNT(id) as count FROM orders WHERE DATE(created_at) = CURDATE()"))['count'] ?? 0;
$low_stock = mysqli_fetch_assoc($conn->query("SELECT COUNT(id) as count FROM inventory WHERE stock_qty <= min_stock_alert"))['count'] ?? 0;

// Sales Velocity (Last 7 Days Heatmap Data)
$days = [];
for ($i = 6; $i >= 0; $i--) {
    $date = date('Y-m-d', strtotime("-$i days"));
    $res = mysqli_fetch_assoc($conn->query("SELECT SUM(total_amount) as s FROM orders WHERE DATE(created_at) = '$date' AND order_status != 'cancelled'"));
    $days[$date] = $res['s'] ?? 0;
}
$max_val = max($days) > 0 ? max($days) : 1;
?>

<main class="flex-1 p-6 lg:p-10">
    <div class="mb-10">
        <h2 class="text-3xl font-black italic text-white uppercase tracking-tighter">Command Center</h2>
        <p class="text-gray-500 text-xs font-bold uppercase tracking-widest mt-1">Operational Node Status: <span class="text-[#00df81]">Online</span></p>
    </div>

    <!-- KPI Grid -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-10">
        <div class="bg-[#111] p-8 rounded-[40px] border border-white/5 shadow-xl">
            <p class="text-[9px] font-black text-gray-500 uppercase tracking-[0.2em] mb-4">Gross Revenue</p>
            <h3 class="text-3xl font-black text-[#00df81] italic"><?php echo formatPrice($total_sales); ?></h3>
        </div>
        <div class="bg-[#111] p-8 rounded-[40px] border border-white/5 shadow-xl">
            <p class="text-[9px] font-black text-gray-500 uppercase tracking-[0.2em] mb-4">Today's Load</p>
            <h3 class="text-3xl font-black text-white italic"><?php echo $today_orders; ?> <span class="text-xs text-gray-600 not-italic">Orders</span></h3>
        </div>
        <div class="bg-[#111] p-8 rounded-[40px] border border-red-500/10 shadow-xl">
            <p class="text-[9px] font-black text-red-500 uppercase tracking-[0.2em] mb-4">Low Stock Critical</p>
            <h3 class="text-3xl font-black text-red-500 italic"><?php echo $low_stock; ?> <span class="text-xs text-gray-600 not-italic">Alerts</span></h3>
        </div>
    </div>

    <!-- Heatmap Visualization -->
    <div class="bg-[#111] p-10 rounded-[48px] border border-white/5 mb-10">
        <h4 class="text-[10px] font-black text-gray-500 uppercase tracking-[0.3em] mb-10 text-center">Node Performance Velocity (7 Days)</h4>
        <div class="flex items-end justify-between h-48 gap-3 md:gap-8 px-4">
            <?php foreach($days as $date => $val): 
                $h = ($val / $max_val) * 100;
            ?>
                <div class="flex-1 flex flex-col items-center gap-4 group">
                    <div class="relative w-full bg-black rounded-2xl overflow-hidden h-full flex items-end border border-white/5">
                        <div class="w-full bg-gradient-to-t from-[#00df81] to-[#00ffea] transition-all duration-1000 group-hover:shadow-[0_0_20px_#00df81]" style="height: <?php echo $h; ?>%;"></div>
                    </div>
                    <span class="text-[8px] font-black text-gray-600 uppercase"><?php echo date('D', strtotime($date)); ?></span>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</main>

<?php require_once 'common/footer.php'; ?>