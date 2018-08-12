<?php
	require_once( __DIR__ . '/config.php' );

	header( 'Content-Type: application/json' );

	$current  = file_get_contents( 'https://api.openweathermap.org/data/2.5/weather?id=' . OWM_CITY_ID . '&appid=' . OWM_API_KEY . '&units=' . OWM_UNITS );
	$forecast = file_get_contents( 'https://api.openweathermap.org/data/2.5/forecast?id=' . OWM_CITY_ID . '&appid=' . OWM_API_KEY . '&units=' . OWM_UNITS );

	$data = new stdClass();
	
	$data->current = json_decode( $current );
	$data->forecast = json_decode( $forecast );
	$data->tempUnit = TEMP_UNIT;
	$data->forecastItems = FORECAST_ITEMS;

	echo json_encode( $data );