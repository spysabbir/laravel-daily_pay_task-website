<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\CaptchaSetting;
use App\Models\DefaultSetting;
use App\Models\MailSetting;
use App\Models\SeoSetting;
use App\Models\SiteSetting;
use App\Models\SmsSetting;
use Illuminate\Http\Request;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;

class SettingController extends Controller implements HasMiddleware
{
    public static function middleware()
    {
        return [
            new Middleware(\Spatie\Permission\Middleware\PermissionMiddleware::using('default.setting') , only:['defaultSetting', 'defaultSettingUpdate']),
            new Middleware(\Spatie\Permission\Middleware\PermissionMiddleware::using('site.setting') , only:['siteSetting', 'siteSettingUpdate']),
            new Middleware(\Spatie\Permission\Middleware\PermissionMiddleware::using('seo.setting') , only:['seoSetting', 'seoSettingUpdate']),
            new Middleware(\Spatie\Permission\Middleware\PermissionMiddleware::using('mail.setting'), only:['mailSetting', 'mailSettingUpdate']),
            new Middleware(\Spatie\Permission\Middleware\PermissionMiddleware::using('sms.setting'), only:['smsSetting', 'smsSettingUpdate']),
            new Middleware(\Spatie\Permission\Middleware\PermissionMiddleware::using('captcha.setting'), only:['captchaSetting', 'captchaSettingUpdate']),
        ];
    }

    // Change Env Function
    public function changeEnv($envKey, $envValue)
    {
        $envFilePath = app()->environmentFilePath();
        $strEnv = file_get_contents($envFilePath);
        $strEnv.="\n";
        $keyStartPosition = strpos($strEnv, "{$envKey}=");
        $keyEndPosition = strpos($strEnv, "\n",$keyStartPosition);
        $oldLine = substr($strEnv, $keyStartPosition, $keyEndPosition-$keyStartPosition);

        if(!$keyStartPosition || !$keyEndPosition || !$oldLine){
            $strEnv.="{$envKey}={$envValue}\n";
        }else{
            $strEnv=str_replace($oldLine, "{$envKey}={$envValue}",$strEnv);
        }
        $strEnv=substr($strEnv, 0, -1);
        file_put_contents($envFilePath, $strEnv);
    }

    public function defaultSetting(){
        $defaultSetting = DefaultSetting::first();
        return view('backend.setting.default', compact('defaultSetting'));
    }

