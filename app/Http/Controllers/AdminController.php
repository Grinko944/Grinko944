<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProductRequest;
use App\Models\Order;
use App\Models\Product;
use App\Models\Report;
use App\Models\User;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Kreait\Firebase\Exception\DatabaseException;
use Kreait\Firebase\Factory;
use Kreait\Firebase\ServiceAccount;

class AdminController extends Controller
{

    public function __construct()
    {
        $this->middleware("role");
    }

    // product

    public function home()
    {
        $request = \request();

        switch ($request->pr) {
            case "Продукты":
                $products = Product::orderBy("id", "desc")
                    ->paginate(8);
                return view("admin.home", compact("products"));
            case "Заказы":
                $orders = Order::orderBy("id", "desc")
                    ->paginate(8);
                return view("admin.home", compact("orders"));
            case "Пользователи":
                $users = User::join("roles", "users.role_id", "=", "roles.r_id")
                    ->orderBy("users.id", "desc")
                    ->paginate(8);
                return view("admin.home", compact("users"));
            case "Доходы":
                $incomes = Report::orderBy("id", "desc")->get();
                $incomes_in_four_mount = array();
                $current_mount = date("m", time());
                foreach ($incomes as $income) {
                    if (date("m", $income->date) == $current_mount ||
                        (date("m", $income->date) - $current_mount) <= 4) {
                        $incomes_in_four_mount[$this->getTime(date("m", $income->date))] = $income->price;
                        if (isset($incomes_in_four_mount[$this->getTime(date("m", $income->date))])) {
                            $incomes_in_four_mount[$this->getTime(date("m", $income->date))] += $income->price;
                        } else {
                            $incomes_in_four_mount[$this->getTime(date("m", $income->date))] = $income->price;
                        }
                    }
                }
                return view("admin.home", ["incomes" => array_slice($incomes_in_four_mount, 0, 5), "labels" => array_keys($incomes_in_four_mount)]);
            case "Чат":
                $serviceAccount = ServiceAccount::fromJsonFile(public_path() . "/restaurant-secret.json");

                $factory = (new Factory)//->withServiceAccount($serviceAccount)
                    ->withDatabaseUri("https://restaurant-7b542-default-rtdb.firebaseio.com");

                $database = $factory->createDatabase();
                $chats = $database->getReference("chats")
                    ->getSnapshot();
                return view("admin.home", ["chats" => $chats->getValue()]);
            default:
                $products = Product::orderBy("id", "desc")
                    ->paginate(8);
                return view("admin.home", compact("products"));
        }
    }

    private function getTime($time_code) {
        $mounts = array("Янв", "Фев", "Март", "Апр", "Май", "Июнь", "Июль", "Авг", "Сен", "Окт", "Ноя", "Дек");
        for ($i = 0; $i <= count($mounts); $i++) {
            if ($time_code == $i + 1) {
                return $mounts[$i];
            }
        }
    }

    public function edit($id)
    {
        $product = Product::where("id", "=", $id)->first();
        if (auth()->user()->role_id <= 2) {
            return view("admin.product.edit", compact("product"));
        } else {
            return redirect()->route("admin.home")->withErrors("Вы не можете изменить этот продукт!");
        }
    }

    public function update(Request $request, $id)
    {
        $product = Product::find($id);
        if (auth()->user()->role_id <= 2) {
            $product->name = $request->name;
            $product->name_RU = $request->name_RU;
            $product->name_BG = $request->name_BG;
            $product->name_DE = $request->name_DE;

            $product->description = $request->description;
            $product->description_RU = $request->description_RU;
            $product->description_BG = $request->description_BG;
            $product->description_DE = $request->description_DE;

            $product->price = $request->price;
            $rates = $this->getCurrencies();

            $product->price_EURO = $request->price / $rates["EUR"];
            $product->price_USD = $request->price / $rates["USD"];

            if ($request->file("photo")) {
              // $path = Storage::putFile("public/$product->id", $request->file("photo"));
              // $url = Storage::url($path);
               $img = $request->file('photo');
               $ext = $img->getClientOriginalExtension();
               $name = time() . '. ' . $ext;
               $path = public_path('/images/' . $product->id);
               $img->move($path, str_replace(" ", "", $name));
              $product->photo = "/public/images/$product->id/" . str_replace(" ", "", $name);
            }
            if ($product->update()) {
                return redirect()->route("admin.home")->with("success", "Вы удачно убновили продукт \"$product->name_RU\"");
            } else {
                return redirect()->route("admin.home")->withErrors("Что-то пошло не так! Попробуйте ещё раз или чуть поже");
            }
        }
        return redirect()->route("admin.home")->withErrors("Вы не можете изменить этот продукт!");
    }

