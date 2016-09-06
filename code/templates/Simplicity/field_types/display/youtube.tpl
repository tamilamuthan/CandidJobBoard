{if $value}
	<iframe src="https://www.youtube.com/embed/{$value|regex_replace:'#https?://(www\.)?(youtube.com/watch\?v=|youtu.be/)#u':''|escape}?html5=1" allowscriptaccess="always" allowfullscreen="true" class="youtube-video"></iframe>
{/if}