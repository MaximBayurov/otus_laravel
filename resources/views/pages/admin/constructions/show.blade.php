@extends('layouts.admin')

@section('h1', 'Просмотр языковой конструкции')

<?php
$additionalClasses = ['my-3'];
?>

@section('content')
    <form class="px-3">
        <div class="mb-4">
            <h2>Данные языка программирования</h2>
            <input type="hidden" name="id" value="{{$construction->id}}"/>
            <x-forms.input
                type="text" placeholder="Символьный идентификатор" id="slug" :readonly="true"
                name="slug" value="{{old('slug') ?? $construction->slug}}" :additional-classes="$additionalClasses"
                label="Символьный идентификатор" :error="$errors->default->first('slug')"/>
            <x-forms.input
                type="text" placeholder="Название" id="title" :readonly="true"
                name="title" value="{{old('title') ?? $construction->title}}" :additional-classes="$additionalClasses"
                label="Название" :error="$errors->default->first('title')"/>
            <x-forms.textarea
                type="string" placeholder="Описание" id="description" :readonly="true"
                name="description" value="{{old('description') ?? $construction->description}}" :additional-classes="$additionalClasses"
                label="Описание" :error="$errors->default->first('description')"/>
        </div>
        <div class="mb-4">
            <h2>Данные языковых конструкций языка</h2>
            <div class="mb-3 d-flex flex-column gap-4" data-cards-list>
                @if(empty($languages))
                    <div class="p-3 bg-body rounded shadow-sm d-flex gap-3" data-card>
                        <div class="d-flex flex-column gap-3 col">
                            <x-forms.select
                                id="language-id-0" name="language[0][id]" :disabled="true"
                                label="Язык программирования" :options="$languageOptions"/>
                            <x-forms.textarea
                                type="string" placeholder="Реализация конструкции в языке" id="language-code-0"
                                name="language[0][code]" :readonly="true"
                                label="Реализация конструкции в языке" height="100"/>
                        </div>
                    </div>
                @else
                    @foreach($languages as $oldLanguage)
                        <div class="p-3 bg-body rounded shadow-sm d-flex gap-3" data-card>
                            <div class="d-flex flex-column gap-3 col">
                                <x-forms.select
                                    id="language-id-{{$loop->index}}" name="languages[{{$loop->index}}][id]"
                                    label="Язык программирования" :options="$languageOptions" :value="$oldLanguage['id']"
                                    :error="$errors->default->first('languages.'.$loop->index.'.id')" :disabled="true"/>
                                <x-forms.textarea
                                    type="string" placeholder="Реализация конструкции в языке" id="language-code-{{$loop->index}}"
                                    name="languages[{{$loop->index}}][code]" :value="$oldLanguage['code']"
                                    label="Реализация конструкции в языке" height="100"
                                    :error="$errors->default->first('languages.'.$loop->index.'.code')" :readonly="true"/>
                            </div>
                        </div>
                    @endforeach
                @endif
            </div>
        </div>
    </form>
    <div class="d-flex justify-content-between">
        <a class="btn btn-secondary" href="{{route('admin.constructions.index')}}">Обратно в список</a>
        <div class="d-flex flex-row gap-3">
            <a class="btn btn-primary" href="{{route('admin.constructions.edit', ['construction' => $construction->id])}}">Редактировать</a>
            <a class="btn btn-success" href="{{route('admin.constructions.create')}}">Добавить</a>
            <form action="{{route('admin.constructions.destroy', ['construction' => $construction->id])}}"
                  method="POST" id="delete-form">
                @method('DELETE')
                @csrf
                <button class="btn btn-danger" form="delete-form">Удалить</button>
            </form>
        </div>
    </div>
@endsection
