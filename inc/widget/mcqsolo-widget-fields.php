<p>
  <label>
  	Title
	<input class="widefat" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo $title; ?>" />
  </label>  
</p>
<p>
  <label>
	<input type="checkbox" name="<?php echo $this->get_field_name('display_title'); ?>" value="1" <?php checked( $display_title, 1 ); ?> />
  	Display widget Title?
  </label> 
</p>
<p>
<label>Select server</label>
<select name="<?php echo $this->get_field_name('server'); ?>" >
	<?php
		$count = 0;
		foreach ($server_info as $server) {
			?>
			<option <?php echo $chosen_server == $count ? 'selected' : ''; ?> value="<?php echo $count; ?>"><?php echo $server['server_name']; ?></option>
		<?php $count++;
		} 
	?>
</select>
</p>