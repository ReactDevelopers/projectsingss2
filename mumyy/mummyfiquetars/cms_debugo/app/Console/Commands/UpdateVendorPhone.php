<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Modules\Portfolio\Entities\PortfolioMedia;
use Modules\Vendor\Entities\Vendor;
use Modules\Vendor\Entities\VendorPhone;
use Modules\Vendor\Services\VendorService;
use Modules\Vendor\Repositories\VendorPhoneRepository;

class UpdateVendorPhone extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'vendor:update:phone';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update vendor phone';

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
    public function __construct(vendorService $vendorService, VendorPhoneRepository $vendorPhoneRepository)
    {
        parent::__construct();
        $this->vendorService = $vendorService;
        $this->vendorPhoneRepository = $vendorPhoneRepository;
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
            $vendorPhone = $vendor->vendorPhone->first();
            $business_phone = $vendor->vendorProfile ? $vendor->vendorProfile->business_phone : false;
            if(!$vendorPhone && $business_phone){
                $data = [
                    'phone_number' => $vendor->vendorProfile->business_phone,
                    'country_code' => '65',
                    'is_primary' => 1,
                    'is_verifyed' => 1,
                    'status' => 1,
                    'is_deleted' => null,
                    'user_id' => $vendor->id,
                ];
                $this->vendorPhoneRepository->create($data);
                echo $vendor->id . '. ' . $vendor->vendorProfile->business_phone;
                echo "\r\n";
            }
        }
        echo "done!!!";
        echo "\r\n";
    }
}
