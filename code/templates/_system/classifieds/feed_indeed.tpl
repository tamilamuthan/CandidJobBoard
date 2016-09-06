<?xml version="1.0" encoding="utf-8"?>
<source>
<publisher>{$GLOBALS.settings.site_title}</publisher>
<publisherurl><![CDATA[{$GLOBALS.site_url}]]></publisherurl>
<lastBuildDate>{$lastBuildDate}</lastBuildDate>
{foreach from=$listings item=listing}
    <job>
        <title><![CDATA[{$listing.Title|clrNonPrintedChars}]]></title>
        <date><![CDATA[{$listing.activation_date}]]></date>
        <referencenumber><![CDATA[{$listing.id}]]></referencenumber>
        <url><![CDATA[{$GLOBALS.site_url}{$listing|listing_url}]]></url>
        <company><![CDATA[{$listing.user.CompanyName|clrNonPrintedChars}]]></company>
        <city><![CDATA[{$listing.Location.City|clrNonPrintedChars}]]></city>
        <state><![CDATA[{$listing.Location.State}]]></state>
        <country><![CDATA[{$listing.Location.Country}]]></country>
        <postalcode><![CDATA[{$listing.Location.ZipCode}]]></postalcode>
        <description><![CDATA[{$listing.JobDescription|strip_tags:false|clrNonPrintedChars}]]></description>
        <salary><![CDATA[]]></salary>
        <education><![CDATA[]]></education>
        <jobtype><![CDATA[{foreach from=$listing.EmploymentType item=list_value name="multifor"}{tr}{$list_value}{/tr}{if !$smarty.foreach.multifor.last}, {/if}{/foreach}]]></jobtype>
        <category><![CDATA[{foreach from=$listing.JobCategory item=list_value name="multifor"}{tr}{$list_value}{/tr}{if !$smarty.foreach.multifor.last}, {/if}{/foreach}]]></category>
        <experience><![CDATA[]]></experience>
    </job>
{/foreach}
</source>