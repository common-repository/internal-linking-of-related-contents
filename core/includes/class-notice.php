<?php
 
if ( ! defined( 'ABSPATH' ) ) {
    exit;
} // Exit if accessed directly

if( !class_exists( 'ilrc_admin_notice' ) ) {

	class ilrc_admin_notice {
	
		/**
		 * Constructor
		 */
		 
		public function __construct( $fields = array() ) {

			if ( !get_user_meta( get_current_user_id(), 'ilrc_notice_userid_' . get_current_user_id() , TRUE ) ) {

				add_action( 'admin_notices', array(&$this, 'admin_notice') );
				add_action( 'admin_head', array( $this, 'dismiss' ) );
			
			}

		}

		/**
		 * Dismiss notice.
		 */
		
		public function dismiss() {
		
			if ( isset( $_GET['ilrc-dismiss'] ) ) {
		
				update_user_meta( get_current_user_id(), 'ilrc_notice_userid_' . get_current_user_id() , intval($_GET['ilrc-dismiss']) );
				remove_action( 'admin_notices', array(&$this, 'admin_notice') );
				
			} 
		
		}

		/**
		 * Admin notice.
		 */
		 
		public function admin_notice() {
			
			global $pagenow;
			$redirect = ( 'admin.php' == $pagenow ) ? '?page=ilrc_panel&ilrc-dismiss=1' : '?ilrc-dismiss=1';
			
		?>
			
            <div class="update-nag notice ilrc-notice">
            
            	<div class="ilrc-noticedescription">
					<strong><?php _e( 'Upgrade to Internal Linking of Related Contents Pro to unlock premium features like 9 premium templates, related posts based on post titles or custom keywords, option to exclude specific posts/categories or post tags, custom post type support, AMP support, and much more.', 'internal-linking-related-contents' ); ?></strong><br/>
					<?php printf( '<a href="%1$s" class="dismiss-notice">'. __( 'Dismiss this notice', 'internal-linking-related-contents' ) .'</a>', esc_url($redirect) ); ?>
                </div>
                
                <a target="_blank" href="<?php echo esc_url( ILRC_UPGRADE_LINK . '/?ref=2&campaign=ilrc-notice' ); ?>" class="button"><?php _e( 'Upgrade Now', 'internal-linking-related-contents' ); ?></a>
                <div class="clear"></div>

            </div>
		
		<?php
		
		}

	}

}

new ilrc_admin_notice();

?>