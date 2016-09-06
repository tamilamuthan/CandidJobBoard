<?xml version="1.0"?>
<rss version="2.0" xmlns:atom="http://www.w3.org/2005/Atom">
    <channel>
        <title>[[Blog]]</title>
        <link><![CDATA[{$GLOBALS.site_url}/blog/]]></link>
        <description></description>
        <language>{$GLOBALS.current_language}</language>
        <pubDate>{$smarty.now|date_format:'D, d M y H:i:s O'}</pubDate>
        <lastBuildDate>{$smarty.now|date_format:'D, d M y H:i:s O'}</lastBuildDate>
        <generator>SmartJobBoard</generator>
        <webMaster>{$GLOBALS.settings.system_email} ({$GLOBALS.settings.site_title})</webMaster>
        <atom:link href="{$GLOBALS.site_url}/blog/rss/" rel="self" type="application/rss+xml" />
        {foreach from=$posts item=post}
            <item>
                <title><![CDATA[{$post.title}]]></title>
                <link><![CDATA[{$GLOBALS.site_url}/blog/{$post.sid}/{$post.title|pretty_url}]]></link>
                <description>
                    <![CDATA[{$post.description}]]>
                </description>
                <pubDate>{$post.date|date_format:'D, d M y H:i:s O'}</pubDate>
                <guid><![CDATA[{$GLOBALS.site_url}/blog/{$post.sid}/{$post.title|pretty_url}]]></guid>
            </item>
        {/foreach}
    </channel>
</rss>
