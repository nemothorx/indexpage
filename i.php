<?
# A better directory indexer, in php

# Original code from Sneakums
# Extensively massaged by Nemo and Screwtape

# Pretty javascript from
# http://www.squarefree.com/bookmarklets/pagedata.html#sort_table

# version map of the star log thingy

# for 1.2: fix bug so filesystem path to request URI is discoverable,
# thus allowing script to be called as a dirindex

# 1.1 (2005.september.26)
#	- Added filetype column and css lines to show the rows

# 1.0 (after many months of ignoring the thing, mid septemberish 2005
#	- Basically, it works

# dir is the directory on the filesystem...
# BUG: this breaks when this script is called as a Dirindex from another
# location
$dir = dirname($_SERVER['SCRIPT_FILENAME']);

# requestbase is the directory path from the web view
// $requestbase = dirname($_SERVER["PHP_SELF"]);

# note that we don't do a dirname on this... so we get the full path thing,
# with an erroneous trailing slash, it should be noted.  this is basically
# correct (except for the trailing), if it's called as a .php but if the URI is
# automatic, then it's already a directory, and we do NOT need to dirname it...
# which is what we're aiming for... :)
$requestbase = $_SERVER['REQUEST_URI'];

# now, $dir should be the filesystem POV version of $requestbase

///////// FOR DEBUG /////

# echo $_SERVER['PATH_TRANSLATED'];
# $foo = $_SERVER['DOCUMENT_ROOT'];
# echo "docroot: $foo<br>";
$foo = $_SERVER['REQUEST_URI'];
//echo "request_uri: $foo<br>";
//echo "dirname of script_filename: $dir<br>";
//$dir = '/home/nemo/public_html/mediajunkie/';

/////////

$sort=$_GET['C'];
$order=$_GET['O'];
$neworder='D';	// default

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

function cmp_size ($a, $b) {
	# sorts numerically, small to large
	if ($a[1][7] == $b[1][7]) return strcasecmp ($a[0], $b[0]);
	return ($a[1][7] > $b[1][7]) ? -1 : 1;
}

function cmp_filetype ($a, $b) {
	if ($a[1][99] == $b[1][99]) return strcasecmp ($a[0], $b[0]);
	return ($a[1][99] > $b[1][99]) ? -1 : 1;
}

function cmp_mtime ($a, $b) {
	if ($a[1][9] == $b[1][9]) return strcasecmp ($a[0], $b[0]);
	return ($a[1][9] > $b[1][9]) ? -1 : 1;
}

function cmp_ctime ($a, $b) {
	if ($a[1][10] == $b[1][10]) return strcasecmp ($a[0], $b[0]);
	return ($a[1][10] > $b[1][10]) ? -1 : 1;
}

function cmp_dir ($a, $b) {
# 	if (is_dir($a[0]) == is_dir($b[0])) return strcasecmp ($a[0], $b[0]);
	return is_dir($a[0]) > is_dir($b[0]) ? -1 : 1;
}

function array_reverse_ref($a) {
   $r = array();
   for($i=0, $j=count($a); $i<count($a); $i++, $j--) {
       $r[$i] =& $a[$j-1];
   }
   return $r;
}


?>


<html xmlns="http://www.w3.org/1999/xhtml" xmlns:s="http://www.house.cx/~nemo/sortablelists">
    <head>
	<title>Index of <? echo $requestbase; ?></title>
        <meta http-equiv="Content-Type" content="application/xhtml+xml;charset=utf-8" />
        <style type="text/css">
            /* <![CDATA[ */
            body {
                margin: 0;
                padding: 0;
                
                background: Window;
                color: WindowText;
                font: message-box;
            }
            
            h1 {
                font: caption;
		font-size: large;
		background: InfoBackground;
                color: CaptionText;
                margin: 0;
		padding: 0.3em 0 0.3em 0.5em;
		border-bottom: 1px solid black;
            }
	    
            div.include {
                background: ButtonFace;
                float: left;
                width: 100%;
		border-top: 1px solid ButtonHighlight;
		border-bottom: 1px solid ButtonShadow;
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
                background: ButtonFace;
                color: ButtonText;
                padding: 0;
            }

            th a, td {
                padding: 0.3em 0.5em 0.3em 0.5em;              
                border-top: 1px solid ButtonHighlight;
                border-bottom: 1px solid ButtonShadow;
            }

	    tbody {
	        background: ButtonFace;
	    }

            th a {
                text-align: left;

                display: block;
                border-left: 1px solid ButtonHighlight;
                border-right: 1px solid ButtonShadow;
            }
            th a:hover {
		background: ButtonHighlight;
            }

	    th.filetype {
		width: 67%;
	    }
            
            td {
                background: ButtonFace;
		/*
		*/
                border-top: 1px solid ButtonShadow;
                border-bottom: 1px solid ButtonHighlight;
	    }
	    
            td.filetype {
	    	font-size: x-small;
            }
	  
	    pre {
		padding: 0.3em 0 0.3em 0.5em;
	    }

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

                if(!g_tables.length)
                    alert("This page doesn't contain any tables.");
                
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
	<h1>
	<?
		// link to top level server
		echo "http://<a href='http://", $_SERVER['SERVER_NAME'], "/'>", $_SERVER['SERVER_NAME'], "</a>";
		echo "/";
		// loop through the $requestbase to get links to each directory
		// in turn...
		$elements = preg_split('/\//', $requestbase, -1, PREG_SPLIT_NO_EMPTY);
		//echo $requestbase; 
		$url="/";
		$cnt=0;
		for ($piece=0; $piece < count($elements); $piece++ ) {
			$url=$url.$elements[$piece]."/";
			echo "<a href='$url'>$elements[$piece]</a>";
			echo "/";
		}
	?>
	</h1>

