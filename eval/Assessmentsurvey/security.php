<?php
class SecurityHelper {
    private static $allowedPaths = [
        'staff_coe_index',
        'staff_ceas_index',
        'staff_cme_index',
        'staffindex',
        'superadmin',
        'assets',
        'includes'
    ];

    private static $allowedPages = [
        'home',
        'evaluation',
        'faculty',
        'student',
        'user',
        'academic',
        'report',
        // Add other allowed pages here
    ];

    public static function validatePath($path) {
        // Remove any directory traversal attempts
        $path = str_replace(['../', '..\\', '..'], '', $path);
        
        // Get the first directory in path
        $firstDir = strtok($path, '/\\');
        
        return in_array($firstDir, self::$allowedPaths);
    }

    public static function validatePage($page) {
        // Sanitize the page parameter
        $page = str_replace(['../', '..\\', '..'], '', $page);
        $page = strtolower(trim($page));
        
        return in_array($page, self::$allowedPages);
    }

    public static function secureInclude($path) {
        if (self::validatePath($path)) {
            $safePath = realpath(__DIR__ . '/../' . $path);
            if ($safePath && strpos($safePath, realpath(__DIR__ . '/../')) === 0) {
                return $safePath;
            }
        }
        return false;
    }
}