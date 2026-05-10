<?php
// ============================================================
// inaffi.com — Logout
// ============================================================

require_once 'includes/config.php';
require_once 'includes/auth.php';
require_once 'includes/helpers.php';

logout(); // destroys session → redirects to /
