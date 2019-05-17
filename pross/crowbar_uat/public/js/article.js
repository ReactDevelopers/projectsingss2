function addReply(id_reply){
    $('#text-reply-area-'+id_reply).toggle();
    $("#reply-area-"+id_reply).hide();
    // $(".ask-question").toggle();
}

function insertReply(id_reply){
    var add_reply_url = $('#add-reply').val();
    var answer_description = $('#answer_description_'+id_reply).val();
    var answer_type = $('select[name=answer_type_' + id_reply + '] option:selected').val();
    
    if(answer_description.length <= 0){
        $('#text-reply-area2-'+id_reply).addClass('has-error');
        $('#text-reply-error-area-'+id_reply).html('<div class="help-block">Enter your comment.</div>');
    }else{
        $('#text-reply-area2-'+id_reply).removeClass('has-error');
        $('#text-reply-error-area-'+id_reply).html('');
    }

    if(id_reply > 0 && answer_description.length > 0){
        $.ajax({
            method: "PUT",
            url: add_reply_url,
            data: { id_parent: id_reply, answer_description: answer_description, type: answer_type}
        }).done(function(data) {
            $('#text-reply-area-'+id_reply).toggle();
            $('#answer_description_'+id_reply).val('');
            $('#add-reply-response-'+id_reply).html(data.message);
            $('#add-reply-response-'+id_reply).fadeIn('slow');
            $('#add-reply-response-'+id_reply).fadeOut(9000);
            setTimeout(function(){
                location.reload();
            },2000);
        });
    }
}

function loadReply(id_reply){
    var reply_list_url = $('#list-reply').val();
    if(id_reply > 0){
        $.ajax({
        method: "POST",
        url: reply_list_url,
        data: { id_reply: id_reply}
        })
        .done(function(data) {
            $("#reply-area-"+id_reply).html(data);
            $("#reply-area-"+id_reply).show();
            $('#text-reply-area-'+id_reply).hide();
        });
    }
}

function closeReplyArea(id_reply){
    $('#text-reply-area-'+id_reply).hide();
}

$(document).on('click','[data-request="follow-question"]',function(){
    $('#popup').show(); 
    var $this = $(this);
    var $url    = $this.data('url');
    $.ajax({
        url: $url, 
        cache: false, 
        contentType: false, 
        processData: false, 
        type: 'get',
        success: function($response){
            $('#popup').hide();
            if($this.hasClass('active')){
                $this.removeClass('active');
                $this.html($response.data);
                $('.follow_user_'+$response.user_id).removeClass('active');
                $('.follow_user_'+$response.user_id).html($response.data);
            }else{
                $this.addClass('active');
                $this.html($response.data);
                $('.follow_user_'+$response.user_id).addClass('active');
                $('.follow_user_'+$response.user_id).html($response.data);
            }
        },error: function(error){
            $('#popup').hide();
        }
    });
});

$(document).on('click','[data-request="follow-article"]',function(){
    $('#popup').show(); 
    var $this = $(this);
    var $url    = $this.data('url');
    $.ajax({
        url: $url, 
        cache: false, 
        contentType: false, 
        processData: false, 
        type: 'get',
        success: function($response){
            $('#popup').hide();
            if($this.hasClass('active')){
                $this.removeClass('active');
                $this.html($response.data);
            }else{
                $this.addClass('active');
                $this.html($response.data);
            }
        },error: function(error){
            $('#popup').hide();
        }
    });
});