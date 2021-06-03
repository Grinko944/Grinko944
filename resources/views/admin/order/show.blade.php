@extends("layouts.app")

@section("title",  "Админ редактирование $order->name - " . config("app.name"))

@section("content")
    <div class="cols d-flex flex-wrap">
        <div class="col-xl-6 col-sm-12 col-md-6 col-lg-6 show__product-img" style="margin-bottom: 10px">
            <img src="{{ $order->photo }}" alt="">
        </div>
        <div class="col-xl-6 col-sm-12 col-md-6 col-lg-6">
            <div class="order__info"><strong>id продукта</strong> {{ $order->id }}</div>
            <br>
            <div class="info__product"><strong>Имя на англ:</strong> {{ $order->name }}</div>
            <div class="info__product"><strong>Имя на рус:</strong> {{ $order->name_RU }}</div>
            <div class="info__product"><strong>Имя на болг:</strong> {{ $order->name_BG }}</div>
            <div class="info__product"><strong>Имя на нем:</strong> {{ $order->name_DE }}</div>
            <br>
            <div class="info__product"><strong>Описание на англ:</strong> {{ $order->description }}</div>
            <div class="info__product"><strong>Описание на рус:</strong> {{ $order->description_RU }}</div>
            <div class="info__product"><strong>Описание на болг:</strong> {{ $order->description_BG }}</div>
            <div class="info__product"><strong>Описание на нем:</strong> {{ $order->description_DE }}</div>
            <br>
            <div class="info__product"><strong>Цена в руб:</strong> {{ $order->price }} ₽</div>
            <div class="info__product">
                <strong>Цена в долларах:</strong> ${{ round($order->price / $rates["USD"]) }}
            </div>
            <div class="info__product">
                <strong>Цена в евро:</strong> &euro;{{ round($order->price / $rates["EUR"]) }}
            </div>

            @if(auth()->user()->role_id <= 2)
                <div class="actions">
                    <span>Действия с продуктом: </span>
                    <form action="{{ route("admin.orderDestroy", $order->id) }}" method="post"
                          onsubmit="if (confirm('Точно удалить заказ?')) { return true } else { return false }"
                          style="display: inline-block">
                        @csrf
                        @method("DELETE")
                        <button type="submit" class="button-delete" value="Удалить">
                            <span class="fa fa-trash"></span>
                        </button>
                    </form>
                    <a href="{{ route("admin.orderEdit", $order) }}" style="color: black">
                        <span class="fa fa-edit"></span>
                    </a>
                </div>
            @endif

        </div>
    </div>
@endsection
