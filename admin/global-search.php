<?php 
require_once 'common/header.php'; 
require_once 'common/sidebar.php'; 

$keyword = isset($_GET['q']) ? sanitize($_GET['q']) : '';
$results_p = [];
$results_o = [];
$results_u = [];

if (!empty($keyword)) {
    // Search Artifacts
    $res_p = mysqli_query($conn, "SELECT * FROM products WHERE name LIKE '%$keyword%' OR sku LIKE '%$keyword%' LIMIT 10");
    while($row = mysqli_fetch_assoc($res_p)) $results_p[] = $row;

    // Search Consignments
    $res_o = mysqli_query($conn, "SELECT * FROM orders WHERE order_number LIKE '%$keyword%' OR shipping_address LIKE '%$keyword%' LIMIT 10");
    while($row = mysqli_fetch_assoc($res_o)) $results_o[] = $row;

    // Search Entities
    $res_u = mysqli_query($conn, "SELECT * FROM users WHERE full_name LIKE '%$keyword%' OR email LIKE '%$keyword%' OR phone LIKE '%$keyword%' LIMIT 10");
    while($row = mysqli_fetch_assoc($res_u)) $results_u[] = $row;
}
?>

<main class="flex-1 p-6 lg:p-12">
    <div class="mb-10">
        <h2 class="text-3xl font-black text-white italic uppercase tracking-tighter">Intelligence Radar</h2>
        <p class="text-gray-500 text-sm mt-2">Cross-node search results for: "<?php echo htmlspecialchars($keyword); ?>"</p>
    </div>

    <!-- Artifact Results -->
    <?php if(!empty($results_p)): ?>
    <section class="mb-12">
        <h3 class="text-[10px] font-black text-primary uppercase tracking-[0.4em] mb-6 ml-4">Artifact Matches (Products)</h3>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <?php foreach($results_p as $p): ?>
            <a href="products.php?search=<?php echo $p['sku']; ?>" class="bg-[#111] p-6 rounded-3xl border border-white/5 flex items-center gap-6 hover:border-primary/40 transition-all">
                <img src="../uploads/products/<?php echo $p['image_main']; ?>" class="w-12 h-12 rounded-xl object-cover">
                <div>
                    <h4 class="text-sm font-black text-white uppercase italic"><?php echo $p['name']; ?></h4>
                    <p class="text-[9px] text-gray-500 font-mono uppercase tracking-tighter">SKU: <?php echo $p['sku']; ?> • <?php echo formatPrice($p['base_price']); ?></p>
                </div>
            </a>
            <?php endforeach; ?>
        </div>
    </section>
    <?php endif; ?>

    <!-- Consignment Results -->
    <?php if(!empty($results_o)): ?>
    <section class="mb-12">
        <h3 class="text-[10px] font-black text-blue-500 uppercase tracking-[0.4em] mb-6 ml-4">Consignment Matches (Orders)</h3>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <?php foreach($results_o as $o): ?>
            <a href="order-view.php?id=<?php echo $o['id']; ?>" class="bg-[#111] p-6 rounded-3xl border border-white/5 flex justify-between items-center hover:border-blue-500/40 transition-all">
                <div>
                    <h4 class="text-sm font-black text-white italic">#<?php echo $o['order_number']; ?></h4>
                    <p class="text-[9px] text-gray-500 uppercase font-bold"><?php echo date('d M Y', strtotime($o['created_at'])); ?></p>
                </div>
                <span class="text-[8px] bg-blue-500/10 text-blue-500 px-3 py-1 rounded-full font-black uppercase"><?php echo $o['order_status']; ?></span>
            </a>
            <?php endforeach; ?>
        </div>
    </section>
    <?php endif; ?>

    <!-- User Results -->
    <?php if(!empty($results_u)): ?>
    <section class="mb-12">
        <h3 class="text-[10px] font-black text-purple-500 uppercase tracking-[0.4em] mb-6 ml-4">Identity Matches (Users)</h3>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <?php foreach($results_u as $u): ?>
            <div class="bg-[#111] p-6 rounded-3xl border border-white/5 flex items-center gap-6">
                <div class="w-12 h-12 rounded-xl bg-purple-500/10 text-purple-500 flex items-center justify-center font-black italic"><?php echo substr($u['full_name'],0,1); ?></div>
                <div>
                    <h4 class="text-sm font-black text-white uppercase italic"><?php echo $u['full_name']; ?></h4>
                    <p class="text-[9px] text-gray-500 font-mono tracking-tighter"><?php echo $u['email']; ?> • <?php echo $u['phone']; ?></p>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </section>
    <?php endif; ?>

    <?php if(empty($results_p) && empty($results_o) && empty($results_u)): ?>
        <div class="py-40 text-center bg-[#111] rounded-[60px] border border-white/5 border-dashed">
            <i class="fa-solid fa-microchip text-gray-800 text-6xl mb-6"></i>
            <h3 class="text-xl font-black text-gray-600 uppercase">Radar Clear</h3>
            <p class="text-gray-500 text-xs mt-2 italic tracking-widest">No matching data nodes found for your query.</p>
        </div>
    <?php endif; ?>
</main>

<?php require_once 'common/footer.php'; ?>