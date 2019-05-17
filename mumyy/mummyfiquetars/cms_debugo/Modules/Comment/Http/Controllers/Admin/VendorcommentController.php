<?php namespace Modules\Comment\Http\Controllers\Admin;

use Laracasts\Flash\Flash;
use Illuminate\Http\Request;
use Modules\Comment\Entities\Vendorcomment;
use Modules\Comment\Repositories\VendorcommentRepository;
use Modules\Comment\Services\VendorcommentService;
use Modules\Core\Http\Controllers\Admin\AdminBaseController;
use URL;
use Excel;
class VendorcommentController extends AdminBaseController
{
    /**
     * @var VendorcommentRepository
     */
    private $repository;

    /**
     * @var CommentService
     */
    private $commentService;

    public function __construct(VendorcommentRepository $repository, VendorcommentService $service)
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
        //$comments = $this->comment->all();
        // $reviews = Comment::all();

        // return view('comment::admin.comments.index', compact('reviews'));

        $comments = $this->service->getItems('list', $request);
        $count = $this->service->getItems('count', $request);
        $limit = $request->get('limit');
        $keyword = $request->get('keyword');
        $page = $request->get('page');
        $array_field = array('id', 'customer_name','business_name', 'title', 'created_at','status');
        $order_field = $request->get('order_field') && in_array($request->get('order_field'), $array_field) ? $request->get('order_field') : 'id';
        $sort = $request->get('sort') ? $request->get('sort') : 'DESC';

        $start = ($page ? $page - 1 : 0) * ($limit ? $limit : 10) + 1;
        $offset = $start + $comments->count() - 1;

        return view('comment::admin.vendorcomments.index', compact(['comments', 'limit', 'keyword', 'page', 'order_field', 'sort', 'count', 'start', 'offset']));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  Comment $comment
     * @return Response
     */
    public function edit(Vendorcomment $comment)
    {
        $previousUrl = URL::previous();

        return view('comment::admin.vendorcomments.edit', compact(['comment', 'previousUrl']));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  Comment $comment
     * @param  Request $request
     * @return Response
     */
    public function update(Vendorcomment $comment, Request $request)
    {
        $this->repository->update($comment, $request->all());

        flash()->success(trans('core::core.messages.resource updated', ['name' => trans('comment::comments.title.vendor comments')]));

        $previousUrl = $request->get('previousUrl');
        
        if($previousUrl){
            return redirect($previousUrl);
        }

        return redirect()->route('admin.comment.vendorcomment.index');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  Comment $comment
     * @return Response
     */
    public function destroy(Vendorcomment $comment)
    {
        $this->repository->destroy($comment);

        flash()->success(trans('core::core.messages.resource deleted', ['name' => trans('comment::comments.title.vendor comments')]));

        return redirect()->route('admin.comment.vendorcomment.index');
    }

    public function getExportVendorComment(Request $request)
    {
         $comments = $this->repository->all();
         Excel::create('Comments', function($excel) use ($comments){
            $excel->sheet('Sheet 1', function($sheet) use ($comments){
                $sheet->loadView('comment::admin.vendorcomments.export.vendorcomment',compact('comments'));
            });
         })->export('csv');
    }

}
