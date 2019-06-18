<?php
function sharon_user_roles(WP_User $user){
    // var_dump($_GET);
    ?>
    <h3>User's Organizations</h3>
    <?php
    $args = array(
        'post_type' => 'organization',
        'posts_per_page' => -1,
        'orderby' => 'title',
        'order' => 'ASC',
    );
    // var_dump($user);
    // var_dump(get_user_meta($user->ID));
    $start = '<div class="sharon-user-orgs-container">';
    $the_query = new WP_Query($args);
    if($the_query->have_posts() ){
        while($the_query->have_posts()){
            $the_query->the_post();
            $id = $the_query->post->ID;
            $slug = $the_query->post->post_name;
            // var_dump("<br /><br />");
            // var_dump($slug);
            $title = get_the_title();
            $option = get_user_meta($user->ID, $slug . "_" . $id, true);
            ob_start();
            // echo $option;
            ?>
            <div class='sharon-3-block'>
                <input type='checkbox' value='1' <?php if($option == 1) echo 'checked="checked"'; ?> name='<?php echo $slug . "_" . $id; ?>' id="<?php echo $slug . "_" . $id; ?>">
                <label for='<?php echo $slug . "_" . $id; ?>'><?php echo $title; ?> </label>
            </div>        
            <?php
            $return .= ob_get_clean();
        }
    }
    $return = $start . $return . "</div>";
    echo $return;
    ?>
    <?php
}
add_action( 'show_user_profile', 'sharon_user_roles' );
add_action( 'edit_user_profile', 'sharon_user_roles' );

function sharon_user_fields_save($user_id){
    if (!current_user_can('edit_user', $user_id)) {
        return;
    }
    $args = array(
        'post_type' => 'organization',
        'posts_per_page' => -1,
        'orderby' => 'title',
        'order' => 'ASC',
    );
    $the_query = new WP_Query($args);
    if($the_query->have_posts() ){
        while($the_query->have_posts()){
            $the_query->the_post();
            $id = $the_query->post->ID;
            $slug = $the_query->post->post_name;
            update_user_meta($user_id, $slug . "_" . $id, $_REQUEST[$slug . "_" . $id]);
        }
    }
}
add_action('personal_options_update', 'sharon_user_fields_save');
add_action('edit_user_profile_update', 'sharon_user_fields_save');