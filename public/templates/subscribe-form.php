
    <form method="post" role="form" action="<?php echo esc_url(get_permalink()); ?>" id="sp-subscribe-form">
        <?php wp_nonce_field(); ?>

        <div id="ajaxResponse" class="sp-alert alert alert-info" style="display: none"></div>
        <div class="form-group sp-input">
            <label for="sp-name"><strong>Name</strong></label>
            <input type="text" class="form-control" name="sp-name" id="sp-name" placeholder="Name">
        </div>
        <div class="form-group sp-input">
            <label for="sp-email"><strong>Email *</strong></label>
            <input type="text" class="form-control" name="sp-email" id="sp-email" placeholder="Email">
        </div>

        <p class="sp-input">
            <button type="submit" id="submit-sp-subscribe" class="btn btn-primary">Subscribe!</button>
        </p>
    </form>



