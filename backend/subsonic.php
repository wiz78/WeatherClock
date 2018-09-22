<?php
require_once( __DIR__ . '/config.php' );

header( 'Content-Type: application/json' );

function getUrl( $func )
{
	$ret = SUBSONIC_SERVER . 'rest/' . $func . '?f=json&u=' . urlencode( SUBSONIC_USER );

	if( SUBSONIC_USE_TOKEN_AUTH ) {

		$salt = bin2hex( random_bytes( 10 ));

		$ret .=	'&v=1.13.0&t=' . md5( SUBSONIC_PASSWORD . $salt ) . '&s=' . $salt;

	} else
		$ret .=	'&v=1.9.0&p=' . urlencode( SUBSONIC_PASSWORD );

	return $ret . '&c=' . urlencode( SUBSONIC_PLAYER );
}

$data = new stdClass();

$data->enabled = defined( 'SUBSONIC_SERVER' );

if( $data->enabled ) {

	$nowPlaying = json_decode( file_get_contents( getUrl( 'getNowPlaying' )));
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

						if( !empty( $entry->coverArt ))
							$data->coverArt = getUrl( 'getCoverArt' ) . '&id=' . urlencode( $entry->coverArt );

						break;
					}
				}

				if( $found )
					break;
			}
	}
}

echo json_encode( $data );