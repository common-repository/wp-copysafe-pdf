//  WordPress Copysafe PDF
//  Copyright (c) 1998-2020 ArtistScope. All Rights Reserved.
//  www.artistscope.com
//
// The Copysafe PDF Reader plugin is supported across all Windows since XP
//
// Special JS version for Wordpress

// Debugging outputs the generated html into a textbox instead of rendering
//	option has been moved to wp-copysafe-pdf.php

// REDIRECTS

var m_szLocation = document.location.href.replace(/&/g,'%26');	
var m_szDownloadNo = wpcsp_plugin_url + "download_no.html";
var m_szDownload = wpcsp_plugin_url + "download.html?ref=" + m_szLocation;


//===========================
//   DO NOT EDIT BELOW 
//===========================

var m_szAgent = navigator.userAgent.toLowerCase();
var m_szBrowserName = navigator.appName.toLowerCase();
var m_szPlatform = navigator.platform.toLowerCase();
var m_bNetscape = false;
var m_bMicrosoft = false;
var m_szPlugin = "";

var m_bWin64 = ((m_szPlatform == "win64") || (m_szPlatform.indexOf("win64")!=-1) || (m_szAgent.indexOf("win64")!=-1));
var m_bWin32 = ((m_szPlatform == "win32") || (m_szPlatform.indexOf("win32")!=-1));
var m_bWindows = (m_szAgent.indexOf("windows nt")!=-1);

var m_bASPS = ((m_szAgent.indexOf("artisreader")!=-1) && (m_bpASPS));
var m_bFirefox = ((m_szAgent.indexOf("firefox") != -1) && (m_bpFx));
var m_bChrome = ((m_szAgent.indexOf("chrome") != -1) && !(window.chrome && chrome.webstore && chrome.webstore.install) && (m_bpChrome));

var m_bNetscape = ((m_bASPS) || (m_bChrome) || (m_bFirefox));

if( m_bWindows < 0 )
{
	window.location=unescape(m_szDownloadNo);
	document.MM_returnValue=false;
}
else if( (m_bWindows) && (m_bNetscape) > 0)
{
	if( !m_bASPS && !m_bpDebugging ){
		window.location=unescape(m_szDownload);
		document.MM_returnValue=false;
	}
	else{
		m_szPlugin = "DLL";
	}
}
else
{
	window.location=unescape(m_szDownload);
	document.MM_returnValue=false;
}


// The copysafe-insert functions

function insertCopysafePDF(szDocName)
{
    if (m_bpDebugging == true)
	{ 
        document.writeln("<textarea rows='27' cols='80'>"); 
	}       
    if ((m_szPlugin == "DLL"))
    {
    	szObjectInsert = "type='application/x-artistscope-pdfreader5' codebase='" + wpcsp_plugin_url +"download.php' ";
    	document.writeln("<ob" + "ject " + szObjectInsert + " width='" + m_bpWidth + "' height='" + m_bpHeight + "'>");
    }
   
    document.writeln("<param name='Document' value='" + m_szImageFolder + m_szClassName + "' />");
    document.writeln("<param name='PrintsAllowed' value='" + m_bpPrintsAllowed + "' />");
    document.writeln("<param name='PrintAnywhere' value='" + m_bpPrintAnywhere + "' />");
    document.writeln("<param name='AllowCapture' value='" + m_bpAllowCapture + "' />");
    document.writeln("<param name='AllowRemote' value='" + m_bpAllowRemote + "' />"); 
    document.writeln("<param name='Language' value='" + m_bpLanguage + "' />");  
    document.writeln("<param name='Background' value='" + m_bpBackground + "' />");
	document.writeln("</object />");
	
    if (m_bpDebugging == true)
    {
    	document.writeln("</textarea />");
    }
}