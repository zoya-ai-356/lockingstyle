<?php require_once 'common/header.php'; ?>

<div class="px-6 py-4 min-h-screen bg-[#0a0a0a]">
    <!-- Dynamic Hero Slider with Placeholder Fix -->
    <div class="relative rounded-[40px] overflow-hidden h-[450px] border border-white/5 shadow-2xl group bg-[#111]">
        <div class="flex h-full transition-transform duration-700 ease-in-out" id="main-slider">
            <?php 
            $banners = $conn->query("SELECT * FROM hero_banners ORDER BY sort_order ASC");
            if ($banners && $banners->num_rows > 0):
                while($b = $banners->fetch_assoc()):
            ?>
            <div class="min-w-full h-full relative">
                <img src="uploads/slides/<?php echo $b['image_path']; ?>" class="w-full h-full object-cover opacity-60">
                <div class="absolute inset-0 bg-gradient-to-t from-black via-transparent to-transparent"></div>
                <div class="absolute bottom-16 left-10">
                    <span class="text-[#00df81] font-black uppercase tracking-[0.4em] text-[10px] mb-4 block"><?php echo $b['subtitle']; ?></span>
                    <h1 class="text-5xl font-black italic uppercase leading-none text-white brand-font"><?php echo $b['title']; ?></h1>
                    <a href="<?php echo $b['link_url']; ?>" class="mt-8 inline-block bg-white text-black font-black px-10 py-4 rounded-2xl text-[10px] uppercase tracking-widest hover:bg-[#00df81] transition-all">Explore Drop</a>
                </div>
            </div>
            <?php 
                endwhile;
            else:
                // AGAR DATABASE KHALI HAI TO YEH DEFAULT BANNER DIKHEGA
            ?>
            <div class="min-w-full h-full relative bg-gradient-to-br from-[#111] to-[#000] flex items-center justify-center">
                <div class="text-center px-10">
                    <span class="text-[#00df81] font-black uppercase tracking-[0.4em] text-[10px] mb-4 block animate-pulse">Initializing Terminal</span>
                    <h1 class="text-6xl font-black italic text-white leading-none uppercase">THE<br><span class="neon-text">FUTURE</span><br>STYLE</h1>
                    <p class="text-gray-500 text-[10px] mt-6 uppercase tracking-widest">No banners found. Please upload from Admin Panel.</p>
                </div>
            </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Category Section -->
    <div class="mt-12 overflow-x-auto no-scrollbar pb-4">
        <h3 class="text-[10px] font-black text-gray-600 uppercase tracking-[0.3em] mb-6 ml-2">Browse Categories</h3>
        <div class="flex gap-6">
            <?php 
            $cats = $conn->query("SELECT * FROM categories WHERE status='active'");
            if($cats && $cats->num_rows > 0):
                while($c = $cats->fetch_assoc()):
            ?>
            <a href="category.php?id=<?php echo $c['id']; ?>" class="flex-shrink-0 text-center group">
                <div class="w-20 h-20 rounded-[28px] border border-white/10 p-1 group-hover:border-[#00df81] transition-all bg-[#111]">
                    <img src="uploads/categories/<?php echo $c['image']; ?>" class="w-full h-full object-cover rounded-[24px]">
                </div>
                <p class="text-[8px] font-black uppercase tracking-widest mt-3 text-gray-500"><?php echo $c['name']; ?></p>
            </a>
            <?php endwhile; 
            else: 
                echo "<p class='text-gray-700 text-[10px] uppercase font-bold ml-2'>No categories active.</p>";
            endif; ?>
        </div>
    </div>

    <!-- Featured Section -->
    <div class="mt-16 pb-20">
        <div class="flex justify-between items-end mb-8 px-2">
            <h2 class="text-2xl font-black italic uppercase">Latest<br><span class="text-[#00df81]">Drops</span></h2>
            <a href="category.php" class="text-[9px] font-black text-gray-600 uppercase tracking-widest border-b border-gray-800 pb-1">View Collection</a>
        </div>
        
        <div class="grid grid-cols-2 gap-4">
            <?php 
            $prods = $conn->query("SELECT * FROM products WHERE status='published' ORDER BY id DESC LIMIT 4");
            if($prods && $prods->num_rows > 0):
                while($p = $prods->fetch_assoc()):
            ?>
            <div class="bg-[#111] rounded-[32px] p-3 border border-white/5 group">
                <a href="product-details.php?id=<?php echo $p['id']; ?>">
                    <div class="aspect-square bg-black rounded-[24px] overflow-hidden mb-4 relative">
                        <img src="uploads/products/<?php echo $p['image_main']; ?>" class="w-full h-full object-cover group-hover:scale-110 transition-all duration-700">
                    </div>
                    <h3 class="text-[10px] font-bold text-white uppercase truncate px-1"><?php echo $p['name']; ?></h3>
                    <p class="text-xs font-black text-[#00df81] mt-1 px-1"><?php echo formatPrice($p['base_price']); ?></p>
                </a>
            </div>
            <?php endwhile; 
            else: ?>
                <div class="col-span-2 py-10 text-center bg-[#111] rounded-3xl border border-dashed border-white/5">
                    <p class="text-gray-600 text-[10px] font-black uppercase">Artifacts not found</p>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php require_once 'common/bottom.php'; ?>