@extends('layouts.public')

@section('h1', 'Профиль')

@section('content')
    <section class="container mb-3 pt-5">
        <h2 class="display-6">Информация о пользователе</h2>
        <form class="mb-3">
            <x-forms.input placeholder="name@example.com" id="floatingInput" name="email" type="email"
                           label="{{__('forms.email')}}" value="example@mail.ru" readonly="{{true}}"/>
            <x-forms.input placeholder="name@example.com" id="floatingInput" name="phone" type="tel"
                           label="{{__('forms.phone')}}" value="+7-999-999-99-99" readonly="{{true}}"/>
        </form>
        <section>
            <h3>Информация о подписке</h3>
            <div class="card text-center">
                <div class="card-header">
                    Бесплатная
                </div>
                <div class="card-body">
                    <h4 class="card-title">Продлить подписку</h4>
                    <p class="card-text">Lorem ipsum dolor sit amet, consectetuer adipiscing elit. Aenean commodo ligula eget dolor. Aenean massa. Cum sociis natoque penatibus et magnis dis parturient montes, nascetur ridiculus mus.</p>
                    <a href="#" class="btn btn-primary">Оформить</a>
                </div>
                <div class="card-footer text-body-secondary">
                    Действительна до 20.20.2026
                </div>
            </div>
        </section>
    </section>
    <section class="container mb-3">
        <h2 class="display-6">Недавние запросы</h2>
        <div class="row">
            <div class="col-md-6 mb-3">
                <div class="card text-start">
                    <div class="card-body">
                        <h3 class="card-title">Special title treatment</h3>
                        <p class="card-text">With supporting text below as a natural lead-in to additional content.</p>
                    </div>
                    <div class="card-footer text-body-secondary">
                        2 days ago
                    </div>
                </div>
            </div>
            <div class="col-md-6 mb-3">
                <div class="card text-start">
                    <div class="card-body">
                        <h3 class="card-title">Special title treatment</h3>
                        <p class="card-text">With supporting text below as a natural lead-in to additional content.</p>
                    </div>
                    <div class="card-footer text-body-secondary">
                        2 days ago
                    </div>
                </div>
            </div>
        </div>
    </section>
    <section class="container mb-3">
        <h2 class="display-6">История запросов</h2>
        <p class="fs-5">Здесь отображается история ваших запросов</p>
    </section>
@endsection

