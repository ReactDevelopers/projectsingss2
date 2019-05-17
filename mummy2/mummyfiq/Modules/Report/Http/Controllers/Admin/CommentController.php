<?php namespace Modules\Report\Http\Controllers\Admin;

use Laracasts\Flash\Flash;
use Illuminate\Http\Request;
use Modules\Report\Entities\Comment;
use Modules\Report\Repositories\CommentRepository;
use Modules\Report\Services\CommentService;
use Modules\Core\Http\Controllers\Admin\AdminBaseController;
use URL;
use Excel;

class CommentController extends AdminBaseController
{
    /**
     * @var CommentRepository
     */
    private $repository;

    /**
     * @var CommentService
     */
    private $service;

    public function __construct(CommentRepository $repository, CommentService $service)
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
        // $items = $this->service->all();

        // return view('report::admin.comments.index', compact('items'));

        $comments = $this->service->getItems('list', $request);
        $count = $this->service->getItems('count', $request);
        $limit = $request->get('limit');
        $keyword = $request->get('keyword');
        $page = $request->get('page');
        $array_field = array('id', 'first_name', 'email', 'status');
        $order_field = $request->get('order_field') && in_array($request->get('order_field'), $array_field) ? $request->get('order_field') : 'id';
        $sort = $request->get('sort') ? $request->get('sort') : 'DESC';

        $start = ($page ? $page - 1 : 0) * ($limit ? $limit : 10) + 1;
        $offset = $start + $comments->count() - 1;
        return view('report::admin.comments.index', compact(['comments', 'limit', 'keyword', 'page', 'order_field', 'sort', 'count', 'start', 'offset']));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  Comment $comment
     * @return Response
     */
    public function detail(Comment $comment)
    {
        return view('report::admin.comments.detail', compact('comment'));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  Comment $comment
     * @return Response
     */
    public function destroy(Comment $comment)
    {
        $this->repository->destroy($comment);

        flash()->success(trans('core::core.messages.resource deleted', ['name' => trans('report::comments.title.comments')]));

        return redirect()->route('admin.report.comment.index');
    }
    public function exportcsv(Request $request)
    {
        //$reviews = $this->service->getItems('list', $request);
        $comments = $this->service->all();
        Excel::create('Report Comment', function($excel) use ($comments){
            $excel->sheet('Sheet 1', function($sheet) use ($comments){
                $sheet->loadView('report::admin.comments.export.report_comment',compact('comments'));
            });
        })->export('csv');
    }
}
