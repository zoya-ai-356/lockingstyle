<?php 
require_once 'common/header.php'; 

// Cart Actions
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $action = $_POST['action'];
    $p_id = (int)$_POST['product_id'];

    if ($action == 'add') {
        $qty = (int)$_POST['qty'];
        $res = $conn->query("SELECT name, base_price, image_main FROM products WHERE id = $p_id");
        $p = $res->fetch_assoc();
        
        // Cart item unique ID
        $cart_id = $p_id; 

        if (isset($_SESSION['cart'][$cart_id])) {
            $_SESSION['cart'][$cart_id]['qty'] += $qty;
        } else {
            $_SESSION['cart'][$cart_id] = [
                'id' => $p_id,
                'name' => $p['name'],
                'price' => $p['base_price'],
                'image' => $p['image_main'],
                'qty' => $qty
            ];
        }
        $_SESSION['success'] = "Artifact secured in bag.";
    }

    if ($action == 'remove') {
        unset($_SESSION['cart'][$p_id]);
    }

    if ($action == 'update') {
        $_SESSION['cart'][$p_id]['qty'] = (int)$_POST['qty'];
    }
    redirect('cart.php');
}
?>

<div class="p-6 pb-32 max-w-2xl mx-auto">
    <div class="mb-10 flex justify-between items-end">
        <h2 class="text-4xl font-black italic uppercase tracking-tighter">Your Bag</h2>
        <p class="text-gray-500 text-[10px] font-black uppercase tracking-widest"><?php echo getCartCount(); ?> Artifacts</p>
    </div>

    <?php showAlert(); ?>

    <?php if (empty($_SESSION['cart'])): ?>
        <div class="py-20 text-center bg-[#111] rounded-[48px] border border-dashed border-white/10">
            <i class="fa-solid fa-bag-shopping text-6xl text-gray-800 mb-6"></i>
            <p class="text-gray-500 uppercase font-black text-xs tracking-widest">Bag is empty</p>
            <a href="category.php" class="mt-8 inline-block text-[#00df81] font-black uppercase text-[10px] border-b border-[#00df81] pb-1">Explore Drops</a>
        </div>
    <?php else: ?>
        <div class="space-y-6">
            <?php 
            $subtotal = 0;
            foreach ($_SESSION['cart'] as $id => $item): 
                $line_total = $item['price'] * $item['qty'];
                $subtotal += $line_total;
            ?>
            <div class="bg-[#111] p-5 rounded-[32px] border border-white/5 flex gap-6 relative">
                <img src="uploads/products/<?php echo $item['image']; ?>" class="w-24 h-24 rounded-2xl object-cover bg-black">
                <div class="flex-1">
                    <h3 class="text-sm font-black text-white uppercase italic truncate pr-8"><?php echo $item['name']; ?></h3>
                    <p class="text-[#00df81] font-black mt-1"><?php echo formatPrice($item['price']); ?></p>
                    
                    <div class="mt-4 flex items-center gap-4">
                        <form action="" method="POST" class="flex items-center bg-black rounded-xl border border-white/10 p-1">
                            <input type="hidden" name="action" value="update">
                            <input type="hidden" name="product_id" value="<?php echo $id; ?>">
                            <button name="qty" value="<?php echo $item['qty']-1; ?>" class="w-8 h-8 text-white">-</button>
                            <span class="w-8 text-center text-xs font-black text-white"><?php echo $item['qty']; ?></span>
                            <button name="qty" value="<?php echo $item['qty']+1; ?>" class="w-8 h-8 text-[#00df81]">+</button>
                        </form>
                    </div>
                </div>
                <form action="" method="POST" class="absolute top-5 right-5">
                    <input type="hidden" name="action" value="remove">
                    <input type="hidden" name="product_id" value="<?php echo $id; ?>">
                    <button type="submit" class="text-red-900 hover:text-red-500"><i class="fa-solid fa-xmark"></i></button>
                </form>
            </div>
            <?php endforeach; ?>
        </div>

        <div class="mt-12 bg-[#111] p-10 rounded-[48px] border border-white/5 space-y-4 shadow-2xl">
            <div class="flex justify-between text-[10px] font-black text-gray-500 uppercase tracking-widest">
                <span>Total Value</span>
                <span class="text-white"><?php echo formatPrice($subtotal); ?></span>
            </div>
            <div class="flex justify-between text-[10px] font-black text-gray-500 uppercase tracking-widest">
                <span>Logistics</span>
                <span class="text-primary italic">Free Shipping</span>
            </div>
            <div class="pt-6 border-t border-white/5 flex justify-between items-center">
                <span class="text-xl font-black text-white uppercase italic">Grand Total</span>
                <span class="text-3xl font-black text-[#00df81]"><?php echo formatPrice($subtotal); ?></span>
            </div>
            
            <a href="checkout.php" class="block w-full bg-white text-black text-center font-black py-6 rounded-[24px] uppercase text-xs tracking-[0.2em] shadow-2xl hover:bg-[#00df81] transition-all mt-8">
                Proceed to Checkout
            </a>
        </div>
    <?php endif; ?>
</div>

<?php require_once 'common/bottom.php'; ?>