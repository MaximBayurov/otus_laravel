@extends('layouts.public')

@section('h1', __('register.registration'))

@section('head-bottom')
    @parent
    <style>

        .form-sign-up {
            min-width: 100%;
            padding: 1rem;
        }

        @media (min-width: 768px) {
            .form-sign-up {
                min-width: 400px;
            }
        }

        .form-sign-up .form-floating:focus-within {
            z-index: 2;
        }

        #passwordInput {
            margin-bottom: -1px;
            border-bottom-right-radius: 0;
            border-bottom-left-radius: 0;
        }

        #passwordRepeatInput {
            margin-bottom: 10px;
            border-top-left-radius: 0;
            border-top-right-radius: 0;
        }
    </style>
@endsection

@section('content')
    <section class="container d-flex justify-content-center">
        <form class="form-sign-up d-flex flex-column gap-4" method="POST" action="{{route('register')}}">
            @method('POST')
            @csrf
            <h2 class="h3 mb-0 fw-normal">{{__('register.please_sign_up')}}</h2>
            @if($errors->any())
                <div class="alert alert-danger" role="alert">
                    {!! implode('<br>', $errors->all(':message')) !!}
                </div>
            @endif
            <x-forms.input type="text" name="name" placeholder="name@example.com" id="floatingInput"
                           :value="old('name')" label="{{__('forms.name')}}"/>
            <x-forms.input type="email" name="email" placeholder="name@example.com" id="floatingInput"
                           :value="old('email')" label="{{__('forms.email')}}"/>
            <div>
                <x-forms.input type="password" name="password" id="passwordInput" label="{{__('forms.password')}}"/>
                <x-forms.input type="password" name="password_confirmation" id="passwordRepeatInput" label="{{__('forms.password_confirmation')}}"/>
            </div>

            <button class="btn btn-primary w-100 py-2" type="submit">{{__('register.submit')}}</button>
        </form>
    </section>
@endsection

