<?php 
require_once '../common/config.php';
require_once '../common/functions.php';
require_once '../common/telegram_notify.php';

if (isset($_POST['login_step_1'])) {
    $user = sanitize($_POST['username']);
    $pass = $_POST['password'];
    $res = $conn->query("SELECT * FROM admin WHERE username='$user'");
    if ($admin = $res->fetch_assoc()) {
        if (password_verify($pass, $admin['password'])) {
            $otp = rand(111111, 999999);
            $conn->query("UPDATE admin SET login_otp='$otp', otp_expiry=DATE_ADD(NOW(), INTERVAL 10 MINUTE) WHERE id='".$admin['id']."'");
            
            // Telegram bhejney ki koshish
            sendTelegramOTP($otp, "ADMIN_ACCESS");
            
            $_SESSION['temp_admin_id'] = $admin['id'];
            $_SESSION['backup_otp'] = $otp; // BACKUP: Screen par dikhane ke liye
            $show_otp = true;
        } else { $error = "Invalid Password"; }
    } else { $error = "Admin Not Found"; }
}

if (isset($_POST['verify_otp'])) {
    $otp = sanitize($_POST['otp']);
    $id = $_SESSION['temp_admin_id'];
    $res = $conn->query("SELECT * FROM admin WHERE id='$id' AND login_otp='$otp' AND otp_expiry > NOW()");
    if ($res->num_rows > 0) {
        $_SESSION['admin'] = true;
        $_SESSION['admin_id'] = $id;
        $conn->query("UPDATE admin SET login_otp=NULL WHERE id='$id'");
        unset($_SESSION['backup_otp']);
        header("Location: index.php"); exit;
    } else { $error = "Invalid OTP"; $show_otp = true; }
}
?>

<!DOCTYPE html>
<html lang="en">
<head><script src="https://cdn.tailwindcss.com"></script></head>
<body class="bg-[#0a0a0a] flex items-center justify-center min-h-screen text-white">
    <div class="w-full max-w-sm p-10 bg-[#111] rounded-[40px] border border-gray-800">
        <?php if(!isset($show_otp)): ?>
        <form method="POST" class="space-y-4">
            <h2 class="text-2xl font-black text-center mb-6 text-[#00df81]">Admin Login</h2>
            <input type="text" name="username" placeholder="Username" required class="w-full bg-black border border-gray-800 p-4 rounded-2xl outline-none">
            <input type="password" name="password" placeholder="Password" required class="w-full bg-black border border-gray-800 p-4 rounded-2xl outline-none">
            <button name="login_step_1" class="w-full bg-[#00df81] text-black font-black py-4 rounded-2xl uppercase text-xs">Get OTP</button>
        </form>
        <?php else: ?>
        <form method="POST" class="space-y-4 text-center">
            <h2 class="text-xl font-bold mb-4">Verify OTP</h2>
            <p class="text-gray-500 text-xs mb-4">Check Telegram or use backup below</p>
            
            <!-- BACKUP OTP BOX (Sirf tab dikhega jab tak hosting theek nahi hoti) -->
            <div class="p-3 bg-white/5 border border-dashed border-white/20 rounded-xl mb-4">
                <p class="text-[10px] text-gray-500 uppercase">Backup Code:</p>
                <p class="text-xl font-mono font-black text-[#00df81] tracking-widest"><?php echo $_SESSION['backup_otp']; ?></p>
            </div>

            <input type="text" name="otp" placeholder="000000" required class="w-full bg-black border border-gray-800 p-4 rounded-2xl text-center text-2xl font-bold outline-none">
            <button name="verify_otp" class="w-full bg-[#00df81] text-black font-black py-4 rounded-2xl uppercase text-xs">Verify & Login</button>
        </form>
        <?php endif; ?>
    </div>
</body>
</html>