    public function defaultSettingUpdate(Request $request){
        $request->validate([
            'referral_registration_bonus_amount' => 'required',
            'referral_withdrawal_bonus_percentage' => 'required',
            'deposit_bkash_account' => ['required', 'regex:/^(?:\+8801|01)[3-9]\d{8}$/'], // Bangladeshi Phone Number
            'deposit_rocket_account' => ['required', 'regex:/^(?:\+8801|01)[3-9]\d{8}$/'],
            'deposit_nagad_account' => ['required', 'regex:/^(?:\+8801|01)[3-9]\d{8}$/'],
            'min_deposit_amount' => 'required',
            'max_deposit_amount' => 'required',
            'withdrawal_balance_deposit_charge_percentage' => 'required',
            'instant_withdraw_charge' => 'required',
            'withdraw_charge_percentage' => 'required',
            'min_withdraw_amount' => 'required',
            'max_withdraw_amount' => 'required',
            'task_posting_charge_percentage' => 'required',
            'task_posting_additional_required_proof_photo_charge' => 'required',
            'task_posting_boosting_time_charge' => 'required',
            'task_posting_additional_work_duration_charge' => 'required',
            'task_posting_min_budget' => 'required',
            'task_proof_max_bonus_amount' => 'required',
            'task_proof_monthly_free_review_time' => 'required',
            'task_proof_additional_review_charge' => 'required',
            'user_max_blocked_time' => 'required',
        ],
        [
            'deposit_bkash_account.regex' => 'The phone number must be a valid Bangladeshi number (+8801XXXXXXXX or 01XXXXXXXX).',
            'deposit_rocket_account.regex' => 'The phone number must be a valid Bangladeshi number (+8801XXXXXXXX or 01XXXXXXXX).',
            'deposit_nagad_account.regex' => 'The phone number must be a valid Bangladeshi number (+8801XXXXXXXX or 01XXXXXXXX).',
        ]);

        $defaultSetting = DefaultSetting::first();

        $defaultSetting->update([
            'referral_registration_bonus_amount' => $request->referral_registration_bonus_amount,
            'referral_withdrawal_bonus_percentage' => $request->referral_withdrawal_bonus_percentage,
            'deposit_bkash_account' => $request->deposit_bkash_account,
            'deposit_rocket_account' => $request->deposit_rocket_account,
            'deposit_nagad_account' => $request->deposit_nagad_account,
            'min_deposit_amount' => $request->min_deposit_amount,
            'max_deposit_amount' => $request->max_deposit_amount,
            'withdrawal_balance_deposit_charge_percentage' => $request->withdrawal_balance_deposit_charge_percentage,
            'instant_withdraw_charge' => $request->instant_withdraw_charge,
            'withdraw_charge_percentage' => $request->withdraw_charge_percentage,
            'min_withdraw_amount' => $request->min_withdraw_amount,
            'max_withdraw_amount' => $request->max_withdraw_amount,
            'task_posting_charge_percentage' => $request->task_posting_charge_percentage,
            'task_posting_additional_required_proof_photo_charge' => $request->task_posting_additional_required_proof_photo_charge,
            'task_posting_boosting_time_charge' => $request->task_posting_boosting_time_charge,
            'task_posting_additional_work_duration_charge' => $request->task_posting_additional_work_duration_charge,
            'task_posting_min_budget' => $request->task_posting_min_budget,
            'task_proof_max_bonus_amount' => $request->task_proof_max_bonus_amount,
            'task_proof_monthly_free_review_time' => $request->task_proof_monthly_free_review_time,
            'task_proof_additional_review_charge' => $request->task_proof_additional_review_charge,
            'user_max_blocked_time' => $request->user_max_blocked_time,
        ]);

        $notification = array(
            'message' => 'Default setting updated successfully.',
            'alert-type' => 'success'
        );

        return back()->with($notification);
    }

    public function siteSetting(){
        $siteSetting = SiteSetting::first();
        return view('backend.setting.site', compact('siteSetting'));
    }

