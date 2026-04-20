<?php 
require_once 'common/header.php'; 
require_once 'common/sidebar.php'; 
require_once 'common/auth_guard.php';

// Only Super Admin (ID 1) can edit roles
if ($_SESSION['admin_id'] != 1) redirect('index.php');

$role_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$res = mysqli_query($conn, "SELECT * FROM roles WHERE id = '$role_id'");
$role = mysqli_fetch_assoc($res);

if (!$role) redirect('staff.php');

$current_perms = json_decode($role['permissions'], true) ?? [];

// Human-readable modules mapping
$available_modules = [
    'pos' => 'Billing Terminal (POS)',
    'products' => 'Catalog & Artifacts',
    'inventory' => 'Global Inventory Node',
    'orders' => 'Logistics & Orders',
    'marketing' => 'Campaigns & Coupons',
    'reports' => 'Data Intelligence',
    'staff' => 'Personnel Management',
    'settings' => 'Core System Parameters'
];

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['update_role'])) {
    $role_name = sanitize($_POST['role_name']);
    $new_perms = isset($_POST['perms']) ? $_POST['perms'] : [];
    $perms_json = mysqli_real_escape_string($conn, json_encode($new_perms));

    $sql = "UPDATE roles SET role_name = '$role_name', permissions = '$perms_json' WHERE id = '$role_id'";
    
    if (mysqli_query($conn, $sql)) {
        logAudit('ROLE_UPDATE', "Modified permissions for role: $role_name");
        $_SESSION['success'] = "Role permissions synchronized.";
        redirect("edit-role.php?id=$role_id");
    }
}
?>

<main class="flex-1 p-6 lg:p-12">
    <div class="mb-12 flex items-center gap-6">
        <a href="staff.php" class="w-12 h-12 bg-card-bg border border-gray-800 rounded-2xl flex items-center justify-center text-gray-500 hover:text-white transition-all">
            <i class="fa-solid fa-arrow-left"></i>
        </a>
        <div>
            <h2 class="text-3xl font-black text-white italic uppercase tracking-tighter">Permission Matrix</h2>
            <p class="text-gray-500 text-sm mt-2 font-bold uppercase tracking-widest">Editing Role: <?php echo $role['role_name']; ?></p>
        </div>
    </div>

    <?php showAlert(); ?>

    <form action="" method="POST" class="max-w-4xl">
        <div class="bg-[#111] p-10 rounded-[48px] border border-white/5 shadow-2xl mb-10">
            <div class="mb-10">
                <label class="text-[10px] font-black text-gray-600 uppercase tracking-[0.4em] mb-4 block">Designation Label</label>
                <input type="text" name="role_name" value="<?php echo $role['role_name']; ?>" required 
                       class="w-full bg-black border border-white/10 p-6 rounded-3xl text-xl font-black text-white italic outline-none focus:border-primary transition-all">
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <?php foreach($available_modules as $key => $label): 
                    $is_checked = in_array($key, $current_perms);
                ?>
                <label class="flex items-center justify-between p-6 bg-black rounded-3xl border border-white/5 cursor-pointer hover:border-primary/40 transition-all group">
                    <div class="flex items-center gap-4">
                        <div class="w-8 h-8 rounded-lg bg-primary/10 flex items-center justify-center text-primary group-hover:bg-primary group-hover:text-black transition-all">
                            <i class="fa-solid fa-shield-halved text-xs"></i>
                        </div>
                        <span class="text-xs font-black text-gray-400 uppercase tracking-widest group-hover:text-white"><?php echo $label; ?></span>
                    </div>
                    <input type="checkbox" name="perms[]" value="<?php echo $key; ?>" <?php echo $is_checked ? 'checked' : ''; ?> 
                           class="w-6 h-6 accent-primary bg-transparent border-white/10">
                </label>
                <?php endforeach; ?>
            </div>
        </div>

        <button type="submit" name="update_role" class="w-full bg-white text-black font-black py-6 rounded-3xl uppercase text-xs tracking-[0.2em] shadow-xl hover:bg-primary transition-all">
            Deploy Access Policy
        </button>
    </form>
</main>

<?php require_once 'common/footer.php'; ?>