$(document).ready(function () {
   $("#category").on("change", function(e){
        fetch_select_index($(this).val());
    });
    function fetch_select_index(val)
    {   console.log($("#token").val());
        $.ajax({
            url: $("#url-category").val(),
            data: {
                'category_id':val,
            },
            success: function (response) {
                if(response)
                {
                    var text = "";
                    var x;
                    var i = 0;
                    var name = "";
                    for (x in response.data) {
                        text += "<option value="+x+">"+response.data[x]+"</option>";
                        i++;
                    }
                    $("#sub_category").html(text);
                }
                else
                {
                    var text = '';
                    $("#sub_category").html(text);
                }
            }
        });
    }
});