    public function siteSettingUpdate(Request $request){
        $request->validate([
            'site_logo' => ['nullable', 'image', 'mimes:jpeg,png,jpg', 'max:2048'],
            'site_favicon' => ['nullable', 'image', 'mimes:jpeg,png,jpg', 'max:2048'],
            'site_name' => 'required|string|max:255',
            'site_url' => 'required|string|max:255|url',
            'site_tagline' => 'required|string|max:255',
            'site_description' => 'required|string',
            'site_timezone' => 'required|in:UTC,Asia/Dhaka',
            'site_currency' => 'required|in:USD,BDT',
            'site_currency_symbol' => 'required|in:$,৳',
            'site_main_email' => 'required|email|max:255|regex:/^[a-z0-9._%+-]+@[a-z0-9.-]+\.[a-z]{3}$/',
            'site_support_email' => 'required|email|max:255|regex:/^[a-z0-9._%+-]+@[a-z0-9.-]+\.[a-z]{3}$/',
            'site_main_phone' => ['required', 'string', 'regex:/^(?:\+8801|01)[3-9]\d{8}$/'],
            'site_support_phone' => ['required', 'string', 'regex:/^(?:\+8801|01)[3-9]\d{8}$/'],
            'site_address' => 'required|string|max:255',
            'site_notice' => 'nullable|string',
            'site_facebook_url' => 'nullable|url',
            'site_twitter_url' => 'nullable|url',
            'site_instagram_url' => 'nullable|url',
            'site_linkedin_url' => 'nullable|url',
            'site_pinterest_url' => 'nullable|url',
            'site_youtube_url' => 'nullable|url',
            'site_whatsapp_url' => 'nullable|url',
            'site_telegram_url' => 'nullable|url',
            'site_tiktok_url' => 'nullable|url',
        ], [
            'site_main_email.regex' => 'The email must follow the format "****@****.***".',
            'site_support_email.regex' => 'The email must follow the format "****@****.***".',
            'site_main_phone.regex' => 'The phone number must be a valid Bangladeshi number (+8801XXXXXXXX or 01XXXXXXXX).',
            'site_support_phone.regex' => 'The phone number must be a valid Bangladeshi number (+8801XXXXXXXX or 01XXXXXXXX).',
        ]);

        $siteSetting = SiteSetting::first();

        $siteSetting->update([
            'site_name' => $request->site_name,
            'site_tagline' => $request->site_tagline,
            'site_description' => $request->site_description,
            'site_url' => $request->site_url,
            'site_timezone' => $request->site_timezone,
            'site_currency' => $request->site_currency,
            'site_currency_symbol' => $request->site_currency_symbol,
            'site_main_phone' => $request->site_main_phone,
            'site_support_phone' => $request->site_support_phone,
            'site_main_email' => $request->site_main_email,
            'site_support_email' => $request->site_support_email,
            'site_address' => $request->site_address,
            'site_notice' => $request->site_notice,
            'site_facebook_url' => $request->site_facebook_url,
            'site_twitter_url' => $request->site_twitter_url,
            'site_instagram_url' => $request->site_instagram_url,
            'site_linkedin_url' => $request->site_linkedin_url,
            'site_pinterest_url' => $request->site_pinterest_url,
            'site_youtube_url' => $request->site_youtube_url,
            'site_whatsapp_url' => $request->site_whatsapp_url,
            'site_telegram_url' => $request->site_telegram_url,
            'site_tiktok_url' => $request->site_tiktok_url,
        ]);

        // Site Logo Upload
        if($request->hasFile('site_logo')){
            if($siteSetting->site_logo != 'default_site_logo.png'){
                unlink(base_path("public/uploads/setting_photo/").$siteSetting->site_logo);
            }

            $manager = new ImageManager(new Driver());
            $site_logo_name = "Site-Logo".".". $request->file('site_logo')->getClientOriginalExtension();
            $image = $manager->read($request->file('site_logo'));
            $image->scale(width: 100, height: 30);
            $image->toPng()->save(base_path("public/uploads/setting_photo/").$site_logo_name);
            $siteSetting->update([
                'site_logo' => $site_logo_name
            ]);
        }

        // Site Favicon Upload
        if($request->hasFile('site_favicon')){
            if($siteSetting->site_favicon != 'default_site_favicon.png'){
                unlink(base_path("public/uploads/setting_photo/").$siteSetting->site_favicon);
            }
            $manager = new ImageManager(new Driver());
            $site_favicon_name = "Site-Favicon".".". $request->file('site_favicon')->getClientOriginalExtension();
            $image = $manager->read($request->file('site_favicon'));
            $image->scale(width: 25, height: 25);
            $image->toPng()->save(base_path("public/uploads/setting_photo/").$site_favicon_name);
            $siteSetting->update([
                'site_favicon' => $site_favicon_name
            ]);
        }

        $this->changeEnv("APP_NAME", "'$request->site_name'");
        $this->changeEnv("APP_URL", "'$request->site_url'");
        $this->changeEnv("APP_TIMEZONE", "'$request->site_timezone'");

        $notification = array(
            'message' => 'Site setting updated successfully.',
            'alert-type' => 'success'
        );

        return back()->with($notification);
    }

    public function seoSetting(){
        $seoSetting = SeoSetting::first();
        return view('backend.setting.seo', compact('seoSetting'));
    }

