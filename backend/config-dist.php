<?php

// edit and rename to config.php

define( 'OWM_API_KEY', 		'enter your API key here' );
define( 'OWM_CITY_ID',		'enter the ID of the city you want to fetch the weather forecast for' );
define( 'OWM_UNITS',		'metric' ); // see https://openweathermap.org/current#data
define( 'TEMP_UNIT',		'°C' ); // if you change OWN_UNITS, you might want to edit this one too
define( 'FORECAST_ITEMS',	9 ); // how many forecast items to show. You might need to change the css if you add too many