<?php

namespace App\Controllers;

use App\Models\GlassClearances;
use App\Models\GlassColors;
use App\Models\GlassFinish;
use App\Models\GlassSize;
use App\Models\GlassType;
use App\Models\User;
use Exception;
use Statickidz\GoogleTranslate;

class SettingsController extends BaseController
{

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
                "actionForm" => "addSize",
                "type" => $type
            ]
        );
    }

    public function glass_size()
    {
        $functionController = new FunctionController();
        $functionController->is_dashboard(false);
        $functionController->is_('settings_page', true);

        define('TITLE_PAGE', 'Fixa Vidros - ' . $functionController->locale('menu_item_glass_size'));
        define('SUBTITLE_PAGE', $functionController->locale('menu_item_glass_size'));

        $size = GlassSize::all();

        $this->render(
            "glass_size", [
                "button" => "add",
                "actionForm" => "addSize",
                "size" => $size
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
                "actionForm" => "addClearances",
                "clearances" => $clearances
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
                "name" => $functionController->locale('menu_item_glass_size'),
                "permissions" => [1],
                "individual" => false,
                "url" => '/settings/glass_size/',
                "icon" => 'expand-outline',
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