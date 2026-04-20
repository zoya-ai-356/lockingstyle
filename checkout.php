<?php 
require_once 'common/header.php'; 
checkUserAuth();

if(empty($_SESSION['cart'])) redirect('index.php');

$user_id = $_SESSION['user_id'];
$u_data = $conn->query("SELECT * FROM users WHERE id = $user_id")->fetch_assoc();

$total = 0;
foreach($_SESSION['cart'] as $item) { $total += ($item['price'] * $item['qty']); }

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['place_order'])) {
    $order_no = 'LS' . strtoupper(substr(md5(uniqid()), 0, 8));
    $address = sanitize($_POST['address']);
    $method = sanitize($_POST['payment_method']);

    // Insert Order
    $sql = "INSERT INTO orders (user_id, order_number, total_amount, payment_method, order_status, shipping_address) 
            VALUES ('$user_id', '$order_no', '$total', '$method', 'pending', '$address')";
    
    if ($conn->query($sql)) {
        $order_id = $conn->insert_id;
        foreach ($_SESSION['cart'] as $item) {
            $pid = $item['id']; $qty = $item['qty']; $pr = $item['price']; $nm = sanitize($item['name']);
            $conn->query("INSERT INTO order_items (order_id, product_id, product_name, quantity, price) VALUES ('$order_id', '$pid', '$nm', '$qty', '$pr')");
        }
        
        unset($_SESSION['cart']);
        redirect("confirm-order.php?id=$order_id");
    }
}
?>

<div class="p-8 max-w-2xl mx-auto pb-32">
    <h2 class="text-3xl font-black italic uppercase mb-10 text-white tracking-tighter">Shipping <span class="text-[#00df81]">& Pay</span></h2>

    <form action="" method="POST" class="space-y-8">
        <div class="bg-[#111] p-8 rounded-[40px] border border-white/5 space-y-6">
            <h3 class="text-[10px] font-black text-gray-500 uppercase tracking-widest mb-4">Fulfillment Details</h3>
            <input type="text" value="<?php echo $u_data['full_name']; ?>" readonly class="w-full bg-black border border-white/10 p-5 rounded-2xl text-gray-400 text-xs outline-none">
            <textarea name="address" required placeholder="COMPLETE SHIPPING ADDRESS" class="w-full bg-black border border-white/10 p-5 rounded-2xl text-white text-xs outline-none focus:border-[#00df81]"><?php echo $u_data['address']; ?></textarea>
        </div>

        <div class="bg-[#111] p-8 rounded-[40px] border border-white/5 space-y-4">
            <h3 class="text-[10px] font-black text-gray-500 uppercase tracking-widest mb-4">Payment Node</h3>
            <label class="flex items-center gap-4 p-5 bg-black rounded-2xl border border-white/5 cursor-pointer has-[:checked]:border-[#00df81] transition-all">
                <input type="radio" name="payment_method" value="cod" checked class="accent-[#00df81]">
                <span class="text-xs font-bold text-white uppercase italic">Cash on Delivery</span>
            </label>
            <label class="flex items-center gap-4 p-5 bg-black rounded-2xl border border-white/5 cursor-pointer has-[:checked]:border-[#00df81] transition-all">
                <input type="radio" name="payment_method" value="whatsapp" class="accent-[#00df81]">
                <span class="text-xs font-bold text-white uppercase italic">Pay via WhatsApp</span>
            </label>
        </div>

        <div class="bg-[#00df81] p-8 rounded-[32px] flex justify-between items-center shadow-xl shadow-emerald-500/10">
            <span class="text-black font-black uppercase italic">Total Due</span>
            <span class="text-2xl font-black text-black"><?php echo formatPrice($total); ?></span>
        </div>

        <button name="place_order" class="w-full bg-white text-black font-black py-6 rounded-[24px] uppercase text-xs tracking-[0.2em] shadow-2xl active:scale-95 transition-all">
            Secure This Order
        </button>
    </form>
</div>

<?php require_once 'common/bottom.php'; ?>