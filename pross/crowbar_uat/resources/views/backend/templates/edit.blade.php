@extends('admin.index')

@section('content')
<!-- Main content -->
    <section class="content">
      <div class="row">
        <div class="col-xs-12">
          <div class="box">
            <div class="box-header">
            	<div class="col-md-8">
              		<h3 class="box-title"></h3>
              	</div>
              	<div class="col-md-4 text-right">
              		<a href="{{ url('admin/message-config') }}" class="btn btn-default"><< Back to Special Request</a>
              	</div>
            </div>
            <!-- /.box-header -->
            <!-- general form elements -->
              <div class="box box-primary">
                
                <!-- /.box-header -->
                <!-- form start -->
                <form role="form" method="post" enctype="multipart/form-data" action="{{ url('admin/message-config/'.$id)}}">
                <input type="hidden" name="_method" value="PUT">
                {{ csrf_field() }}
                  <div class="box-body">
                    <div class="form-group col-md-12 no-padding">
                      <div class="col-md-3 no-padding">
                        <label>Status</label>
                        <select class="form-control" name="message_type" disabled>
                          <option value="email" @if($message->message_type=='email') selected @endif>Email</option>
                          <option value="sms" @if($message->message_type)=='sms') selected @endif>SMS</option>
                        </select>
                    </div>
                    </div>
                    <div class="form-group @if ($errors->has('subject'))has-error @endif">
                      <label for="name">Subject</label>
                      <input type="text" class="form-control" id="subject" name="subject" placeholder="Subject" value="{{ $message->subject }}">
                      @if ($errors->has('subject'))
                        <span class="help-block">
                        {{ $errors->first('subject')}}
                        </span>
                      @endif
                    </div>
                    <div class="form-group @if ($errors->has('content'))has-error @endif">
                      <label for="content">Content</label>
                      <textarea class="form-control" id="message-content" placeholder="Address" name="content">{{ $message->content }}</textarea>
                      @if ($errors->has('content'))
                        <span class="help-block">
                        {{ $errors->first('content')}}
                        </span>
                      @endif
                    </div>
                    
                    <div class="form-group col-md-3 no-padding">
                        <label>Status</label>
                        <select class="form-control" name="status">
                          <option value="active" @if($message->status=='active') selected @endif>Active</option>
                          <option value="inactive" @if($message->status=='inactive') selected @endif>Inactive</option>
                        </select>
                    </div>
                    <div class="form-group col-md-9"></div>
                    
                  </div>
                  <!-- /.box-body -->

                  <div class="box-footer">
                    <button type="submit" class="btn btn-primary">Submit</button>
                  </div>
                </form>
              </div>
              <!-- /.box -->
            <!-- /.box-body -->
          </div>
          <!-- /.box -->
        </div>
      </div>
    </section>

@endsection

@section('customjs')
<script src="https://cdn.ckeditor.com/4.5.7/standard/ckeditor.js"></script>
<script type="text/javascript">
  CKEDITOR.replace('message-content');
</script>
@endsection