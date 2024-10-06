<?php

require_once '../config/cors.php';
require '../vendor/autoload.php';
require '../config/database.php';

use App\Models\GlassThickness;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use App\Models\Client;
use App\Models\Category;
use App\Models\SubCategory;
use App\Models\GlassType;


// Função para exibir a barra de progresso
function progressBar($percent, $width = 50)
{
    $bar = '[';
    $pos = floor($percent / 100 * $width);
    for ($i = 0; $i < $width; $i++) {
        if ($i < $pos) {
            $bar .= '=';
        } elseif ($i == $pos) {
            $bar .= '>';
        } else {
            $bar .= ' ';
        }
    }
    $bar .= '] ' . round($percent, 2) . '%';

    return $bar;
}

$inputFileName = 'produtos.csv';

try {
    // Carrega o arquivo CSV usando IOFactory
    $spreadsheet = IOFactory::load($inputFileName);

    // Seleciona a primeira planilha
    $worksheet = $spreadsheet->getActiveSheet();

    $totalRows = $worksheet->getHighestRow();
    $rowCount = 0;

    // Obtenha o cabeçalho (primeira linha) para mapear os nomes das colunas
    $header = [];
    foreach ($worksheet->getRowIterator() as $rowIndex => $row) {

        $cellIterator = $row->getCellIterator();
        $cellIterator->setIterateOnlyExistingCells(false);

        if ($rowIndex == 1) {
            // A primeira linha contém o cabeçalho
            foreach ($cellIterator as $cell) {
                $header[] = $cell->getValue();
            }
        } else {
            // Lê as linhas de dados após o cabeçalho
            $rowData = [];
            $cellIndex = 0;
            foreach ($cellIterator as $cell) {
                $columnName = $header[$cellIndex] ?? 'Unknown'; // Nome da coluna com fallback
                $rowData[$columnName] = $cell->getValue(); // Associa o valor ao nome da coluna
                $cellIndex++;
            }

            $glass_type_name = $rowData['glass_type_name'];
            $glass_type = GlassType::where('name', $glass_type_name)->first();
            if (!$glass_type) {
                $glass_type = new GlassType();
                $glass_type->name = $glass_type_name;
                $glass_type->ncm = $rowData['NCM'];
            }else{
                $glass_type->ncm = $rowData['NCM'];
            }
            $glass_type->save();

            $category_name = $rowData['category_name'];
            $category = Category::where('name', $category_name)->first();
            if(!$category){

                $category = new Category();
                $category->name = $category_name;
                $category->save();

                $thickness = GlassThickness::where('glass_type_id', null)
                    ->where('products_id', null)
                    ->where('category_id', null)
                    ->orderBy('id', 'desc')->get();

                $values = [];
                if (!$category->thickness()->exists()):
                    foreach ($thickness as $thick):
                        $values[] = [
                            'name' => $thick->name,
                            'price' => $thick->price,
                            'type' => $thick->type,
                            'category' => $thick->category,
                            'active' => $thick->active,
                        ];
                    endforeach;
                    $category->thickness()->createMany($values);
                endif;
            }

            $sub_category_additional_description = $rowData['sub_category_additional_description'];
            $sub_category_description = $rowData['sub_category_description'];
            $sub_category_active = boolval(intval($rowData['sub_category_active']));
            $image = $rowData['image'];

            if (!str_contains($image, 'http')) {
                // Encontrar a posição da última ocorrência de 'png/' para garantir a reconstrução correta da URL
                $pos = strrpos($image, 'png/');

                // Se 'png/' for encontrado, usar a parte após essa ocorrência para construir a URL completa
                if ($pos !== false) {
                    $image = "https://sistema.wvetro.com.br/wvetro/" . substr($image, $pos + 4); // '+ 4' para remover 'png/'
                } else {
                    // Se 'png/' não for encontrado, apenas adicionar o prefixo à imagem
                    $image = "https://sistema.wvetro.com.br/wvetro/" . $image;
                }
            }

            $sub_category = SubCategory::where('name', $sub_category_description)
                ->where('category_id', $category->id)
                ->first();

            if (!$sub_category) {
                $sub_category = new SubCategory();
                $sub_category->name = $sub_category_description;
                $sub_category->additional_name = $sub_category_additional_description;
                $sub_category->image = $image;
                $sub_category->active = $sub_category_active;
                $sub_category->glass_type_id = $glass_type->id;
                $sub_category->category_id = $category->id;
            }else{
                $sub_category->name = $sub_category_description;
                $sub_category->additional_name = $sub_category_additional_description;
                $sub_category->image = $image;
                $sub_category->active = $sub_category_active;
                $sub_category->glass_type_id = $glass_type->id;
            }
            $sub_category->save();

            $rowCount++;

            // Calcular o progresso percentual
            $progress = ($rowCount / ($totalRows-1)) * 100;

            // Atualizar a barra de progresso
            echo "\r" . progressBar($progress, 50) . " $rowCount/".$totalRows-1;

        }
    }

} catch (\PhpOffice\PhpSpreadsheet\Reader\Exception $e) {
    echo 'Erro ao carregar o arquivo: ', $e->getMessage();
}

