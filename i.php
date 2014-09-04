<?
# A better directory indexer, in php

# Original code from Sneakums
# Extensively massaged by Nemo and Screwtape

# Pretty javascript from
# http://www.squarefree.com/bookmarklets/pagedata.html#sort_table

# version map of the star log thingy

# FEATURE THOUGHTS
# * reimplementing serverside sorting? (see 1.1)
# * implement file(1) with caching? (or at least make file checks an easy option at top of script, and/or secret URL hack option)

# this 1.7.1: 2014 Sept 05
#	- style enhancement for the info column for directories

# very 1.7: 2014 Aug 14
#	- file(1) check no longer default
#	- directory item count fixed. Counts files and dirs seperate too.
#	- directory type includes first line of that directories .header file
#	- removed unused serverside sorting functions (but kept cmp_name)
#	- fixed default sort order (from cmp_name :)
#	- renamed 'type' column to 'info'

# so 1.6.2: 2013 Sep 16
#	- cleaned up variables (removing unused ones, ensuring all used are defined)

# at 1.6.1: 2012 Sep 27
#	- CSS tuning. (li no longer right)

# in 1.6: 2012 Apr 24
#       - horrible time hack to subtract 3600 from a files time_t offset when php thinks it originated in a DST enabled time. Makes output consistent with `ls`


# for 1.5: 2010 sept 28
#	- added count-of-items inside directory

# for 1.4: 2010 sept 26
#	- fixed column headers links. php was leaking, due to stale server-side sorting code. Since all sorting is javascript client side, it's removed

# for 1.3: (2010 sept 16) major reworking of bits to bring it up to 2010
#	- fixed file handling, and can be called with path=foo/bar/ param
#	    - modrewrite means this can be used as proper index now
#	- better inc file handling. 
#	- bug in escapeshellarg - strips non-ascii

# for 1.2: fix bug so filesystem path to request URI is discoverable,
# thus allowing script to be called as a dirindex

# 1.1 (2005.september.26)
#	- Added filetype column and css lines to show the rows

# 1.0 (after many months of ignoring the thing, mid septemberish 2005
#	- Basically, it works


# dir is the directory on the filesystem that we want to inspect!
# NEEDED so we know where to inspect files with `find`

# BUG: this breaks when this script is called as a Dirindex from another
# location
$scriptdir = dirname($_SERVER['SCRIPT_FILENAME'])."/";
$reqdir = $_GET['path'];
$dir = $scriptdir.$reqdir;
#echo "dir: $dir<br>";

#name(script_filename)),realpath($_REQUEST['path']))!==0) {
#                      die ("FUCK YOU! KEEP OUTTA MY FILEZ!"); }

$requesturi = $_SERVER['REQUEST_URI'];
#echo "requesturi: $requesturi<br>";

# TODO
# make `dir` be calculatable from requesturi, NOT from SCRIPT_FILENAME

//$requestparts = pathinfo($_SERVER['REQUEST_URI']);
//$requestdir = $requestparts['dirname'];
// echo "pathinfo dirname: $requestdir<br>";


# echo $_SERVER['PATH_TRANSLATED'];
# $foo = $_SERVER['DOCUMENT_ROOT'];
# echo "docroot: $foo<br>";
//$foo = $_SERVER['REQUEST_URI'];
// echo "request_uri: $foo<br>";
//echo "dirname of script_filename: $dir<br>";

//$infstuff = apache_lookup_uri($_SERVER['REQUEST_URI']);
//print_r($infstuff);
//echo "<br>";
//$infname= $infstuff->filename;
//echo "infstuff: $infname<br>";
//echo getcwd() . "\n";

/////////

function bytes_pp ($n) {
	if ($n < 1024)
		return sprintf ("% 6dB", $n);
	if ($n < 1048756)
		return sprintf ("% 6.1fK", (float)$n / 1024);
	if ($n < 1073741824)
		return sprintf ("% 6.1fM", (float)$n / 1048756);
	return sprintf ("% 6.1fG", (float)$n / 1073741824);
}

function cmp_name ($a, $b) {
            # sorts alphabetical, a->z
            return strcasecmp ($a[0], $b[0]);
}


