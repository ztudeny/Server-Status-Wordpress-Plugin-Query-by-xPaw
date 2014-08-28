<?php 

	echo $before_widget;

if ( $display_title == 1){

	echo $before_title . $title . $after_title;	
}

?>
	<?php if ( $display_title == 1){ //If display title is off, set Online status as H4; ?>

		<?php if( !$server_stats['error'] ){ ?>
			<p class="mcq-solo-online">Online</p>
		<?php }else{ // server offline ?>
			<p class="mcq-solo-offline">Offline</p>
		<?php } ?>

	<?php }else{ ?>

		<?php if( !$server_stats['error'] ){ ?>		
			<h4 class="mcq-solo-online">Online</h4>
		<?php }else{ // server offline ?>
			<h4 class="mcq-solo-offline">Offline</h4>
		<?php } ?>

	<?php } ?>
	<?php if( !$server_stats['error'] ){ ?>		
		<p class="mcqplayers-solo"><strong><?php echo $server_stats['players']['online']; ?></strong>/<?php echo $server_stats['players']['max']; ?> <span>online</span></p>
		<p class="mcqip-solo"><?php echo $server_info['server_ip']; ?></p>
		<p class="mcqversion-solo">Version of server: <strong><?php echo $server_stats['version']['name']; ?></strong></p>
		<p class="mcqsolo-shortdescription" >
			<?php echo $server_info['short_description']; ?>
		</p>
	<?php }else{ // server offline ?>
		<p class="mcqplayers-solo">Generating creepers</p>
		<p class="mcqip-solo"><?php echo $server_info['server_ip']; ?></p>
		<p class="mcqversion-solo">We will be back soon</p>
		<p class="mcqsolo-shortdescription" >
			<?php echo $server_info['short_description']; ?>
		</p>
	<?php } ?>

<?php
	echo $after_widget;

?>