<div class="pagination">
  <ul>
<?php if ($this->uri['controller'] == 'items') { ?>
    <li class="prev"><a href="<?php echo $this->url_for($this->uri['controller'], $this->uri['action']); ?>/?page=<?php echo $this->uri['params']['page']-1; ?>">&larr; Previous</a></li>
    <li class="next"><a href="<?php echo $this->url_for($this->uri['controller'], $this->uri['action']); ?>/?page=<?php echo $this->uri['params']['page']+1; ?>">Next &rarr;</a></li>
<?php } else { ?>
    <li class="prev"><a href="<?php echo $this->url_for($this->uri['controller'], $this->uri['action'], $this->uri['params']['id']); ?>/?page=<?php echo $this->uri['params']['page']-1; ?>">&larr; Previous</a></li>
    <li class="next"><a href="<?php echo $this->url_for($this->uri['controller'], $this->uri['action'], $this->uri['params']['id']); ?>/?page=<?php echo $this->uri['params']['page']+1; ?>">Next &rarr;</a></li>
<?php } ?>
  </ul>
</div>