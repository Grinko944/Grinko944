@extends("layouts.app")

@section("title",  "Админ редактирование $product->name - " . config("app.name"))

@section("content")
    <form action="{{ route("admin.update", $product->id) }}" method="post" enctype="multipart/form-data">
        @csrf
        @method("PATCH")
        @include("parts.form.add_edit")
        <input type="submit" value="Сохранить" class="btn btn-outline-success">
    </form>
@endsection
