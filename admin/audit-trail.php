<?php 
require_once 'common/header.php'; 
require_once 'common/sidebar.php'; 
require_once 'common/auth_guard.php';

guardPage('settings'); // RBAC Check

$logs = $conn->query("SELECT * FROM audit_logs ORDER BY id DESC LIMIT 500");
?>

<main class="flex-1 p-6 lg:p-10">
    <div class="mb-10 flex justify-between items-center">
        <div>
            <h2 class="text-3xl font-black italic text-white uppercase tracking-tighter">Forensic Audit</h2>
            <p class="text-gray-500 text-xs font-bold uppercase tracking-widest mt-1">System-wide Action Logs</p>
        </div>
        <button onclick="window.print()" class="bg-white text-black font-black px-6 py-3 rounded-xl text-[10px] uppercase shadow-lg"><i class="fa-solid fa-print mr-2"></i> Print Report</button>
    </div>

    <div class="bg-[#111] rounded-[48px] border border-white/5 overflow-hidden shadow-2xl">
        <div class="overflow-x-auto no-scrollbar">
            <table class="w-full text-left">
                <thead>
                    <tr class="bg-black text-[10px] font-black uppercase text-gray-600 tracking-widest border-b border-white/5">
                        <th class="p-6">Timestamp</th>
                        <th class="p-6">Entity</th>
                        <th class="p-6">Operation</th>
                        <th class="p-6">Intelligence Details</th>
                        <th class="p-6 text-right">Node IP</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-white/5">
                    <?php while($l = $logs->fetch_assoc()): 
                        $lvl_color = "text-blue-500";
                        if(strpos($l['action_type'], 'DELETE') !== false) $lvl_color = "text-red-500";
                        if(strpos($l['action_type'], 'SUCCESS') !== false) $lvl_color = "text-emerald-500";
                    ?>
                    <tr class="hover:bg-white/[0.02] transition-colors">
                        <td class="p-6 text-xs font-mono text-gray-500 whitespace-nowrap"><?php echo date('M d, H:i:s', strtotime($l['created_at'])); ?></td>
                        <td class="p-6">
                            <span class="text-xs font-black text-white uppercase italic"><?php echo $l['admin_name']; ?></span>
                        </td>
                        <td class="p-6">
                            <span class="<?php echo $lvl_color; ?> text-[9px] font-black uppercase px-2 py-1 bg-white/5 rounded-lg border border-white/5"><?php echo $l['action_type']; ?></span>
                        </td>
                        <td class="p-6 text-xs text-gray-400 max-w-xs truncate" title="<?php echo $l['description']; ?>">
                            <?php echo $l['description']; ?>
                        </td>
                        <td class="p-6 text-right text-[10px] font-mono text-gray-600"><?php echo $l['ip_address']; ?></td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>
</main>

<?php require_once 'common/footer.php'; ?>