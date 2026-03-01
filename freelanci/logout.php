<?php
require_once __DIR__ . '/config/session.php';
Auth::logout();
header('Location: index.php');
exit;
