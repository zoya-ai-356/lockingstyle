<?php 
require_once 'common/header.php'; 
require_once 'common/sidebar.php'; 
require_once 'common/auth_guard.php';

guardPage('products');

// Action Handlers
if (isset($_GET['approve'])) {
    $id = (int)$_GET['approve'];
    mysqli_query($conn, "UPDATE reviews SET status = 'approved' WHERE id = '$id'");
    $_SESSION['success'] = "Review approved.";
}

if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    mysqli_query($conn, "DELETE FROM reviews WHERE id = '$id'");
    $_SESSION['success'] = "Review purged.";
}

$reviews = mysqli_query($conn, "SELECT r.*, u.full_name, p.name as product_name 
                                FROM reviews r 
                                JOIN users u ON r.user_id = u.id 
                                JOIN products p ON r.product_id = p.id 
                                ORDER BY r.status DESC, r.created_at DESC");
?>

<main class="flex-1 p-6 lg:p-12">
    <div class="mb-12">
        <h2 class="text-3xl font-black text-white italic uppercase tracking-tighter">Review Moderation</h2>
        <p class="text-gray-500 text-sm mt-2 font-bold uppercase tracking-widest">Managing Social Proof & Feedback</p>
    </div>

    <div class="grid grid-cols-1 gap-6">
        <?php while($r = mysqli_fetch_assoc($reviews)): ?>
        <div class="bg-[#111] p-8 rounded-[40px] border border-white/5 shadow-2xl flex flex-col md:flex-row justify-between items-center gap-8">
            <div class="flex-1">
                <div class="flex items-center gap-3 mb-4">
                    <span class="px-3 py-1 bg-yellow-500/10 text-yellow-500 text-[10px] font-black rounded-full uppercase border border-yellow-500/20">
                        <?php echo $r['rating']; ?> Stars
                    </span>
                    <span class="text-gray-600 text-[10px] font-bold uppercase"><?php echo date('d M Y', strtotime($r['created_at'])); ?></span>
                </div>
                <h4 class="text-lg font-black text-white italic uppercase"><?php echo $r['full_name']; ?></h4>
                <p class="text-xs text-[#00df81] font-bold uppercase mt-1">Product: <?php echo $r['product_name']; ?></p>
                <p class="text-sm text-gray-400 mt-4 leading-relaxed italic">"<?php echo $r['comment']; ?>"</p>
            </div>

            <div class="flex gap-4">
                <?php if($r['status'] == 'pending'): ?>
                    <a href="reviews.php?approve=<?php echo $r['id']; ?>" class="bg-emerald-500 text-black font-black px-6 py-3 rounded-xl text-[10px] uppercase shadow-lg">Approve</a>
                <?php endif; ?>
                <a href="reviews.php?delete=<?php echo $r['id']; ?>" class="bg-red-500/10 text-red-500 font-black px-6 py-3 rounded-xl text-[10px] uppercase border border-red-500/20">Delete</a>
            </div>
        </div>
        <?php endwhile; ?>
    </div>
</main>

<?php require_once 'common/footer.php'; ?>