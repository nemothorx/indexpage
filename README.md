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
