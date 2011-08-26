<?php if (!empty($_SESSION['user'])) { ?>

      <form action="<?php echo $this->link_to(NULL, 'items', 'add'); ?>" method="post" enctype="multipart/form-data">
        <fieldset>
          <legend>New <?php echo $this->config->items['name']; ?></legend>
<?php if ($this->config->items['titles']['enabled'] == TRUE) { ?>
            <div class="clearfix">
              <label for="title"><?php echo $this->config->items['titles']['name']; ?></label>
              <div class="input">
                <input class="medium" name="title" size="50" type="text" value="<?php if (isset($_GET['title'])) { echo $_GET['title']; } ?>" />
              </div>
            </div> <!-- /clearfix -->
<?php } ?>
<?php if ($this->config->items['content']['enabled'] == TRUE) { ?>
            <div class="clearfix">
              <label for="content"><?php echo $this->config->items['content']['name']; ?></label>
              <div class="input">
                <textarea name="content" rows="5" cols="50"><?php if (isset($_GET['content'])) { echo $_GET['content']; } ?></textarea>
              </div>
            </div> <!-- /clearfix -->
<?php } ?>
<?php if ($this->config->items['uploads']['enabled'] == TRUE) { ?>
            <div class="clearfix">
              <label for="file"><?php echo $this->config->items['uploads']['name']; ?></label>
              <div class="input">
                <input type="file" name="file" id="file" />
              </div>
            </div> <!-- /clearfix -->
<?php } ?>
          <div class="actions">
            <button type="submit" class="btn">Submit</button>
          </div>
        </fieldset>
      </form>

<?php } ?>