@extends('layouts.admin')

@section('h1', 'Импорт')

@section('svg')
    @parent
    <svg xmlns="http://www.w3.org/2000/svg" class="d-none">
        <symbol viewBox="0 0 16 16" id="sort-up">
            <title>Sort Up</title>
            <path
                d="M3.5 12.5a.5.5 0 0 1-1 0V3.707L1.354 4.854a.5.5 0 1 1-.708-.708l2-1.999.007-.007a.498.498 0 0 1 .7.006l2 2a.5.5 0 1 1-.707.708L3.5 3.707zm3.5-9a.5.5 0 0 1 .5-.5h7a.5.5 0 0 1 0 1h-7a.5.5 0 0 1-.5-.5M7.5 6a.5.5 0 0 0 0 1h5a.5.5 0 0 0 0-1zm0 3a.5.5 0 0 0 0 1h3a.5.5 0 0 0 0-1zm0 3a.5.5 0 0 0 0 1h1a.5.5 0 0 0 0-1z"/>
        </symbol>
        <symbol viewBox="0 0 16 16" id="sort-down">
            <title>Sort Down</title>
            <path
                d="M3.5 2.5a.5.5 0 0 0-1 0v8.793l-1.146-1.147a.5.5 0 0 0-.708.708l2 1.999.007.007a.497.497 0 0 0 .7-.006l2-2a.5.5 0 0 0-.707-.708L3.5 11.293zm3.5 1a.5.5 0 0 1 .5-.5h7a.5.5 0 0 1 0 1h-7a.5.5 0 0 1-.5-.5M7.5 6a.5.5 0 0 0 0 1h5a.5.5 0 0 0 0-1zm0 3a.5.5 0 0 0 0 1h3a.5.5 0 0 0 0-1zm0 3a.5.5 0 0 0 0 1h1a.5.5 0 0 0 0-1z"/>
        </symbol>
        <symbol viewBox="0 0 16 16" id="remove">
            <title>Remove</title>
            <path
                d="M15 8a6.973 6.973 0 0 0-1.71-4.584l-9.874 9.875A7 7 0 0 0 15 8M2.71 12.584l9.874-9.875a7 7 0 0 0-9.874 9.874ZM16 8A8 8 0 1 1 0 8a8 8 0 0 1 16 0"/>
        </symbol>
        <symbol viewBox="0 0 16 16" id="reload">
            <title>Reload</title>
            <path
                d="M11 5.466V4H5a4 4 0 0 0-3.584 5.777.5.5 0 1 1-.896.446A5 5 0 0 1 5 3h6V1.534a.25.25 0 0 1 .41-.192l2.36 1.966c.12.1.12.284 0 .384l-2.36 1.966a.25.25 0 0 1-.41-.192Zm3.81.086a.5.5 0 0 1 .67.225A5 5 0 0 1 11 13H5v1.466a.25.25 0 0 1-.41.192l-2.36-1.966a.25.25 0 0 1 0-.384l2.36-1.966a.25.25 0 0 1 .41.192V12h6a4 4 0 0 0 3.585-5.777.5.5 0 0 1 .225-.67Z"/>
        </symbol>
    </svg>
@endsection

@section('content')

    <form action="{{route('admin.import.start')}}" method="POST" class="px-3 d-flex gap-4 flex-column mb-4"
          id="import-form" enctype="multipart/form-data">
        @method('POST')
        @csrf
        <x-forms.input
            type="email" placeholder="example@example.ru" id="email"
            name="email" value="{{old('email')}}"
            label="E-mail" :error="$errors->default->first('email')"/>
        <x-forms.select
            id="entity" name="entity" :value="old('entity')"
            label="Импортируемая модель" :options="$models"
            :error="$errors->default->first('entity')"/>
        <x-forms.file
            id="file" name="file"
            label="Импортируемый файл"
            :error="$errors->default->first('file')"
        />
        <x-forms.checkbox id="withHeaders" label="Импортируемый файл содержит заголовки"
                          :value="old('withHeaders', 'false')"
                          name="withHeaders"/>

        <div @class(['d-flex', 'flex-column', 'mh-100', 'd-none' => empty($fieldsRendered)]) id="fields">
            <h2 class="mb-4">Столбцы в файле импорта</h2>
            <div id="fields-content" class="d-flex flex-column gap-4">
                @if(!empty($fieldsRendered))
                    {!! $fieldsRendered !!}
                @endif
            </div>
            <div class="spinner-border d-none mx-auto" role="status" id="fields-loader">
                <span class="sr-only"></span>
            </div>
        </div>
    </form>

    <div class="d-flex">
        <button class="btn btn-primary" type="submit" form="import-form">Импортировать</button>
    </div>
@endsection


@section('java-script')
    <script type="module">
        $(() => {
            let $fields = $('#fields');
            let $fieldsContent = $('#fields-content');
            let token = $('#import-form input[name="_token"]').val();
            let $fieldsLoader = $('#fields-loader');

            let loadFields = function (model) {
                if (model.length === 0) {
                    $fields.addClass('d-none');
                    return;
                }

                $fieldsContent.html('');
                $fields.removeClass('d-none');
                $fieldsLoader.toggleClass('d-none');
                $.ajax({
                    type: 'post',
                    url: '{{route('admin.import.fields')}}',
                    data: {
                        model,
                        '__token': $('#import-form input[name="_token"]').val(),
                        '__method': 'POST',
                    },
                    headers: {
                        'X-CSRF-TOKEN': token
                    },
                }).done(function (data) {
                    $fieldsContent.html(data)
                }).fail(function () {
                    $fieldsContent.html("<div class='alert alert-danger'>Не удалось загрузить поля</div>")
                }).always(function () {
                    $fieldsLoader.toggleClass('d-none');
                });
            }

            $('#entity').on('change', function (event) {
                loadFields($(this).val())
            })

            $fieldsContent.on('click', '.js-remove', function (event) {
                let $button = $(event.currentTarget);
                $button.closest('.js-field-wrapper').remove();
            });
            $fieldsContent.on('click', '.js-sort-up', function (event) {
                let $button = $(event.currentTarget);
                let $wrapper = $button.closest('.js-field-wrapper');
                let $prev = $wrapper.prev().first();
                if ($prev.length !== 0) {
                    $wrapper.insertBefore($prev);
                }
            });
            $fieldsContent.on('click', '.js-sort-down', function (event) {
                let $button = $(event.currentTarget);
                let $wrapper = $button.closest('.js-field-wrapper');
                let $next = $wrapper.next().first();
                if ($next.length !== 0) {
                    $wrapper.insertAfter($next);
                }
            });
            $fieldsContent.on('click', '.js-reload', function (event) {
                loadFields($('#entity').val());
            });
        })
    </script>
@endsection
