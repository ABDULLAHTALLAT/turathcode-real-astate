<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $users = User::where('role', '!=', 'admin')
            ->when($request->query('role'), fn ($q, $r) => $q->where('role', $r))
            ->when($request->query('q'), fn ($q, $v) => $q->where(fn ($w) => $w->where('name', 'like', "%$v%")
                ->orWhere('email', 'like', "%$v%")->orWhere('phone', 'like', "%$v%")))
            ->withCount(['properties', 'sentRequests'])
            ->latest()->paginate(15)->withQueryString();

        return view('admin.users', compact('users'));
    }

    public function toggle(User $user)
    {
        abort_if($user->isAdmin(), 403);
        $user->update(['is_active' => ! $user->is_active]);

        return back()->with('success', $user->is_active ? 'تم تفعيل الحساب.' : 'تم إيقاف الحساب.');
    }
}
