<div class="modal-dialog member_modal_style invite-msg-modal" role="add-member">
    <div class="modal-content">
        <button type="button" class="button close_modal" data-dismiss="modal"><img src="{{asset('images/close-me.png')}}" /></button>
        <div >
            <div class="col-md-12 col-sm-12 col-xs-12">
                <div class="talent_icon text-center">
                    <img src="{{asset('images/add-note.png')}}" />
                </div>            
                <div class="col-sm-12 text-center member_modal_text top-space">
                    <span class="hire-title">{{trans('website.W0932')}}</span>
                </div>
                <form class="form-horizontal" role="inviteMember{{$event_id}}" action="{{url(sprintf('%s/invite-member/process/',TALENT_ROLE_TYPE))}}" method="POST" accept-charset="utf-8" autocomplete="off">
                    <div class="row text-center top-space">
                        <div class="invite-circle-radio-list">
                            <div class="invite-member-availability">
                                <div class="radio color_pink">                
                                    <input type="radio" id="from_circle" name="invite_from" value="from_circle" data-request="focus-input-checkbox" checked="checked">
                                    <label for="from_circle"><span class="check"></span>{{trans('website.W0933')}}</label>
                                </div>
                            </div>
                        </div>
                        <div class="invite-circle-radio-list">
                            <div class="invite-member-availability">
                                <div class="radio color_pink">                
                                    <input type="radio" id="outside_circle" name="invite_from" value="outside_circle" data-request="focus-input-checkbox">
                                    <label for="outside_circle"><span class="check"></span>{{trans('website.W0934')}}</label>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="clearfix"></div>
                    <div class="member_modal_btn addnotetext">
                        <div class="form-group" id="show_from_circle">
                            <div class="skills-filter">
                                <div class="custom-dropdown">
                                    <select id="talent_emails" name="talent_emails[]" style="max-width: 400px;" class="filter form-control" multiple="true" data-placeholder="{{trans('website.W0936')}}">
                                    </select>
                                    <div class="js-example-tags-container white-tags"></div>
                                </div>
                            </div>
                    	</div>

                        <div class="form-group" id="show_outside_circle" style="display:none;">
                            <div class="col-md-8 col-sm-12 col-xs-12">
                                <div class="custom-dropdown">
                                    <input type="text"  id="outside_names" class="form-control" placeholder="Please Enter Name">
                                    {{-- <select name="outside_emails[]" class="form-control" data-request="email-tags" multiple="true"> 
                                    </select>--}}
                                </div>
                            </div>
                        </div>

                        <div class="form-group" id="show_outside_circle_name" style="display:none;">
                            <div class="col-md-8 col-sm-12 col-xs-12">
                                <div class="custom-dropdown">
                                    <input type="text" id="outside_emails" class="form-control" placeholder="Please Enter Email" autocomplete="off">
                                    {{-- <select name="outside_names[]" class="form-control" data-request="name"  multiple="true"> 
                                    </select>--}}
                                </div>
                            </div>
                            <div class="col-md-4 col-sm-12 col-xs-12 ">
                                <button type="button" class="button " id="add-outside-info" >Add</button>
                            </div>
                        </div>

                        <div class="form-group" >
                            <div class="col-md-12 col-sm-12 col-xs-12">
                                <div class="js-example-tags-container white-tags outside-email-name">
                                    <ul></ul>
                                </div>
                            </div>
                        </div>

                        <input type="text" name="event_id" value="{{$event_id}}" class="hide">
                        <input type="hidden" name="ret_page" value="{{$ret_page}}" class="hide">

                        <a class="greybutton-line" data-dismiss="modal">{{trans('website.W0355')}}</a>
                        <button type="button" class="button" data-request="ajax-submit" data-target="[role='inviteMember{{$event_id}}']">{{trans('website.W0935')}}</button>
                    </div>
                </form>
            </div>
            <div class="clearfix"></div>
        </div>
    </div>
