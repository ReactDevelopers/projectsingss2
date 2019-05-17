@php $margin_class_remove_div1 = isset($margin_class_remove) ? '' : 'my-4' ; @endphp
<div class="form-row {{ $margin_class_remove_div1 }}">
    <div class="col-sm-12">
        <label><strong>Files</strong></label>
        <p class="small">
            File types supported are: pdf, doc, docx, png, jpg, jpeg, gif, xls, xlsx.<br>
            File must be less than 2 MB in size.
        </p>
    </div>
</div>
@php $margin_class_remove_div2 = isset($margin_class_remove) ? '' : 'mb-3' ; @endphp
<div class="form-row {{ $margin_class_remove_div2 }}">
    <div class="col-sm-12 mb-2">
        <a href="#" class="btn btn-sm btn-primary file-upload"
            :class="{'is-invalid': form.errors.has('upload_file')}">
            <i class="fa fa-upload mr-2"></i> Upload New File
            <form enctype="multipart/form-data" method="POST" role="form">
                {{ csrf_field() }}
                <input type="file" name="upload_file" class="spark-uploader-control"
                    :class="{'is-invalid': form.errors.has('upload_file')}"
                    v-on:change="previewFile"
                    ref="upload_file">
            </form>
        </a>
        <span class="invalid-feedback" v-show="form.errors.has('upload_file')">
        @{{ form.errors.get('upload_file') }}
        </span>
    </div>
    <div class="col-sm-12">
        <div class="form-row">
            <div class="col-auto mb-2" v-for="(input, index) in uploaded_rows">
                <a :href="`/files/${input.id}/download`" class="btn btn-sm btn-secondary">
                    <i class="fa fa-file mr-2"></i> @{{ input.orig_filename }}
                </a>
                <a href="#" @click.prevent="deleteUploadsRow(index,`${input.id}`)"
                    class="btn btn-sm btn-danger"><i class="fa fa-close"></i></a>
            </div>
        </div>
    </div>
</div>
