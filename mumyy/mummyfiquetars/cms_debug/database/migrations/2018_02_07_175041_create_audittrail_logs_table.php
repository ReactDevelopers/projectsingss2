<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAuditTrailLogsTable extends Migration
{
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('audittrail__logs', function(Blueprint $table) {
			$table->engine = 'InnoDB';
            $table->increments('id');
			$table->unsignedInteger('entity_id')->nullable();
			$table->string('entity_type')->nullable();
			$table->string('event_name');
			$table->string('title')->nullable();
			$table->text('description')->nullable();
			$table->longText('old_data')->nullable();
			$table->unsignedInteger('performed_user_id')->nullable();
            $table->softDeletes();
            $table->timestamps(); 
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('audittrail__logs');
	}
}
