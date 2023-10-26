@extends('layouts.admin')

@section('h1', 'Просмотр языка программирования')

<?php
$additionalClasses = ['my-3'];
?>

@section('content')
    <form class="px-3">
        <div class="mb-4">
            <h2>Данные языка программирования</h2>
            <input type="hidden" name="id" value="{{$language->id}}"/>
            <x-forms.input
                type="text" placeholder="Символьный идентификатор" id="slug" :readonly="true"
                name="slug" value="{{old('slug') ?? $language->slug}}" :additional-classes="$additionalClasses"
                label="Символьный идентификатор" :error="$errors->default->first('slug')"/>
            <x-forms.input
                type="text" placeholder="Название" id="title" :readonly="true"
                name="title" value="{{old('title') ?? $language->title}}" :additional-classes="$additionalClasses"
                label="Название" :error="$errors->default->first('title')"/>
            <x-forms.textarea
                type="string" placeholder="Описание" id="description" :readonly="true"
                name="description" value="{{old('description') ?? $language->description}}" :additional-classes="$additionalClasses"
                label="Описание" :error="$errors->default->first('description')"/>
        </div>
        <div class="mb-4">
            <h2>Данные языковых конструкций языка</h2>
            <div class="mb-3 d-flex flex-column gap-4" data-cards-list>
                @if(empty($constructions))
                    <div class="p-3 bg-body rounded shadow-sm d-flex gap-3" data-card>
                        <div class="d-flex flex-column gap-3 col">
                            <x-forms.select
                                id="construction-id-0" name="constructions[0][id]" :disabled="true"
                                label="Языковая конструкция" :options="$constructionOptions"/>
                            <x-forms.textarea
                                type="string" placeholder="Реализация конструкции в языке" id="construction-code-0"
                                name="constructions[0][code]" :readonly="true"
                                label="Реализация конструкции в языке" height="100"/>
                        </div>
                    </div>
                @else
                    @foreach($constructions as $oldConstruction)
                        <div class="p-3 bg-body rounded shadow-sm d-flex gap-3" data-card>
                            <div class="d-flex flex-column gap-3 col">
                                <x-forms.select
                                    id="construction-id-{{$loop->index}}" name="constructions[{{$loop->index}}][id]"
                                    label="Языковая конструкция" :options="$constructionOptions" :value="$oldConstruction['id']"
                                    :error="$errors->default->first('constructions.'.$loop->index.'.id')" :disabled="true"/>
                                <x-forms.textarea
                                    type="string" placeholder="Реализация конструкции в языке" id="construction-code-{{$loop->index}}"
                                    name="constructions[{{$loop->index}}][code]" :value="$oldConstruction['code']"
                                    label="Реализация конструкции в языке" height="100"
                                    :error="$errors->default->first('constructions.'.$loop->index.'.code')" :readonly="true"/>
                            </div>
                        </div>
                    @endforeach
                @endif
            </div>
        </div>
    </form>
    <div class="d-flex justify-content-between">
        <a class="btn btn-secondary" href="{{route('admin.languages.index')}}">Обратно в список</a>
        <div class="d-flex flex-row gap-3">
            <a class="btn btn-primary" href="{{route('admin.languages.edit', ['language' => $language->id])}}">Редактировать</a>
            <a class="btn btn-success" href="{{route('admin.languages.create')}}">Добавить</a>
            <form action="{{route('admin.languages.destroy', ['language' => $language->id])}}" method="POST">
                @method('DELETE')
                @csrf
                <button class="btn btn-danger">Удалить</button>
            </form>
        </div>
    </div>
@endsection
