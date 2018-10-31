$(document).ready(function () {

    $("#import_form").on('submit', function (e) {
        e.preventDefault(); // prevent native submit

        var fileInputs = $('input[type=file]');

        if (fileInputs[0].value !== '') {
            $('#startImport').css('display', 'none');

            let csrfToken = document.cookie.match(/csrfToken=\S+;/)[0];
            let csrfTockenValue = csrfToken.slice(10, -1);

            $(this).ajaxSubmit({
                "headers": {
                    "X-CSRF-Token" : csrfTockenValue,
                },
                success: viewResponse
            });

        } else {
            alert('Please select a file to import');
        }
        fileInputs[0].value = '';
        $('#myResultsDiv').text('');
    });

    function viewResponse(data) {

        for (var collectionKey in data) {
            var collection = data[collectionKey];

            $('#myResultsDiv')
                .css('display', 'block')
                .append('<h3>' + collectionKey + '</h3>')
                .append('<ul id="' + collectionKey + '" class="list-group row">');

            for (var itemIndex in collection) {
                var collectionItem = collection[itemIndex];
                $('ul#' + collectionKey).append('<li class="list-group-item">' + collectionItem + '</li>');
            }
            $('#myResultsDiv').append('</hr>');
        }

        $('#myResultsDiv').append('<div id="reportFooter" class="row"></div>');
        $('#reportFooter').append('<a class="btn btn-info text-light" id="downloadReport">Download report</a>');
        var importReport = JSON.stringify(data);
        $('#downloadReport').attr('href', 'data:application/json;charset=utf-8,' + encodeURIComponent(importReport))
            .attr('download', 'import-report');
        $('#startImport').css('display', 'block');
    }
});

