@extends("layouts.app")

@section("title",  "Админ редактирование $order->name - " . config("app.name"))

@section("content")
    <form action="{{ route("admin.orderUpdate", $order->o_id) }}" method="post" enctype="multipart/form-data">
        @csrf
        @method("PATCH")

        <div class="form-group">
            <input type="text" name="status" class="form-control edit_input" value="{{ old("status") ?? $order->status ?? "" }}" placeholder="Статус заказа" readonly>
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
