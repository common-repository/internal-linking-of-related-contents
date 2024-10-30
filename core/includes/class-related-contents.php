<?php

if( !class_exists( 'ilrc_content' ) ) {

	class ilrc_content {

		/**
		* Constructor
		*/

		public function __construct() {

			add_filter(
				'the_content',
				array(&$this, 'putRelatedContents'),
				ilrc_setting('ilrc_hookpriority', '999')
			);

		}

		/**
		* COUNT PARAGRAPHS
		*/

		public function countParagraphs($string) {

			$counter = 0;
			$temp = explode('</p>', $string);
			$counter = count($temp)-1;
			return $counter;

		}

		/**
		* SPLIT BY PARAGRAPHS BASIC FUNCTION
		*/

		public function splitbyParagraphsBasic($content, $relatedArray ) {

			$counter = 0;

			foreach ( $relatedArray as $relatedContent ) {

				if (isset($buffer[$relatedContent['position']])) {

					$buffer[$relatedContent['position']] .= $relatedContent['content'];

				} else {

					$buffer[$relatedContent['position']]  = $relatedContent['content'];

				}

			}

			$temp = explode('</p>', $content);

			$counter = count($temp)-1;

			foreach ( $buffer as $k => $v ) {

				if ($k === 0) {

					$temp[0] = $v . $temp[0];

				} elseif (array_key_exists($k, $temp))  {

					$temp[$k] = $v . $temp[$k] ;

				} elseif (!array_key_exists($k, $temp) || $k === -1){

					$temp[$counter] = $temp[$counter] . $v;

				}

			}

			$content = implode('</p>', $temp);
			return $content;

		}

		/**
		* INSERT NODE
		*/

		public function insertNode($parent, & $at, $newNode, $nestedLevel, $elementsToIgnore = false) {

			if(
				!is_object($parent) ||
				!property_exists($parent, "childNodes") ||
				$parent->childNodes === NULL
			){
		    	return;
		    }

			foreach($parent->childNodes as $element ) {

			    if( get_class($element) != "DOMElement"){
			        continue;
			    }

			    if(!property_exists($element,"tagName")){
			        continue;
			    }

				$tag = $element->tagName;

			    $class = $element->getAttribute("class");

				$toignore = explode('|', $elementsToIgnore);

			    if (
					in_array($class, $toignore) ||
					$tag == 'blockquote'
			    ){
			    	continue;
			    }

			    if($tag == 'p'){
			        $at--;
			        if($at==0){
			            $parent->insertBefore($newNode,$element);
			        }
			    }

					$this->insertNode(
						$element,
						$at,
						$newNode,
						$nestedLevel + 1,
						$elementsToIgnore
					);

		    }

		}

		/**
		* SPLIT BY PARAGRAPHS DOMDocument FUNCTION
		*/

		public function splitbyParagraphsDD($content, $relatedArray ) {

			$dom = new DOMDocument();
			libxml_use_internal_errors(true);
			$dom->loadHTML('<?xml encoding="utf-8" ?>' . $content);
			libxml_clear_errors();

			$domRoot = $dom->getElementsByTagName('body');
			$root_element = $domRoot[0];

			$temp = $dom->saveHTML();

			$i= 1 ;
			$offset = ilrc_setting('ilrc_offset', 2);

			foreach ($relatedArray as $relatedContent) {

				${"buffer" . $i} = $dom->createDocumentFragment();
				${"buffer" . $i}->appendXML($relatedContent['content']);
				${"position" . $i} =  $i * $offset + 1;
				$this->insertNode(
					$root_element,
					${"position" . $i},
					${"buffer" . $i},
					0,
					ilrc_setting('ilrc_toignore', 'wp-block-media-text__content|wp-block-cover-text|wp-block-column|wp-block-group__inner-container')
				);
				$i++;
			}

			$temp = preg_replace('~<(?:!DOCTYPE|/?(?:html|body))[^>]*>\s*~i', '', $dom->saveHTML());
			$return = str_replace('<?xml encoding="utf-8" ?>','', $temp);

			return $return;

		}

		/**
		* GET RELATED POSTS
		*/

		public function getRelatedContents($postID) {

			$catsArray = array();
			$tagsArray = array();

			$args = array(
				'post_type' => 'post',
				'posts_per_page' => '-1',
				'post_status' => 'publish',
				'orderby' => 'date',
				'order' => 'asc',
				'fields' => 'ids',
			);

			switch (ilrc_setting('ilrc_enginesearch', 'categories')) {

				case 'categories':
				default:

					foreach (get_the_category($postID) as $cat) {
						$catsArray[] = sanitize_text_field($cat->term_id);
					}

					$engineSearchArgs = array(

						'tax_query' => array(
							 array(
								'taxonomy' => 'category',
								'field' => 'term_id',
								'terms' => $catsArray,
								'operator' => 'IN'
							 ),

						)

					);

				break;
				case 'tags':

					foreach (wp_get_post_tags($postID) as $tag) {
						$tagsArray[] = sanitize_text_field($tag->term_id);
					}

					$engineSearchArgs = array(

						'tax_query' => array(
							 array(
								'taxonomy' => 'post_tag',
								'field' => 'term_id',
								'terms' => $tagsArray,
								'operator' => 'IN'
							 ),

						)

					);

				break;

			}

			$return = get_posts(array_merge($args, $engineSearchArgs));
			array_splice($return, array_search($postID, $return ), 1);

			return $return;

		}

		/**
		* PRINT RELATED CONTENTS
		*/

		public function printRelatedContents($postID) {

			$output  = '[ilrc';
			$output .= ' cta="' . esc_html__(ilrc_setting('ilrc_cta', esc_html__( 'Read more', 'internal-linking-related-contents'))) . '" ';
			$output .= ' template="' . esc_html__(ilrc_setting('ilrc_template', 'template-2')) . '" ';
			$output .= ' target="' . esc_html__(ilrc_setting('ilrc_targetattribute', '')) . '" ';
			$output .= ' rel="' . esc_html__(ilrc_setting('ilrc_relattribute', '')) . '" ';
			$output .= ' postid="' . esc_html__($postID) . '" ';
			$output .= ']';

			$output = do_shortcode($output);

			return $output;

		}

		/**
		* PUT RELATED CONTENTS
		*/

		public function putRelatedContents($content) {

			global $post;

			$postsArray = $this->getRelatedContents($post->ID);

			if ( count($postsArray) > 0 ) {

				$count = 0;

				foreach ($postsArray as $postContent) {

					$relatedArray[] = array(
						'position' => $count,
						'content' => $this->printRelatedContents($postContent)
					);

					$count++;

					if ( $count >= intval(ilrc_setting('ilrc_count', $this->countParagraphs($content))))
						break;


				}

				if (class_exists('DOMDocument')){

					return $this->splitbyParagraphsDD($content, $relatedArray);

				} else {

					return $this->splitbyParagraphsBasic($content, $relatedArray);

				}

			} else {

				return $content;

			}

		}

	}

	new ilrc_content();

}

?>
