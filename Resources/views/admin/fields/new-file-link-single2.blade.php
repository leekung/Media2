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
    .jsThumbnailImageWrapper img {
        max-width: 100%;
    }
</style>
<?php
$random = mt_rand();
?>
<script>
    window["openMediaWindow{{ $random }}"] = function (event) {
        window.open('{!! route('media.grid.select') !!}?callbackFunction=includeMedia{{ $random }}&accept={{ $accept }}', '_blank', 'menubar=no,status=no,toolbar=no,scrollbars=yes,height=500,width=1000');
    };
    window["includeMedia{{ $random }}"] = function (mediaId, filePath) {
        var accept = /{{ empty($accept) ? '.' : $accept }}/;
        if (!accept.test(filePath)) {
            alert("{{ trans('media::message.Invalid file type') }}");
            return;
        }
        var html = '<figure data-id="'+ mediaId +'"><img src="' + filePath + '" alt=""/>' +
                '<a class="jsRemoveSimpleLink" href="#" data-id="' + mediaId + '">' +
                '<i class="fa fa-times-circle removeIcon"></i></a>' +
                '<input type="hidden" name="medias_single[{{ $zone }}]" value="' + mediaId + '">' +
                '</figure>';
        $('.simple-wrap-{{ $random }} .jsThumbnailImageWrapper').html(html).fadeIn('slow', function() {
            window["toggleButton{{ $random }}"]();
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
            <?php if (${$zone}->isImage()): ?>
                <img src="{{ Imagy::getThumbnail(${$zone}->path, (isset($thumbnailSize) ? $thumbnailSize : 'mediumThumb')) }}" alt="{{ ${$zone}->alt_attribute }}"/>
            <?php else: ?>
                <i class="fa fa-file" style="font-size: 50px;"></i>
            <?php endif; ?>
            <a class="jsRemoveSimpleLink" href="#" data-id="{{ ${$zone}->pivot->id }}">
                <i class="fa fa-times-circle removeIcon"></i>
            </a>
        <?php endif; ?>
    </div>
</div>
<script>
    $( document ).ready(function() {
        $('.jsThumbnailImageWrapper').off('click', '.jsRemoveSimpleLink');
        $('.jsThumbnailImageWrapper').on('click', '.jsRemoveSimpleLink', function (e) {
            e.preventDefault();
            $(e.delegateTarget).fadeOut('slow', function() {
                toggleButton($(this));
            }).html('');
        });
    });

    function toggleButton(el) {
        var browseButton = el.parent().find('.btn-browse');
        browseButton.toggle();
    }
</script>
