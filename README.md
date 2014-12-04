hyp-google-maps
===============

A Wordpress google maps and geocoding plugin

This plugin allows you to easily create Google maps and geocode users, posts, pages and custom post types. You can now toggle the option for user geocoding. To learn more about map options see [Google MapOptions](http://code.google.com/apis/maps/documentation/javascript/reference.html#MapOptions).

##Options Page
![Options Page](https://lh5.googleusercontent.com/-8LOMoXLVMYE/VH-UbgVzMcI/AAAAAAAAAQ4/GPhKRxdRjGQ/w0-h0-no/options.jpg)

##Edit User
![Edit User](https://lh5.googleusercontent.com/-8LOMoXLVMYE/VH-UbgVzMcI/AAAAAAAAAQ4/GPhKRxdRjGQ/w0-h0-no/user.jpg)

##Edit Post
![Edit Post](https://lh5.googleusercontent.com/-8LOMoXLVMYE/VH-UbgVzMcI/AAAAAAAAAQ4/GPhKRxdRjGQ/w0-h0-no/post.jpg)


###Manual Usage

* **API Key** - Obtain your API Key and enter it into this field
* **Geocoded Post Types** - Post types to geocode and display the map/form
* **Default Location** - Stored Lat,Long in decimal degrees for the starter map
* **Enter Location** - Input an address or general location, then press the 'Geocode' button

###Shortcode
```php
[hgm_map width="400px" height="400px"]
```
```php
[hgm_geocoder width="400px" height="400px"]
```

###PHP

```php
<?php hgm_map('width=400px&height=400px')); ?>
```
```php
<?php hgm_map(array('width' => '400px','height' => '400px')); ?>
```
```php
<?php hgm_geocoder(array('width' => '400px','height' => '400px')); ?>
```
```php
<?php hgm_get_user_location($user_id); ?>
```
```php
<?php hgm_get_post_location($post_id); ?>
```

###Map Arguments

* **width** - Enter css width value, 100px, 50% etc
* **height** - Enter css height value, 100px, 50% etc
* **center** - Enter lat,long (decimal deg) separated by a comma
* **zoom** - Google zoom value
* **heading** - Enter a heading for the info window
* **content** - Enter content for the info window
* **options** - Enter a comma separated list of Google MapOptions. Ex = maxZoom:2,minZoom:10

##Geocode Arguments

* **width** - Enter css width value, 100px, 50% etc
* **height** - Enter css height value, 100px, 50% etc
* **center** - Enter lat,long (decimal deg) separated by a comma
* **zoom** - Google zoom value
* **options** - Enter a comma separated list of Google MapOptions
* **position** - Enter 'above' to display map above the form

##Geocode Complete Event

```javascript
<script type="text/javascript">
function saveValue(){ FORMFIELD.value = hgmLocation; }
	eventObject.addEventlistener('geocoded',saveValue);
</script>
```
