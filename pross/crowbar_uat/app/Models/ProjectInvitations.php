<?php

    namespace Models; 

    use Illuminate\Database\Eloquent\Model;
    use Illuminate\Support\Facades\DB;
    use Illuminate\Support\Facades\Crypt;
    use Illuminate\Support\Facades\Mail;

    class ProjectInvitations extends Model{
        protected $table = 'project_invitation';
        protected $primaryKey = 'id_invitation';
        
        const CREATED_AT = 'created';
        const UPDATED_AT = 'updated';

        protected $fillable = [];

        protected $hidden = [];

        public function __construct() {
            
        }

        /**
         * [This method is used to send] 
         * @param [Integer]$employer_id[Used for employer id]
         * @param [String]$data[Used for dat]
         * @return Boolean
         */ 

        public static function send($employer_id, $data){
            $table_project_invitation    = \DB::table('project_invitation');
            $isInserted = $table_project_invitation->insert($data);

            if(!empty($isInserted)){
                $isNotified = \Models\Notifications::notify(
                    $data['talent_id'],
                    $employer_id,
                    'JOB_INVITATION_SENT_BY_EMPLOYER',
                    json_encode([
                        "talent_id" => (string) $data['talent_id'],
                        "employer_id" => (string) $employer_id,
                        "project_id" => (string) $data['project_id']
                    ])
                );

                return true;
            }else{
                return false;
            }
        }

        /**
         * [This method is used to findById] 
         * @param [Integer]$project_id [Used for project id]
         * @param [Integer]$talent_id[Used for user's id]
         * @return Data Response
         */ 

        public static function findById($project_id, $talent_id){
            $table_project_invitation    = \DB::table('project_invitation');
            $table_project_invitation->select([
                'project_invitation.message',
                'project_invitation.created',
            ]);
            $table_project_invitation->where('talent_id','=',$talent_id);
            $table_project_invitation->where('project_id','=',$project_id);
            $table_project_invitation->orderBy('id_invitation','DESC');

            return $table_project_invitation->get()->first();
        }
    }