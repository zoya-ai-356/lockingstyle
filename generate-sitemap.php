<?php
/**
 * LOCKINGSTYLE - Automated SEO Pulse
 * Run this via Cron or manual Trigger to update sitemap.xml
 */
require_once 'common/config.php';

$sitemap = '<?xml version="1.0" encoding="UTF-8"?>';
$sitemap .= '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">';

// Base URLs
$sitemap .= '<url><loc>'.SITE_URL.'/</loc><priority>1.0</priority></url>';
$sitemap .= '<url><loc>'.SITE_URL.'/new-styles.php</loc><priority>0.9</priority></url>';
$sitemap .= '<url><loc>'.SITE_URL.'/category.php</loc><priority>0.8</priority></url>';

// Dynamic Product URLs
$res_p = mysqli_query($conn, "SELECT id, created_at FROM products WHERE status='published'");
while($p = mysqli_fetch_assoc($res_p)) {
    $sitemap .= '<url>';
    $sitemap .= '<loc>'.SITE_URL.'/product-details.php?id='.$p['id'].'</loc>';
    $sitemap .= '<lastmod>'.date('Y-m-d', strtotime($p['created_at'])).'</lastmod>';
    $sitemap .= '<changefreq>daily</changefreq>';
    $sitemap .= '<priority>0.9</priority>';
    $sitemap .= '</url>';
}

// Dynamic Category URLs
$res_c = mysqli_query($conn, "SELECT id FROM categories WHERE status='active'");
while($c = mysqli_fetch_assoc($res_c)) {
    $sitemap .= '<url>';
    $sitemap .= '<loc>'.SITE_URL.'/category.php?id='.$c['id'].'</loc>';
    $sitemap .= '<priority>0.7</priority>';
    $sitemap .= '</url>';
}

$sitemap .= '</urlset>';

// Write to root
if (file_put_contents('sitemap.xml', $sitemap)) {
    echo "SEO_PULSE: sitemap.xml synchronized successfully.";
} else {
    echo "SEO_ERROR: Permission denied on root directory.";
}
?>