@extends('layouts.base')

@section('html-class', 'h-100')
@section('body-class') bg-white d-flex flex-column h-100 @endsection
@section('head-bottom')
    @vite('resources/sass/app.scss')
    @vite('resources/js/app.js')
@endsection

@section('body')
    @section('svg')
    <svg xmlns="http://www.w3.org/2000/svg" class="d-none">
        <symbol id="bootstrap" viewBox="0 0 118 94">
            <title>Bootstrap</title>
            <path fill-rule="evenodd" clip-rule="evenodd" d="M24.509 0c-6.733 0-11.715 5.893-11.492 12.284.214 6.14-.064 14.092-2.066 20.577C8.943 39.365 5.547 43.485 0 44.014v5.972c5.547.529 8.943 4.649 10.951 11.153 2.002 6.485 2.28 14.437 2.066 20.577C12.794 88.106 17.776 94 24.51 94H93.5c6.733 0 11.714-5.893 11.491-12.284-.214-6.14.064-14.092 2.066-20.577 2.009-6.504 5.396-10.624 10.943-11.153v-5.972c-5.547-.529-8.934-4.649-10.943-11.153-2.002-6.484-2.28-14.437-2.066-20.577C105.214 5.894 100.233 0 93.5 0H24.508zM80 57.863C80 66.663 73.436 72 62.543 72H44a2 2 0 01-2-2V24a2 2 0 012-2h18.437c9.083 0 15.044 4.92 15.044 12.474 0 5.302-4.01 10.049-9.119 10.88v.277C75.317 46.394 80 51.21 80 57.863zM60.521 28.34H49.948v14.934h8.905c6.884 0 10.68-2.772 10.68-7.727 0-4.643-3.264-7.207-9.012-7.207zM49.948 49.2v16.458H60.91c7.167 0 10.964-2.876 10.964-8.281 0-5.406-3.903-8.178-11.425-8.178H49.948z"></path>
        </symbol>
    </svg>
    @show
    <header class="p-3 border-bottom bg-light">
        <div class="container">
            <div class="d-flex flex-wrap align-items-center justify-content-between">
                <a href="/" class="d-flex align-items-center mb-2 mb-lg-0 link-body-emphasis text-decoration-none">
                    <svg class="bi me-2" width="40" height="32" role="img" aria-label="Bootstrap"><use xlink:href="#bootstrap"></use></svg>
                </a>

                @auth
                <div class="dropdown text-end">
                    <a href="#" class="d-block link-body-emphasis text-decoration-none dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">
                        <img src="https://github.com/mdo.png" alt="mdo" width="32" height="32" class="rounded-circle">
                    </a>
                    <ul class="dropdown-menu text-small">
                        <li><a class="dropdown-item" href="/profile">Profile</a></li>
                        <li><hr class="dropdown-divider"></li>
                        <li><a class="dropdown-item" href="#">Sign out</a></li>
                    </ul>
                </div>
                @else
                <div class="text-end">
                    <a href="/login" class="btn btn-outline-primary me-2">Вход</a>
                    <a href="/register" class="btn btn-primary">Регистрация</a>
                </div>
                @endauth
            </div>
        </div>
    </header>
    <main>
        @yield('content')
    </main>

    <footer class="footer mt-auto pt-3 pb-5 bg-light border-top" id="footer">
        <h1 class="container display-4 fw-normal text-body-emphasis mb-3">@yield('h1', 'Без названия')</h1>

        <div class="container d-flex flex-column flex-xl-row justify-content-between gap-4 mb-4">
            <div class="container row row-cols-1 row-cols-sm-2 row-cols-lg-3 row-cols-xl-2 row-cols-xxl-3 p-0">
                <div class="col mb-3">
                    <h5>Основные</h5>
                    <ul class="nav flex-column">
                        <li class="nav-item mb-2"><a href="/" class="nav-link p-0 text-body-secondary">Главная</a></li>
                        <li class="nav-item mb-2"><a href="/about" class="nav-link p-0 text-body-secondary">О нас</a></li>
                    </ul>
                </div>
            </div>

            <form class="col-xl-4">
                <h5>Подпишитесь на рассылку</h5>
                <p>Ежемесячная рассылка о всех последних новостях</p>
                <div class="d-flex flex-column flex-sm-row w-100 gap-2">
                    <label for="newsletter1" class="visually-hidden">E-mail</label>
                    <input id="newsletter1" type="text" class="form-control" placeholder="E-mail">
                    <button class="btn btn-primary" type="button">Подписаться</button>
                </div>
            </form>
        </div>
    </footer>
@endsection
