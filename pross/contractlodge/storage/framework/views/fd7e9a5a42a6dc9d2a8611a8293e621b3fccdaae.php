<div class="col-sm-8">
        <label><strong>Files</strong></label>
        <p class="small">
            File types supported are: pdf, doc, docx, png, jpg, jpeg, gif, xls, xlsx.
        </p>
    </div>
<div class="col-sm-10">
    <div class="form-row">
        <div class="col-auto mb-2" v-for="(input, index) in uploaded_rows">
            <a :href="`/files/${input.id}/download`" class="btn btn-sm btn-secondary">
                <i class="fa fa-file mr-2">
                </i>
                {{ input.orig_filename }}
            </a>
        </div>
    </div>
</div>