    public function showProduct($id)
    {
        $product = Product::find($id);
        $rates = $this->getCurrencies();
        return view("admin.product.show", compact("product", "rates"));
    }

    public function destroy($id)
    {
        $product = Product::find($id);
        if (auth()->user()->role_id <= 2) {
            if ($product->delete()) {
                return redirect()->route("admin.home")->with("success", "Вы удачно удалили продукт");
            } else {
                return redirect()->route("admin.home")->withErrors("Что-то пошло не так! Попробуйте ещё раз или чуть поже");
            }
        }
        return redirect()->route("admin.home")->withErrors("Вы не можете удалить этот продукт!");
    }

    public function create()
    {
        return view("admin.product.create");
    }

    public function store(ProductRequest $request)
    {
        if (auth()->user()->role_id <= 2) {
            $product = new Product();

            if ($request->file("photo")) {
                 $img = $request->file('photo');
                 $ext = $img->getClientOriginalExtension();
                 $name = time() . '. ' . $ext;
                 $path = public_path('/images/' . $product->id);
                 $img->move($path, str_replace(" ", "", $name));
                 $product->photo = "/public/images/$product->id/" . str_replace(" ", "", $name);
            }

            $product->name = $request->name;
            $product->name_RU = $request->name_RU;
            $product->name_BG = $request->name_BG;
            $product->name_DE = $request->name_DE;

            $product->description = $request->description;
            $product->description_RU = $request->description_RU;
            $product->description_BG = $request->description_BG;
            $product->description_DE = $request->description_DE;

            $rates = $this->getCurrencies();
            $product->price = $request->price;
            $product->price_EURO = $request->price / $rates["EUR"];
            $product->price_USD = $request->price / $rates["USD"];

            if ($product->save()) {
                return redirect()->route("admin.home")->with("success", "Вы удачно создали новый продукт");
            } else {
                return redirect()->route("admin.home")->withErrors("Что-то пошло не так! Попробуйте ещё раз или чуть поже");
            }
        }
        return redirect()->route("admin.home")->withErrors("Вы не можете создовать продукты продукт!");
    }

    // user

    public function userAdd()
    {
        return view("admin.user.add");
    }

    public function userStore(Request $request)
    {
        $user = new User();
        $user->name = $request->name;
        $user->email = $request->email;
        $user->role_id = $request->role;
        $user->password = Hash::make($request->password);
        $user->password_encrypted = Crypt::encrypt($request->password);
        if ($user->save()) {
            return redirect()->route("admin.home")->with("success", "Вы удачно зарегистрировали пользователя");
        } else {
            return redirect()->route("admin.home")->withErrors("Что-то пошло не так! Попробуйте ещё раз или чуть поже");
        }
    }

    public function destroyUser($id)
    {
        $user = User::find($id);
        if (auth()->user()->role_id <= 2) {
            if ($user->delete()) {
                return redirect()->route("admin.home")->with("success", "Вы удачно удалили пользователя из базы данных");
            } else {
                return redirect()->route("admin.home")->withErrors("Что-то пошло не так! Попробуйте ещё раз или чуть поже");
            }
        }
        return redirect()->route("admin.home")->withErrors("Вы не можете удалить этого пользователя!");
    }

