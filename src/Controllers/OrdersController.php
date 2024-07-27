<?php

namespace App\Controllers;

use App\Models\Category;
use App\Models\Client;
use App\Models\GlassThickness;
use App\Models\GlassType;
use App\Models\Orders;
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
        $functionController->is_dashboard(false);
        $functionController->is_('orders_page', true);

        define('TITLE_PAGE', 'Fixa Vidros - ' . $functionController->locale('menu_item_orders'));
        define('SUBTITLE_PAGE', $functionController->locale('menu_item_orders'));

        $orders = Orders::orderBy('orders.id', 'desc')
        ->select(
            'orders.*',
            'client.name as client_name',
            'users.name as user_name'
        )
        ->join('client', 'client.id', '=', 'orders.client_id')
        ->join('users', 'users.id', '=', 'orders.user_id')
        ->get();
        $orders_array = [];
        foreach ($orders as $order) {
            $order->str_created = date('d/m/Y H:i', strtotime($order->created_at));
            $order->total_items = $order->loadCount('order_items');
            $orders_array[] = $order;
        }

        $this->render('orders', [
            'orders' => $orders_array,
            'button' => 'add',
            'url' => '/order/new/'
        ]);
    }

    public function getOrder($orderId=false)
    {
        $functionController = new FunctionController();
        $functionController->is_dashboard(false);
        $functionController->is_('orders_page', true);


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
        foreach ($categories as $category) {
            $subCategorias[$category->id] = SubCategory::where('sub_category.active', true)
                ->where('sub_category.category_id', $category->id)
                ->select(
                    'sub_category.*',
                    'glass_type.name as glass_type_name',
                    'glass_type.id as glass_type_id',
                )
                ->join('glass_type', 'glass_type.id', '=', 'sub_category.glass_type_id')
                ->get();
        }

        $products = Product::orderBy('products.id', 'desc')
            ->select(
                'products.*',
                'category.name as category_name',
                'category.id as category_id',
                'category.active as category_active',
                'glass_type.name as glass_type_name',
                'glass_type.id as glass_type_id',
                'glass_type.active as glass_type_active',
                'sub_category.name as sub_category_name',
                'sub_category.id as sub_category_id',
                'sub_category.active as sub_category_active',
                'sub_category.image as image'
            )
            ->join('category', 'category.id', '=', 'products.category_id')
            ->join('sub_category', 'sub_category.id', '=', 'products.sub_category_id')
            ->join('glass_type', 'glass_type.id', '=', 'products.glass_type_id')
            ->get();
        $products_array = [];
        foreach ($products as $product) {
            $product->str_created = date('d/m/Y H:i', strtotime($product->created_at));
            $products_array[] = $product;
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

        define('TITLE_PAGE', 'Fixa Vidros - ' . $functionController->locale('menu_item_orders'));
        define('SUBTITLE_PAGE', $functionController->locale('menu_item_orders'));

        $data = [
            'showPrice' => in_array($_SESSION['permission_id'], [1, 2, 3]),
            'order' => $order,
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
        $this->render('order', ['button' => "None"]);
    }

    public function updateOrder($clientId)
    {
        $functionController = new FunctionController();
        $functionController->api = true;
        $status_code = 200;

        $data = $functionController->putStatement();
        $order = Orders::find($clientId);
        $response = $functionController->baseResponse();

        $order->obs = $data->obs;
        $order->active = (isset($data->active) and $data->active === 'on');
        $order->save();

        $user = User::find($_SESSION['id']);
        $user->setLog('Orders', "Usuário atualizou o produto {$order->id}");
        $response->message = $functionController->locale('register_success_update');
        $response->status = "success";
        $response->url = "/orders/";
        $response->dialog = true;
        $response->spinner = true;

        $functionController->sendResponse($response, $status_code);
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