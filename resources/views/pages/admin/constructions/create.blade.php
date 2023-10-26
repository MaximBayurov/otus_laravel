@extends('layouts.admin')

@section('h1', 'Добавление языковой конструкции')

@section('head-bottom')
    @parent
    <script type="text/javascript" src="{{ URL::asset('js/admin/AdminCardsList.js') }}"></script>
@endsection

<?php
    $additionalClasses = ['my-3'];
?>

@section('content')
    <form action="{{route('admin.constructions.store')}}" method="POST" class="px-3" id="create-form">
        @method('POST')
        @csrf
        <div class="mb-4">
            <h2>Данные языковой конструкции</h2>
            <x-forms.input
                type="text" placeholder="Символьный идентификатор" id="slug"
                name="slug" value="{{old('slug')}}" :additional-classes="$additionalClasses"
                label="Символьный идентификатор" :error="$errors->default->first('slug')">
            </x-forms.input>
            <x-forms.input
                type="text" placeholder="Название" id="title"
                name="title" value="{{old('title')}}" :additional-classes="$additionalClasses"
                label="Название" :error="$errors->default->first('title')"/>
            <x-forms.textarea
                type="string" placeholder="Описание" id="description"
                name="description" value="{{old('description')}}" :additional-classes="$additionalClasses"
                label="Описание" :error="$errors->default->first('description')"/>
        </div>
        <div class="mb-4">
            <h2>Реализация конструкции в языках</h2>
            <p class="alert alert-info mb-3">Не обязательно к заполнению. Вы также сможете добавить реализации конструкций в языке позже, после создания записи</p>
            <div class="mb-3 d-flex flex-column gap-4" data-cards-list>
                @if(empty($languages))
                    <div class="p-3 bg-body rounded shadow-sm d-flex gap-3" data-card>
                        <div class="d-flex flex-column gap-3">
                            <button class="btn btn-danger" type="button" data-remove-card-button>
                                <svg  width="16" height="16" fill="currentColor" class="bi bi-list" ><use xlink:href="#trash"></use></svg>
                            </button>
                        </div>
                        <div class="d-flex flex-column gap-3 col">
                            <x-forms.select
                                id="language-id-0" name="languages[0][id]"
                                label="Язык программирования" :options="$languageOptions"/>
                            <x-forms.textarea
                                type="string" placeholder="Реализация конструкции в языке" id="language-code-0"
                                name="languages[0][code]"
                                label="Реализация конструкции в языке" height="100"/>
                        </div>
                    </div>
                @else
                    @foreach($languages as $oldLanguage)
                        <div class="p-3 bg-body rounded shadow-sm d-flex gap-3" data-card>
                            <div class="d-flex flex-column gap-3">
                                <button class="btn btn-danger" type="button" data-remove-card-button>
                                    <svg  width="16" height="16" fill="currentColor" class="bi bi-list" ><use xlink:href="#trash"></use></svg>
                                </button>
                            </div>
                            <div class="d-flex flex-column gap-3 col">
                                <x-forms.select
                                    id="language-id-{{$loop->index}}" name="languages[{{$loop->index}}][id]"
                                    label="Язык программирования" :options="$languageOptions" :value="$oldLanguage['id']"
                                    :error="$errors->default->first('languages.'.$loop->index.'.id')"/>
                                <x-forms.textarea
                                    type="string" placeholder="Реализация конструкции в языке" id="language-code-{{$loop->index}}"
                                    name="languages[{{$loop->index}}][code]" :value="$oldLanguage['code']"
                                    label="Реализация конструкции в языке" height="100"
                                    :error="$errors->default->first('languages.'.$loop->index.'.code')"/>
                            </div>
                        </div>
                    @endforeach
                @endif
            </div>
            <button class="btn btn-success" data-add-card-button type="button">Добавить</button>
        </div>
    </form>
    <div class="d-flex justify-content-between">
        <a class="btn btn-secondary" href="{{route('admin.constructions.index')}}">Обратно в список</a>
        <button class="btn btn-primary" type="submit" form="create-form">Создать</button>
    </div>
@endsection

@section('java-script')
    <script type="module">
        $(() => {
            let cardsList = new AdminCardsList(
                $('[data-cards-list]'),
                '[data-card]'
            );
            $('[data-add-card-button]').on('click', (event) => {
                cardsList.onAddCardButtonClick(event);
            });
            $(document).on('click', '[data-remove-card-button]', (event) => {
                cardsList.onRemoveCardButtonClick(event);
            });
        })
    </script>
@endsection
