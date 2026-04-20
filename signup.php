<?php 
require_once 'common/header.php'; 

if (isset($_POST['register_user'])) {
    $name = sanitize($_POST['full_name']);
    $email = sanitize($_POST['email']);
    $phone = sanitize($_POST['phone']);
    $pass = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $ref_by = sanitize($_POST['referral_by']); // Someone else's code
    $my_ref = strtoupper(substr(md5($email), 0, 8)); // Generate my code

    // Check if email exists
    $check = $conn->query("SELECT id FROM users WHERE email='$email'");
    if ($check->num_rows > 0) {
        $_SESSION['error'] = "EMAIL_ALREADY_REGISTERED";
    } else {
        $ref_user_id = 0;
        if(!empty($ref_by)) {
            $ref_res = $conn->query("SELECT id FROM users WHERE referral_code='$ref_by'");
            if($row = $ref_res->fetch_assoc()) $ref_user_id = $row['id'];
        }

        $sql = "INSERT INTO users (full_name, email, phone, password, referral_code, referred_by, status) 
                VALUES ('$name', '$email', '$phone', '$pass', '$my_ref', '$ref_user_id', 'active')";
        
        if($conn->query($sql)) {
            $_SESSION['success'] = "Account Created. Please Login.";
            redirect('login.php');
        }
    }
}
?>

<div class="p-8 max-w-md mx-auto">
    <div class="text-center mb-10 mt-10">
        <h2 class="text-4xl font-black italic brand-font neon-text uppercase">Join Vault</h2>
        <p class="text-gray-500 text-[10px] font-bold uppercase tracking-widest mt-2">Become a Style Ambassador</p>
    </div>

    <?php showAlert(); ?>

    <form action="" method="POST" class="space-y-5">
        <input type="text" name="full_name" required placeholder="FULL NAME" class="w-full bg-[#111] border border-white/5 p-5 rounded-3xl text-white text-xs outline-none focus:border-[#00df81]">
        <input type="email" name="email" required placeholder="EMAIL ADDRESS" class="w-full bg-[#111] border border-white/5 p-5 rounded-3xl text-white text-xs outline-none focus:border-[#00df81]">
        <input type="tel" name="phone" required placeholder="PHONE NUMBER" class="w-full bg-[#111] border border-white/5 p-5 rounded-3xl text-white text-xs outline-none focus:border-[#00df81]">
        <input type="password" name="password" required placeholder="CREATE PASSWORD" class="w-full bg-[#111] border border-white/5 p-5 rounded-3xl text-white text-xs outline-none focus:border-[#00df81]">
        <input type="text" name="referral_by" placeholder="REFERRAL CODE (OPTIONAL)" class="w-full bg-black border border-[#00df81]/20 p-5 rounded-3xl text-[#00df81] text-xs outline-none">
        
        <button name="register_user" class="w-full bg-[#00df81] text-black font-black py-5 rounded-3xl uppercase text-xs tracking-widest shadow-xl shadow-emerald-500/10">Initialize Identity</button>
    </form>
    
    <p class="text-center text-gray-600 text-[10px] font-bold mt-10 uppercase tracking-widest">
        Already have identity? <a href="login.php" class="text-white underline">Sign In</a>
    </p>
</div>

<?php require_once 'common/bottom.php'; ?>