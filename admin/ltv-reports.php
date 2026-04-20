<?php 
require_once 'common/header.php'; 
require_once 'common/sidebar.php'; 
require_once 'common/auth_guard.php';

guardPage('reports');

$query = "SELECT u.id, u.full_name, u.email, u.phone, 
          COUNT(o.id) as total_orders, 
          SUM(o.total_amount) as total_spent,
          MAX(o.created_at) as last_purchase
          FROM users u 
          JOIN orders o ON o.user_id = u.id 
          WHERE o.order_status = 'delivered'
          GROUP BY u.id 
          ORDER BY total_spent DESC 
          LIMIT 50";
$res = mysqli_query($conn, $query);
?>

<main class="flex-1 p-6 lg:p-12">
    <div class="mb-12">
        <h2 class="text-3xl font-black text-white italic uppercase tracking-tighter">LTV Intelligence</h2>
        <p class="text-gray-500 text-sm mt-2 font-bold uppercase tracking-widest">Profiling High-Net-Worth Customers</p>
    </div>

    <div class="bg-[#111] rounded-[48px] border border-white/5 overflow-hidden shadow-2xl">
        <div class="overflow-x-auto no-scrollbar">
            <table class="w-full text-left">
                <thead>
                    <tr class="bg-black text-[9px] font-black uppercase text-gray-500 tracking-widest border-b border-white/5">
                        <th class="p-8">Customer Node</th>
                        <th class="p-8 text-center">Frequency</th>
                        <th class="p-8 text-right">Lifetime Value (LTV)</th>
                        <th class="p-8 text-right">Last Interaction</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-white/5">
                    <?php while($row = mysqli_fetch_assoc($res)): 
                        $initial = strtoupper(substr($row['full_name'], 0, 1));
                    ?>
                    <tr class="hover:bg-white/[0.02] transition-colors group">
                        <td class="p-8">
                            <div class="flex items-center gap-6">
                                <div class="w-12 h-12 rounded-2xl bg-[#00df81] text-black flex items-center justify-center font-black italic shadow-lg shadow-emerald-500/10">
                                    <?php echo $initial; ?>
                                </div>
                                <div>
                                    <h4 class="text-sm font-black text-white uppercase italic group-hover:text-[#00df81] transition-colors"><?php echo $row['full_name']; ?></h4>
                                    <p class="text-[9px] text-gray-500 font-mono"><?php echo $row['email']; ?></p>
                                </div>
                            </div>
                        </td>
                        <td class="p-8 text-center">
                            <span class="text-lg font-black text-white"><?php echo $row['total_orders']; ?></span>
                            <p class="text-[8px] text-gray-600 uppercase font-black tracking-tighter">Success Orders</p>
                        </td>
                        <td class="p-8 text-right">
                            <p class="text-xl font-black text-[#00df81] italic"><?php echo formatPrice($row['total_spent']); ?></p>
                            <p class="text-[8px] text-gray-600 uppercase font-black">Gross Revenue Contribution</p>
                        </td>
                        <td class="p-8 text-right">
                            <p class="text-xs font-bold text-gray-400"><?php echo date('d M, Y', strtotime($row['last_purchase'])); ?></p>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>
</main>

<?php require_once 'common/footer.php'; ?>