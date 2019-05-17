<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddColumnFieldToUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        /*        
        -- ----------------------------
        --  Table structure for `users`
        -- ----------------------------
        DROP TABLE IF EXISTS `users`;
        CREATE TABLE `users` (
          `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
          `email` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
          `password` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
          `permissions` text COLLATE utf8_unicode_ci,
          `last_login` timestamp NULL DEFAULT NULL,
          `first_name` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
          `last_name` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
          `remember_token` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
          `facebook_id` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
          `google_id` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
          `is_verified` tinyint(4) DEFAULT NULL,
          `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
          `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
          `status` tinyint(4) DEFAULT NULL,
          `is_deleted` tinyint(4) DEFAULT NULL,
          PRIMARY KEY (`id`),
          UNIQUE KEY `users_email_unique` (`email`)
        ) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
        */
        if (!Schema::hasColumn('users', 'is_deleted')){
            Schema::table('users', function (Blueprint $table) {
                $table->tinyInteger('is_deleted')->nullable()->after('updated_at');
            });
        }
        if (!Schema::hasColumn('users', 'status')){
            Schema::table('users', function (Blueprint $table) {
                $table->tinyInteger('status')->nullable()->after('updated_at');
            });
        }
        if (!Schema::hasColumn('users', 'is_verified')){
            Schema::table('users', function (Blueprint $table) {
                $table->tinyInteger('is_verified')->nullable()->after('last_name');
            });
        }
        if (!Schema::hasColumn('users', 'google_id')){
            Schema::table('users', function (Blueprint $table) {
                $table->string('google_id')->nullable()->after('last_name');
            });
        }
        if (!Schema::hasColumn('users', 'facebook_id')){
            Schema::table('users', function (Blueprint $table) {
                $table->string('facebook_id')->nullable()->after('last_name');
            });
        }
        if (!Schema::hasColumn('users', 'remember_token')){
            Schema::table('users', function (Blueprint $table) {
                $table->string('remember_token')->nullable()->after('last_name');
            });
        }
        Schema::table('users', function (Blueprint $table) {
            // $table->string('name')->nullable()->change();
            $table->string('email')->nullable()->change();
            $table->string('password')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['is_deleted', 'status', 'is_verified', 'google_id', 'facebook_id', 'remember_token']);
        });
    }
}
