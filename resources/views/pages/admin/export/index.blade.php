@extends('layouts.admin')

@section('h1', 'Экспорт')

@section('content')
    <form action="{{route('admin.export.start')}}" method="POST" class="px-3 d-flex gap-4 flex-column mb-4" id="export-form">
        @method('POST')
        @csrf
        <x-forms.input
            type="email" placeholder="example@example.ru" id="email"
            name="email" value="{{old('email')}}"
            label="E-mail" :error="$errors->default->first('email')"/>
        <x-forms.select
            id="entity" name="entity" :value="old('entity')"
            label="Экспортируемая модель" :options="$models"
            :error="$errors->default->first('entity')"/>
        <x-forms.checkbox id="redo" label="Выполнить экспорт повторно, если нет данных за сегодня" :value="old('redo', 'false')"
                          name="redo"/>
    </form>

    <div class="d-flex">
        <button class="btn btn-primary" type="submit" form="export-form">Экспортировать</button>
    </div>
@endsection
