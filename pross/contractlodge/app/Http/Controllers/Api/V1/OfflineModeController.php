<?php

namespace App\Http\Controllers\Api\V1;

use App\OfflineMode;
use App\Http\Controllers\Controller;

class OfflineModeController extends Controller
{

    /**
     * Action to create list of url's to cache.
     *
     * @return Response
     */
    public function getPageUrlList() {
        $offline_mode = new OfflineMode;
        $urls_to_cache = $offline_mode->getPageUrlList();

        return response()->json($urls_to_cache);
    }

}
