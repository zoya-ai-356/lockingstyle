<?php 
require_once 'common/header.php'; 

$slug = sanitize($_GET['slug'] ?? '');
$res = $conn->query("SELECT * FROM pages WHERE slug = '$slug' AND is_active = 1");
$page = $res->fetch_assoc();

if (!$page) {
    echo "<div class='py-40 text-center'><h1 class='text-6xl font-black text-white mb-4 italic'>404</h1><p class='text-gray-500 uppercase font-black text-xs tracking-[0.5em]'>Node Not Found</p></div>";
    require_once 'common/bottom.php';
    exit();
}
?>

<div class="max-w-4xl mx-auto p-8 pb-40 pt-16">
    <div class="mb-16">
        <h1 class="text-5xl font-black text-white italic uppercase leading-none mb-4"><?php echo $page['title']; ?></h1>
        <div class="h-1 w-20 bg-[#00df81]"></div>
    </div>

    <div class="prose prose-invert max-w-none text-gray-400 leading-[1.8] text-sm md:text-base italic">
        <?php echo $page['content']; ?>
    </div>
</div>

<style>
    /* Styling for CMS generated content */
    .prose h1, .prose h2, .prose h3 { color: #fff; font-family: 'Syncopate', sans-serif; font-style: italic; margin-top: 3rem; margin-bottom: 1rem; text-transform: uppercase; }
    .prose h2 { font-size: 1.5rem; border-left: 4px solid #00df81; padding-left: 1rem; }
    .prose p { margin-bottom: 2rem; }
    .prose ul { list-style-type: square; color: #00df81; margin-left: 1.5rem; margin-bottom: 2rem; }
    .prose ul li { color: #a0a0a0; padding-left: 0.5rem; }
</style>

<?php require_once 'common/bottom.php'; ?>