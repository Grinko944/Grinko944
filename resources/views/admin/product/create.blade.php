@extends("layouts.app")

@section("title",  "Админ добовление продукта - " . config("app.name"))

@section("content")
    <form action="{{ route("admin.store") }}" method="post" enctype="multipart/form-data">
        @csrf
        @method("POST")
        @include("parts.form.add_edit")
        <input type="submit" value="Создать" class="btn btn-outline-success">
    </form>
@endsection
