@php
    $columns = $thumbnails->perPage();
@endphp
    <div class="row">
        @foreach ($thumbnails as $thumbnail)
                @php
                    $thumbnailName = $thumbnail->getRelativePathname();
                    $pdfName = substr($thumbnailName, 0, -4);
                @endphp
                <div class="column">
                    <div class="image-container">
                        <img src="{{asset('files/thumbnails') . '/' . $thumbnailName}}" width="100%">
                        <a href='{{asset('files/pdf') . '/' . $pdfName . '.pdf'}}'
                           class="after view-pdf" data-file_name = "{{$pdfName}}">{{ $thumbnailName }}</a>
                    </div>
                </div>
        @endforeach
    </div>
{{ $thumbnails->appends(request()->input())->links() }}