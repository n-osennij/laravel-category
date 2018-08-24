<?php
    $config = config('laravelcategory');

    if(!empty($append)) {
        $breadcrumbs[] = [
            'name' => $append,
            'slug' => '',
        ];
    }

?>

<nav aria-label="breadcrumb">
    <ol class="breadcrumb">
        <li class="breadcrumb-item">
            <a href="{{route($config['route']['name'])}}">
                {{$config['main_level']}}
            </a>
        </li>
        @isset($breadcrumbs)
            @foreach($breadcrumbs as $breadcrumb)
                @php($route = route($config['route']['name'], [$config['route']['params']['slug'] => $breadcrumb['slug']]))
                @if ($loop->last)
                    <li class="breadcrumb-item active">{{$breadcrumb['name']}}</li>
                @else
                    <li class="breadcrumb-item">
                        <a href="{{$route}}">
                            {{$breadcrumb['name']}}
                        </a>
                    </li>
                @endif
            @endforeach
        @endisset
    </ol>
</nav>