?>


<html xmlns="http://www.w3.org/1999/xhtml" xmlns:s="http://www.house.cx/~nemo/sortablelists">
    <head>
	<title>Index of <? echo $requesturi; ?></title>
        <meta http-equiv="Content-Type" content="application/xhtml+xml;charset=utf-8" />

        <style type="text/css">
            /* <![CDATA[ */
<? 

$Window = "#000";
$WindowText = "#999";
$InfoBackground = "#333";
$CaptionText = "#aaa";
$ButtonText = "#F00";
$ButtonFace = "#222";
$ButtonHighlight = "#333";
$ButtonShadow = "#111";

print "
            body {
                margin: 0;
                padding: 0;
                
                background: $Window;
                color: $WindowText;
                font: sans-serif;
            }

	a:link {
	  color: #3a3;
	  font-weight: bold;
	}
	a:visited {
	  color: #797;
	}
	a:hover {
	  color: #f93;
    	background: $ButtonShadow;
	}

            
            h1 {
                font: caption;
		font-size: large;
		background: $InfoBackground;
                color: $CaptionText;
                margin: 0;
		padding: 0.5em 0 0.5em 2em;
		border-bottom: 1px solid $ButtonShadow;
            }
	    
            div.include {
                background: $Window;
		padding-left: 1em;
		padding-top: 0.5em;
		padding-bottom: 0.5em;
		border-top: 1px solid $ButtonShadow;
		border-bottom: 1px solid $ButtonHighlight;
            }
	    
            table {
                clear: left;
		background: black;
                border-collapse: seperate;
		border-spacing: 0;
                width: 100%;
		empty-cells: show;
                
                font: message-box; /* IE compatibility :< */
		border-bottom: 1px solid Black;
            }
            
            th {
                background: $ButtonFace;
                color: $ButtonText;
                padding: 0;
            }

            th a, td {
                padding: 0.3em 0.5em 0.3em 0.5em;              
                border-top: 1px solid $ButtonHighlight;
                border-bottom: 1px solid $ButtonShadow;
            }

	    tbody {
	        background: $ButtonFace;
	    }

            th a {
                text-align: left;

                display: block;
                border-left: 1px solid $ButtonHighlight;
                border-right: 1px solid $ButtonShadow;
            }
            th a:hover {
		background: $ButtonHighlight;
            }

	    th.fileinfo {
		width: 67%;
	    }

            td {
		/*
                background: $ButtonFace;
                border-top: 1px solid $ButtonShadow;
                border-bottom: 1px solid $ButtonHighlight;
		*/
		border: 0px;
	    }
            tr.data:hover {
		background-color: $ButtonShadow;
            }
	    
            td.fileinfo {
	    	font-size: small;
            }
	  
	    pre {
		padding: 0.3em 0 0.3em 0.5em;
	    }
	    ul {
		margin: 2em;
		text-align: left;
	    }
