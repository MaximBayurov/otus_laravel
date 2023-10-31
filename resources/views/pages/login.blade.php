@extends('layouts.public')

@section('h1', __('auth.authorization'))

@section('head-bottom')
    @parent
    <style>

        .form-signin {
            max-width: 100%;
            padding: 1rem;
        }

        @media (min-width: 768px) {
            .form-signin {
                max-width: 600px;
            }
        }

        .form-signin .form-floating:focus-within {
            z-index: 2;
        }

        .form-signin input[type="email"] {
            margin-bottom: -1px;
            border-bottom-right-radius: 0;
            border-bottom-left-radius: 0;
        }

        .form-signin input[type="password"] {
            margin-bottom: 10px;
            border-top-left-radius: 0;
            border-top-right-radius: 0;
        }
    </style>
@endsection

@section('content')
    <section class="container d-flex justify-content-center">
        <form class="form-signin" method="POST" action="{{route('login')}}">
            @method('POST')
            @csrf
            <h2 class="h3 mb-3 fw-normal">{{__('auth.please_sign_in')}}</h2>
            @if($errors->any())
                <div class="alert alert-danger" role="alert">
                    {!! implode('<br>', $errors->all(':message')) !!}
                </div>
            @endif
            <x-forms.input type="email" placeholder="name@example.com" id="floatingInput" name="email"
                           label="{{__('forms.email')}}" :value="old('email')"/>
            <x-forms.input type="password" placeholder="{{__('forms.password-placeholder')}}" id="floatingPassword"
                           name="password" label="{{__('forms.password')}}"/>

            <x-forms.checkbox id="flexCheckDefault" label="{{__('forms.remember')}}" :value="old('remember', 'true')"
                              name="remember"/>

            <button class="btn btn-primary w-100 py-2" type="submit">{{__('auth.submit')}}</button>
        </form>
    </section>
@endsection

