<?php 
require_once 'common/header.php'; 
require_once 'common/sidebar.php'; 
require_once 'common/auth_guard.php';

guardPage('pos'); // Permission check
?>

<main class="flex-1 p-6 flex flex-col h-screen overflow-hidden">
    <div class="flex flex-col lg:flex-row gap-6 h-full pb-20">
        
        <!-- Search & Grid Section -->
        <div class="flex-1 flex flex-col gap-6 overflow-hidden">
            <div class="bg-[#111] p-6 rounded-[32px] border border-white/5">
                <input type="text" id="posSearch" onkeyup="searchProducts()" placeholder="SCAN BARCODE OR SEARCH ARTIFACT..." 
                       class="w-full bg-black border border-white/10 p-5 rounded-2xl text-white font-black text-xs tracking-widest outline-none focus:border-[#00df81]">
            </div>

            <div id="productGrid" class="flex-1 overflow-y-auto grid grid-cols-2 md:grid-cols-3 xl:grid-cols-4 gap-4 pr-2 no-scrollbar">
                <?php 
                $prods = $conn->query("SELECT p.*, i.stock_qty FROM products p JOIN inventory i ON p.id = i.product_id WHERE p.status='published'");
                while($p = $prods->fetch_assoc()):
                ?>
                <div class="bg-[#111] p-4 rounded-[32px] border border-white/5 flex flex-col items-center text-center cursor-pointer hover:border-[#00df81]/40 transition-all active:scale-95 product-item"
                     data-name="<?php echo strtolower($p['name']); ?>" data-sku="<?php echo strtolower($p['sku']); ?>"
                     onclick="addToBill('<?php echo $p['id']; ?>', '<?php echo addslashes($p['name']); ?>', '<?php echo $p['base_price']; ?>')">
                    <img src="../uploads/products/<?php echo $p['image_main']; ?>" class="w-20 h-20 rounded-2xl object-cover mb-4">
                    <h5 class="text-[10px] font-black text-white uppercase leading-tight truncate w-full"><?php echo $p['name']; ?></h5>
                    <p class="text-xs font-black text-[#00df81] mt-2"><?php echo formatPrice($p['base_price']); ?></p>
                    <p class="text-[8px] text-gray-600 font-bold uppercase mt-1">Stock: <?php echo $p['stock_qty']; ?></p>
                </div>
                <?php endwhile; ?>
            </div>
        </div>

        <!-- Billing Terminal Section -->
        <div class="w-full lg:w-[450px] bg-[#111] rounded-[48px] border border-white/5 flex flex-col overflow-hidden shadow-2xl">
            <div class="p-8 border-b border-white/5 bg-black flex justify-between items-center">
                <h3 class="text-xl font-black text-white italic uppercase tracking-tighter">Billing Node</h3>
                <i class="fa-solid fa-cash-register text-[#00df81]"></i>
            </div>

            <!-- Cart Items -->
            <div id="billItems" class="flex-1 overflow-y-auto p-6 space-y-4 no-scrollbar">
                <div id="emptyBill" class="text-center py-20 text-gray-700 uppercase font-black text-[10px] tracking-widest">Node Empty</div>
            </div>

            <!-- Summary -->
            <div class="p-8 bg-black border-t border-white/5">
                <div class="space-y-3 mb-8">
                    <div class="flex justify-between text-[10px] font-bold text-gray-500 uppercase tracking-widest">
                        <span>Subtotal</span>
                        <span id="subtotalVal">₹0.00</span>
                    </div>
                    <div class="flex justify-between text-2xl font-black text-white italic uppercase">
                        <span>Total Payable</span>
                        <span id="totalVal" class="text-[#00df81]">₹0.00</span>
                    </div>
                </div>

                <form action="process-pos.php" method="POST" onsubmit="return validatePOS()">
                    <input type="hidden" name="cart_json" id="cartJson">
                    <input type="hidden" name="final_amount" id="finalAmt">
                    
                    <div class="grid grid-cols-2 gap-4 mb-4">
                        <select name="payment_mode" class="bg-black border border-white/10 p-4 rounded-xl text-[10px] font-black text-white uppercase">
                            <option value="cash">Cash Payment</option>
                            <option value="upi">Digital UPI</option>
                            <option value="card">Bank Card</option>
                        </select>
                        <input type="text" name="customer_phone" placeholder="CUST PHONE" class="bg-black border border-white/10 p-4 rounded-xl text-[10px] font-black text-white">
                    </div>

                    <button class="w-full bg-[#00df81] text-black font-black py-5 rounded-[24px] uppercase text-[10px] tracking-[0.2em] shadow-2xl active:scale-95 transition-all">Generate Invoice</button>
                </form>
            </div>
        </div>

    </div>
</main>

<script>
let billCart = [];

function addToBill(id, name, price) {
    const exists = billCart.find(i => i.id === id);
    if(exists) { exists.qty++; } 
    else { billCart.push({ id, name, price: parseFloat(price), qty: 1 }); }
    renderBill();
}

function updateQty(id, delta) {
    const item = billCart.find(i => i.id === id);
    if(item) {
        item.qty += delta;
        if(item.qty <= 0) billCart = billCart.filter(i => i.id !== id);
        renderBill();
    }
}

function renderBill() {
    const container = document.getElementById('billItems');
    if(billCart.length === 0) {
        container.innerHTML = '<div id="emptyBill" class="text-center py-20 text-gray-700 uppercase font-black text-[10px] tracking-widest">Node Empty</div>';
    } else {
        container.innerHTML = billCart.map(i => `
            <div class="flex items-center justify-between bg-black p-5 rounded-2xl border border-white/5">
                <div class="flex-1 truncate pr-4">
                    <p class="text-[10px] font-black text-white uppercase">${i.name}</p>
                    <p class="text-[9px] text-[#00df81] font-bold mt-1">₹${i.price.toFixed(2)} x ${i.qty}</p>
                </div>
                <div class="flex items-center gap-4">
                    <button onclick="updateQty('${i.id}', -1)" class="w-8 h-8 rounded-lg bg-[#111] text-white hover:text-red-500 transition-colors">-</button>
                    <span class="text-xs font-black text-white">${i.qty}</span>
                    <button onclick="updateQty('${i.id}', 1)" class="w-8 h-8 rounded-lg bg-[#111] text-[#00df81] hover:bg-[#00df81] hover:text-black transition-all">+</button>
                </div>
            </div>
        `).join('');
    }

    const total = billCart.reduce((acc, i) => acc + (i.price * i.qty), 0);
    document.getElementById('subtotalVal').innerText = '₹' + total.toFixed(2);
    document.getElementById('totalVal').innerText = '₹' + total.toFixed(2);
    document.getElementById('cartJson').value = JSON.stringify(billCart);
    document.getElementById('finalAmt').value = total.toFixed(2);
}

function searchProducts() {
    let input = document.getElementById('posSearch').value.toLowerCase();
    let items = document.getElementsByClassName('product-item');
    for (let i = 0; i < items.length; i++) {
        if (!items[i].getAttribute('data-name').includes(input) && !items[i].getAttribute('data-sku').includes(input)) {
            items[i].style.display = "none";
        } else { items[i].style.display = "flex"; }
    }
}
</script>