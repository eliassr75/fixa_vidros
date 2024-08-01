<?php

namespace App\Controllers;

use App\Models\Category;
use App\Models\Client;
use App\Models\GlassThickness;
use App\Models\GlassType;
use App\Models\OrderFinance;
use App\Models\Orders;
use App\Models\OrdersItems;
use App\Models\OrderStatus;
use App\Models\Product;
use App\Models\SubCategory;
use App\Models\User;
use Doctrine\Inflector\Rules\Transformation;
use Exception;

class OrdersController extends BaseController
{
    public function index()
    {
        $functionController = new FunctionController();
        $functionController->is_dashboard(true);
        $functionController->is_('orders_page', true);

        define('TITLE_PAGE', 'Fixa Vidros - ' . $functionController->locale('menu_item_orders'));
        define('SUBTITLE_PAGE', $functionController->locale('menu_item_orders'));

        $orders = Orders::orderBy('orders.id', 'desc')
        ->select(
            'orders.*',
            'client.name as client_name',
            'users.name as user_name',
            'type_status_orders.name as type_status_name',
            'type_status_orders.id as type_status_id',
            'type_status_orders.color as type_status_color',
            'type_status_finance.id as type_status_finance_id',
            'type_status_finance.name as type_status_finance_name',
            'type_status_finance.color as type_status_finance_color',
        )
        ->join('client', 'client.id', '=', 'orders.client_id')
        ->join('users', 'users.id', '=', 'orders.user_id')
        ->join('type_status_orders', 'type_status_orders.id', '=', 'orders.status_id')
        ->join('type_status_finance', 'type_status_finance.id', '=', 'orders.finance_id')
        ->get();
        $orders_array = [];
        foreach ($orders as $order) {
            $order->str_created = date('d/m/Y H:i', strtotime($order->created_at));
            $order->total_items = count($order->items()->get());
            $orders_array[] = $order;
        }

        $this->render('orders', [
            'orders' => $orders_array,
            'button' => 'add',
            'url' => '/order/new/'
        ]);
    }

    public function prepareOrderData($orderId)
    {
        $functionController = new FunctionController();
        $orderStatus = [];
        $orderFinance = [];
        $existsOrder = [];
        if($orderId){
            $orderStatus = OrderStatus::all();
            $orderFinance = OrderFinance::all();
            $order = Orders::find($orderId);
            $orderItems = $order->items()->get();
            $existsOrder['order'] = $order;
            $existsOrder['items'] = $orderItems;
        }

        $clients = Client::orderBy('id', 'desc')->get();
        $clients_array = [];
        foreach ($clients as $client) {
            $client->str_created = date('d/m/Y H:i', strtotime($client->created_at));
            $clients_array[] = $client;
        }

        $settingsController = new SettingsController();
        $settingsController->only_return = true;

        $categories = Category::where('active', true)->get();
        $subCategorias = [];
        $products_array = [];

        foreach ($categories as $category) {
            $category->thickness = $category->thickness()->get();
            $subcategories = SubCategory::where('sub_category.active', true)
                ->where('sub_category.category_id', $category->id)
                ->select(
                    'sub_category.*',
                    'glass_type.name as glass_type_name'
                )
                ->join('glass_type', 'glass_type.id', '=', 'sub_category.glass_type_id')
                ->get();

            $subCategorias[$category->id] = $subcategories;

            foreach ($subcategories as $subcategory) {

                $products_array[] = [
                    "name" => $category->name . " " . $subcategory->name,
                    "str_created" => date('d/m/Y H:i', strtotime($subcategory->created_at)),
                    "custom_name" => $subcategory->additional_name,
                    "glass_type_name" => $subcategory->glass_type_name,
                    "glass_type_id" => $subcategory->glass_type_id,
                    "category_id" => $category->id,
                    "sub_category_id" => $subcategory->id,
                    "obs" => null,
                    "category_name" => $category->name,
                    "sub_category_name" => $subcategory->name,
                    "image" => $subcategory->image,
                ];
            }
        }

        if($orderId):

            $order = Orders::where('orders.id', $orderId)
                ->select(
                    'orders.*',
                    'client.name as client_name',
                    'users.name as user_name'
                )
                ->join('client', 'client.id', '=', 'orders.client_id')
                ->join('users', 'users.id', '=', 'orders.user_id')
                ->first();
            $order->str_created = date('d/m/Y H:i', strtotime($order->created_at));

        else:
            $order = new Orders();
        endif;

        $data = [
            'existsOrder' => $existsOrder,
            'readOnly' => !in_array($_SESSION['permission_id'], [1, 2, 3]),
            'showPrice' => in_array($_SESSION['permission_id'], [1, 2, 3]),
            'min_date' => date('Y-m-d'),
            'order' => $order,
            'orderStatus' => $orderStatus,
            'orderFinance' => $orderFinance,
            'categories' => $categories,
            'subCategorias' => $subCategorias,
            'products' => $products_array,
            'clients_array' => $clients_array,
            'thickness' => $settingsController->glass_thickness(),
            'types' => $settingsController->glass_type(),
            'colors' => $settingsController->glass_colors(),
            'clearances' => $settingsController->glass_clearances(),
            'finish' => $settingsController->glass_finish(),
        ];

        $functionController->exportVarsToJS($data);
    }

