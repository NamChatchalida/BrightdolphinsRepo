<!-- start Simple Custom CSS and JS -->
<script type="text/javascript">
function yourprefix_remove_title_youtube_oembed( $html, $url, $args ) {

    if( strpos( $url, 'youtu.be' ) !== false || strpos( $url, 'youtube.com' ) !== false ) :

        return str_replace( '?feature=oembed', '?feature=oembed&amp;showinfo=0&amp;rel=0', $html );

    else:

        return $html;

    endif;

}
add_filter( 'embed_oembed_html', 'yourprefix_remove_title_youtube_oembed', 10, 3 );
</script>
<!-- end Simple Custom CSS and JS -->
