<?php
/*
Section: Collapser
Author: Enrique Chávez
Author URI: http://tmeister.net
Version: 1.2.3
Description: Collapser is a simple but handy section that provides a way to show small pieces of information using an accordion-nav type with a feature image on a side to stand out the content. With more that 15 options to play with.
Class Name: CollapserTm
Cloning: true
Workswith: templates, main
External: http://tmeister.net/themes-and-sections/collapser/
Demo: http://pagelines.tmeister.net/collapser/
*/

/*
 * PageLines Headers API
 *
 *  Sections support standard WP file headers (http://codex.wordpress.org/File_Header) with these additions:
 *  -----------------------------------
 *   - Section: The name of your section.
 *   - Class Name: Name of the section class goes here, has to match the class extending PageLinesSection.
 *   - Cloning: (bool) Enable cloning features.
 *   - Depends: If your section needs another section loaded first set its classname here.
 *   - Workswith: Comma seperated list of template areas the section is allowed in.
 *   - Failswith: Comma seperated list of template areas the section is NOT allowed in.
 *   - Demo: Use this to point to a demo for this product.
 *   - External: Use this to point to an external overview of the product
 *   - Long: Add a full description, used on the actual store page on http://www.pagelines.com/store/
 *
 */

class CollapserTm extends PageLinesSection
{

    var $domain           = 'tm_collapser';
    var $tax_id           = 'tm_collapser_sets';
    var $custom_post_type = 'tm_collapser_post';

    function section_persistent()
    {
        $this->post_type_setup();
        $this->post_meta_setup();
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
        $clone_id      = function_exists('pl_has_editor') ? $this->get_dms_clone_id( $this->prefix() ) : $clone_id;

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
        $title_color            = $this->opt('tm_collapser_title_color',$oset) ? pl_hashify($this->opt('tm_collapser_title_color',$oset)) : pl_hashify( pl_link_color() );
        $item_back              = $this->opt('tm_collapser_item_background',$oset) ? pl_hashify($this->opt('tm_collapser_item_background',$oset)) : '#fff';
        $item_back_hover        = $this->opt('tm_collapser_item_background_over',$oset) ? pl_hashify($this->opt('tm_collapser_item_background_over',$oset)) : pl_hashify( pl_link_color() );
        $item_title_color       = $this->opt('tm_collapser_title_item_color',$oset) ? pl_hashify($this->opt('tm_collapser_title_item_color',$oset)) : pl_hashify( pl_text_color() );
        $item_title_color_hover = $this->opt('tm_collapser_title_over_color',$oset) ? pl_hashify($this->opt('tm_collapser_title_over_color',$oset)) : "fff000";//pl_hashify( pl_text_color() );
        $border                 = $this->opt('tm_collapser_menu_border',$oset) ? pl_hashify($this->opt('tm_collapser_menu_border',$oset)) : '#eaeaea';
        $content_color          = $this->opt('tm_collapser_text_color',$oset) ? pl_hashify($this->opt('tm_collapser_text_color',$oset)) : pl_hashify( pl_text_color() );


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
            }

            .collapser-block<?php echo $clone_id?> .block-title span,
            <?php echo $this->dmshify() ?> .block-title span{
                background: <?php echo $title_back ?>;
            }

            .collapser-block<?php echo $clone_id?> .collapser-heading,
            <?php echo $this->dmshify() ?> .collapser-heading{
                border: 1px solid <?php echo $border ?>;
                background-color: <?php echo $item_back ?> !important;
            }

            .collapser-block<?php echo $clone_id?> .collapser-heading:hover,
            .collapser-block<?php echo $clone_id?> .collapser-heading.active,
            <?php echo $this->dmshify() ?> .collapser-heading:hover,
            <?php echo $this->dmshify() ?> .collapser-heading.active{
                background-color: <?php echo $item_back_hover ?> !important;
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
    function section_template( $clone_id = null )
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
            echo setup_section_notify($this, __('Sorry,there are no post to display.', $this->domain), get_admin_url().'edit.php?post_type='.$this->custom_post_type, __('Please create some posts', $this->domain));
            return;
        }

