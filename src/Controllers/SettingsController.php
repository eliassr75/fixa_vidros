<?php

namespace App\Controllers;

use App\Models\Category;
use App\Models\GlassClearances;
use App\Models\GlassColors;
use App\Models\GlassFinish;
use App\Models\GlassSize;
use App\Models\GlassThickness;
use App\Models\GlassType;
use App\Models\PrintTemplates;
use App\Models\SubCategory;
use App\Models\User;
use Exception;
use Statickidz\GoogleTranslate;

class SettingsController extends BaseController
{

    public function addImage()
    {
        $functionController = new FunctionController();
        $functionController->api = true;

        $status_code = 200;
        $response = $functionController->baseResponse();

        if (isset($_FILES['image'])) {

            $uploadDir =  __DIR__ . '/../../public/assets/img/uploads/';
            $file = $_FILES['image'];
            $fileName = $file['name'];
            $fileTmpName = $file['tmp_name'];
            $fileSize = $file['size'];
            $fileError = $file['error'];
            $fileType = $file['type'];

            // Checando erros
            if ($fileError === 0) {
                // Gerando um novo nome de arquivo com hash
                $fileExt = pathinfo($fileName, PATHINFO_EXTENSION);
                $newFileName = hash('sha256', $fileName . time()) . '.' . $fileExt;
                $fileDestination = $uploadDir . $newFileName;

                // Movendo o arquivo para o destino
                if (move_uploaded_file($fileTmpName, $fileDestination)) {
                    $response->image = '/assets/img/uploads/' . $newFileName;
                } else {
                    $response->status = 'error';
                    $response->message = 'Failed to move uploaded file';
                }
            } else {
                $response->status = 'error';
                $response->message = 'File upload error';
            }
        }else{
            $response->image = "/assets/img/sample/photo/1.jpg";
            $response->status = 'warning';
            $response->message = 'Not file founded';
        }

        $functionController->sendResponse($response, $status_code);
    }

    public function print($routeName, $Id, $printId)
    {
        $functionController = new FunctionController();

        switch ($routeName) {
            default:
                break;
        }

        $print = PrintTemplates::find($printId);

        $this->render(
            "print_preview", [
                "print" => $print
            ]
        );


    }
    public function update($routeName, $Id)
    {
        $functionController = new FunctionController();
        $functionController->api = true;

        $status_code = 200;
        $response = $functionController->baseResponse();
        $data = $functionController->putStatement();

        switch ($routeName) {
            case 'subcategory':

                $subCategory = SubCategory::find($Id);

                $subCategory->name = $data->name;
                $subCategory->additional_name = $data->additional_name;
                $subCategory->image = $data->image;
                $subCategory->category_id = $data->category_id;
                $subCategory->glass_type_id = $data->type;

                $subCategory->save();

                break;
            case 'category':
                $category = Category::find($Id);

                $category->name = $data->name;
                $category->active = (isset($data->active) and $data->active === 'on');

                $category->save();
                break;
            case 'glass_type':
                $glass_type = GlassType::find($Id);

                $glass_type->name = $data->name;
                $glass_type->active = (isset($data->active) and $data->active === 'on');

                $glass_type->save();
                break;
            case 'glass_thickness':
                $thickness = GlassThickness::find($Id);

                $thickness->name = $data->name;
                $thickness->price = $data->price;
                $thickness->active = (isset($data->active) and $data->active === 'on');

                $array = explode("/", $data->type);
                $thickness->type = end($array);
                $thickness->category = $array[0];

                $thickness->save();
                break;
            case 'glass_colors':
                $glass_color = GlassColors::find($Id);

                $glass_color->name = $data->name;
                $glass_color->percent = $data->percent;
                $glass_color->active = (isset($data->active) and $data->active === 'on');

                $glass_color->save();
                break;
            case 'glass_finish':
                $glass_finish = GlassFinish::find($Id);

                $glass_finish->name = $data->name;
                $glass_finish->active = (isset($data->active) and $data->active === 'on');

                $glass_finish->save();
                break;
            case 'glass_clearances':
                $glass_finish = GlassClearances::find($Id);

                $glass_finish->name = $data->name;
                $glass_finish->width = $data->width;
                $glass_finish->height = $data->height;
                $glass_finish->active = (isset($data->active) and $data->active === 'on');

                $glass_finish->save();
                break;
            case 'print_templates':

                $template = PrintTemplates::find($Id);
                $template->name = $data->name;
                $template->width = $data->width;
                $template->height = $data->height;
                $template->spacing = $data->spacing;
                $template->model = $data->model;
                $template->active = (isset($data->active) and $data->active === 'on');

                $template->save();
                break;
        }

        $response->message = $functionController->locale('register_success_update');
        $response->status = "success";
        $response->reload = true;
        $response->spinner = true;

        $functionController->sendResponse($response, $status_code);
    }

