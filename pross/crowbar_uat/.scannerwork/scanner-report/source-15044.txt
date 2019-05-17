<div class="modal-dialog member_modal_style invite-msg-modal" role="add-member">
    <div class="modal-content">
        <button type="button" class="button close_modal" data-dismiss="modal"><img src="{{asset('images/close-me.png')}}" /></button>
        <div >
            <div class="col-md-12 col-sm-12 col-xs-12">
                <div class="talent_icon text-center">
                    <img src="{{asset('images/key-lock.png')}}" />
                </div>            
                <div class="col-sm-12 text-center member_modal_text top-space">
                    <span class="hire-title hire-text">{{trans('website.W0978')}}</span>
                </div>
                <form class="form-horizontal" role="inviteMember" action="{{url(sprintf('%s/accept-transfer-ownership/process/',TALENT_ROLE_TYPE))}}" method="POST" accept-charset="utf-8"  onkeypress="return event.keyCode != 13">
                <div class="form-group row text-center top-space">
                    <div class="invite-circle-radio-list col-md-12 col-sm-12">
                        <div class="invite-member-availability search-box-availability">
                            <input type="password" class="form-control" name="old_password" placeholder="{{trans('website.W0979')}}"  value="">
                        </div>
                    </div>
                </div>
                    <div class="clearfix"></div>
                    <div class="member_modal_btn addnotetext wrapper-button">
                        <a class="greybutton-line" data-dismiss="modal">{{trans('website.W0355')}}</a>
                        <button type="button" class="button" data-request="ajax-submit" data-target="[role='inviteMember']">{{trans('website.W0974')}}</button>
                    </div>
                </form>
            </div>
            <div class="clearfix"></div>
        </div>
    </div>
</div>
{{-- <script type="text/javascript">
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
            }else{
                $('[name="outside_emails[]"]').select2();
                $('#show_from_circle').hide();
                $('#show_outside_circle').show();
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
            }
        });


    });
</script> --}}s