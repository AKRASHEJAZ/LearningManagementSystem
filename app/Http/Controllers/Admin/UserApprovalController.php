<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\AchievementService;
use Illuminate\Http\Request;

class UserApprovalController extends Controller
{
    public function __construct(private readonly AchievementService $achievementService)
    {
    }

    public function index(Request $request)
    {
        $status = $request->query('status', 'pending');
        if (! is_string($status) || ! in_array($status, ['pending', 'rejected', 'approved'], true)) {
            $status = 'pending';
        }

        $users = User::query()
            ->where('is_admin', false)
            ->where('approval_status', $status)
            ->orderBy('id', 'desc')
            ->paginate(20)
            ->withQueryString();

        return view('admin.users.approvals.index', [
            'users' => $users,
            'status' => $status,
        ]);
    }

    public function show(User $user)
    {
        abort_if($user->is_admin, 404);

        return view('admin.users.approvals.show', [
            'user' => $user,
        ]);
    }

    public function approve(Request $request, User $user)
    {
        abort_if($user->is_admin, 404);

        $user->forceFill([
            'approval_status' => 'approved',
            'approved_at' => now(),
            'approved_by' => $request->user()->id,
            'rejected_at' => null,
            'rejected_by' => null,
            'rejection_reason' => null,
        ])->save();

        $this->achievementService->onUserApproved($user);

        return redirect()
            ->route('admin.user-approvals.show', $user)
            ->with('status', 'User approved.');
    }

    public function reject(Request $request, User $user)
    {
        abort_if($user->is_admin, 404);

        $validated = $request->validate([
            'rejection_reason' => ['nullable', 'string', 'max:1000'],
        ]);

        $user->forceFill([
            'approval_status' => 'rejected',
            'rejected_at' => now(),
            'rejected_by' => $request->user()->id,
            'rejection_reason' => $validated['rejection_reason'] ?? null,
            'approved_at' => null,
            'approved_by' => null,
        ])->save();

        return redirect()
            ->route('admin.user-approvals.show', $user)
            ->with('status', 'User rejected.');
    }
}
