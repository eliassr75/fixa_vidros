<?php

use App\Controllers\MenuController;

$menuController = new MenuController();

foreach (['Animes', 'Mangás'] as $category){
    $menuItems[] = ["name" => $category, "values" => $menuController->getMenuItemsByCategory($category)];
}

?>
<div class="container-fluid my-2 navbar-navlink">
    <nav class="px-3 navbar navbar-expand-lg bg-black">
        <div class="container-fluid">
            <a aria-label="anchor" class="navbar-brand" href="javascript:void(0);">
                <img src="https://animabook.net/img/logo.png" alt="" class="d-inline-block align-text-top">
            </a>
            <button class="navbar-toggler collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="navbar-collapse collapse" id="navbarSupportedContent" style="">
                <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                    <li class="nav-item">
                        <a class="nav-link active" aria-current="page" href="javascript:void(0);">Home</a>
                    </li>
                    <?php foreach ($menuItems as $menuItem){ ?>

                    <li class="nav-item dropdown dropdown-center">
                        <a class="nav-link dropdown-toggle" href="javascript:void(0);" id="navbarDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                            <?=$menuItem["name"]?>
                        </a>
                        <ul class="dropdown-menu w-auto" aria-labelledby="navbarDropdown">
                            <?php foreach ($menuItem["values"] as $subMenuItem){ ?>
                            <li><a class="dropdown-item" href="<?=$subMenuItem["link"]?>"><?=$subMenuItem["title"]?></a></li>
                            <?php } ?>
                        </ul>
                    </li>

                    <?php } ?>
                </ul>

                <a aria-label="anchor" class="nav-link text-muted fs-18 ms-auto d-flex align-content-center" href="javascript:void(0);">
                    <span class="avatar me-2 avatar-rounded">
                        <img src="/assets/images/default.png" alt="img">
                    </span>
                    <span>Elias Craveiro</span>
                </a>

                </div>
            </div>
        </div>
    </nav>
</div>