@extends("layouts.app")

@section("title",  "Админ создание заказа - " . config("app.name"))

@section("content")
    <form action="{{ route("admin.orderStore") }}" method="post" enctype="multipart/form-data">
        @csrf
        @method("POST")

        <div class="form-group">
            <input type="text" name="product_id" class="form-control" value="{{ old("product_id") ?? "" }}" placeholder="id продукта">
{{--            <div class="products">--}}
{{--                @foreach($products as $product)--}}
{{--                    <div class="product_item">--}}
{{--                        <div class="product__photo">--}}
{{--                            <img src="{{ $product->photo }}" alt="">--}}
{{--                        </div>--}}
{{--                        <div class="product__id">--}}
{{--                            {{ $product->id }}--}}
{{--                        </div>--}}
{{--                        <div class="product__name">--}}
{{--                            {{ $product->name }}--}}
{{--                        </div>--}}
{{--                    </div>--}}
{{--                @endforeach--}}
{{--            </div>--}}
        </div>

        <div class="form-group">
            <input type="text" name="price" class="form-control" value="{{ old("price") ?? "" }}" placeholder="Цена заказа">
        </div>

        <div class="form-group">
            <input type="text" name="user_id" class="form-control" value="{{ old("user_id") ?? "" }}" placeholder="id пользователя">
        </div>

        <div class="form-group">
            <input type="text" name="delivery_time" class="form-control" value="{{ old("delivery_time") ?? "" }}" placeholder="Время доставки">
        </div>

        <div class="form-group">
            <input type="text" name="status" class="form-control edit_input" value="{{ old("status") ?? $order->status ?? "" }}" placeholder="Введите статус заказа" readonly>
        </div>

        <div class="values" style="margin: 10px 0">
            <button type="button" class="btn btn-info btn_value_edit_order">Принят</button>
            <button type="button" class="btn btn-info btn_value_edit_order">Готовиться</button>
            <button type="button" class="btn btn-info btn_value_edit_order">Доставляется</button>
            <button type="button" class="btn btn-info btn_value_edit_order">Выполнен</button>
        </div>

        <input type="submit" value="Сохранить" class="btn btn-outline-success">
    </form>

    <script>
        let btns = document.querySelectorAll(".btn_value_edit_order"),
            editInput = document.querySelector(".edit_input")

        btns.forEach(btn => {
            btn.addEventListener("click", () => {
                editInput.value = ""
                editInput.value = btn.textContent
            })
        })
    </script>
@endsection

