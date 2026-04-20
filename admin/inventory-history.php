<?php 
require_once 'common/header.php'; 
require_once 'common/sidebar.php'; 
require_once 'common/auth_guard.php';

guardPage('inventory');

// Ensure the inventory logs table exists
mysqli_query($conn, "CREATE TABLE IF NOT EXISTS inventory_logs (
    id INT AUTO_INCREMENT PRIMARY KEY,
    product_id INT,
    branch_id INT,
    admin_id INT,
    change_qty INT,
    action_type ENUM('restock', 'sale', 'transfer', 'adjustment', 'return'),
    remarks TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
)");

$history = mysqli_query($conn, "SELECT l.*, p.name as p_name, p.sku, b.branch_name, a.full_name as admin_name 
                                FROM inventory_logs l
                                JOIN products p ON l.product_id = p.id
                                JOIN branches b ON l.branch_id = b.id
                                LEFT JOIN admin a ON l.admin_id = a.id
                                ORDER BY l.id DESC LIMIT 100");
?>

<main class="flex-1 p-6 lg:p-12">
    <div class="mb-12 flex justify-between items-end">
        <div>
            <h2 class="text-3xl font-black text-white italic uppercase tracking-tighter">Stock Ledger</h2>
            <p class="text-gray-500 text-sm mt-2 font-bold uppercase tracking-widest">Forensic Inventory Movement Log</p>
        </div>
        <div class="bg-card-bg p-4 rounded-2xl border border-white/5">
            <span class="text-[9px] font-black text-gray-500 uppercase tracking-widest block mb-1">Total Logs</span>
            <span class="text-xl font-black text-primary"><?php echo mysqli_num_rows($history); ?> Entries</span>
        </div>
    </div>

    <div class="bg-[#111] rounded-[48px] border border-white/5 overflow-hidden shadow-2xl">
        <div class="overflow-x-auto no-scrollbar">
            <table class="w-full text-left">
                <thead>
                    <tr class="bg-black text-[9px] font-black uppercase text-gray-600 tracking-widest border-b border-white/5">
                        <th class="p-8">Timestamp</th>
                        <th class="p-8">Artifact</th>
                        <th class="p-8">Node (Branch)</th>
                        <th class="p-8 text-center">Movement</th>
                        <th class="p-8">Context</th>
                        <th class="p-8 text-right">Operator</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-white/5">
                    <?php while($log = mysqli_fetch_assoc($history)): 
                        $is_positive = ($log['change_qty'] > 0);
                        $type_color = [
                            'restock' => 'text-emerald-500',
                            'sale' => 'text-blue-500',
                            'transfer' => 'text-purple-500',
                            'adjustment' => 'text-orange-500',
                            'return' => 'text-yellow-500'
                        ];
                    ?>
                    <tr class="hover:bg-white/[0.02] transition-colors">
                        <td class="p-8 text-xs font-mono text-gray-500 whitespace-nowrap"><?php echo date('M d, H:i', strtotime($log['created_at'])); ?></td>
                        <td class="p-8">
                            <p class="text-sm font-bold text-white uppercase italic"><?php echo $log['p_name']; ?></p>
                            <p class="text-[9px] text-gray-600 font-mono"><?php echo $log['sku']; ?></p>
                        </td>
                        <td class="p-8">
                            <span class="text-[10px] font-black text-gray-400 uppercase tracking-widest"><?php echo $log['branch_name']; ?></span>
                        </td>
                        <td class="p-8 text-center">
                            <span class="text-lg font-black <?php echo $is_positive ? 'text-emerald-500' : 'text-red-500'; ?>">
                                <?php echo $is_positive ? '+' : ''; ?><?php echo $log['change_qty']; ?>
                            </span>
                        </td>
                        <td class="p-8">
                            <span class="px-2 py-1 bg-white/5 rounded text-[8px] font-black uppercase <?php echo $type_color[$log['action_type']]; ?> border border-white/5">
                                <?php echo $log['action_type']; ?>
                            </span>
                            <p class="text-[10px] text-gray-500 mt-2 italic"><?php echo $log['remarks']; ?></p>
                        </td>
                        <td class="p-8 text-right">
                            <p class="text-[10px] font-black text-white uppercase"><?php echo $log['admin_name'] ?? 'System'; ?></p>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>
</main>

<?php require_once 'common/footer.php'; ?>