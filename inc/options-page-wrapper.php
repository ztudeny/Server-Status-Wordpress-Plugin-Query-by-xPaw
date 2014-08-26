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
							<p>You need to have set these ports for online status to work.</p>
							<code>
								enable-query=true ; 
								query.port=25565
							</code>
							<p> Most hostings have it set that by default but if it's not working you can check <a href="http://magicraft.creepy.cz/?post_type=knowledge_base&p=95" target="_blank">tutorial</a> and set it yourself.</p>

							<form name="creepymc_form" method="post" action="">
							<table id="creepymc_servers"  class="widefat">
									<thead>
										<tr>
											<th class="row-title">Minecraft Servers</th>
											<th></th>
										</tr>
									</thead>

								<?php if ( !isset( $server_list ) || $server_list == '' || $server_list[0] == '') { ?>

									<tr>
										<td><label>Server Name</label><input name="server_name[]" type="text" value="" class="all-options" /></td>
										<td><label>Server IP</label><input name="server_ip[]" type="text" value="" class="all-options" /></td>
									</tr>
									<tr>
										<td><label>Short description</label><input type="text" value="large-text" class="large-text" /></td>
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
					
						<h3><span>code test</span></h3>
						<div class="inside">

						<code>


						<?php

						$server_adress = 'friendlyuk.gamename.net:25798';

						echo "<br/>Test 1 <br/>";

					   	require(get_stylesheet_directory() . '/library/server-status/MinecraftQuery.class.php');

					    $Query = new MinecraftQuery( );

					    try
					    {
					        $Query->Connect( $server_adress, 25565 );

					        print_r( $Query->GetInfo( ) );
					        print_r( $Query->GetPlayers( ) );
					    }
					    catch( MinecraftQueryException $e )
					    {
					        echo $e->getMessage( );
					    }
					?>

						<?php
						echo "Test 2 <br/>";
	// Edit this ->
	define( 'MQ_SERVER_ADDR', $server_adress );
	define( 'MQ_SERVER_PORT', 25565 );
	define( 'MQ_TIMEOUT', 1 );
	// Edit this <-
	
	// Display everything in browser, because some people can't look in logs for errors
	Error_Reporting( E_ALL | E_STRICT );
	Ini_Set( 'display_errors', true );
	
							// Require the class
	require(get_stylesheet_directory() . '/library/server-status/MinecraftServerPing.php');
	
	$Timer = MicroTime( true );
	
	$Info = false;
	$Query = null;
	
	try
	{
		$Query = new MinecraftPing( MQ_SERVER_ADDR, MQ_SERVER_PORT, MQ_TIMEOUT );
		
		$Info = $Query->Query( );
		
		if( $Info === false )
		{
			/*
			 * If this server is older than 1.7, we can try querying it again using older protocol
			 * This function returns data in a different format, you will have to manually map
			 * things yourself if you want to match 1.7's output
			 *
			 * If you know for sure that this server is using an older version,
			 * you then can directly call QueryOldPre17 and avoid Query() and then reconnection part
			 */
			
			$Query->Close( );
			$Query->Connect( );
			
			$Info = $Query->QueryOldPre17( );
		}
	}
	catch( MinecraftPingException $e )
	{
		$Exception = $e;
	}
	
	if( $Query !== null )
	{
		$Query->Close( );
	}
	
	$Timer = Number_Format( MicroTime( true ) - $Timer, 4, '.', '' );
?>



<?php if( isset( $Exception ) ): ?>
		<div class="panel panel-primary">
			<div class="panel-heading"><?php echo htmlspecialchars( $Exception->getMessage( ) ); ?></div>
			<p><?php echo nl2br( $e->getTraceAsString(), false ); ?></p>
		</div>
<?php else: ?>
		<div class="row">
				<table class="table table-bordered table-striped">
					<thead>
						<tr>
							<th colspan="2">Server Info <em>(queried in <?php echo $Timer; ?>s)</em></th>
						</tr>
					</thead>
					<tbody>
<?php if( $Info !== false ): ?>
<?php foreach( $Info as $InfoKey => $InfoValue ): ?>
						<tr>
							<td><?php echo htmlspecialchars( $InfoKey ); ?></td>
							<td><?php
	if( $InfoKey === 'favicon' )
	{
		echo '<img width="64" height="64" src="' . Str_Replace( "\n", "", $InfoValue ) . '">';
	}else if( Is_Array( $InfoValue ) )
	{
		echo "<pre>";
		print_r( $InfoValue );
		echo "</pre>";
	}
	else
	{
		echo htmlspecialchars( $InfoValue );
	}
?></td>
						</tr>
<?php endforeach; ?>
<?php else: ?>
						<tr>
							<td colspan="2">No information received</td>
						</tr>
<?php endif; ?>
					</tbody>
				</table>
		</div>
<?php endif; ?>


							<?php 
							echo 'info';
							var_dump($server_info);

							echo "list";
								var_dump($server_list);
								echo count($server_list);
								echo "stats";
								//var_dump($server_stats);

							foreach ($server_info as $server) {

								$server = mccreepy_get_server_stats( $server['server_ip']);
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
					if (count($server_list) > 1){ ?>

					<div class="server-list">

						<?php
						$onlinePlayers = 0;
						$totalPlayers = 0;
						$online = 0;

						foreach ($server_list as $server) {
							$onlinePlayers += $server['numplayers'];
							$totalPlayers += $server['maxplayers'];	

							if( $online == 0 && $server['online']) {
								$online = 1;
							}
						}
						// any server online
						if($online == 0){ ?>
							<h3 class="mcserver-offline">Currently offline</h3>
						<?php
						}else{ ?>
							<h3 class="mcserver-online">Currently Online <span class="mcplayers-header"><?php echo $onlinePlayers.'/'.$totalPlayers; ?></span></h3>
						<?php 
						}	?>

							<div class="inside">
								<ul>
								<?php $count = 0;
								foreach ($server_list as $server) {
									?>
									<li>
										<h4><?php echo $server_info[$count]['server_name']; ?></h4>
										<?php if ( $server['online'] && $server['maxplayers'] > 0 ){
										?>
											<p class="mcserver-small-online">Players online 
												<span class="players"><strong><?php echo $server['numplayers']; ?></strong>/<?php echo $server['maxplayers']; ?></span>
											</p>
										<?php
										}else{ ?>
											<p class="mcserver-small-offline">
												Offline
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

						<h3 class="<?php echo $server_list[0]['online'] ? 'mcserver-solo-online' : 'mcserver-solo-offline' ;?>"><?php echo $server_list[0]['online'] ? 'Online' : 'Offline' ;?></h3>
						<div class="inside">

							<p class="mcplayers-solo"><strong><?php echo $server_list[0]['numplayers']; ?></strong>/<?php echo $server_list[0]['maxplayers']; ?> <span>online</span></p>
							<p class="mcip-solo"><?php echo $server_info[0]['server_ip']; ?></p>
							<p class="mcversion-solo">Version of server: <strong><?php echo $server_list[0]['version']; ?></strong></p>
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
