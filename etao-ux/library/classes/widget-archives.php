<?php
/**
 * The Archives widget replaces the default WordPress Archives widget. This version gives total
 * control over the output to the user by allowing the input of all the arguments typically seen
 * in the wp_get_archives() function.
 *
 * @package Hybrid
 * @subpackage Classes
 * @author Justin Tadlock <justin@justintadlock.com>
 * @copyright Copyright (c) 2008 - 2011, Justin Tadlock
 * @link http://themehybrid.com/hybrid-core
 * @license http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 */

/**
 * Archives widget class.
 *
 * @since 0.6.0
 * @link http://codex.wordpress.org/Template_Tags/wp_get_archives
 * @link http://themehybrid.com/themes/hybrid/widgets
 */
class Hybrid_Widget_Archives extends WP_Widget {

	/**
	 * Set up the widget's unique name, ID, class, description, and other options.
	 * @since 1.2.0
	 */
	function __construct() {

		/* Set up the widget options. */
		$widget_options = array(
			'classname' => 'archives',
			'description' => esc_html__( 'An advanced widget that gives you total control over the output of your archives.', 'hybrid-core' )
		);

		/* Set up the widget control options. */
		$control_options = array(
			'width' => 525,
			'height' => 350
		);

		/* Create the widget. */
		$this->WP_Widget(
			'hybrid-archives',			// $this->id_base
			__( 'Archives', 'hybrid-core' ),	// $this->name
			$widget_options,			// $this->widget_options
			$control_options			// $this->control_options
		);
	}

	/**
	 * Outputs the widget based on the arguments input through the widget controls.
	 * @since 0.6.0
	 */
	function widget( $args, $instance ) {
		extract( $args );

		/* Set up the arguments for wp_get_archives(). */
		$args = array(
			'type' =>			$instance['type'],
			'format' =>		$instance['format'],
			'before' =>		$instance['before'],
			'after' =>		$instance['after'],
		    'show_author' => !empty ( $instance['show_author'] ) ? true : false,
			'show_post_count' =>	!empty( $instance['show_post_count'] ) ? true : false,
			'limit' =>			!empty( $instance['limit'] ) ? intval( $instance['limit'] ) : '',
			'echo' =>			false
		);

		/* Output the theme's $before_widget wrapper. */
		echo $before_widget;

		/* If a title was input by the user, display it. */
		if ( !empty( $instance['title'] ) )
			echo $before_title . apply_filters( 'widget_title',  $instance['title'], $instance, $this->id_base ) . $after_title;

		/* Get the archives list. */
		$archives = str_replace( array( "\r", "\n", "\t" ), '', wp_get_archives( $args ) );

		/* If the archives should be shown in a <select> drop-down. */
		if ( 'option' == $args['format'] ) {

			/* Create a title for the drop-down based on the archive type. */
			if ( 'yearly' == $args['type'] )
				$option_title = esc_html__( 'Select Year', 'hybrid-core' );

			elseif ( 'monthly' == $args['type'] )
				$option_title = esc_html__( 'Select Month', 'hybrid-core' );

			elseif ( 'weekly' == $args['type'] )
				$option_title = esc_html__( 'Select Week', 'hybrid-core' );

			elseif ( 'daily' == $args['type'] )
				$option_title = esc_html__( 'Select Day', 'hybrid-core' );

			elseif ( 'postbypost' == $args['type'] || 'alpha' == $args['type'] )
				$option_title = esc_html__( 'Select Post', 'hybrid-core' );

			/* Output the <select> element and each <option>. */
			echo '<p><select name="archive-dropdown" onchange=\'document.location.href=this.options[this.selectedIndex].value;\'>';
				echo '<option value="">' . $option_title . '</option>';
				echo $archives;
			echo '</select></p>';
		}
		
		
	

		
		/*后期代码优化，暂时顶一下，最小化改动原先的代码*/
		
		/* If the format should be an unordered list. */
		elseif ( 'html' == $args['format'] ) {
			echo '<ul class="xoxo archives">';		
			if (have_posts ()) :
			 
			 while ( have_posts() ) : the_post(); 				
			   echo '<li>';
			   echo apply_atomic_shortcode( 'sidebar-author',  __( '[siderbar-author]'));
			   echo apply_atomic_shortcode( 'sidebar_title', '[siderbar-title]' ); 
			   echo '</li>';			
			  
			 endwhile;
			
			endif;
			
			echo '</ul><!-- .xoxo .archives -->';
		}

		/* All other formats. */
		else {
			echo $archives;
		}

		/* Close the theme's widget wrapper. */
		echo $after_widget;
	}

	/**
	 * Updates the widget control options for the particular instance of the widget.
	 * @since 0.6.0
	 */
	function update( $new_instance, $old_instance ) {
		$instance = $old_instance;

		$instance = $new_instance;

		$instance['title'] = strip_tags( $new_instance['title'] );
		$instance['before'] = strip_tags( $new_instance['before'] );
		$instance['after'] = strip_tags( $new_instance['after'] );
		/*是否要显示作者*/
		$instance['show_author'] = ( isset( $new_instance['show_author'] ) ? 1 : 0 );
		
		$instance['limit'] = strip_tags( $new_instance['limit'] );
		$instance['show_post_count'] = ( isset( $new_instance['show_post_count'] ) ? 1 : 0 );

		return $instance;
	}

