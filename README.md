October CMS Statefull Website Plugin
====================================

This plugin was developed to increase the performance of high traffic websites.
On my experiments, it increased the performance drastically, mainly for pageviews.
But, it adds some complexity to deploy or update the contents and in most of cases it
will change the architecture of your website.

## Installation

Search for "statefull" or use the plugin's code: eBussola.Statefull

Choose a structure to use and follow the instructions

## Structures

### Local cache pages (low performance / easy setup difficult / no extra costs )

Don't need the AJAX Page Container

### CloudFlare cache layer (high performance / medium setup difficult / cost $20 per month)

Use the AJAX Page Container

### S3 static page host (high performance / hard setup difficult / low cost)

Use the AJAX Page Container


## The AJAX Page Container

It is used to centralize the ajax framework requests. Because now you are running your website on
another infrastructure you need to keep your plugins working.

### Configuring

1. Create a page with the route /ajax
(You can customise this route)
![image 1](https://s3.amazonaws.com/ebussola-stash/statefull-website/screenshots/ajax-page-container-1.png)

2. Add every component used on all your pages with exact the same configuration.
![image 2](https://s3.amazonaws.com/ebussola-stash/statefull-website/screenshots/ajax-page-container-2.png)

3. Drop the "AJAX Page Container component" to your footer's layout
![animated 1](https://s3.amazonaws.com/ebussola-stash/statefull-website/screenshots/ajax-page-container-3.gif)

#### Customise the ajax route

You can customise the url of ajax page container on the settings.
Remember to change the route on the page too.