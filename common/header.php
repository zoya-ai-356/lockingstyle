<?php 
require_once 'config.php'; 
require_once 'functions.php'; 
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>LOCKINGSTYLE | Premium Store</title>
    
    <!-- Tailwind CSS CDN (Isse design turant wapas aa jayega) -->
    <script src="https://cdn.tailwindcss.com"></script>
    
    <!-- Font Awesome Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;900&display=swap');
        body { 
            background-color: #0a0a0a !important; 
            color: white !important; 
            font-family: 'Inter', sans-serif; 
        }
        .neon-text { color: #00df81; text-shadow: 0 0 10px rgba(0, 223, 129, 0.5); }
    </style>
</head>
<body class="bg-[#0a0a0a] text-white pb-24">

<header class="p-6 flex justify-between items-center sticky top-0 bg-black/80 backdrop-blur-lg z-50 border-b border-white/5">
    <a href="index.php" class="text-2xl font-black italic tracking-tighter neon-text uppercase">LockingStyle</a>
    <div class="flex items-center gap-5">
        <a href="cart.php" class="relative text-white text-xl">
            <i class="fa-solid fa-bag-shopping"></i>
            <?php if(getCartCount() > 0): ?>
                <span class="absolute -top-2 -right-2 bg-[#00df81] text-black text-[9px] font-black w-4 h-4 rounded-full flex items-center justify-center"><?php echo getCartCount(); ?></span>
            <?php endif; ?>
        </a>
    </div>
</header>