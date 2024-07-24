<?php

namespace App\Controllers;

use App\Models\Category;
use App\Models\Client;
use App\Models\GlassThickness;
use App\Models\GlassType;
use App\Models\Permissions;
use App\Models\Product;
use App\Models\SubCategory;
use App\Models\User;
use Doctrine\Inflector\Rules\Transformation;
use Exception;

class ProductController extends BaseController
{
    public function index()
    {
        $functionController = new FunctionController();
        $functionController->is_dashboard(false);
        $functionController->is_('products_page', true);

        define('TITLE_PAGE', 'Fixa Vidros - ' . $functionController->locale('menu_item_products'));
        define('SUBTITLE_PAGE', $functionController->locale('menu_item_products'));

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

        $this->render('products', [
            'products' => $products_array,
            'button' => 'add',
            'url' => '/product/new/'
        ]);
    }

    public function categoriesJson($Id)
    {
        $functionController = new FunctionController();
        $functionController->api = true;
        $status_code = 200;

        $subCategory = SubCategory::where('glass_type_id', $Id)->first();
        $categories = [];

        foreach ($subCategory->categories() as $category) {
            $category->subCategory = $category->sub_categories()->get();
            $categories[] = $category;
        }

        $functionController->sendResponse($categories, $status_code);
    }

    public function json($clientId=false)
    {
        $functionController = new FunctionController();
        $functionController->api = true;

        $user = false;
        $users_array = [];

        if($clientId):
            $user = Client::find($clientId);
            $user->str_created = date('d/m/Y H:i', strtotime($user->created_at));
            $user->current_permission = $user->current_permission();

            $newMissingDataKeys = [];
            foreach ($user->missingDataKeys as $missingDataKey) {
                $key_name = $missingDataKey['name'];
                $missingDataKey['label'] = ucfirst($functionController->locale("input_{$missingDataKey['name']}"));
                if ($key_name === "birthday"):
                    $missingDataKey['value'] = $user->$key_name ? date('d/m/Y', strtotime($user->$key_name)) : null;
                else:
                    $missingDataKey['value'] = $user->$key_name;
                endif;
                $newMissingDataKeys[] = $missingDataKey;
            }
            $user->missing_data = $newMissingDataKeys;

        else:
            $users = Client::with('permissions')->orderBy('id', 'desc')->get();
            foreach ($users as $user_obj) {
                $user_obj->str_created = date('d/m/Y H:i', strtotime($user_obj->created_at));
                $user_obj->str_updated = date('d/m/Y H:i', strtotime($user_obj->updated_at));
                $user_obj->str_birthday = date('d/m/Y', strtotime($user_obj->birthday));
                $user_obj->current_permission = $user_obj->current_permission();

                $newMissingDataKeys = [];
                foreach ($user_obj->missingDataKeys as $missingDataKey) {
                    $key_name = $missingDataKey['name'];
                    $missingDataKey['label'] = $functionController->locale("input_{$missingDataKey['name']}");
                    $missingDataKey['value'] = $user_obj->$key_name;
                    $newMissingDataKeys[] = $missingDataKey;
                }
                $user_obj->missing_data = $newMissingDataKeys;

                $users_array[] = $user_obj;
            }
        endif;

        $permissions = Permissions::orderBy('id', 'asc')->get();
        $functionController->sendResponse([
            'users' => $users_array,
            'user' => $user,
            'permissions' => $permissions,
        ]);
    }

