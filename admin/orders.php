<?php 
require_once 'common/header.php'; 
require_once 'common/sidebar.php'; 

// Update Status Logic
if(isset($_POST['update_status'])) {
    $oid = (int)$_POST['order_id'];
    $st = sanitize($_POST['status']);
    $conn->query("UPDATE orders SET order_status = '$st' WHERE id = $oid");
    $_SESSION['success'] = "Order #$oid updated to $st";
}

$status_filter = $_GET['status'] ?? 'pending';
$orders = $conn->query("SELECT o.*, u.full_name FROM orders o LEFT JOIN users u ON o.user_id = u.id WHERE o.order_status = '$status_filter' ORDER BY o.id DESC");
?>

<main class="flex-1 p-6 lg:p-10">
    <div class="mb-10">
        <h2 class="text-3xl font-black italic text-white uppercase tracking-tighter">Logistics Flow</h2>
        <p class="text-gray-500 text-xs font-bold uppercase tracking-widest mt-1">Order Fulfillment Center</p>
    </div>

    <!-- Status Tabs -->
    <div class="flex gap-2 mb-8 bg-[#111] p-1 rounded-2xl w-max border border-white/5">
        <a href="orders.php?status=pending" class="px-6 py-3 rounded-xl text-[10px] font-black uppercase transition-all <?php echo $status_filter=='pending'?'bg-[#00df81] text-black':'text-gray-500'; ?>">Pending</a>
        <a href="orders.php?status=packing" class="px-6 py-3 rounded-xl text-[10px] font-black uppercase transition-all <?php echo $status_filter=='packing'?'bg-[#00df81] text-black':'text-gray-500'; ?>">Packing</a>
        <a href="orders.php?status=shipped" class="px-6 py-3 rounded-xl text-[10px] font-black uppercase transition-all <?php echo $status_filter=='shipped'?'bg-[#00df81] text-black':'text-gray-500'; ?>">Shipped</a>
        <a href="orders.php?status=delivered" class="px-6 py-3 rounded-xl text-[10px] font-black uppercase transition-all <?php echo $status_filter=='delivered'?'bg-[#00df81] text-black':'text-gray-500'; ?>">Delivered</a>
    </div>

    <div class="space-y-4">
        <?php while($o = $orders->fetch_assoc()): ?>
        <div class="bg-[#111] p-8 rounded-[40px] border border-white/5">
            <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-6 mb-8">
                <div>
                    <h4 class="text-xl font-black text-white italic">#<?php echo $o['order_number']; ?></h4>
                    <p class="text-[10px] text-gray-500 font-bold uppercase mt-1"><?php echo $o['full_name'] ?? 'Guest Node'; ?> • <?php echo date('d M, H:i', strtotime($o['created_at'])); ?></p>
                </div>
                <div class="text-right">
                    <p class="text-2xl font-black text-[#00df81]"><?php echo formatPrice($o['total_amount']); ?></p>
                    <span class="text-[9px] font-black uppercase tracking-widest text-gray-600"><?php echo $o['payment_method']; ?></span>
                </div>
            </div>

            <div class="p-6 bg-black rounded-3xl border border-white/5 mb-8">
                <p class="text-[9px] font-black text-gray-600 uppercase tracking-widest mb-2">Shipping Terminal</p>
                <p class="text-xs text-gray-400 italic"><?php echo $o['shipping_address']; ?></p>
            </div>

            <div class="flex flex-wrap gap-4">
                <form action="" method="POST" class="flex-1 flex gap-2">
                    <input type="hidden" name="order_id" value="<?php echo $o['id']; ?>">
                    <select name="status" class="flex-1 bg-black border border-white/10 p-4 rounded-xl text-[10px] font-black text-white uppercase">
                        <option value="pending">Set Pending</option>
                        <option value="packing">Start Packing</option>
                        <option value="shipped">Mark Shipped</option>
                        <option value="delivered">Complete Order</option>
                        <option value="cancelled">Cancel Order</option>
                    </select>
                    <button name="update_status" class="bg-white text-black font-black px-6 rounded-xl text-[10px] uppercase">Update</button>
                </form>
                <a href="order-view.php?id=<?php echo $o['id']; ?>" class="bg-[#111] border border-white/10 text-white p-4 rounded-xl hover:bg-white hover:text-black transition-all"><i class="fa-solid fa-eye"></i></a>
            </div>
        </div>
        <?php endwhile; ?>
    </div>
</main>

<?php require_once 'common/footer.php'; ?>