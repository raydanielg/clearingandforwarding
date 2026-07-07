<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Setting;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class SettingsController extends Controller
{
    // ── Users ──
    public function usersIndex(Request $request)
    {
        $query = User::with('roles', 'client');

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        $users = $query->latest()->paginate(15);
        $roles = Role::orderBy('name')->get();

        if ($request->ajax()) {
            return response()->json([
                'html' => view('admin.settings.users.partials.table', compact('users'))->render(),
            ]);
        }

        return view('admin.settings.users.index', compact('users', 'roles'));
    }

    public function usersStore(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email',
            'phone' => 'nullable|string|max:20',
            'password' => 'required|string|min:8',
            'role' => 'required|exists:roles,name',
            'client_id' => 'nullable|exists:clients,id',
            'is_active' => 'boolean',
        ]);

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'phone' => $validated['phone'] ?? null,
            'password' => Hash::make($validated['password']),
            'client_id' => $validated['client_id'] ?? null,
            'is_active' => $validated['is_active'] ?? true,
        ]);

        $user->assignRole($validated['role']);

        if ($request->ajax()) {
            return response()->json(['success' => true, 'message' => 'User created', 'user' => $user]);
        }

        return back()->with('status', 'User created');
    }

    public function usersUpdate(Request $request, User $user)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email,' . $user->id,
            'phone' => 'nullable|string|max:20',
            'password' => 'nullable|string|min:8',
            'role' => 'required|exists:roles,name',
            'client_id' => 'nullable|exists:clients,id',
            'is_active' => 'boolean',
        ]);

        $user->update([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'phone' => $validated['phone'] ?? null,
            'client_id' => $validated['client_id'] ?? null,
            'is_active' => $validated['is_active'] ?? true,
        ]);

        if (!empty($validated['password'])) {
            $user->update(['password' => Hash::make($validated['password'])]);
        }

        $user->syncRoles([$validated['role']]);

        if ($request->ajax()) {
            return response()->json(['success' => true, 'message' => 'User updated']);
        }

        return back()->with('status', 'User updated');
    }

    public function usersDestroy(Request $request, User $user)
    {
        if ($user->id === auth()->id()) {
            if ($request->ajax()) {
                return response()->json(['success' => false, 'message' => 'Cannot delete yourself'], 400);
            }
            return back()->with('error', 'Cannot delete yourself');
        }

        $user->delete();

        if ($request->ajax()) {
            return response()->json(['success' => true, 'message' => 'User deleted']);
        }

        return back()->with('status', 'User deleted');
    }

    // ── Roles ──
    public function rolesIndex(Request $request)
    {
        $roles = Role::with('permissions')->orderBy('name')->get();
        $permissions = Permission::orderBy('name')->get();
        $selectedRole = $request->filled('role') ? Role::with('permissions')->find($request->role) : null;
        return view('admin.settings.roles.index', compact('roles', 'permissions', 'selectedRole'));
    }

    public function rolesStore(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:roles,name',
            'permissions' => 'nullable|array',
            'permissions.*' => 'exists:permissions,name',
        ]);

        $role = Role::create(['name' => $validated['name']]);
        if (!empty($validated['permissions'])) {
            $role->syncPermissions($validated['permissions']);
        }

        if ($request->ajax()) {
            return response()->json(['success' => true, 'message' => 'Role created', 'role' => $role]);
        }

        return back()->with('status', 'Role created');
    }

    public function rolesUpdate(Request $request, Role $role)
    {
        $validated = $request->validate([
            'permissions' => 'nullable|array',
            'permissions.*' => 'exists:permissions,name',
        ]);

        $role->syncPermissions($validated['permissions'] ?? []);

        if ($request->ajax()) {
            return response()->json(['success' => true, 'message' => 'Role permissions updated']);
        }

        return back()->with('status', 'Role permissions updated');
    }

    public function rolesDestroy(Request $request, Role $role)
    {
        if (in_array($role->name, ['Super Admin'])) {
            if ($request->ajax()) {
                return response()->json(['success' => false, 'message' => 'Cannot delete Super Admin role'], 400);
            }
            return back()->with('error', 'Cannot delete Super Admin role');
        }

        $role->delete();

        if ($request->ajax()) {
            return response()->json(['success' => true, 'message' => 'Role deleted']);
        }

        return back()->with('status', 'Role deleted');
    }

    // ── Company Settings ──
    public function companyIndex()
    {
        $settings = Setting::all()->pluck('value', 'key');
        return view('admin.settings.company', compact('settings'));
    }

    public function companyUpdate(Request $request)
    {
        $validated = $request->validate([
            'company_name' => 'required|string|max:255',
            'company_tin' => 'nullable|string|max:50',
            'vat_rate' => 'nullable|numeric|min:0|max:100',
            'currency' => 'nullable|string|size:3',
            'petty_cash_limit' => 'nullable|numeric|min:0',
            'free_days_default' => 'nullable|integer|min:0',
            'sms_sender_id' => 'nullable|string|max:20',
        ]);

        foreach ($validated as $key => $value) {
            Setting::updateOrCreate(['key' => $key], ['value' => $value]);
        }

        if ($request->ajax()) {
            return response()->json(['success' => true, 'message' => 'Settings updated']);
        }

        return back()->with('status', 'Settings updated');
    }
}
