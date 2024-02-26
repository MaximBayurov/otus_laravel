@php
    use App\Enums\Permissions;use App\Models\Construction;use App\Models\Language;
@endphp
@extends('layouts.base')

@section('head-bottom')
    @vite('resources/sass/admin.scss')
    @vite('resources/js/admin.js')
    <link rel="stylesheet" href="{{ URL::asset('css/admin/style.css') }}">
@endsection
@section('body')
    @section('svg')
        <svg xmlns="http://www.w3.org/2000/svg" class="d-none">
            <symbol id="bootstrap" viewBox="0 0 118 94">
                <title>Bootstrap</title>
                <path fill-rule="evenodd" clip-rule="evenodd"
                      d="M24.509 0c-6.733 0-11.715 5.893-11.492 12.284.214 6.14-.064 14.092-2.066 20.577C8.943 39.365 5.547 43.485 0 44.014v5.972c5.547.529 8.943 4.649 10.951 11.153 2.002 6.485 2.28 14.437 2.066 20.577C12.794 88.106 17.776 94 24.51 94H93.5c6.733 0 11.714-5.893 11.491-12.284-.214-6.14.064-14.092 2.066-20.577 2.009-6.504 5.396-10.624 10.943-11.153v-5.972c-5.547-.529-8.934-4.649-10.943-11.153-2.002-6.484-2.28-14.437-2.066-20.577C105.214 5.894 100.233 0 93.5 0H24.508zM80 57.863C80 66.663 73.436 72 62.543 72H44a2 2 0 01-2-2V24a2 2 0 012-2h18.437c9.083 0 15.044 4.92 15.044 12.474 0 5.302-4.01 10.049-9.119 10.88v.277C75.317 46.394 80 51.21 80 57.863zM60.521 28.34H49.948v14.934h8.905c6.884 0 10.68-2.772 10.68-7.727 0-4.643-3.264-7.207-9.012-7.207zM49.948 49.2v16.458H60.91c7.167 0 10.964-2.876 10.964-8.281 0-5.406-3.903-8.178-11.425-8.178H49.948z"></path>
            </symbol>
            <symbol viewBox="0 0 16 16" id="burger">
                <title>Burger</title>
                <path fill-rule="evenodd"
                      d="M2.5 12a.5.5 0 0 1 .5-.5h10a.5.5 0 0 1 0 1H3a.5.5 0 0 1-.5-.5zm0-4a.5.5 0 0 1 .5-.5h10a.5.5 0 0 1 0 1H3a.5.5 0 0 1-.5-.5zm0-4a.5.5 0 0 1 .5-.5h10a.5.5 0 0 1 0 1H3a.5.5 0 0 1-.5-.5z"/>
            </symbol>
            <symbol viewBox="0 0 16 16" id="trash">
                <title>Trash</title>
                <path
                    d="M5.5 5.5A.5.5 0 0 1 6 6v6a.5.5 0 0 1-1 0V6a.5.5 0 0 1 .5-.5Zm2.5 0a.5.5 0 0 1 .5.5v6a.5.5 0 0 1-1 0V6a.5.5 0 0 1 .5-.5Zm3 .5a.5.5 0 0 0-1 0v6a.5.5 0 0 0 1 0V6Z"/>
                <path
                    d="M14.5 3a1 1 0 0 1-1 1H13v9a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V4h-.5a1 1 0 0 1-1-1V2a1 1 0 0 1 1-1H6a1 1 0 0 1 1-1h2a1 1 0 0 1 1 1h3.5a1 1 0 0 1 1 1v1ZM4.118 4 4 4.059V13a1 1 0 0 0 1 1h6a1 1 0 0 0 1-1V4.059L11.882 4H4.118ZM2.5 3h11V2h-11v1Z"/>
            </symbol>
        </svg>
    @show
    <main class="d-flex flex-nowrap">
        <div class="flex-shrink-0 p-3" style="width: 280px;">
            <a href="/admin"
               class="d-flex align-items-center pb-3 mb-3 link-body-emphasis text-decoration-none border-bottom">
                <svg class="bi pe-none me-2" width="30" height="24">
                    <use xlink:href="#bootstrap"></use>
                </svg>
                <span class="fs-5 fw-semibold"></span>
            </a>
            <ul class="list-unstyled ps-0">
                @can('viewAny', Language::class)
                    <li class="mb-1">
                        <button class="btn btn-toggle d-inline-flex align-items-center rounded border-0 collapsed"
                                data-bs-toggle="collapse" data-bs-target="#languages-collapse" aria-expanded="false">
                            Языки программирования
                        </button>
                        <div class="collapse" id="languages-collapse" style="">
                            <ul class="btn-toggle-nav list-unstyled fw-normal pb-1 small">
                                <li><a href="{{route('admin.languages.index')}}"
                                       class="link-body-emphasis d-inline-flex text-decoration-none rounded">Список</a>
                                </li>
                            </ul>
                        </div>
                    </li>
                @endcan
                @can('viewAny', Construction::class)
                    <li class="mb-1">
                        <button class="btn btn-toggle d-inline-flex align-items-center rounded border-0 collapsed"
                                data-bs-toggle="collapse" data-bs-target="#constructions-collapse"
                                aria-expanded="false">
                            Языковые конструкции
                        </button>
                        <div class="collapse" id="constructions-collapse" style="">
                            <ul class="btn-toggle-nav list-unstyled fw-normal pb-1 small">
                                <li><a href="{{route('admin.constructions.index')}}"
                                       class="link-body-emphasis d-inline-flex text-decoration-none rounded">Список</a>
                                </li>
                            </ul>
                        </div>
                    </li>
                @endcan

                @can('admin.export')
                <li class="mb-1">
                    <a href="{{route('admin.export.index')}}" class="btn btn-toggle btn-toggle--no-arrow d-inline-flex align-items-center rounded border-0 collapsed">
                        Экспорт
                    </a>
                </li>
                @endcan
                @can('admin.import')
                <li class="mb-1">
                    <a href="{{route('admin.import.index')}}" class="btn btn-toggle btn-toggle--no-arrow d-inline-flex align-items-center rounded border-0 collapsed">
                        Импорт
                    </a>
                </li>
                @endcan
                <li class="border-top my-3"></li>
                <li class="mb-1">
                    <button class="btn btn-toggle d-inline-flex align-items-center rounded border-0 collapsed"
                            data-bs-toggle="collapse" data-bs-target="#account-collapse" aria-expanded="false">
                        Аккаунт
                    </button>
                    <div class="collapse" id="account-collapse" style="">
                        <ul class="btn-toggle-nav list-unstyled fw-normal pb-1 small">
                            <li><a href="#" class="link-body-emphasis d-inline-flex text-decoration-none rounded">Профиль</a>
                            </li>
                            <li><a class="link-body-emphasis d-inline-flex text-decoration-none rounded"
                                   style="cursor: pointer" id="logout-link">Выйти</a></li>

                            <form action="{{route('logout')}}" method="POST" class="visually-hidden"
                                  id="logout-form">
                                @csrf
                                @method('POST')
                                <button type="submit">Выйти</button>
                            </form>
                        </ul>
                        <script type="module">
                            $(() => {
                                $('#logout-link').on('click', (event) => {
                                    event.preventDefault();
                                    $('#logout-form').trigger('submit');
                                })
                            })
                        </script>
                    </div>
                </li>
            </ul>
        </div>
        <div class="b-example-divider b-example-vr"></div>
        <div class="container d-flex flex-column p-3 admin-content-area overflow-auto">
            <h1 class="mb-3">
                @yield('h1', 'Административный раздел')
            </h1>
            @if(session()->has('error'))
                <div class="alert alert-danger">
                    {{session()->get('error')}}
                </div>
            @endif
            <div class="d-flex flex-column vh-100">
                @if(session()->has('alert-success'))
                    <div class="alert alert-success">
                        {{session('alert-success')}}
                    </div>
                @endif
                @yield('content')
            </div>
        </div>
    </main>
    @yield('java-script')
@endsection
