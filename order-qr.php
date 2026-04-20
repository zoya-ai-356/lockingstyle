<?php 
require_once 'common/header.php'; 
checkUserAuth();

$order_no = sanitize($_GET['no'] ?? '');
$res = mysqli_query($conn, "SELECT id, total_amount, order_status FROM orders WHERE order_number = '$order_no' AND user_id = '".$_SESSION['user_id']."'");
$order = mysqli_fetch_assoc($res);

if (!$order) redirect('profile.php');

// Payload for QR (Identity Verification)
$payload = "LS-ORD-" . $order_no . "-AMT-" . $order['total_amount'];
$qr_url = "https://chart.googleapis.com/chart?chs=300x300&cht=qr&chl=" . urlencode($payload) . "&choe=UTF-8";
?>

<div class="p-8 md:p-20 max-w-2xl mx-auto text-center">
    <div class="mb-12">
        <h2 class="text-4xl font-black text-white italic uppercase tracking-tighter">Identity Token</h2>
        <p class="text-gray-500 text-sm mt-4 uppercase font-bold tracking-widest">Show this at the collection node</p>
    </div>

    <div class="bg-white p-10 rounded-[60px] shadow-[0_0_50px_rgba(0,223,129,0.2)] inline-block relative overflow-hidden group">
        <!-- Visual Decoration -->
        <div class="absolute top-0 left-0 w-full h-2 bg-primary"></div>
        
        <img src="<?php echo $qr_url; ?>" class="w-64 h-64 mx-auto mb-6 grayscale group-hover:grayscale-0 transition-all duration-700" alt="Order QR">
        
        <div class="pt-6 border-t border-gray-100">
            <p class="text-black font-black text-xl italic uppercase">#<?php echo $order_no; ?></p>
            <p class="text-gray-400 text-[10px] font-bold uppercase tracking-widest mt-1">Order Status: <?php echo strtoupper($order['order_status']); ?></p>
        </div>
    </div>

    <div class="mt-12 grid grid-cols-1 gap-4">
        <button onclick="window.print()" class="w-full bg-[#111] border border-white/10 text-white font-black py-5 rounded-3xl uppercase text-xs tracking-widest hover:bg-white hover:text-black transition-all">
            <i class="fa-solid fa-download mr-2"></i> Save Digital Pass
        </button>
        <a href="profile.php" class="text-gray-600 text-[10px] font-black uppercase tracking-[0.4em] hover:text-primary transition-colors">Return to Vault</a>
    </div>
</div>

<?php require_once 'common/bottom.php'; ?>