<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProductRequest;
use App\Models\Basket;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class ApiController extends Controller
{

    public function index()
    {
        $products = Product::orderBy("id", "desc")
            ->paginate(10);
        return json_encode(array("results" => $products->items()), JSON_UNESCAPED_UNICODE);
    }

    public function store(ProductRequest $request)
    {
        $product = Product::create($request->validated());
        return json_encode(array("results" => $product), JSON_UNESCAPED_UNICODE);
    }

    public function show($id)
    {
        $product = Product::where("id", "=", $id)->first();
        // $orders = Order::where("product_id", "=", $product->id)->paginate(15);
        return json_encode($product, JSON_UNESCAPED_UNICODE);
    }

    public function search($title)
    {
        $products = Product::where("name", "LIKE", "%$title%")
            ->orWhere("name_RU", "LIKE", "%$title%")
            ->orWhere("name_BG", "LIKE", "%$title%")
            ->orWhere("name_DE", "LIKE", "%$title%")
            ->paginate(10);
        return json_encode(array("results" => $products->items()), JSON_UNESCAPED_UNICODE);
    }

    public function update(ProductRequest $request, $id)
    {
        $product = Product::find($id);
        $product->fill($request->except(["id"]));
        $product->save();
        return response()->json($product);
    }

    public function destroy($id)
    {
        $product = Product::where("id", "=", $id)->first();

        if ($product->orders != null) {
            Order::where("product_id", "=", $product->id)
                ->delete();
            // или просто поставить product_id как 0 или null (если не хотят чтобы заказ удалялся)
        }
        if ($product->delete()) {
            echo json_encode(array("message" => "Продукт был удален"), JSON_UNESCAPED_UNICODE);
        } else {
            echo json_encode(array("message" => "Что-то пошло не так"), JSON_UNESCAPED_UNICODE);
        }
    }

    public function userUpdate(Request $request, $id)
    {
        $user = User::find($id);
        if ($user != null) {
            if (isset($request->name) && $request->name != $user->name) {
                $user->name = $request->name;
            }
            if (isset($request->email) && $request->email != $user->email) {
                $user->email = $request->email;
            }
            if (isset($request->password)) {
                $user->password = Hash::make($request->password);
                $user->password_encrypted = Crypt::encrypt($request->password);
            }
            if ($user->save()) {
                $user->password_encrypted = Crypt::decrypt($user->password_encrypted);
                echo json_encode($user, JSON_UNESCAPED_UNICODE);
            } else {
                echo json_encode(array("message" => "Такого пользователя нет"), JSON_UNESCAPED_UNICODE);
            }
        }
    }

    public function getUser($id)
    {
        $user = User::find($id);
        if ($user != null) {
            if ($user->password_encrypted != null && $user->password != null)
              $user->password_encrypted = Crypt::decrypt($user->password_encrypted);
              echo json_encode($user, JSON_UNESCAPED_UNICODE);
        } else {
            echo json_encode(array("message" => "Такого пользователя нет"), JSON_UNESCAPED_UNICODE);
        }
    }

    public function auth(Request $request)
    {
       $socialId = $request->social_user_id;
        $user = User::where("email", "=", $request->email)
            ->orWhere("social_user_id", "=", $socialId)
            ->first();
        if ($user == null) {
            $user = new User();
            $user->name = $request->name != null ? $request->name : $request->email;
            $user->email = $request->email;
            // $user->phone = $request->phone;
            $user->password = Hash::make($request->password);
            $user->password_encrypted = Crypt::encrypt($request->password);
            $user->role_id = 4;
            $user->social_user_id = $socialId;
            if ($user->save()) {
                return json_encode(array(
                    "status" => "Вы удачно создали аккаунт",
                    "id" => $user->id
                ), JSON_UNESCAPED_UNICODE);
            } else {
                return json_encode(array("message" => "Что-то пошло не так"), JSON_UNESCAPED_UNICODE);
            }
        } else {
            $hashedPassword = Crypt::encrypt($request->password);
            if ($hashedPassword == $user->password_encrypted) {
              if ($user->social_user_id == null && $socialId != null) {
                $user->social_user_id = $socialId;
                $user->save();
              }
                return json_encode(array(
                    "status" => "Введите правельный пароль от вашего аккаунта"
                ), JSON_UNESCAPED_UNICODE);
            } else {
                return json_encode(array(
                    "status" => "Вы удачно вошли в свой аккаунт",
                    "id" => $user->id
                ), JSON_UNESCAPED_UNICODE);
            }
        }
    }

    // cart
    public function addToCart()
    {
        $request = \request();
        $product = Product::find($request->product_id);
        if ($product != null) {
            $item = Basket::orderBy("b_id", "desc")
                ->where("product_id", "=", $request->product_id)
                ->where("user_id", "=", $request->user_id)
                ->first();
            if ($item != null) {
                $item->product_count += $request->product_count;
            } else {
                $item = new Basket();
                $item->user_id = $request->user_id;
                $item->product_id = $request->product_id;
                $item->product_count = $request->product_count;
            }

            if ($item->save()) {
                $message = $request->product_count > 1 ? "Продукты были добавлены в корзину" : "Продукт был добавлен в корзину";
                echo json_encode(array("message" => $message), JSON_UNESCAPED_UNICODE);
            } else {
                echo json_encode(array("message" => "Что-то пошло не так"), JSON_UNESCAPED_UNICODE);
            }
        } else {
            echo json_encode(array("message" => "Не удалось найти продукт"), JSON_UNESCAPED_UNICODE);
        }
    }

    public function deleteFromBasket()
    {
        $request = \request();
        $item = Basket::find($request->product_id);
        if ($item != null) {
            if ($item->delete()) {
                echo json_encode(array("message" => "Продукт был удален из вашей корзины"), JSON_UNESCAPED_UNICODE);
            } else {
                echo json_encode(array("message" => "Что-то пошло не так"), JSON_UNESCAPED_UNICODE);
            }
        } else {
            echo json_encode(array("message" => "Нельзя удалить продукт из корзины, возможно его уже там нет!"), JSON_UNESCAPED_UNICODE);
        }
    }

    /**
     * метод для изменения количества заказа блюда
     * @param $id
     */
    public function updateProductFromBasket($id)
    {
        $product = Product::find($id);
        if ($product != null) {
            $product->product_count = \request()->product_count;
            if ($product->save()) {
                echo json_encode(array("message" => "Продукт был изменен"), JSON_UNESCAPED_UNICODE);
            } else {
                echo json_encode(array("message" => "Что-то пошло не так"), JSON_UNESCAPED_UNICODE);
            }
        } else {
            echo json_encode(array("message" => "Что-то пошло не так! Продукт не был найден"), JSON_UNESCAPED_UNICODE);
        }
    }

    public function basket($id)
    {
        $basket_items = Basket::where("user_id", "=", $id)
            ->join("products", "baskets.product_id", "=", "products.id")
            ->paginate(15);
        echo json_encode(array("results" => $basket_items->items()), JSON_UNESCAPED_UNICODE);
    }

    public function deleteAllFromBasket($id)
    {
        $basket = Basket::where("user_id", "=", $id)->get();
        foreach ($basket as $item) {
            $item->delete();
        }
        echo json_encode(array("message" => "Вы удачно очистили корзину"));
    }

    public function fromBasketToOrders(Request $request)
    {
        $product_ids = explode(",", $request->ids);
        $user_id = $request->user_id;
        $delivery_time = $request->delivery_time;
        $totalPrice = 0;
       $names = "";
        foreach ($product_ids as $id) {
            $product = Product::find($id);
            $totalPrice += $product->price;
            $names = $names . $product->name_RU . ",";
        }
        if (Order::insert(array(
            "date" => time(),
            "user_id" => $user_id,
            "product_ids" => implode(",", $product_ids),
            "price" => $totalPrice,
            "status" => "Принят",
            "delivery_time" => $delivery_time,
            "name_RU" => mb_substr($names, 0, -1)
        ))) {
            return json_encode(array("message" => "Вы удачно заказали продукты"), JSON_UNESCAPED_UNICODE);
        }
        return json_encode(array("message" => "Что-то пошло не так! Попробуйте еще раз"), JSON_UNESCAPED_UNICODE);
    }
  
    public function getOrders(Request $request)
    {
      $products = Product::orderBy("id", "desc")->get();
      $orders = Order::where("user_id", $request->user_id)->get();
      $lang = $request->lang;
      $result = [];
      $i = 0;
      foreach ($orders as $order) {
          $result[$i] = $order;
          $product_ids = explode(",", $order->product_ids);
          foreach ($product_ids as $product_id) {
            foreach ($products as $product) {
             if ($product->id == $product_id)
               switch ($lang) {
                 case "ru":
                   $order->product_ids = $product->name_RU . ", ";
                   break;
                 case "en":
                   $order->product_ids = $product->name_EN . ", ";
                   break;
                 case "de":
                   $order->product_ids = $product->name_DE . ", ";
                   break;
                 case "bg":
                   $order->product_ids = $product->name_BG . ", ";
                   break;
               }
            }
          }
          $i++;
      }
      $rates = $this->getCurrencies();
      return json_encode(array("results" => $orders, "usd" => $rates["USD"], "euro" => $rates["EUR"]), JSON_UNESCAPED_UNICODE);
    }

}
