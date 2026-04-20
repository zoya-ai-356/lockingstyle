<?php 
require_once 'common/header.php'; 
require_once 'common/sidebar.php'; 
require_once 'common/auth_guard.php';

guardPage('settings');

// Initialize Banners table
mysqli_query($conn, "CREATE TABLE IF NOT EXISTS hero_banners (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255),
    subtitle VARCHAR(255),
    image_path VARCHAR(255),
    link_url VARCHAR(255),
    sort_order INT DEFAULT 0
)");

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['upload_banner'])) {
    $title = sanitize($_POST['title']);
    $subtitle = sanitize($_POST['subtitle']);
    $url = sanitize($_POST['url']);
    
    $image = "";
    if(!empty($_FILES['banner_img']['name'])) {
        $ext = pathinfo($_FILES['banner_img']['name'], PATHINFO_EXTENSION);
        $image = 'hero_' . time() . '.' . $ext;
        move_uploaded_file($_FILES['banner_img']['tmp_name'], "../uploads/slides/" . $image);
    }

    mysqli_query($conn, "INSERT INTO hero_banners (title, subtitle, image_path, link_url) VALUES ('$title', '$subtitle', '$image', '$url')");
    $_SESSION['success'] = "Hero frame deployed.";
}

$banners = mysqli_query($conn, "SELECT * FROM hero_banners ORDER BY sort_order ASC");
?>

<main class="flex-1 p-6 lg:p-12">
    <div class="mb-12 flex justify-between items-center">
        <div>
            <h2 class="text-3xl font-black text-white italic uppercase tracking-tighter text-transparent bg-clip-text bg-gradient-to-r from-[#00df81] to-white">Visual Architect</h2>
            <p class="text-gray-500 text-sm mt-2 font-bold uppercase tracking-widest">Managing Homepage Cinematic Banners</p>
        </div>
        <button onclick="document.getElementById('bannerModal').classList.remove('hidden')" class="bg-[#00df81] text-black font-black px-8 py-4 rounded-2xl shadow-xl uppercase text-[10px]">
            Add Slide
        </button>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-10">
        <?php while($b = mysqli_fetch_assoc($banners)): ?>
        <div class="bg-[#111] rounded-[48px] border border-white/5 overflow-hidden shadow-2xl relative group">
            <div class="h-64 relative">
                <img src="../uploads/slides/<?php echo $b['image_path']; ?>" class="w-full h-full object-cover opacity-60">
                <div class="absolute inset-0 bg-gradient-to-t from-black to-transparent"></div>
                <div class="absolute bottom-6 left-8">
                    <h4 class="text-xl font-black text-white italic uppercase leading-none"><?php echo $b['title']; ?></h4>
                    <p class="text-[10px] text-[#00df81] font-bold mt-2 uppercase tracking-widest"><?php echo $b['subtitle']; ?></p>
                </div>
            </div>
            <div class="p-6 flex justify-between items-center bg-black">
                <p class="text-[9px] text-gray-600 font-mono"><?php echo $b['link_url']; ?></p>
                <button class="text-red-500 text-xs hover:scale-110 transition-transform"><i class="fa-solid fa-trash"></i></button>
            </div>
        </div>
        <?php endwhile; ?>
    </div>
</main>

<!-- Modal -->
<div id="bannerModal" class="fixed inset-0 bg-black/90 backdrop-blur-xl z-[200] flex items-center justify-center p-6 hidden">
    <div class="bg-[#111] w-full max-w-md rounded-[48px] border border-white/10 overflow-hidden">
        <div class="p-8 border-b border-white/5 flex justify-between items-center">
            <h3 class="text-xl font-black text-white uppercase italic">New Hero Frame</h3>
            <button onclick="document.getElementById('bannerModal').classList.add('hidden')" class="text-gray-500 hover:text-white"><i class="fa-solid fa-xmark text-2xl"></i></button>
        </div>
        <form action="" method="POST" enctype="multipart/form-data" class="p-8 space-y-6">
            <input type="text" name="title" placeholder="PRIMARY HEADING" required class="w-full bg-black border border-white/10 p-5 rounded-2xl text-white text-xs outline-none">
            <input type="text" name="subtitle" placeholder="TAGLINE / SUBTITLE" class="w-full bg-black border border-white/10 p-5 rounded-2xl text-white text-xs outline-none">
            <input type="text" name="url" placeholder="REDIRECT URL (e.g. category.php?id=1)" class="w-full bg-black border border-white/10 p-5 rounded-2xl text-white text-xs outline-none">
            <input type="file" name="banner_img" required class="w-full text-[10px] text-gray-500">
            <button name="upload_banner" class="w-full bg-[#00df81] text-black font-black py-5 rounded-2xl uppercase text-[10px] tracking-widest shadow-xl">Deploy Frame</button>
        </form>
    </div>
</div>

<?php require_once 'common/footer.php'; ?>