<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="csrf-token" content="{{ csrf_token() }}"/>
    <meta name="description" content="">
    <meta name="author" content="">

    <title>Thumbnails</title>

    <link href="https://fonts.googleapis.com/css?family=Nunito:200,600" rel="stylesheet">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css"
          integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">


    <script src="https://code.jquery.com/jquery-3.3.1.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js"
            integrity="sha384-UO2eT0CpHqdSJQ6hJty5KVphtPhzWj9WO1clHTMGa3JDZwrnQq4sF86dIHNDz0W1"
            crossorigin="anonymous"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js"
            integrity="sha384-JjSmVgyd0p3pXB1rRibZUAYoIIy6OrQ6VrjIEaFf/nJGzIxFDsf4x0xIM+B07jRM"
            crossorigin="anonymous"></script>
    <script src="https://github.com/pipwerks/PDFObject/blob/master/pdfobject.min.js"></script>

    <style>
        .image-container {
            position: relative;
            width: 200px;
            height: 325px;
        }

        .image-container .after {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            display: none;
            color: #FFF;
        }

        .image-container:hover .after {
            display: block;
            background: rgba(0, 0, 0, .6);
        }

        .column {
            float: left;
            width: 20%;
            padding: 5px;
        }

        /* Clear floats after image containers */
        .row::after {
            content: "";
            clear: both;
            display: table;
        }
    </style>
</head>

<body cz-shortcut-listen="true">

<nav class="navbar navbar-expand-md navbar-dark bg-dark fixed-top">
    <a class="navbar-brand" href="{{ route('main') }}">Thumbnails</a>
    <button type="button" id="addBtn" class="btn btn-success" data-toggle="modal" data-target="#uploadModal">
        Add new
    </button>
</nav>

<main role="main" class="container">
    @include('modals.upload')
    <div class="starter-template" style="margin-top:80px">
        <h1>Thumbnails starter template</h1>
        <p class="lead">Press "Add" button to add new PDF file. Click on thumbnail to view full document</p>

        <div class="alert" id="action-message" style="display: none"></div>

        @include('includes.thumbnails-output')
    </div>
</main><!-- /.container -->
</body>

<script>

    $(document).on("click", "#deleteBtn", function () {

        $('#action-message').css('display', 'none');
        $('#action-message').html('');
        $('#action-message').removeClass();

        const delete_name = $(this).data('delete');
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        $.ajax({
            url: "{{ route('api.delete') }}",
            method: "POST",
            data: {
                delete_name: delete_name
            },
            success: function (data) {
                $('div.column.active').remove();
                $('#viewModal').modal('toggle');
                $('#action-message').css('display', 'block');
                $('#action-message').html(data.message);
                $('#action-message').addClass(data.class_name);
            }
        })
    });

    $(document).ready(function () {

        /*
        * This is the plugin
        */
        (function (a) {

            a.createModal = function (b) {
                defaults = {
                    title: "",
                    message: "Your Message Goes Here!",
                    closeButton: true,
                    scrollable: false,
                    file_name: "URL"
                };
                var b = a.extend({}, defaults, b);
                var c = (b.scrollable === true) ? 'style="max-height: 600px;overflow-y: auto;"' : "";
                html = '<div class="modal fade" id="viewModal">';
                html += '<div class="modal-dialog">';
                html += '<div class="modal-content">';
                html += '<div class="modal-header">';
                if (b.title.length > 0) {
                    html += '<h4 class="modal-title">' + b.title + "</h4>"
                }
                html += '<button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>';
                html += "</div>";
                html += '<div class="modal-body" ' + c + ">";
                html += b.message;
                html += "</div>";
                html += '<div class="modal-footer">';
                html += '<button id="deleteBtn" data-delete = "' + b.file_name + '" type="button" class="btn btn-danger">Delete</button>';
                if (b.closeButton === true) {
                    html += '<button type="button" class="btn btn-primary" data-dismiss="modal">Close</button>'
                }
                html += "</div>";
                html += "</div>";
                html += "</div>";
                html += "</div>";
                a("body").prepend(html);
                a("#viewModal").modal().on("hidden.bs.modal", function () {
                    a(this).remove()
                })
            }
        })(jQuery);

        $('#addBtn').on('click', function () {
            $('#message').css('display', 'none');
            $('#message').html('');
            $('#message').removeClass();
        });

        $("body").on('click', '.view-pdf', function () {
            const pdf_link = $(this).attr('href');
            const file_name = $(this).data('file_name');
            $(this).parent().parent().addClass('active');
            //var iframe = '<div class="iframe-container"><iframe src="'+pdf_link+'"></iframe></div>'
            //var iframe = '<object data="'+pdf_link+'" type="application/pdf"><embed src="'+pdf_link+'" type="application/pdf" /></object>'
            const iframe = '<object type="application/pdf" data="' + pdf_link + '" width="100%" height="500">No Support</object>';
            $.createModal({
                title: 'View file',
                message: iframe,
                closeButton: true,
                scrollable: false,
                file_name: file_name
            });
            return false;
        });

        $('#upload').on('click', function () {
            $('#upload_form').submit();
        });

        $('#upload_form').on('submit', function (event) {
            event.preventDefault();

            $('#message').css('display', 'none');
            $('#message').html('');
            $('#message').removeClass();

            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            $.ajax({
                url: "{{ route('api.add') }}",
                method: "POST",
                data: new FormData(this),
                dataType: 'JSON',
                contentType: false,
                cache: false,
                processData: false,
                success: function (data) {
                    $('#message').css('display', 'block');
                    $('#message').html(data.message);
                    $('#message').addClass(data.class_name);

                    let thumbnail = '<div class="column">';
                    thumbnail += '<div class="image-container">';
                    thumbnail += '<img src="' + data.uploaded_thumbnail + '" width="100%">';
                    thumbnail += '<a href="'
                        + data.uploaded_file +
                        '" data-file_name = "'
                        + data.uploaded_file_name +
                        '" class="after view-pdf">' + data.uploaded_file_name + '.jpg' + '</a>';
                    thumbnail += ' </div>';

                    $('.row:last').append(thumbnail);
                }
            })
        });

    });

</script>

</html>