var PublishedFiles = {
    triggerDelete: function (form) {
        form.trigger('submit');
    },
    loadForm: function (url, form) {
        form.attr('action', url);
    },
    init: function () {
        var form = $('#delete-published-file-form');

        $('.delete-lite-published-files').on('click', function () {
            var url = $(this).attr('data-href');
            PublishedFiles.loadForm(url, form);
        });

        $('#submit-delete-file').on('click', function () {
            PublishedFiles.triggerDelete(form);
        });
    }
};

$(document).ready(function () {
    PublishedFiles.init();
});
