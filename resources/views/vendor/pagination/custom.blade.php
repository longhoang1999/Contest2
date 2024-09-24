@if ($paginator->hasPages())
    <ul class="pagination">
        {{-- Nút về trang đầu --}}
        @if ($paginator->onFirstPage())
            <li class="disabled"><span>&laquo; Đầu</span></li>
        @else
            <li><a href="{{ $paginator->url(1) }}">&laquo; Đầu</a></li>
        @endif

        {{-- Nút Previous --}}
        @if ($paginator->previousPageUrl())
            <li><a href="{{ $paginator->previousPageUrl() }}">«</a></li>
        @else
            <li class="disabled"><span>«</span></li>
        @endif

        {{-- Các số trang --}}
        @foreach ($elements as $element)
            {{-- Nếu là một chuỗi, hiển thị nó --}}
            @if (is_string($element))
                <li class="disabled"><span>{{ $element }}</span></li>
            @endif

            {{-- Nếu là một mảng các trang --}}
            @if (is_array($element))
                @foreach ($element as $page => $url)
                    @if ($page == $paginator->currentPage())
                        <li class="active"><span>{{ $page }}</span></li>
                    @else
                        <li><a href="{{ $url }}">{{ $page }}</a></li>
                    @endif
                @endforeach
            @endif
        @endforeach

        {{-- Nút Next --}}
        @if ($paginator->nextPageUrl())
            <li><a href="{{ $paginator->nextPageUrl() }}">»</a></li>
        @else
            <li class="disabled"><span>»</span></li>
        @endif

        {{-- Nút về trang cuối --}}
        @if ($paginator->hasMorePages())
            <li><a href="{{ $paginator->url($paginator->lastPage()) }}">Cuối &raquo;</a></li>
        @else
            <li class="disabled"><span>Cuối &raquo;</span></li>
        @endif
    </ul>
@endif
