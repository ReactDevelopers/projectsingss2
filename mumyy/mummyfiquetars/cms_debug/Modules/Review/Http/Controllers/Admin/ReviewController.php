<?php namespace Modules\Review\Http\Controllers\Admin;

use Laracasts\Flash\Flash;
use Illuminate\Http\Request;
use Modules\Review\Entities\Review;
use Modules\Review\Repositories\ReviewRepository;
use Modules\Core\Http\Controllers\Admin\AdminBaseController;
use Modules\Review\Services\ReviewService;

class ReviewController extends AdminBaseController
{
    /**
     * @var ReviewRepository
     */
    private $review;

    /**
     * @var ReviewService
     */
    private $service;

    public function __construct(ReviewRepository $review, ReviewService $service)
    {
        parent::__construct();

        $this->review = $review;
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
        // $reviews = Review::all();

        // return view('review::admin.reviews.index', compact('reviews'));

        $reviews = $this->service->getItems('list', $request);
        $count = $this->service->getItems('count', $request);
        $limit = $request->get('limit');
        $keyword = $request->get('keyword');
        $page = $request->get('page');
        $array_field = array('id', 'business_name', 'customer_name', 'message', 'email_content');
        $order_field = $request->get('order_field') && in_array($request->get('order_field'), $array_field) ? $request->get('order_field') : 'id';
        $sort = $request->get('sort') ? $request->get('sort') : 'DESC';

        $start = ($page ? $page - 1 : 0) * ($limit ? $limit : 10) + 1;
        $offset = $start + $reviews->count() - 1;

        return view('review::admin.reviews.index', compact(['reviews', 'limit', 'keyword', 'page', 'order_field', 'sort', 'count', 'start', 'offset']));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return Response
     */
    public function create()
    {
        return view('review::admin.reviews.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  Request $request
     * @return Response
     */
    public function store(Request $request)
    {
        $this->review->create($request->all());

        flash()->success(trans('core::core.messages.resource created', ['name' => trans('review::reviews.title.reviews')]));

        return redirect()->route('admin.review.review.index');
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  Review $review
     * @return Response
     */
    public function edit(Review $review)
    {
        return view('review::admin.reviews.edit', compact('review'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  Review $review
     * @param  Request $request
     * @return Response
     */
    public function update(Review $review, Request $request)
    {
        $this->review->update($review, $request->all());

        flash()->success(trans('core::core.messages.resource updated', ['name' => trans('review::reviews.title.reviews')]));

        // return redirect()->route('admin.review.review.index');
        return view('review::admin.reviews.edit', compact('review'));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  Review $review
     * @return Response
     */
    public function destroy(Review $review)
    {
        $this->review->destroy($review);

        flash()->success(trans('core::core.messages.resource deleted', ['name' => trans('review::reviews.title.reviews')]));

        return redirect()->route('admin.review.review.index');
    }

    
}
