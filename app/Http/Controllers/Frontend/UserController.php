<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Block;
use App\Models\Bonus;
use App\Models\Deposit;
use App\Models\User;
use App\Models\Verification;
use App\Models\Withdraw;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\Validator;
use Stevebauman\Location\Facades\Location;
use App\Models\Report;
use App\Models\ReportReply;
use App\Models\Support;
use App\Events\SupportEvent;

class UserController extends Controller
{
    public function dashboard(Request $request)
    {
        // $user = $request->user();
        // return $myIp = $request->ip();
        // return $position = Location::get('103.4.119.20');
        return view('frontend/dashboard');
    }

    // Profile.............................................................................................................

    public function profileEdit(Request $request)
    {
        $user = $request->user();
        return view('profile.edit', compact('user'));
    }

    public function profileSetting(Request $request)
    {
        $user = $request->user();
        return view('profile.setting', compact('user'));
    }

    public function userProfile($id)
    {
        $user = User::findOrFail(decrypt($id));
        $blocked = Block::where('user_id', $user->id)->where('blocked_by', Auth::id())->exists();
        return view('frontend.user_profile.index', compact('user', 'blocked'));
    }

    // Verification.............................................................................................................

    public function verification(Request $request)
    {
        $verification = Verification::where('user_id', $request->user()->id)->first();
        $user = $request->user();
        return view('frontend.verification.index', compact('user', 'verification'));
    }

