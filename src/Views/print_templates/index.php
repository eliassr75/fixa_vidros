<?php include_once __DIR__ . '/../htmlInit.php'; ?>
<?php require_once __DIR__ . '/../htmlHead.php'; ?>
<?php require_once __DIR__ . '/../bodyContentInit.php'; ?>

<?php

use App\Controllers\FunctionController;
$functionController = new FunctionController();

?>

<div class="section mt-2">
    <ul class="listview image-listview inset list my-2">
        <?php if(count($templates)): foreach ($templates as $template):

            $template->created_text = date('d/m/Y H:i', strtotime($template->created_at));

            ?>
            <li id="li-model">
                <a href="javascript:void(0)" onclick='actionForm("editPrintTemplates", <?=$functionController->parseObjectToJson($template)?>)'
                   data-bs-toggle="modal" data-bs-target="#actionSheetForm" class="item">
                    <div class="icon-box bg-<?=$template->active ? "primary" : "danger"?>">
                        <ion-icon name="print-outline"></ion-icon>
                    </div>
                    <div class="in">
                        <?=$template->name?>
                        <div>
                            <?php if($template->width): ?>
                                <span class="badge badge-primary">
                                    <ion-icon name="code-outline" class="fs-5 me-1"></ion-icon>
                                    <?=$template->width?> <?=$template->type?>
                                </span>
                            <?php endif; ?>
                            <?php if($template->height): ?>
                                <span class="badge badge-primary">
                                    <ion-icon name="chevron-expand-outline" class="fs-5 me-1"></ion-icon>
                                    <?=$template->height?> <?=$template->type?>
                                </span>
                            <?php endif; ?>
                        </div>
                    </div>
                </a>
            </li>
        <?php endforeach; else: ?>
            <div class="alert alert-primary" role="alert">
                <?= $functionController->locale('not_found_results') ?>
            </div>
        <?php endif; ?>
    </ul>
</div>

<?php require_once __DIR__ . '/../bodyContentEnd.php'; ?>
<?php require_once __DIR__ . '/../htmlEnd.php';?>
