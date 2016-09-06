<?xml version="1.0" encoding="utf-8"?>
<trovit>
{foreach from=$listings item=listing}
    <ad>
        <id><![CDATA[{$listing.id}]]></id>
        <url><![CDATA[{$GLOBALS.site_url}{$listing|listing_url}]]></url>
        <title><![CDATA[{$listing.Title|clrNonPrintedChars}]]></title>
        <content><![CDATA[{$listing.JobDescription|strip_tags:false|clrNonPrintedChars}]]></content>
        <company><![CDATA[{$listing.user.CompanyName|clrNonPrintedChars}]]></company>
        <category><![CDATA[{foreach from=$listing.JobCategory item=list_value name="multifor"}{tr}{$list_value}{/tr}{if !$smarty.foreach.multifor.last}, {/if}{/foreach}]]></category>
        <contract><![CDATA[{foreach from=$listing.EmploymentType item=list_value name="multifor"}{tr}{$list_value}{/tr}{if !$smarty.foreach.multifor.last}, {/if}{/foreach}]]></contract>
        <city><![CDATA[{$listing.Location.City|clrNonPrintedChars}]]></city>
        <region><![CDATA[{$listing.Location.State}]]></region>
        <postcode><![CDATA[{$listing.Location.ZipCode}]]></postcode>
        <date><![CDATA[{$listing.activation_date|replace:'-':'/'}]]></date>
        <expiration_date><![CDATA[{$listing.expiration_date|replace:'-':'/'}]]></expiration_date>
        <contact_name><![CDATA[{$listing.user.FullName}]]></contact_name>
        {if stripos($listing.user.username, 'jobg8') === false}
            <contact_email><![CDATA[{$listing.user.username}]]></contact_email>
        {/if}
    </ad>
{/foreach}
</trovit>