    public function verificationStore(Request $request)
    {
        $request->validate([
            'id_type' => 'required|in:NID,Passport,Driving License',
            'id_number' => 'required|string|min:10|unique:verifications,id_number,'.$request->user()->id.',user_id',
            'id_front_image' => 'required|image|mimes:jpeg,png,jpg|max:2048',
            'id_with_face_image' => 'required|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        $manager = new ImageManager(new Driver());
        // id_front_image
        $id_front_image_name = $request->user()->id."-id_front_image".".". $request->file('id_front_image')->getClientOriginalExtension();
        $image = $manager->read($request->file('id_front_image'));
        $image->toJpeg(80)->save(base_path("public/uploads/verification_photo/").$id_front_image_name);
        // id_with_face_image
        $id_with_face_image_name = $request->user()->id."-id_with_face_image".".". $request->file('id_with_face_image')->getClientOriginalExtension();
        $image = $manager->read($request->file('id_with_face_image'));
        $image->toJpeg(80)->save(base_path("public/uploads/verification_photo/").$id_with_face_image_name);

        Verification::create([
            'user_id' => $request->user()->id,
            'id_type' => $request->id_type,
            'id_number' => $request->id_number,
            'id_front_image' => $id_front_image_name,
            'id_with_face_image' => $id_with_face_image_name,
        ]);

        $notification = array(
            'message' => 'Id Verification request submitted successfully.',
            'alert-type' => 'success'
        );

        return back()->with($notification);
    }

    public function reVerificationStore(Request $request)
    {
        $request->validate([
            'id_type' => 'required|in:NID,Passport,Driving License',
            'id_number' => 'required|string|max:255|unique:verifications,id_number,'.$request->user()->id.',user_id',
            'id_front_image' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'id_with_face_image' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        $verification = Verification::where('id', $request->verification_id)->first();

        $manager = new ImageManager(new Driver());
        // id_front_image
        if ($request->file('id_front_image')) {
            unlink(base_path("public/uploads/verification_photo/").$verification->id_front_image);
            $id_front_image_name = $request->user()->id."-id_front_image".".". $request->file('id_front_image')->getClientOriginalExtension();
            $image = $manager->read($request->file('id_front_image'));
            $image->toJpeg(80)->save(base_path("public/uploads/verification_photo/").$id_front_image_name);
            $verification->update([
                'id_front_image' => $id_front_image_name,
            ]);
        }
        // id_with_face_image
        if ($request->file('id_with_face_image')) {
            unlink(base_path("public/uploads/verification_photo/").$verification->id_with_face_image);
            $id_with_face_image_name = $request->user()->id."-id_with_face_image".".". $request->file('id_with_face_image')->getClientOriginalExtension();
            $image = $manager->read($request->file('id_with_face_image'));
            $image->toJpeg(80)->save(base_path("public/uploads/verification_photo/").$id_with_face_image_name);
            $verification->update([
                'id_with_face_image' => $id_with_face_image_name,
            ]);
        }

        $verification->update([
            'id_type' => $request->id_type,
            'id_number' => $request->id_number,
            'status' => 'Pending',
        ]);

        $notification = array(
            'message' => 'Id Verification request updated successfully.',
            'alert-type' => 'success'
        );

        return back()->with($notification);
    }

    // Deposit.............................................................................................................

    public function deposit(Request $request)
    {
        $user = User::findOrFail(Auth::id());
        $hasVerification = $user->hasVerification('Approved');

        if (!$hasVerification) {
            return redirect()->route('verification')->with('error', 'Please verify your account first.');
        } else if ($user->status == 'Blocked' || $user->status == 'Banned') {
            return redirect()->route('dashboard')->with('error', 'Your account is blocked or banned.');
        } else {
            if ($request->ajax()) {
                $query = Deposit::where('user_id', Auth::id());

                if ($request->status) {
                    $query->where('deposits.status', $request->status);
                }

                if ($request->method){
                    $query->where('deposits.method', $request->method);
                }

                $query->select('deposits.*')->orderBy('created_at', 'desc');

                $deposits = $query->get();

                return DataTables::of($deposits)
                    ->addIndexColumn()
                    ->editColumn('amount', function ($row) {
                        return '<span class="badge bg-primary">' . get_site_settings('site_currency_symbol') . ' ' . $row->amount . '</span>';
                    })
                    ->editColumn('payable_amount', function ($row) {
                        return '<span class="badge bg-dark">' . get_site_settings('site_currency_symbol') . ' ' . $row->payable_amount . '</span>';
                    })
                    ->editColumn('method', function ($row) {
                        if ($row->method == 'Bkash') {
                            $method = '
                            <span class="badge bg-primary">' . $row->method . '</span>
                            ';
                        } else if ($row->method == 'Nagad') {
                            $method = '
                            <span class="badge bg-warning">' . $row->method . '</span>
                            ';
                        } else if ($row->method == 'Rocket') {
                            $method = '
                            <span class="badge bg-info">' . $row->method . '</span>
                            ';
                        } else {
                            $method = '
                            <span class="badge bg-success">' . $row->method . '</span>
                            ';
                        }
                        return $method;
                    })
                    ->editColumn('number', function ($row) {
                        if ($row->number) {
                            $number = $row->number;
                        } else {
                            $number = 'N/A';
                        }
                        return $number;
                    })
                    ->editColumn('transaction_id', function ($row) {
                        if ($row->transaction_id) {
                            $transaction_id = $row->transaction_id;
                        } else {
                            $transaction_id = 'N/A';
                        }
                        return $transaction_id;
                    })
                    ->editColumn('created_at', function ($row) {
                        return $row->created_at->format('d M Y h:i A');
                    })
                    ->addColumn('approved_or_rejected_at', function ($row) {
                        if ($row->status == 'Approved') {
                            if ($row->method == 'Withdrawal Balance') {
                                $date = date('d M Y h:i A', strtotime($row->created_at));
                            } else {
                                $date = date('d M Y h:i A', strtotime($row->approved_at));
                            }
                        } else if ($row->status == 'Rejected') {
                            $date = date('d M Y h:i A', strtotime($row->rejected_at));
                        } else {
                            $date = 'N/A';
                        }
                        return $date;
                    })
                    ->editColumn('status', function ($row) {
                        if ($row->status == 'Pending') {
                            $status = '
                            <span class="badge bg-info text-white">' . $row->status . '</span>
                            ';
                        } else if ($row->status == 'Approved') {
                            $status = '
                            <span class="badge bg-success">' . $row->status . '</span>
                            ';
                        } else {
                            $status = '
                            <span class="badge bg-danger">' . $row->status . '</span>
                            ';
                        }
                        return $status;
                    })
                    ->rawColumns(['method', 'number', 'amount', 'payable_amount', 'created_at', 'approved_or_rejected_at', 'status'])
                    ->make(true);
            }

            $total_deposit = Deposit::where('user_id', $request->user()->id)->where('status', 'Approved')->sum('amount');

            return view('frontend.deposit.index', compact('total_deposit'));
        }
    }

    public function depositStore(Request $request)
    {
        $minDepositAmount = get_default_settings('min_deposit_amount');
        $maxDepositAmount = get_default_settings('max_deposit_amount');

        $validator = Validator::make($request->all(), [
            'amount' => "required|numeric|min:$minDepositAmount|max:$maxDepositAmount",
            'method' => 'required|in:Bkash,Nagad,Rocket',
            'number' => ['required', 'string', 'regex:/^(?:\+8801|01)[3-9]\d{8}$/'],
            'transaction_id' => 'required|string|max:255',
        ],
        [
            'number.regex' => 'The phone number must be a valid Bangladeshi number (+8801XXXXXXXX or 01XXXXXXXX).',
        ]);

        if($validator->fails()){
            return response()->json([
                'status' => 400,
                'error'=> $validator->errors()->toArray()
            ]);
        }else{
            Deposit::create([
                'user_id' => $request->user()->id,
                'amount' => $request->amount,
                'payable_amount' => $request->amount,
                'method' => $request->method,
                'number' => $request->number,
                'transaction_id' => $request->transaction_id,
                'status' => 'Pending',
            ]);

            return response()->json([
                'status' => 200,
            ]);
        }
    }

    public function withdrawalBalanceDepositStore(Request $request)
    {
        $minDepositAmount = get_default_settings('min_deposit_amount');
        $maxDepositAmount = get_default_settings('max_deposit_amount');
        $currencySymbol = get_site_settings('site_currency_symbol');

        $validator = Validator::make($request->all(), [
            'deposit_amount' => "required|numeric|min:$minDepositAmount|max:$maxDepositAmount",
        ]);

        if($validator->fails()){
            return response()->json([
                'status' => 400,
                'error'=> $validator->errors()->toArray()
            ]);
        }else{
            if ($request->deposit_amount > $request->user()->withdraw_balance) {
                return response()->json([
                    'status' => 401,
                    'error'=> 'Insufficient balance in your account to deposit ' . $currencySymbol .' '. $request->deposit_amount .
                            '. Your current balance is ' . $currencySymbol .' '. $request->user()->withdraw_balance
                ]);
            }else {
                $deposit_amount = $request->deposit_amount - ($request->deposit_amount * get_default_settings('withdrawal_balance_deposit_charge_percentage') / 100);

                Deposit::create([
                    'user_id' => $request->user()->id,
                    'method' => 'Withdrawal Balance',
                    'amount' => $request->deposit_amount,
                    'payable_amount' => $deposit_amount,
                    'status' => 'Approved',
                ]);

                $request->user()->increment('deposit_balance', $deposit_amount);
                $request->user()->decrement('withdraw_balance', $request->deposit_amount);
                $total_deposit = Deposit::where('user_id', $request->user()->id)->where('status', 'Approved')->sum('amount');

                return response()->json([
                    'status' => 200,
                    'deposit_balance' => number_format($request->user()->deposit_balance, 2, '.', ''),
                    'withdraw_balance' => number_format($request->user()->withdraw_balance, 2, '.', ''),
                    'total_deposit' => $total_deposit,
                ]);
            }
        }
    }

    // Withdraw.............................................................................................................

    public function withdraw(Request $request)
    {
        $user = User::findOrFail(Auth::id());
        $hasVerification = $user->hasVerification('Approved');

        if (!$hasVerification) {
            return redirect()->route('verification')->with('error', 'Please verify your account first.');
        } else if ($user->status == 'Blocked' || $user->status == 'Banned') {
            return redirect()->route('dashboard')->with('error', 'Your account is blocked or banned.');
        } else {
            if ($request->ajax()) {
                $query = Withdraw::where('user_id', Auth::id());

                if ($request->status) {
                    $query->where('withdraws.status', $request->status);
                }

                if ($request->method){
                    $query->where('withdraws.method', $request->method);
                }

                if ($request->type){
                    $query->where('withdraws.type', $request->type);
                }

                $query->select('withdraws.*')->orderBy('created_at', 'desc');

                $withdraws = $query->get();

                return DataTables::of($withdraws)
                    ->addIndexColumn()
                    ->editColumn('type', function ($row) {
                        if ($row->type == 'Ragular') {
                            $type = '
                            <span class="badge bg-dark">' . $row->type . '</span>
                            ';
                        } else {
                            $type = '
                            <span class="badge bg-primary">' . $row->type . '</span>
                            ';
                        }
                        return $type;
                    })
                    ->editColumn('amount', function ($row) {
                        return '<span class="badge bg-primary">' . get_site_settings('site_currency_symbol') . ' ' . $row->amount . '</span>';
                    })
                    ->editColumn('payable_amount', function ($row) {
                        return '<span class="badge bg-dark">' . get_site_settings('site_currency_symbol') . ' ' . $row->payable_amount . '</span>';
                    })
                    ->editColumn('method', function ($row) {
                        if ($row->method == 'Bkash') {
                            $method = '
                            <span class="badge bg-primary">' . $row->method . '</span>
                            ';
                        } else if ($row->method == 'Nagad') {
                            $method = '
                            <span class="badge bg-success">' . $row->method . '</span>
                            ';
                        } else {
                            $method = '
                            <span class="badge bg-info">' . $row->method . '</span>
                            ';
                        }
                        return $method;
                    })
                    ->editColumn('created_at', function ($row) {
                        return $row->created_at->format('d M Y h:i A');
                    })
                    ->addColumn('approved_or_rejected_at', function ($row) {
                        if ($row->status == 'Approved') {
                            $date = date('d M Y h:i A', strtotime($row->approved_at));
                        } else if ($row->status == 'Rejected') {
                            $date = date('d M Y h:i A', strtotime($row->rejected_at));
                        } else {
                            $date = 'N/A';
                        }
                        return $date;
                    })
                    ->editColumn('status', function ($row) {
                        if ($row->status == 'Pending') {
                            $status = '
                            <span class="badge bg-info text-white">' . $row->status . '</span>
                            ';
                        } else if ($row->status == 'Approved') {
                            $status = '
                            <span class="badge bg-success">' . $row->status . '</span>
                            ';
                        } else {
                            $status = '
                            <span class="badge bg-danger">' . $row->status . '</span>
                            ';
                        }
                        return $status;
                    })
                    ->rawColumns(['type', 'method', 'amount', 'payable_amount', 'created_at', 'approved_or_rejected_at', 'status'])
                    ->make(true);
            }

            $total_withdraw = Withdraw::where('user_id', $request->user()->id)->where('status', 'Approved')->sum('amount');

            return view('frontend.withdraw.index', compact('total_withdraw'));
        }
    }

    public function withdrawStore(Request $request)
    {
        $minWithdrawAmount = get_default_settings('min_withdraw_amount');
        $maxWithdrawAmount = get_default_settings('max_withdraw_amount');
        $currencySymbol = get_site_settings('site_currency_symbol');
        $withdrawChargePercentage = get_default_settings('withdraw_charge_percentage');
        $instantWithdrawCharge = get_default_settings('instant_withdraw_charge');

        $validator = Validator::make($request->all(), [
            'type' => 'required|in:Ragular,Instant',
            'amount' => "required|numeric|min:$minWithdrawAmount|max:$maxWithdrawAmount",
            'method' => 'required|in:Bkash,Nagad,Rocket',
            'number' => ['required', 'string', 'regex:/^(?:\+8801|01)[3-9]\d{8}$/'],
        ],
        [
            'number.regex' => 'The phone number must be a valid Bangladeshi number (+8801XXXXXXXX or 01XXXXXXXX).',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 400,
                'error' => $validator->errors()->toArray()
            ]);
        }

        if ($request->amount > $request->user()->withdraw_balance) {
            return response()->json([
                'status' => 402,
                'error' => 'Insufficient balance in your account to withdraw ' . $currencySymbol . $request->amount .
                        '. Your current balance is ' . $currencySymbol . $request->user()->withdraw_balance
            ]);
        }

        $payableAmount = $request->amount - ($request->amount * $withdrawChargePercentage / 100);
        if ($request->type == 'Instant') {
            $payableAmount -= $instantWithdrawCharge;
        }

        Withdraw::create([
            'type' => $request->type,
            'user_id' => $request->user()->id,
            'amount' => $request->amount,
            'method' => $request->method,
            'number' => $request->number,
            'payable_amount' => $payableAmount,
            'status' => 'Pending',
        ]);

        $request->user()->decrement('withdraw_balance', $request->amount);

        return response()->json([
            'status' => 200,
            'withdraw_balance' => number_format($request->user()->withdraw_balance, 2, '.', ''),
        ]);
    }

    // Bonus.............................................................................................................

    public function bonus(Request $request)
    {
        $user = User::findOrFail(Auth::id());
        $hasVerification = $user->hasVerification('Approved');

        if (!$hasVerification) {
            return redirect()->route('verification')->with('error', 'Please verify your account first.');
        } else if ($user->status == 'Blocked' || $user->status == 'Banned') {
            return redirect()->route('dashboard')->with('error', 'Your account is blocked or banned.');
        } else {
            if ($request->ajax()) {
                $query = Bonus::where('user_id', Auth::id());

                if ($request->type) {
                    $query->where('bonuses.type', $request->type);
                }

                $query->select('bonuses.*')->orderBy('created_at', 'desc');

                $bonuses = $query->get();

                return DataTables::of($bonuses)
                    ->addIndexColumn()
                    ->editColumn('type', function ($row) {
                        $type = '
                            <span class="badge bg-primary">' . $row->type . '</span>
                        ';
                        return $type;
                    })
                    ->editColumn('bonus_by', function ($row) {
                        if ($row->type == 'Proof Task Approved Bonus') {
                            $bonus_by = '
                            <a href="'.route('user.profile', encrypt($row->bonusBy->id)).'" title="'.$row->bonusBy->name.'" class="text-info">
                                '.$row->bonusBy->name.'
                            </a>
                            ';
                        } else {
                            $bonus_by = '
                            <span class="badge bg-primary">'.get_site_settings('site_name').'</span>
                            ';
                        }
                        return $bonus_by;
                    })
                    ->editColumn('amount', function ($row) {
                        return '<span class="badge bg-primary">' . get_site_settings('site_currency_symbol') . ' ' . $row->amount . '</span>';
                    })
                    ->editColumn('created_at', function ($row) {
                        return $row->created_at->format('d M Y h:i A');
                    })
                    ->rawColumns(['type', 'bonus_by', 'amount', 'created_at'])
                    ->make(true);
            }

            $total_bonus = Bonus::where('user_id', Auth::id())->sum('amount');

            return view('frontend.bonus.index', compact('total_bonus'));
        }
    }

    // Notification.............................................................................................................

    public function notification(Request $request)
    {
        if ($request->ajax()) {
            $user = Auth::user();
            $notifications = $user->notifications;

            return DataTables::of($notifications)
                ->addIndexColumn()
                ->editColumn('type', function ($row) {
                    return class_basename($row->type);
                })
                ->editColumn('title', function ($row) {
                    return $row->data['title'];
                })
                ->editColumn('message', function ($row) {
                    return $row->data['message'];
                })
                ->editColumn('created_at', function ($row) {
                    return $row->created_at->diffForHumans();
                })
                ->editColumn('status', function ($row) {
                    if ($row->read_at) {
                        $status = '
                        <span class="badge bg-success">Read</span>
                        ';
                    } else {
                        $status = '
                        <span class="badge bg-danger">Unread</span>
                        ';
                    }
                    return $status;
                })
                ->rawColumns(['status'])
                ->make(true);
        }

        return view('frontend.notification.index');
    }

    public function notificationRead($id)
    {
        $notification = Auth::user()->notifications()->where('id', $id)->first();
        if ($notification) {
            $notification->markAsRead();
        }
        return redirect()->route('notification');
    }

    public function notificationReadAll()
    {
        Auth::user()->unreadNotifications->markAsRead();
        return redirect()->route('notification');
    }

    // Refferal.............................................................................................................

    public function refferal()
    {
        return view('frontend.refferal.index');
    }

    // Block.............................................................................................................

    public function blockList(Request $request)
    {
        if ($request->ajax()) {
            $blockedUsers = Block::where('blocked_by', Auth::id());

            $query = $blockedUsers->select('blocks.*');

            $blockedList = $query->get();

            return DataTables::of($blockedList)
                ->addIndexColumn()
                ->editColumn('user', function ($row) {
                    return '
                        <a href="'.route('user.profile', encrypt($row->blocked->id)).'" title="'.$row->blocked->name.'" class="text-info">
                            '.$row->blocked->name.'
                        </a>
                    ';
                })
                ->editColumn('blocked_at', function ($row) {
                    return date('d M Y h:i A', strtotime($row->blocked_at));
                })
                ->addColumn('action', function ($row) {
                    $action = '
                    <a href="'.route('block_user', $row->blocked->id).'" title="Unblock" class="btn btn-danger btn-sm">
                        Unblock
                    </a>
                    ';
                    return $action;
                })
                ->rawColumns(['user', 'action'])
                ->make(true);
        }
        return view('frontend.block_list.index');
    }

    public function blockUser($id)
    {
        $blocked = Block::where('user_id', $id)->where('blocked_by', Auth::id())->exists();

        if ($blocked) {
            Block::where('user_id', $id)->where('blocked_by', Auth::id())->delete();

            $notification = array(
                'message' => 'User unblocked successfully.',
                'alert-type' => 'success'
            );

            return back()->with($notification);
        }

        Block::create([
            'user_id' => $id,
            'blocked_by' => Auth::id(),
            'blocked_at' => now(),
        ]);

        $notification = array(
            'message' => 'User blocked successfully.',
            'alert-type' => 'error'
        );

        return back()->with($notification);
    }

    // Report.............................................................................................................

    public function reportList(Request $request)
    {
        if ($request->ajax()) {
            $reportedUsers = Report::where('reported_by', Auth::id());

            $query = $reportedUsers->select('reports.*');

            if ($request->status) {
                $query->where('reports.status', $request->status);
            }

            $reportedList = $query->get();

            return DataTables::of($reportedList)
                ->addIndexColumn()
                ->editColumn('user', function ($row) {
                    return '
                        <a href="'.route('user.profile', encrypt($row->reported->id)).'" title="'.$row->reported->name.'" class="text-info">
                            '.$row->reported->name.'
                    ';
                })
                ->editColumn('created_at', function ($row) {
                    return date('d M Y h:i A', strtotime($row->created_at));
                })
                ->editColumn('status', function ($row) {
                    if ($row->status == 'Pending') {
                        $status = '
                        <span class="badge bg-warning">' . $row->status . '</span>
                        ';
                    } else {
                        $status = '
                        <span class="badge bg-success">' . $row->status . '</span>
                        ';
                    }
                    return $status;
                })
                ->addColumn('action', function ($row) {
                    $action = '
                        <button type="button" data-id="' . $row->id . '" class="btn btn-primary btn-xs viewBtn" data-bs-toggle="modal" data-bs-target=".viewModal">View</button>
                    ';
                    return $action;
                })
                ->rawColumns(['user', 'status', 'action'])
                ->make(true);
        }
        return view('frontend.report_list.index');
    }

    public function reportView($id)
    {
        $report = Report::findOrFail($id);
        $report_reply = ReportReply::where('report_id', $id)->first();
        return view('frontend.report_list.view', compact('report', 'report_reply'));
    }

    public function reportUser(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'reason' => 'required|string|max:5000',
            'photo' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        if($validator->fails()){
            return response()->json([
                'status' => 400,
                'error'=> $validator->errors()->toArray()
            ]);
        }else{
            $photo_name = null;
            if ($request->file('photo')) {
                $manager = new ImageManager(new Driver());
                $photo_name = $id."-report_photo".date('YmdHis').".".$request->file('photo')->getClientOriginalExtension();
                $image = $manager->read($request->file('photo'));
                $image->toJpeg(80)->save(base_path("public/uploads/report_photo/").$photo_name);
            }

            Report::create([
                'user_id' => $id,
                'reported_by' => Auth::id(),
                'reason' => $request->reason,
                'photo' => $photo_name,
                'status' => 'Pending',
            ]);

            return response()->json([
                'status' => 200,
            ]);
        }
    }

    // Support.............................................................................................................

    public function support(){
        $supports = Support::where('sender_id', Auth::id())->orWhere('receiver_id', Auth::id())->get();

        Support::where('receiver_id', Auth::id())->where('status', 'Unread')->each(function ($message) {
            $message->status = 'Read';
            $message->save();
        });

        return view('frontend.support.index' , compact('supports'));
    }

    public function supportSendMessage(Request $request){
        $validator = Validator::make($request->all(), [
            'message' => 'required|string|max:5000',
            'photo' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        if($validator->fails()){
            return response()->json([
                'status' => 400,
                'error'=> $validator->errors()->toArray()
            ]);
        }else{
            $photo_name = null;
            if ($request->file('photo')) {
                $manager = new ImageManager(new Driver());
                $photo_name = Auth::id()."-support_photo_".date('YmdHis').".".$request->file('photo')->getClientOriginalExtension();
                $image = $manager->read($request->file('photo'));
                $image->toJpeg(80)->save(base_path("public/uploads/support_photo/").$photo_name);
            }

            $support = Support::create([
                'sender_id' => Auth::id(),
                'receiver_id' => 1,
                'message' => $request->message,
                'photo' => $photo_name,
            ]);

            Support::where('receiver_id', Auth::id())->where('status', 'Unread')->each(function ($message) {
                $message->status = 'Read';
                $message->save();
            });

            SupportEvent::dispatch($support);

            return response()->json([
                'status' => 200,
                'support' => $support,
            ]);
        }
    }
}
