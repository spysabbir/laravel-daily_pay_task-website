<ul class="nav">
    <li class="nav-item nav-category">Main</li>
    <li class="nav-item">
        <a href="{{ Auth::user()->user_type === 'Backend' ? route('backend.dashboard') : route('dashboard') }}" class="nav-link">
            <i class="link-icon" data-feather="box"></i>
            <span class="link-title">Dashboard</span>
        </a>
    </li>

    @if (Auth::user()->user_type === 'Backend')
        <li class="nav-item nav-category">Super Admin</li>
        @can('SettingMenu')
        <li class="nav-item">
            <a class="nav-link" data-bs-toggle="collapse" href="#setting" role="button" aria-expanded="false" aria-controls="setting">
                <i class="link-icon" data-feather="settings"></i>
                <span class="link-title">Setting</span>
                <i class="link-arrow" data-feather="chevron-down"></i>
            </a>
            <div class="collapse" id="setting">
                <ul class="nav sub-menu">
                    @can('site.setting')
                    <li class="nav-item">
                        <a href="{{ route('backend.site.setting') }}" class="nav-link">Site</a>
                    </li>
                    @endcan
                    @can('default.setting')
                    <li class="nav-item">
                        <a href="{{ route('backend.default.setting') }}" class="nav-link">Default</a>
                    </li>
                    @endcan
                    @can('seo.setting')
                    <li class="nav-item">
                        <a href="{{ route('backend.seo.setting') }}" class="nav-link">Seo</a>
                    </li>
                    @endcan
                    @can('mail.setting')
                    <li class="nav-item">
                        <a href="{{ route('backend.mail.setting') }}" class="nav-link">Mail</a>
                    </li>
                    @endcan
                    @can('sms.setting')
                    <li class="nav-item">
                        <a href="{{ route('backend.sms.setting') }}" class="nav-link">Sms</a>
                    </li>
                    @endcan
                    @can('captcha.setting')
                    <li class="nav-item">
                        <a href="{{ route('backend.captcha.setting') }}" class="nav-link">Captcha</a>
                    </li>
                    @endcan
                </ul>
            </div>
        </li>
        @endcan

        <li class="nav-item nav-category">Admin</li>
        @can('RolePermissionMenu')
            <li class="nav-item">
                <a class="nav-link" data-bs-toggle="collapse" href="#rolePermission" role="button" aria-expanded="false" aria-controls="rolePermission">
                    <i class="link-icon" data-feather="lock"></i>
                    <span class="link-title">Role Permission</span>
                    <i class="link-arrow" data-feather="chevron-down"></i>
                </a>
                <div class="collapse" id="rolePermission">
                    <ul class="nav sub-menu">
                        @can('role.index')
                        <li class="nav-item">
                            <a href="{{ route('backend.role.index') }}" class="nav-link">Role</a>
                        </li>
                        @endcan
                        @can('permission.index')
                        <li class="nav-item">
                            <a href="{{ route('backend.permission.index') }}" class="nav-link">Permission</a>
                        </li>
                        @endcan
                        @can('role-permission.index')
                        <li class="nav-item">
                            <a href="{{ route('backend.role-permission.index') }}" class="nav-link">Assigning</a>
                        </li>
                        @endcan
                    </ul>
                </div>
            </li>
        @endcan

        @can('EmployeeMenu')
            @can('employee.index')
            <li class="nav-item">
                <a href="{{ route('backend.employee.index') }}" class="nav-link">
                    <i class="link-icon" data-feather="users"></i>
                    <span class="link-title">Employee</span>
                </a>
            </li>
            @endcan
        @endcan

        @can('CategoryMenu')
            @can('category.index')
            <li class="nav-item">
                <a href="{{ route('backend.category.index') }}" class="nav-link">
                    <i class="link-icon" data-feather="file"></i>
                    <span class="link-title">Category</span>
                </a>
            </li>
            @endcan
        @endcan

        @can('SubCategoryMenu')
            @can('sub_category.index')
            <li class="nav-item">
                <a href="{{ route('backend.sub_category.index') }}" class="nav-link">
                    <i class="link-icon" data-feather="folder"></i>
                    <span class="link-title">Sub Category</span>
                </a>
            </li>
            @endcan
        @endcan

        @can('ChildCategoryMenu')
            @can('child_category.index')
            <li class="nav-item">
                <a href="{{ route('backend.child_category.index') }}" class="nav-link">
                    <i class="link-icon" data-feather="folder-plus"></i>
                    <span class="link-title">Child Category</span>
                </a>
            </li>
            @endcan
        @endcan

        @can('JobPostChargeMenu')
            @can('job_post_charge.index')
            <li class="nav-item">
                <a href="{{ route('backend.job_post_charge.index') }}" class="nav-link">
                    <i class="link-icon" data-feather="folder-plus"></i>
                    <span class="link-title">Job Post Charge</span>
                </a>
            </li>
            @endcan
        @endcan

        @can('FaqMenu')
            @can('faq.index')
            <li class="nav-item">
                <a href="{{ route('backend.faq.index') }}" class="nav-link">
                    <i class="link-icon" data-feather="help-circle"></i>
                    <span class="link-title">Faq</span>
                </a>
            </li>
            @endcan
        @endcan

        <li class="nav-item nav-category">User</li>
        @can('UserMenu')
            @can('user.index')
            <li class="nav-item">
                <a href="{{ route('backend.user.index') }}" class="nav-link">
                    <i class="link-icon" data-feather="user"></i>
                    <span class="link-title">User</span>
                </a>
            </li>
            @endcan
        @endcan

        @can('VerificationMenu')
            @can('verification.request')
            <li class="nav-item">
                <a href="{{ route('backend.verification.request') }}" class="nav-link">
                    <i class="link-icon" data-feather="user-check"></i>
                    <span class="link-title">Verification Request</span>
                </a>
            </li>
            @endcan
        @endcan

        @can('DepositMenu')
            <li class="nav-item">
                <a class="nav-link" data-bs-toggle="collapse" href="#depositRequest" role="button" aria-expanded="false" aria-controls="depositRequest">
                    <i class="link-icon" data-feather="credit-card"></i>
                    <span class="link-title">Deposit request</span>
                    <i class="link-arrow" data-feather="chevron-down"></i>
                </a>
                <div class="collapse" id="depositRequest">
                    <ul class="nav sub-menu">
                        @can('deposit.request')
                        <li class="nav-item">
                            <a href="{{ route('backend.deposit.request') }}" class="nav-link">Pending</a>
                        </li>
                        @endcan
                        @can('deposit.request.approved')
                        <li class="nav-item">
                            <a href="{{ route('backend.deposit.request.approved') }}" class="nav-link">Approved</a>
                        </li>
                        @endcan
                    </ul>
                </div>
            </li>
        @endcan

        @can('WithdrawMenu')
            <li class="nav-item">
                <a class="nav-link" data-bs-toggle="collapse" href="#withdrawRequest" role="button" aria-expanded="false" aria-controls="withdrawRequest">
                    <i class="link-icon" data-feather="dollar-sign"></i>
                    <span class="link-title">Withdraw request</span>
                    <i class="link-arrow" data-feather="chevron-down"></i>
                </a>
                <div class="collapse" id="withdrawRequest">
                    <ul class="nav sub-menu">
                        @can('withdraw.request')
                        <li class="nav-item">
                            <a href="{{ route('backend.withdraw.request') }}" class="nav-link">Pending</a>
                        </li>
                        @endcan
                        @can('withdraw.request.approved')
                        <li class="nav-item">
                            <a href="{{ route('backend.withdraw.request.approved') }}" class="nav-link">Approved</a>
                        </li>
                        @endcan
                    </ul>
                </div>
            </li>
        @endcan
    @else
        <li class="nav-item nav-category">User</li>
        @if (!Auth::user()->hasVerification('Approved'))
        <li class="nav-item">
            <a href="{{ route('verification') }}" class="nav-link">
                <i class="link-icon" data-feather="user-check"></i>
                <span class="link-title">Verification</span>
            </a>
        </li>
        @endif
        <li class="nav-item">
            <a href="{{ route('find.works') }}" class="nav-link">
                <i class="link-icon" data-feather="search"></i>
                <span class="link-title">Find Works</span>
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link" data-bs-toggle="collapse" href="#workList" role="button" aria-expanded="false" aria-controls="workList">
                <i class="link-icon" data-feather="list"></i>
                <span class="link-title">Work List</span>
                <i class="link-arrow" data-feather="chevron-down"></i>
            </a>
            <div class="collapse" id="workList">
                <ul class="nav sub-menu">
                    <li class="nav-item">
                        <a href="{{ route('work.list.pending') }}" class="nav-link">Pending</a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ route('work.list.approved') }}" class="nav-link">Approved</a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ route('work.list.rejected') }}" class="nav-link">Rejected</a>
                    </li>
                </ul>
            </div>
        </li>

        <li class="nav-item">
            <a href="{{ route('post.job') }}" class="nav-link">
                <i class="link-icon" data-feather="plus"></i>
                <span class="link-title">Post Job</span>
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link" data-bs-toggle="collapse" href="#jobList" role="button" aria-expanded="false" aria-controls="jobList">
                <i class="link-icon" data-feather="list"></i>
                <span class="link-title">Job List</span>
                <i class="link-arrow" data-feather="chevron-down"></i>
            </a>
            <div class="collapse" id="jobList">
                <ul class="nav sub-menu">
                    <li class="nav-item">
                        <a href="{{ route('job.list.running') }}" class="nav-link">Running</a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ route('job.list.completed') }}" class="nav-link">Completed</a>
                    </li>
                </ul>
            </div>
        </li>

        <li class="nav-item">
            <a class="nav-link" data-bs-toggle="collapse" href="#wallet" role="button" aria-expanded="false" aria-controls="wallet">
                <i class="link-icon" data-feather="credit-card"></i>
                <span class="link-title">Wallet</span>
                <i class="link-arrow" data-feather="chevron-down"></i>
            </a>
            <div class="collapse" id="wallet">
                <ul class="nav sub-menu">
                    <li class="nav-item">
                        <a href="{{ route('deposit') }}" class="nav-link">Deposit</a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ route('withdraw') }}" class="nav-link">Withdraw</a>
                    </li>
                </ul>
            </div>
        </li>

        <li class="nav-item">
            <a href="{{ route('notification') }}" class="nav-link">
                <i class="link-icon" data-feather="bell"></i>
                <span class="link-title">Notification</span>
            </a>
        </li>

        <li class="nav-item">
            <a href="{{ route('refferal') }}" class="nav-link">
                <i class="link-icon" data-feather="share"></i>
                <span class="link-title">Refferal</span>
            </a>
        </li>
    @endif
</ul>
