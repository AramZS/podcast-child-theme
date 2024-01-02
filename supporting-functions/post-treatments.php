<?php
add_filter( 'the_content', 'ex1_the_content_filter' );
add_filter('the_excerpt', 'ex1_the_content_filter');
function ex1_the_content_filter($content) {
    // finds all links in your content
    preg_match_all('/(\<a href=.*?a>)/',$content,$matches, PREG_SET_ORDER);

    // loop through all matches
    foreach($matches as $m){
        // potential link to be replaced...
        $toReplace = $m[0];

		$baseUrl = parse_url(home_url(), PHP_URL_HOST);
		//$urlParts = explode('.', $baseUrl);

        // if current link does not already have target="{whatever}"
        // You can add whatever additional "IF" you require to this one
        if (!preg_match('/target\s*\=/',$toReplace)  && !preg_match('/'.preg_quote($baseUrl).'/',$toReplace)){
            // adds target="_blank" to the current link
            $replacement = preg_replace('/(\<a.*?)(\>)(.*?\/a\>.*?)/','$1 target="_blank" rel="nofollow"$2$3',$toReplace);
            // replaces the current link with the $replacement string
            $content = str_ireplace($toReplace,$replacement,$content);
        }
		$content = alter_link_to_htmx($toReplace, $content);
    }
      return $content;
}