" ?>

            /* ]]> */
        </style>
        
        <script type="text/javascript">
            /* <![CDATA[ */
            
            /*
             * Functions for table sorting
             */
             
            var g_order;

            function toArray(c) {
                var a, k;
                
                a = new Array;
                
                for (k=0; k < c.length; ++k)
                    a[k]=c[k];
                return a;
            }
            
            function countCols(tab) {
                var nCols, i;

                nCols = 0;
                for (i=0; i < tab.rows.length; ++i)
                    if(tab.rows[i].cells.length > nCols)
                        nCols=tab.rows[i].cells.length;
                return nCols;
            }
            
            function insAtTop(par,child) {
                if (par.childNodes.length)
                    par.insertBefore(child, par.childNodes[0]);
                else
                    par.appendChild(child);
            }
            
            function compareRows(a,b) {
                if (a.sortKey == b.sortKey)
                    return 0;
                
                return (a.sortKey < b.sortKey) ? g_order : -g_order;
            }
            
            function sortTable(table, colNo, type) {
                var rows, nR, bs, i, j, sortCell, sortKey;
                rows = new Array();
                bs=table.tBodies;

                // Get the current sort order
                g_order = table.sortOrder;
                if (g_order == undefined) {
                    g_order = table.sortOrder = -1;
                } 
                
                // Reverse it
                g_order = g_order * -1;
                
                // Save our new sort order for next time.
                table.sortOrder = g_order
                    
                for (i=0; i < bs.length; ++i) {
                    for (j=0; j < bs[i].rows.length; ++j) {
                        // Add this row to our list of rows.
                        nR = rows.push(bs[i].rows[j]);
                        
                        sortCell = rows[nR-1].cells[colNo];
                        
                        if (sortCell) {
                            // Get a sort key for this row.
                            rows[nR-1].sortKey = sortCell.getAttribute("s:sortvalue");
                            if (rows[nR-1].sortKey == "" || rows[nR-1].sortKey == null)
                                rows[nR-1].sortKey = sortCell.textContent;
                            
                            // Convert it to the appropriate type.
                            switch (type) {
                                case "number":
                                    rows[nR-1].sortKey = parseFloat(rows[nR-1].sortKey);
                                    if (isNaN(rows[nR-1].sortKey))
                                        rows[nR-1].sortKey = 0;
                                    break;
                                case "text":
                                    rows[nR-1].sortKey = rows[nR-1].sortKey.toLowerCase();
                                    break;
                            }
                                    
                        } else {
                            rows[nR-1].sortKey = "";
                        }
                        
                        //alert("Row " + (nR-1) + " has key '" + rows[nR-1].sortKey + "'");
                    }
                }
                    
                rows.sort(compareRows);
                
                for (i=0; i < rows.length; ++i)
                    insAtTop(table.tBodies[0], rows[i]);
            }
            
            function sortTableFromLink(evt) {
                var cell, row, table, type, colNo;
                
                // Look upward for the nearest cell, row, and table ancestors.
                cell = evt.target;
                while (cell.tagName.toLowerCase() != "th") {
                    cell = cell.parentNode;
                }
                
                row = evt.target;
                while (row.tagName.toLowerCase() != "tr") {
                    row = row.parentNode;
                }

                table = evt.target;
                while (table.tagName.toLowerCase() != "table") {
                    table = table.parentNode;
                }

                // Get the sort type, if there is one.
                type = cell.getAttribute("s:type")
                if (type == "" || type == null) {
                    type = "text"
                }
                
                // Count out which column we're looking at.
                for (colNo = 0; colNo < row.cells.length; ++colNo) {
                    if (row.cells[colNo] == cell) break;
                }
                                
                // Do the sort.
                sortTable(table, colNo, type, 1);
                
                // Stop the click from actually working.
                // This is broken in Safari because Safari is stupid.
                evt.preventDefault();
            }
            
            function addSortLinks(evt) {
                var g_tables, j, k, l, thead, links;

                g_tables = toArray(document.getElementsByTagName('table'));

//                if(!g_tables.length)
//                    alert("This page doesn't contain any tables.");
                
                for (j=0; j < g_tables.length; ++j) {
                    thead = g_tables[j].tHead.rows[0];
                    
                    for (k=0; k < thead.cells.length; ++k) {
                        links = toArray(thead.cells[k].getElementsByTagName('a'));
                        
                        for (l=0; l < links.length; ++l) {
                            links[l].addEventListener("click", sortTableFromLink, false);
                        }
                    }
                }
            }

            // Don't blow up if we load this page in IE 6
            if (window.addEventListener)
                window.addEventListener("load", addSortLinks, false);
            /* ]]> */
        </script>
    </head>
    <body>
<?
		if(file_exists(".header")) {
			echo "<div class='include'>";
			include ".header";
			echo "</div>\n";
		}
echo "	<h1>\n";
		// link to top level server
		echo "http://<a href='http://", $_SERVER['HTTP_HOST'], "/'>", $_SERVER['HTTP_HOST'], "</a>";
		//echo "/";
		// loop through the $requesturi to get links to each directory
		// in turn...
		$elements = preg_split('/\//', $requesturi, -1, PREG_SPLIT_NO_EMPTY);
		$url="/";
		$cnt=0;
		for ($piece=0; $piece < count($elements); $piece++ ) {
			echo "/";
			$url=$url.$elements[$piece]."/";
			echo "<a href='$url'>$elements[$piece]</a>";
		}
		// fudge to find out if we're looking at a directory and append final slash
		$reverse=strrev($requesturi);
		if ($reverse{0} == "/") { echo "/\n";}