    public function create($routeName)
    {
        $functionController = new FunctionController();
        $functionController->api = true;

        $status_code = 200;
        $response = $functionController->baseResponse();
        $response->reload = true;
        $response->spinner = true;
        $response->dialog = true;

        $data = $functionController->postStatement($_POST);

        switch ($routeName) {
            case 'subcategory':

                $subCategory = new SubCategory();

                $subCategory->name = $data->name;
                $subCategory->additional_name = $data->additional_name;
                $subCategory->image = $data->image;
                $subCategory->category_id = $data->category_id;
                $subCategory->glass_type_id = $data->type;

                $subCategory->save();
                $response->dialog = false;

                break;
            case 'category':
                $category = new Category();

                $category->name = $data->name;
                $category->active = (isset($data->active) and $data->active === 'on');

                $category->save();
                $response->url = "/settings/category/{$category->id}/";
                $response->reload = false;
                break;
            case 'glass_type':
                $glass_type = new GlassType();

                $glass_type->name = $data->name;
                $glass_type->active = (isset($data->active) and $data->active === 'on');

                $glass_type->save();
                $response->dialog = false;
                break;
            case 'glass_thickness':
                $thickness = new GlassThickness();

                $thickness->name = $data->name;
                $thickness->price = $data->price;
                $thickness->active = (isset($data->active) and $data->active === 'on');

                $array = explode("/", $data->type);
                $thickness->type = end($array);
                $thickness->category = $array[0];

                $thickness->save();
                $response->dialog = false;
                break;
            case 'glass_colors':
                $glass_color = new GlassColors();

                $glass_color->name = $data->name;
                $glass_color->percent = $data->percent;
                $glass_color->active = (isset($data->active) and $data->active === 'on');

                $glass_color->save();
                $response->dialog = false;
                break;
            case 'glass_finish':
                $glass_finish = new GlassFinish();

                $glass_finish->name = $data->name;
                $glass_finish->active = (isset($data->active) and $data->active === 'on');

                $glass_finish->save();
                $response->dialog = false;
                break;
            case 'glass_clearances':
                $glass_finish = new GlassClearances();

                $glass_finish->name = $data->name;
                $glass_finish->width = $data->width;
                $glass_finish->height = $data->height;
                $glass_finish->active = (isset($data->active) and $data->active === 'on');

                $glass_finish->save();
                $response->dialog = false;
                break;

            case 'print_templates':
                $template = new PrintTemplates();

                $template->name = $data->name;
                $template->width = $data->width;
                $template->height = $data->height;
                $template->spacing = $data->spacing;
                $template->model = $data->model;
                $template->active = (isset($data->active) and $data->active === 'on');

                $template->save();
                break;
        }

        $response->message = $functionController->locale('register_success_created');
        $response->status = "success";

        $functionController->sendResponse($response, $status_code);
    }

