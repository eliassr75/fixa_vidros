<?php

require_once '../config/cors.php';
require '../vendor/autoload.php';
require '../config/database.php';

use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use App\Models\Client;

$inputFileName = 'clientes.csv';

try {
    // Carrega o arquivo CSV usando IOFactory
    $spreadsheet = IOFactory::load($inputFileName);

    // Seleciona a primeira planilha
    $worksheet = $spreadsheet->getActiveSheet();

    // Obtenha o cabeçalho (primeira linha) para mapear os nomes das colunas
    $header = [];
    $c = 0;
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

            $client = Client::where('document', $rowData['document'] ?? "000.000.000-0{$c}")->first();
            if (!$client):
                $client = new Client();
            endif;

            $client->id = $rowData['id'];
            $client->email = $rowData['email'];
            $client->name = $rowData['company_name'];
            $client->company_name = $rowData['company_name'];
            $client->trading_name = $rowData['trading_name'];
            $client->document = $rowData['document'] ?? "00.000.000/{$c}-00";
            $client->phone_number = $rowData['phone'] ?? ($rowData['cellphone'] ?? "(00) 00000-0000");
            $client->zip_code = $rowData['zip_code'];
            $client->address = $rowData['address'] ?? "--";
            $client->complement = $rowData['complement'] ?? "--";
            $client->address_number = 0;
            $client->zone = "--";
            $client->city = $rowData['city'] ?? "--";
            $client->state = $rowData['state'] ?? "--";
            $client->birthday = $rowData['birthday'] ? date('Y-m-d', strtotime(str_replace('/','-', $rowData['birthday']))) : null;
            $client->obs = $rowData['obs'];
            $client->validate();
            $client->save();

            $c++;

            print ("Cliente {$client->name} criado com sucesso.\n");

        }
    }

} catch (\PhpOffice\PhpSpreadsheet\Reader\Exception $e) {
    echo 'Erro ao carregar o arquivo: ', $e->getMessage();
}

