<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;

class PermissionTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Permission::insert([
            // RolePermissionManagement
            ['name' => 'RolePermissionMenu', 'group_name' => 'RolePermissionManagement', 'guard_name' => 'web'],
            ['name' => 'role.index', 'group_name' => 'RolePermissionManagement', 'guard_name' => 'web'],
            ['name' => 'role.create', 'group_name' => 'RolePermissionManagement', 'guard_name' => 'web'],
            ['name' => 'role.edit', 'group_name' => 'RolePermissionManagement', 'guard_name' => 'web'],
            ['name' => 'role.destroy', 'group_name' => 'RolePermissionManagement', 'guard_name' => 'web'],
            ['name' => 'permission.index', 'group_name' => 'RolePermissionManagement', 'guard_name' => 'web'],
            ['name' => 'permission.create', 'group_name' => 'RolePermissionManagement', 'guard_name' => 'web'],
            ['name' => 'permission.edit', 'group_name' => 'RolePermissionManagement', 'guard_name' => 'web'],
            ['name' => 'permission.destroy', 'group_name' => 'RolePermissionManagement', 'guard_name' => 'web'],
            ['name' => 'role-permission.index', 'group_name' => 'RolePermissionManagement', 'guard_name' => 'web'],
            ['name' => 'role-permission.edit', 'group_name' => 'RolePermissionManagement', 'guard_name' => 'web'],
            ['name' => 'role-permission.destroy', 'group_name' => 'RolePermissionManagement', 'guard_name' => 'web'],
            // ExpenseManagement
            ['name' => 'ExpenseMenu', 'group_name' => 'ExpenseManagement', 'guard_name' => 'web'],
            ['name' => 'expense_category.index', 'group_name' => 'ExpenseManagement', 'guard_name' => 'web'],
            ['name' => 'expense_category.create', 'group_name' => 'ExpenseManagement', 'guard_name' => 'web'],
            ['name' => 'expense_category.edit', 'group_name' => 'ExpenseManagement', 'guard_name' => 'web'],
            ['name' => 'expense_category.destroy', 'group_name' => 'ExpenseManagement', 'guard_name' => 'web'],
            ['name' => 'expense_category.trash', 'group_name' => 'ExpenseManagement', 'guard_name' => 'web'],
            ['name' => 'expense_category.restore', 'group_name' => 'ExpenseManagement', 'guard_name' => 'web'],
            ['name' => 'expense_category.delete', 'group_name' => 'ExpenseManagement', 'guard_name' => 'web'],
            ['name' => 'expense_category.status', 'group_name' => 'ExpenseManagement', 'guard_name' => 'web'],
            ['name' => 'expense.index', 'group_name' => 'ExpenseManagement', 'guard_name' => 'web'],
            ['name' => 'expense.create', 'group_name' => 'ExpenseManagement', 'guard_name' => 'web'],
            ['name' => 'expense.edit', 'group_name' => 'ExpenseManagement', 'guard_name' => 'web'],
            ['name' => 'expense.destroy', 'group_name' => 'ExpenseManagement', 'guard_name' => 'web'],
            ['name' => 'expense.trash', 'group_name' => 'ExpenseManagement', 'guard_name' => 'web'],
            ['name' => 'expense.restore', 'group_name' => 'ExpenseManagement', 'guard_name' => 'web'],
            ['name' => 'expense.delete', 'group_name' => 'ExpenseManagement', 'guard_name' => 'web'],
            ['name' => 'expense.status', 'group_name' => 'ExpenseManagement', 'guard_name' => 'web'],
            // SettingManagement
            ['name' => 'SettingMenu', 'group_name' => 'SettingManagement', 'guard_name' => 'web'],
            ['name' => 'site.setting', 'group_name' => 'SettingManagement', 'guard_name' => 'web'],
            ['name' => 'default.setting', 'group_name' => 'SettingManagement', 'guard_name' => 'web'],
            ['name' => 'seo.setting', 'group_name' => 'SettingManagement', 'guard_name' => 'web'],
            ['name' => 'mail.setting', 'group_name' => 'SettingManagement', 'guard_name' => 'web'],
            ['name' => 'sms.setting', 'group_name' => 'SettingManagement', 'guard_name' => 'web'],
            ['name' => 'captcha.setting', 'group_name' => 'SettingManagement', 'guard_name' => 'web'],
            // AccountsStatementManagement
            ['name' => 'AccountsStatementMenu', 'group_name' => 'AccountsStatementManagement', 'guard_name' => 'web'],
            ['name' => 'earnings.statement', 'group_name' => 'AccountsStatementManagement', 'guard_name' => 'web'],
            ['name' => 'expenses.statement', 'group_name' => 'AccountsStatementManagement', 'guard_name' => 'web'],
            // TopListManagement
            ['name' => 'TopListMenu', 'group_name' => 'TopListManagement', 'guard_name' => 'web'],
            ['name' => 'top.deposit.user', 'group_name' => 'TopListManagement', 'guard_name' => 'web'],
            ['name' => 'top.withdraw.user', 'group_name' => 'TopListManagement', 'guard_name' => 'web'],
            ['name' => 'top.posted_task.user', 'group_name' => 'TopListManagement', 'guard_name' => 'web'],
            ['name' => 'top.worked_task.user', 'group_name' => 'TopListManagement', 'guard_name' => 'web'],
            ['name' => 'top.referred.user', 'group_name' => 'TopListManagement', 'guard_name' => 'web'],
            // ReportListManagement
            ['name' => 'ReportListMenu', 'group_name' => 'ReportListManagement', 'guard_name' => 'web'],
            ['name' => 'deposit.report', 'group_name' => 'ReportListManagement', 'guard_name' => 'web'],
            ['name' => 'withdraw.report', 'group_name' => 'ReportListManagement', 'guard_name' => 'web'],
            ['name' => 'posted_task.report', 'group_name' => 'ReportListManagement', 'guard_name' => 'web'],
            ['name' => 'worked_task.report', 'group_name' => 'ReportListManagement', 'guard_name' => 'web'],
            // EmployeeManagement
            ['name' => 'EmployeeMenu', 'group_name' => 'EmployeeManagement', 'guard_name' => 'web'],
            ['name' => 'employee.index', 'group_name' => 'EmployeeManagement', 'guard_name' => 'web'],
            ['name' => 'employee.inactive', 'group_name' => 'EmployeeManagement', 'guard_name' => 'web'],
            ['name' => 'employee.create', 'group_name' => 'EmployeeManagement', 'guard_name' => 'web'],
            ['name' => 'employee.edit', 'group_name' => 'EmployeeManagement', 'guard_name' => 'web'],
            ['name' => 'employee.destroy', 'group_name' => 'EmployeeManagement', 'guard_name' => 'web'],
            ['name' => 'employee.trash', 'group_name' => 'EmployeeManagement', 'guard_name' => 'web'],
            ['name' => 'employee.restore', 'group_name' => 'EmployeeManagement', 'guard_name' => 'web'],
            ['name' => 'employee.delete', 'group_name' => 'EmployeeManagement', 'guard_name' => 'web'],
            ['name' => 'employee.status', 'group_name' => 'EmployeeManagement', 'guard_name' => 'web'],
            // UserManagement
            ['name' => 'UserMenu', 'group_name' => 'UserManagement', 'guard_name' => 'web'],
            ['name' => 'user.active', 'group_name' => 'UserManagement', 'guard_name' => 'web'],
            ['name' => 'user.inactive', 'group_name' => 'UserManagement', 'guard_name' => 'web'],
            ['name' => 'user.blocked', 'group_name' => 'UserManagement', 'guard_name' => 'web'],
            ['name' => 'user.banned', 'group_name' => 'UserManagement', 'guard_name' => 'web'],
            ['name' => 'user.status', 'group_name' => 'UserManagement', 'guard_name' => 'web'],
            ['name' => 'user.device', 'group_name' => 'UserManagement', 'guard_name' => 'web'],
            ['name' => 'user.destroy', 'group_name' => 'UserManagement', 'guard_name' => 'web'],
            ['name' => 'user.trash', 'group_name' => 'UserManagement', 'guard_name' => 'web'],
            ['name' => 'user.restore', 'group_name' => 'UserManagement', 'guard_name' => 'web'],
            ['name' => 'user.delete', 'group_name' => 'UserManagement', 'guard_name' => 'web'],
            // CategoryManagement
            ['name' => 'CategoryMenu', 'group_name' => 'CategoryManagement', 'guard_name' => 'web'],
            ['name' => 'category.index', 'group_name' => 'CategoryManagement', 'guard_name' => 'web'],
            ['name' => 'category.create', 'group_name' => 'CategoryManagement', 'guard_name' => 'web'],
            ['name' => 'category.edit', 'group_name' => 'CategoryManagement', 'guard_name' => 'web'],
            ['name' => 'category.destroy', 'group_name' => 'CategoryManagement', 'guard_name' => 'web'],
            ['name' => 'category.trash', 'group_name' => 'CategoryManagement', 'guard_name' => 'web'],
            ['name' => 'category.restore', 'group_name' => 'CategoryManagement', 'guard_name' => 'web'],
            ['name' => 'category.delete', 'group_name' => 'CategoryManagement', 'guard_name' => 'web'],
            ['name' => 'category.status', 'group_name' => 'CategoryManagement', 'guard_name' => 'web'],
            ['name' => 'sub_category.index', 'group_name' => 'CategoryManagement', 'guard_name' => 'web'],
            ['name' => 'sub_category.create', 'group_name' => 'CategoryManagement', 'guard_name' => 'web'],
            ['name' => 'sub_category.edit', 'group_name' => 'CategoryManagement', 'guard_name' => 'web'],
            ['name' => 'sub_category.destroy', 'group_name' => 'CategoryManagement', 'guard_name' => 'web'],
            ['name' => 'sub_category.trash', 'group_name' => 'CategoryManagement', 'guard_name' => 'web'],
            ['name' => 'sub_category.restore', 'group_name' => 'CategoryManagement', 'guard_name' => 'web'],
            ['name' => 'sub_category.delete', 'group_name' => 'CategoryManagement', 'guard_name' => 'web'],
            ['name' => 'sub_category.status', 'group_name' => 'CategoryManagement', 'guard_name' => 'web'],
            ['name' => 'child_category.index', 'group_name' => 'CategoryManagement', 'guard_name' => 'web'],
            ['name' => 'child_category.create', 'group_name' => 'CategoryManagement', 'guard_name' => 'web'],
            ['name' => 'child_category.edit', 'group_name' => 'CategoryManagement', 'guard_name' => 'web'],
            ['name' => 'child_category.destroy', 'group_name' => 'CategoryManagement', 'guard_name' => 'web'],
            ['name' => 'child_category.trash', 'group_name' => 'CategoryManagement', 'guard_name' => 'web'],
            ['name' => 'child_category.restore', 'group_name' => 'CategoryManagement', 'guard_name' => 'web'],
            ['name' => 'child_category.delete', 'group_name' => 'CategoryManagement', 'guard_name' => 'web'],
            ['name' => 'child_category.status', 'group_name' => 'CategoryManagement', 'guard_name' => 'web'],
            // Notification
            ['name' => 'SendNotificationMenu', 'group_name' => 'SendNotificationManagement', 'guard_name' => 'web'],
            ['name' => 'send.notification', 'group_name' => 'SendNotificationManagement', 'guard_name' => 'web'],
            ['name' => 'send.notification.store', 'group_name' => 'SendNotificationManagement', 'guard_name' => 'web'],
            // TaskPostChargeManagement
            ['name' => 'TaskPostChargeMenu', 'group_name' => 'TaskPostChargeManagement', 'guard_name' => 'web'],
            ['name' => 'task_post_charge.index', 'group_name' => 'TaskPostChargeManagement', 'guard_name' => 'web'],
            ['name' => 'task_post_charge.create', 'group_name' => 'TaskPostChargeManagement', 'guard_name' => 'web'],
            ['name' => 'task_post_charge.edit', 'group_name' => 'TaskPostChargeManagement', 'guard_name' => 'web'],
            ['name' => 'task_post_charge.destroy', 'group_name' => 'TaskPostChargeManagement', 'guard_name' => 'web'],
            ['name' => 'task_post_charge.trash', 'group_name' => 'TaskPostChargeManagement', 'guard_name' => 'web'],
            ['name' => 'task_post_charge.restore', 'group_name' => 'TaskPostChargeManagement', 'guard_name' => 'web'],
            ['name' => 'task_post_charge.delete', 'group_name' => 'TaskPostChargeManagement', 'guard_name' => 'web'],
            ['name' => 'task_post_charge.status', 'group_name' => 'TaskPostChargeManagement', 'guard_name' => 'web'],
            // FaqManagement
            ['name' => 'FaqMenu', 'group_name' => 'FaqManagement', 'guard_name' => 'web'],
            ['name' => 'faq.index', 'group_name' => 'FaqManagement', 'guard_name' => 'web'],
            ['name' => 'faq.create', 'group_name' => 'FaqManagement', 'guard_name' => 'web'],
            ['name' => 'faq.edit', 'group_name' => 'FaqManagement', 'guard_name' => 'web'],
            ['name' => 'faq.destroy', 'group_name' => 'FaqManagement', 'guard_name' => 'web'],
            ['name' => 'faq.trash', 'group_name' => 'FaqManagement', 'guard_name' => 'web'],
            ['name' => 'faq.restore', 'group_name' => 'FaqManagement', 'guard_name' => 'web'],
            ['name' => 'faq.delete', 'group_name' => 'FaqManagement', 'guard_name' => 'web'],
            ['name' => 'faq.status', 'group_name' => 'FaqManagement', 'guard_name' => 'web'],
            // TestimonialManagement
            ['name' => 'TestimonialMenu', 'group_name' => 'TestimonialManagement', 'guard_name' => 'web'],
            ['name' => 'testimonial.index', 'group_name' => 'TestimonialManagement', 'guard_name' => 'web'],
            ['name' => 'testimonial.create', 'group_name' => 'TestimonialManagement', 'guard_name' => 'web'],
            ['name' => 'testimonial.edit', 'group_name' => 'TestimonialManagement', 'guard_name' => 'web'],
            ['name' => 'testimonial.destroy', 'group_name' => 'TestimonialManagement', 'guard_name' => 'web'],
            ['name' => 'testimonial.trash', 'group_name' => 'TestimonialManagement', 'guard_name' => 'web'],
            ['name' => 'testimonial.restore', 'group_name' => 'TestimonialManagement', 'guard_name' => 'web'],
            ['name' => 'testimonial.delete', 'group_name' => 'TestimonialManagement', 'guard_name' => 'web'],
            ['name' => 'testimonial.status', 'group_name' => 'TestimonialManagement', 'guard_name' => 'web'],
            // VerificationManagement
            ['name' => 'VerificationMenu', 'group_name' => 'VerificationManagement', 'guard_name' => 'web'],
            ['name' => 'verification.request', 'group_name' => 'VerificationManagement', 'guard_name' => 'web'],
            ['name' => 'verification.request.check', 'group_name' => 'VerificationManagement', 'guard_name' => 'web'],
            ['name' => 'verification.request.rejected', 'group_name' => 'VerificationManagement', 'guard_name' => 'web'],
            ['name' => 'verification.request.approved', 'group_name' => 'VerificationManagement', 'guard_name' => 'web'],
            ['name' => 'verification.request.delete', 'group_name' => 'VerificationManagement', 'guard_name' => 'web'],
            // DepositManagement
            ['name' => 'DepositMenu', 'group_name' => 'DepositManagement', 'guard_name' => 'web'],
            ['name' => 'deposit.request', 'group_name' => 'DepositManagement', 'guard_name' => 'web'],
            ['name' => 'deposit.request.store', 'group_name' => 'DepositManagement', 'guard_name' => 'web'],
            ['name' => 'deposit.request.check', 'group_name' => 'DepositManagement', 'guard_name' => 'web'],
            ['name' => 'deposit.request.rejected', 'group_name' => 'DepositManagement', 'guard_name' => 'web'],
            ['name' => 'deposit.request.approved', 'group_name' => 'DepositManagement', 'guard_name' => 'web'],
            // WithdrawManagement
            ['name' => 'WithdrawMenu', 'group_name' => 'WithdrawManagement', 'guard_name' => 'web'],
            ['name' => 'withdraw.request', 'group_name' => 'WithdrawManagement', 'guard_name' => 'web'],
            ['name' => 'withdraw.request.store', 'group_name' => 'WithdrawManagement', 'guard_name' => 'web'],
            ['name' => 'withdraw.request.check', 'group_name' => 'WithdrawManagement', 'guard_name' => 'web'],
            ['name' => 'withdraw.request.rejected', 'group_name' => 'WithdrawManagement', 'guard_name' => 'web'],
            ['name' => 'withdraw.request.approved', 'group_name' => 'WithdrawManagement', 'guard_name' => 'web'],
            // BalanceTransferManagement
            ['name' => 'BalanceTransferMenu', 'group_name' => 'BalanceTransferManagement', 'guard_name' => 'web'],
            ['name' => 'balance.transfer.history', 'group_name' => 'BalanceTransferManagement', 'guard_name' => 'web'],
            ['name' => 'balance.transfer.store', 'group_name' => 'BalanceTransferManagement', 'guard_name' => 'web'],
            // Bonus
            ['name' => 'BonusMenu', 'group_name' => 'BonusManagement', 'guard_name' => 'web'],
            ['name' => 'bonus.history', 'group_name' => 'BonusManagement', 'guard_name' => 'web'],
            ['name' => 'bonus.store', 'group_name' => 'BonusManagement', 'guard_name' => 'web'],
            // PostedTaskManagement
            ['name' => 'PostedTaskMenu', 'group_name' => 'PostedTaskManagement', 'guard_name' => 'web'],
            ['name' => 'posted_task_list.pending', 'group_name' => 'PostedTaskManagement', 'guard_name' => 'web'],
            ['name' => 'posted_task_list.rejected', 'group_name' => 'PostedTaskManagement', 'guard_name' => 'web'],
            ['name' => 'posted_task_list.running', 'group_name' => 'PostedTaskManagement', 'guard_name' => 'web'],
            ['name' => 'posted_task_list.canceled', 'group_name' => 'PostedTaskManagement', 'guard_name' => 'web'],
            ['name' => 'posted_task_list.paused', 'group_name' => 'PostedTaskManagement', 'guard_name' => 'web'],
            ['name' => 'posted_task_list.completed', 'group_name' => 'PostedTaskManagement', 'guard_name' => 'web'],
            ['name' => 'posted_task.update', 'group_name' => 'PostedTaskManagement', 'guard_name' => 'web'],
            ['name' => 'posted_task.canceled', 'group_name' => 'PostedTaskManagement', 'guard_name' => 'web'],
            ['name' => 'posted_task.paused.resume', 'group_name' => 'PostedTaskManagement', 'guard_name' => 'web'],
            // WorkedTaskManagement
            ['name' => 'WorkedTaskMenu', 'group_name' => 'WorkedTaskManagement', 'guard_name' => 'web'],
            ['name' => 'worked_task_list.pending', 'group_name' => 'WorkedTaskManagement', 'guard_name' => 'web'],
            ['name' => 'worked_task_list.approved-rejected', 'group_name' => 'WorkedTaskManagement', 'guard_name' => 'web'],
            ['name' => 'worked_task_list.reviewed', 'group_name' => 'WorkedTaskManagement', 'guard_name' => 'web'],
            ['name' => 'worked_task.proof_check', 'group_name' => 'WorkedTaskManagement', 'guard_name' => 'web'],
            ['name' => 'worked_task.reviewed_check', 'group_name' => 'WorkedTaskManagement', 'guard_name' => 'web'],
            // ReportManagement
            ['name' => 'ReportMenu', 'group_name' => 'ReportManagement', 'guard_name' => 'web'],
            ['name' => 'report.pending', 'group_name' => 'ReportManagement', 'guard_name' => 'web'],
            ['name' => 'report.check', 'group_name' => 'ReportManagement', 'guard_name' => 'web'],
            ['name' => 'report.false', 'group_name' => 'ReportManagement', 'guard_name' => 'web'],
            ['name' => 'report.received', 'group_name' => 'ReportManagement', 'guard_name' => 'web'],
            // SupportManagement
            ['name' => 'SupportMenu', 'group_name' => 'SupportManagement', 'guard_name' => 'web'],
            ['name' => 'support.index', 'group_name' => 'SupportManagement', 'guard_name' => 'web'],
            ['name' => 'support.reply', 'group_name' => 'SupportManagement', 'guard_name' => 'web'],
            // ContactManagement
            ['name' => 'ContactMenu', 'group_name' => 'ContactManagement', 'guard_name' => 'web'],
            ['name' => 'contact.unread', 'group_name' => 'ContactManagement', 'guard_name' => 'web'],
            ['name' => 'contact.read', 'group_name' => 'ContactManagement', 'guard_name' => 'web'],
            ['name' => 'contact.delete', 'group_name' => 'ContactManagement', 'guard_name' => 'web'],
            // SubscriberManagement
            ['name' => 'SubscriberMenu', 'group_name' => 'SubscriberManagement', 'guard_name' => 'web'],
            ['name' => 'subscriber.index', 'group_name' => 'SubscriberManagement', 'guard_name' => 'web'],
            ['name' => 'subscriber.delete', 'group_name' => 'SubscriberManagement', 'guard_name' => 'web'],
            ['name' => 'subscriber.newsletter', 'group_name' => 'SubscriberManagement', 'guard_name' => 'web'],
            ['name' => 'subscriber.newsletter.send', 'group_name' => 'SubscriberManagement', 'guard_name' => 'web'],
            ['name' => 'subscriber.newsletter.delete', 'group_name' => 'SubscriberManagement', 'guard_name' => 'web'],
        ]);

        $this->command->info('Permissions added successfully.');

        return;
    }
}
