<?php
require_once __DIR__ . '/auth/bootstrap.php';
auth_logout();
header('Location: ' . app_url('website/admin/auth/login.php'));
exit;
