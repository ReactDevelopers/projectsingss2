@extends('layouts.backend.dashboard')

@section('content')
    <section class="content">
        <div class="row">
            <div class="col-md-3">
                <div class="box box-solid">
                    <div class="box-header with-border">
                        <h3 class="box-title">Folders</h3>
                        <div class="box-tools">
                            <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
                        </div>
                    </div>
                    <div class="box-body no-padding">
                        <ul class="nav nav-pills nav-stacked">
                            <li><a href="{{url('administrator/messages/inbox')}}"><i class="fa fa-inbox"></i> Inbox</a>
                            <li><a href="{{url('administrator/messages/closed')}}"><i class="fa fa-power-off"></i> Closed</a></li>
                            <li><a href="{{url('administrator/messages/trashed')}}"><i class="fa fa-trash-o"></i> Trash</a></li>
                        </ul>
                    </div>
                </div>
            </div>
            <div class="col-md-9">
                <div class="panel">
                    <div class="box">
                        <form role="add-talent" method="post" enctype="multipart/form-data" action="{{ url('administrator/messages/reply/'.$message['id_message']) }}">
                            <input type="hidden" name="_method" value="PUT">
                            {{ csrf_field() }}

                            <div class="box-body no-padding">
                                <div class="mailbox-read-info">
                                    <h3 class="break-all">{{$message['message_subject']}}</h3>
                                    <span class="mailbox-read-time pull-right"></span>
                                </div>
                                <div class="clear-fix"></div>
                                <div class="mailbox-read-message" style="border-bottom:1px solid #f1f1f1;">
                                    <div class="row" style="border-bottom:1px solid #f1f1f1;margin-bottom:5px;padding-bottom:10px;">
                                        <div class="col-md-6 text-left">
                                            <a href="javascript:;" class="mailbox-attachment-name">From: {{$message['sender_name']}} ({{$message['sender_email']}})</a>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-12">
                                            <div>
                                                {{$message['message_content']}}
                                                <div class="clear-fix"></div>
                                            </div>
                                            <span class="mailbox-attachment-size">{{___ago($message['created'])}}</span>
                                        </div>
                                    </div>
                                    @if($message['message_ticket_status'] == 'open')
                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="form-group">
                                                <label for="name">Reply</label>
                                                <textarea name="message_content" class="form-control"></textarea>
                                                <input name="record_id" value="{{$message['id_message']}}" type="hidden">
                                                <div class="clear-fix"></div>
                                                <div class="btn-group pull-right" style="margin-top:10px;">
                                                    <button data-request="ajax-submit" data-target='[role="add-talent"]' name="reply" class="btn btn-default" type="button" value="reply">Reply</button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    @endif

                                    @if(!empty($message_replay))
                                        <div class="row" style="border-bottom:1px solid #f1f1f1;margin-bottom:5px;padding-bottom:10px;">
                                            <div class="col-md-6 text-left">
                                                <a href="javascript:;" class="mailbox-attachment-name">From: CrowBar Admin</a>
                                            </div>
                                        </div>
                                        @foreach($message_replay as $m)
                                        <div class="row">
                                            <div class="col-md-12">
                                                <div>
                                                    {{$m['message_content']}}
                                                    <div class="clear-fix"></div>
                                                </div>
                                                <span class="mailbox-attachment-size">{{___ago($m['created'])}}</span>
                                            </div>
                                        </div>
                                        @endforeach
                                    @endif
                                </div>
                            </div>

                        </form>
                        @if($message['message_status'] != 'trashed')
                        <div class="box-footer">
                            <a href="{{url('administrator/messages/delete/'.$message['id_message'])}}" onclick="return confirm('Do you really want to continue with this action?');">
                                <button class="btn btn-default" name="delete" value="do_delete" type="submit"><i class="fa fa-trash-o"></i> Delete</button>
                            </a>
                        </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection

@push('inlinescript')
<script type="text/javascript">

</script>
@endpush
