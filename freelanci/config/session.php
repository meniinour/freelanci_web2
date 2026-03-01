<?php
require_once __DIR__ . '/database.php';
require_once __DIR__ . '/../classes/Auth.php';

Auth::startSession();

function isLoggedIn(): bool { return Auth::isLoggedIn(); }
function isAdmin(): bool { return Auth::isAdmin(); }
function isFreelance(): bool { return Auth::isFreelance(); }
function isClient(): bool { return Auth::isClient(); }
function requireLogin(): void { Auth::requireLogin('login.php'); }
function requireGuest(): void { Auth::requireGuest('index.php'); }
function requireAdmin(): void { Auth::requireAdmin('index.php'); }
function getCurrentUser(): ?array { return Auth::getCurrentUser(); }