<?
		if(file_exists(".HEADER")) {
			echo "<div class='include'>";
			echo "<pre>";
			include ".HEADER";
			echo "</pre>\n";
			echo "</div>\n";
		}
        
if (($dir !== false) && !strstr ($dir, "..") && ($d = opendir (realpath ($dir)))) {
	$x = array();

	while (false !== ($f = readdir ($d))) {
		# now we drop anything that is a dotfile
		if (!ereg("(^\\.|~$)", $f)) {
			$y = stat ("$dir/$f");
			if( ini_get('safe_mode') ){
				// Do it the safe mode way
			   }else{
				// Do it the regular way
				$z = shell_exec("file -N '$dir/$f'");
			}
			list($filename, $filetype) = split(':', $z);
			$y[99] = $filetype;
			# force directory sizes to be zero
			if (is_dir("$dir/$f")) {
				$y[7]=0;
			}
			array_push ($x, array ($f, $y));
		}
	}

	# we need to put in sorting stuff here
	# TODO
	# work out the proper server-side sorting stuff. The sorting functions
	# exist above, we just don't reference them yet
	# refer to $sort and $order	
	if ($sort == 'M') {
        	usort ($x, "cmp_mtime");
	} else if ($sort == 'C') {
        	usort ($x, "cmp_ctime");
	} else if ($sort == 'S') {
        	usort ($x, "cmp_size");
	} else if ($sort == 'F') {
        	usort ($x, "cmp_filetype");
	} else {
        	usort ($x, "cmp_name");
	}


	if ($order == 'D') {
		$tmp=array_reverse_ref($x);
		$x=$tmp;
		$neworder='A';
	}
# BUG ALERT
# the 'neworder' changes for all sorting types, so there is no consistency
# on what the default order will be when you click a new column. 

?>
        <table>
            <thead>
                <tr>
                    <th s:type="number">
                        <a href='?C=S&amp;O=<? echo $neworder; ?>'>Size</a>
                    </th>
                    <th>
                        <a href='?C=M&amp;O=<? echo $neworder; ?>'>Timestamp</a>
                    </th>
                    <th class=name>
                        <a href='?C=N&amp;O=<? echo $neworder; ?>'>Name</a>
                    </th>
                    <th class=filetype>
                        <a href='?C=F&amp;O=<? echo $neworder; ?>'>Type</a>
                    </th>
                </tr>
            </thead>
            <tbody>
<?

	foreach ($x as $cons) {
		$f = $cons[0];
		echo "<tr>\n",
			"  <td align=right s:sortvalue='", $cons[1][7], "' nowrap>", is_file ("$dir/$f") ? bytes_pp ($cons[1][7]) : "", "</td>",
			"  <td nowrap>", strftime ("%Y-%m-%d  %H:%M", $cons[1][9]), "</td>\n",
			"  <td nowrap><a href='", rawurlencode($f), is_dir ("$dir/$f") ? "/" : "", "'>",
			$f, is_dir ("$dir/$f") ? "/" : "", "</a></td>\n",
			"  <td class='filetype'>", $cons[1][99], "</td>\n",
			"</tr>\n";
	}
}
?>
	    
            </tbody>
        </table>
	<? // echo $_SERVER["SERVER_SIGNATURE"]; ?>

<?
		if(file_exists(".README")) {
			echo "<div class='include'>";
			echo "<pre>";
			include ".README";
			echo "</pre>";
			echo "</div>";
		}

# this is good debug
# phpinfo();
?>

    </body>
</html>

