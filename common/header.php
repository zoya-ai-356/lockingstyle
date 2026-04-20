<?php require_once 'config.php'; require_once 'functions.php'; ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>LOCKINGSTYLE</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body { background-color: #0a0a0a; color: white; font-family: sans-serif; }
        .neon-glow { text-shadow: 0 0 10px #00df81; color: #00df81; }
        .no-scrollbar::-webkit-scrollbar { display: none; }
    </style>
</head>
<body class="pb-24">
<header class="p-5 flex justify-between items-center sticky top-0 bg-black/80 backdrop-blur-lg z-50 border-b border-white/5">
    <a href="index.php" class="text-2xl font-black italic tracking-tighter neon-glow uppercase">Lockingstyle</a>
    <a href="cart.php" class="relative text-white text-xl">
        <i class="fa-solid fa-bag-shopping"></i>
        <?php if(getCartCount() > 0): ?>
            <span class="absolute -top-2 -right-2 bg-white text-black text-[10px] font-black w-4 h-4 rounded-full flex items-center justify-center"><?php echo getCartCount(); ?></span>
        <?php endif; ?>
    </a>
</header>