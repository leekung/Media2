@include('media::admin.grid.partials.content', ['isWysiwyg' => true])
<script>
    $(document).ready(function () {
        $('.jsInsertImage').on('click', function (e) {
            e.preventDefault();
            function getUrlParam(paramName) {
                var reParam = new RegExp('(?:[\?&]|&)' + paramName + '=([^&]+)', 'i');
                var match = window.location.search.match(reParam);

                return ( match && match.length > 1 ) ? match[1] : null;
            }

            var funcNum = getUrlParam('CKEditorFuncNum');

            var $this = $(this), mediaId = $this.data('id'), mediaUrl = $this.data('file');
            var accept = getUrlParam('accept');
            if (accept) {
                var regex = new RegExp(accept);
                if (!regex.test(mediaUrl)) {
                    alert("{{ trans('media::message.Invalid file type') }}");
                    return;
                }
            }
            window.opener.CKEDITOR.tools.callFunction(funcNum, $(this).data('file'));
            window.close();
        });
    });
</script>
</body>
</html>
