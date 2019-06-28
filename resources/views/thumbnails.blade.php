<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" xmlns:scrolling="http://www.w3.org/1999/xhtml">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}"/>

    <title>Upload Files</title>
    <!-- Fonts -->
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
    <!-- Styles -->
    <style>
        html, body {
            background-color: #fff;
            color: #636b6f;
            font-family: 'Nunito', sans-serif;
            font-weight: 200;
            height: 100vh;
            margin: 0;
        }

        .full-height {
            height: 100vh;
        }

        .flex-center {
            align-items: center;
            display: flex;
            justify-content: center;
        }

        .position-ref {
            position: relative;
        }

        .top-right {
            position: absolute;
            right: 10px;
            top: 18px;
        }

        .content {
            text-align: center;
        }

        .title {
            font-size: 84px;
        }

        .links > a {
            color: #636b6f;
            padding: 0 25px;
            font-size: 13px;
            font-weight: 600;
            letter-spacing: .1rem;
            text-decoration: none;
            text-transform: uppercase;
        }

        .m-b-md {
            margin-bottom: 30px;
        }

        .iframe-container {
            padding-bottom: 60%;
            padding-top: 30px;
            height: 0;
            overflow: hidden;
        }

        .iframe-container iframe,
        .iframe-container object,
        .iframe-container embed {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
        }

        .modal.in .modal-dialog {
            transform: none; /*translate(0px, 0px);*/
        }
    </style>
</head>
<body>
<div class="flex-center position-ref full-height">
    <div class="content">
        <div class="title m-b-md">
            Thumbnails
        </div>

        <button type="button" class="btn btn-success" data-toggle="modal" data-target="#exampleModal">
            Add
        </button>


        <button type="button" class="btn btn-warning" data-toggle="modal" data-target="#documentModal">
            Open
        </button>

        <div class="container">
            <div class="row">
                <h2>PDF in modal preview using Easy Modal Plugin</h2>
                <a class="btn btn-primary view-pdf" href="/files/pdf/public_files_pdf_EPIC.pdf">View PDF</a>
            </div>
        </div>


        <div id="thumbnails" style="margin-top: 20px">
            <span id="uploaded_file">
                 @foreach($thumbnails as $thumbnail)
                    <img src="{{asset("files/thumbnails/" . $thumbnail->getRelativePathname())}}" class="img-thumbnail" width="300" />
                @endforeach
            </span>
        </div>
    </div>
</div>


{{--Modal for upload--}}
<div class="modal fade" id="exampleModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
     aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Add new PDF</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>

            <div class="modal-body">
                <div class="alert" id="message" style="display: none"></div>
                <form method="post" enctype="multipart/form-data" id="upload_form">
                    <input type="file" name="select_file" id="select_file"/>
                </form>
            </div>

            <div class="modal-footer">
                <button type="submit" name="upload" id="upload" class="btn btn-primary">Upload</button>
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
            </div>

        </div>
    </div>
</div>


<div class="modal fade" id="documentModal" tabindex="-1" role="dialog" aria-labelledby="documentModalLabel"
     aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <?php
            $id = 'public_files_pdf_EPIC.pdf';
            ?>
            {{--            <img src="{{ action('Thumbnail@getDocument', ['id'=> $id]) }}" style="width:600px; height:800px;">--}}
        </div>
    </div>
</div>

</body>

<script>
    $(document).ready(function () {

        /*
         * This is the plugin
        */
        (function (a) {
            a.createModal = function (b) {
                defaults = {title: "", message: "Your Message Goes Here!", closeButton: true, scrollable: false};
                var b = a.extend({}, defaults, b);
                var c = (b.scrollable === true) ? 'style="max-height: 600px;overflow-y: auto;"' : "";
                html = '<div class="modal fade" id="myModal">';
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
                if (b.closeButton === true) {
                    html += '<button type="button" class="btn btn-primary" data-dismiss="modal">Close</button>'
                }
                html += "</div>";
                html += "</div>";
                html += "</div>";
                html += "</div>";
                a("body").prepend(html);
                a("#myModal").modal().on("hidden.bs.modal", function () {
                    a(this).remove()
                })
            }
        })(jQuery);

        /*
        * Here is how you use it
        */
        $(function () {
            $('.view-pdf').on('click', function () {
                var pdf_link = $(this).attr('href');
                //var iframe = '<div class="iframe-container"><iframe src="'+pdf_link+'"></iframe></div>'
                //var iframe = '<object data="'+pdf_link+'" type="application/pdf"><embed src="'+pdf_link+'" type="application/pdf" /></object>'
                var iframe = '<object type="application/pdf" data="' + pdf_link + '" width="100%" height="600">No Support</object>';
                $.createModal({
                    title: 'View',
                    message: iframe,
                    closeButton: true,
                    scrollable: false
                });
                return false;
            });
        });

        $('#upload').on('click', function () {
            $('#upload_form').submit();
        });

        $('#upload_form').on('submit', function (event) {
            event.preventDefault();

            $('#message').css('display', 'none');
            $('#message').html('');
            $('#message').addClass('');

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
                    $('#uploaded_file').prepend(data.uploaded_file);
                }
            })
        });

    });

</script>
</html>
