<nav class="fixed bottom-6 left-6 right-6 h-20 bg-[#141414]/90 backdrop-blur-xl rounded-[32px] border border-white/10 flex items-center justify-around px-4 z-[100] shadow-2xl md:hidden">
    <a href="index.php" class="flex flex-col items-center gap-1 <?php echo basename($_SERVER['PHP_SELF']) == 'index.php' ? 'text-[#00df81]' : 'text-gray-500'; ?>">
        <i class="fa-solid fa-house text-xl"></i>
        <span class="text-[8px] font-black uppercase tracking-widest">Home</span>
    </a>
    <a href="category.php" class="flex flex-col items-center gap-1 <?php echo basename($_SERVER['PHP_SELF']) == 'category.php' ? 'text-[#00df81]' : 'text-gray-500'; ?>">
        <i class="fa-solid fa-layer-group text-xl"></i>
        <span class="text-[8px] font-black uppercase tracking-widest">Drops</span>
    </a>
    <a href="track-order.php" class="flex flex-col items-center gap-1 <?php echo basename($_SERVER['PHP_SELF']) == 'track-order.php' ? 'text-[#00df81]' : 'text-gray-500'; ?>">
        <i class="fa-solid fa-truck-fast text-xl"></i>
        <span class="text-[8px] font-black uppercase tracking-widest">Status</span>
    </a>
    <a href="profile.php" class="flex flex-col items-center gap-1 <?php echo basename($_SERVER['PHP_SELF']) == 'profile.php' ? 'text-[#00df81]' : 'text-gray-500'; ?>">
        <i class="fa-solid fa-user-astronaut text-xl"></i>
        <span class="text-[8px] font-black uppercase tracking-widest">Vault</span>
    </a>
</nav>

<script>
    // Security: Disable Right Click
    document.addEventListener('contextmenu', event => event.preventDefault());
    // Security: Disable Ctrl+U
    document.onkeydown = function(e) {
        if (e.ctrlKey && (e.keyCode === 85 || e.keyCode === 117)) { return false; }
    };
</script>
<script src="assets/js/main.js"></script>
</body>
</html>
