⚠️ This tool is deprecated
=======================

Unfortunately, Panoramio has removed access to their `http://www.panoramio.com/map/get_panoramas.php` endpoint. This means that `panoramio-dump` has no way of getting the filenames so it can download the photos.

This repository will stay up for posterity, but it doesn't work anymore. Sorry.


Panoramio Dump
==============

Download Panoramio photos with geolocation, author, and description info embedded in EXIF tags.

I threw this tool together so I could preserve the geolocation info on 500+ photos that I had mapped manually on Panoramio. Since Panoramio doesn't offer a bulk export function, use this to create a backup of your photos. It's also handy if you want to bulk upload to another photo hosting provider &mdash; the image descriptions and geolocation are coded right into the file.

*Note: this tool makes use of the [Panoramio API](http://www.panoramio.com/api/terms.html), which prohibits downloading or making copies of photos hosted on Panoramio. So please, don't download photos that you don't have the rights to.*

Setup
-------

There are a few options at the beginning of `index.php`. They're pretty straightforward:

    $set = 4494359;             // Panoramio user ID, or 'public' for all photos
    $qty = 10;                 // Number of photos to fetch
    $spaces_in_filename = true; // Save filenames as "Photo Title.jpg" (true) or "photo-title.jpg" (false)

Once you have those options set, make sure `img/`, `img/original/`, and `img/modified` are writable by the server.

Run the dump
------------

A simple `php index.php` in your terminal will start the process and you'll see filenames appear as they are created.

    > $ php index.php 
    Olive tree at Corga 101949894.jpg ... done.
    Fes medina walls 98699195.jpg ... done.
    Ume Julmarknad 101954715.jpg ... done.
    Lisbon waterfront 95857127.jpg ... done.

If you need to interrupt the process, you can just run the file again and it'll pick up where it left off. (Before doing work on an image, the script checks the `img/modified/` folder to see if there's already one with the same name. If there is, it'll skip to the next photo.)

Thanks
------

Thanks to [PEL](http://lsolesen.github.io/pel/) and [sharpbang](http://sharpbang.wordpress.com/2013/08/18/adding-exif-data-using-php/) for sharing wonderful code and making my life easier.
