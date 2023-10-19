<?php

/** @var yii\web\View $this */

$this->title = 'My Yii Application';
?>

<div class="wp-calendar container-fluid">
    <div class="header-calendar">
        <div class="row g-0">
            <div class="col-auto" style="border: 1px solid #000; width: 180px" aria-label="10">
                10:00
            </div>
            <div class="col-auto" style="border: 1px solid #000; width: 180px" aria-label="11">
                11:00
            </div>
            <div class="col-auto" style="border: 1px solid #000; width: 180px" aria-label="12">
                12:00
            </div>
            <div class="col-auto" style="border: 1px solid #000; width: 180px" aria-label="13">
                13:00
            </div>
            <div class="col-auto" style="border: 1px solid #000; width: 180px" aria-label="13">
                14:00
            </div>
            <div class="col-auto" style="border: 1px solid #000; width: 180px" aria-label="13">
                15:00
            </div>
        </div>
    </div>
    <div class="body-calendar">
        <div class="row g-0">
            <div class="col-auto" style="border: 1px solid #000; width: 180px">
                <div class="selector resizable" style="background: #cecece; border: 1px solid #000; width: 150px;">
                    test 1
                </div>
            </div>
            <div class="col-auto" style="border: 1px solid #000; width: 180px">
                2
            </div>
            <div class="col-auto" style="border: 1px solid #000; width: 180px">
                <div class="selector resizable" style="background: #cecece; border: 1px solid #000; width: 150px;">
                    test 2
                </div>
            </div>
            <div class="col-auto" style="border: 1px solid #000; width: 180px">
                4
            </div>
            <div class="col-auto" style="border: 1px solid #000; width: 180px">
                5
            </div>
            <div class="col-auto" style="border: 1px solid #000; width: 180px">
                6
            </div>
        </div>
        <div class="row g-0">
            <div class="col-auto" style="border: 1px solid #000; width: 180px">
                <div class="selector resizable" style="background: #cecece; border: 1px solid #000; width: 150px;">
                    test 3
                </div>
            </div>
            <div class="col-auto" style="border: 1px solid #000; width: 180px">
                2
            </div>
            <div class="col-auto" style="border: 1px solid #000; width: 180px">
                <div class="selector resizable" style="background: #cecece; border: 1px solid #000; width: 150px;">
                    test 4
                </div>
            </div>
            <div class="col-auto" style="border: 1px solid #000; width: 180px">
                4
            </div>
            <div class="col-auto" style="border: 1px solid #000; width: 180px">
                5
            </div>
            <div class="col-auto" style="border: 1px solid #000; width: 180px">
                6
            </div>
        </div>
    </div>
</div>

<div class="modal modal-draggable" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Modal title</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p>Modal body text goes here.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary">Save changes</button>
            </div>
        </div>
    </div>
</div>

<style>
    .body-calendar .row > div{
        position: relative;
    }

    .body-calendar .row{
        height: 50px;
    }

    .resizable{
        position: absolute;
        left: 0;
        top: 0;
        height: 50px;
        z-index: 50;
    }
</style>