    public function category($categoryId=false, $json=false)
    {
        $functionController = new FunctionController();
        $functionController->is_dashboard(false);
        $functionController->is_('settings_page', true);

        define('TITLE_PAGE', 'Fixa Vidros - ' . $functionController->locale('menu_item_category'));
        define('SUBTITLE_PAGE', $functionController->locale('menu_item_category'));

        if($categoryId):

            $data = $functionController->getStatement($_GET);
            $status_code = 200;
            $category = Category::find($categoryId);

            if($json):
                $functionController->api = true;
            endif;

            $thickness = GlassThickness::where('glass_type_id', null)
                ->where('sub_category_id', null)->orderBy('id', 'desc')->get();
            $types = GlassType::where('active', true)->get();

            if($json):
                $response = $functionController->baseResponse();

                $values = [];

                try{
                    if(isset($data->type) && intval($data->type)):
                        $categories = $category->sub_categories()->where('glass_type_id', $data->type)->get();
                    else:
                        $categories = $category->sub_categories()->get();
                    endif;
                }catch (Exception $e){
                    $categories = $category->sub_categories()->get();
                }

                foreach ($categories as $subCategory):
                    $subCategory->glass_type = GlassType::find($subCategory->glass_type_id);
                    $subCategory->created_text = date('d/m/Y H:i', strtotime($subCategory->created_at));
                    $values[] =  $subCategory;
                endforeach;

                $response->values = [
                    "subCategories" => $values,
                    "thickness" => $thickness,
                    "types" => $types
                ];
                $functionController->sendResponse($response, $status_code);

            else:

                $this->render(
                    "category", [
                        "button" => "None",
                        "category" => $category,
                        "default" => [
                            "thickness" => $thickness,
                            "types" => $types
                        ]
                    ]
                );

            endif;

        else:

            $categories = Category::all();
            $this->render(
                "categories", [
                    "button" => "add",
                    "actionForm" => "addCategory",
                    "categories" => $categories,
                ]
            );
        endif;
    }

    public function glass_type()
    {
        $functionController = new FunctionController();
        $functionController->is_dashboard(false);
        $functionController->is_('settings_page', true);

        define('TITLE_PAGE', 'Fixa Vidros - ' . $functionController->locale('menu_item_glass_type'));
        define('SUBTITLE_PAGE', $functionController->locale('menu_item_glass_type'));

        $type = GlassType::all();

        $this->render(
            "glass_type", [
                "button" => "add",
                "actionForm" => "addType",
                "type" => $type
            ]
        );
    }

    public function glass_thickness()
    {
        $functionController = new FunctionController();
        $functionController->is_dashboard(false);
        $functionController->is_('settings_page', true);

        define('TITLE_PAGE', 'Fixa Vidros - ' . $functionController->locale('menu_item_glass_thickness'));
        define('SUBTITLE_PAGE', $functionController->locale('menu_item_glass_thickness'));

        $thickness = GlassThickness::where('glass_type_id', null)->
            where('sub_category_id', null)->orderBy('id', 'desc')->get();

        $this->render(
            "glass_thickness", [
                "button" => "add",
                "actionForm" => "addThickness",
                "thickness" => $thickness
            ]
        );
    }

    public function glass_colors()
    {
        $functionController = new FunctionController();
        $functionController->is_dashboard(false);
        $functionController->is_('settings_page', true);

        define('TITLE_PAGE', 'Fixa Vidros - ' . $functionController->locale('menu_item_glass_colors'));
        define('SUBTITLE_PAGE', $functionController->locale('menu_item_glass_colors'));

        $colors = GlassColors::all();

        $this->render(
            "glass_colors", [
                "button" => "add",
                "actionForm" => "addColor",
                "colors" => $colors
            ]
        );
    }

