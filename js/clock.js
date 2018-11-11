var forecastTpl;
var lastCoverArt;

document.observe( 'dom:loaded', function()
{
	var tpl = $( 'forecastTemplate' );
	
	forecastTpl = new Template( tpl.innerHTML );
	
	tpl.remove();
	
	updateClock();
	updateWeather();
	updateSubsonic();
} );

function updateClock()
{
	var date = new Date();

	$( 'date' ).update( date.toLocaleDateString( navigator.language, {
		weekday: 'long',
		day: 'numeric',
		month: 'long',
		year: 'numeric'
	} ));

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
							parseForecast( json.forecast, json.forecastItems );
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

function parseForecast( json, numItems)
{
	var len = json.list[ 0 ];
	var forcast = $( 'forecast' );
	
	forecast.innerHTML = '';

	for( var i = 0, len = Math.min( json.list.length, numItems ); i < len; i++ ) {
		var item = json.list[ i ];

		forecast.insert( forecastTpl.evaluate( {
			idx:		i,
			temp:		Math.round( item.main.temp ),
			humidity:	Math.round( item.main.humidity ),
			hour: 		fmtTimePart( new Date( item.dt * 1000 ).getHours() )
		} ));
		
		updateWeatherIcon( item.weather, $( 'forecast' + i ).down( '.icon' ));
	}
}

function updateSubsonic()
{
	new Ajax.Request( 'backend/subsonic.php',
					  {
						parameters: {
							lastCoverArtId: lastCoverArt
						},
						onSuccess: function( response )
						{
							var json = response.responseJSON;

							if( json.enabled ) {
								var forecast = $( 'forecast' );
								var mediaInfo = $( 'nowPlaying' );

								updateSubsonic.delay( 5 );

								if( json.title ) {

									if( json.coverArtId != lastCoverArt ) {
										var coverArt = $( 'coverArt' );

										lastCoverArt = json.coverArtId;

										coverArt.innerHTML = '';

										if( json.coverArt )
											coverArt.insert( new Element( 'img', { src: json.coverArt } ));
									}

									$( 'title' ).update( json.title );
									$( 'artist' ).update( json.artist );
									$( 'album' ).update( json.album );

									mediaInfo.show();
									forecast.hide();

								} else {
									forecast.show();
									mediaInfo.hide();
								}
							}
						},
					  	onFailure: function()
					  	{
							updateSubsonic.delay( 30 ); 
					  	}
				   	} );
}
