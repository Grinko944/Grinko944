@extends("layouts.app")

@section("title",  "Админ просмотр продукта $product->name - " . config("app.name"))

@section("content")
    <div class="cols d-flex flex-wrap">
        <div class="col-xl-6 col-sm-12 col-md-6 col-lg-6 show__product-img">
            <img src="{{ $product->photo }}" alt="">
        </div>
        <div class="col-xl-6 col-sm-12 col-md-6 col-lg-6">
            <div class="info__product"><strong>Имя на англ:</strong> {{ $product->name }}</div>
            <div class="info__product"><strong>Имя на рус:</strong> {{ $product->name_RU }}</div>
            <div class="info__product"><strong>Имя на болг:</strong> {{ $product->name_BG }}</div>
            <div class="info__product"><strong>Имя на нем:</strong> {{ $product->name_DE }}</div>
            <br>
            <div class="info__product"><strong>Описание на англ:</strong> {{ $product->description }}</div>
            <div class="info__product"><strong>Описание на рус:</strong> {{ $product->description_RU }}</div>
            <div class="info__product"><strong>Описание на болг:</strong> {{ $product->description_BG }}</div>
            <div class="info__product"><strong>Описание на нем:</strong> {{ $product->description_DE }}</div>
            <br>
            <div class="info__product"><strong>Цена в руб:</strong> {{ $product->price }} ₽</div>
            <div class="info__product"><strong>Цена в долларах:</strong> ${{ round($product->price / $rates["USD"]) }}</div>
            <div class="info__product"><strong>Цена в евро:</strong> &euro;{{ round($product->price / $rates["EUR"]) }}</div>
            <br>
            @if(auth()->user()->role_id <= 2)
                <div class="actions">
                    <span>Действия с продуктом: </span>
                    <form action="{{ route("admin.destroy", $product->id) }}" method="post"
                          onsubmit="if (confirm('Точно удалить пост?')) { return true } else { return false }"
                          style="display: inline-block">
                        @csrf
                        @method("DELETE")
                        <button type="submit" class="button-delete" value="Удалить">
                            <span class="fa fa-trash"></span>
                        </button>
                    </form>
                    <a href="{{ route("admin.edit", $product) }}" style="color: black"><span class="fa fa-edit"></span></a>
                </div>
            @endif
        </div>
    </div>
@endsection
