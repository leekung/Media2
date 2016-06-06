@include('media::admin.grid.partials.content', ['isWysiwyg' => false])
<script>
    function getUrlParam(paramName) {
        var reParam = new RegExp('(?:[\?&]|&)' + paramName + '=([^&]+)', 'i');
        var match = window.location.search.match(reParam);

        return ( match && match.length > 1 ) ? match[1] : null;
    }
    $(document).ready(function () {
        $("body").on("click", '.jsInsertImage', function (e) {
            e.preventDefault();

            var callbackFunction = getUrlParam('callbackFunction');

            var $this = $(this), mediaId = $this.data('id'), mediaUrl = $this.data('file');
            var accept = getUrlParam('accept');
            if (accept) {
                var regex = new RegExp(accept);
                if (!regex.test(mediaUrl)) {
                    alert("{{ trans('media::message.Invalid file type') }}");
                    return;
                }
            }

            window.opener[callbackFunction](mediaId, mediaUrl);
            window.close();
        });

        $("body").on("click", '.jsInsertImageGallery', function (e) {
            e.preventDefault();

            var callbackFunction = getUrlParam('callbackFunction'),
                    $selected = $(".select-gallery:checked");
            if ($selected.length) {
                $selected.each(function(i, o) {
                    var $this = $(this), mediaId = $this.data('id'), mediaUrl = $this.data('file');
                    window.opener[callbackFunction](mediaId, mediaUrl);
                });
                window.close();
            }
        });
    });
</script>
</body>
</html>
