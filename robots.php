<?php
/**
 * LOCKINGSTYLE - Dynamic Robots Architect
 */
header("Content-Type: text/plain");
require_once 'common/config.php';

echo "User-agent: *\n";
echo "Disallow: /admin/\n";
echo "Disallow: /common/\n";
echo "Disallow: /assets/js/\n";
echo "Disallow: /cart.php\n";
echo "Disallow: /checkout.php\n";
echo "Disallow: /profile.php\n";
echo "Disallow: /verify-account.php\n";

// Dynamic Sitemap link
echo "\nSitemap: " . SITE_URL . "/sitemap.php\n";

// Crawl-delay for high scale protection
echo "Crawl-delay: 10\n";
?>