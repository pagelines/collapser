<?php
/*
Section: Collapser
Author: Enrique Chávez
Author URI: http://tmeister.net
Version: 2.3
Description: Collapser is a simple but handy section that provides a way to show small pieces of information using an accordion-nav type with a feature image on a side to stand out the content. With more that 15 options to play with.
Class Name: CollapserTm
External: http://enriquechavez.co/products/collapser/
Demo: http://pagelines.tmeister.net/collapser/
PageLines: true
*/

require_once( 'cmb/custom-meta-boxes.php' );

class CollapserTm extends PageLinesSection
{

    var $domain           = 'tm_collapser';
    var $tax_id           = 'tm_collapser_sets';
    var $custom_post_type = 'tm_collapser_post';
    var $section_name      = 'Collapser';
    var $section_version   = '2.3';
    var $section_key ;
    var $chavezShop;


    function section_persistent()
    {
        $this->post_type_setup();
        $this->section_key = strtolower( str_replace(' ', '_', $this->section_name) );
        //$this->verify_license();
        //add_filter('pl_sorted_settings_array', array(&$this, 'add_global_panel'));
        ( PL_CORE_VERSION > '1.0.4' ) ? add_filter( 'cmb_meta_boxes', array(&$this, 'meta_boxes') ) : $this->post_meta_setup();

    }

     function verify_license(){
        if( !class_exists( 'chavezShopVerifier' ) ) {
            include( dirname( __FILE__ ) . '/inc/chavezshop_verifier.php' );
        }
        $this->chavezShop = new chavezShopVerifier( $this->section_name, $this->section_version, $this->opt('collapser_license_key') );
    }

    function add_global_panel($settings){
        $valid = "";
        if( get_option( $this->section_key."_activated" ) ){
            $valid = ( $this->chavezShop->check_license() ) ? ' - Your license is valid' : ' - Your license is invalid';
        }

        if( !isset( $settings['eChavez'] ) ){
            $settings['eChavez'] = array(
                'name' => 'Enrique Chavez Shop',
                'icon' => 'icon-shopping-cart',
                'opts' => array()
            );
        }

        $collapser_opts = array(
            'key'   => 'collapser_license_key',
            'type'  => 'text',
            'title' => '<i class="icon-shopping-cart"></i> ' . __('Collapser License Key', 'collapser') . $valid,
            'label' => __('License Key', 'collapser'),
            'help'  => __('The section is fully functional whitout a key license, this license is used only get access to autoupdates within your admin.', 'collapser')

        );

        array_push($settings['eChavez']['opts'], $collapser_opts);
        return $settings;

    }

    function dmshify(){
        if( function_exists('pl_has_editor') ){
            return $this->prefix();
        }else{
            return '#nodms';
        }
    }

    function get_dms_clone_id($prefix){
        preg_match('/"([^"]*)"/', $prefix, $match);
        return $match[1];
    }

