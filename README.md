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

## Bugs

* If the URL lacks a trailing slash, the URL gets messy
* Last tested and working on php5. Not working on php7

## Other helper files you may want to have:

* .header (info above the page)
* .index (include this file INSTEAD of generating an index)
* .readme (footer)

Note that the header and footer check at the top level of the URL path,
whilst the header (again) and index are checked at the requested path
location. Thus you can have a site-specific header and footer, and a 
page-specific header and index. 


