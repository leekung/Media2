@extends('layouts.master')

@section('content-header')
<h1>
    {{ trans('media::media.title.media') }}
</h1>
<ol class="breadcrumb">
    <li><a href="{{ route('dashboard.index') }}"><i class="fa fa-dashboard"></i> {{ trans('core::core.breadcrumb.home') }}</a></li>
    <li><i class="fa fa-camera"></i> {{ trans('media::media.breadcrumb.media') }}</li>
</ol>
@stop

@section('styles')
<link href="{!! Module::asset('media:css/dropzone.css') !!}" rel="stylesheet" type="text/css" />
<style>
.dropzone {
    border: 1px dashed #CCC;
    min-height: 227px;
    margin-bottom: 20px;
}
</style>
@stop

@section('content')
<div class="row">
    <div class="col-md-12">
        <form method="POST" class="dropzone">
            {!! Form::token() !!}
        </form>
    </div>
</div>

<div class="row">
    <div class="col-md-12">
        <div class="box box-primary">
            <div class="box-body">
                <table class="data-table table table-bordered table-hover jsFileList">
                    <thead>
                        <tr>
                            <th>Id</th>
                            <th>{{ trans('core::core.table.thumbnail') }}</th>
                            <th>{{ trans('media::media.table.filename') }}</th>
                            <th>{{ trans('media::media.form.category_id') }}</th>
                            <th>{{ trans('media::media.form.youtube_url') }}</th>
                            <th>{{ trans('media::media.form.alt_attribute') }}</th>
                            <th>{{ trans('media::media.form.description') }}</th>
                            <th>{{ trans('media::media.form.keywords') }}</th>
                            <th>{{ trans('core::core.table.created at') }}</th>
                            <th>{{ trans('core::core.table.actions') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                    </tbody>
                    <tfoot>
                        <tr>
                            <th>Id</th>
                            <th>{{ trans('core::core.table.thumbnail') }}</th>
                            <th>{{ trans('media::media.table.filename') }}</th>
                            <th>{{ trans('media::media.form.category_id') }}</th>
                            <th>{{ trans('media::media.form.youtube_url') }}</th>
                            <th>{{ trans('media::media.form.alt_attribute') }}</th>
                            <th>{{ trans('media::media.form.description') }}</th>
                            <th>{{ trans('media::media.form.keywords') }}</th>
                            <th>{{ trans('core::core.table.created at') }}</th>
                            <th>{{ trans('core::core.table.actions') }}</th>
                        </tr>
                    </tfoot>
                </table>
            <!-- /.box-body -->
            </div>
        </div>
    </div>
</div>
@include('core::partials.delete-modal')
@stop

@section('scripts')
<script src="{!! Module::asset('media:js/dropzone.js') !!}"></script>
<?php $config = config('asgard.media.config'); ?>
<script>
    var maxFilesize = '<?php echo $config['max-file-size'] ?>',
            acceptedFiles = '<?php echo $config['allowed-types'] ?>';
</script>
<script src="{!! Module::asset('media:js/init-dropzone.js') !!}"></script>

<?php $locale = App::getLocale(); ?>
<script type="text/javascript">
    var $api, $dataTable = $('.data-table'), categories = {!! json_encode((object)$categories) !!};
    $(function () {
        $dataTable.DataTable({
            processing: true,
            serverSide: true,
            lengthChange: false,
            filter: true,
            sort: true,
            info: true,
            autoWidth: true,
            order: [[ 0, "desc" ]],
            ajax: '{!! route('admin.media.media.index') !!}',
            columns: [
                { data: 'id', name: 'id', searchable: false, visible: false },
                { data: 'thumbnail', name: 'thumbnail', searchable: false, sortable: false },
                { data: 'filename', name: 'filename' },
                {
                    data: 'category_id',
                    name: 'category_id',
                    className: 'editable',
                    render: function ( data, type, row, meta ) {
                        return '<a class="editable" data-source="{{ str_replace('"', '\"', json_encode((object)$categories)) }}" data-name="category_id" data-type="select" data-pk="'+row.id+'" data-value="'+row.category_id+'">'+categories[row.category_id || '0']+'</a>'
                    }
                },
                {
                    data: 'youtube_url',
                    name: 'youtube_url',
                    className: 'editable',
                    render: function ( data, type, row, meta ) {
                        return '<a class="editable" data-name="youtube_url" data-pk="'+row.id+'">'+(row.youtube_url || '')+'</a>'
                    }
                },
                {
                    data: 'alt_attribute',
                    name: 'alt_attribute',
                    className: 'editable',
                    render: function ( data, type, row, meta ) {
                        return '<a class="editable" data-name="alt_attribute" data-pk="'+row.id+'">'+(row.alt_attribute || '')+'</a>'
                    }
                },
                {
                    data: 'description',
                    name: 'description',
                    className: 'editable',
                    render: function ( data, type, row, meta ) {
                        return '<a class="editable" data-name="description" data-pk="'+row.id+'">'+(row.description || '')+'</a>'
                    }
                },
                {
                    data: 'keywords',
                    name: 'keywords',
                    className: 'editable',
                    render: function ( data, type, row, meta ) {
                        return '<a class="editable" data-name="keywords" data-pk="'+row.id+'">'+(row.keywords || '')+'</a>'
                    }
                },
                { data: 'created_at', name: 'created_at', searchable: false },
                {
                    data: null,
                    sortable: false,
                    searchable: false,
                    className: "center",
                    render: function ( data, type, row, meta ) {
                        return '<div class="btn-group">' +
                                '<a href="{{ route('admin.media.media.index') }}/' + row.id + '/edit" class="btn btn-default btn-flat"><i class="fa fa-pencil"></i></a>' +
                                '<button class="btn btn-danger btn-flat" data-toggle="modal" data-target="#modal-delete-confirmation" ' +
                                'data-action-target="{{ route('admin.media.media.index') }}/' + row.id + '"><i class="fa fa-trash"></i></button>' +
                                '</div>'
                    }
                }
            ],
            initComplete: function () {
                $api = this.api();
            },
            drawCallback: function (setting) {
                $('a.editable').editable({
                    url: function(params) {
                        var name = params.name;
                        var key = params.pk;
                        var value = params.value;
                        $.ajax({
                            url: '{{ route("api.media.update") }}',
                            method: 'POST',
                            data: {
                                locale: "{{ LaravelLocalization::getCurrentLocale() }}",
                                name: name,
                                id: key,
                                value: value,
                                _token: '{{ csrf_token() }}'
                            },
                            success: function(res) {
                            }
                        });
                    },
                    type: 'text',
                    mode: 'inline',
                    send: 'always'
                });
            }
        });


    });
</script>
@stop
