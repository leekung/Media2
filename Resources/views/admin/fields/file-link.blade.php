<style>
    .jsThumbnailImageWrapper {
        position: relative;
        display: inline-block;
        background-color: #fff;
        border: 1px solid #eee;
        padding: 3px;
        border-radius: 3px;
        margin-top: 20px;
    }
    .jsThumbnailImageWrapper i.removeIcon {
        position: absolute;
        top:-10px;
        right:-10px;
        color: #f56954;
        font-size: 2em;
        background: white;
        border-radius: 20px;
        height: 25px;
    }
</style>
<?php
$random = mt_rand();
?>
<script>
    window["openMediaWindow{{ $random }}"] = function (event) {
        window.open('{!! route('media.grid.select') !!}?callbackFunction=includeMedia{{ $random }}', '_blank', 'menubar=no,status=no,toolbar=no,scrollbars=yes,height=500,width=1000');
    };
    window["includeMedia{{ $random }}"] = function (mediaId, mediaUrl) {
        $.ajax({
            type: 'POST',
            url: '{{ route('api.media.link') }}',
            data: {
                'mediaId': mediaId,
                '_token': '{{ csrf_token() }}',
                'entityClass': '{{ $entityClass }}',
                'entityId': '{{ $entityId }}',
                'zone': '{{ $zone }}'
            },
            success: function (data) {
                if(!data.error) {
                    var html = '<figure data-id="'+ data.result.imageableId +'"><img src="' + data.result.path + '" alt=""/>' +
                            '<a class="jsRemoveSimpleLink" href="#" data-id="' + data.result.imageableId + '">' +
                            '<i class="fa fa-times-circle removeIcon"></i>' +
                            '</a></figure>';

                    $('.simple-wrap-{{ $random }} .jsThumbnailImageWrapper').html(html).fadeIn('slow', function() {
                        window["toggleButton{{ $random }}"]();
                    });
                }
            }
        });
    };
    window["toggleButton{{ $random }}"] = function () {
        $('.btn-browse-'+'{{ $random }}').toggle();

    }
</script>
<div class="form-group simple-wrap-{{ $random }}">
    {!! Form::label($zone, ucwords(str_replace('_', ' ', $zone)) . ':') !!}
    <div class="clearfix"></div>

    <a class="btn btn-primary btn-browse btn-browse-{{ $random }}" onclick="openMediaWindow{{ $random }}(event, '{{ $zone }}');" <?php echo (isset(${$zone}->path))?'style="display:none;"':'' ?>><i class="fa fa-upload"></i>
        {{ trans('media::media.Browse') }}
    </a>

    <div class="clearfix"></div>

    <div class="jsThumbnailImageWrapper">
        <?php if (isset(${$zone}->path)): ?>
            <figure data-id="{{ ${$zone}->pivot->id }}">
            <?php if (${$zone}->isImage()): ?>
                <img src="{{ Imagy::getThumbnail(${$zone}->path, (isset($thumbnailSize) ? $thumbnailSize : 'mediumThumb')) }}" alt="{{ ${$zone}->alt_attribute }}"/>
            <?php else: ?>
                <i class="fa fa-file" style="font-size: 50px;"></i>
            <?php endif; ?>
            <a class="jsRemoveSimpleLink" href="#" data-id="{{ ${$zone}->pivot->id }}">
                <i class="fa fa-times-circle removeIcon"></i>
            </a>
            </figure>
        <?php endif; ?>
    </div>
</div>
<script>
    $( document ).ready(function() {
        $('.simple-wrap-{{ $random }} .jsThumbnailImageWrapper').on('click', '.jsRemoveSimpleLink', function (e) {
            e.preventDefault();
            var $this = $(this), imageableId = $this.data('id');
            $.ajax({
                type: 'POST',
                url: '{{ route('api.media.unlink') }}',
                data: {
                    'imageableId': imageableId,
                    '_token': '{{ csrf_token() }}'
                },
                success: function(data) {
                    if (data.error === false) {
                        $(e.delegateTarget).fadeOut('slow', function() {
                            window["toggleButton{{ $random }}"]();
                        }).html('');
                    } else {
                        $(e.delegateTarget).append(data.message);
                    }
                }
            });
        });
    });
</script>
