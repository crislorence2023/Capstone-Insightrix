<?php
class RBAC {
    private $roles = [
        'staff_coe_index' => [
            'permissions' => [
                'home',
                'evaluation-status',
                'Room',
                'subject_list',
                'class_list',
                'new_faculty',
                'faculty_list',
                'new_student',
                'student_list',
                'AssignClassSubjects'
            ]
        ],
        'staff_cme' => [
            'permissions' => [
                'home',
                'evaluation-status',
                // Add specific permissions for CME staff
            ]
        ],
        'staff_ceas' => [
            'permissions' => [
                'home',
                'evaluation-status',
                // Add specific permissions for CEAS staff
            ]
        ],
        'admin' => [
            'permissions' => [
                '*' // Wildcard for all permissions
            ]
        ]
    ];

    public function __construct() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
    }

    public function getCurrentUserRole() {
        return isset($_SESSION['user_role']) ? $_SESSION['user_role'] : null;
    }

    public function hasPermission($permission, $role = null) {
        if ($role === null) {
            $role = $this->getCurrentUserRole();
        }

        if (!$role || !isset($this->roles[$role])) {
            return false;
        }

        if (in_array('*', $this->roles[$role]['permissions'])) {
            return true;
        }

        return in_array($permission, $this->roles[$role]['permissions']);
    }

    public function getRolePermissions($role) {
        return isset($this->roles[$role]) ? $this->roles[$role]['permissions'] : [];
    }
}
