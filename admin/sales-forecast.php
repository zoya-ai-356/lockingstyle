<?php 
require_once 'common/header.php'; 
require_once 'common/sidebar.php'; 
require_once 'common/auth_guard.php';

guardPage('reports');

// Logic: Calculate Daily Sales Velocity for the last 30 days
$days_to_analyze = 30;
$sales_data = [];
$total_period_revenue = 0;

for ($i = $days_to_analyze; $i >= 1; $i--) {
    $date = date('Y-m-d', strtotime("-$i days"));
    $res = mysqli_fetch_assoc($conn->query("SELECT SUM(total_amount) as s FROM orders WHERE DATE(created_at) = '$date' AND order_status != 'cancelled'"));
    $val = $res['s'] ?? 0;
    $sales_data[$date] = $val;
    $total_period_revenue += $val;
}

$daily_velocity = $total_period_revenue / $days_to_analyze;
$projected_monthly = $daily_velocity * 30;
$projected_quarterly = $daily_velocity * 90;
?>

<main class="flex-1 p-6 lg:p-12">
    <div class="mb-12">
        <h2 class="text-3xl font-black text-white italic uppercase tracking-tighter">Revenue Forecasting</h2>
        <p class="text-gray-500 text-sm mt-2">Predictive modeling based on current node velocity.</p>
    </div>

    <!-- Projection Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8 mb-12">
        <div class="bg-[#111] p-10 rounded-[48px] border border-white/5 shadow-2xl relative overflow-hidden">
            <p class="text-[10px] font-black text-primary uppercase tracking-[0.4em] mb-4">Current Velocity</p>
            <h3 class="text-3xl font-black text-white italic"><?php echo formatPrice($daily_velocity); ?> <span class="text-xs text-gray-600">/ Day</span></h3>
        </div>

        <div class="bg-[#111] p-10 rounded-[48px] border border-white/5 shadow-2xl">
            <p class="text-[10px] font-black text-gray-500 uppercase tracking-[0.4em] mb-4">Projected 30-Day</p>
            <h3 class="text-3xl font-black text-white italic"><?php echo formatPrice($projected_monthly); ?></h3>
            <div class="mt-4 flex items-center gap-2">
                <span class="w-2 h-2 rounded-full bg-primary animate-pulse"></span>
                <span class="text-[9px] text-gray-600 font-bold uppercase">Confidence: High</span>
            </div>
        </div>

        <div class="bg-[#111] p-10 rounded-[48px] border border-white/5 shadow-2xl">
            <p class="text-[10px] font-black text-gray-500 uppercase tracking-[0.4em] mb-4">Projected 90-Day</p>
            <h3 class="text-3xl font-black text-[#00df81] italic"><?php echo formatPrice($projected_quarterly); ?></h3>
        </div>
    </div>

    <!-- Velocity Visualizer -->
    <div class="bg-[#111] p-10 rounded-[48px] border border-white/5 shadow-2xl">
        <h4 class="text-xs font-black text-gray-400 uppercase tracking-widest mb-10">Sales Momentum (Last 30 Cycles)</h4>
        <div class="flex items-end justify-between h-40 gap-1 px-4">
            <?php 
            $max_val = max($sales_data) > 0 ? max($sales_data) : 1;
            foreach($sales_data as $date => $val): 
                $h = ($val / $max_val) * 100;
            ?>
                <div class="flex-1 bg-white/5 rounded-t-sm relative group" style="height: <?php echo $h; ?>%;">
                    <div class="absolute inset-0 bg-primary opacity-0 group-hover:opacity-100 transition-opacity"></div>
                </div>
            <?php endforeach; ?>
        </div>
        <div class="flex justify-between mt-6 text-[8px] font-black text-gray-600 uppercase tracking-widest">
            <span><?php echo date('M d', strtotime("-$days_to_analyze days")); ?></span>
            <span>Current Node Time</span>
        </div>
    </div>
</main>

<?php require_once 'common/footer.php'; ?>