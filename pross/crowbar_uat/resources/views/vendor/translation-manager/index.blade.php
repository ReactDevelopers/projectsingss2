@extends('layouts.backend.translation')

@section('content')
    <section class="content">
        <div class="panel">
            <div class="panel-body">
                <h3>Translation Manager</h3>
                <div class="alert alert-success success-import" style="display:none;">
                    <p>Done importing, processed <strong class="counter">N</strong> items! Reload this page to refresh the groups!</p>
                </div>
                <div class="alert alert-success success-find" style="display:none;">
                    <p>Done searching for translations, found <strong class="counter">N</strong> items!</p>
                </div>
                <div class="alert alert-success success-publish" style="display:none;">
                    <p>Done publishing the translations for group '<?= $group ?>'!</p>
                </div>
                <?php if(Session::has('successPublish')) : ?>
                    <div class="alert alert-info">
                        <?php echo Session::get('successPublish'); ?>
                    </div>
                <?php endif; ?>
                <hr> 
                <div class="row">
                    <div class="col-md-9">
                        <form role="form" action="{{url(sprintf("%s/translations/view",ADMIN_FOLDER)) }}">
                            <input type="hidden" name="_token" value="<?php echo csrf_token(); ?>">
                            <div class="form-group">
                                <select name="group" id="group" class="form-control group-select">
                                    <?php foreach($groups as $key => $value): ?>
                                        <option value="<?= $key ?>"<?= $key == $group ? ' selected':'' ?>><?= $value ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </form>
                    </div>
                    <div class="col-md-3">
                        <?php if(!isset($group)) : ?>
                            <form class="hide form-inline form-import" method="POST" action="<?= url(sprintf("%s/translations/import",ADMIN_FOLDER)) ?>" data-remote="true" role="form">
                                <input type="hidden" name="_token" value="<?php echo csrf_token(); ?>">
                                <select name="replace" class="form-control">
                                    <option value="0">Append new translations</option>
                                    <option value="1">Replace existing translations</option>
                                </select>
                                <button type="submit" class="btn btn-success"  data-disable-with="Loading..">Import groups</button>
                            </form>
                            <form class="hide form-inline form-find" method="POST" action="<?= url(sprintf("%s/translations/find",ADMIN_FOLDER)) ?>" data-remote="true" role="form" data-confirm="Are you sure you want to scan you app folder? All found translation keys will be added to the database.">
                                <input type="hidden" name="_token" value="<?php echo csrf_token(); ?>">
                                <p></p>
                                <button type="submit" class="btn btn-primary" data-disable-with="Searching.." >Find translations in files</button>
                            </form>
                        <?php endif; ?>
                        <?php if(isset($group)) : ?>
                            <form class="form-inline form-publish" method="POST" action="<?= url(sprintf("%s/translations/publish/%s",ADMIN_FOLDER,$group)) ?>" data-remote="true" role="form" data-confirm="Are you sure you want to publish the translations group '<?= $group ?>? This will overwrite existing language files.">
                                <input type="hidden" name="_token" value="<?php echo csrf_token(); ?>">
                                <button type="submit" class="btn btn-primary" data-disable-with="Publishing.." >Publish translations</button>
                                <a href="<?= url(sprintf("%s/translations",ADMIN_FOLDER)) ?>" class="btn btn-default">Back</a>
                            </form>
                        <?php endif; ?>
                    </div>
                    <div class="clear-fix"></div>
                </div>
                <hr> 
                <?php if($group): ?>
                    <?php if(0){ ?>
                        <form action="<?= url(sprintf("%s/translations/add/%s",ADMIN_FOLDER,$group)) ?>" method="POST"  role="form">
                            <input type="hidden" name="_token" value="<?php echo csrf_token(); ?>">
                            <textarea class="form-control" rows="3" name="keys" placeholder="Add 1 key per line, without the group prefix"></textarea>
                            <p></p>
                            <input type="submit" value="Add keys" class="btn btn-primary">
                        </form>
                        <hr>
                        <h4>Total: <?= $numTranslations ?>, changed: <?= $numChanged ?></h4>
                    <?php } ?>
                    <div class="table-responsive">
                        <table class="table">
                            <thead>
                            <tr>
                                <th>Key</th>
                                <?php foreach($locales as $locale): ?>
                                    <th><?= $locale ?></th>
                                <?php endforeach; ?>
                                <?php if($deleteEnabled): ?>
                                    <th>&nbsp;</th>
                                <?php endif; ?>
                            </tr>
                            </thead>
                            <tbody>

                            <?php foreach($translations as $key => $translation): ?>
                                <tr id="<?= $key ?>">
                                    <td><?= $key ?></td>
                                    <?php foreach($locales as $locale): ?>
                                        <?php $t = isset($translation[$locale]) ? $translation[$locale] : null?>
                                        <td>
                                            <a href="#edit" class="editable status-<?= $t ? $t->status : 0 ?> locale-<?= $locale ?>" data-locale="<?= $locale ?>" data-name="<?= $locale . "|" . $key ?>" id="username" data-type="textarea" data-pk="<?= $t ? $t->id : 0 ?>" data-url="<?= url(sprintf("%s/translations/edit/%s",ADMIN_FOLDER,$group)) ?>" data-title="Enter translation"><?= $t ? htmlentities($t->value, ENT_QUOTES, 'UTF-8', false) : '' ?></a>
                                        </td>
                                    <?php endforeach; ?>
                                    <?php if($deleteEnabled): ?>
                                        <td>
                                            <a href="<?= url(sprintf("%s/translations/delete/%s/%s",ADMIN_FOLDER,$group,$key)) ?>" class="delete-key" data-confirm="Are you sure you want to delete the translations for '<?= $key ?>?"><span class="glyphicon glyphicon-trash"></span></a>
                                        </td>
                                    <?php endif; ?>
                                </tr>
                            <?php endforeach; ?>

                            </tbody>
                        </table>
                    </div>
                <?php else: ?>
                    <p>Choose a group to display the group translations. If no groups are visible, make sure you have run the migrations and imported the translations.</p>
                <?php endif; ?>
                <div class="clear-fix"></div>
            </div>
        </div>
    </section>
@endsection