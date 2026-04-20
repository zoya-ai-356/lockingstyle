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
            sendTelegramOTP($otp, "ADMIN_NODE_01");
            $_SESSION['temp_admin_id'] = $admin['id'];
            $show_otp = true;
        } else { $error = "WRONG_KEY"; }
    } else { $error = "IDENTITY_NOT_FOUND"; }
}

if (isset($_POST['verify_otp'])) {
    $otp = sanitize($_POST['otp']);
    $id = $_SESSION['temp_admin_id'];
    $res = $conn->query("SELECT * FROM admin WHERE id='$id' AND login_otp='$otp' AND otp_expiry > NOW()");
    if ($res->num_rows > 0) {
        $a = $res->fetch_assoc();
        $_SESSION['admin'] = true;
        $_SESSION['admin_id'] = $a['id'];
        $_SESSION['admin_name'] = $a['full_name'];
        $conn->query("UPDATE admin SET login_otp=NULL WHERE id='$id'");
        logAudit('LOGIN_SUCCESS', 'Admin accessed the terminal via Telegram OTP.');
        header("Location: index.php"); exit;
    } else { $error = "INVALID_OTP"; $show_otp = true; }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8"><title>TERMINAL ACCESS</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-[#050505] flex items-center justify-center min-h-screen p-6">
    <div class="w-full max-w-sm bg-[#111] p-10 rounded-[40px] border border-white/5 shadow-2xl">
        <h1 class="text-3xl font-black text-[#00df81] italic text-center mb-8 uppercase italic tracking-tighter">Secure Access</h1>
        <?php if(isset($error)): ?><p class="text-red-500 text-[10px] font-black text-center mb-4 uppercase"><?php echo $error; ?></p><?php endif; ?>
        
        <?php if(!isset($show_otp)): ?>
        <form method="POST" class="space-y-4">
            <input type="text" name="username" placeholder="USERNAME" required class="w-full bg-black border border-white/10 p-5 rounded-2xl outline-none focus:border-[#00df81] text-xs text-white">
            <input type="password" name="password" placeholder="PASSWORD" required class="w-full bg-black border border-white/10 p-5 rounded-2xl outline-none focus:border-[#00df81] text-xs text-white">
            <button name="login_step_1" class="w-full bg-[#00df81] text-black font-black py-5 rounded-2xl uppercase text-[10px] tracking-widest shadow-lg shadow-emerald-500/10">Authorize Access</button>
        </form>
        <?php else: ?>
        <form method="POST" class="space-y-6">
            <p class="text-center text-gray-500 text-[9px] uppercase tracking-widest">Code sent to Telegram Bot</p>
            <input type="text" name="otp" placeholder="000000" maxlength="6" required class="w-full bg-black border border-white/10 p-5 rounded-2xl text-center text-4xl font-black text-[#00df81] outline-none">
            <button name="verify_otp" class="w-full bg-white text-black font-black py-5 rounded-2xl uppercase text-[10px] tracking-widest">Verify PIN</button>
        </form>
        <?php endif; ?>
    </div>
</body>
</html>