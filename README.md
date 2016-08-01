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

Don't need the AJAX Page Container.

I highly recommend to use the "Blazing fast page delivery" described below

### S3 static page host (high performance / hard setup difficult / low cost) [NOT IMPLEMENTED]

Use the AJAX Page Container


## The AJAX Page Container

It is used to centralize the ajax framework requests. Because now you are running your website on
another infrastructure (some cases) you need to keep your plugins working.

### Setup

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


## The Ajax Flash Message

Only use this feature if your site use the [flash tag markup](https://octobercms.com/docs/markup/tag-flash)

Because of your site is now statefull, you will need to check if there are any message to be delivered to user.
This component do this job.

### Setup

Make sure you are using the {% flash %} tag.

By default this feature is disabled, **just active this feature on the settings page**.

In the settings you can adjust the delay to close the alert
or set the wrapper element where the alert will be placed.

### Checking for messages without redirect

You can call the function window.ebussolaStatefullCheckMessages whenever you want to check for new messages.


## Blazing fast page delivery

For an even better performance, install this piece of code inside your index.php just after the composer boot.

PS.: every time you update the OctoberCMS your index.php is rewritten, so you need to re-install this code to it.

```php
/*
|--------------------------------------------------------------------------
| eBussola.Statefull Plugin
|--------------------------------------------------------------------------
|
| Statefull check if an cache file exists and loads it, preventing to start the whole framework.
|
 */
$request = \Symfony\Component\HttpFoundation\Request::createFromGlobals();

if ($request->isMethod('GET')) {
    $cachePath = __DIR__ . '/storage/statefull-cache';
    $pathInfo = $request->getPathInfo();
    $blacklist = file_exists($cachePath . '/index-blacklist.config') ?
        file_get_contents($cachePath . '/index-blacklist.config') : null;
    $paramBlacklist = file_exists($cachePath . '/param-blacklist.config') ?
        json_decode(file_get_contents($cachePath . '/param-blacklist.config'), true) : [];

    $paramBlacklistFunctionFile = $cachePath . '/param-blacklist-function.php';
    if (file_exists($paramBlacklistFunctionFile)) {
        include $paramBlacklistFunctionFile;

        if (preg_match('/^(?!\/backend)(?!\/combine)' . $blacklist . '/i', $pathInfo) === 1 && !isParamBlacklisted($paramBlacklist)) {
            $file = $cachePath . $pathInfo . '.html';

            if (file_exists($file)) {
                echo file_get_contents($file);
                exit(0);
            }
        }
    }
}
```