	/**
	 * Displays the widget control options in the Widgets admin screen.
	 * @since 0.6.0
	 */
	function form( $instance ) {

		/* Set up the default form values. */
		$defaults = array(
			'title' => esc_attr__( 'Archives', 'hybrid-core' ),
			'limit' => 10,
			'type' => 'monthly',
			'format' => 'html',
			'before' => '',
			'after' => '',
		    'show_author' => false,
			'show_post_count' => false
		);

		/* Merge the user-selected arguments with the defaults. */
		$instance = wp_parse_args( (array) $instance, $defaults );

		/* Create an array of archive types. */
		$type = array( 'alpha' => esc_attr__( 'Alphabetical', 'hybrid-core' ), 'daily' => esc_attr__( 'Daily', 'hybrid-core' ), 'monthly' => esc_attr__( 'Monthly', 'hybrid-core' ),'postbypost' => esc_attr__( 'Post By Post', 'hybrid-core' ), 'weekly' => esc_attr__( 'Weekly', 'hybrid-core' ), 'yearly' => esc_attr__( 'Yearly', 'hybrid-core' ) );

		/* Create an array of archive formats. */
		$format = array( 'custom' => esc_attr__( 'Custom', 'hybrid-core' ), 'html' => esc_attr__( 'HTML', 'hybrid-core' ), 'option' => esc_attr__( 'Option', 'hybrid-core' ) );
		?>

		<div class="hybrid-widget-controls columns-2">
		<p>
			<label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e( 'Title:', 'hybrid-core' ); ?></label>
			<input type="text" class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" value="<?php echo esc_attr( $instance['title'] ); ?>" />
		</p>
		<p>
			<label for="<?php echo $this->get_field_id( 'limit' ); ?>"><code>limit</code></label>
			<input type="text" class="smallfat code" id="<?php echo $this->get_field_id( 'limit' ); ?>" name="<?php echo $this->get_field_name( 'limit' ); ?>" value="<?php echo esc_attr( $instance['limit'] ); ?>" />
		</p>
		<p>
			<label for="<?php echo $this->get_field_id( 'type' ); ?>"><code>type</code></label> 
			<select class="widefat" id="<?php echo $this->get_field_id( 'type' ); ?>" name="<?php echo $this->get_field_name( 'type' ); ?>">
				<?php foreach ( $type as $option_value => $option_label ) { ?>
					<option value="<?php echo esc_attr( $option_value ); ?>" <?php selected( $instance['type'], $option_value ); ?>><?php echo esc_html( $option_label ); ?></option>
				<?php } ?>
			</select>
		</p>
		<p>
			<label for="<?php echo $this->get_field_id( 'format' ); ?>"><code>format</code></label> 
			<select class="widefat" id="<?php echo $this->get_field_id( 'format' ); ?>" name="<?php echo $this->get_field_name( 'format' ); ?>">
				<?php foreach ( $format as $option_value => $option_label ) { ?>
					<option value="<?php echo esc_attr( $option_value ); ?>" <?php selected( $instance['format'], $option_value ); ?>><?php echo esc_html( $option_label ); ?></option>
				<?php } ?>
			</select>
		</p>
		</div>

		<div class="hybrid-widget-controls columns-2 column-last">
		<p>
			<label for="<?php echo $this->get_field_id( 'before' ); ?>"><code>before</code></label>
			<input type="text" class="widefat code" id="<?php echo $this->get_field_id( 'before' ); ?>" name="<?php echo $this->get_field_name( 'before' ); ?>" value="<?php echo esc_attr( $instance['before'] ); ?>" />
		</p>
		<p>
			<label for="<?php echo $this->get_field_id( 'after' ); ?>"><code>after</code></label>
			<input type="text" class="widefat code" id="<?php echo $this->get_field_id( 'after' ); ?>" name="<?php echo $this->get_field_name( 'after' ); ?>" value="<?php echo esc_attr( $instance['after'] ); ?>" />
		</p>
		<p>
			<label for="<?php echo $this->get_field_id( 'show_post_count' ); ?>">
			<input class="checkbox" type="checkbox" <?php checked( $instance['show_post_count'], true ); ?> id="<?php echo $this->get_field_id( 'show_post_count' ); ?>" name="<?php echo $this->get_field_name( 'show_post_count' ); ?>" /> <?php _e( 'Show post count?', 'hybrid-core' ); ?> <code>show_post_count</code></label>
		</p>
		<p>
			<label for="<?php echo $this->get_field_id( 'show_author' ); ?>">
			<input class="checkbox" type="checkbox" <?php checked( $instance['show_author'], true ); ?> id="<?php echo $this->get_field_id( 'show_author' ); ?>" name="<?php echo $this->get_field_name( 'show_author' ); ?>" /> <?php _e( 'show author?', 'hybrid-core' ); ?> <code>show_author</code></label>
		</p>
		</div>
		<div style="clear:both;">&nbsp;</div>
	<?php
	}
}

?>