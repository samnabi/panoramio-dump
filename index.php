<?php

/***** OPTIONS *****/

$set = 4494359;				// Panoramio user ID, or 'public' for all photos
$qty = 10;					// Number of photos to fetch
$spaces_in_filename = true;	// Save filenames as "Photo Title.jpg" (true) or "photo-title.jpg" (false)

/*** END OPTIONS ***/

require('lib/PelJpeg.php'); // PHP EXIF Library (PEL)
require('lib/setGeolocation.php'); // Function to convert from decimal degrees to a format that PEL understands

// Converts photo titles to valid filenames
function slug($str, $replace=array(), $delimiter='-') {
	if( !empty($replace) ) {
		$str = str_replace((array)$replace, ' ', $str);
	}
	$clean = iconv('UTF-8', 'ASCII//TRANSLIT', $str);
	$clean = preg_replace("/[^a-zA-Z0-9\/_|+ -]/", '', $clean);
	$clean = strtolower(trim($clean, '-'));
	$clean = preg_replace("/[\/_|+ -]+/", $delimiter, $clean);
	return $clean;
}

$photos = array();

// Create needed directories
$directories= array('img/original/','img/modified/');
foreach ($directories as $directory) {
	if (!file_exists($directory)) {
		mkdir($directory,0755,true);
	}
}

$i = 0;
while ($i < ceil($qty/100)) {
	$from = $i*100;
	$to = $qty < $from+100 ? $qty : ($i+1)*100;
	$panoramio_query = json_decode(file_get_contents('http://www.panoramio.com/map/get_panoramas.php?set='.$set.'&from='.$from.'&to='.$to.'&size=original&mapfilter=false'));
	foreach ($panoramio_query->photos as $photo) {
		$file = $spaces_in_filename ? preg_replace('/\s+/',' ',preg_replace('/[^a-zA-Z0-9-_\s]/',' ', $photo->photo_title)).' '.$photo->photo_id.'.jpg' : slug($photo->photo_title).' '.$photo->photo_id.'.jpg';

		if(file_exists('img/modified/'.$file)) continue;

		copy($photo->photo_file_url, 'img/original/'.$file);

		// Status message
		echo $file.' ... ';

		// Create a PEL object
		$pelJpeg = new PelJpeg('img/original/'.$file);

		// Allow EXIF data to be set if none exists
		$pelExif = $pelJpeg->getExif();
		if ($pelExif == null) {
		    $pelExif = new PelExif();
		    $pelJpeg->setExif($pelExif);
		}

		// Allow TIFF data (subset of EXIF) to be set if none exists
		$pelTiff = $pelExif->getTiff();
		if ($pelTiff == null) {
		    $pelTiff = new PelTiff();
		    $pelExif->setTiff($pelTiff);
		}

		// Allow root image file directory to be set if none exists
		$pelIfd0 = $pelTiff->getIfd();
		if ($pelIfd0 == null) {
		    $pelIfd0 = new PelIfd(PelIfd::IFD0);
		    $pelTiff->setIfd($pelIfd0);
		}

		// Set the title
		$pelIfd0->addEntry(new PelEntryAscii(PelTag::IMAGE_DESCRIPTION, $photo->photo_title));

		// Set the author
		$pelIfd0->addEntry(new PelEntryAscii(PelTag::ARTIST, $photo->owner_name));

		// Set geolocation data
		$pelSubIfdGps = new PelIfd(PelIfd::GPS);
		$pelIfd0->addSubIfd($pelSubIfdGps);
		setGeolocation($pelSubIfdGps, $photo->latitude, $photo->longitude);

		// Save image
		$pelJpeg->saveFile('img/modified/'.$file);

		// Status message
		echo 'done.'."\n";
	}
	// Get next 100 photos (if necessary)
	$i++;
}

?>
