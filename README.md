# Server Status Wordpress Plugin Query by xPaw

## Description
Get online status and players of Minecraft server comfortably in Wordpress widget.<br>
[xPaw](https://github.com/xPaw) made [Minecraft-query](https://github.com/xPaw/PHP-Minecraft-Query) which is getting data from MC server, give him fav ;).<br>
Plugin contains 2 widgets <br>

Minecraft Server - Single will show one server in your widget area with more detailed description and version of server.<br>
Minecraft Server - Multi will show a list of servers with just name and players online.

## Instructions
Before using this class, you need to make sure that your server is running GS4 status listener.

Look for those settings in **server.properties**:

> *enable-query=true*<br>
> *query.port=25565*

## Wordpress Installation
Go to your **~ wordpress/wp-content/plugins** and copy there folder **minecraft-query**.

Then activate it in Wordpress plugins and go to Server Settings and set your properties.

Last thing is going to Widgets, and Drag **Minecraft server - Multi** or **Minecraft server - Single** to your widget area and choose whitch server you want to show.


## RCON
Minecraft implements [Source RCON protocol](https://developer.valvesoftware.com/wiki/Source_RCON_Protocol), so I suggest using [PHP Source Query](https://github.com/xPaw/PHP-Source-Query-Class) library for your RCON needs.


## License
> *This work is licensed under a Creative Commons Attribution-NonCommercial-ShareAlike 3.0 Unported License.<br>
> To view a copy of this license, visit http://creativecommons.org/licenses/by-nc-sa/3.0/*
