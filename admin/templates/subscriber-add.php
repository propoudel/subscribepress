<div class="wrap">
    <div class="form-wrap">
        <h1>Add Subscriber</h1>
        <form action="<?php echo esc_url(get_permalink()); ?>" method="post" name="add-subscriber-email-form">
            <?php wp_nonce_field(); ?>
            <div class="sp-input">
                <label for="sp-name">Name</label>
                <input type="text" id="sp-name" name="sp-name">
            </div>

            <div class="sp-input">
                <label for="sp-email">Email</label>
                <input type="text" id="sp-email" name="sp-email">
            </div>
            <div class="sp-input">
                <label for="sp-status">Status</label>
                Confirmed <input type="radio" name="sp-status" value="1">
                Unconfirmed  <input type="radio" value="0" name="sp-status">
            </div>

            <div class="sp-input">
                <input type="submit" value="Submit" class="button add-new-h2" name="submit">
            </div>
        </form>

    </div>

</div>