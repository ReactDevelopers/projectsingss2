<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterMimeTypeFieldLength extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Have to drop column because according to docs we cannot rename a table
        // that has a column type of 'enum' in it.
        // Ref: https://laravel.com/docs/5.7/migrations#renaming-and-dropping-tables
        Schema::table('uploads', function (Blueprint $table) {
            if (Schema::hasColumn('uploads', 'mime_type')) {
                $table->dropColumn('mime_type');
            }
        });
        Schema::table('uploads', function (Blueprint $table) {
            $table->string('mime_type', 255)->nullable()->default(null)->after('filepath');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
}
