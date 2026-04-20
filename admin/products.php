<?php 
require_once 'common/header.php'; 
require_once 'common/sidebar.php'; 

// 1. Handle New Product Creation
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['save_product'])) {
    $name = sanitize($_POST['name']);
    $cat_id = (int)$_POST['category_id'];
    $price = (float)$_POST['base_price'];
    $sku = sanitize($_POST['sku']);
    $slug = strtolower(str_replace(' ', '-', $name)) . '-' . rand(100,999);
    
    // Image Processor
    $image_name = "";
    if(!empty($_FILES['image_main']['name'])) {
        $ext = pathinfo($_FILES['image_main']['name'], PATHINFO_EXTENSION);
        $image_name = 'ls_prod_' . time() . '.' . $ext;
        move_uploaded_file($_FILES['image_main']['tmp_name'], "../uploads/products/" . $image_name);
    }

    $sql = "INSERT INTO products (category_id, name, slug, base_price, sku, image_main, status) 
            VALUES ('$cat_id', '$name', '$slug', '$price', '$sku', '$image_name', 'published')";
    
    if($conn->query($sql)) {
        $new_id = $conn->insert_id;
        // Auto-create inventory record for branch 1
        $conn->query("INSERT INTO inventory (product_id, branch_id, stock_qty) VALUES ('$new_id', 1, 0)");
        logAudit('PRODUCT_CREATE', "Artifact added: $name (SKU: $sku)");
        $_SESSION['success'] = "Artifact Authorized & Published.";
    }
}
?>

<main class="flex-1 p-6 lg:p-10">
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-6 mb-12">
        <div>
            <h2 class="text-3xl font-black italic text-white uppercase tracking-tighter">Inventory Vault</h2>
            <p class="text-gray-500 text-xs font-bold uppercase tracking-widest mt-1">Catalog Management Engine</p>
        </div>
        <button onclick="document.getElementById('addModal').classList.remove('hidden')" class="bg-[#00df81] text-black font-black px-8 py-4 rounded-2xl text-[10px] uppercase shadow-lg shadow-emerald-500/10">Add New Artifact</button>
    </div>

    <?php showAlert(); ?>

    <div class="grid grid-cols-1 gap-4">
        <?php 
        $prods = $conn->query("SELECT p.*, c.name as cat_name FROM products p LEFT JOIN categories c ON p.category_id = c.id ORDER BY p.id DESC");
        while($p = $prods->fetch_assoc()):
        ?>
        <div class="bg-[#111] p-6 rounded-3xl border border-white/5 flex items-center justify-between group hover:border-[#00df81]/20 transition-all">
            <div class="flex items-center gap-6">
                <img src="../uploads/products/<?php echo $p['image_main']; ?>" class="w-16 h-16 rounded-2xl object-cover bg-black border border-white/5">
                <div>
                    <h4 class="text-lg font-black text-white italic uppercase"><?php echo $p['name']; ?></h4>
                    <p class="text-[9px] text-gray-500 font-bold uppercase tracking-widest"><?php echo $p['cat_name']; ?> • SKU: <?php echo $p['sku']; ?></p>
                </div>
            </div>
            <div class="text-right">
                <p class="text-lg font-black text-[#00df81] mb-1"><?php echo formatPrice($p['base_price']); ?></p>
                <div class="flex gap-4">
                    <a href="product-gallery.php?id=<?php echo $p['id']; ?>" class="text-gray-600 hover:text-white"><i class="fa-solid fa-images"></i></a>
                    <a href="products.php?delete=<?php echo $p['id']; ?>" class="text-red-900 hover:text-red-500"><i class="fa-solid fa-trash-can"></i></a>
                </div>
            </div>
        </div>
        <?php endwhile; ?>
    </div>
</main>

<!-- Add Artifact Modal -->
<div id="addModal" class="fixed inset-0 bg-black/90 backdrop-blur-xl z-[200] flex items-center justify-center p-6 hidden">
    <div class="bg-[#111] w-full max-w-2xl rounded-[48px] border border-white/10 overflow-hidden shadow-2xl">
        <div class="p-10 border-b border-white/5 flex justify-between items-center bg-black">
            <h3 class="text-xl font-black text-white italic uppercase tracking-tighter">Initialize Artifact</h3>
            <button onclick="document.getElementById('addModal').classList.add('hidden')" class="text-gray-500 hover:text-white"><i class="fa-solid fa-xmark text-2xl"></i></button>
        </div>
        <form action="" method="POST" enctype="multipart/form-data" class="p-10 space-y-6">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="text-[9px] font-black text-gray-500 uppercase tracking-widest mb-2 block">Artifact Name</label>
                    <input type="text" name="name" required class="w-full bg-black border border-white/10 p-4 rounded-2xl text-white text-xs outline-none focus:border-[#00df81]">
                </div>
                <div>
                    <label class="text-[9px] font-black text-gray-500 uppercase tracking-widest mb-2 block">SKU Identity</label>
                    <input type="text" name="sku" required class="w-full bg-black border border-white/10 p-4 rounded-2xl text-white text-xs outline-none focus:border-[#00df81]">
                </div>
                <div>
                    <label class="text-[9px] font-black text-gray-500 uppercase tracking-widest mb-2 block">Category DNA</label>
                    <select name="category_id" class="w-full bg-black border border-white/10 p-4 rounded-2xl text-white text-xs outline-none">
                        <?php $cats = $conn->query("SELECT * FROM categories"); while($c = $cats->fetch_assoc()) echo "<option value='{$c['id']}'>{$c['name']}</option>"; ?>
                    </select>
                </div>
                <div>
                    <label class="text-[9px] font-black text-gray-500 uppercase tracking-widest mb-2 block">Selling Price</label>
                    <input type="number" step="0.01" name="base_price" required class="w-full bg-black border border-white/10 p-4 rounded-2xl text-white text-xs outline-none focus:border-[#00df81]">
                </div>
            </div>
            <div>
                <label class="text-[9px] font-black text-gray-500 uppercase tracking-widest mb-2 block">Primary Visual (Main Image)</label>
                <input type="file" name="image_main" required class="w-full text-gray-500 text-[10px]">
            </div>
            <button name="save_product" class="w-full bg-white text-black font-black py-5 rounded-2xl uppercase text-[10px] tracking-widest shadow-xl active:scale-95 transition-all">Authorize Ingestion</button>
        </form>
    </div>
</div>