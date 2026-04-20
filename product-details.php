<!-- STAR RATING SUMMARY -->
<div class="mt-20 border-t border-white/5 pt-16">
    <div class="flex justify-between items-end mb-12 px-2">
        <div>
            <h3 class="text-2xl font-black text-white italic uppercase tracking-tighter"><?php _e('prod_reviews'); ?></h3>
            <div class="flex items-center gap-4 mt-2">
                <div class="flex text-primary text-xs">
                    <i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i><i class="fa-solid fa-star-half-stroke"></i>
                </div>
                <span class="text-[10px] font-black text-gray-500 uppercase tracking-widest">4.8 Average across nodes</span>
            </div>
        </div>
        <button onclick="document.getElementById('review-form').classList.toggle('hidden')" class="bg-white text-black font-black px-6 py-2 rounded-xl text-[9px] uppercase tracking-widest">Post Feedback</button>
    </div>

    <!-- Review Form (Hidden by Default) -->
    <div id="review-form" class="hidden bg-[#111] p-8 rounded-[40px] border border-white/5 mb-12">
        <form action="submit-review.php" method="POST" class="space-y-6">
            <input type="hidden" name="product_id" value="<?php echo $p['id']; ?>">
            <div>
                <label class="text-[10px] font-black text-gray-600 uppercase tracking-widest mb-3 block">Star Rating</label>
                <select name="rating" class="w-full bg-black border border-white/10 p-4 rounded-xl text-primary font-bold">
                    <option value="5">★★★★★ - Perfect</option>
                    <option value="4">★★★★☆ - Great</option>
                    <option value="3">★★★☆☆ - Average</option>
                    <option value="2">★★☆☆☆ - Subpar</option>
                    <option value="1">★☆☆☆☆ - Reject</option>
                </select>
            </div>
            <div>
                <label class="text-[10px] font-black text-gray-600 uppercase tracking-widest mb-3 block">Your Intelligence (Review)</label>
                <textarea name="comment" rows="4" required class="w-full bg-black border border-white/10 p-5 rounded-2xl text-white text-xs outline-none focus:border-primary"></textarea>
            </div>
            <button type="submit" name="submit_feedback" class="w-full bg-primary text-black font-black py-4 rounded-2xl text-[10px] uppercase">Submit to Terminal</button>
        </form>
    </div>

    <!-- Feedback List -->
    <div class="space-y-6">
        <?php 
        $revs = $conn->query("SELECT r.*, u.full_name FROM reviews r JOIN users u ON r.user_id = u.id WHERE r.product_id = '$id' AND r.status = 'approved' ORDER BY r.id DESC");
        while($r = $revs->fetch_assoc()):
        ?>
        <div class="bg-[#111] p-6 rounded-[32px] border border-white/5">
            <div class="flex justify-between items-start mb-4">
                <div class="flex items-center gap-4">
                    <div class="w-10 h-10 rounded-xl bg-black border border-white/10 flex items-center justify-center text-primary font-black uppercase italic"><?php echo substr($r['full_name'],0,1); ?></div>
                    <div>
                        <h4 class="text-xs font-black text-white uppercase"><?php echo $r['full_name']; ?></h4>
                        <p class="text-[8px] text-gray-600 font-bold uppercase mt-0.5">Verified Inhabitant</p>
                    </div>
                </div>
                <div class="flex text-primary text-[8px]">
                    <?php for($i=0; $i<$r['rating']; $i++) echo '<i class="fa-solid fa-star"></i>'; ?>
                </div>
            </div>
            <p class="text-sm text-gray-400 leading-relaxed italic">"<?php echo $r['comment']; ?>"</p>
        </div>
        <?php endwhile; ?>
    </div>
</div>