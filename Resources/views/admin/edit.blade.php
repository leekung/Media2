@extends('layouts.master')

@section('content-header')
<h1>
    {{ trans('media::media.title.edit media') }} <small>{{ $file->filename }}</small>
</h1>
<ol class="breadcrumb">
    <li><a href="{{ URL::route('dashboard.index') }}"><i class="fa fa-dashboard"></i> {{ trans('core::core.breadcrumb.home') }}</a></li>
    <li><a href="{{ URL::route('admin.media.media.index') }}">{{ trans('media::media.title.media') }}</a></li>
    <li class="active">{{ trans('media::media.title.edit media') }}</li>
</ol>
@stop

@section('content')
{!! Form::open(['route' => ['admin.media.media.update', $file->id], 'method' => 'put']) !!}
<div class="row">
    <div class="col-md-8">
        <div class="nav-tabs-custom">
            @include('partials.form-tab-headers')
            <div class="tab-content">
                <?php $i = 0; ?>
                <?php foreach (LaravelLocalization::getSupportedLocales() as $locale => $language): ?>
                    <?php ++$i; ?>
                    <div class="tab-pane {{ App::getLocale() == $locale ? 'active' : '' }}" id="tab_{{ $i }}">
                        @include('media::admin.partials.edit-fields', ['lang' => $locale])
                    </div>
                <?php endforeach; ?>
                <div class='form-group{{ $errors->has("category_id") ? ' has-error' : '' }}'>
                    {!! Form::label("category_id", trans('media::media.form.category_id')) !!}
                    <select name="category_id" id="category_id" class="form-control">
                        <?php foreach ($categories as $id => $category): ?>
                        <option value="{{ $id }}" {{ old('category_id', $file->category_id) == $id ? 'selected' : '' }}>
                            {{ $category }}
                        </option>
                        <?php endforeach; ?>
                    </select>
                    {!! $errors->first("category_id", '<span class="help-block">:message</span>') !!}
                </div>
                <div class='form-group{{ $errors->has("youtube_url") ? ' has-error' : '' }}'>
                    {!! Form::label("youtube_url", trans('media::media.form.youtube_url')) !!}
                    {!! Form::text("youtube_url", Input::old("youtube_url", $file->youtube_url), ['class' => 'form-control', 'placeholder' => trans('media::media.form.youtube_url')]) !!}
                    {!! $errors->first("youtube_url", '<span class="help-block">:message</span>') !!}
                </div>
                <div class="box-footer">
                    <button type="submit" class="btn btn-primary btn-flat">{{ trans('core::core.button.update') }}</button>
                    <button class="btn btn-default btn-flat" name="button" type="reset">{{ trans('core::core.button.reset') }}</button>
                    <a class="btn btn-danger pull-right btn-flat" href="{{ URL::route('admin.media.media.index')}}"><i class="fa fa-times"></i> {{ trans('core::core.button.cancel') }}</a>
                </div>
            </div>
        </div> {{-- end nav-tabs-custom --}}
    </div>
    <div class="col-md-4">
        <?php if ($file->isImage()): ?>
            <img src="{{ $file->path }}" alt="{{ $file->alt_attribute }}" style="width: 100%;"/>
            <?php else: ?>
            <?php
            $map_icons = [
                    'xls' => 'fa-file-excel-o',
                    'xlsx' => 'fa-file-excel-o',
                    'doc' => 'fa-file-word-o',
                    'docx' => 'fa-file-word-o',
                    'pdf' => 'fa-file-pdf-o',
                    'zip' => 'fa-file-archive-o',
                    'rar' => 'fa-file-archive-o',
                    'gz' => 'fa-file-archive-o',
                    'mp4' => 'fa-file-video-o',
                    '3gp' => 'fa-file-video-o',
                    'ogv' => 'fa-file-video-o',
                    'webm' => 'fa-file-video-o',
                    'txt' => 'fa-file-text-o',
            ];
            $extension = pathinfo($file->path, PATHINFO_EXTENSION);
            ?>
            <a href="{{ $file->path }}" target="_blank">
                <i class="fa fa-file <?=(isset($map_icons[$extension]) ? $map_icons[$extension] : '')?>" style="font-size: 50px;"></i>
            </a>
        <?php endif; ?>
    </div>
</div>


<?php if ($file->isImage()): ?>
<div class="row">
    <div class="col-md-12">
        <h3>Thumbnails</h3>

        <ul class="list-unstyled">
            <?php foreach ($thumbnails as $thumbnail): ?>
                <li style="float:left; margin-right: 10px">
                    <img src="{{ Imagy::getThumbnail($file->path, $thumbnail->name()) }}" alt=""/>
                    <p class="text-muted" style="text-align: center">{{ $thumbnail->name() }} ({{ $thumbnail->size() }})</p>
                </li>
            <?php endforeach; ?>
        </ul>
    </div>
</div>
<?php endif; ?>
{!! Form::close() !!}
@stop


@section('footer')
    <a data-toggle="modal" data-target="#keyboardShortcutsModal"><i class="fa fa-keyboard-o"></i></a> &nbsp;
@stop

@section('shortcuts')
    <dl class="dl-horizontal">
        <dt><code>b</code></dt>
        <dd>{{ trans('core::core.back to index', ['name' => 'media']) }}</dd>
    </dl>
@stop

@section('scripts')
    <script>
        $( document ).ready(function() {
            $(document).keypressAction({
                actions: [
                    { key: 'b', route: "<?= route('admin.media.media.index') ?>" }
                ]
            });
        });
    </script>
@stop
