<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\AdminToggleRequest;
use App\Models\User;

class UserManagementController extends Controller
{
    public function index()
    {
        $users = User::query()
            ->orderByDesc('id')
            ->paginate(25);

        return view('admin.users.index', [
            'users' => $users,
        ]);
    }

    public function updateAdmin(AdminToggleRequest $request, User $user)
    {
        $makeAdmin = (bool) $request->validated('make_admin');

        if ($user->id === $request->user()->id) {
            return back()->with('status', 'You cannot change your own admin status.');
        }

        if (! $makeAdmin && $user->isAdmin()) {
            $adminCount = User::query()->where('is_admin', true)->count();
            if ($adminCount <= 1) {
                return back()->with('status', 'Cannot remove the last admin.');
            }
        }

        $user->is_admin = $makeAdmin;

        if ($makeAdmin) {
            // Ensure promoted admins are not stuck in pending state.
            if (! $user->isApproved()) {
                $user->approval_status = 'approved';
                $user->approved_at = now();
                $user->approved_by = $request->user()->id;
                $user->rejected_at = null;
                $user->rejected_by = null;
                $user->rejection_reason = null;
            }
        }

        $user->save();

        return back()->with('status', $makeAdmin ? 'User promoted to admin.' : 'User demoted from admin.');
    }
}
