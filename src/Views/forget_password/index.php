<?php

use App\Controllers\FunctionController;
$functionsController = new FunctionController();

?>

<?php include_once __DIR__ . '/../htmlInit.php'; ?>
<?php require_once __DIR__ . '/../htmlHead.php'; ?>
<?php require_once __DIR__ . '/../bodyContentInit.php'; ?>

<div class="section mt-2 text-center">
    <img src="/assets/img/logo-fixa.png" class="w-75" alt="">
</div>
<div class="section mb-5 p-2">

    <form data-method="POST" data-action="<?=$route?>" data-ajax="default" data-callback="">

        <div class="card">
            <div class="card-body pb-1">

                <div class="form-group basic animated">
                    <div class="input-wrapper">
                        <label class="label" for="email"><?=$functionsController->locale('input_email')?></label>
                        <input type="email" class="form-control" id="email" name="email" placeholder="<?=$functionsController->locale('input_email')?>" required>
                        <i class="clear-input">
                            <ion-icon name="close-circle"></ion-icon>
                        </i>
                    </div>
                </div>

            </div>
        </div>


        <div class="form-links mt-2">
            <div>
                <a href="/new-account/"><?=$functionsController->locale('create_account')?></a>
            </div>
            <div>
                <a href="/login/"><?=$functionsController->locale('exists_account')?></a>
            </div>
        </div>

        <div class="form-button-group  transparent">
            <button type="submit" class="btn btn-primary btn-block btn-lg btn-submit">
                <?=$functionsController->locale('recovery_password')?>
            </button>
        </div>

    </form>
</div>


<?php require_once __DIR__ . '/../bodyContentEnd.php'; ?>
<?php require_once __DIR__ . '/../htmlEnd.php';?>