    public function getOrder($orderId=false)
    {
        $functionController = new FunctionController();
        $functionController->is_dashboard(false);
        $functionController->is_('orders_page', true);

        $this->prepareOrderData($orderId);

        define('TITLE_PAGE', 'Fixa Vidros - ' . $functionController->locale('menu_item_orders'));
        define('SUBTITLE_PAGE', $functionController->locale('menu_item_orders'));
        $this->render('order', ['button' => "None"]);
    }

    public function newOrder()
    {
        $functionController = new FunctionController();
        $functionController->api = true;
        $status_code = 200;

        $response = $functionController->baseResponse();
        $data = $functionController->postStatement($_POST);

        $order = new Orders();
        $order->client_id = $data->client_id;
        $order->user_id = $_SESSION['id'];
        $order->total_price = $data->total_price;
        $order->obs_client = $data->obs_client;
        $order->date_delivery = $data->date_delivery;
        $order->save();

        $items = [];
        foreach ($data->items as $key => $value) {
            foreach ($value as $item) {
                $base = [];
                $base['order_id'] = $order->id;
                $base['category_id'] = $item->category_id;
                $base['sub_category_id'] = $item->sub_category_id;
                if (intval($item->product_id)):
                    $base['product_id'] = $item->product_id;
                endif;
                $base['glass_thickness_id'] = $item->glass_thickness_id;
                $base['glass_color_id'] = $item->glass_color_id;
                $base['glass_finish_id'] = $item->glass_finish_id;
                $base['glass_clearances_id'] = $item->glass_clearances_id;
                $base['glass_type_id'] = $item->glass_type_id;
                $base['quantity'] = $item->quantity;
                $base['width'] = $item->width;
                $base['height'] = $item->height;
                $base['obs_factory'] = $item->obs_factory;
                $base['obs_client'] = $item->obs_client;
                $base['obs_tempera'] = $item->obs_tempera;
                $base['price'] = $item->price;
                $items[] = $base;
            }
        }

        $order = Orders::find($order->id);
        try{
            $order->items()->createMany($items);
            $response->message = $functionController->locale('register_success_created');
        }catch (Exception $e){
            $response->message = $e->getMessage();
        }

        $response->status = "success";
        $response->dialog = true;
        $response->url = "/order/{$order->id}";
        $response->spinner = true;
        $functionController->sendResponse($response, $status_code);
    }

    public function updateOrder($orderId)
    {
        $functionController = new FunctionController();
        $functionController->api = true;
        $status_code = 200;

        try{

            $data = $functionController->customPutStatement();
            $response = $functionController->baseResponse();

            $order = Orders::find($orderId);
            $order->status_id = $data->status_id;
            $order->finance_id = $data->finance_id;
            $order->total_price = $data->total_price;
            $order->client_id = $data->client_id;
            $order->obs_client = $data->obs_client;
            $order->date_delivery = $data->date_delivery;


            if (!empty($data->ids_to_remove)) {
                $ids = explode(',', $data->ids_to_remove);
                $order->items()->whereIn('id', $ids)->delete();
            }

            $order->save();

            $needsCreate = [];
            foreach ($data->items as $key => $value) {
                foreach ($value as $item) {
                    $base = [];
                    $base['category_id'] = $item->category_id;
                    $base['sub_category_id'] = $item->sub_category_id;
                    if (intval($item->product_id)):
                        $base['product_id'] = $item->product_id;
                    endif;
                    $base['glass_thickness_id'] = $item->glass_thickness_id;
                    $base['glass_color_id'] = $item->glass_color_id;
                    $base['glass_finish_id'] = $item->glass_finish_id;
                    $base['glass_clearances_id'] = $item->glass_clearances_id;
                    $base['quantity'] = $item->quantity;
                    $base['width'] = $item->width;
                    $base['height'] = $item->height;
                    $base['obs_factory'] = $item->obs_factory;
                    $base['obs_client'] = $item->obs_client;
                    $base['obs_tempera'] = $item->obs_tempera;
                    $base['price'] = $item->price;
                    if (!$order->items()->where('id', $item->id)->exists()) {
                        $needsCreate[] = $base;
                    }else{
                        OrdersItems::where('id', $item->id)->update($base);
                    }

                }
            }

            $response->message = $functionController->locale('register_success_update');
            if ($needsCreate) {
                $order->items()->createMany($needsCreate);
            }

    //        $user = User::find($_SESSION['id']);
    //        $user->setLog('Orders', "Usuário atualizou o pedido {$order->id}");

            $response->status = "success";
            $response->reload = true;
            $response->dialog = true;
            $response->spinner = true;

            $functionController->sendResponse($response, $status_code);

        }catch (Exception $e){
            $response->message = $e->getMessage();
            $response->dialog = true;
            $response->spinner = true;
            $functionController->sendResponse($response, $status_code);
            return;
        }
    }

    public function changeOrder($orderId)
    {
        $functionController = new FunctionController();
        $functionController->api = true;
        $response = $functionController->baseResponse();
        $status_code = 200;

        $orderModel = new Orders();
        $order_search = $orderModel->find($orderId);

        $order_search->status = "Cancelado";
        $user = User::find($_SESSION['id']);
        $user->setLog('Orders', "O status do pedido {$order_search->id} foi modificado para " . ($order_search->status) . " - {$_SESSION['name']}");
        $order_search->save();

        $response->message = $functionController->locale('register_success_update');
        $response->status = "success";
        $response->dialog = true;
        $functionController->sendResponse($response, $status_code);
    }
}