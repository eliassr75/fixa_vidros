<!-- App Header -->
<?php if(isset($_SESSION['dashboard']) and $_SESSION['dashboard']): ?>
<div class="appHeader bg-primary text-light">
    <div class="left">
        <a href="#" class="headerButton" data-bs-toggle="modal" data-bs-target="#sidebarPanel">
            <ion-icon name="menu-outline"></ion-icon>
        </a>
    </div>
    <div class="pageTitle" ondblclick="window.location.href='/logout/'">
        <img src="/assets/img/logo-fixa.png" alt="logo" class="logo">
    </div>
    <div class="right">
        <a href="/notifications/" class="headerButton">
            <ion-icon class="icon" name="notifications-outline"></ion-icon>
            <span class="badge badge-danger">4</span>
        </a>
    </div>
</div>
<?php else: ?>
<div class="appHeader">
    <div class="left">
        <a href="#" class="headerButton goBack">
            <ion-icon name="chevron-back-outline" role="img" class="md hydrated" aria-label="chevron back outline"></ion-icon>
        </a>
    </div>
    <div class="pageTitle"><?=SUBTITLE_PAGE?></div>
    <div class="right">

        <?php switch($button){
            case 'add':
                ?>
                <a href="javascript:void(0)" class="headerButton" onclick="actionForm('addUser')" data-bs-toggle="modal" data-bs-target="#actionSheetForm">
                    <ion-icon role="img" class="md hydrated" name="add-outline"></ion-icon>
                </a>
                <?php
                break;
        } ?>

    </div>
</div>
<?php endif; ?>
<!-- * App Header -->
