<?php 

if (!function_exists('ilrc_css_custom')) {

	function ilrc_css_custom() { 

		$css = '';

		if ( ilrc_setting('ilrc_margintop') ) :

			$css .= '
				.internal-linking-related-contents:before { margin-top:' . esc_html(ilrc_setting('ilrc_margintop')) . '}';
			
		endif;
		
		if ( ilrc_setting('ilrc_marginbottom') ) :

			$css .= '
				.internal-linking-related-contents:after { margin-bottom:' . esc_html(ilrc_setting('ilrc_marginbottom')) . '}';
			
		endif;
		
		if ( ilrc_setting('ilrc_backgroundcolor') ) :

			$css .= '
				.internal-linking-related-contents .template-1,
				.internal-linking-related-contents .template-2,
				.internal-linking-related-contents .template-3 { background-color:' . esc_html(ilrc_setting('ilrc_backgroundcolor')) . '!important}';
			
		endif;
		
		if ( ilrc_setting('ilrc_backgroundcolorhover') ) :
			
			$css .= '
				.internal-linking-related-contents .template-1:hover,
				.internal-linking-related-contents .template-1:active,
				.internal-linking-related-contents .template-1:focus,
				.internal-linking-related-contents .template-2 span.cta,
				.internal-linking-related-contents .template-2:hover,
				.internal-linking-related-contents .template-2:active,
				.internal-linking-related-contents .template-2:focus,
				.internal-linking-related-contents .template-3:hover,
				.internal-linking-related-contents .template-3:active,
				.internal-linking-related-contents .template-3:focus,
				.internal-linking-related-contents .template-3 .postTitle { background-color:' . esc_html(ilrc_setting('ilrc_backgroundcolorhover')) . '!important}';

		endif;
				
		if ( ilrc_setting('ilrc_textcolor') ) :
			
			$css .= '
				.internal-linking-related-contents .template-1 span,
				.internal-linking-related-contents .template-2 span.postTitle,
				.internal-linking-related-contents .template-3 span.cta { color:' . esc_html(ilrc_setting('ilrc_textcolor')) . '}';
			
		endif;
		
		if ( ilrc_setting('ilrc_ctatextcolor') ) :
			
			$css .= '
				.internal-linking-related-contents .template-2 span.cta,
				.internal-linking-related-contents .template-3 span.postTitle { color:' . esc_html(ilrc_setting('ilrc_ctatextcolor')) . '}';
			
		endif;
		
		wp_add_inline_style( 'ilrc_style', $css );
		
	}

	add_action('wp_enqueue_scripts', 'ilrc_css_custom', 999);

}

?>