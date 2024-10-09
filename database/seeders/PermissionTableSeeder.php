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
            ['name' => 'role-permission.create', 'group_name' => 'RolePermissionManagement', 'guard_name' => 'web'],
            ['name' => 'role-permission.edit', 'group_name' => 'RolePermissionManagement', 'guard_name' => 'web'],
            ['name' => 'role-permission.destroy', 'group_name' => 'RolePermissionManagement', 'guard_name' => 'web'],

            ['name' => 'SettingMenu', 'group_name' => 'SettingManagement', 'guard_name' => 'web'],
            ['name' => 'site.setting', 'group_name' => 'SettingManagement', 'guard_name' => 'web'],
            ['name' => 'default.setting', 'group_name' => 'SettingManagement', 'guard_name' => 'web'],
            ['name' => 'seo.setting', 'group_name' => 'SettingManagement', 'guard_name' => 'web'],
            ['name' => 'mail.setting', 'group_name' => 'SettingManagement', 'guard_name' => 'web'],
            ['name' => 'sms.setting', 'group_name' => 'SettingManagement', 'guard_name' => 'web'],
            ['name' => 'captcha.setting', 'group_name' => 'SettingManagement', 'guard_name' => 'web'],

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

            ['name' => 'UserMenu', 'group_name' => 'UserManagement', 'guard_name' => 'web'],
            ['name' => 'user.active', 'group_name' => 'UserManagement', 'guard_name' => 'web'],
            ['name' => 'user.edit', 'group_name' => 'UserManagement', 'guard_name' => 'web'],
            ['name' => 'user.destroy', 'group_name' => 'UserManagement', 'guard_name' => 'web'],
            ['name' => 'user.trash', 'group_name' => 'UserManagement', 'guard_name' => 'web'],
            ['name' => 'user.restore', 'group_name' => 'UserManagement', 'guard_name' => 'web'],
            ['name' => 'user.delete', 'group_name' => 'UserManagement', 'guard_name' => 'web'],
            ['name' => 'user.inactive', 'group_name' => 'UserManagement', 'guard_name' => 'web'],
            ['name' => 'user.blocked', 'group_name' => 'UserManagement', 'guard_name' => 'web'],
            ['name' => 'user.banned', 'group_name' => 'UserManagement', 'guard_name' => 'web'],

            ['name' => 'CategoryMenu', 'group_name' => 'CategoryManagement', 'guard_name' => 'web'],
            ['name' => 'category.index', 'group_name' => 'CategoryManagement', 'guard_name' => 'web'],
            ['name' => 'category.create', 'group_name' => 'CategoryManagement', 'guard_name' => 'web'],
            ['name' => 'category.edit', 'group_name' => 'CategoryManagement', 'guard_name' => 'web'],
            ['name' => 'category.destroy', 'group_name' => 'CategoryManagement', 'guard_name' => 'web'],
            ['name' => 'category.trash', 'group_name' => 'CategoryManagement', 'guard_name' => 'web'],
            ['name' => 'category.restore', 'group_name' => 'CategoryManagement', 'guard_name' => 'web'],
            ['name' => 'category.delete', 'group_name' => 'CategoryManagement', 'guard_name' => 'web'],

            ['name' => 'SubCategoryMenu', 'group_name' => 'SubCategoryManagement', 'guard_name' => 'web'],
            ['name' => 'sub_category.index', 'group_name' => 'SubCategoryManagement', 'guard_name' => 'web'],
            ['name' => 'sub_category.create', 'group_name' => 'SubCategoryManagement', 'guard_name' => 'web'],
            ['name' => 'sub_category.edit', 'group_name' => 'SubCategoryManagement', 'guard_name' => 'web'],
            ['name' => 'sub_category.destroy', 'group_name' => 'SubCategoryManagement', 'guard_name' => 'web'],
            ['name' => 'sub_category.trash', 'group_name' => 'SubCategoryManagement', 'guard_name' => 'web'],
            ['name' => 'sub_category.restore', 'group_name' => 'SubCategoryManagement', 'guard_name' => 'web'],
            ['name' => 'sub_category.delete', 'group_name' => 'SubCategoryManagement', 'guard_name' => 'web'],

            ['name' => 'ChildCategoryMenu', 'group_name' => 'ChildCategoryManagement', 'guard_name' => 'web'],
            ['name' => 'child_category.index', 'group_name' => 'ChildCategoryManagement', 'guard_name' => 'web'],
            ['name' => 'child_category.create', 'group_name' => 'ChildCategoryManagement', 'guard_name' => 'web'],
            ['name' => 'child_category.edit', 'group_name' => 'ChildCategoryManagement', 'guard_name' => 'web'],
            ['name' => 'child_category.destroy', 'group_name' => 'ChildCategoryManagement', 'guard_name' => 'web'],
            ['name' => 'child_category.trash', 'group_name' => 'ChildCategoryManagement', 'guard_name' => 'web'],
            ['name' => 'child_category.restore', 'group_name' => 'ChildCategoryManagement', 'guard_name' => 'web'],
            ['name' => 'child_category.delete', 'group_name' => 'ChildCategoryManagement', 'guard_name' => 'web'],

            ['name' => 'TaskPostChargeMenu', 'group_name' => 'TaskPostChargeManagement', 'guard_name' => 'web'],
            ['name' => 'task_post_charge.index', 'group_name' => 'TaskPostChargeManagement', 'guard_name' => 'web'],
            ['name' => 'task_post_charge.create', 'group_name' => 'TaskPostChargeManagement', 'guard_name' => 'web'],
            ['name' => 'task_post_charge.edit', 'group_name' => 'TaskPostChargeManagement', 'guard_name' => 'web'],
            ['name' => 'task_post_charge.destroy', 'group_name' => 'TaskPostChargeManagement', 'guard_name' => 'web'],
            ['name' => 'task_post_charge.trash', 'group_name' => 'TaskPostChargeManagement', 'guard_name' => 'web'],
            ['name' => 'task_post_charge.restore', 'group_name' => 'TaskPostChargeManagement', 'guard_name' => 'web'],
            ['name' => 'task_post_charge.delete', 'group_name' => 'TaskPostChargeManagement', 'guard_name' => 'web'],

            ['name' => 'FaqMenu', 'group_name' => 'FaqManagement', 'guard_name' => 'web'],
            ['name' => 'faq.index', 'group_name' => 'FaqManagement', 'guard_name' => 'web'],
            ['name' => 'faq.create', 'group_name' => 'FaqManagement', 'guard_name' => 'web'],
            ['name' => 'faq.edit', 'group_name' => 'FaqManagement', 'guard_name' => 'web'],
            ['name' => 'faq.destroy', 'group_name' => 'FaqManagement', 'guard_name' => 'web'],
            ['name' => 'faq.trash', 'group_name' => 'FaqManagement', 'guard_name' => 'web'],
            ['name' => 'faq.restore', 'group_name' => 'FaqManagement', 'guard_name' => 'web'],
            ['name' => 'faq.delete', 'group_name' => 'FaqManagement', 'guard_name' => 'web'],

            ['name' => 'TestimonialMenu', 'group_name' => 'TestimonialManagement', 'guard_name' => 'web'],
            ['name' => 'testimonial.index', 'group_name' => 'TestimonialManagement', 'guard_name' => 'web'],
            ['name' => 'testimonial.create', 'group_name' => 'TestimonialManagement', 'guard_name' => 'web'],
            ['name' => 'testimonial.edit', 'group_name' => 'TestimonialManagement', 'guard_name' => 'web'],
            ['name' => 'testimonial.destroy', 'group_name' => 'TestimonialManagement', 'guard_name' => 'web'],
            ['name' => 'testimonial.trash', 'group_name' => 'TestimonialManagement', 'guard_name' => 'web'],
            ['name' => 'testimonial.restore', 'group_name' => 'TestimonialManagement', 'guard_name' => 'web'],
            ['name' => 'testimonial.delete', 'group_name' => 'TestimonialManagement', 'guard_name' => 'web'],

            ['name' => 'VerificationMenu', 'group_name' => 'VerificationManagement', 'guard_name' => 'web'],
            ['name' => 'verification.request', 'group_name' => 'VerificationManagement', 'guard_name' => 'web'],
            ['name' => 'verification.request.show', 'group_name' => 'VerificationManagement', 'guard_name' => 'web'],
            ['name' => 'verification.request.status.change', 'group_name' => 'VerificationManagement', 'guard_name' => 'web'],
            ['name' => 'verification.request.rejected', 'group_name' => 'VerificationManagement', 'guard_name' => 'web'],
            ['name' => 'verification.request.approved', 'group_name' => 'VerificationManagement', 'guard_name' => 'web'],
            ['name' => 'verification.request.delete', 'group_name' => 'VerificationManagement', 'guard_name' => 'web'],

            ['name' => 'DepositMenu', 'group_name' => 'DepositManagement', 'guard_name' => 'web'],
            ['name' => 'deposit.request', 'group_name' => 'DepositManagement', 'guard_name' => 'web'],
            ['name' => 'deposit.request.show', 'group_name' => 'DepositManagement', 'guard_name' => 'web'],
            ['name' => 'deposit.request.status.change', 'group_name' => 'DepositManagement', 'guard_name' => 'web'],
            ['name' => 'deposit.request.rejected', 'group_name' => 'DepositManagement', 'guard_name' => 'web'],
            ['name' => 'deposit.request.approved', 'group_name' => 'DepositManagement', 'guard_name' => 'web'],
            ['name' => 'deposit.request.delete', 'group_name' => 'DepositManagement', 'guard_name' => 'web'],

            ['name' => 'WithdrawMenu', 'group_name' => 'WithdrawManagement', 'guard_name' => 'web'],
            ['name' => 'withdraw.request', 'group_name' => 'WithdrawManagement', 'guard_name' => 'web'],
            ['name' => 'withdraw.request.show', 'group_name' => 'WithdrawManagement', 'guard_name' => 'web'],
            ['name' => 'withdraw.request.status.change', 'group_name' => 'WithdrawManagement', 'guard_name' => 'web'],
            ['name' => 'withdraw.request.rejected', 'group_name' => 'WithdrawManagement', 'guard_name' => 'web'],
            ['name' => 'withdraw.request.approved', 'group_name' => 'WithdrawManagement', 'guard_name' => 'web'],
            ['name' => 'withdraw.request.delete', 'group_name' => 'WithdrawManagement', 'guard_name' => 'web'],

            ['name' => 'PostTaskMenu', 'group_name' => 'PostTaskManagement', 'guard_name' => 'web'],
            ['name' => 'post_task_list.pending', 'group_name' => 'PostTaskManagement', 'guard_name' => 'web'],
            ['name' => 'post_task_list.rejected', 'group_name' => 'PostTaskManagement', 'guard_name' => 'web'],
            ['name' => 'post_task_list.running', 'group_name' => 'PostTaskManagement', 'guard_name' => 'web'],
            ['name' => 'post_task_list.canceled', 'group_name' => 'PostTaskManagement', 'guard_name' => 'web'],
            ['name' => 'post_task_list.completed', 'group_name' => 'PostTaskManagement', 'guard_name' => 'web'],

            ['name' => 'ProofTaskMenu', 'group_name' => 'ProofTaskManagement', 'guard_name' => 'web'],
            ['name' => 'proof_task_list.pending', 'group_name' => 'ProofTaskManagement', 'guard_name' => 'web'],
            ['name' => 'proof_task_list.approved', 'group_name' => 'ProofTaskManagement', 'guard_name' => 'web'],
            ['name' => 'proof_task_list.rejected', 'group_name' => 'ProofTaskManagement', 'guard_name' => 'web'],
            ['name' => 'proof_task_list.reviewed', 'group_name' => 'ProofTaskManagement', 'guard_name' => 'web'],

            ['name' => 'ReportUserMenu', 'group_name' => 'ReportUserManagement', 'guard_name' => 'web'],
            ['name' => 'report_user.pending', 'group_name' => 'ReportUserManagement', 'guard_name' => 'web'],
            ['name' => 'report_user.resolved', 'group_name' => 'ReportUserManagement', 'guard_name' => 'web'],

            ['name' => 'SupportMenu', 'group_name' => 'SupportManagement', 'guard_name' => 'web'],
            ['name' => 'support', 'group_name' => 'SupportManagement', 'guard_name' => 'web'],

            ['name' => 'ContactMenu', 'group_name' => 'ContactManagement', 'guard_name' => 'web'],
            ['name' => 'contact', 'group_name' => 'ContactManagement', 'guard_name' => 'web'],

            ['name' => 'SubscriberMenu', 'group_name' => 'SubscriberManagement', 'guard_name' => 'web'],
            ['name' => 'subscriber.index', 'group_name' => 'SubscriberManagement', 'guard_name' => 'web'],
            ['name' => 'subscriber.delete', 'group_name' => 'SubscriberManagement', 'guard_name' => 'web'],
            ['name' => 'subscriber.newsletter', 'group_name' => 'SubscriberManagement', 'guard_name' => 'web'],
            ['name' => 'subscriber.newsletter.send', 'group_name' => 'SubscriberManagement', 'guard_name' => 'web'],
            ['name' => 'subscriber.newsletter.view', 'group_name' => 'SubscriberManagement', 'guard_name' => 'web'],
            ['name' => 'subscriber.newsletter.delete', 'group_name' => 'SubscriberManagement', 'guard_name' => 'web'],
        ]);

        $this->command->info('Permissions added successfully.');

        return;
    }
}
