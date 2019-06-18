<?php
function ind_organization_management(){
    $has_orgs = false;
    $orgs_array = [];
    $args = array(
        'post_type' => 'organization',
        'posts_per_page' => -1,
        'orderby' => 'title',
        'order' => 'ASC',
    );
    $the_query = new WP_Query($args);
    $user_id = get_current_user_id();
    if($the_query->have_posts() ){
        while($the_query->have_posts()){
            $the_query->the_post();
            $id = $the_query->post->ID;
            $slug = $the_query->post->post_name;
            $option = get_user_meta($user_id, $slug . "_" . $id, true);
            if($option || current_user_can('administrator')){
                $has_orgs = true;
                array_push($orgs_array, $id);
            }
        }
    }
    if(is_user_logged_in() && (current_user_can('administrator') || $has_orgs)){
        // foreach($orgs_array as $key => $value){
            ob_start();
            ?>
            <select id="organization" name="organization" placeholder="" class="org-dropdown" required="">
                <option value="" dissabled selected>Select an Organization</option>
                <?php
                    foreach($orgs_array as $key => $value){
                        ?>
                        <option value="<?php echo $value; ?>"><?php echo get_the_title($value); ?></option>
                        <?php
                    }
                ?>
            </select>
            <a id='org-form-go' data-url='<?php echo home_url(); ?>/organization-management-form?id=' href='#'>Go</a>
            <div class='org-management-form-container'></div>
            <?php
            $form = ob_get_clean();
            echo $form;
        // }

    }else{
        return 'You do not have access to any organizaitons.';
    }
}
add_shortcode( 'ind-organization-management', 'ind_organization_management' );

function ind_org_management_form(){
    if(isset($_GET['id'])){
        $id = $_GET['id'];
    }
    ?>
    <h1 class='form-title'>Editing - <?php echo get_the_title($id); ?></h1>
    <?php 
    cred_form(4803,$id);
}
add_shortcode( 'ind-org-management-form', 'ind_org_management_form' );