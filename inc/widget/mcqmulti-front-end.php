<?php 

	echo $before_widget;

if ( $display_title == 1){

	echo $before_title . $title . $after_title;	
}

	$total_players = 0;
	$online_players = 0;
	$is_online = 0;
	foreach ($chosen_servers as $server) {	

		if ( !$server_stats[$server]['error'] ){

			$online_players += $server_stats[$server]['players']['online'];
			$total_players += $server_stats[$server]['players']['max'];
			if ( $is_online == 0 ){
				$is_online = 1;
			}

		}
	}

?>
	<?php if ( $display_title == 1){ //If display title is off, set Online status as H4; ?>
		<p class="<?php echo $is_online == 1 ? 'mcq-multi-online' : 'mcq-multi-offline' ;?>">
		<?php if( $is_online == 1 ){
			echo 'Online players <span class="mcq-players"><strong>'.$online_players.'</strong>/'.$total_players.'</span>';
		}else{
			echo 'Servers are offline';
		}
		?>
	</p>
	<?php }else{ ?>

		<h4 class="<?php echo $is_online == 1 ? 'mcq-multi-online' : 'mcq-multi-offline' ;?>">
		<?php if( $is_online == 1 ){
			echo 'Online players <span class="mcq-players"><strong>'.$online_players.'</strong>/'.$total_players.'</span>';
		}else{
			echo 'Servers are offline';
		}
		?>
		</h4>
	<?php } ?>

<ul class="mcq-server-list">
<?php foreach ($chosen_servers as $server) { ?>
	<li>
		<h5>
			<?php echo $server_info[$server]['server_name']; ?>
		</h5>

		<?php 
		if ( !$server_stats[$server]['error'] )
		{ ?>
			<p class="mcqplayers-multi"><strong><?php echo $server_stats[$server]['players']['online']; ?></strong>/<?php echo $server_stats[$server]['players']['max']; ?>
				<span class="mcq-online">
				</span>
			</p>		
			<p class="mcqip-multi"><?php echo $server_info[$server]['server_ip']; ?></p>

		<?php }else{ //Server offline ?>
			<p class="mcqplayers-multi">Offline
				<span class="mcqq-offline">
				</span>
			</p>		
			<p class="mcqip-multi"><?php echo $server_info[$server]['server_ip']; ?></p>
			
		<?php 
		} // end else-Server offline ?>
	</li>
	
<?php } ?>
</ul>

<?php
	echo $after_widget;

?>