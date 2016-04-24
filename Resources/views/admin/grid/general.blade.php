@include('media::admin.grid.partials.content')
<script>
    function getUrlParam(paramName) {
        var reParam = new RegExp('(?:[\?&]|&)' + paramName + '=([^&]+)', 'i');
        var match = window.location.search.match(reParam);

        return ( match && match.length > 1 ) ? match[1] : null;
    }
    $(document).ready(function () {
        $('.jsInsertImage').on('click', function (e) {
            e.preventDefault();

            var callbackFunction = getUrlParam('callbackFunction');

            var $this = $(this), mediaId = $this.data('id'), mediaUrl = $this.data('file');
            window.opener[callbackFunction](mediaId, mediaUrl);
            window.close();
        });

        $('.jsInsertImageGallery').on('click', function (e) {
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
