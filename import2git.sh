#!/bin/bash

# Prior to this:
# * Script written and revised over many years, some versions saved or recovered from backup trawling
# * repo created on github: https://github.com/nemothorx/indexpage
# * This script created to turn different-file "repo" into a git repo with time metadata intact, using git-timemachine is as per https://github.com/nemothorx/git-timemachine

cp -a i-1.1.php i.php ; eval `git timemachine i.php` ; git add i.php
git commit -a -m "
1.1 (2005.september.26)
    - Added filetype column and css lines to show the rows
1.0 (after many months of ignoring the thing, mid septemberish 2005
    - Basically, it works
"

cp -a i-1.1a.php i.php ; eval `git timemachine i.php` ; git add i.php
git commit -a -m "
1.1a (27 Sept 2005)
    - improve requestbase by removing dirname
"

cp -a i-1.1b.php i.php ; eval `git timemachine i.php` ; git add i.php
git commit -a -m "
1.1b (30 Apr 2006)
    - test for php safe_mode before shell_exec
    - notes of intention for 1.2
"

cp -a i-1.5.php i.php ; eval `git timemachine i.php` ; git add i.php
git commit -a -m "
for 1.5: 2010 sept 28
    - added count-of-items inside directory
for 1.4: 2010 sept 26
    - fixed column headers links. php was leaking, due to stale
        server-side sorting code. Since all sorting is javascript
        client side, it's removed
for 1.3: (2010 sept 16) major reworking of bits to bring it up to 2010
    - fixed file handling, and can be called with path=foo/bar/ param
        - modrewrite means this can be used as proper index now
    - better inc file handling.
    - bug in escapeshellarg - strips non-ascii
for 1.2: fix bug so filesystem path to request URI is discoverable,
        thus allowing script to be called as a dirindex
"

cp -a i-1.6.php i.php ; eval `git timemachine i.php` ; git add i.php
git commit -a -m "
in 1.6: 2012 Apr 24
    - horrible time hack to subtract 3600 from a files time_t offset
      when php thinks it originated in a DST enabled time.
      Makes output consistent with 'ls'
"

cp -a i-1.6.1.php i.php ; eval `git timemachine i.php` ; git add i.php
git commit -a -m "
at 1.6.1: 2012 Sep 27
    - CSS tuning (li no longer right)
"

cp -a i-1.6.2.php i.php ; eval `git timemachine i.php` ; git add i.php
git commit -a -m "
so 1.6.2: 2013 Sep 16
    - cleaned up variables 
        (removing unused ones, ensuring all used are defined)
"

cp -a i-1.7.php i.php ; eval `git timemachine i.php` ; git add i.php
git commit -a -m "
very 1.7: 2014 Aug 14
    - file(1) check no longer default
    - directory item count fixed. Counts files and dirs seperate too.
    - directory type includes first line of that directories .header file
    - removed unused serverside sorting functions (but kept cmp_name)
    - fixed default sort order (from cmp_name :)
    - renamed 'type' column to 'info'
"

cp -a i-1.7.1.php i.php ; eval `git timemachine i.php` ; git add i.php
git commit -a -m "
this 1.7.1: 2014 Sept 05
    - style enhancement for the info column for directories
"

cp -a i-1.8.php i.php ; eval `git timemachine i.php` ; git add i.php
git commit -a -m "
much 1.8: 2014 Dec 22
    - breadcrumb workaround to avoid '?path=' ugliness. Still in URL tho
        (triggered when no trailing slash)
"

cp -a i-1.9.php i.php ; eval `git timemachine i.php` ; git add i.php
git commit -a -m "
1.9pre: 2020 Feb 10
    - added TODO intentions for 1.9
"

eval `git timemachine reset`

git add import2git.sh
git commit -a -m "
import2git.sh script method and original file versions listed for posterity
`ls -o --full-time`
"
