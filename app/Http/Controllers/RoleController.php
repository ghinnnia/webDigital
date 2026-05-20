<?php
// app/Http/Controllers/RoleController.php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class RoleController extends Controller
{
    /**
     * Get list of all unique roles from users table
     */
    public function list(Request $request)
    {
        try {
            // Get unique roles from users table
            $roles = User::select('role')
                ->whereNotNull('role')
                ->distinct()
                ->get()
                ->map(function($item) {
                    return [
                        'id' => $item->role,
                        'role' => ucfirst($item->role)
                    ];
                });

            // Add default roles if no data found
            if ($roles->isEmpty()) {
                $defaultRoles = [
                    ['id' => 'general_manager', 'role' => 'General Manager'],
                    ['id' => 'manager_divisi', 'role' => 'Manager Divisi'],
                    ['id' => 'karyawan', 'role' => 'Karyawan'],
                    ['id' => 'finance', 'role' => 'Finance'],
                    ['id' => 'hr', 'role' => 'HR'],
                    ['id' => 'admin', 'role' => 'Admin'],
                    ['id' => 'owner', 'role' => 'Owner'],
                ];
                return response()->json($defaultRoles);
            }

            return response()->json($roles);
            
        } catch (\Exception $e) {
            // Return default roles on error
            return response()->json([
                ['id' => 'general_manager', 'role' => 'General Manager'],
                ['id' => 'manager_divisi', 'role' => 'Manager Divisi'],
                ['id' => 'karyawan', 'role' => 'Karyawan'],
                ['id' => 'finance', 'role' => 'Finance'],
                ['id' => 'hr', 'role' => 'HR'],
                ['id' => 'admin', 'role' => 'Admin'],
                ['id' => 'owner', 'role' => 'Owner'],
            ]);
        }
    }

    /**
     * Get all roles with their permissions (if needed)
     */
    public function getAllRoles()
    {
        $roles = [
            ['id' => 'general_manager', 'name' => 'General Manager', 'permissions' => ['all']],
            ['id' => 'manager_divisi', 'name' => 'Manager Divisi', 'permissions' => ['manage_team', 'view_reports']],
            ['id' => 'karyawan', 'name' => 'Karyawan', 'permissions' => ['self_attendance', 'self_tasks']],
            ['id' => 'finance', 'name' => 'Finance', 'permissions' => ['manage_salaries', 'view_financial']],
            ['id' => 'hr', 'name' => 'HR', 'permissions' => ['manage_employees', 'manage_attendance']],
            ['id' => 'admin', 'name' => 'Admin', 'permissions' => ['all']],
            ['id' => 'owner', 'name' => 'Owner', 'permissions' => ['all']],
        ];
        
        return response()->json($roles);
    }
}