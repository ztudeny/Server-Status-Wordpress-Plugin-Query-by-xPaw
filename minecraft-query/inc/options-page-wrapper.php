<div class="wrap" id="creepy-server-settings">
	
	<div id="icon-options-general" class="icon32"></div>
	<h2>Servers settings</h2>
	
	<div id="poststuff">
	
		<div id="post-body" class="metabox-holder columns-2">
		
			<!-- main content -->
			<div id="post-body-content">
				
				<div class="meta-box-sortables ui-sortable">
					
					<div class="postbox">
					
						<h3><span>Add your servers</span></h3>
						<div class="inside">
							<p>Before using this class, you need to make sure that your server is running GS4 status listener.<br/>
								Look for those settings in server.properties:</p>
							<code>
								enable-query=true ; 
								query.port=25565
							</code>
							<p> Most hostings have it set that by default but if it's not working you can check <a href="http://magicraft.creepy.cz/?post_type=knowledge_base&p=95" target="_blank">tutorial</a> and set it yourself.</p>
							<p> Don't forget to give fav to <a href="https://github.com/xPaw" target="_blank" alt="xPaw Github">xPaw</a> for a <a href="https://github.com/xPaw/PHP-Minecraft-Query" target="_blank" alt="Minecraft query">script</a> that we are running.</p>

							<form name="creepymc_form" method="post" action="">
							<table id="creepymc_servers"  class="widefat">
									<thead>
										<tr>
											<th class="row-title">Minecraft Servers</th>
											<th></th>
										</tr>
									</thead>

								<?php if ( !isset( $server_stats ) || $server_stats == '' || $server_stats[0] == '') { ?>

									<tr>
										<td><label>Server Name</label><input name="server_name[]" type="text" value="" class="all-options" /></td>
										<td><label>Server IP</label><input name="server_ip[]" type="text" value="" class="all-options" /></td>
									</tr>
									<tr>
										<td><label>Short description</label><input name="short_description[]" type="text" value="Short description of server" class="large-text" /></td>
									</tr>

								<?php }else{ ?>

									<?php
									foreach ($server_info as $server) { ?>
									<tr>
										<td><label for="tablecell">Server Name</label><input name="server_name[]" type="text" value="<?php echo $server['server_name'];?>" class="all-options" /></td>
										<td><label for="tablecell">Server IP</label><input name="server_ip[]" type="text" value="<?php echo $server['server_ip']; ?>" class="all-options" /></td>
									</tr>
									<tr>
										<td><label>Short description</label><input name="short_description[]" type="text" value="<?php echo  $server['short_description'];;?>" class="large-text" /></td>
										<?php if ($count > 0) {?>
											<td><input type="button" value="Remove" class="creepymc_remove_server button-secondary"></td>
										<?php } else { $count++; } ?>
									</tr>
									<?php }

								} ?>

								<?php /*?>
									<p id="oldRow<?=$product['id']?>">Item quantity: <input type="text" name="qty<?=$product['id']?>" size="4" value="<?=$product['qty']?>" /> Item name: <input type="text" name="name<?=$product['id']?>" value="<?=$product['name']?>" /> <input type="checkbox" name="delete_ids[]" value="<?=$product['id']?>"> Mark to delete</p>
								<?php endforeach; */ ?>

							</table>

							<input class="button-primary" type="submit" name="save" value="save" />

							<input class="button-secondary creepymc_add_server" type="button" value="Add Server" />
	

							</form>

						</div> <!-- .inside -->
					
					</div> <!-- .postbox -->

					<?php if( $debug ){ ?>
					<div class="postbox">
					
						<h3><span>Debug testing</span></h3>
						<div class="inside">

						<code>

							<?php 
							echo 'info';
							var_dump($server_info);

							echo "list";
								var_dump($server_stats);
								echo count($server_stats);
								echo "stats";
								//var_dump($server_stats);

							foreach ($server_info as $server) {

								$server = mcq_get_server_stats( $server['server_ip']);
								var_dump($server);
								# code...
							}


							?>
						</code>


						</div> <!-- .inside -->
					
					</div> <!-- .postbox -->

					<?php } //end debug ?>
					
				</div> <!-- .meta-box-sortables .ui-sortable -->
				
			</div> <!-- post-body-content -->
			
			<!-- sidebar -->
			<div id="postbox-container-1" class="postbox-container">
				
				<div class="meta-box-sortables">
					

					<?php 
					if (count($server_stats) > 1){ ?>

					<div class="server-list">

						<?php
						$onlinePlayers = 0;
						$totalPlayers = 0;
						$offline = 1;

						foreach ($server_stats as $server) {

							if ( !$server['error']) {

								$onlinePlayers += $server['players']['online'];
								$totalPlayers += $server['players']['max'];
								// Atleast one server online
								if ($offline) {
										$offline = 0;
								}	
							}
						}
						// any server online
						if($offline){ ?>
							<h3 class="mcserver-offline">Currently offline</h3>
						<?php
						}else{ ?>
							<h3 class="mcserver-online">Currently Online <span class="mcplayers-header"><?php echo $onlinePlayers.'/'.$totalPlayers; ?></span></h3>
						<?php 
						}	?>

							<div class="inside">
								<ul>
								<?php $count = 0;
								foreach ($server_stats as $server) {
									?>
									<li>
										<h4><?php echo $server_info[$count]['server_name']; ?></h4>
										<?php if ( $server['error'] ){
										?>
											<p class="mcserver-small-offline">
												Offline
											</p>
										<?php
										}else{ ?>
											<p class="mcserver-small-online">Players online 
												<span class="players"><strong><?php echo $server['players']['online']; ?></strong>/<?php echo $server['players']['max']; ?></span>
											</p>
										<?php 
										} ?>
									</li>

								<?php $count++;
								} ?>

								</ul>
							</div> <!-- .inside -->

					</div> <!-- .server-list -->

						<?php
					}else{

					 ?>

					<div class="single-server">

						<h3 class="<?php echo $server_stats[0]['error'] ? 'mcserver-solo-offline' : 'mcserver-solo-online' ;?>"><?php echo $server_stats[0]['error'] ? 'Offline' : 'Online' ;?></h3>
						<div class="inside">

							<p class="mcplayers-solo"><strong><?php echo $server_stats[0]['players']['online']; ?></strong>/<?php echo $server_stats[0]['players']['max']; ?> <span>online</span></p>
							<p class="mcip-solo"><?php echo $server_info[0]['server_ip']; ?></p>
							<p class="mcversion-solo">Version of server: <strong><?php echo $server_stats[0]['version']['name']; ?></strong></p>
							<p class="mcdesc-solo"><?php echo $server_info[0]['short_description']; ?></p>
						</div> <!-- .inside -->

					</div> <!-- .single-server -->



					<?php
					}	?>
						
					
				</div> <!-- .meta-box-sortables -->
				
			</div> <!-- #postbox-container-1 .postbox-container -->
			
		</div> <!-- #post-body .metabox-holder .columns-2 -->
		
		<br class="clear">
	</div> <!-- #poststuff -->
	
</div> <!-- .wrap -->
