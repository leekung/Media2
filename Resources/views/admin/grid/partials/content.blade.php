<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>{{ trans('media::media.file picker') }}</title>
    {!! Theme::style('vendor/bootstrap/dist/css/bootstrap.min.css') !!}
    {!! Theme::style('vendor/font-awesome/css/font-awesome.min.css') !!}
    {!! Theme::style('vendor/admin-lte/dist/css/AdminLTE.css') !!}
    {!! Theme::style('vendor/datatables.net-bs/css/dataTables.bootstrap.min.css') !!}
    <link href="{!! Module::asset('translation:vendor/x-editable/dist/bootstrap3-editable/css/bootstrap-editable.css') !!}" rel="stylesheet" type="text/css" />
    <link href="{!! Module::asset('media:css/dropzone.css') !!}" rel="stylesheet" type="text/css" />
    <style>
        body {
            background: #ecf0f5;
            margin-top: 20px;
        }
        .dropzone {
            border: 1px dashed #CCC;
            min-height: 227px;
            margin-bottom: 20px;
            display: none;
        }
    </style>
    @include('partials.asgard-globals')
</head>
<body>
<div class="container">
    <div class="row">
        <form method="POST" class="dropzone">
            {!! Form::token() !!}
        </form>
    </div>
    <div class="clearfix"></div>
    <div class="row">
        <div class="box box-primary">
            <div class="box-header">
                <h3 class="box-title">{{ trans('media::media.choose file') }}</h3>
                <div class="box-tools pull-right">
                    <button class="btn btn-box-tool jsShowUploadForm" data-toggle="tooltip" title="" data-original-title="Upload new">
                        <i class="fa fa-cloud-upload"></i>
                        Upload new
                    </button>
                </div>
            </div>
            <div class="box-body">
                <table class="data-table table table-bordered table-hover jsFileList data-table">
                    <thead>
                    <tr>
                        <th>Id</th>
                        <th data-sortable="false">
                            <a href="javascript:;" class="btn btn-primary jsInsertImageGallery">
                                {{ trans('media::media.insert') }}
                            </a>
                        </th>
                        <th>{{ trans('core::core.table.thumbnail') }}</th>
                        <th>{{ trans('media::media.table.filename') }}</th>
                        <th>Alt</th>
                        <th>Description</th>
                        <th>Keywords</th>
                        <th>{{ trans('core::core.table.created at') }}</th>
                        <th data-sortable="false">{{ trans('core::core.table.actions') }}</th>
                    </tr>
                    </thead>
                    <tbody>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
{!! Theme::script('vendor/jquery/jquery.min.js') !!}
{!! Theme::script('vendor/bootstrap/dist/js/bootstrap.min.js') !!}
{!! Theme::script('vendor/datatables.net/js/jquery.dataTables.min.js') !!}
{!! Theme::script('vendor/datatables.net-bs/js/dataTables.bootstrap.min.js') !!}
<script src="{!! Module::asset('media:js/dropzone.js') !!}"></script>
<script src="{!! Module::asset('translation:vendor/x-editable/dist/bootstrap3-editable/js/bootstrap-editable.min.js') !!}"></script>
<?php $config = config('asgard.media.config'); ?>
<script>
    var maxFilesize = '<?php echo $config['max-file-size'] ?>',
        acceptedFiles = '<?php echo $config['allowed-types'] ?>';
</script>
<script src="{!! Module::asset('media:js/init-dropzone.js') !!}"></script>
<script>
    $( document ).ready(function() {
        $('.jsShowUploadForm').on('click',function (event) {
            event.preventDefault();
            $('.dropzone').fadeToggle();
        });
    });
</script>

<?php $locale = App::getLocale(); ?>
<script type="text/javascript">
    var $api, $dataTable = $('.data-table');
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
            ajax: '{!! route('media.grid.select') !!}',
            columns: [
                { data: 'id', name: 'id', searchable: false, visible: false },
                {
                    data: null,
                    name: 'select',
                    searchable: false,
                    visible: {{ Input::get('multiple') == 1 ? 'true' : 'false' }},
                    render: function ( data, type, row, meta ) {
                        return row.path.match(/\.(jpg|png|jpeg|gif)$/i) ? '<input type="checkbox" class="select-gallery" data-file="'+row.path+'" data-id="'+row.id+'" data-file-path="'+row.path+'">' : '';
                    }
                },
                { data: 'thumbnail', name: 'thumbnail', searchable: false, sortable: false },
                { data: 'filename', name: 'filename' },
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
                { data: 'action', name: 'action', searchable: false, sortable: false }
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


