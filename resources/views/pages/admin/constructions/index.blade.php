@php use App\Enums\Permissions;use App\Models\Construction; @endphp
@extends('layouts.admin')

@section('h1', "Список языковых конструкций")

@section('content')
    @can((Permissions\Constructions::CREATE)->code(), Construction::class)
        <div class="d-flex justify-content-end mb-2">
            <a href="{{route('admin.constructions.create')}}" class="btn btn-success">Добавить</a>
        </div>
    @endcan
    @if(count($constructions) > 0)
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
            @foreach($constructions as $construction)
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
                                @can((Permissions\Constructions::UPDATE)->code(), $construction)
                                    <li><a class="dropdown-item"
                                           href="{{route('admin.constructions.edit', $construction->id)}}">Редактировать</a>
                                    </li>
                                @endcan
                                <li><a class="dropdown-item"
                                       href="{{route('admin.constructions.show', $construction->id)}}">Просмотр</a>
                                </li>
                                @can((Permissions\Constructions::DELETE)->code(), $construction)
                                    <li class="border-top my-3"></li>
                                    <li>
                                        <form
                                            action="{{route('admin.constructions.destroy', ['construction' => $construction->id])}}"
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
                    <th class="align-middle">{{$construction->id}}</th>
                    <td class="align-middle">{{$construction->slug}}</td>
                    <td class="align-middle">{{$construction->title}}</td>
                    <td class="align-middle">{{$construction->description}}</td>
                    <td class="align-middle">{{$construction->created_at}}</td>
                    <td class="align-middle">{{$construction->updated_at}}</td>
                </tr>
            @endforeach
            </tbody>
        </table>
        {{$constructions->links('components.pagination', ['collection' => $constructions])}}
    @else
        <div class="alert alert-info" role="alert">
            Пока нет никаких записей
        </div>
    @endif
@endsection
