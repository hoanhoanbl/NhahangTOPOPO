<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!class_exists('AuthController')) {
    require_once dirname(__DIR__, 3) . '/controllers/AuthController.php';
}

if (!function_exists('adminAuth')) {
    function adminAuth(): AuthController
    {
        static $auth = null;
        if ($auth === null) {
            $auth = new AuthController();
        }
        return $auth;
    }
}

if (!function_exists('adminScopedBranchId')) {
    function adminScopedBranchId(int $requested = 0): int
    {
        return adminAuth()->resolveScopedBranchId($requested);
    }
}

if (!function_exists('adminCanAccessBranch')) {
    function adminCanAccessBranch(int $branchId): bool
    {
        return adminAuth()->canAccessBranch($branchId);
    }
}

if (!function_exists('adminDenyAndRedirect')) {
    function adminDenyAndRedirect(string $redirectUrl, string $message = 'Ban khong co quyen truy cap du lieu cua co so khac.'): void
    {
        adminAuth()->denyBranchAccess($message);
        echo "<script>window.location.href='" . htmlspecialchars($redirectUrl, ENT_QUOTES) . "';</script>";
        exit();
    }
}

if (!function_exists('adminCurrentRole')) {
    function adminCurrentRole(): string
    {
        return adminAuth()->getCurrentRole();
    }
}

if (!function_exists('adminIsGlobalAdmin')) {
    function adminIsGlobalAdmin(): bool
    {
        return adminAuth()->isAdmin();
    }
}