    function section_head($clone_id = null)
    {
        global $post, $pagelines_ID;

        //DMS Compatibility
        $clone_id    = function_exists('pl_has_editor') ? $this->get_dms_clone_id( $this->prefix() ) : $clone_id;
        $img_path    = function_exists('pl_has_editor') ? WP_PLUGIN_URL . "/collapser/sections/collapser/images" : PL_EXTEND_URL . "/collapser/images" ;

        $parent      = "collapser-accordion".$clone_id;
        $oset        = array('post_id' => $pagelines_ID, 'clone_id' => $clone_id);
        $limit       = ( $this->opt('tm_collapser_items', $oset) ) ? $this->opt('tm_collapser_items', $oset) : '5';
        $set         = ( $this->opt('tm_collapser_set', $oset) ) ? $this->opt('tm_collapser_set', $oset) : null;
        $this->posts = $this->get_posts($this->custom_post_type, $this->tax_id, $set, $limit);


        if( !count( $this->posts ) ){
            return;
        }

        $current     = $this->posts[0];
        $last        = $parent.'-collapser-'.$current->ID;


        /**********************************************************************
        ** Styles
        **********************************************************************/
        $title_back             = $this->opt('tm_collapser_section_title_bg',$oset) ? pl_hashify( $this->opt('tm_collapser_section_title_bg',$oset)) : '#fff';
        $title_color            = $this->opt('tm_collapser_title_color',$oset) ? pl_hashify($this->opt('tm_collapser_title_color',$oset)) : '#21759B';
        $item_back              = $this->opt('tm_collapser_item_background',$oset) ? pl_hashify($this->opt('tm_collapser_item_background',$oset)) : '#fff';
        $item_back_hover        = $this->opt('tm_collapser_item_background_over',$oset) ? pl_hashify($this->opt('tm_collapser_item_background_over',$oset)) : '#21759B';
        $item_title_color       = $this->opt('tm_collapser_title_item_color',$oset) ? pl_hashify($this->opt('tm_collapser_title_item_color',$oset)) : '#000000';
        $item_title_color_hover = $this->opt('tm_collapser_title_over_color',$oset) ? pl_hashify($this->opt('tm_collapser_title_over_color',$oset)) : '#000000';
        $border                 = $this->opt('tm_collapser_menu_border',$oset) ? pl_hashify($this->opt('tm_collapser_menu_border',$oset)) : '#eaeaea';
        $content_color          = $this->opt('tm_collapser_text_color',$oset) ? pl_hashify($this->opt('tm_collapser_text_color',$oset)) : '#000000';


    ?>
        <script>
            jQuery(document).ready(function()
            {
                var last<?php echo $clone_id ?> = "<?php echo $last ?>";
                jQuery('#<?php echo $parent ?> .collapser-heading').delegate('.collapser-toggle','click',function()
                {
                    var target = jQuery( this ).parent();
                    var collapser = jQuery( this ).parent().parent().parent();
                    var image, gallery;

                    if( last<?php echo $clone_id ?> == target.attr('id') ){
                        return;
                    }

                    collapser.find('.active').removeClass('active');
                    target.addClass('active');
                    image = jQuery(this).data('image');
                    gallery = jQuery('#<?php echo $parent ?>-wrapper').find('.collapser-gallery img');
                    gallery.fadeOut('slow', function(){
                        gallery.attr('src', image);
                        gallery.fadeIn('slow');
                    });
                    last<?php echo $clone_id ?> = target.attr('id');
                });
            });
        </script>

        <style type="text/css">
            .collapser-block<?php echo $clone_id?>  .block-title,
            <?php echo $this->dmshify() ?> .block-title{
                color: <?php echo $title_color ?>;
                background: url("<?php echo $img_path ?>/title-bg.png") repeat-x scroll 0 18px transparent;;
            }

            .collapser-block<?php echo $clone_id?> .block-title span,
            <?php echo $this->dmshify() ?> .block-title span{
                background: <?php echo $title_back ?>;
            }

            .collapser-block<?php echo $clone_id?> .collapser-heading,
            <?php echo $this->dmshify() ?> .collapser-heading{
                border: 1px solid <?php echo $border ?>;
                background: url("<?php echo $img_path ?>/more.png") 15px no-repeat;
                background-color:  <?php echo $item_back ?>
            }

            .collapser-block<?php echo $clone_id?> .plus-min,
            .collapser-block<?php echo $clone_id?> .plus-min{
                background: url("<?php echo $img_path ?>/more.png") 15px no-repeat;
            }

            .collapser-block<?php echo $clone_id?> .plus-min:hover,
            .collapser-block<?php echo $clone_id?> .plus-min.active,
            <?php echo $this->dmshify() ?> .collapser-heading:hover .plus-min,
            <?php echo $this->dmshify() ?> .collapser-heading.active .plus-min{
                background: url("<?php echo $img_path ?>/less.png") 15px  no-repeat <?php echo $item_back_hover ?>;
            }

            .collapser-block<?php echo $clone_id?> .collapser-heading .collapser-toggle
            <?php echo $this->dmshify() ?> .collapser-heading .collapser-toggle{
                color: <?php echo $item_title_color ?>;
            }
            .collapser-block<?php echo $clone_id?> .collapser-heading .collapser-toggle:hover,
            .collapser-block<?php echo $clone_id?> .collapser-heading.active .collapser-toggle,
            <?php echo $this->dmshify() ?> .collapser-heading .collapser-toggle:hover,
            <?php echo $this->dmshify() ?> .collapser-heading.active .collapser-toggle{
                color: <?php echo $item_title_color_hover ?> !important;
            }

            .collapser-block<?php echo $clone_id?> .collapser-inner p,
            <?php echo $this->dmshify() ?> .collapser-inner p{
                color: <?php echo $content_color ?>;
            }


        </style>

    <?php
    }
    function section_template($clone_id = null)
    {
        global $post, $pagelines_ID;

        //DMS Compatibility
        $clone_id      = function_exists('pl_has_editor') ? $this->get_dms_clone_id( $this->prefix() ) : $clone_id;

        $parent            = "collapser-accordion".$clone_id;
        $current_page_post = $post;
        $oset              = array('post_id' => $pagelines_ID, 'clone_id' => $clone_id);

        $limit             = ( $this->opt('tm_collapser_items', $oset) ) ? $this->opt('tm_collapser_items', $oset) : '5';
        $set               = ( $this->opt('tm_collapser_set', $oset) ) ? $this->opt('tm_collapser_set', $oset) : null;
        $title             = ( $this->opt('tm_collapser_title', $oset) ) ? $this->opt('tm_collapser_title', $oset) : 'Collapser Section';
        $position          = ( $this->opt('tm_collapser_position', $oset) ) ? $this->opt('tm_collapser_position', $oset) : 'left';
        $read_more_text    = ( $this->opt('tm_collapser_read_more_text', $oset ) ) ? $this->opt('tm_collapser_read_more_text', $oset )  : 'Read More';
        $this->posts       = $this->get_posts($this->custom_post_type, $this->tax_id, $set, $limit);
        $show_first        = ! $this->opt( 'tm_collapser_hide_first_tab',$oset );

        if( !count($this->posts  ) ){
            echo setup_section_notify($this, __('Sorry,there are no post to display.', 'collapser'), get_admin_url().'edit.php?post_type='.$this->custom_post_type, __('Please create some posts', 'collapser'));
            return;
        }

        $current = $this->posts[0];
        $inner_oset = array('post_id' => $current->ID);
        $image = $this->find_and_show_image($current->ID, true);
    ?>
        <div class="collapser-block<?php echo $clone_id?>">
            <h3 class="block-title">
                <span data-sync="tm_collapser_title"><?php echo $title ?></span>
            </h3>
            <div class="row" id="<?php echo $parent ?>-wrapper">

                <?php if ($position == 'none'): ?>
                    <div class="span12">
                        <div class="collapser-data" id="<?php echo $parent ?>">
                            <?php echo $this->draw_collapsers($parent, $show_first) ?>
                        </div>
                    </div>
                <?php else: ?>
                    <div class="span6 <?php echo ( $position == 'left' ) ? 'collapser-gallery' : '' ;  ?> ">
                        <?php if ($position == 'left'): ?>
                            <img src="<?php echo $image ?>" class="center">
                        <?php else: ?>
                            <div class="collapser-data" id="<?php echo $parent ?>">
                                <?php echo $this->draw_collapsers($parent, $show_first) ?>
                            </div>
                        <?php endif ?>
                    </div>
                    <div class="span6 <?php echo ( $position == 'right' ) ? 'collapser-gallery' : '' ;  ?>">
                        <?php if ($position == 'right'): ?>
                            <img src="<?php echo $image ?>" class="center">
                        <?php else: ?>
                            <div class="collapser-data" id="<?php echo $parent ?>">
                                <?php echo $this->draw_collapsers($parent, $show_first) ?>
                            </div>
                        <?php endif ?>
                    </div>
                <?php endif ?>

            </div>
        </div>
        <?php $post = $current_page_post; ?>
    <?php
    }

