{title}
    {$post.title}
{/title}
{keywords}
    {$post.keywords}
{/keywords}
{description}
    {if $post.description}
        {$post.description}
    {else}
        {$post.text|strip_tags|truncate:165}
    {/if}
{/description}

<a href="{$GLOBALS.user_site_url}/blog/" class="blog__back btn__back">[[Back]]</a>
<h1 class="title__primary title__primary-small title__centered title__bordered">{$post.title|escape}</h1>
<div class="static-pages content-text static-pages__blog">
    <div class="blog__content--date">
        {$post.date|date}
    </div>
    {if $post.image}
        <div class="blog__content--image">
            <img src="{$post.image}" />
        </div>
    {/if}
    {$post.text}

    <div class="social-share">
    <span class="social-share__title">
        [[Share]]:
    </span>
        <div class="social-share__icons">
            <span class='st_facebook_large' displayText='Facebook'></span>
            <span class='st_twitter_large' displayText='Tweet'></span>
            <span class='st_googleplus_large' displayText='Google +'></span>
            <span class='st_linkedin_large' displayText='LinkedIn'></span>
            <span class='st_pinterest_large' displayText='Pinterest'></span>
            <span class='st_email_large' displayText='Email'></span>
        </div>
    </div>
</div>
{literal}
    <script type="text/javascript">var switchTo5x=true;</script>
    <script type="text/javascript" src="https://ws.sharethis.com/button/buttons.js"></script>
    <script type="text/javascript">stLight.options({publisher: "3f1014ed-afda-46f1-956a-a51d42078320", doNotHash: false, doNotCopy: false, hashAddressBar: false});</script>
{/literal}