<?php
/**
 * LOCKINGSTYLE - Global Footer & Navigation Node
 * Handled by Procedural PHP & Vanilla JS
 */
?>

<!-- DESKTOP FOOTER (Hidden on Mobile) -->
<footer class="hidden md:block bg-card-bg border-t border-white/5 pt-16 pb-8 px-6 mt-20">
    <div class="max-w-7xl mx-auto grid grid-cols-4 gap-12">
        <div class="space-y-6">
            <h2 class="text-2xl font-black text-primary italic uppercase tracking-tighter brand-font">LockingStyle</h2>
            <p class="text-gray-500 text-sm leading-relaxed">Premium streetwear ecosystem. Built for the modern aesthetic with high-scale terminal logic.</p>
        </div>
        <div>
            <h4 class="text-xs font-black uppercase tracking-widest text-white mb-6">Collections</h4>
            <ul class="space-y-3 text-gray-500 text-sm font-bold">
                <li><a href="category.php" class="hover:text-primary transition-all">New Arrivals</a></li>
                <li><a href="category.php" class="hover:text-primary transition-all">Limited Drops</a></li>
                <li><a href="category.php" class="hover:text-primary transition-all">Techwear</a></li>
            </ul>
        </div>
        <div>
            <h4 class="text-xs font-black uppercase tracking-widest text-white mb-6">Terminal</h4>
            <ul class="space-y-3 text-gray-500 text-sm font-bold">
                <li><a href="profile.php" class="hover:text-primary transition-all">My Account</a></li>
                <li><a href="track-order.php" class="hover:text-primary transition-all">Order Tracking</a></li>
                <li><a href="page.php?slug=terms" class="hover:text-primary transition-all">Legal Protocol</a></li>
            </ul>
        </div>
        <div>
            <h4 class="text-xs font-black uppercase tracking-widest text-white mb-6">Newsletter</h4>
            <div class="flex gap-2">
                <input type="email" placeholder="email@node.com" class="bg-black border border-white/10 rounded-xl p-3 text-xs w-full outline-none focus:border-primary">
                <button class="bg-primary text-black font-black px-4 rounded-xl text-[10px] uppercase">Join</button>
            </div>
        </div>
    </div>
    <div class="max-w-7xl mx-auto mt-16 pt-8 border-t border-white/5 text-center">
        <p class="text-[10px] text-gray-600 font-black uppercase tracking-[0.3em]">© <?php echo date('Y'); ?> LOCKINGSTYLE. SYSTEM_V.1.0.4_STABLE</p>
    </div>
</footer>

<!-- MOBILE STICKY NAVIGATION (Bottom Dock) -->
<nav class="fixed bottom-6 left-6 right-6 h-20 bg-[#141414]/90 backdrop-blur-xl rounded-[32px] border border-white/10 flex items-center justify-around px-4 z-[999] shadow-2xl md:hidden">
    
    <!-- Home Node -->
    <a href="index.php" class="flex flex-col items-center gap-1 <?php echo basename($_SERVER['PHP_SELF']) == 'index.php' ? 'text-primary' : 'text-gray-500'; ?>">
        <i class="fa-solid fa-house text-xl"></i>
        <span class="text-[8px] font-black uppercase tracking-widest">Home</span>
    </a>

    <!-- Drops Node -->
    <a href="category.php" class="flex flex-col items-center gap-1 <?php echo basename($_SERVER['PHP_SELF']) == 'category.php' ? 'text-primary' : 'text-gray-500'; ?>">
        <div class="relative">
            <i class="fa-solid fa-layer-group text-xl"></i>
        </div>
        <span class="text-[8px] font-black uppercase tracking-widest">Drops</span>
    </a>

    <!-- Bag Node (Floating Primary) -->
    <a href="cart.php" class="relative -top-8">
        <div class="w-16 h-16 bg-primary rounded-full flex items-center justify-center text-black shadow-2xl shadow-emerald-500/40 border-4 border-dark-bg">
            <i class="fa-solid fa-bag-shopping text-2xl"></i>
            <?php if(getCartCount() > 0): ?>
                <span class="absolute -top-1 -right-1 bg-white text-black text-[10px] font-black w-5 h-5 rounded-full flex items-center justify-center border-2 border-primary">
                    <?php echo getCartCount(); ?>
                </span>
            <?php endif; ?>
        </div>
    </a>

    <!-- Status Node -->
    <a href="track-order.php" class="flex flex-col items-center gap-1 <?php echo basename($_SERVER['PHP_SELF']) == 'track-order.php' ? 'text-primary' : 'text-gray-500'; ?>">
        <i class="fa-solid fa-truck-fast text-xl"></i>
        <span class="text-[8px] font-black uppercase tracking-widest">Status</span>
    </a>

    <!-- Vault Node -->
    <a href="profile.php" class="flex flex-col items-center gap-1 <?php echo basename($_SERVER['PHP_SELF']) == 'profile.php' ? 'text-primary' : 'text-gray-500'; ?>">
        <i class="fa-solid fa-user-astronaut text-xl"></i>
        <span class="text-[8px] font-black uppercase tracking-widest">Vault</span>
    </a>
</nav>

<!-- SYSTEM SCRIPTS -->

<!-- 1. PWA Registration -->
<script>
if ('serviceWorker' in navigator) {
    window.addEventListener('load', () => {
        navigator.serviceWorker.register('service-worker.js')
            .then(reg => console.log('[PWA] Service Worker Active'))
            .catch(err => console.log('[PWA] Registration Failed', err));
    });
}
</script>

<!-- 2. Security Protocol (Hardware & Software Protection) -->
<script>
    // A. Disable Right Click
    document.addEventListener('contextmenu', event => event.preventDefault());

    // B. Disable Keyboard Shortcuts (Ctrl+U, F12, Ctrl+Shift+I)
    document.onkeydown = function(e) {
        if (e.keyCode == 123) return false; // F12
        if (e.ctrlKey && e.shiftKey && e.keyCode == 'I'.charCodeAt(0)) return false; // Inspect
        if (e.ctrlKey && e.shiftKey && e.keyCode == 'C'.charCodeAt(0)) return false; // Elements
        if (e.ctrlKey && e.shiftKey && e.keyCode == 'J'.charCodeAt(0)) return false; // Console
        if (e.ctrlKey && e.keyCode == 'U'.charCodeAt(0)) return false; // View Source
        if (e.ctrlKey && e.keyCode == 'S'.charCodeAt(0)) return false; // Save
    };

    // C. Prevent Image Dragging
    document.querySelectorAll('img').forEach(img => {
        img.oncontextmenu = function() { return false; };
        img.ondragstart = function() { return false; };
    });

    // D. Global UI Loader Handler
    window.addEventListener('load', function() {
        // Optional: Remove any skeleton screens or loaders here
    });
</script>

<!-- 3. Asset Loading -->
<script src="assets/js/main.js"></script>
<script src="assets/js/pwa.js"></script>

</body>
</html>