    function draw_collapsers($parent, $show_first)
    {
        global $post;
        $out = "";
        $first = true;
        foreach ($this->posts as $post)
        {
            setup_postdata( $post );
            $inner_oset = array('post_id' => $post->ID);
            $image = $this->find_and_show_image($post->ID, true);
            $link = plmeta('tm_collapser_url', $inner_oset);
            $readmore = plmeta('tm_collapser_read_more_text', $inner_oset);
            $morelink = ( strlen($link) ) ? '<p><a href="'.$link.'">'.$readmore.'</a></p>' : ' ';
            $in = ($first && $show_first) ? 'in' : ' ';
            $active = ($first && $show_first) ? 'active' : '';
            $collapser = '<div class="accordion-group">
                    <div class="collapser-heading '.$active.'" id="'.$parent.'-collapser-'.$post->ID.'">
                      <a class="collapser-toggle" data-toggle="collapse" data-parent="#'.$parent.'" href="#'.$parent.'-'.$post->ID.'" data-image="'.$image.'">
                            <span class="plus-min"></span>
                            <span class="title-head">
                                '.get_the_title().'
                            </span>
                      </a>
                    </div>
                    <div id="'.$parent.'-'.$post->ID.'" class="accordion-body collapse '.$in.'">
                      <div class="collapser-inner">
                        <p>'.apply_filters('the_content' ,$post->post_content).'</p>
                        '.$morelink.'
                      </div>
                    </div>
                  </div>';
            $out .=  $collapser;
            $first = false;
        }
        return $out;
    }

