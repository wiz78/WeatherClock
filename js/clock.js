var forecastTpl;

document.observe( 'dom:loaded', function()
{
	var tpl = $( 'forecastTemplate' );
	
	forecastTpl = new Template( tpl.innerHTML );
	
	tpl.remove();
	
	updateClock();
	updateWeather();
} );

function updateClock()
{
	var date = new Date();
	
	$( 'hour' ).update( fmtTimePart( date.getHours() ));
	$( 'minute' ).update( fmtTimePart( date.getMinutes() ));
	
	updateClock.delay( 1 );
}

function fmtTimePart( part )
{
	return ( '0' + part ).slice( -2 );
}

function updateWeather()
{
	new Ajax.Request( 'backend/weather.php',
					  {
	 				  	onSuccess: function( response )
						{
							var json = response.responseJSON;
							var tempUnit = json.tempUnit;
							
							parseWeather( json.current, tempUnit );
							parseForecast( json.forecast, json.forecastItems, tempUnit );
						},
						onComplete: updateWeather.delay( 60 * 30 )
					  } );
}

function updateWeatherIcon( weather, target )
{
	new Ajax.Request( 'img/' + weather[ 0 ].icon + '.svg',
					  {
	 				  	onSuccess: function( response )
						{
							$( target ).update( response.responseText );
						}
					  } );
}

function parseWeather( json, tempUnit )
{
	updateWeatherIcon( json.weather, 'weatherIcon' );
											
	$( 'temp' ).update( Math.round( json.main.temp ) + ' ' + tempUnit );
	$( 'humidity' ).update( Math.round( json.main.humidity ) + '%' );
}

function parseForecast( json, numItems, tempUnit )
{
	var len = json.list[ 0 ];
	var forcast = $( 'forecast' );
	
	forecast.innerHTML = '';

	for( var i = 0, len = Math.min( json.list.length, numItems ); i < len; i++ ) {
		var item = json.list[ i ];

		forecast.insert( forecastTpl.evaluate( {
			idx:	i,
			temp:	Math.round( item.main.temp ) + ' ' + tempUnit,
			hour: 	fmtTimePart( new Date( item.dt * 1000 ).getHours() )
		} ));
		
		updateWeatherIcon( item.weather, $( 'forecast' + i ).down( '.icon' ));
	}
}