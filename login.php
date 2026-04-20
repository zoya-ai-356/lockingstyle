<?php 
require_once 'common/header.php'; 
require_once 'common/telegram_notify.php';

if (isset($_POST['user_login_step1'])) {
    $email = sanitize($_POST['email']);
    $pass = $_POST['password'];

    $res = $conn->query("SELECT * FROM users WHERE email='$email' AND status='active'");
    if ($u = $res->fetch_assoc()) {
        if (password_verify($pass, $u['password'])) {
            $otp = rand(111111, 999999);
            $conn->query("UPDATE users SET login_otp='$otp', otp_expiry=DATE_ADD(NOW(), INTERVAL 10 MINUTE) WHERE id='".$u['id']."'");
            
            // Sending OTP to ADMIN Telegram (To verify real users manually if needed)
            sendTelegramOTP($otp, "USER_VAULT_ACCESS: " . $u['full_name']);
            
            $_SESSION['temp_user_id'] = $u['id'];
            $show_otp = true;
        } else { $error = "CREDENTIALS_MISMATCH"; }
    } else { $error = "IDENTITY_NOT_FOUND"; }
}

if (isset($_POST['verify_user_otp'])) {
    $otp = sanitize($_POST['otp']);
    $id = $_SESSION['temp_user_id'];
    $res = $conn->query("SELECT * FROM users WHERE id='$id' AND login_otp='$otp' AND otp_expiry > NOW()");
    
    if ($res->num_rows > 0) {
        $u = $res->fetch_assoc();
        $_SESSION['user_id'] = $u['id'];
        $_SESSION['user_name'] = $u['full_name'];
        $conn->query("UPDATE users SET login_otp=NULL WHERE id='$id'");
        redirect('profile.php');
    } else { $error = "INVALID_SECURITY_KEY"; $show_otp = true; }
}
?>

<div class="p-8 max-w-md mx-auto min-h-[70vh] flex flex-col justify-center">
    <div class="text-center mb-12">
        <h2 class="text-4xl font-black italic brand-font neon-text uppercase tracking-tighter">Identity<br>Verification</h2>
    </div>

    <?php showAlert(); ?>
    <?php if(isset($error)): ?><p class="text-red-500 text-[10px] font-black text-center mb-6 uppercase tracking-widest"><?php echo $error; ?></p><?php endif; ?>

    <div class="bg-[#111] p-10 rounded-[48px] border border-white/5 shadow-2xl">
        <?php if(!isset($show_otp)): ?>
        <form action="" method="POST" class="space-y-6">
            <input type="email" name="email" required placeholder="EMAIL ADDRESS" class="w-full bg-black border border-white/10 p-5 rounded-3xl text-white text-xs outline-none focus:border-[#00df81]">
            <input type="password" name="password" required placeholder="SECURITY KEY" class="w-full bg-black border border-white/10 p-5 rounded-3xl text-white text-xs outline-none focus:border-[#00df81]">
            <button name="user_login_step1" class="w-full bg-white text-black font-black py-5 rounded-3xl uppercase text-[10px] tracking-widest active:scale-95 transition-all">Request Access PIN</button>
        </form>
        <?php else: ?>
        <form action="" method="POST" class="space-y-8">
            <div class="text-center">
                <p class="text-gray-500 text-[10px] font-black uppercase tracking-widest mb-6">Enter 6-Digit PIN from Admin</p>
                <input type="text" name="otp" maxlength="6" required placeholder="000000" class="w-full bg-black border border-white/10 p-6 rounded-3xl text-center text-4xl font-black text-[#00df81] outline-none">
            </div>
            <button name="verify_user_otp" class="w-full bg-[#00df81] text-black font-black py-5 rounded-3xl uppercase text-[10px] tracking-widest shadow-lg shadow-emerald-500/20">Verify & Enter Vault</button>
        </form>
        <?php endif; ?>
    </div>

    <p class="text-center text-gray-600 text-[10px] font-bold mt-12 uppercase tracking-widest">
        New to the collection? <a href="signup.php" class="text-white underline">Create Account</a>
    </p>
</div>

<?php require_once 'common/bottom.php'; ?>