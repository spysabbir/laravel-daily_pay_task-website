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

        @can('ExpenseMenu')
            <li class="nav-item">
                <a class="nav-link" data-bs-toggle="collapse" href="#expense" role="button" aria-expanded="false" aria-controls="expense">
                    <i class="link-icon" data-feather="dollar-sign"></i>
                    <span class="link-title">Expense</span>
                    <i class="link-arrow" data-feather="chevron-down"></i>
                </a>
                <div class="collapse" id="expense">
                    <ul class="nav sub-menu">
                        @can('expense_category.index')
                        <li class="nav-item">
                            <a href="{{ route('backend.expense_category.index') }}" class="nav-link">Expense Category</a>
                        </li>
                        @endcan
                        @can('expense.index')
                        <li class="nav-item">
                            <a href="{{ route('backend.expense.index') }}" class="nav-link">Expense</a>
                        </li>
                        @endcan
                    </ul>
                </div>
            </li>
        @endcan

        @can('ReportMenu')
            <li class="nav-item">
                <a class="nav-link" data-bs-toggle="collapse" href="#report" role="button" aria-expanded="false" aria-controls="report">
                    <i class="link-icon" data-feather="file-text"></i>
                    <span class="link-title">Report</span>
                    <i class="link-arrow" data-feather="chevron-down"></i>
                </a>
                <div class="collapse" id="report">
                    <ul class="nav sub-menu">
                        @can('deposit.report')
                        <li class="nav-item">
                            <a href="{{ route('backend.deposit.report') }}" class="nav-link">Deposit</a>
                        </li>
                        @endcan
                        @can('withdraw.report')
                        <li class="nav-item">
                            <a href="{{ route('backend.withdraw.report') }}" class="nav-link">Withdraw</a>
                        </li>
                        @endcan
                        @can('bonus.report')
                        <li class="nav-item">
                            <a href="{{ route('backend.bonus.report') }}" class="nav-link">Bonus</a>
                        </li>
                        @endcan
                        @can('expense.report')
                        <li class="nav-item">
                            <a href="{{ route('backend.expense.report') }}" class="nav-link">Expense</a>
                        </li>
                        @endcan
                        @can('posted_task.report')
                        <li class="nav-item">
                            <a href="{{ route('backend.posted_task.report') }}" class="nav-link">Posted Task</a>
                        </li>
                        @endcan
                        @can('worked_task.report')
                        <li class="nav-item">
                            <a href="{{ route('backend.worked_task.report') }}" class="nav-link">Worked Task</a>
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
            <li class="nav-item">
                <a class="nav-link" data-bs-toggle="collapse" href="#EmployeeMenu" role="button" aria-expanded="false" aria-controls="EmployeeMenu">
                    <i class="link-icon" data-feather="users"></i>
                    <span class="link-title">Employee</span>
                    <i class="link-arrow" data-feather="chevron-down"></i>
                </a>
                <div class="collapse" id="EmployeeMenu">
                    <ul class="nav sub-menu">
                        @can('employee.index')
                        <li class="nav-item">
                            <a href="{{ route('backend.employee.index') }}" class="nav-link">Active</a>
                        </li>
                        @endcan
                        @can('employee.inactive')
                        <li class="nav-item">
                            <a href="{{ route('backend.employee.inactive') }}" class="nav-link">Inactive</a>
                        </li>
                        @endcan
                    </ul>
                </div>
            </li>
        @endcan

        @can('UserMenu')
            <li class="nav-item">
                <a class="nav-link" data-bs-toggle="collapse" href="#UserMenu" role="button" aria-expanded="false" aria-controls="UserMenu">
                    <i class="link-icon" data-feather="user"></i>
                    <span class="link-title">User</span>
                    <i class="link-arrow" data-feather="chevron-down"></i>
                </a>
                <div class="collapse" id="UserMenu">
                    <ul class="nav sub-menu">
                        @can('user.active')
                        <li class="nav-item">
                            <a href="{{ route('backend.user.active') }}" class="nav-link">Active</a>
                        </li>
                        @endcan
                        @can('user.inactive')
                        <li class="nav-item">
                            <a href="{{ route('backend.user.inactive') }}" class="nav-link">Inactive</a>
                        </li>
                        @endcan
                        @can('user.blocked')
                        <li class="nav-item">
                            <a href="{{ route('backend.user.blocked') }}" class="nav-link">Blocked</a>
                        </li>
                        @endcan
                        @can('user.banned')
                        <li class="nav-item">
                            <a href="{{ route('backend.user.banned') }}" class="nav-link">Banned</a>
                        </li>
                        @endcan
                    </ul>
                </div>
            </li>
        @endcan

        <li class="nav-item nav-category">Editor</li>
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

        @can('TaskPostChargeMenu')
            @can('task_post_charge.index')
            <li class="nav-item">
                <a href="{{ route('backend.task_post_charge.index') }}" class="nav-link">
                    <i class="link-icon" data-feather="dollar-sign"></i>
                    <span class="link-title">Task Post Charge</span>
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

        @can('TestimonialMenu')
            @can('testimonial.index')
            <li class="nav-item">
                <a href="{{ route('backend.testimonial.index') }}" class="nav-link">
                    <i class="link-icon" data-feather="message-square"></i>
                    <span class="link-title">Testimonial</span>
                </a>
            </li>
            @endcan
        @endcan

        @can('SubscriberMenu')
            <li class="nav-item">
                <a class="nav-link" data-bs-toggle="collapse" href="#SubscriberMenu" role="button" aria-expanded="false" aria-controls="SubscriberMenu">
                    <i class="link-icon" data-feather="users"></i>
                    <span class="link-title">Subscriber</span>
                    <i class="link-arrow" data-feather="chevron-down"></i>
                </a>
                <div class="collapse" id="SubscriberMenu">
                    <ul class="nav sub-menu">
                        @can('subscriber.index')
                        <li class="nav-item">
                            <a href="{{ route('backend.subscriber.index') }}" class="nav-link">Subscriber</a>
                        </li>
                        @endcan
                        @can('subscriber.newsletter')
                        <li class="nav-item">
                            <a href="{{ route('backend.subscriber.newsletter') }}" class="nav-link">Newsletter</a>
                        </li>
                        @endcan
                    </ul>
                </div>
            </li>
        @endcan

        <li class="nav-item nav-category">Service Agent</li>
        @can('SupportMenu')
            @can('support')
            <li class="nav-item">
                <a href="{{ route('backend.support') }}" class="nav-link">
                    <i class="link-icon" data-feather="message-circle"></i>
                    <span class="link-title">Support</span>
                </a>
            </li>
            @endcan
        @endcan

        @can('ContactMenu')
            @can('contact')
            <li class="nav-item">
                <a href="{{ route('backend.contact') }}" class="nav-link">
                    <i class="link-icon" data-feather="phone"></i>
                    <span class="link-title">Contact</span>
                </a>
            </li>
            @endcan
        @endcan

        @can('ReportListMenu')
            <li class="nav-item">
                <a class="nav-link" data-bs-toggle="collapse" href="#ReportListMenu" role="button" aria-expanded="false" aria-controls="ReportListMenu">
                    <i class="link-icon" data-feather="alert-triangle"></i>
                    <span class="link-title">Report User</span>
                    <i class="link-arrow" data-feather="chevron-down"></i>
                </a>
                <div class="collapse" id="ReportListMenu">
                    <ul class="nav sub-menu">
                        @can('report_list.pending')
                        <li class="nav-item">
                            <a href="{{ route('backend.report_list.pending') }}" class="nav-link">Pending</a>
                        </li>
                        @endcan
                        @can('report_list.resolved')
                        <li class="nav-item">
                            <a href="{{ route('backend.report_list.resolved') }}" class="nav-link">Resolved</a>
                        </li>
                        @endcan
                    </ul>
                </div>
            </li>
        @endcan

        <li class="nav-item nav-category">Moderator</li>
        @can('VerificationMenu')
            <li class="nav-item">
                <a class="nav-link" data-bs-toggle="collapse" href="#VerificationMenu" role="button" aria-expanded="false" aria-controls="VerificationMenu">
                    <i class="link-icon" data-feather="user-check"></i>
                    <span class="link-title">Verification</span>
                    <i class="link-arrow" data-feather="chevron-down"></i>
                </a>
                <div class="collapse" id="VerificationMenu">
                    <ul class="nav sub-menu">
                        @can('verification.request')
                        <li class="nav-item">
                            <a href="{{ route('backend.verification.request') }}" class="nav-link">Pending</a>
                        </li>
                        @endcan
                        @can('verification.request.approved')
                        <li class="nav-item">
                            <a href="{{ route('backend.verification.request.approved') }}" class="nav-link">Approved</a>
                        </li>
                        @endcan
                    </ul>
                </div>
            </li>
        @endcan

        @can('DepositMenu')
            <li class="nav-item">
                <a class="nav-link" data-bs-toggle="collapse" href="#DepositMenu" role="button" aria-expanded="false" aria-controls="DepositMenu">
                    <i class="link-icon" data-feather="credit-card"></i>
                    <span class="link-title">Deposit</span>
                    <i class="link-arrow" data-feather="chevron-down"></i>
                </a>
                <div class="collapse" id="DepositMenu">
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
                <a class="nav-link" data-bs-toggle="collapse" href="#WithdrawMenu" role="button" aria-expanded="false" aria-controls="WithdrawMenu">
                    <i class="link-icon" data-feather="dollar-sign"></i>
                    <span class="link-title">Withdraw</span>
                    <i class="link-arrow" data-feather="chevron-down"></i>
                </a>
                <div class="collapse" id="WithdrawMenu">
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

        @can('PostedTaskMenu')
            <li class="nav-item">
                <a class="nav-link" data-bs-toggle="collapse" href="#PostedTaskMenu" role="button" aria-expanded="false" aria-controls="PostedTaskMenu">
                    <i class="link-icon" data-feather="briefcase"></i>
                    <span class="link-title">Posted Task</span>
                    <i class="link-arrow" data-feather="chevron-down"></i>
                </a>
                <div class="collapse" id="PostedTaskMenu">
                    <ul class="nav sub-menu">
                        @can('posted_task_list.pending')
                        <li class="nav-item">
                            <a href="{{ route('backend.posted_task_list.pending') }}" class="nav-link">Pending</a>
                        </li>
                        @endcan
                        @can('posted_task_list.rejected')
                        <li class="nav-item">
                            <a href="{{ route('backend.posted_task_list.rejected') }}" class="nav-link">Rejected</a>
                        </li>
                        @endcan
                        @can('posted_task_list.running')
                        <li class="nav-item">
                            <a href="{{ route('backend.posted_task_list.running') }}" class="nav-link">Running</a>
                        </li>
                        @endcan
                        @can('posted_task_list.canceled')
                        <li class="nav-item">
                            <a href="{{ route('backend.posted_task_list.canceled') }}" class="nav-link">Canceled</a>
                        </li>
                        @endcan
                        @can('posted_task_list.paused')
                        <li class="nav-item">
                            <a href="{{ route('backend.posted_task_list.paused') }}" class="nav-link">Paused</a>
                        </li>
                        @endcan
                        @can('posted_task_list.completed')
                        <li class="nav-item">
                            <a href="{{ route('backend.posted_task_list.completed') }}" class="nav-link">Completed</a>
                        </li>
                        @endcan
                    </ul>
                </div>
            </li>
        @endcan

        @can('WorkedTaskMenu')
            <li class="nav-item">
                <a class="nav-link" data-bs-toggle="collapse" href="#WorkedTaskMenu" role="button" aria-expanded="false" aria-controls="WorkedTaskMenu">
                    <i class="link-icon" data-feather="check-circle"></i>
                    <span class="link-title">Worked Task</span>
                    <i class="link-arrow" data-feather="chevron-down"></i>
                </a>
                <div class="collapse" id="WorkedTaskMenu">
                    <ul class="nav sub-menu">
                        @can('worked_task_list.all')
                        <li class="nav-item">
                            <a href="{{ route('backend.worked_task_list.all') }}" class="nav-link">All</a>
                        </li>
                        @endcan
                        @can('worked_task_list.reviewed')
                        <li class="nav-item">
                            <a href="{{ route('backend.worked_task_list.reviewed') }}" class="nav-link">Reviewed</a>
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
            <a href="{{ route('find_tasks.clear.filters') }}" class="nav-link">
                <i class="link-icon" data-feather="search"></i>
                <span class="link-title">Find Tasks</span>
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link" data-bs-toggle="collapse" href="#workedTaskList" role="button" aria-expanded="false" aria-controls="workedTaskList">
                <i class="link-icon" data-feather="list"></i>
                <span class="link-title">Worked Task List</span>
                <i class="link-arrow" data-feather="chevron-down"></i>
            </a>
            <div class="collapse" id="workedTaskList">
                <ul class="nav sub-menu">
                    <li class="nav-item">
                        <a href="{{ route('worked_task.list.pending') }}" class="nav-link">Pending</a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ route('worked_task.list.approved') }}" class="nav-link">Approved</a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ route('worked_task.list.rejected') }}" class="nav-link">Rejected</a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ route('worked_task.list.reviewed') }}" class="nav-link">Reviewed</a>
                    </li>
                </ul>
            </div>
        </li>

        <li class="nav-item">
            <a href="{{ route('post_task') }}" class="nav-link">
                <i class="link-icon" data-feather="plus"></i>
                <span class="link-title">Post Task</span>
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link" data-bs-toggle="collapse" href="#postedTaskList" role="button" aria-expanded="false" aria-controls="postedTaskList">
                <i class="link-icon" data-feather="list"></i>
                <span class="link-title">Posted Task List</span>
                <i class="link-arrow" data-feather="chevron-down"></i>
            </a>
            <div class="collapse" id="postedTaskList">
                <ul class="nav sub-menu">
                    <li class="nav-item">
                        <a href="{{ route('posted_task.list.pending') }}" class="nav-link">Pending</a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ route('posted_task.list.rejected') }}" class="nav-link">Rejected</a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ route('posted_task.list.running') }}" class="nav-link">Running</a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ route('posted_task.list.canceled') }}" class="nav-link">Canceled</a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ route('posted_task.list.paused') }}" class="nav-link">Paused</a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ route('posted_task.list.completed') }}" class="nav-link">Completed</a>
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
                        <a href="{{ route('bonus') }}" class="nav-link">Bonus</a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ route('withdraw') }}" class="nav-link">Withdraw</a>
                    </li>
                </ul>
            </div>
        </li>

        <li class="nav-item">
            <a href="{{ route('block_list') }}" class="nav-link">
                <i class="link-icon" data-feather="shield"></i>
                <span class="link-title">Block List</span>
            </a>
        </li>

        <li class="nav-item">
            <a href="{{ route('report_list') }}" class="nav-link">
                <i class="link-icon" data-feather="file-text"></i>
                <span class="link-title">Report List</span>
            </a>
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

        <li class="nav-item">
            <a href="{{ route('support') }}" class="nav-link">
                <i class="link-icon" data-feather="message-circle"></i>
                <span class="link-title">Support</span>
            </a>
        </li>
    @endif
</ul>
