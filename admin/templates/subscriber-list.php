<div class="wrap">

    <h1>Subscribers <a class="page-title-action" href="?page=<?php echo esc_attr($_REQUEST['page']); ?>&action=add">Add New</a></h1>
    <?php  $subscriber->views(); ?>
    <form id="filter" method="post">
        <input type="hidden" name="page" value="<?php echo $_REQUEST['page'] ?>"/>
        <?php $subscriber->search_box('search', 'id'); ?>
        <?php $subscriber->display() ?>
    </form>
</div>
