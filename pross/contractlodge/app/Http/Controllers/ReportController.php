<?php

namespace App\Http\Controllers;

use App\Report;
use App\Confirmation;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');

    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('reports.index');
    }

    /**
     * Display the Outstanding Confirmations.
     * @return \Illuminate\Http\Response
     */
    public function showOutStandingConfirmation() {
        $confirmations = Confirmation::getOutStandingConfirmations();
        return view('reports.confirmations.outstanding', compact('confirmations'));
    }
}
