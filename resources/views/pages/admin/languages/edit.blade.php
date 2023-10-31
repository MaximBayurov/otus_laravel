@php use App\Enums\Permissions; @endphp
@extends('layouts.admin')

@section('head-bottom')
    @parent
    <script type="text/javascript" src="{{ URL::asset('js/admin/AdminCardsList.js') }}"></script>
@endsection

@section('h1', 'Редактирование языка программирования')

<?php
$additionalClasses = ['my-3'];
?>

@section('content')
    <form action="{{route('admin.languages.update', ['language' => $language->id])}}" method="POST" class="px-3" id="edit-form">
        @method('PATCH')
        @csrf
        <div class="mb-4">
            <h2>Данные языка программирования</h2>
            <input type="hidden" name="id" value="{{$language->id}}"/>
            <x-forms.input
                type="text" placeholder="Символьный идентификатор" id="slug"
                name="slug" value="{{old('slug') ?? $language->slug}}" :additional-classes="$additionalClasses"
                label="Символьный идентификатор" :error="$errors->default->first('slug')"/>
            <x-forms.input
                type="text" placeholder="Название" id="title"
                name="title" value="{{old('title') ?? $language->title}}" :additional-classes="$additionalClasses"
                label="Название" :error="$errors->default->first('title')"/>
            <x-forms.textarea
                type="string" placeholder="Описание" id="description"
                name="description" value="{{old('description') ?? $language->description}}" :additional-classes="$additionalClasses"
                label="Описание" :error="$errors->default->first('description')"/>
        </div>
        <div class="mb-4">
            <h2>Данные языковых конструкций языка</h2>
            <p class="alert alert-info mb-3">Не обязательно к заполнению.</p>
            <div class="mb-3 d-flex flex-column gap-4" data-cards-list>
                @if(empty($constructions))
                    <div class="p-3 bg-body rounded shadow-sm d-flex gap-3" data-card>
                        <div class="d-flex flex-column gap-3">
                            <button class="btn btn-danger" type="button" data-remove-card-button>
                                <svg  width="16" height="16" fill="currentColor" class="bi bi-list" ><use xlink:href="#trash"></use></svg>
                            </button>
                        </div>
                        <div class="d-flex flex-column gap-3 col">
                            <x-forms.select
                                id="construction-id-0" name="constructions[0][id]"
                                label="Языковая конструкция" :options="$constructionOptions"/>
                            <x-forms.textarea
                                type="string" placeholder="Реализация конструкции в языке" id="construction-code-0"
                                name="constructions[0][code]"
                                label="Реализация конструкции в языке" height="100"/>
                        </div>
                    </div>
                @else
                    @foreach($constructions as $oldConstruction)
                        <div class="p-3 bg-body rounded shadow-sm d-flex gap-3" data-card>
                            <div class="d-flex flex-column gap-3">
                                <button class="btn btn-danger" type="button" data-remove-card-button>
                                    <svg  width="16" height="16" fill="currentColor" class="bi bi-list" ><use xlink:href="#trash"></use></svg>
                                </button>
                            </div>
                            <div class="d-flex flex-column gap-3 col">
                                <x-forms.select
                                    id="construction-id-{{$loop->index}}" name="constructions[{{$loop->index}}][id]"
                                    label="Языковая конструкция" :options="$constructionOptions" :value="$oldConstruction['id']"
                                    :error="$errors->default->first('constructions.'.$loop->index.'.id')"/>
                                <x-forms.textarea
                                    type="string" placeholder="Реализация конструкции в языке" id="construction-code-{{$loop->index}}"
                                    name="constructions[{{$loop->index}}][code]" :value="$oldConstruction['code']"
                                    label="Реализация конструкции в языке" height="100"
                                    :error="$errors->default->first('constructions.'.$loop->index.'.code')"/>
                            </div>
                        </div>
                    @endforeach
                @endif
            </div>
            <button class="btn btn-success" data-add-card-button type="button">Добавить</button>
        </div>
    </form>
    <div class="d-flex justify-content-between">
        <a class="btn btn-secondary" href="{{route('admin.languages.index')}}">Обратно в список</a>
        <div class="d-flex flex-row gap-3">
            <button class="btn btn-primary" type="submit" id="edit-form">Сохранить</button>
            <a class="btn btn-secondary" href="{{route('admin.languages.show', ['language' => $language->id])}}">Просмотр</a>
            @can((Permissions\Languages::DELETE)->code(), $language)
            <form action="{{route('admin.languages.destroy', ['language' => $language->id])}}" method="POST" id="delete-form">
                @method('DELETE')
                @csrf
                <button class="btn btn-danger" form="delete-form">Удалить</button>
            </form>
            @endcan
        </div>
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
