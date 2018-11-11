<?php
require_once( __DIR__ . '/config.php' );

header( 'Content-Type: application/json' );

function getUrl( $func, $params = null )
{
	if( !$params )
		$params = array();

	$params[ 'f' ] = 'json';
	$params[ 'u' ] = SUBSONIC_USER;
	$params[ 'c' ] = SUBSONIC_PLAYER;

	if( SUBSONIC_USE_TOKEN_AUTH ) {

		$salt = bin2hex( random_bytes( 10 ));

		$params[ 'v' ] = '1.13.0';
		$params[ 't' ] = md5( SUBSONIC_PASSWORD . $salt );
		$params[ 's' ] = $salt;

	} else {
		
		$params[ 'v' ] = '1.9.0';
		$params[ 'p' ] = SUBSONIC_PASSWORD;
	}
		
	foreach( $params as $key => $val )
		$params[ $key ] = $key . '=' . urlencode( $val );

	$params = implode( '&', $params );

	return SUBSONIC_SERVER . '/rest/' . $func . '?' . $params;
}

function initCurl( $func, $params = null )
{
	$ch = curl_init();
	
	curl_setopt( $ch, CURLOPT_URL,              getUrl( $func, $params ));
	curl_setopt( $ch, CURLOPT_FOLLOWLOCATION,   true );
	curl_setopt( $ch, CURLOPT_RETURNTRANSFER,   true );
	curl_setopt( $ch, CURLOPT_FORBID_REUSE,		true );

	return $ch;
}

function sendRequest( $func, $params = null )
{
	$ch = initCurl( $func, $params );

	$response = curl_exec( $ch );

	curl_close( $ch );

	return json_decode( $response );
}

function getImage( $func, $params )
{
	$ch = initCurl( $func, $params );
	$hdr = array();
	
	curl_setopt( $ch, CURLOPT_HEADERFUNCTION,
				 function( $ch, $str ) use ( &$hdr )
				 {
					$hdr[] = $str;

					return strlen( $str );
				 } );

	$data = curl_exec( $ch );

	curl_close( $ch );

	foreach( $hdr as $i => $line )
        if( $i > 0 ) {

            list( $key, $value ) = explode( ':', $line );

			if( !strcasecmp( $key, 'Content-Type' ))
           		$contentType = trim( $value ); 
        }

	$response = 'data:' . $contentType . ';base64,';

	return $response . base64_encode( $data );
}
	
$data = new stdClass();

$data->enabled = defined( 'SUBSONIC_SERVER' );

if( $data->enabled ) {

	$nowPlaying = sendRequest( 'getNowPlaying' );
	$field      = 'subsonic-response';
	$nowPlaying = $nowPlaying->$field;

	if( $nowPlaying->status === 'ok' ) {

		$entries = $nowPlaying->nowPlaying->entry;

		if( is_array( $entries ))
			foreach( SUBSONIC_MONITORED_PLAYERS as $player ) {

				$player = strtolower( $player );

				foreach( $entries as $entry ) {

					$found = (( $player === '*' ) || ( strtolower( $entry->playerName ) === $player )) &&
							 ( $entry->duration > $entry->minutesAgo * 60 );

					if( $found ) {

						$data->title = $entry->title;
						$data->album = $entry->album;
						$data->artist = $entry->artist;
						$data->coverArtId = $entry->coverArt;

						if( !empty( $entry->coverArt ) && ( $data->coverArtId != $_REQUEST[ 'lastCoverArtId' ] ))
							$data->coverArt = getImage( 'getCoverArt', array( 'id' => $entry->coverArt ));

						break;
					}
				}

				if( $found )
					break;
			}
	}
}

echo json_encode( $data );