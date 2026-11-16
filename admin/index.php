<?php
require_once __DIR__ . '/../includes/functions.php';
redirect(is_admin() ? 'admin/dashboard.php' : 'admin/login.php');