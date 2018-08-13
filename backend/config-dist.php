<?php

// edit and rename to config.php

define( 'OWM_API_KEY', 		'enter your API key here' );
define( 'OWM_CITY_ID',		'enter the ID of the city you want to fetch the weather forecast for' );
define( 'OWM_UNITS',		'metric' ); // see https://openweathermap.org/current#data
define( 'TEMP_UNIT',		'°C' ); // if you change OWN_UNITS, you might want to edit this one too
define( 'FORECAST_ITEMS',	9 ); // how many forecast items to show. You might need to change the css if you add too many

/* 
	Uncomment this section if you wish to monitor what's playing on a Subsonic server

define( 'SUBSONIC_SERVER',				'https://www.example.org/subsonic/' ); // NOTE: it should end with a /
define( 'SUBSONIC_USER',				'username' );
define( 'SUBSONIC_PASSWORD',			'password' );
define( 'SUBSONIC_USE_TOKEN_AUTH',		true ); // set it to true only if the server supports token auth (v5.3+)
define( 'SUBSONIC_PLAYER',				'Soundwaves' ); // the name of the player to use to connect to the server

// an ordered array of players to monitor: the track playing on the first one of these will be shown in the web page
// add a '*' entry to display what's playing on any player
// In this example, first look for Soundwaves (https://www.tellini.org/products/ios/soundwaves/). If it's not playing anything,
// fallback to any other player
define( 'SUBSONIC_MONITORED_PLAYERS',	array( 'Soundwaves', '*' ));

*/