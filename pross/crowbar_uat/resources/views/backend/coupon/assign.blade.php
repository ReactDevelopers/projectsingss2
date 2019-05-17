@extends('layouts.backend.dashboard')
@section('content')
    <section class="content">
        <div class="row">
            <div class="col-xs-12">
                <div class="panel">
                    <form role="add-talent" method="post" enctype="multipart/form-data" action="{{ url(sprintf('%s/coupon/assign',ADMIN_FOLDER)) }}">
                        <input type="hidden" name="_method" value="PUT">
                        {{ csrf_field() }}

                        <input type="hidden" class="form-control" name="coupon_code" value="{{$coupon_code}}">
                        <div class="panel-body">
                            <div class="form-group">
                                <label for="name">Enter Email</label>
                                <input type="text" class="form-control" name="email" placeholder="Enter Email" value="{{ old('email') }}">
                            </div>
                        </div>

                        {{-- <div class="panel-body">
                            <div class="form-group">
                                <div class="datebox-no startdate">
                                    <label class="control-label">Assign Emails</label>
                                       	<select id="skills" name="skills[]" style="max-width: 400px;" class="filter form-control" data-request="tags" multiple="true" data-placeholder="{{ trans('website.W0193') }}">
                        					{!!___dropdown_options(___cache('skills'),'','',false)!!}
                   						</select>
                    					<div class="js-example-tags-container white-tags"></div>
                            	</div>
                        	</div>
                    	</div> --}}

{{--                         <div class="form-group">
                            <label class="control-label col-md-12 col-sm-12 col-xs-12">{{trans('website.W0861')}}</label>
                            <div class="col-md-12 col-sm-12 col-xs-12">
                                <div class="custom-dropdown">
                                    <select name="emails[]" class="form-control" data-request="email-tags" multiple="true">
                                        
                                    </select>
                                    <div class="js-example-tags-container white-tags"></div>
                                </div>
                            </div>
                        </div> --}}

                        {{-- <select class="js-example-tags form-control" multiple="multiple">
                          <option selected="selected">orange</option>
                          <option>white</option>
                          <option selected="selected">purple</option>
                        </select>
 --}}

                        {{-- <div class="custom-dropdown">
                            <select name="subindustry[]" style="max-width: 400px;"  class="form-control" data-request="tags-true" multiple="true" data-placeholder="{{ trans('website.W0799') }}">
                                
                            </select>
                            <div class="js-example-tags-container white-tags"></div>
                        </div> --}}
                        {{-- <div class="form-group">
                           <label class="control-label col-md-12 col-sm-12 col-xs-12">desired skills(max 10)<span class="pull-right selectOption">Select options according to your priority</span></label>
                           <div class="col-md-12 col-sm-12 col-xs-12">
                               <div class="customSelect selectedValOutBox">
                                   <select name="skill[]" multiple>
                                   </select>
                               </div>
                           </div>
                       </div> --}}


                        <div class="panel-footer">
                            <a href="{{ $backurl }}" class="btn btn-default">Back</a>
                            <button type="button" data-request="ajax-submit" data-target='[role="add-talent"]' class="btn btn-default">Save</button>
                        </div>
                    </form>

                </div>
            </div>
        </div>
    </section>
@endsection

@push('inlinescript')
<link href="{{ asset('css/jquery-ui.css') }}" rel="stylesheet">
<script src="{{ asset('js/jquery-ui.js') }}" type="text/javascript"></script>
<script src="{{ asset ("/backend/js/select2.min.js") }}"></script>
<script type="text/javascript">

    // $("#select2").select2({
    //     createSearchChoice:function(term, data) { 
    //         if ($(data).filter(function() { 
    //             return this.text.localeCompare(term)===0; 
    //         }).length===0) 
    //         {return {id:term, text:term};} 
    //     },
    //     multiple: true,
    //     data: [{id: 0, text: 'story'},{id: 1, text: 'bug'},{id: 2, text: 'task'}]
    // });

    //$(".js-example-tags").select2({
    //   tags: true
    // });

    // $('.js-example-tags').select2({
    //   createTag: function (params) {
    //     var term = $.trim(params.term);

    //     if (term === '') {
    //       return null;
    //     }

    //     return {
    //       id: term,
    //       text: term,
    //       newTag: true // add additional parameters
    //     }
    //   }
    // });


    // $('[name="skill[]"]').on('keyup', function(e) { 
    //     if(e.keyCode === 13) addToList($(this).val()); 
    // });

    // $(document).on('keyup', '.customSelect', function (e) { if (e.which === 13) { 
    //         alert('Pressed enter!'); 
    //     } 
    // });


    // $('[name="skill[]"]').select2({
    //     formatLoadMore : function() {return 'Loading more...'},
    //     tags:true,
    //     ajax: {
    //     url: "http://localhost/janeous/public/skill",
    //     dataType: 'json',
    //     data: function (params) {
    //     var query = {
    //     search: params.term,
    //     type: 'public'
    //     }
    //     return query;
    //     }
    //     },
    //     placeholder:"Select Skills"
    // }).parent('.customSelect').addClass('select2Init'); 


    // $('[data-request="email-tags"]').select2({
    //     multiple: true,
    //     tags: true,
    //     maximumInputLength: 50,
    //     insertTag: function(data, tag) {
    //         var reg = /^([A-Za-z0-9_\-\.])+\@([A-Za-z0-9_\-\.])+\.([A-Za-z]{2,4})$/;
    //         if((reg.test(tag.text)) == false){
    //             return false;
    //         }

    //         var $found = false;
    //         $.each(data, function(index, value) {
    //             if($.trim(tag.text).toUpperCase() == $.trim(value.text).toUpperCase()) {
    //                 $found = true;
    //             }
    //         });

    //         if(!$found) data.unshift(tag);   
    //     },
    //     language: {
    //         noResults: function (params) {
    //             return $valid_email_note;
    //         }
    //     }
    // }).on('change', function() {
    //     console.log('change');
    //     var $selected = $(this).find('option:selected');
    //     var $container = $(this).siblings('.js-example-tags-container');

    //     var $list = $('<ul>');
    //     $selected.each(function(k, v) {
    //         var $li = $('<li class="tag-selected"><a class="destroy-tag-selected">×</a>' + $(v).text() + '</li>');
    //         $li.children('a.destroy-tag-selected')
    //         .off('click.select2-copy')
    //         .on('click.select2-copy', function(e) {
    //             var $opt = $(this).data('select2-opt');
    //             $opt.attr('selected', false);
    //             $opt.parents('select').trigger('change');
    //         }).data('select2-opt', $(v));
    //         $list.append($li);
    //     });
    //     $container.html('').append($list);
    // }).trigger('change');



</script>
@endpush
