@isset($categories)

    @php($config = config('laravelcategory'))

    <div class="row">
        @foreach($categories as $category)
            <div class="col-lg-4 col-md-6">
                <div class="card my-3 laravelcategorycard">
                    <a href="{{route($config['route']['name'], [$config['route']['params']['slug'] => $category['slug']])}}">
                        @empty($category->img)
                            <img class="card-img" src="{{$config['empty_category_image']}}" alt="{{$category->name}}">
                        @else
                            <img class="card-img" src="{{asset($config['storage'].$category->img)}}"
                                 alt="{{$category->name}}">
                        @endempty

                        <div class="text">
                            <h5 class="text-center text-dark">
                                {{$category->name}}
                            </h5>
                        </div>
                    </a>
                </div>
            </div>
        @endforeach
    </div>

@endisset