echo "	</h1>\n";

		if(file_exists("$reqdir/.header")) {
			echo "<div class='include'>";
			include "$reqdir/.header";
			echo "</div>\n";
		}

if(file_exists("$dir/.index")) {
	echo "<div class='include'>";
	include "$dir/.index";
	echo "</div>\n";
} else {
        
    if (($dir !== false) && !strstr ($dir, "..") && ($d = opendir (realpath ($dir)))) {
	    $x = array();

	    while (false !== ($f = readdir ($d))) {
		    # now we drop anything that is a dotfile
		    if (!ereg("(^\\.|~$)", $f)) {
			    $y = stat ("$dir/$f");
# file(1) check. This can hurt performance. Off by default now
#			    $fileinfo = shell_exec("file -b -z ".escapeshellarg($dir."/".$f));
			    $y[99] = $fileinfo;
			    # special extra magic for directories:
			    if (is_dir("$dir/$f")) {
				# force directory sizes to be zero
				$y[7]=0;
				$dircnt=0;
				$filecnt=0;
				# count internal objects
#    http://www.php.net/manual/en/function.scandir.php#95913  <-- to get extra info into directory lists?
				# TODO: make sorting by type sort directories by their count? 
				$dirscanresult = scandir("$dir/$f");
				if (is_array($dirscanresult)) {
				    foreach($dirscanresult as $entry) {
					if (!preg_match("/^\..*/", "$entry")) {
					    if (is_dir("$dir/$f/$entry")) {
						$dircnt++;
					    } else {
						$filecnt++;
					    }
					}
				    }
				}
#				$filecnt = count($dirscanresult)-2;
				$y[99] .= $fileinfo ." <tt>[" .$dircnt ." dirs, " .$filecnt ." files]</tt>"; 
				if(file_exists("$dir/$f/.header")) {
					$y[99] .= " ".shell_exec("head -1 ".escapeshellarg($dir."/".$f."/.header"));
				}
			    }
			    array_push ($x, array ($f, $y));
		    }
	    }

    usort ($x, "cmp_name");
    

    echo "
	    <table>
		<thead>
		    <tr>
			<th s:type='number'>
			    <a href='#'>Size</a>
			</th>
			<th>
			    <a href='#'>Timestamp</a>
			</th>
			<th class=name>
			    <a href='#'>Name</a>
			</th>
			<th class=fileinfo>
			    <a href='#'>Info</a>
			</th>
		    </tr>
		</thead>
		<tbody>
    ";

	    foreach ($x as $cons) {
		    $f = $cons[0];
//		    $rowcount++;
		    # ick ick ick. This hack works around the "bug" where file dates are shown with the offset from when they were made. This is diferent to 'ls'.
		    # this hack makes the times appear self-consistent in the output, but only if
		    # no timezone information is revealed out
	            if (date('I', $cons[1][9]) == 1) { $cons[1][9] -= 3600; }

		    echo "<tr class=data>\n",
			    "  <td align=right s:sortvalue='", $cons[1][7], "' nowrap>", is_file ("$dir/$f") ? bytes_pp ($cons[1][7]) : "", "</td>",
			    "  <td nowrap>", strftime ("%Y-%m-%d  %H:%M", $cons[1][9]), "</td>\n",
			    "  <td nowrap><a href='", rawurlencode($f), is_dir ("$dir/$f") ? "/" : "", "'>",
			    $f, is_dir ("$dir/$f") ? "/" : "", "</a></td>\n",
			    "  <td class='fileinfo'>", $cons[1][99], "</td>\n",
			    "</tr>\n";
	    }
    }

}
?>
	    
            </tbody>
        </table>
	<? // echo $_SERVER["SERVER_SIGNATURE"]; ?>

<?
		if(file_exists(".readme")) {
			echo "<div class='include'>";
			include ".readme";
			echo "</div>\n";
		}

# this is good debug
# phpinfo();
?>
    </body>
</html>

