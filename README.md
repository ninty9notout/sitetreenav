## Overview

An extension which provides the functionality to add nested `<ul>` SiteTree menus into the template.

Since this was created to be used as a secondary navigation, this extension will output the tree menu from the second level of hierarchy, and will omit the top-most level (Home, About Us, Contact Us, etc) while listing only it's children.

## Requirements

Requires a SilverStripe 3 ([http://silverstripe.org](http://silverstripe.org)) installation.

## Installation:

* Put the module in your SilverStripe installation
* Rebuild the class manifest using `/dev/build?flush=all`

## Basic usage:

Place `$SiteTreeNav` somewhere in the theme. This will output the following HTML:

	<ul class="site-tree-nav">
		<li><a href="/about-us/our-services/">Our Services</a></li>
		<li class="section">
			<a href="/about-us/the-team/">The Team</a>
			<ul>
				<li><a href="/about-us/the-team/the-ceo/">The CEO</a></li>
				<li class="active"><a href="/about-us/the-team/the-secretary/">The Secretary</a></li>
			</ul>
		</li>
		<li><a href="/about-us/another-page/">Another Page</a></li>
	</ul>

The relevant classes will be applied to the `<li>` for styling purpose. The `.active` class marks the page that is currently being viewed, while the `.section` class represents the current sub level that is being viewed. In the example above, an user is viewing the page "The Secretary" which is a child of "The Team" page under the "About Us" section of the website.

## Development and Contribution

This is an unofficial second version of the [Nested Menu](https://github.com/markjames/silverstripe-nestedmenu) module by [Mark James](https://github.com/markjames), which provides the same functionality for SilverStripe 2.

Have any changes in mind?

* Fork from GitHub
* Do your thing
* Make a pull request
* ???
* Profit