$(document).ready(function () {
    function formatRepo (repo) {
      if (repo.loading) return repo.text;
      var markup = repo.full_name;
      return markup;
    };

    function formatRepoSelection (repo) {
        return repo.text;
    };

  $(".form-data-vendor").select2({
    ajax: {
        url: $("#url-vendor").val(),
        dataType: 'json',
        delay: 250,
        data: function (params) {
          return {
            q: params.term, // search term
            page: params.page
          };
        },
        processResults: function (data, params) {
          // parse the results into the format expected by Select2
          // since we are using custom formatting functions we do not need to
          // alter the remote JSON data, except to indicate that infinite
          // scrolling can be used
          params.page = params.page || 1;
          console.log(data);
          return {
            results: data.items,
            pagination: {
                more: (params.page * 10) < data.total_count
            }
          };
        },
        cache: true
      },
      escapeMarkup: function (markup) { return markup; }, // let our custom formatter work
      minimumInputLength: 1,
      templateResult: formatRepo,
      templateSelection: formatRepoSelection // omitted for brevity, see the source of this page
    });

  $("#vendor").on("change", function(e){
        fetch_select_index($(this).val());
    });
    function fetch_select_index(val)
    {   console.log($("#token").val());
        $.ajax({
            url: $("#url-category-vendor").val(),
            data: {
                'vendor_id':val,
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
                    $("#category").html(text);
                }
                else
                {
                    var text = '';
                    $("#category").html(text);
                }
            }
        });
    }
});