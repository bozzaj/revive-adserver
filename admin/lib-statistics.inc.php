<?php // $Revision$

/************************************************************************/
/* phpAdsNew 2                                                          */
/* ===========                                                          */
/*                                                                      */
/* Copyright (c) 2001 by the phpAdsNew developers                       */
/* http://sourceforge.net/projects/phpadsnew                            */
/*                                                                      */
/* This program is free software. You can redistribute it and/or modify */
/* it under the terms of the GNU General Public License as published by */
/* the Free Software Foundation; either version 2 of the License.       */
/************************************************************************/



// Define defaults
$clientCache = array();
$bannerCache = array();
$zoneCache = array();



/*********************************************************/
/* Limit a string to a number of characters              */
/*********************************************************/

function phpAds_breakString ($str, $maxLen, $append = "...")
{
	return strlen($str) > $maxLen 
		? rtrim(substr($str, 0, $maxLen-strlen($append))).$append 
		: $str;
}


/*********************************************************/
/* Build the client name from ID and name                */
/*********************************************************/

function phpAds_buildClientName ($clientID, $clientName)
{
	return ("[id$clientID] $clientName");
}


/*********************************************************/
/* Fetch the client name from the database               */
/*********************************************************/

function phpAds_getClientName ($clientID)
{
	global $clientCache, $phpAds_tbl_clients;
	global $strUntitled;
	
	if ($clientID != '' && $clientID != 0)
	{
		if (isset($clientCache[$clientID]) && is_array($clientCache[$clientID]))
		{
			$row = $clientCache[$clientID];
		}
		else
		{
			$res = db_query("
			SELECT
				*
			FROM
				$phpAds_tbl_clients
			WHERE
				clientID = $clientID
			") or mysql_die();
			
			$row = @mysql_fetch_array($res);
			
			$clientCache[$clientID] = $row;
		}
		
		return (phpAds_BuildClientName ($clientID, $row['clientname']));
	}
	else
		return ($strUntitled);
}


/*********************************************************/
/* Get list order status                                      */
/*********************************************************/
// Order for $phpAds_tbl_clients
function phpAds_getListOrder ($ListOrder, $OrderDirection)
{
	switch ($ListOrder)
	{
		case 'name':
			$sqlTableOrder = 'ORDER BY clientname';
			break;
		case 'id':
			$sqlTableOrder = 'ORDER BY parent, clientID';
			break;
		default:
			$sqlTableOrder = 'ORDER BY clientname';
	}
	switch ($OrderDirection)
	{
		case 'down':
			$sqlTableOrder .= ' ';
			$sqlTableOrder .= 'ASC';
			break;
		case 'up':
			$sqlTableOrder .= ' ';
			$sqlTableOrder .= 'DESC';
			break;
		default:
			$sqlTableOrder .= ' ';
			$sqlTableOrder .= 'ASC';
	}
	return ($sqlTableOrder);
}

// Order for $phpAds_tbl_banners
function phpAds_getBannerListOrder ($ListOrder, $OrderDirection)
{
	switch ($ListOrder)
	{
		case 'name':
			$sqlTableOrder = 'ORDER BY description';
			break;
		case 'id':
			$sqlTableOrder = 'ORDER BY bannerID';
			break;
		default:
			$sqlTableOrder = 'ORDER BY description';
	}
	switch ($OrderDirection)
	{
		case 'down':
			$sqlTableOrder .= ' ';
			$sqlTableOrder .= 'ASC';
			break;
		case 'up':
			$sqlTableOrder .= ' ';
			$sqlTableOrder .= 'DESC';
			break;
		default:
			$sqlTableOrder .= ' ';
			$sqlTableOrder .= 'ASC';
	}
	return ($sqlTableOrder);
}

// Order for $phpAds_tbl_banners
function phpAds_getZoneListOrder ($ListOrder, $OrderDirection)
{
	switch ($ListOrder)
	{
		case 'name':
			$sqlTableOrder = 'ORDER BY zonename';
			break;
		case 'id':
			$sqlTableOrder = 'ORDER BY zoneid';
			break;
		default:
			$sqlTableOrder = 'ORDER BY zonename';
	}
	switch ($OrderDirection)
	{
		case 'down':
			$sqlTableOrder .= ' ';
			$sqlTableOrder .= 'ASC';
			break;
		case 'up':
			$sqlTableOrder .= ' ';
			$sqlTableOrder .= 'DESC';
			break;
		default:
			$sqlTableOrder .= ' ';
			$sqlTableOrder .= 'ASC';
	}
	return ($sqlTableOrder);
}


/*********************************************************/
/* Fetch the ID of the parent of a campaign              */
/*********************************************************/

function phpAds_getParentID ($clientID)
{
	global $clientCache, $phpAds_tbl_clients;
	
	if (isset($clientCache[$clientID]) && is_array($clientCache[$clientID]))
	{
		$row = $clientCache[$clientID];
	}
	else
	{
		$res = db_query("
		SELECT
			*
		FROM
			$phpAds_tbl_clients
		WHERE
			clientID = $clientID
		") or mysql_die();
		
		$row = @mysql_fetch_array($res);
		
		$clientCache[$clientID] = $row;
	}
	
	return ($row['parent']);
}



/*********************************************************/
/* Fetch the name of the parent of a campaign            */
/*********************************************************/

function phpAds_getParentName ($clientID)
{
	global $clientCache, $phpAds_tbl_clients;
	
	if (isset($clientCache[$clientID]) && is_array($clientCache[$clientID]))
	{
		$row = $clientCache[$clientID];
	}
	else
	{
		$res = db_query("
		SELECT
			*
		FROM
			$phpAds_tbl_clients
		WHERE
			clientID = $clientID
		") or mysql_die();
		
		$row = @mysql_fetch_array($res);
		
		$clientCache[$clientID] = $row;
	}
	
	return (phpAds_getClientName ($row['parent']));
}



/*********************************************************/
/* Build the banner name from ID, Description and Alt    */
/*********************************************************/

function phpAds_buildBannerName ($bannerID, $description = '', $alt = '', $limit = 30)
{
	global $strUntitled;
	
	$name = '';
	
	if ($description != "")
		$name .= $description;
	elseif ($alt != "")
		$name .= $alt;
	else
		$name .= $strUntitled;
	
	
	if (strlen($name) > $limit)
		$name = phpAds_breakString ($name, $limit);
	
	if ($bannerID != '')
		$name = "[id$bannerID] ".$name;
	
	return ($name);
}



/*********************************************************/
/* Fetch the banner name from the database               */
/*********************************************************/

function phpAds_getBannerName ($bannerID, $limit = 30, $id = true)
{
	global $bannerCache, $phpAds_tbl_banners;
	
	if (isset($bannerCache[$bannerID]) && is_array($bannerCache[$bannerID]))
	{
		$row = $bannerCache[$bannerID];
	}
	else
	{
		$res = db_query("
		SELECT
			*
		FROM
			$phpAds_tbl_banners
		WHERE
			bannerID = $bannerID
		") or mysql_die();
		
		$row = @mysql_fetch_array($res);
		
		$bannerCache[$bannerID] = $row;
	}
	
	if ($id)
		return (phpAds_buildBannerName ($bannerID, $row['description'], $row['alt'], $limit));
	else
		return (phpAds_buildBannerName ('', $row['description'], $row['alt'], $limit));
}


/*********************************************************/
/* Build the zone name from ID and name                  */
/*********************************************************/

function phpAds_buildZoneName ($zoneid, $zonename)
{
	return ("[id$zoneid] $zonename");
}


/*********************************************************/
/* Fetch the zone name from the database                 */
/*********************************************************/

function phpAds_getZoneName ($zoneid)
{
	global $zoneCache, $phpAds_tbl_zones;
	global $strUntitled;
	
	if ($zoneid != '' && $zoneid != 0)
	{
		if (isset($zoneCache[$zoneid]) && is_array($zoneCache[$zoneid]))
		{
			$row = $zoneCache[$zoneid];
		}
		else
		{
			$res = db_query("
			SELECT
				*
			FROM
				$phpAds_tbl_zones
			WHERE
				zoneid = $zoneid
			") or mysql_die();
			
			$row = @mysql_fetch_array($res);
			
			$zoneCache[$zoneID] = $row;
		}
		
		return (phpAds_BuildZoneName ($zoneid, $row['zonename']));
	}
	else
		return ($strUntitled);
}



/*********************************************************/
/* Fetch the HTML needed to display a banner from the db */
/*********************************************************/

function phpAds_getBannerCode ($bannerID)
{
	global $bannerCache, $phpAds_tbl_banners;
	
	if (is_array($bannerCache[$bannerID]))
	{
		$row = $bannerCache[$bannerID];
	}
	else
	{
		$res = db_query("
		SELECT
			*
		FROM
			$phpAds_tbl_banners
		WHERE
			bannerID = $bannerID
		") or mysql_die();
		
		$row = @mysql_fetch_array($res);
		
		$bannerCache[$bannerID] = $row;
	}
	
	return (phpAds_buildBannerCode ($bannerID, $row['banner'], $row['active'], $row['format'], $row['width'], $row['height'], $row['bannertext']));
}


/*********************************************************/
/* Build the HTML needed to display a banner             */
/*********************************************************/

function phpAds_buildBannerCode ($bannerID, $banner, $active, $format, $width, $height, $bannertext)
{
	global $strShowBanner;
	
	if ($active == "true")
	{
		if ($format == "html")
		{
			$htmlcode 	= htmlspecialchars (stripslashes ($banner));
			$htmlcode   = strlen($htmlcode) > 500 ? substr ($htmlcode, 0, 500)."..." : $htmlcode;
			$htmlcode	= chunk_split ($htmlcode, 70, "<br>\n");
			
			$buffer		= "<table width='100%' border='0' cellspacing='0' cellpadding='0'><tr>";
			$buffer    .= "<td width='80%' valign='top' align='left'>\n";
			$buffer	   .= $htmlcode;
			$buffer    .= "\n</td>";
			$buffer    .= "<td width='20%' valign='top' align='right' nowrap>&nbsp;&nbsp;";
			$buffer	   .= "<a href='banner-htmlpreview.php?bannerID=$bannerID' target='_new' ";
			$buffer	   .= "onClick=\"return openWindow('banner-htmlpreview.php?bannerID=$bannerID', '', 'status=no,scrollbars=no,resizable=no,width=$width,height=$height');\">";
			$buffer    .= "<img src='images/icon-zoom.gif' align='absmiddle' border='0'>&nbsp;".$strShowBanner."</a>&nbsp;&nbsp;</td>";
			$buffer	   .= "</tr></table>";
		}
		elseif($format == "url" || $format == "web")
		{
			if (eregi("swf$", $banner))
			{
				$buffer  = "<object classid='clsid:D27CDB6E-AE6D-11cf-96B8-444553540000' ";
				$buffer .= "codebase='http://download.macromedia.com/pub/shockwave/cabs/flash/";
				$buffer .= "swflash.cab#version=5,0,0,0' width='$width' height='$height'>";
				$buffer .= "<param name='movie' value='$banner'>";
				$buffer .= "<param name='quality' value='high'>";
				$buffer .= "<param name='bgcolor' value='#FFFFFF'>";
				$buffer .= "<embed src='$banner' quality=high ";
				$buffer .= "bgcolor='#FFFFFF' width='$width' height='$height' type='application/x-shockwave-flash' ";
				$buffer .= "pluginspace='http://www.macromedia.com/shockwave/download/index.cgi?P1_Prod_Version=ShockwaveFlash'></embed>";
				$buffer .= "</object>";
			}
			else
				$buffer = "<img src='$banner' width='$width' height='$height'>";
		}
		elseif($format == "swf")
		{
			$buffer  = "<object classid='clsid:D27CDB6E-AE6D-11cf-96B8-444553540000' ";
			$buffer .= "codebase='http://download.macromedia.com/pub/shockwave/cabs/flash/";
			$buffer .= "swflash.cab#version=5,0,0,0' width='$width' height='$height'>";
			$buffer .= "<param name='movie' value='../adview.php?bannerID=$bannerID'>";
			$buffer .= "<param name='quality' value='high'>";
			$buffer .= "<param name='bgcolor' value='#FFFFFF'>";
			$buffer .= "<embed src='../adview.php?bannerID=$bannerID' quality=high ";
			$buffer .= "bgcolor='#FFFFFF' width='$width' height='$height' type='application/x-shockwave-flash' ";
			$buffer .= "pluginspace='http://www.macromedia.com/shockwave/download/index.cgi?P1_Prod_Version=ShockwaveFlash'></embed>";
			$buffer .= "</object>";
		}
		else
			$buffer = "<img src='../adview.php?bannerID=$bannerID' width='$width' height='$height'>";
	}
	else
	{
		if ($format == "html")
		{
			$htmlcode 	= htmlspecialchars (stripslashes ($banner));
			$htmlcode   = strlen($htmlcode) > 500 ? substr ($htmlcode, 0, 500)."..." : $htmlcode;
			$htmlcode	= chunk_split ($htmlcode, 70, "<br>\n");
			
			$buffer		= "<table width='100%' border='0' cellspacing='0' cellpadding='0'><tr>";
			$buffer    .= "<td width='80%' valign='top' align='left' style='filter: Alpha(Opacity=50)'>\n";
			$buffer	   .= $htmlcode;
			$buffer    .= "\n</td>";
			$buffer    .= "<td width='20%' valign='top' align='right' nowrap>&nbsp;&nbsp;";
			$buffer	   .= "<a href='banner-htmlpreview.php?bannerID=$bannerID' target='_new' ";
			$buffer	   .= "onClick=\"return openWindow('banner-htmlpreview.php?bannerID=$bannerID', '', 'status=no,scrollbars=no,resizable=no,width=$width,height=$height');\">";
			$buffer    .= "<img src='images/icon-zoom.gif' align='absmiddle' border='0'>&nbsp;".$strShowBanner."</a>&nbsp;&nbsp;</td>";
			$buffer	   .= "</tr></table>";
		}
		elseif($format == "url" || $format == "web")
		{
			if (eregi("swf$", $banner))
			{
				$buffer  = "<object classid='clsid:D27CDB6E-AE6D-11cf-96B8-444553540000' ";
				$buffer .= "codebase='http://download.macromedia.com/pub/shockwave/cabs/flash/";
				$buffer .= "swflash.cab#version=5,0,0,0' width='$width' height='$height'>";
				$buffer .= "<param name='movie' value='$banner'>";
				$buffer .= "<param name='quality' value='high'>";
				$buffer .= "<param name='bgcolor' value='#FFFFFF'>";
				$buffer .= "<embed src='$banner' quality=high ";
				$buffer .= "bgcolor='#FFFFFF' width='$width' height='$height' type='application/x-shockwave-flash' ";
				$buffer .= "pluginspace='http://www.macromedia.com/shockwave/download/index.cgi?P1_Prod_Version=ShockwaveFlash'></embed>";
				$buffer .= "</object>";
			}
			else
				$buffer = "<img src='$banner' width='$width' height='$height' style='filter: Alpha(Opacity=50)'>";
		}
		elseif($format == "swf")
		{
			$buffer  = "<object classid='clsid:D27CDB6E-AE6D-11cf-96B8-444553540000' ";
			$buffer .= "codebase='http://download.macromedia.com/pub/shockwave/cabs/flash/";
			$buffer .= "swflash.cab#version=5,0,0,0' width='$width' height='$height'>";
			$buffer .= "<param name='movie' value='../adview.php?bannerID=$bannerID'>";
			$buffer .= "<param name='quality' value='high'>";
			$buffer .= "<param name='bgcolor' value='#FFFFFF'>";
			$buffer .= "<embed src='../adview.php?bannerID=$bannerID' quality=high ";
			$buffer .= "bgcolor='#FFFFFF' width='$width' height='$height' type='application/x-shockwave-flash' ";
			$buffer .= "pluginspace='http://www.macromedia.com/shockwave/download/index.cgi?P1_Prod_Version=ShockwaveFlash'></embed>";
			$buffer .= "</object>";
		}
		else
			$buffer = "<img src='../adview.php?bannerID=$bannerID' width='$width' height='$height' style='filter: Alpha(Opacity=50)'>";
	}
	
	if (!$bannertext == "")
		$buffer .= "<br>".$bannertext;
	
	return ($buffer);
}



/*********************************************************/
/* Build Click-Thru ratio                                */
/*********************************************************/

function phpAds_buildCTR ($views, $clicks)
{
	global $phpAds_percentage_decimals;
	
	if ($views > 0)
		$ctr = number_format(($clicks*100)/$views, $phpAds_percentage_decimals)."%";
	else
		$ctr="0.00%";
		
	return ($ctr);
}

?>
