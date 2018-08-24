@php($config = config('laravelcategory'))

<nav aria-label="breadcrumb">
    <ol class="breadcrumb">
        <li class="breadcrumb-item">
            <a href="{{route($config['route']['name'])}}">
                {{$config['main_level']}}
            </a>
        </li>
        @isset($breadcrumbs)
            @foreach($breadcrumbs as $breadcrumb)
                @if ($loop->last and empty($append))
                    <li class="breadcrumb-item active">{{$breadcrumb['name']}}</li>
                @elseif($loop->last and !empty($append))
                    <li class="breadcrumb-item active">{{$append}}</li>
                @else
                    <li class="breadcrumb-item">
                        <a href="{{route($config['route']['name'], [$config['route']['params']['slug'] => $breadcrumb['slug']])}}">
                            {{$breadcrumb['name']}}
                        </a>
                    </li>
                @endif
            @endforeach
        @endisset
    </ol>
</nav>