        $current = $this->posts[0];
        $inner_oset = array('post_id' => $current->ID);
        $image = plmeta('tm_collapser_image', $inner_oset);
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
            $image = plmeta('tm_collapser_image', $inner_oset);
            $link = plmeta('tm_collapser_url', $inner_oset);
            $readmore = plmeta('tm_collapser_read_more_text', $inner_oset);
            $morelink = ( strlen($link) ) ? '<p><a href="'.$link.'">'.$readmore.'</a></p>' : ' ';
            $in = ($first && $show_first) ? 'in' : ' ';
            $active = ($first && $show_first) ? 'active' : '';
            $collapser = '<div class="accordion-group">
                    <div class="collapser-heading '.$active.'" id="'.$parent.'-collapser-'.$post->ID.'">
                      <a class="collapser-toggle" data-toggle="collapse" data-parent="#'.$parent.'" href="#'.$parent.'-'.$post->ID.'" data-image="'.$image.'">
                        '.get_the_title().'
                      </a>
                    </div>
                    <div id="'.$parent.'-'.$post->ID.'" class="accordion-body collapse '.$in.'">
                      <div class="collapser-inner">
                        <p>'.nl2br($post->post_content).'</p>
                        '.$morelink.'
                      </div>
                    </div>
                  </div>';
            $out .=  $collapser;
            $first = false;
        }
        return $out;
    }

    function post_meta_setup(){
        $pt_tab_options = array(
            'tm_collapser_image' => array(
                'title'        => __( 'Collapser Post Image', $this->domain),
                'shortexp'     => __( 'Featured image for the Collapser post', $this->domain),
                'inputlabel'   => __( 'Select a Image', $this->domain),
                'type'         => 'image_upload',
                'exp'          => __('This image will be displayed beside the list, the suggested size is up to 520px width & 400px height.', $this->domain),
            ),
            'tm_collapser_url' => array(
                'title'        => __( 'Target URL (Optional)', $this->domain),
                'shortexp'     => __( 'You can set a URL for "Read more".', $this->domain),
                'inputlabel'   => __( 'URL', $this->domain),
                'type'         => 'text',
                'exp'          => __('', $this->domain),
            ),
            'tm_collapser_read_more_text' => array(
                'title'        => __( 'Link title (Optional)', $this->domain),
                'shortexp'     => __( 'Set the Link title', $this->domain),
                'inputlabel'   => __( 'Link title', $this->domain),
                'type'         => 'text',
                'exp'          => __('Please type the link title for default the text to show is "Read more" this link will show after the content.', $this->domain),
            ),

        );

        $pt_panel = array(
                'id'        => 'tm_collapser',
                'name'      => __('Collapser Post  Details',$this->domain),
                'posttype'  => array( $this->custom_post_type ),
                'hide_tabs' => false
            );

        $pt_panel =  new PageLinesMetaPanel( $pt_panel );


        $pt_tab = array(
            'id'        => 'tm_collapser_metatab',
            'name'      => __("Please fill the below fields", $this->domain) ,
            'icon'      => $this->icon,
        );

        $pt_panel->register_tab( $pt_tab, $pt_tab_options );
    }


    function post_type_setup(){
        $args = array(
            'label'          => __('Collapser Posts', $this->domain),
            'singular_label' => __('Post', $this->domain),
            'description'    => __('', $this->domain),
            'taxonomies'     => array( $this->tax_id ),
            'menu_icon'      => $this->icon,
            'supports'       => array( 'title', 'editor')
        );
        $taxonomies = array(
            $this->tax_id => array(
                'label'          => __('Collapser Sets', $this->domain),
                'singular_label' => __('Collapser Set', $this->domain),
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
                echo '<img src="'.m_pagelines('tm_collapser_image', $post->ID).'" style="max-width: 300px; max-height: 100px" />';
                break;
        }
    }

    function section_optionator( $settings )
    {
        $settings = wp_parse_args($settings, $this->optionator_default);
        $opt_array = array(
            'tm_collapser_title'    => array(
                'type'          => 'text',
                'inputlabel'    => __('Title', $this->domain),
                'title'         => __('Section Title', $this->domain),
                'shortexp'      => __('Default: "Latest from the Blog"', $this->domain),
                'exp'           => __('If set the title will show on the top of the section', $this->domain),
            ),
            'tm_collapser_set'  => array(
                'type'          => 'select_taxonomy',
                'taxonomy_id'   => $this->tax_id,
                'title'         => __('Select the set to show', $this->domain),
                'shortexp'      => __('The set to show', $this->domain),
                'inputlabel'    => __('Select a set', $this->domain),
                'exp'           => __('Select the set you would like to show on this page. if don\'t select a set the slider will show the last entries under collapser posts', $this->domain)
            ),
            'tm_collapser_items' => array(
                'type'          => 'count_select',
                'inputlabel'    => __('Number of post to show', $this->domain),
                'title'         => __('Number of post', $this->domain),
                'shortexp'      => __('Default value is 5', $this->domain),
                'exp'           => __('The amount of post to show.', $this->domain),
                'count_start'   => 1,
                'count_number'  => 100,
            ),
            'tm_collapser_hide_first_tab' => array(
                'type' => 'check',
                'inputlabel' => __('Start with the first tab closed', $this->domain),
                'title' => __('First tab closed'),
                'shortexp' => _('Check if you don\'t want that the first tab shows open')
            ),
            'tm_collapser_position' => array(
                'title'         => 'Thumbnail position',
                'type'          => 'select',
                'selectvalues'  => array(
                    'left'  => array('name' => __( 'Left', $this->domain) ),
                    'right' => array('name' => __( 'Right', $this->domain) ),
                    'none'  => array('name' => __( 'Do not use thumbnails'), $this->domain)
                ),
                'inputlabel'    => __( 'Position', $this->domain ),
                'shortexp'      => 'Default value: Left',
                'exp'           => 'Indicates where the thumbnail images will be displayed. If you want to use a full  width tabs use the "Do not use thumbnails" option'
            ),

            'tm_collapser_section_title_bg' => array(
                'type' => 'colorpicker',
                'inputlabel' => __( 'Title Background', $this->domain ),
                'title' => __( 'Title Background', $this->domain ),
                'default' => '#F00000'
            ),

            'tm_collapser_title_color'  => array(
                'inputlabel'    => __( 'Section Title Text', $this->domain ),
                'type' => 'colorpicker',
                'title' => __( 'Section Title Text', $this->domain )
            ),
            'tm_collapser_item_background'  => array(
                'inputlabel'    => __( 'Item highlight', $this->domain ),
                'type' => 'colorpicker',
                'title' => __( 'Item highlight', $this->domain ),
            ),
            'tm_collapser_item_background_over' => array(
                'inputlabel'    => __( 'Item highlight hover', $this->domain ),
                'type' => 'colorpicker',
                'title' => __( 'Item highlight hover', $this->domain ),
            ),
            'tm_collapser_title_item_color' => array(
                'inputlabel'    => __( 'Item Title Text', $this->domain ),
                'type' => 'colorpicker',
                'title' => __( 'Item Title Text', $this->domain ),
            ),
            'tm_collapser_title_over_color' => array(
                'inputlabel'    => __( 'Item Title Text Hover', $this->domain ),
                'type' => 'colorpicker',
                'title' => __( 'Item Title Text Hover', $this->domain ),
            ),
            'tm_collapser_menu_border'  => array(
                'inputlabel'    => __( 'Item Border', $this->domain ),
                'type' => 'colorpicker',
                'title' => __( 'Item Border', $this->domain ),
            ),
            'tm_collapser_text_color'   => array(
                'inputlabel'    => __( 'Content Text', $this->domain ),
                'type' => 'colorpicker',
                'title' => __( 'Content Text', $this->domain ),
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
        $query                 = array();
        $query['orderby']      = 'ID';
        $query['post_type']    = $custom_post;
        $query[ $tax_id ] = $set;

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
