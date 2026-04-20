<?php 
require_once 'common/header.php'; 
checkUserAuth();

$user_id = $_SESSION['user_id'];

// Get Category Affinity (Which categories does the user buy most?)
$affinity_query = "SELECT c.name, COUNT(oi.id) as total_items 
                   FROM order_items oi 
                   JOIN products p ON oi.product_id = p.id 
                   JOIN categories c ON p.category_id = c.id 
                   JOIN orders o ON oi.order_id = o.id 
                   WHERE o.user_id = '$user_id' AND o.order_status = 'delivered'
                   GROUP BY c.id ORDER BY total_items DESC LIMIT 5";
$res = mysqli_query($conn, $affinity_query);

$max_items = 0;
$affinity_data = [];
while($row = mysqli_fetch_assoc($res)) {
    $affinity_data[] = $row;
    if($row['total_items'] > $max_items) $max_items = $row['total_items'];
}
?>

<div class="p-8 md:p-12 max-w-4xl mx-auto pb-32 pt-10">
    <div class="mb-12">
        <h2 class="text-4xl font-black text-white italic uppercase tracking-tighter">Style Intelligence</h2>
        <p class="text-gray-500 text-xs font-bold uppercase tracking-widest mt-2">Data-driven aesthetic profiling</p>
    </div>

    <!-- Category Affinity Chart -->
    <div class="bg-[#111] p-10 rounded-[60px] border border-white/5 shadow-2xl mb-12">
        <h3 class="text-xs font-black text-primary uppercase tracking-[0.4em] mb-10 text-center text-glow">Category Dominance</h3>
        
        <div class="space-y-8">
            <?php foreach($affinity_data as $data): 
                $pct = ($max_items > 0) ? ($data['total_items'] / $max_items) * 100 : 0;
            ?>
            <div class="space-y-3">
                <div class="flex justify-between items-end">
                    <span class="text-xs font-black text-white uppercase italic tracking-widest"><?php echo $data['name']; ?></span>
                    <span class="text-[9px] font-bold text-gray-500"><?php echo $data['total_items']; ?> ARTIFACTS</span>
                </div>
                <div class="h-1.5 w-full bg-black rounded-full overflow-hidden flex border border-white/5">
                    <div class="h-full bg-gradient-to-r from-primary/20 to-primary rounded-full transition-all duration-1000" style="width: <?php echo $pct; ?>%"></div>
                </div>
            </div>
            <?php endforeach; ?>

            <?php if(empty($affinity_data)): ?>
                <div class="py-20 text-center">
                    <i class="fa-solid fa-chart-line text-gray-800 text-5xl mb-4"></i>
                    <p class="text-gray-600 text-xs font-black uppercase">Insufficient data for profiling.</p>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Financial Milestone -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
        <div class="bg-[#111] p-8 rounded-[40px] border border-white/5">
            <p class="text-[8px] font-black text-gray-500 uppercase tracking-widest mb-2">Acquisition Level</p>
            <?php 
                $total_spent = mysqli_fetch_assoc(mysqli_query($conn, "SELECT SUM(total_amount) as total FROM orders WHERE user_id = '$user_id' AND order_status = 'delivered'"))['total'] ?? 0;
                $level = floor($total_spent / 5000) + 1;
            ?>
            <h4 class="text-2xl font-black text-white italic uppercase">Rank: Tier 0<?php echo $level; ?></h4>
            <div class="mt-4 flex gap-2">
                <?php for($i=0; $i<$level; $i++): ?>
                    <div class="w-4 h-1 bg-primary rounded-full"></div>
                <?php endfor; ?>
            </div>
        </div>
        
        <div class="bg-[#111] p-8 rounded-[40px] border border-white/5">
            <p class="text-[8px] font-black text-gray-500 uppercase tracking-widest mb-2">Next Milestone</p>
            <p class="text-xs font-bold text-gray-400">Spend <?php echo formatPrice(5000 - ($total_spent % 5000)); ?> more to reach Tier 0<?php echo $level+1; ?>.</p>
        </div>
    </div>
</div>

<?php require_once 'common/bottom.php'; ?>