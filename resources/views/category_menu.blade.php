<ul class="list-group">

    @php($config = config('laravelcategory'))

    <li class="list-group-item active">
        @isset($category)
            {{$category->name}}
        @else
            {{$config['main_level']}}
        @endisset
    </li>

    @foreach($categories as $category)
        <a class="list-group-item list-group-item-action"
           href="{{route($config['route']['name'], [$config['route']['params']['slug'] => $category->slug])}}"
        >
            {{$category->name}}
        </a>
    @endforeach

</ul>