    public function getProduct($productId=false)
    {
        $functionController = new FunctionController();
        $functionController->is_dashboard(false);
        $functionController->is_('products_page', true);

        $defaultThickness = GlassThickness::where('glass_type_id', null)
            ->where('active', true)
            ->where('products_id', null)->orderBy('id', 'desc')->get();
        $types = GlassType::where('active', true)->get();
        $categories = Category::where('active', true)->get();
        $category = false;

        if($productId):

            $data = $functionController->postStatement($_POST);
            if(!empty($data)):

                $product = Product::find($productId);

            endif;

            $product = Product::where('products.id', $productId)->
            select(
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
                ->first();
            $product->str_created = date('d/m/Y H:i', strtotime($product->created_at));
            $category = Category::find($product->category_id);

        else:
            $product = new Product();
        endif;

        define('TITLE_PAGE', 'Fixa Vidros - ' . $functionController->locale('menu_item_products'));
        define('SUBTITLE_PAGE', $functionController->locale('menu_item_products'));

        $this->render('product', [
            'product' => $product,
            'button' => "None",
            'defaultThickness' => $defaultThickness,
            'types' => $types,
            'categories' => $categories,
            'category' => $category,
        ]);
    }

    public function newClient()
    {
        $functionController = new FunctionController();
        $functionController->api = true;
        $status_code = 200;

        $data = $functionController->postStatement($_POST);
        $client = new Client();

        $response = $functionController->baseResponse();
        $invalid_document = false;

        foreach ($client->missingDataKeys as $missingDataKey) {
            $key_name = $missingDataKey['name'];
            
            if ($key_name === 'birthday'):
                if (!empty($data->$key_name)):
                    $birthday = str_replace('/', '-', $data->$key_name);
                    $birthday = date('Y-m-d', strtotime($birthday));

                    $client->$key_name = $birthday;
                    $client->age = $functionController->timeDiff($birthday, date('Y-m-d'), 'years');
                endif;
            elseif($key_name === 'document'):

                $cpf = $functionController->validaCPF($data->document);
                $cnpj = $functionController->validateCNPJ($data->document);

                if(!$cpf and !$cnpj):
                    $invalid_document = true;
                    break;
                else:
                    $client->$key_name = $data->$key_name;
                endif;
            elseif ($key_name === 'phone_number'):
                if (strlen($data->$key_name) >= 14):
                    $client->$key_name = $data->$key_name;
                endif;
            else:
                $client->$key_name = $data->$key_name;
            endif;
        }

        if ($invalid_document):

            $response->message = $functionController->locale('invalid_document');
            $response->dialog = true;
            $response->status = "warning";
            $status_code = 400;
        else:

            $client->validate();
            $client->save();
            $user = User::find($_SESSION['id']);
            $user->setLog('Client', "Usuário criou o cliente {$client->id}");
            $response->message = $functionController->locale('register_success_update');
            $response->status = "success";
            $response->url = "/clients/";
            $response->dialog = true;
            $response->spinner = true;
        endif;

        $functionController->sendResponse($response, $status_code);
    }

    public function updateProduct($clientId)
    {
        $functionController = new FunctionController();
        $functionController->api = true;
        $status_code = 200;

        $data = $functionController->putStatement();
        $product = Product::find($clientId);
        $response = $functionController->baseResponse();

        $product->name = $data->name;
        $product->custom_name = $data->custom_name;
        $product->obs = $data->obs;
        $product->active = (isset($data->active) and $data->active === 'on');
        $product->save();

        $user = User::find($_SESSION['id']);
        $user->setLog('Product', "Usuário atualizou o produto {$product->id}");
        $response->message = $functionController->locale('register_success_update');
        $response->status = "success";
        $response->url = "/products/";
        $response->dialog = true;
        $response->spinner = true;

        $functionController->sendResponse($response, $status_code);
    }

    public function changeProduct($productId)
    {
        $functionController = new FunctionController();
        $functionController->api = true;
        $response = $functionController->baseResponse();
        $status_code = 200;

        $productModel = new Product();
        $product_search = $productModel->find($productId);

        $product_search->active = !$product_search->active;
        $user = User::find($_SESSION['id']);
        $user->setLog('Product', "O status do produco {$product_search->id} foi modificado para " . ($product_search->active ? "Ativo" : "Inativo") . " - {$_SESSION['name']}");
        $product_search->save();

        $response->message = $functionController->locale('register_success_update');
        $response->status = "success";
        $response->dialog = true;
        $functionController->sendResponse($response, $status_code);
    }
}