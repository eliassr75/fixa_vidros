

    </div>
    <!-- * App Capsule -->

    <div id="toast-container"></div>

    <?php
        if(isset($login) && $login === false){
            require_once 'additionalContent.php';
        }
    ?>

    <?php require_once 'htmlScripts.php'; ?>

</body>