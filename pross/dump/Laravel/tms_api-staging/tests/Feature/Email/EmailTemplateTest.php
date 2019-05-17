<?php

use Laravel\Lumen\Testing\DatabaseMigrations;
use Laravel\Lumen\Testing\DatabaseTransactions;
use App\User;
use App\Models\EmailTemplate;

class EmailTemplateTest extends ListTestCase
{
    use DatabaseTransactions;

    public function test_email_detail_and_update_data() {
    	
    	$email_template = EmailTemplate::inRandomOrder()->first();

    	$this->json('GET','/email-template/'.$email_template->id, [], $this->getAuthHeader());
        $this->assertResponseStatus(200);

        $this->json('PUT','/email-template/'.$email_template->id, [
        	'body' => 'sdsdsd',
        	'subject' => 'sdsjhdkjw'
        ], $this->getAuthHeader());

        $this->assertResponseStatus(200);

        $this->json('PUT','/email-template/'.$email_template->id, [], $this->getAuthHeader());
        $this->assertResponseStatus(422);
    }
}