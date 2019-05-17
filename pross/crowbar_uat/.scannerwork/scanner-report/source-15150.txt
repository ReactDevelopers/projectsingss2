<div class="new-upload" id="files-{{$file['id_file']}}">
    <div class="uploaded-docx clearfix">
        <a href="{{url(sprintf('/download/file?file_id=%s',___encrypt($file['id_file'])))}}">
            <div class="grey-attachement-image">
                <img src="{{asset('images/pink-attachement.png')}}">
            </div>
        </a>
        <div class="upload-info">
            <p>{{$file['extension']}}</p>
            <p class="attachement-name">{{$file['filename']}}</p>
        </div>
        <a href="javascript:void(0);" data-url="{{sprintf(url('ajax/%s?id_file=%s'), DELETE_DOCUMENT, $file['id_file'] )}}" data-single="true" data-after-upload=".single-remove" data-toremove="files" title="Delete" data-request="delete" data-file_id="{{$file['id_file']}}" data-delete-id="file_id" data-edit-id="file_id" class="delete-attachment c-p" data-ask="{{trans('website.W0688')}}"><img src="{{asset('images/delete-icon.png')}}" />
        </a>
		<input type="hidden" name="documents[]" value="{{$file['id_file']}}">
    </div>
</div>