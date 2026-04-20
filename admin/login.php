<?php 
require_once '../common/config.php';
require_once '../common/functions.php';

if (isset($_POST['login_step_1'])) {
    $user = sanitize($_POST['username']);
    $pass = $_POST['password'];

    $res = $conn->query("SELECT * FROM admin WHERE username='$user'");
    if ($res && $admin = $res->fetch_assoc()) {
        if (password_verify($pass, $admin['password'])) {
            $otp = rand(111111, 999999);
            $expiry = date("Y-m-d H:i:s", strtotime("+10 minutes"));
            $conn->query("UPDATE admin SET login_otp='$otp', otp_expiry='$expiry' WHERE id='".$admin['id']."'");
            
            $_SESSION['temp_admin_id'] = $admin['id'];
            $show_otp = true;
        } else { $error = "WRONG_KEY"; }
    } else { $error = "NODE_NOT_FOUND"; }
}

if (isset($_POST['verify_otp'])) {
    $otp = sanitize($_POST['otp']);
    $id = $_SESSION['temp_admin_id'];
    $res = $conn->query("SELECT * FROM admin WHERE id='$id' AND login_otp='$otp' AND otp_expiry > NOW()");
    if ($res && $res->num_rows > 0) {
        $_SESSION['admin'] = true;
        $_SESSION['admin_id'] = $id;
        $conn->query("UPDATE admin SET login_otp=NULL WHERE id='$id'");
        header("Location: index.php"); exit;
    } else { $error = "INVALID_PIN"; $show_otp = true; }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Terminal Access</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-[#050505] flex items-center justify-center min-h-screen text-white font-mono">
    <div class="w-full max-w-sm p-10 bg-[#111] rounded-[40px] border border-white/5 shadow-2xl">
        <h2 class="text-2xl font-black text-[#00df81] text-center mb-8 uppercase italic tracking-tighter">Admin Login</h2>
        
        <?php if(isset($error)) echo "<p class='text-red-500 text-[10px] font-black text-center mb-6 uppercase tracking-widest'>$error</p>"; ?>

        <?php if(!isset($show_otp)): ?>
        <form method="POST" class="space-y-4">
            <input type="text" name="username" placeholder="IDENTITY" required class="w-full bg-black border border-white/10 p-5 rounded-2xl text-xs outline-none focus:border-[#00df81]">
            <input type="password" name="password" placeholder="SECURITY_KEY" required class="w-full bg-black border border-white/10 p-5 rounded-2xl text-xs outline-none focus:border-[#00df81]">
            <button name="login_step_1" class="w-full bg-[#00df81] text-black font-black py-5 rounded-2xl uppercase text-[10px] tracking-widest">Get Access PIN</button>
        </form>
        <?php else: ?>
        <form method="POST" class="space-y-8 text-center">
            <p class="text-gray-600 text-[9px] uppercase tracking-[0.3em]">Identity Challenge Active</p>
            <input type="text" name="otp" placeholder="000000" maxlength="6" required autofocus class="w-full bg-black border border-white/10 p-5 rounded-2xl text-center text-4xl font-black text-[#00df81] outline-none">
            <button name="verify_otp" class="w-full bg-white text-black font-black py-5 rounded-2xl uppercase text-[10px]">Authorize Entry</button>
            <a href="../view-system-key.php" target="_blank" class="text-[#00df81] text-[9px] font-bold uppercase underline block mt-4 tracking-widest animate-pulse">View PIN in System</a>
        </form>
        <?php endif; ?>
    </div>
</body>
</html>