    public function seoSettingUpdate(Request $request){
        $request->validate([
            'title' => 'required',
            'author' => 'required',
            'keywords' => 'required',
            'description' => 'required',
            'og_url' => 'required',
            'og_site_name' => 'required',
            'twitter_card' => 'required',
            'twitter_site' => 'required',
            'image' => ['nullable', 'image', 'mimes:jpeg,png,jpg,gif', 'max:2048'],
            'image_alt' => 'required',
        ]);

        $seoSetting = SeoSetting::first();
        $seoSetting->update([
            'title' => $request->title,
            'author' => $request->author,
            'keywords' => $request->keywords,
            'description' => $request->description,
            'og_url' => $request->og_url,
            'og_site_name' => $request->og_site_name,
            'twitter_card' => $request->twitter_card,
            'twitter_site' => $request->twitter_site,
            'image_alt' => $request->image_alt,
        ]);

        // Seo Image Upload
        if($request->hasFile('image')){
            if($seoSetting->image != 'default_seo_image.jpg'){
                unlink(base_path("public/uploads/setting_photo/").$seoSetting->image);
            }

            $manager = new ImageManager(new Driver());
            $seo_image_name = "Seo-Image".".". $request->file('image')->getClientOriginalExtension();
            $image = $manager->read($request->file('image'));
            $image->toJpeg(80)->save(base_path("public/uploads/setting_photo/").$seo_image_name);
            $seoSetting->update([
                'image' => $seo_image_name
            ]);
        }

        $notification = array(
            'message' => 'SEO setting updated successfully.',
            'alert-type' => 'success'
        );

        return back()->with($notification);
    }

    public function mailSetting(){
        $mailSetting = MailSetting::first();
        return view('backend.setting.mail', compact('mailSetting'));
    }

    public function mailSettingUpdate(Request $request){
        $request->validate([
            'mail_driver' => 'required',
            'mail_mailer' => 'required',
            'mail_host' => 'required',
            'mail_port' => 'required',
            'mail_username' => 'required',
            'mail_password' => 'required',
            'mail_encryption' => 'required',
            'mail_from_address' => 'required',
        ]);

        $this->changeEnv("MAIL_MAILER", "'$request->mail_mailer'");
        $this->changeEnv("MAIL_HOST", "'$request->mail_host'");
        $this->changeEnv("MAIL_PORT", "'$request->mail_port'");
        $this->changeEnv("MAIL_USERNAME", "'$request->mail_username'");
        $this->changeEnv("MAIL_PASSWORD", "'$request->mail_password'");
        $this->changeEnv("MAIL_ENCRYPTION", "'$request->mail_encryption'");
        $this->changeEnv("MAIL_FROM_ADDRESS", "'$request->mail_from_address'");

        $mailSetting = MailSetting::first();
        $mailSetting->update([
            'mail_driver' => $request->mail_driver,
            'mail_mailer' => $request->mail_mailer,
            'mail_host' => $request->mail_host,
            'mail_port' => $request->mail_port,
            'mail_username' => $request->mail_username,
            'mail_password' => $request->mail_password,
            'mail_encryption' => $request->mail_encryption,
            'mail_from_address' => $request->mail_from_address,
        ]);

        $notification = array(
            'message' => 'Mail setting updated successfully.',
            'alert-type' => 'success'
        );

        return back()->with($notification);
    }

    public function smsSetting(){
        $smsSetting = SmsSetting::first();
        return view('backend.setting.sms', compact('smsSetting'));
    }

    public function smsSettingUpdate(Request $request){
        $request->validate([
            'sms_driver' => 'required',
            'sms_api_key' => 'required',
            'sms_from' => 'required',
        ]);

        $smsSetting = SmsSetting::first();
        $smsSetting->update([
            'sms_driver' => $request->sms_driver,
            'sms_api_key' => $request->sms_api_key,
            'sms_from' => $request->sms_from,
        ]);

        $notification = array(
            'message' => 'SMS setting updated successfully.',
            'alert-type' => 'success'
        );

        return back()->with($notification);
    }

    public function captchaSetting(){
        $captchaSetting = CaptchaSetting::first();
        return view('backend.setting.captcha', compact('captchaSetting'));
    }

    public function captchaSettingUpdate(Request $request){
        $request->validate([
            'captcha_secret_key' => 'required',
            'captcha_site_key' => 'required',
        ]);

        $this->changeEnv("NOCAPTCHA_SECRET", "'$request->captcha_secret_key'");
        $this->changeEnv("NOCAPTCHA_SITEKEY", "'$request->captcha_site_key'");

        $captchaSetting = CaptchaSetting::first();
        $captchaSetting->update([
            'captcha_secret_key' => $request->captcha_secret_key,
            'captcha_site_key' => $request->captcha_site_key,
        ]);

        $notification = array(
            'message' => 'Captcha setting updated successfully.',
            'alert-type' => 'success'
        );

        return back()->with($notification);
    }
}
