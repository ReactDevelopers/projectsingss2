<?php namespace Modules\Report\Http\Controllers\Admin;

use Laracasts\Flash\Flash;
use Illuminate\Http\Request;
use Modules\Report\Entities\Review;
use Modules\Report\Repositories\ReviewRepository;
use Modules\Report\Services\ReviewService;
use Modules\Core\Http\Controllers\Admin\AdminBaseController;
use URL;
use Excel;

class ReviewController extends AdminBaseController
{
    /**
     * @var ReviewRepository
     */
    private $repository;

    /**
     * @var ReviewService
     */
    private $service;

    public function __construct(ReviewRepository $repository, ReviewService $service)
    {
        parent::__construct();

        $this->repository = $repository;
        $this->service = $service;
    }

    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index(Request $request)
    {
        //$reviews = $this->review->all();

        // return view('report::admin.reviews.index', compact(''));

        $reviews = $this->service->getItems('list', $request);
        $count = $this->service->getItems('count', $request);
        $limit = $request->get('limit');
        $keyword = $request->get('keyword');
        $page = $request->get('page');
        $array_field = array('id', 'first_name', 'email', 'status');
        $order_field = $request->get('order_field') && in_array($request->get('order_field'), $array_field) ? $request->get('order_field') : 'id';
        $sort = $request->get('sort') ? $request->get('sort') : 'DESC';

        $start = ($page ? $page - 1 : 0) * ($limit ? $limit : 10) + 1;
        $offset = $start + $reviews->count() - 1;
        return view('report::admin.reviews.index', compact(['reviews', 'limit', 'keyword', 'page', 'order_field', 'sort', 'count', 'start', 'offset']));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  Review $review
     * @return Response
     */
    public function detail(Review $review)
    {
        return view('report::admin.reviews.detail', compact('review'));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  Review $review
     * @return Response
     */
    public function destroy(Review $review)
    {
        $this->repository->destroy($review);

        flash()->success(trans('core::core.messages.resource deleted', ['name' => trans('report::reviews.title.reviews')]));

        return redirect()->route('admin.report.review.index');
    }
    public function exportcsv(Request $request)
    {
        //$reviews = $this->service->getItems('list', $request);
        $reviews = $this->service->all();
        Excel::create('Report Review', function($excel) use ($reviews){
            $excel->sheet('Sheet 1', function($sheet) use ($reviews){
                $sheet->loadView('report::admin.reviews.export.report_review',compact('reviews'));
            });
        })->export('csv');
    }
}