    function meta_boxes( $meta_boxes ){
        $meta_boxes[] = array(
        'title' => 'Collapser Extra Data',
        'pages' => $this->custom_post_type,
        'fields' => array(
            array(
                'id'   => 'tm_collapser_image',
                'name' => __( 'Collapser Post Image', 'collapser'),
                'type' => 'image'
            ),
            array(
                'id'   => 'tm_collapser_url',
                'name' => __( 'Target URL (Optional)', 'collapser'),
                'type' => 'text_url'
            ),
            array(
                'id'   => 'tm_collapser_read_more_text',
                'name' => __( 'Link title (Optional)', 'collapser'),
                'type' => 'text'
            )
        )
    );

    return $meta_boxes;
    }

    function post_meta_setup(){
        $pt_tab_options = array(
            'tm_collapser_image' => array(
                'title'        => __( 'Collapser Post Image', 'collapser'),
                'shortexp'     => __( 'Featured image for the Collapser post', 'collapser'),
                'inputlabel'   => __( 'Select a Image', 'collapser'),
                'type'         => 'image_upload',
                'exp'          => __('This image will be displayed beside the list, the suggested size is up to 520px width & 400px height.', 'collapser'),
            ),
            'tm_collapser_url' => array(
                'title'        => __( 'Target URL (Optional)', 'collapser'),
                'shortexp'     => __( 'You can set a URL for "Read more".', 'collapser'),
                'inputlabel'   => __( 'URL', 'collapser'),
                'type'         => 'text',
                'exp'          => __('', 'collapser'),
            ),
            'tm_collapser_read_more_text' => array(
                'title'        => __( 'Link title (Optional)', 'collapser'),
                'shortexp'     => __( 'Set the Link title', 'collapser'),
                'inputlabel'   => __( 'Link title', 'collapser'),
                'type'         => 'text',
                'exp'          => __('Please type the link title for default the text to show is "Read more" this link will show after the content.', 'collapser'),
            ),

        );

        $pt_panel = array(
                'id'        => 'tm_collapser',
                'name'      => __('Collapser Post  Details','collapser'),
                'posttype'  => array( $this->custom_post_type ),
                'hide_tabs' => false
            );

        $pt_panel =  new PageLinesMetaPanel( $pt_panel );


        $pt_tab = array(
            'id'        => 'tm_collapser_metatab',
            'name'      => __("Please fill the below fields", 'collapser') ,
            'icon'      => $this->icon,
        );

        $pt_panel->register_tab( $pt_tab, $pt_tab_options );
    }


    function post_type_setup(){
        $args = array(
            'label'          => __('Collapser Posts', 'collapser'),
            'singular_label' => __('Post', 'collapser'),
            'description'    => __('', 'collapser'),
            'taxonomies'     => array( $this->tax_id ),
            'menu_icon'      => $this->icon,
            'supports'       => array( 'title', 'editor')
        );
        $taxonomies = array(
            $this->tax_id => array(
                'label'          => __('Collapser Sets', 'collapser'),
                'singular_label' => __('Collapser Set', 'collapser'),
            )
        );
        $columns = array(
            "cb"              => "<input type=\"checkbox\" />",
            "title"           => "Title",
            "collapser_media" => "Media",
            $this->tax_id     => "Collapser Set"
        );
        $this->post_type = new PageLinesPostType( $this->custom_post_type, $args, $taxonomies, $columns, array(&$this, 'column_display') );
    }

    function column_display($column){
        global $post;
        switch ($column){
            case $this->tax_id:
                echo get_the_term_list($post->ID, $this->tax_id, '', ', ','');
                break;
            case 'collapser_media':
                echo $this->find_and_show_image($post->ID);
                break;
        }
    }

    function find_and_show_image($postID, $return_path = false){
        $image = get_post_meta($postID, 'tm_collapser_image', true);
        if( strstr($image, 'http') ){
            $image_url = $image;
        }else{
            $image_url = wp_get_attachment_url( $image );
        }
        return ( !$return_path ) ? '<img src="'.$image_url.'" style="max-width: 300px; max-height: 100px" />' : $image_url;
    }



