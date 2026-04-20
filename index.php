<?php require_once 'common/header.php'; ?>

<div class="px-6 py-4">
    <!-- Dynamic Hero Slider -->
    <div class="relative rounded-[48px] overflow-hidden h-[500px] border border-white/5 shadow-2xl group">
        <div class="flex h-full transition-transform duration-700 ease-in-out" id="main-slider">
            <?php 
            $banners = $conn->query("SELECT * FROM hero_banners ORDER BY sort_order ASC");
            if ($banners->num_rows > 0):
                while($b = $banners->fetch_assoc()):
            ?>
            <div class="min-w-full h-full relative">
                <img src="uploads/slides/<?php echo $b['image_path']; ?>" class="w-full h-full object-cover opacity-60">
                <div class="absolute inset-0 bg-gradient-to-t from-black via-transparent to-transparent"></div>
                <div class="absolute bottom-16 left-10">
                    <span class="text-[#00df81] font-black uppercase tracking-[0.4em] text-[10px] mb-4 block"><?php echo $b['subtitle']; ?></span>
                    <h1 class="text-6xl font-black italic uppercase leading-none text-white brand-font"><?php echo str_replace(' ', '<br>', $b['title']); ?></h1>
                    <a href="<?php echo $b['link_url']; ?>" class="mt-8 inline-block bg-white text-black font-black px-10 py-4 rounded-2xl text-[10px] uppercase tracking-widest hover:bg-[#00df81] transition-all">
                        <?php _e('prod_buy_now'); ?>
                    </a>
                </div>
            </div>
            <?php 
                endwhile;
            else:
                // Default Fallback Slider
            ?>
            <div class="min-w-full h-full relative">
                <img src="https://images.unsplash.com/photo-1558769132-cb1aea458c5e?auto=format&fit=crop&w=1200" class="w-full h-full object-cover opacity-60">
                <div class="absolute inset-0 flex flex-col justify-center px-10">
                    <h1 class="text-6xl font-black italic text-white leading-none uppercase">THE<br><span class="neon-text">LIMITLESS</span><br>STYLE</h1>
                </div>
            </div>
            <?php endif; ?>
        </div>
        
        <!-- Slider Controls -->
        <button onclick="moveSlide(-1)" class="absolute left-4 top-1/2 -translate-y-1/2 w-12 h-12 bg-black/20 backdrop-blur-md rounded-full border border-white/10 text-white hover:bg-[#00df81] hover:text-black transition-all">
            <i class="fa-solid fa-chevron-left"></i>
        </button>
        <button onclick="moveSlide(1)" class="absolute right-4 top-1/2 -translate-y-1/2 w-12 h-12 bg-black/20 backdrop-blur-md rounded-full border border-white/10 text-white hover:bg-[#00df81] hover:text-black transition-all">
            <i class="fa-solid fa-chevron-right"></i>
        </button>
    </div>

    <!-- ... rest of the featured section ... -->
</div>

<script>
let currentSlide = 0;
const slider = document.getElementById('main-slider');
const totalSlides = slider.children.length;

function moveSlide(dir) {
    currentSlide += dir;
    if (currentSlide >= totalSlides) currentSlide = 0;
    if (currentSlide < 0) currentSlide = totalSlides - 1;
    slider.style.transform = `translateX(-${currentSlide * 100}%)`;
}

// Auto Slide every 8 seconds
setInterval(() => moveSlide(1), 8000);
</script>

<?php require_once 'common/bottom.php'; ?>