</div>
<script type="text/javascript">
    $(document).ready(function(){
        $('[name="talent_emails[]"]').select2({
            ajax: {
                url: "{{url('talent/get_user_emails')}}",
                dataType: 'json',
                data: function (params) {
                    var query = {
                        search: params.term,
                        type: 'public'
                    }
                    return query;
                }
            },
            placeholder: function(){
                $(this).find('option[value!=""]:first').html();
            }
        }).on('change', function() {
        var $selected = $(this).find('option:selected');
        var $container = $(this).siblings('.js-example-tags-container');

        var $list = $('<ul>');
        $selected.each(function(k, v) {
            var $li = $('<li class="tag-selected"><a class="destroy-tag-selected">×</a>' + $(v).text() + '</li>');
            $li.children('a.destroy-tag-selected')
            .off('click.select2-copy')
            .on('click.select2-copy', function(e) {
                var $opt = $(this).data('select2-opt');
                $opt.attr('selected', false);
                $opt.parents('select').trigger('change');
            }).data('select2-opt', $(v));
            $list.append($li);
        });
        $container.html('').append($list);
    }).trigger('change');


        $('input:radio[name="invite_from"]').click(function() {
            var invite_from = $('input[name="invite_from"]:checked').val();
            if (invite_from == "from_circle") {
                $('#show_from_circle').show();
                $('#show_outside_circle').hide();
                $('#show_outside_circle_name').hide();
                $('#add-outside').hide();
            }else{
                // $('[name="outside_emails[]"]').select2();
                $('#show_from_circle').hide();
                $('#show_outside_circle').show();
                $('#show_outside_circle_name').show();
                $('#add-outside').show();
                $('[data-request="email-tags"]').select2({
                    multiple: true,
                    tags: true,
                    maximumInputLength: 50,
                    insertTag: function(data, tag) {
                        var reg = /^([A-Za-z0-9_\-\.])+\@([A-Za-z0-9_\-\.])+\.([A-Za-z]{2,4})$/;
                        if((reg.test(tag.text)) == false){
                            return false;
                        }

                        var $found = false;
                        $.each(data, function(index, value) {
                            if($.trim(tag.text).toUpperCase() == $.trim(value.text).toUpperCase()) {
                                $found = true;
                            }
                        });

                        if(!$found) data.unshift(tag);   
                    },
                    language: {
                        noResults: function (params) {
                            return $valid_email_note;
                        }
                    }
                }).on('change', function() {
                    var $selected = $(this).find('option:selected');
                    var $container = $(this).siblings('.js-example-tags-container');

                    var $list = $('<ul>');
                    $selected.each(function(k, v) {
                        var $li = $('<li class="tag-selected"><a class="destroy-tag-selected">×</a>' + $(v).text() + '</li>');
                        $li.children('a.destroy-tag-selected')
                        .off('click.select2-copy')
                        .on('click.select2-copy', function(e) {
                            var $opt = $(this).data('select2-opt');
                            $opt.attr('selected', false);
                            $opt.parents('select').trigger('change');
                        }).data('select2-opt', $(v));
                        $list.append($li);
                    });
                    $container.html('').append($list);
                }).trigger('change'); 

                $('[data-request="name"]').select2({
                    multiple: true,
                    tags: true,
                    maximumInputLength: 50,
                    insertTag: function(data, tag) {
                        /*var reg = /^([A-Za-z0-9_\-\.])+\@([A-Za-z0-9_\-\.])+\.([A-Za-z]{2,4})$/;
                        if((reg.test(tag.text)) == false){
                            return false;
                        }*/

                        var $found = false;
                        $.each(data, function(index, value) {
                            if($.trim(tag.text).toUpperCase() == $.trim(value.text).toUpperCase()) {
                                $found = true;
                            }
                        });

                        if(!$found) data.unshift(tag);   
                    },
                    language: {
                        noResults: function (params) {
                            return "Please Enter Name.";
                        }
                    }
                }).on('change', function() {
                    var $selected = $(this).find('option:selected');
                    var $container = $(this).siblings('.js-example-tags-container');

                    var $list = $('<ul>');
                    $selected.each(function(k, v) {
                        var $li = $('<li class="tag-selected"><a class="destroy-tag-selected">×</a>' + $(v).text() + '</li>');
                        $li.children('a.destroy-tag-selected')
                        .off('click.select2-copy')
                        .on('click.select2-copy', function(e) {
                            var $opt = $(this).data('select2-opt');
                            $opt.attr('selected', false);
                            $opt.parents('select').trigger('change');
                        }).data('select2-opt', $(v));
                        $list.append($li);
                    });
                    $container.html('').append($list);
                }).trigger('change'); 
            }
        });
        
        $(document).on('click','#add-outside-info',function() {
            var $names = $('#outside_names').val();
            var $email = $('#outside_emails').val();
            var $existingEmail = $('input[name="outside_emails[]"]');
            var $err = 0;
            var $reg = /^([A-Za-z0-9_\-\.])+\@([A-Za-z0-9_\-\.])+\.([A-Za-z]{2,4})$/;

            if($names == '' ||$names == null ){
                $('#outside_names').parent().find('.help-block').remove();
                $('#outside_names').parent().append('<div class="help-block">Please enter name.</div>');
                $('#outside_names').parent().parent().parent().addClass('has-error');
                $err++;

            }else{
                $('#outside_names').parent().find('.help-block').remove();
                $('#outside_names').parent().parent().parent().removeClass('has-error');
            }

            if($email == '' || $email == null ){
                $('#outside_emails').parent().find('.help-block').remove();
                $('#outside_emails').parent().append('<div class="help-block">Please enter email.</div>');
                $('#outside_emails').parent().parent().parent().addClass('has-error');
                $err++;

            }else if($reg.test($email) === false){
                $('#outside_emails').parent().find('.help-block').remove();
                $('#outside_emails').parent().append('<div class="help-block">Please enter valid email.</div>');
                $('#outside_emails').parent().parent().parent().addClass('has-error');
                $err++;
            }else if($existingEmail != undefined && $existingEmail.length > 0){
                $emailsArr = $("input[name='outside_emails[]']").map(function(){return $(this).val();}).get();
                $.each($emailsArr,function(key,val){
                    if($email==val){
                        $('#outside_emails').parent().find('.help-block').remove();
                        $('#outside_emails').parent().append('<div class="help-block">Please enter a different email.</div>');
                        $('#outside_emails').parent().parent().parent().addClass('has-error');
                        $err++;
                    }
                });
            }else{
                $('#outside_emails').parent().find('.help-block').remove();
                $('#outside_emails').parent().parent().parent().removeClass('has-error');
            }

            if($err == 0 ){
                $('#outside_emails').parent().find('.help-block').remove();
                $('#outside_emails').parent().parent().parent().removeClass('has-error');
                $('#outside_names').val('');
                $('#outside_emails').val('');
                $list = '<li class="tag-selected"><a class="destroy-tag-selected">×</a>'+$names+'<br/>'+$email+'<input type="hidden" name="outside_emails[]" value="'+$email+'" /><input type="hidden" name="outside_names[]" value="'+$names+'" /></li>';
                $('.outside-email-name > ul').append($list);
            }else{
                return false;
            }
        });

        $(document).on('click','.destroy-tag-selected',function(){
            console.log($(this).index())
            $(this).parent().remove();
        });

    });


</script>