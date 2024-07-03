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
                <?php if(isset($newAccount) and $newAccount === true){ ?>

                    <div class="form-group basic animated">
                        <div class="input-wrapper">
                            <label class="label" for="name">Nome e último nome</label>
                            <input type="text" class="form-control" id="name" name="name" placeholder="Nome e último nome" required>
                            <i class="clear-input">
                                <ion-icon name="close-circle"></ion-icon>
                            </i>
                        </div>
                    </div>

                    <div class="form-group basic animated">
                        <div class="input-wrapper">
                            <label class="label" for="email">E-mail</label>
                            <input type="email" class="form-control" id="email" name="email" placeholder="E-mail" required>
                            <i class="clear-input">
                                <ion-icon name="close-circle"></ion-icon>
                            </i>
                        </div>
                    </div>

                    <div class="form-group basic animated">
                        <div class="input-wrapper">
                            <label class="label" for="password1">Senha</label>
                            <input type="password" class="form-control" id="password" name="password"
                            autocomplete="new-password" placeholder="Senha" required>
                            <i class="clear-input">
                                <ion-icon name="close-circle"></ion-icon>
                            </i>
                        </div>
                    </div>
                <?php }else{ ?>

                    <div class="form-group basic animated">
                        <div class="input-wrapper">
                            <label class="label" for="email1">E-mail</label>
                            <input type="email" class="form-control" id="email" name="email" placeholder="E-mail"
                                   autocomplete="new-password" required>
                            <i class="clear-input">
                                <ion-icon name="close-circle"></ion-icon>
                            </i>
                        </div>
                    </div>

                    <div class="form-group basic animated">
                        <div class="input-wrapper">
                            <label class="label" for="password1">Senha</label>
                            <input type="password" class="form-control" id="password" name="password"
                                autocomplete="new-password" placeholder="Senha" required>
                            <i class="clear-input">
                                <ion-icon name="close-circle"></ion-icon>
                            </i>
                        </div>
                    </div>
                <?php } ?>
            </div>
        </div>


        <div class="form-links mt-2">
            <div>
                <a href="/new-account/"><?=$functionsController->locale('create_account')?></a>
            </div>
            <div><a href="app-forgot-password.html" class="text-muted"><?=$functionsController->locale('forget_password')?></a></div>
        </div>

        <div class="form-button-group  transparent">
            <button type="submit" class="btn btn-primary btn-block btn-lg btn-submit">Acessar</button>
        </div>

    </form>
</div>


<?php require_once __DIR__ . '/../bodyContentEnd.php'; ?>
<?php require_once __DIR__ . '/../htmlEnd.php';?>
