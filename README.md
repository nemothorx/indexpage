# indexpage - a better apache index

An directory indexer written in PHP to add more to directory listings. 

* It's prettier (CSS) 
* More interactive (column sorting by JS) 
* User creatable information (header and footer includes. Even replace the index with an include)
* More informative (directory sizes and information from subdirectory header includes)

Can nearly transparently replace the default indexer

# FEATURE THOUGHTS / TODO

* reimplementing serverside sorting? (see 1.1)
* implement file(1) with caching? (or at least make file checks an easy option at top of script, and/or secret URL hack option)
* Works on php7


## Bugs

* If the URL lacks a trailing slash, the URL gets messy


## Other helper files you may want to have:

* .header - the one in the root is always added above breadcrumbs
          - the local one (if different) is below the breadcrumbs
* .index - replaces the automatic listing
         - auto listing counts files/dirs  for "info" within each subdir
              # AND includes the first line of the .header from EACH subdir
* .readme - only the one local is checked, added below index listing
* .footer - only the one in the root is checked, added last

All these are included without further processing (yikes!) and assumed to be written in html
  * global header and footer intended to be one-liners
  * local header could go either way, but first line expected useful alone
  * index and readme intended to be multiline content

## Setup/usage

Place i.php at the path of the URL root, possibly with a suitably hidden name (for example `.i.php` and then direct directory requests to it. 


### Apache2
Originally written with apache/php in mind, current code does not work in apache properly. Older revisions do (or did), and used a .htaccess rule:

```
RewriteCond %{REQUEST_FILENAME}/ -d
RewriteRule (.*) /.i.php?path=$1 [NC]
```

### nginx
The current revision is known to work with nginx and php8.2-fpm. It uses the following within a `location /` block within a virtual host (noting that a php handler must also be defined)

```
    if ( -d $request_filename ) {
        rewrite ^(.*)$ /.i.php?path=/$1;
    }
```

## Security concerns

If the `?path=$1` construct is recognised and the php location known, it could be used to investigate paths not intended to reveal path information. It cannot traverse above the URL root, so should not be a danger to the wider system. 

Recommend it be used only where all paths are expected to be visible - ie, as a replacement to the web server's default directory listing html. 

If i.php exists in sub-paths, then the `?path=$1` constructs manually can lead to nonsensical results, where a requested path is not the current path, and the .index is taken from the requested path, whilst directory listing is taken from the current path. 
