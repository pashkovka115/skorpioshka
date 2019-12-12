<article class="content item-editor-page">
    <div class="title-block">
        <h3 class="title"><?= $pageName ?? '' ?><span class="sparkline bar" data-type="bar"></span>
        </h3>
    </div>
    <form name="item" method="post">

        <?php foreach ($templates as $template):
            echo $this->templates[$template];
        endforeach; ?>

        <div class="form-group row">
            <div class="col-sm-10 col-sm-offset-2">
                <button type="submit" class="btn btn-primary"><?= $submit ?? 'Отправить' ?></button>
            </div>
        </div>
    </form>
</article>