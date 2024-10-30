<?php

if (!function_exists('ilrc_function')) {

	function ilrc_function($atts,  $content = null) {
		
		extract(shortcode_atts(array(
			'postid' => '',
			'template' => 'template-2',
			'posttitle' => '',
			'url' => '',
			'target' => '',
			'rel' => '',
			'cta' => esc_html__( 'Read more', 'internal-linking-related-contents')
		), $atts));

			$output = '';

			$relatedPost = get_post($postid);
			
			$title = ($posttitle) ? $posttitle : $relatedPost->post_title;
			$links = ($url) ? $url : get_permalink($postid);
			
			$target = ($target == '_blank') ? 'target="_blank" ' : '';
			$rel = ($rel == 'nofollow') ? 'rel="nofollow" ' : '';

			switch ($template) {

				case 'template-1':
				case 'template-2':
				case 'template-3':
				default:

					$output .= '<div class="internal-linking-related-contents">';
					$output .= '<a ' . $target . $rel . ' href="' . esc_url($links) . '" class="' . esc_attr($template) . '">';
					$output .= '<span class="cta">';
					$output .= esc_html($cta);
					$output .= '</span>';
					$output .= '<span class="postTitle">';
					$output .= esc_html($title);
					$output .= '</span>';
					$output .= '</a>';
					$output .= '</div>';

				break;

			}
		
		return $output;
		
	}
	
	add_shortcode('ilrc', 'ilrc_function');

}

?>
