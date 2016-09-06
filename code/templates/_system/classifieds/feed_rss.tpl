<?xml version="1.0" encoding="utf-8"?>
<rss version="2.0" xmlns:atom="http://www.w3.org/2005/Atom">
	<channel>
		<title><![CDATA[{$GLOBALS.settings.site_title}]]></title>
		<link><![CDATA[{$GLOBALS.site_url}]]></link>
		<description><![CDATA[Jobs from {$GLOBALS.settings.site_title}]]></description>
		<language>{$GLOBALS.current_language}</language>
		<pubDate>{$lastBuildDate} GMT</pubDate>
		<lastBuildDate>{$lastBuildDate} GMT</lastBuildDate>
		<generator>Smart Job Board</generator>
		<webMaster>{$GLOBALS.settings.system_email} ({$GLOBALS.settings.site_title})</webMaster>
		<image>
			<url>{$GLOBALS.site_url}/templates/{$GLOBALS.settings.TEMPLATE_USER_THEME}/assets/images/{$GLOBALS.theme_settings.logo|escape:'url'}</url>
			<title>{$GLOBALS.settings.site_title}</title>
			<link>{$GLOBALS.custom_domain_url}</link>
		</image>
		<atom:link href="{$GLOBALS.custom_domain_url}/feeds/{$feed.id}.xml" rel="self" type="application/rss+xml" />
		{foreach from=$listings item=listing name=listings_block}
			<item>
				<title><![CDATA[{$listing.Title|clrNonPrintedChars}]]></title>
				<link><![CDATA[{$GLOBALS.site_url}{$listing|listing_url}]]></link>
				<description>
					<![CDATA[{$listing|location}
					{$listing.user.CompanyName|clrNonPrintedChars}<br/>
					{$listing.JobDescription|clrNonPrintedChars}]]>
				</description>
				<pubDate>{$listing.activation_date|date_format:'D, d M Y H:i:s'} GMT</pubDate>
				<guid><![CDATA[{$GLOBALS.site_url}{$listing|listing_url}]]></guid>
			</item>
		{/foreach}
	</channel>
</rss>