    public function glass_finish()
    {
        $functionController = new FunctionController();
        $functionController->is_dashboard(false);
        $functionController->is_('settings_page', true);

        define('TITLE_PAGE', 'Fixa Vidros - ' . $functionController->locale('menu_item_glass_finish'));
        define('SUBTITLE_PAGE', $functionController->locale('menu_item_glass_finish'));

        $finish = GlassFinish::all();

        $this->render(
            "glass_finish", [
                "button" => "add",
                "actionForm" => "addFinish",
                "finish" => $finish
            ]
        );
    }

    public function glass_clearances()
    {
        $functionController = new FunctionController();
        $functionController->is_dashboard(false);
        $functionController->is_('settings_page', true);

        define('TITLE_PAGE', 'Fixa Vidros - ' . $functionController->locale('menu_item_glass_clearances'));
        define('SUBTITLE_PAGE', $functionController->locale('menu_item_glass_clearances'));

        $clearances = GlassClearances::all();

        $this->render(
            "glass_clearances", [
                "button" => "add",
                "actionForm" => "addClearance",
                "clearances" => $clearances
            ]
        );
    }

    public function print_templates()
    {
        $functionController = new FunctionController();
        $functionController->is_dashboard(false);
        $functionController->is_('settings_page', true);

        define('TITLE_PAGE', 'Fixa Vidros - ' . $functionController->locale('menu_item_print_templates'));
        define('SUBTITLE_PAGE', $functionController->locale('menu_item_print_templates'));

        $templates = PrintTemplates::all();

        $this->render(
            "print_templates", [
                "button" => "add",
                "actionForm" => "addPrintTemplates",
                "templates" => $templates
            ]
        );
    }

    public function index()
    {

        $functionController = new FunctionController();
        $functionController->is_dashboard(false);
        $functionController->is_('settings_page', true);


        define('TITLE_PAGE', 'Fixa Vidros - ' . $functionController->locale('menu_item_settings'));
        define('SUBTITLE_PAGE', $functionController->locale('menu_item_settings'));


        $options = [
            [
                "name" => $functionController->locale('menu_item_category'),
                "permissions" => [1],
                "individual" => false,
                "url" => '/settings/category/',
                "icon" => 'copy-outline',
                "badge" => false
            ],
            [
                "name" => $functionController->locale('menu_item_glass_type'),
                "permissions" => [1],
                "individual" => false,
                "url" => '/settings/glass_type/',
                "icon" => 'help-circle-outline',
                "badge" => false
            ],
            [
                "name" => $functionController->locale('menu_item_glass_thickness'),
                "permissions" => [1],
                "individual" => false,
                "url" => '/settings/glass_thickness/',
                "icon" => 'layers-outline',
                "badge" => false
            ],
            [
                "name" => $functionController->locale('menu_item_glass_colors'),
                "permissions" => [1],
                "individual" => false,
                "url" => '/settings/glass_colors/',
                "icon" => 'color-palette-outline',
                "badge" => false
            ],
            [
                "name" => $functionController->locale('menu_item_glass_finish'),
                "permissions" => [1],
                "individual" => false,
                "url" => '/settings/glass_finish/',
                "icon" => 'checkmark-circle-outline',
                "badge" => false
            ],
            [
                "name" => $functionController->locale('menu_item_glass_clearances'),
                "permissions" => [1],
                "individual" => false,
                "url" => '/settings/glass_clearances/',
                "icon" => 'resize-outline',
                "badge" => false
            ],
            [
                "name" => $functionController->locale('menu_item_print_templates'),
                "permissions" => [1],
                "individual" => false,
                "url" => '/settings/print_templates/',
                "icon" => 'print-outline',
                "badge" => false
            ],
        ];

        $user_permission_id = $_SESSION['permission_id'];
        $obj_menu_options = [];
        foreach ($options as $option) {

            if (in_array($user_permission_id, $option['permissions'])):

                $obj_menu_options[] = $option;

            endif;

        }


        $this->render(
        "settings", [
                "button" => "None",
                "menu_options" => $functionController->parseJsonToObject($obj_menu_options)
            ]
        );
    }
}