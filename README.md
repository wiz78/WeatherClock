# WeatherClock
A simple clock with weather info, meant to be used on a Rasperry Pi with its 7" display, in a full-screen Chromium instance

![Screenshot](screenshot.png)

Optionally, it can be configured to monitor a [Subsonic](http://www.subsonic.org/) instance and, when something is playing, replace the weather forecast with media info:

![Subsonic support](SubsonicSupport.png)

(it should also work with other streaming apps that implement Subsonic's API, such as [Ampache](http://ampache.org/) or [Madsonic](http://madsonic.org/)...)

# Clock setup

You'll need an API key for https://openweathermap.org in order to get the required information. Once you have it, edit **backend/config-dist.php** and save it as **backend/config.php**

# Chromium Setup

Follow the instructions at https://blogs.wcode.org/2013/09/howto-boot-your-raspberry-pi-into-a-fullscreen-browser-kiosk/ to setup your Pi to start Chromium on boot, pointing it to the website (hosted either on the Pi itself, or another machine)