    public function userEdit($id)
    {
        if (auth()->user()->id == $id || auth()->user()->role_id == 1) {
            $user = User::find($id);
            return view("admin.user.edit", compact("user"));
        } else {
            return redirect()->route("admin.home")->withErrors("Вы не можете изменить профиль этого пользователя");
        }
    }

    public function userUpdate(Request $request, $id)
    {
        if (auth()->user()->id == $id || auth()->user()->role_id == 1) {
            $user = User::find($id);
            $user->name = $request->name;
            $user->email = $request->email;
            $user->role_id = $request->role;
            $user->password = Hash::make($request->password);
            $user->password_encrypted = Crypt::encrypt($request->password);
            if ($user->save()) {
                return redirect()->route("admin.home")->with("success", "Вы удачно изменили профиль пользователя");
            } else {
                return redirect()->route("admin.home")->withErrors("Что-то пошло не так! Попробуйте ещё раз или чуть поже");
            }
        } else {
            return redirect()->route("admin.home")->withErrors("Вы не можете изменить профиль этого пользователя");
        }
    }

    // order

    public function orderShow($id)
    {
        // сделать так чтобы если у одного и того же пользовотеля заказов больше одного
        // все заказы были сформированы в один и когда назимаешь посмотреть заказ, там были все заказы
        $order = Order::join("products", "orders.product_id", "=", "products.id")
            ->where("o_id", "=", $id)->first();
        $rates = $this->getCurrencies();
        return view("admin.order.show", compact("order", "rates"));
    }

    public function orderCreate()
    {
        $products = Product::orderBy("id", "desc")
            ->get()->take(8);
        return view("admin.order.create", compact("products"));
    }

    public function orderStore(Request $request)
    {
        $order = new Order();

        $order->date = time();
        $order->user_id = $request->user_id;
        $order->product_ids = $request->product_id;
        $order->price = $request->price;
        $order->status = $request->status;
        $order->delivery_time = $request->delivery_time;

        if ($order->save()) {
            return redirect()->route("admin.home")->with("success", "Вы удачно создали новый заказ");
        } else {
            return redirect()->route("admin.home")->withErrors("Что-то пошло не так! Попробуйте ещё раз или чуть поже");
        }
    }

    public function orderEdit($id)
    {
        $order = Order::find($id);
        if (auth()->user()->role_id <= 2) {
            return view("admin.order.edit", compact("order"));
        } else {
            return redirect()->route("admin.home")->withErrors("Вы не можете изменить этот заказ!");
        }
    }

    public function orderUpdate($id)
    {
        $request = \request();
        if (auth()->user()->role_id <= 2) {
            $order = Order::find($id);
            $order->status = $request->status;
            if ($order->update()) {
                if ($order->status == "Выполнен") {
                    $id = $order->o_id;
                    Report::insert(array(
                        "date" => time(),
                        "order_id" => $order->o_id,
                        "price" => $order->price
                    ));
                    if ($order->delete()) {
                        return redirect()->route("admin.home")->with("success", "Вы удачно убновили заказ №$id.\nИ заказ был автоматически удален");
                    }
                } else {
                    return redirect()->route("admin.home")->with("success", "Вы удачно убновили заказ №$order->o_id");
                }
            } else {
                return redirect()->route("admin.home")->withErrors("Что-то пошло не так! Попробуйте ещё раз или чуть поже");
            }
        } else {
            return redirect()->route("admin.home")->withErrors("Вы не можете изменить этот заказ!");
        }
    }

    public function destroyOrder($id)
    {
        $order = Order::where("o_id", "=", $id)->first();
        if (auth()->user()->role_id <= 2 && $order != null) {
            if ($order->delete()) {
                return redirect()->route("admin.home")->with("success", "Вы удачно удалили заказ");
            } else {
                return redirect()->route("admin.home")->withErrors("Что-то пошло не так! Попробуйте ещё раз или чуть поже");
            }
        }
        return redirect()->route("admin.home")->withErrors("Вы не можете удалить этот заказ!");
    }

}
