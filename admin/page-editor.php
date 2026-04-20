<?php 
require_once 'common/header.php'; 
require_once 'common/sidebar.php'; 
require_once 'common/auth_guard.php';

guardPage('settings');

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['publish_page'])) {
    $title = sanitize($_POST['title']);
    $slug = strtolower(str_replace(' ', '-', $title));
    $content = mysqli_real_escape_string($conn, $_POST['content']);
    $status = (int)$_POST['is_active'];

    $sql = "INSERT INTO pages (title, slug, content, is_active) 
            VALUES ('$title', '$slug', '$content', '$status')
            ON DUPLICATE KEY UPDATE title='$title', content='$content', is_active='$status'";
    
    if ($conn->query($sql)) {
        logAudit('PAGE_UPDATE', "CMS Page Modified: $title");
        $_SESSION['success'] = "Node Content Published.";
        redirect('page-editor.php');
    }
}
?>

<main class="flex-1 p-6 lg:p-10">
    <div class="mb-10 flex justify-between items-center">
        <div>
            <h2 class="text-3xl font-black italic text-white uppercase tracking-tighter">Content Architect</h2>
            <p class="text-gray-500 text-xs font-bold uppercase tracking-widest mt-1">Manage Static Info Nodes</p>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-10">
        <!-- Editor Form -->
        <div class="lg:col-span-2">
            <form action="" method="POST" class="bg-[#111] p-10 rounded-[48px] border border-white/5 space-y-8 shadow-2xl">
                <div>
                    <label class="text-[10px] font-black text-gray-500 uppercase tracking-widest mb-3 block">Page Designation (Title)</label>
                    <input type="text" name="title" required placeholder="e.g. Terms of Service" class="w-full bg-black border border-white/10 p-5 rounded-2xl text-white text-sm outline-none focus:border-[#00df81]">
                </div>

                <div>
                    <label class="text-[10px] font-black text-gray-500 uppercase tracking-widest mb-3 block">Raw HTML / Markdown Content</label>
                    <textarea name="content" rows="15" required class="w-full bg-black border border-white/10 p-8 rounded-[32px] text-white text-xs font-mono outline-none focus:border-[#00df81] leading-relaxed"></textarea>
                </div>

                <div class="flex items-center gap-4 p-6 bg-black rounded-3xl border border-white/5">
                    <input type="checkbox" name="is_active" value="1" checked class="w-6 h-6 accent-[#00df81]">
                    <span class="text-xs font-black text-white uppercase italic">Broadcast to Frontend (Public)</span>
                </div>

                <button name="publish_page" class="w-full bg-[#00df81] text-black font-black py-6 rounded-3xl uppercase text-xs tracking-[0.2em] shadow-2xl active:scale-95 transition-all">
                    Commit Page Structure
                </button>
            </form>
        </div>

        <!-- Pages List -->
        <div class="space-y-6">
            <h3 class="text-xs font-black text-gray-600 uppercase tracking-widest ml-4">Stored Page Nodes</h3>
            <?php 
            $pgs = $conn->query("SELECT * FROM pages ORDER BY id DESC");
            while($p = $pgs->fetch_assoc()):
            ?>
            <div class="bg-[#111] p-6 rounded-3xl border border-white/5 flex justify-between items-center group hover:border-[#00df81]/40 transition-all">
                <div>
                    <h4 class="text-sm font-black text-white uppercase italic"><?php echo $p['title']; ?></h4>
                    <p class="text-[9px] text-gray-600 font-mono mt-1">/page.php?slug=<?php echo $p['slug']; ?></p>
                </div>
                <div class="flex gap-3">
                    <a href="../page.php?slug=<?php echo $p['slug']; ?>" target="_blank" class="w-10 h-10 rounded-xl bg-black flex items-center justify-center text-gray-400 hover:text-white"><i class="fa-solid fa-arrow-up-right-from-square"></i></a>
                    <button class="w-10 h-10 rounded-xl bg-black flex items-center justify-center text-red-900 hover:text-red-500"><i class="fa-solid fa-trash"></i></button>
                </div>
            </div>
            <?php endwhile; ?>
        </div>
    </div>
</main>

<?php require_once 'common/footer.php'; ?>