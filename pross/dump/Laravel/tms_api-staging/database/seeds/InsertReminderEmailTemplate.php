<?php

use Illuminate\Database\Seeder;

class InsertReminderEmailTemplate extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //
    	DB::table('email_templates')->insert([
	        [
	            'body'  => '<div style="border: 1px;">

	            <p style="font-size: 14px;">Dear Course Participants</p>
	        
	            <p style="font-size: 14px;"><strong style="font-size: 15px;">Course Reminder :&nbsp;</strong> {TITLE}</p>
	        
	            <p style="font-size: 14px;">This is an automated reminder from SgWA to you to attend the abovementioned course.</p>
	        
	            <p style="font-size: 14px;">
	                <br>
	            </p><strong>Course Details:</strong></div>
	        
	        <p>
	            <br>
	        </p>
	        <div style="border: 1px;">
	            <div>
	        
	                <table border="1" cellpadding="5" cellspacing="0">
	                    <tbody>
	                        <tr>
	                            <td style="font-weight: bold;" width="90px">Course Title</td>
	                            <td>{TITLE}</td>
	                        </tr>
	                        <tr>
	                            <td style="font-weight: bold;">Course Date</td>
	                            <td>{START_DATE} to {END_DATE}
	        
	                                <table style="border-collapse: collapse; width: 50%; text-align: center;" border="1" cellspacing="0" cellpadding="0"></table>
	                            </td>
	                        </tr>
	                        <tr>
	                            <td style="font-weight: bold;">Assessment Date</td>
	                            <td>{ASSESSMENT_DATE}</td>
	                        </tr>
	                        <tr>
	                            <td style="font-weight: bold;">Course Venue</td>
	                            <td>{VENUE}</td>
	                        </tr>
	                        <tr>
	                            <td style="vertical-align: top;">Participants List</td>
	                            <td>{PARTICIPANTS}
	        
	                                <p>Note: Supervisors are to inform those participants without emails.</p>
	                            </td>
	                        </tr>
	                    </tbody>
	                </table>
	        
	                <p style="margin-top: 30px;">Please contact {ADMIN_NAME} at email {ADMIN_EMAIL}, should you require further assistance.</p>
	        
	                <p style="margin-top: 30px;">Thank you.</p>
	            </div>
	        </div>',
	            'subject'   => "TRAINING REMINDER - COURSE REMINDER NOTIFICATION - {COURSE_TITLE} {START_DATE}",
	            'type'      => "3",
	            'created_at'=> date('Y-m-d H:i:s')
	        ]
    	]);
    }
}
