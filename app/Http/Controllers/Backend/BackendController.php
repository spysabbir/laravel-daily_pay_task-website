<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\PostTask;
use App\Models\Withdraw;
use App\Models\UserDetail;

class BackendController extends Controller
{
    public function dashboard()
    {
        $totalUsers = User::where('user_type', 'Frontend')->count();
        $activeUsers = User::where('user_type', 'Frontend')->where('status', 'Active')->count();

        $totalPostTask = PostTask::count();
        $runningPostTasks = PostTask::where('status', 'Running')->count();

        $totalDeposit = Withdraw::where('status', 'Approved')->sum('amount');
        $totalWithdraw = Withdraw::where('status', 'Approved')->sum('amount');

        $totalData = [
            'totalUsers' => $totalUsers,
            'totalPostTask' => $totalPostTask,
            'activeUsers' => $activeUsers,
            'runningPostTasks' => $runningPostTasks,
            'totalDeposit' => $totalDeposit,
            'totalWithdraw' => $totalWithdraw,
        ];

        return view('backend.dashboard' , compact('totalData'));
    }

    public function profileEdit(Request $request)
    {
        $user = $request->user();
        $userDetails = UserDetail::where('user_id', $user->id)->latest()->take(5)->get();
        return view('profile.edit', compact('user', 'userDetails'));
    }

    public function profileSetting(Request $request)
    {
        $user = $request->user();
        return view('profile.setting', compact('user'));
    }
}
