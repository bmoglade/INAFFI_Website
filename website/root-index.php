<?php
// ============================================================
// inaffi.com — Root entry point
// ============================================================
// This file belongs at public_html/index.php on Hostinger.
// It forwards all traffic to the actual app in website/inaffi/
//
// DEPLOY INSTRUCTIONS:
//   Upload THIS file as: public_html/index.php
// ============================================================
header('Location: /website/inaffi/', true, 301);
exit();
