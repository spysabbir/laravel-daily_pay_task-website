<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('site_settings', function (Blueprint $table) {
            $table->id();
            $table->string('site_name');
            $table->string('site_tagline')->nullable();
            $table->text('site_description')->nullable();
            $table->string('site_url');
            $table->enum('site_timezone', ['UTC', 'Asia/Dhaka']);
            $table->enum('site_currency', ['USD', 'BDT'])->nullable();
            $table->enum('site_currency_symbol', ['$', '৳'])->nullable();
            $table->string('site_logo')->nullable();
            $table->string('site_favicon')->nullable();
            $table->string('site_main_email')->nullable();
            $table->string('site_support_email')->nullable();
            $table->string('site_main_phone')->nullable();
            $table->string('site_support_phone')->nullable();
            $table->string('site_address')->nullable();
            $table->text('site_notice')->nullable();
            $table->string('site_facebook_url')->nullable();
            $table->string('site_twitter_url')->nullable();
            $table->string('site_instagram_url')->nullable();
            $table->string('site_linkedin_url')->nullable();
            $table->string('site_pinterest_url')->nullable();
            $table->string('site_youtube_url')->nullable();
            $table->string('site_whatsapp_url')->nullable();
            $table->string('site_telegram_url')->nullable();
            $table->string('site_tiktok_url')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('site_settings');
    }
};
