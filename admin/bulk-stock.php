<?php 
require_once 'common/header.php'; 
require_once 'common/sidebar.php'; 
require_once 'common/auth_guard.php';

guardPage('inventory');

// Handle Bulk Update
if (isset($_POST['bulk_update'])) {
    foreach ($_POST['stock'] as $p_id => $branches) {
        foreach ($branches as $b_id => $qty) {
            $p_id = (int)$p_id; $b_id = (int)$b_id; $qty = (int)$qty;
            $conn->query("INSERT INTO inventory (product_id, branch_id, stock_qty) 
                          VALUES ($p_id, $b_id, $qty) 
                          ON DUPLICATE KEY UPDATE stock_qty = $qty");
        }
    }
    $_SESSION['success'] = "Global Stock Matrix Synchronized.";
}

$products = $conn->query("SELECT id, name, sku FROM products WHERE status='published'");
$branches = $conn->query("SELECT id, branch_name FROM branches WHERE is_active=1");
$branch_list = [];
while($b = $branches->fetch_assoc()) { $branch_list[] = $b; }
?>

<main class="flex-1 p-6 lg:p-10">
    <div class="mb-10">
        <h2 class="text-3xl font-black italic text-white uppercase tracking-tighter">Stock Synchronizer</h2>
        <p class="text-gray-500 text-xs font-bold uppercase tracking-widest mt-1">Multi-Branch Inventory Control</p>
    </div>

    <form action="" method="POST">
        <div class="bg-[#111] rounded-[48px] border border-white/5 overflow-hidden shadow-2xl">
            <div class="overflow-x-auto no-scrollbar">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-black text-[9px] font-black uppercase text-gray-500 tracking-widest border-b border-white/5">
                            <th class="p-8">Artifact (Product)</th>
                            <?php foreach($branch_list as $bl): ?>
                                <th class="p-8 text-center"><?php echo $bl['branch_name']; ?></th>
                            <?php endforeach; ?>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-white/5">
                        <?php while($p = $products->fetch_assoc()): ?>
                        <tr class="hover:bg-white/[0.02] transition-colors">
                            <td class="p-8">
                                <p class="text-sm font-black text-white uppercase italic"><?php echo $p['name']; ?></p>
                                <p class="text-[9px] text-gray-600 font-mono mt-1">SKU: <?php echo $p['sku']; ?></p>
                            </td>
                            <?php foreach($branch_list as $bl): 
                                $pid = $p['id']; $bid = $bl['id'];
                                $stock = $conn->query("SELECT stock_qty FROM inventory WHERE product_id=$pid AND branch_id=$bid")->fetch_assoc()['stock_qty'] ?? 0;
                            ?>
                            <td class="p-8">
                                <input type="number" name="stock[<?php echo $pid; ?>][<?php echo $bid; ?>]" 
                                       value="<?php echo $stock; ?>" 
                                       class="w-20 mx-auto block bg-black border border-white/10 p-3 rounded-xl text-center text-[#00df81] font-black outline-none focus:border-[#00df81]">
                            </td>
                            <?php endforeach; ?>
                        </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <div class="sticky bottom-10 mt-10">
            <button name="bulk_update" class="w-full bg-[#00df81] text-black font-black py-6 rounded-3xl uppercase text-[10px] tracking-[0.3em] shadow-2xl shadow-emerald-500/20 active:scale-95 transition-all">
                Synchronize Global Inventory
            </button>
        </div>
    </form>
</main>

<?php require_once 'common/footer.php'; ?>