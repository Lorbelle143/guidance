<?php
// Redirected to unified login process
require_once __DIR__.'/../config/config.php';
require_once __DIR__.'/../includes/session.php';
require_once __DIR__.'/../includes/functions.php';
redirect('login.php?tab=admin');