    function section_optionator( $settings )
    {
        $settings = wp_parse_args($settings, $this->optionator_default);
        $opt_array = array(
            'tm_collapser_title'    => array(
                'type'          => 'text',
                'inputlabel'    => __('Title', 'collapser'),
                'title'         => __('Section Title', 'collapser'),
                'shortexp'      => __('Default: "Latest from the Blog"', 'collapser'),
                'exp'           => __('If set the title will show on the top of the section', 'collapser'),
            ),
            'tm_collapser_set'  => array(
                'type'          => 'select_taxonomy',
                'taxonomy_id'   => $this->tax_id,
                'title'         => __('Select the set to show', 'collapser'),
                'shortexp'      => __('The set to show', 'collapser'),
                'inputlabel'    => __('Select a set', 'collapser'),
                'exp'           => __('Select the set you would like to show on this page. if don\'t select a set the slider will show the last entries under collapser posts', 'collapser')
            ),
            'tm_collapser_items' => array(
                'type'         => 'count_select',
                'inputlabel'   => __('Number of post to show', 'collapser'),
                'title'        => __('Number of post', 'collapser'),
                'shortexp'     => __('Default value is 5', 'collapser'),
                'exp'          => __('The amount of post to show.', 'collapser'),
                'count_start'  => 1,
                'count_number' => 100,
                'default'      => 5
            ),
            'tm_collapser_hide_first_tab' => array(
                'type' => 'check',
                'inputlabel' => __('Start with the first tab closed', 'collapser'),
                'title' => __('First tab closed'),
                'shortexp' => _('Check if you don\'t want that the first tab shows open')
            ),
            'tm_collapser_position' => array(
                'title'         => 'Thumbnail position',
                'type'          => 'select',
                'selectvalues'  => array(
                    'left'  => array('name' => __( 'Left', 'collapser') ),
                    'right' => array('name' => __( 'Right', 'collapser') ),
                    'none'  => array('name' => __( 'Do not use thumbnails'), 'collapser')
                ),
                'inputlabel'    => __( 'Position', 'collapser' ),
                'shortexp'      => 'Default value: Left',
                'exp'           => 'Indicates where the thumbnail images will be displayed. If you want to use a full  width tabs use the "Do not use thumbnails" option'
            ),

            'tm_collapser_section_title_bg' => array(
                'type' => 'colorpicker',
                'inputlabel' => __( 'Title Background', 'collapser' ),
                'title' => __( 'Title Background', 'collapser' ),
                'default' => '#FFFFFF'
            ),

            'tm_collapser_title_color'  => array(
                'inputlabel'    => __( 'Section Title Text', 'collapser' ),
                'type' => 'colorpicker',
                'title' => __( 'Section Title Text', 'collapser' ),
                'default' => '#21759B'
            ),
            'tm_collapser_item_background'  => array(
                'inputlabel'    => __( 'Item highlight', 'collapser' ),
                'type' => 'colorpicker',
                'title' => __( 'Item highlight', 'collapser' ),
                'default' => '#FFFFFF'
            ),
            'tm_collapser_item_background_over' => array(
                'inputlabel'    => __( 'Item highlight hover', 'collapser' ),
                'type' => 'colorpicker',
                'title' => __( 'Item highlight hover', 'collapser' ),
                'default' => '#21759B'
            ),
            'tm_collapser_title_item_color' => array(
                'inputlabel'    => __( 'Item Title Text', 'collapser' ),
                'type' => 'colorpicker',
                'title' => __( 'Item Title Text', 'collapser' ),
                'default' => '#000000'
            ),
            'tm_collapser_title_over_color' => array(
                'inputlabel'    => __( 'Item Title Text Hover', 'collapser' ),
                'type' => 'colorpicker',
                'title' => __( 'Item Title Text Hover', 'collapser' ),
                'default' => '#000000'
            ),
            'tm_collapser_menu_border'  => array(
                'inputlabel'    => __( 'Item Border', 'collapser' ),
                'type' => 'colorpicker',
                'title' => __( 'Item Border', 'collapser' ),
                'default' => '#eaeaea'
            ),
            'tm_collapser_text_color'   => array(
                'inputlabel'    => __( 'Content Text', 'collapser' ),
                'type' => 'colorpicker',
                'title' => __( 'Content Text', 'collapser' ),
                'default' => '#000000'
            )
        );

        $settings = array(
            'id'        => $this->id.'_meta',
            'name'      => $this->name,
            'icon'      => $this->icon,
            'clone_id'  => $settings['clone_id'],
            'active'    => $settings['active']
        );

        register_metatab($settings, $opt_array);
    }

    function get_posts( $custom_post, $tax_id, $set = null, $limit = null){
        $query              = array();
        $query['orderby']   = 'ID';
        $query['post_type'] = $custom_post;
        $query[ $tax_id ]   = $set;

        if(isset($limit)){
            $query['showposts'] = $limit;
        }

        $q = new WP_Query($query);

        if(is_array($q->posts))
            return $q->posts;
        else
            return array();
    }

} /* End of section class - No closing php tag needed */

