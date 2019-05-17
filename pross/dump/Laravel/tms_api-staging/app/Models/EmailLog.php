<?php 
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EmailLog extends Model {

    protected $fillable = ["subject", "	recipient_to", "recipient_cc",'status'];
    public $table = 'email_log';
}