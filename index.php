<?php

/**
 * Root index.php — redirects all traffic to the public/ directory.
 * This allows the app to work on shared hosting where the document root
 * cannot be set to /public.
 */

$publicPath = __DIR__ . '/public';

// If the public directory doesn't exist, show a friendly error.
if (!is_dir($publicPath)) {
    http_response_code(500);
    echo '<!DOCTYPE html><html><head><meta charset="utf-8"><title>Setup Required</title>'
       . '<style>body{font-family:system-ui,sans-serif;background:#001816;color:#e6f5f1;display:flex;align-items:center;justify-content:center;min-height:100vh;margin:0}'
       . '.box{max-width:500px;padding:40px;border-radius:16px;background:#01241f;border:1px solid #013028;text-align:center}'
       . 'h1{color:#f9ac00;margin:0 0 10px}p{line-height:1.6;opacity:0.8}code{background:#013028;padding:2px 8px;border-radius:4px;color:#4db5a8}</style></head>'
       . '<body><div class="box"><h1>Setup Required</h1>'
       . '<p>The <code>public/</code> directory was not found.<br>Please ensure the application is fully deployed.</p>'
       . '</div></body></html>';
    exit;
}

// Serve the request through the public/index.php
require $publicPath . '/index.php';
