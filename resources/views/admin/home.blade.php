@extends("layouts.app")

@section("title",  "Admin home - " . config("app.name"))

@section("content")

    <div class="cols">

        <div class="choose__table-btn">
            @foreach(array("Продукты", "Заказы", "Пользователи", "Чат") as $cat)
                <h4 class="nav_btn @if(request()->pr == $cat) selected @endif">
                    <a href="{{ route("admin.home") . "?pr=$cat" }}" class="text-center vert-align">
                        {{ $cat }}
                    </a>
                    <br>
                    @if(auth()->user()->role_id <= 2 && request()->pr == $cat)
                        @switch(request()->pr)
                            @case("Продукты")
                            <button class="btn btn-info" style="margin: 10px">
                                <a href="{{ route("admin.create") }}" style="text-decoration: none; margin: 10px">
                                    Создать товар
                                </a>
                            </button>
                            @break
                            @case("Заказы")
                            <button class="btn btn-info" style="margin: 10px">
                                <a href="{{ route("admin.orderCreate") }}"
                                   style="text-decoration: none; color: white; margin: 10px">
                                    Создать заказ
                                </a>
                            </button>
                            @break
                            @case("Пользователи")
                            <button class="btn btn-info" style="margin: 10px">
                                <a href="{{ route("admin.userCreate") }}" style="text-decoration: none; color: white;">
                                    Создать пользователя
                                </a>
                            </button>
                            @break
                        @endswitch
                    @endif
                </h4>
            @endforeach
        </div>
          
          <script>
                let btn = document.querySelectorAll(".nav_btn")[1];
                setInterval(function () {
                  btn.style.backgroundColor = "#ffe083"
                }, 5 * 60 * 1000)
          </script>

        <div class="row">
            @if(isset($products))
                <table class="table table-striped" id="products_table">
                    <thead>
                    <tr>
                        <th class="text-center vert-align">Фото</th>
                        <th class="text-center vert-align">Имя англ.</th>
                        <th class="text-center vert-align">Имя рус.</th>
                        <th class="text-center vert-align">Имя блг.</th>
                        <th class="text-center vert-align">Имя нем.</th>
                        <th class="text-center vert-align">Действия</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($products as $product)
                        <tr class="product__item" id="product-{{ $product->id }}">
                            <td>
                                <img class="img-responsive" src="{{ $product->photo }}" alt="">
                            </td>
                            <td class="text-center vert-align">{{ $product->name }}</td>
                            <td class="text-center vert-align">{{ $product->name_RU }}</td>
                            <td class="text-center vert-align">{{ $product->name_BG }}</td>
                            <td class="text-center vert-align">{{ $product->name_DE }}</td>
                            <td class="text-center vert-align">
                                @if(auth()->user()->role_id <= 2)
                                    <form action="{{ route("admin.destroy", $product->id) }}" method="post"
                                          onsubmit="if (confirm('Точно удалить пост?')) { return true } else { return false }"
                                          style="display: inline-block">
                                        @csrf
                                        @method("DELETE")
                                        <button type="submit" class="button-delete" value="Удалить">
                                            <span class="fa fa-trash"></span>
                                        </button>
                                    </form>
                                    <a href="{{ route("admin.edit", $product) }}"><span class="fa fa-edit"></span></a>
                                @endif
                                <a href="{{ route("admin.showProduct", $product->id) }}"><span
                                        class="fa fa-info"></span></a>
                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            @endif

            @if(isset($orders))
                <table class="table table-striped" id="orders_table">
                    <thead>
                    <tr>
                        @if($orders->count() > 0)
                            <th class="text-center vert-align">id продукта</th>
                            <th class="text-center vert-align">id пользователя</th>
                            <th class="text-center vert-align">Имя рус.</th>
                            <th class="text-center vert-align">Время доставки</th>
                            <th class="text-center vert-align">статус</th>
                            <th class="text-center vert-align">изменить статус</th>
                        @else
                            <th class="text-center vert-align">Заказы</th>
                        @endif
                    </tr>
                    </thead>
                    <tbody>
                    @if($orders->count() > 0)
                        @foreach($orders as $order)
                            <tr class="product__item">
                                <td class="text-center vert-align">
                                    @php($orderIds = explode(",", $order->product_ids))
                                    @foreach($orderIds as $orderId)
                                        <a style="color: blue"
                                           href="/?pr=Продукты#product-{{ $orderId }}">{{ $orderId }}, </a>
                                    @endforeach
                                </td>
                                <td class="text-center vert-align">
                                  <a href="http://bbq-luxor.com/admin/home?pr=Пользователи#user-{{ $order->user_id }}" style="color: blue">
                                    {{ $order->user_id }}
                                  </a>
                                </td>
                                <td class="text-center vert-align"> 
                                  <a style="color: blue" href="/?pr=Продукты#product-{{ $orderId }}">{{ $order->name_RU }}</a></td>
                                <td class="text-center vert-align">{{ $order->delivery_time }}</td>
                                <td class="text-center vert-align">{{ $order->status }}</td>
                                <td class="text-center vert-align">
                                    @if(auth()->user()->role_id <= 2)
                                        <form action="{{ route("admin.orderDestroy", $order->id) }}" method="post"
                                              onsubmit="if (confirm('Точно удалить заказ?')) { return true } else { return false }"
                                              style="display: inline-block">
                                            @csrf
                                            @method("DELETE")
                                            <button type="submit" class="button-delete" value="Удалить">
                                                <span class="fa fa-trash"></span>
                                            </button>
                                        </form>
                                        <a href="{{ route("admin.orderEdit", $order->id) }}"><span
                                                class="fa fa-edit"></span></a>
                                    @endif
                                    <a href="{{ route("admin.showOrder", $order->id) }}"><span
                                            class="fa fa-info"></span></a>
                                </td>
                            </tr>
                        @endforeach
                    @else
                        <tr>
                            <td>К сажалению заказов нет</td>
                        </tr>
                    @endif
                    </tbody>
                </table>
            @endif

            @if(isset($users))
                <table class="table table-striped" id="orders_table">
                    <thead>
                    <tr>
                        <th class="text-center">id</th>
                        <th class="text-center">Имя</th>
                        <th class="text-center">email</th>
                        <th class="text-center">Роль</th>
                        @if(auth()->user()->role_id <= 2)
                            <th class="text-center">Действия</th>
                        @endif
                    </tr>
                    </thead>
                    <tbody>
                    <tr>
                    @foreach($users as $user)
                      <tr class="product__item" id="user-{{ $user->id }}">
                            <td class="text-center vert-align">{{ $user->id }}</td>
                            <td class="text-center vert-align">{{ $user->name }}</td>
                            <td class="text-center vert-align">{{ $user->email }}</td>
                            <td class="text-center vert-align">{{ $user->role_name }}</td>
                            <td class="text-center vert-align">
                                @if(auth()->user()->role_id <= 2)
                                    <form action="{{ route("admin.destroyUser", $user->id) }}" method="post"
                                          onsubmit="if (confirm('Точно удалить пользователя?')) { return true } else { return false }"
                                          style="display: inline-block">
                                        @csrf
                                        @method("DELETE")
                                        <button type="submit" class="button-delete" value="Удалить">
                                            <span class="fa fa-trash"></span>
                                        </button>
                                    </form>
                                    <a href="{{ route("admin.userEdit", $user->id) }}"><span class="fa fa-edit"></span></a>
                                @endif
                            </td>
                        </tr>
                        @endforeach
                        </tr>
                    </tbody>
                </table>
            @endif

            @if(isset($incomes))

                {{--                @foreach($incomes_in_four_mount as $income)--}}
                {{--                    <button onclick="test({{ $incomes_in_four_mount[0]["original"] }})">Click</button>--}}
                {{--                    <div class="div">--}}
                {{--                        {{ $income }}--}}
                {{--                    </div>--}}
                {{--                @endforeach--}}

                <canvas id="graphic" width="550" height="550"></canvas>
                <script>
                    function drawGraphic(data, labels) {
                        let canvas = document.getElementById("graphic"),
                            ctx = canvas.getContext("2d"),
                            max = getMax(data)

                        ctx.fillStyle = "black"; // Задаём чёрный цвет для линий
                        ctx.lineWidth = 2.0; // Ширина линии
                        ctx.beginPath(); // Запускает путь
                        ctx.moveTo(50, 10); // Указываем начальный путь
                        ctx.lineTo(50, 460); // Перемешаем указатель
                        ctx.lineTo(540, 460); // Ещё раз перемешаем указатель
                        ctx.stroke(); // Делаем контур

// Цвет для рисования
                        ctx.fillStyle = "black";
// Цикл для отображения значений по Y
                        for (let i = 0; i < 6; i++) {

                            if (max > 500000 && max < 1000000) {
                                ctx.fillText((5 - i) * 100000 + "", 10, i * 80 + 55);
                            } else if (max > 1000000 && max < 5000000) {
                                ctx.fillText((5 - i) * 1000000 + "", 0, i * 80 + 55);
                            } else if (max < 500000) {
                                ctx.fillText((5 - i) * 100000 + "", 10, i * 80 + 55);
                            } else {
                                ctx.fillText((5 - i) * 10000000 + "", 0, i * 80 + 55);
                            }

                            ctx.beginPath();
                            ctx.moveTo(50, i * 80 + 60);
                            ctx.lineTo(40, i * 80 + 60);
                            ctx.stroke();
                        }

                        // Выводим меток
                        for (let i = 0; i < labels.length; i++) {
                            ctx.fillText(labels[i], 80 + i * 100, 475);
                        }

                        ctx.fillStyle = "green";
// Цикл для от рисовки графиков

                        var rects = []

                        for (let i = 0; i < data.length; i++) {
                            var dp = data[i];
                            let y = 0,
                                h = 0
                            if (max > 500000 && max < 1000000) {
                                y = 458 - (dp / 13000)
                                h = (dp / 13000)
                                //ctx.fillRect(70 + i * 100, 458 - (dp / 13000), 50, (dp / 13000))
                            } else if (max > 1000000 && max < 5000000) {
                                y = 458 - (dp / 13000)
                                h = (dp / 13000)
                                // ctx.fillRect(70 + i * 100, 458 - (dp / 13000), 50, (dp / 13000))
                            } else if (max <= 500000) {
                                y = 458 - (dp / 1300)
                                h = (dp / 1300)
                                //ctx.fillRect(70 + i * 100, 458 - (dp / 1300), 50, (dp / 1300))
                            } else {
                                y = 458 - (dp / 130000)
                                h = (dp / 130000)
                                //ctx.fillRect(70 + i * 100, 458 - (dp / 130000), 50, (dp / 130000))
                            }
                            ctx.fillRect(70 + i * 100, y, 50, h)
                            rects.push({x: 70 + i * 100, y, w: 50, h, other: dp})
                        }

                        // проверка на то что курсор на фигуре
                        let i = 0, r

                        // render initial rects.
                        while (r = rects[i++]) {
                            ctx.rect(r.x, r.y, r.w, r.h);
                        }
                        ctx.fillStyle = "green";
                        ctx.fill();

                        canvas.onmousemove = function (e) {

                            // important: correct mouse position:
                            let rect = this.getBoundingClientRect(),
                                x = e.clientX - rect.left,
                                y = e.clientY - rect.top,
                                i = 0, r;

                            // ctx.clearRect(0, 0, canvas.width, canvas.height); // for demo

                            while (r = rects[i++]) {
                                // add a single rect to path:
                                ctx.beginPath();
                                ctx.rect(r.x, r.y, r.w, r.h);

                                // check if we hover it, fill red, if not fill it blue
                                ctx.fillStyle = ctx.isPointInPath(x, y) ? "red" : "green";
                                ctx.fill();
                            }
                        }
                    }

                    function getMax(array) {
                        let max = 0
                        array.forEach((i) => {
                            if (i > max) {
                                max = i
                            }
                        })
                        return max
                    }

                    function uniq(a) {
                        for (var q = 1, i = 1; q < a.length; ++q) {
                            if (a[q] !== a[q - 1]) {
                                a[i++] = a[q];
                            }
                        }

                        a.length = i;
                        return a;
                    }

                    let prices = [],
                        labels = [];

                    @foreach($labels as $label)
                    labels.push("{{ $label }}")

                    @foreach($incomes as $key => $income)
                    @if($key == $label)
                    prices.map((e, i) => {
                        if (e.key == "{{ $key }}") {
                            e.price = e.price + {{ $income }}
                        }
                    })
                    @else
                    prices.push({key: "{{ $key }}", price: {{ $income }}})
                    @endif
                    prices.push({key: "{{ $key }}", price: {{ $income }}})
                    @endforeach

                    @endforeach

                    prices.map((e, i) => {
                        prices.push(prices[i].price)
                    })

                    prices.map((e, i) => {
                        if (isNaN(e)) {
                            // prices.splice(i, i + 1)
                            prices[i] = null
                        }
                    })

                    prices = prices.filter(element => element !== null)

                    prices = uniq(prices)

                    console.log(prices)

                    drawGraphic(prices, labels)
                </script>
            @endif

            @if(isset($chats))
                <div class="chats">
                    @foreach($chats as $chatItem)
                        @foreach($chatItem as $chat)
                            <div class="chat">
                                <h3>
                                    <a href="{{ route("single_chat", ["userId" => $chat["user_id"], "orderId" => $chat["id"]]) . "?fromId=" . auth()->user()->id . "&orderId=" . $chat["id"] . "&userId=" . $chat["user_id"] }}">{{ $chat["name"] }}</a>
                                </h3>
                                id пользователя:<span> {{ $chat["user_id"] }}</span>
                            </div>
                        @endforeach
                    @endforeach
                </div>
            @endif
        </div>
    </div>

@endsection
