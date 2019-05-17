<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddQuestionTypeColumnToMmQuestionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('mm__questions', function (Blueprint $table) {
            $table->tinyInteger('question_type')->after('anwsers_type')->default(1);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('mm__questions', function (Blueprint $table) {
            $table->dropColumn(['question_type']); 
        });
    }
}
