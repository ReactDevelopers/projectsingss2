<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Modules\Portfolio\Entities\PortfolioMedia;
use Modules\Vendor\Entities\Vendor;
use Modules\Vendor\Entities\VendorProfile;
use Modules\Vendor\Services\VendorService;

class UpdateVendorSocialLink extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'vendor:update:sociallink';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update vendor social linkl';

    /**
     * @var vendorService
     */
    private $vendorService;

    /**
     * @var VendorPhoneRepository
     */
    private $vendorPhoneRepository;

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct(vendorService $vendorService)
    {
        parent::__construct();
        $this->vendorService = $vendorService;
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $vendors = $this->vendorService->all();

        foreach ($vendors as $key => $vendor) {
            $vendorProfile = $vendor->vendorProfile;
            if($vendorProfile){
                $social_media_link = $vendorProfile->social_media_link;

                if(!$this->checkJson($social_media_link)){
                    $social_media_link = json_encode([
                        'facebook' => "",
                        'twitter' => "",
                        'instagram' => "",
                        'pinterest' => "",
                    ]);
                    $vendorProfile->social_media_link = '{\"facebook\":\"\",\"twitter\":\"\",\"instagram\":\"\",\"pinterest\":\"\"}';//json_encode($social_media_link);
                    $vendorProfile->save();

                    echo $vendor->id;
                    echo "\r\n";
                }
            }
        }
        echo "done!!!";
        echo "\r\n";
    }

    public function checkjson($str = false){
        if(!$str){
            return false;
        }

        $decode = json_decode($str);

        if(!$decode){
            return false;
        }

        return true;
    }
}
