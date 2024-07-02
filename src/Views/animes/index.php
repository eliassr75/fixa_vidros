<?php include_once __DIR__ . '/../htmlInit.php'; ?>
<?php require_once __DIR__ . '/../htmlHead.php'; ?>
<?php require_once __DIR__ . '/../bodyContentInit.php'; ?>

<br>
<!-- Start::row-1 -->
<div class="row d-flex justify-content-center">
    <?php if($filters){ ?>
    <div class="col-12">
        <div class="card custom-card">
            <div class="card-header">
                <h4 class="card-title"> Categories &amp; Fliters</h4>
            </div>
            <div class="card-body">
                <div class="row g-2">
                    <div class="col-12 form-check">
                        <input type="checkbox" data-bs-checkboxes="mygroup" class="form-check-input" id="checkbox-1" checked>
                        <label for="checkbox-1" class="form-check-label">Mens</label>
                    </div>
                    <div class="col-12 form-group mt-3">
                        <label class="form-label">Category</label>
                        <select class="form-control" data-trigger name="choices-single-default" id="choices-single-default1">
                            <option value="">--Select--</option>
                            <option value="Choice 1">Dress</option>
                            <option value="Choice 2">Bags &amp; Purses</option>
                            <option value="Choice 3">Coat &amp; Jacket</option>
                            <option value="Choice 4">Beauty</option>
                            <option value="Choice 5">Jeans</option>
                            <option value="Choice 6">Jewellery</option>
                            <option value="Choice 7">Electronics</option>
                            <option value="Choice 8">Sports</option>
                            <option value="Choice 9">Technology</option>
                            <option value="Choice 10">Watches</option>
                            <option value="Choice 11">Accessories</option>
                        </select>
                    </div>
                    <div class="col-12">
                        <a class="btn btn-success my-1" href="products.html">Apply Filter</a>
                        <a class="btn btn-info my-1" href="products.html">Search Now</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <?php } ?>

    <div class="col-11 row">
        <?php /*print_r($response_animes);*/ foreach ($response_animes->data as $anime) {

            $titles = new stdClass;
            foreach ($anime->titles as $title) {
                $property = strtolower($title->type);
                $titles->$property = $title->title;
            }
            ?>

        <div class="col-xxl-2 col-lg-3 col-md-4 col-sm-6 col-12">
            <div class="card custom-card overflow-hidden">
                <div class="row g-0">
                    <div class="col-md-12 position-relative">
                        <img src="<?=$anime->images->jpg->large_image_url?>" id="image<?=$anime->mal_id?>"
                             class="img-cover img-fluid rounded-start w-100" style="height: 40vh; object-fit: cover;">
                        <div class="offcanvas offcanvas-start position-absolute w-100 h-100" data-bs-scroll="true"
                             data-bs-backdrop="false" tabindex="-1" id="offcanvas<?=$anime->mal_id?>"
                             aria-labelledby="offcanvas<?=$anime->mal_id?>Label">
                            <div class="offcanvas-header border-bottom border-block-end-dashed">
                                <div class="fs-6">
                                    <img class="avatar me-2 avatar-rounded" src="https://cdn.myanimelist.net/images/favicon.ico">
                                    <?=$anime->mal_id?>
                                </div>
                                <button type="button" class="ms-auto btn-close"></button>
                            </div>
                            <div class="offcanvas-body">

                                <div class="mb-2">
                                    <div class="d-flex align-items-center justify-content-between">
                                        <div><span class="text-muted fs-13">Score</span></div>
                                        <div><span class="tbtn-success fs-12"><?=$anime->score?></span></div>
                                    </div>
                                    <div>
                                        <div class="progress progress-animate progress-xs mt-1" role="progressbar" aria-valuenow="<?=$anime->score*10?>" aria-valuemin="0" aria-valuemax="100">
                                            <div class="progress-bar bg-success-gradient" style="width: <?=$anime->score*10?>%"></div>
                                        </div>
                                    </div>
                                </div>

                                <div class="text-muted d-flex align-items-center justify-content-between my-2">
                                    <span class="tbtn-success">Rank</span>
                                    <span><?=$anime->rank?></span>
                                </div>

                                <div class="text-muted d-flex align-items-center justify-content-between my-2">
                                    <span class="tbtn-success">Episódios</span>
                                    <span><?=$anime->episodes?></span>
                                </div>

                                <hr>

                                <div class="d-flex flex-wrap align-items-center justify-content-between border-bottom my-2">
                                    <?php
                                    if($anime->season){

                                        $season = new stdClass;
                                        $season_anime = match ($anime->season) {
                                            'winter' => ["season" => "Inverno", "class" => "text-dark-gradient"],
                                            'spring' => ["season" => "Primavera", "class" => "text-warning"],
                                            'summer' => ["season" => "Verão", "class" => "text-warning-gradient"],
                                            'fall' => ["season" => "Outono", "class" => "text-light-gradient"],
                                        };
                                        $season->season = $season_anime['season'];
                                        $season->class = $season_anime['class'];

                                    ?>
                                        <div>
                                            <i class="ri-checkbox-blank-circle-fill <?=$season->class?> me-1 align-middle d-inline-block"></i>
                                            <?=$season->season?>
                                        </div>
                                    <?php } ?>
                                    <div>
                                        <i class="ri-calendar-2-fill me-1 align-middle d-inline-block"></i>
                                        <?=$anime->year?>
                                    </div>
                                    <div>
                                        <i class="ri-projector-2-fill me-1 align-middle d-inline-block"></i>
                                        <?=$anime->type?>
                                    </div>
                                </div>

                                <div class="d-flex flex-wrap align-items-center justify-content-between border-bottom my-2">
                                    <div>
                                        <i class="ri-information-fill me-1 align-middle d-inline-block"></i>
                                        <?=$anime->status?>
                                    </div>
                                    <div>
                                        <i class="ri-check-double-fill <?=$anime->approved ? 'tbtn-success' : 'text-danger'?> me-1 align-middle d-inline-block"></i>
                                    </div>
                                </div>

                                <div class="d-flex flex-wrap align-items-center justify-content-between border-bottom my-2">
                                    <div>
                                        <i class="ri-star-fill me-1 align-middle d-inline-block"></i>
                                        <?=$anime->popularity?>
                                    </div>
                                    <div>
                                        <i class="ri-user-follow-fill me-1 align-middle d-inline-block"></i>
                                        <?=$anime->members?>
                                    </div>
                                    <div>
                                        <i class="ri-heart-2-fill me-1 align-middle d-inline-block"></i>
                                        <?=$anime->favorites?>
                                    </div>
                                </div>

                            </div>
                            <div class="offcanvas-bottom text-center">
                                <a href="/animes/<?=$anime->mal_id?>" class="align-items-center text-success">
                                    <i class="ri-arrow-right-circle-line"></i> Ver todos os detalhes
                                </a>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-12">
                        <div class="card-header">
                            <div class="card-title w-100 text-truncate">
                                <span class=""><?=$titles->default ? $titles->default : "--" ?></span>
                                <p class="card-text">
                                    <small class="text-muted">
                                        <?=$titles->japanese ? $titles->japanese : ($titles->english ? $titles->english : "--") ?>
                                    </small>
                                </p>

                                <div class="w-auto d-flex justify-content-between">

                                    <button class="btn btn-lg btn-icon btn-success-gradient rounded-pill btn-wave">
                                        <i class="ri-heart-2-fill"></i>
                                    </button>
                                    <button class="btn btn-lg btn-icon btn-success-gradient rounded-pill btn-wave">
                                        <i class="ri-check-double-fill"></i>
                                    </button>
                                    <button class="btn btn-lg btn-icon btn-success-gradient rounded-pill btn-wave">
                                        <i class="ri-time-fill"></i>
                                    </button>
                                    <button class="btn btn-lg btn-icon btn-success-gradient rounded-pill btn-wave"
                                            data-bs-toggle="offcanvas" data-bs-target="#offcanvas<?=$anime->mal_id?>"
                                            aria-controls="offcanvas<?=$anime->mal_id?>">
                                        <i class="ri-information-fill"></i>
                                    </button>

                                </div>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </div>

        <?php } ?>

        <div class="mb-5">
            <div class="float-end">
                <ul class="pagination ">
                    <li class="page-item page-prev disabled">
                        <a class="page-link" href="javascript:void(0);" tabindex="-1">Prev</a>
                    </li>
                    <li class="page-item active"><a class="page-link" href="javascript:void(0);">1</a></li>
                    <li class="page-item"><a class="page-link" href="javascript:void(0);">2</a></li>
                    <li class="page-item"><a class="page-link" href="javascript:void(0);">3</a></li>
                    <li class="page-item"><a class="page-link" href="javascript:void(0);">4</a></li>
                    <li class="page-item"><a class="page-link" href="javascript:void(0);">5</a></li>
                    <li class="page-item page-next">
                        <a class="page-link" href="javascript:void(0);">Next</a>
                    </li>
                </ul>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../bodyContentEnd.php'; ?>
<?php require_once __DIR__ . '/../htmlEnd.php';?>

<script>

</script>

