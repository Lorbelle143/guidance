<?php
// Redirected to unified login page (admin tab)
require_once __DIR__.'/../config/config.php';
require_once __DIR__.'/../includes/session.php';
require_once __DIR__.'/../includes/functions.php';
redirect('login.php?tab=admin');
