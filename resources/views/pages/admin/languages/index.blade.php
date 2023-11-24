@php use App\Enums\Permissions;use App\Models\Language; @endphp

@extends('layouts.admin')

@section('h1', "Список языков программирования")

@section('content')
    @can((Permissions\Languages::CREATE)->code(), Language::class)
        <div class="d-flex justify-content-end mb-2">
            <a href="{{route('admin.languages.create')}}" class="btn btn-success">Добавить</a>
        </div>
    @endcan

    @if(count($languages) > 0)
        <table class="table table-hover">
            <thead>
            <tr>
                <th scope="col"></th>
                <th scope="col">id</th>
                <th scope="col">slug</th>
                <th scope="col">title</th>
                <th scope="col">description</th>
                <th scope="col">created_at</th>
                <th scope="col">updated_at</th>
            </tr>
            </thead>
            <tbody>
            @foreach($languages as $language)
                <tr>
                    <th scope="row">
                        <div class="dropdown-center">
                            <button class="btn btn-primary" type="button" data-bs-toggle="dropdown"
                                    aria-expanded="false">
                                <svg width="16" height="16" fill="currentColor" class="bi bi-list">
                                    <use xlink:href="#burger"></use>
                                </svg>
                            </button>
                            <ul class="dropdown-menu">
                                @can((Permissions\Languages::UPDATE)->code(), $language)
                                    <li><a class="dropdown-item"
                                           href="{{route('admin.languages.edit', $language->id)}}">Редактировать</a>
                                    </li>
                                @endcan
                                <li><a class="dropdown-item" href="{{route('admin.languages.show', $language->id)}}">Просмотр</a>
                                </li>
                                @can((Permissions\Languages::DELETE)->code(), $language)
                                    <li class="border-top my-3"></li>
                                    <li>
                                        <form
                                            action="{{route('admin.languages.destroy', ['language' => $language->id])}}"
                                            method="POST">
                                            @method('DELETE')
                                            @csrf
                                            <button class="dropdown-item">Удалить</button>
                                        </form>
                                    </li>
                                @endcan
                            </ul>
                        </div>
                    </th>
                    <th class="align-middle">{{$language->id}}</th>
                    <td class="align-middle">{{$language->slug}}</td>
                    <td class="align-middle">{{$language->title}}</td>
                    <td class="align-middle">{{$language->description}}</td>
                    <td class="align-middle">{{$language->created_at}}</td>
                    <td class="align-middle">{{$language->updated_at}}</td>
                </tr>
            @endforeach
            </tbody>
        </table>
        {{$languages->links('components.pagination', ['collection' => $languages])}}
    @else
        <div class="alert alert-info" role="alert">
            Пока нет никаких записей
        </div>
    @endif
@endsection
