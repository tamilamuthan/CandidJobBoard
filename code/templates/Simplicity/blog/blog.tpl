<div class="posts-list">
    <h1 class="title__primary title__primary-small title__centered title__bordered">[[Blog]]</h1>
    <link rel="alternate" type="application/rss+xml" href="{$GLOBALS.site_url}/blog/rss/" title="[[Blog]]" />
    {foreach from=$posts item='post'}
        <article class="media well listing-item listing-item__blog {if not $post.image}listing-item__no-logo{/if}">
            {if $post.image}
                <div class="media-left listing-item__logo">
                    <a href="{$GLOBALS.site_url}/blog/{$post.sid}/{$post.title|pretty_url}/">
                        <img class="media-object profile__img-company" src="{$post.image}" alt="{$post.title|escape}">
                    </a>
                </div>
            {/if}
            <div class="media-body">
                <div class="media-heading listing-item__title">
                    <a href="{$GLOBALS.site_url}/blog/{$post.sid}/{$post.title|pretty_url}/" class="link">{$post.title|escape}</a>
                </div>
                <div class="listing-item__info clearfix">
                    <span class="blog__content--date">
                        {$post.date|date}
                    </span>
                </div>
                <div class="listing-item__desc">
                    {$post.text|strip_tags}
                </div>
            </div>
        </article>
    {/foreach}
    {if $posts|@count == 10}
        <button type="button" class="load-more btn btn__white" data-page="2">
            [[Load more]]
        </button>
    {/if}
</div>
{javascript}
    <script>
        $('.load-more').click(function() {
            var self = $(this);
            self.addClass('loading');
            $.get('?&page=' + self.data('page'), function(data) {
                self.removeClass('loading');
                var posts = $(data).find('.listing-item__blog');
                if (posts.length < 10) {
                    self.hide();
                }
                if (posts.length) {
                    $('.listing-item__blog').last().after(posts);
                    self.data('page', parseInt(self.data('page')) + 1);
                }
            });
        });
    </script>
{/javascript}