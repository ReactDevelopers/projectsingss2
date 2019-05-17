<?php namespace Modules\Comment\Http\Controllers\Admin;

use Laracasts\Flash\Flash;
use Illuminate\Http\Request;
use Modules\Comment\Entities\Comment;
use Modules\Comment\Repositories\CommentRepository;
use Modules\Comment\Services\CommentService;
use Modules\Core\Http\Controllers\Admin\AdminBaseController;
use URL;
use Excel;
class CommentController extends AdminBaseController
{
    /**
     * @var CommentRepository
     */
    private $comment;

    /**
     * @var CommentService
     */
    private $commentService;

    public function __construct(CommentRepository $comment, CommentService $commentService)
    {
        parent::__construct();

        $this->comment = $comment;
        $this->commentService = $commentService;
    }

    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index(Request $request)
    {
        //$comments = $this->comment->all();
        // $reviews = Comment::all();

        // return view('comment::admin.comments.index', compact('reviews'));

        $reviews = $this->commentService->getItems('list', $request);
        $count = $this->commentService->getItems('count', $request);
        $limit = $request->get('limit');
        $keyword = $request->get('keyword');
        $page = $request->get('page');
        $array_field = array('id', 'customer_name','business_name', 'title', 'created_at','status');
        $order_field = $request->get('order_field') && in_array($request->get('order_field'), $array_field) ? $request->get('order_field') : 'id';
        $sort = $request->get('sort') ? $request->get('sort') : 'DESC';

        $start = ($page ? $page - 1 : 0) * ($limit ? $limit : 10) + 1;
        $offset = $start + $reviews->count() - 1;

        return view('comment::admin.comments.index', compact(['reviews', 'limit', 'keyword', 'page', 'order_field', 'sort', 'count', 'start', 'offset']));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return Response
     */
    public function create()
    {
        return view('comment::admin.comments.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  Request $request
     * @return Response
     */
    public function store(Request $request)
    {
        $this->comment->create($request->all());

        flash()->success(trans('core::core.messages.resource created', ['name' => trans('comment::comments.title.comments')]));

        return redirect()->route('admin.comment.comment.index');
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  Comment $comment
     * @return Response
     */
    public function edit(Comment $comment)
    {
        $previousUrl = URL::previous();

        return view('comment::admin.comments.edit', compact(['comment', 'previousUrl']));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  Comment $comment
     * @param  Request $request
     * @return Response
     */
    public function update(Comment $comment, Request $request)
    {
        $this->comment->update($comment, $request->all());

        // update rating point
        $vendor_id = $comment->vendor_id;
        $this->commentService->updateRatingPoint($vendor_id);

        flash()->success(trans('core::core.messages.resource updated', ['name' => trans('comment::comments.title.comments')]));

        $previousUrl = $request->get('previousUrl');
        
        if($previousUrl){
            return redirect($previousUrl);
        }

        return redirect()->route('admin.comment.comment.index');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  Comment $comment
     * @return Response
     */
    public function destroy(Comment $comment)
    {
        $vendor_id = $comment->vendor_id;
        
        $this->comment->destroy($comment);

        // update rating point
        $this->commentService->updateRatingPoint($vendor_id);

        flash()->success(trans('core::core.messages.resource deleted', ['name' => trans('comment::comments.title.comments')]));

        return redirect()->route('admin.comment.comment.index');
    }

    /**
     * Export Review Data List
     *
     * @param  Review $customer
     * @return Response
     */

    public function getExportComment(Request $request){
        $comments = $this->comment->all();
        Excel::create('Reviews', function($excel) use ($comments){
            $excel->sheet('Sheet 1', function($sheet) use ($comments){
                $sheet->loadView('comment::admin.comments.export.comment',compact('comments'));
            });
        })->export('csv');
    }
}
