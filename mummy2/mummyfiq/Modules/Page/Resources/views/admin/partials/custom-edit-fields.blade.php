<div class="box-body">
    <div class="box-body">
        <div class='form-group{{ $errors->has("{$lang}.title") ? ' has-error' : '' }}'>
            {!! Form::label("{$lang}[title]", trans('page::pages.form.title')) !!}
            <?php $old = $page->hasTranslation($lang) ? $page->translate($lang)->title : '' ?>
            {!! Form::text("{$lang}[title]", old("{$lang}.title", $old), ['class' => 'form-control', 'data-slug' => 'source', 'placeholder' => trans('page::pages.form.title')]) !!}
            {!! $errors->first("{$lang}.title", '<span class="help-block">:message</span>') !!}
        </div>
        <div class='{{ $errors->has("{$lang}.body") ? ' has-error' : '' }}'>
            {!! Form::label("{$lang}[body]", trans('page::pages.form.body')) !!}
            <?php $old = $page->hasTranslation($lang) ? $page->translate($lang)->body : '' ?>
            <textarea class="ckeditor" name="{{$lang}}[body]" rows="20" cols="80">
                {!! old("$lang.body", $old) !!}
            </textarea>
            {!! $errors->first("{$lang}.body", '<span class="help-block">:message</span>') !!}
        </div>
        <div class="form-group{{ $errors->has("medias_single") ? ' has-error' : '' }}">
            @include('media::admin.fields.file-link', [
                'entityClass' => 'Modules\\\\Page\\\\Entities\\\\PageTranslation',
                'entityId' => $page->pageTranslation->id,
                'zone' => 'background'
            ])
            {!! $errors->first("medias_single", '<span class="help-block">:message</span>') !!}
        </div>
        <?php if (config('asgard.page.config.partials.translatable.edit') !== []): ?>
            <?php foreach (config('asgard.page.config.partials.translatable.edit') as $partial): ?>
                @include($partial)
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</div>
