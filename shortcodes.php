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
                <option value="" dissabled selected>Select Department/Committee</option>
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
        $return = "Sorry, you don't have permission to access this content.";
        $return .= wp_login_form();
    }
}
add_shortcode( 'ind-organization-management', 'ind_organization_management' );

function ind_complete_management(){
    $has_orgs = false;
    $choice = ['Edit Department/Committee Page', 'Schedule a meeting or event', 'Add Document / minutes'];
    if(is_user_logged_in() && (current_user_can('administrator') || $has_orgs)){
        // foreach($choice as $key => $value){
            
            ob_start();
            ?>
            <input type='hidden' id='meeting-creation-url' data-url='<?php echo home_url() . "/complete-management/add-meeting-and-agenda"; ?>'>
            <select id="complete-dropdown" name="complete" placeholder="" class="complete-dropdown" required="">
                <option value="" dissabled selected>What do you want to do?</option>
                <?php
                    foreach($choice as $key => $value){
                        ?>
                        <option value="<?php echo $key; ?>"><?php echo $value; ?></option>
                        <?php
                    }
                ?>
            </select>
            <a id='complete-form-go' data-url='<?php echo home_url(); ?>/complete-management-form?id=' href='#'>Go</a>
            <div class='complete-management-form-container'></div>
            <?php
            $form = ob_get_clean();
            echo $form;
        // }

    }else{
        $return = "Sorry, you don't have permission to access this content.";
        $return .= wp_login_form();
    }
}
add_shortcode( 'ind-complete-management', 'ind_complete_management' );

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

function ind_add_document(){
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
    $categories = get_terms([
        'taxonomy' => 'document-category',
        'hide_empty' => false,
    ]);
    // var_dump($categories);
    
    ob_start();
    ?>
    <div class='upload-doc-form-container'>
        <form id='upload-doc-form-id'>
            <h2>Upload Document Form</h2>
            <label for='upload-doc-title'>Title: 
                <input id='upload-doc-title' name='upload-doc-title' type='text' class='upload-required'>
            </label>
            <label for='upload-doc-date'>Date: 
                <input id='upload-doc-date' name='upload-doc-date' type='date'>
            </label>
            <label for='upload-doc-category'>Category: 
                <select id='doc-category' name='doc-category' class='doc-cat-dropdown'>
                    <option value='' dissabled selected>Select a Category</option>
                    <?php foreach($categories as $key => $value){
                        $id = $value->term_id;
                        $name = $value->name;
                        ?>
                        <option value='<?php echo $id; ?>'><?php echo $name; ?></option>
                        <?php
                    }
                    ?>
                </select>
            </label>
            <label for='upload-doc-file'>Upload a PDF file
                <input type="file" id='upload-doc-file' class='upload-required' name="my_file_upload[]">
            </label>
            <label for='upload-doc-orgs'>Organization: 
                <?php
                $start = ob_get_clean();
                if(is_user_logged_in() && (current_user_can('administrator') || $has_orgs)){
                    ob_start();
                    ?>
                    <select id="doc-organization" name="doc-organization" placeholder="" class="doc-org-dropdown" required="">
                        <option value="" dissabled selected>Select Department/Committee</option>
                        <?php
                            foreach($orgs_array as $key => $value){
                                ?>
                                <option value="<?php echo $value; ?>"><?php echo get_the_title($value); ?></option>
                                <?php
                            }
                        ?>
                    </select>
                    <?php
                    $org = ob_get_clean();
                }
                ob_start();
                ?>
                <br />
            </label>
            <a id='doc-form-save' class='indsha-button' href='#'>Save</a>
        </form>
    </div>
    <?php
    $end = ob_get_clean();
    return $start . $org . $end;
}
add_shortcode( 'ind-add-document', 'ind_add_document');

function ind_add_event(){
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
    ob_start();
    ?>
    <div class='event-doc-form-container'>
        <form id='event-doc-form-id'>
            <h2>Meeting and Agenda creation form</h2>
            <label for='event-doc-orgs'>
                <?php
                $start = ob_get_clean();
                if(is_user_logged_in() && (current_user_can('administrator') || $has_orgs)){
                    ob_start();
                    ?>
                    <select id="doc-organization" class="meeting-required" name="doc-organization" placeholder="" class="doc-org-dropdown">
                        <option value="" dissabled selected>Select Department/Committee</option>
                        <?php
                            foreach($orgs_array as $key => $value){
                                ?>
                                <option value="<?php echo $value; ?>"><?php echo get_the_title($value); ?></option>
                                <?php
                            }
                        ?>
                    </select>
                    <?php
                    $org = ob_get_clean();
                }
                ob_start();
                ?>
                <br />
            </label>
            <label for='event-doc-date'>Date / Time: 
                <input type='datetime-local' class="meeting-required" id='event-doc-date' name='event-doc-date'>
            </label>
            <label for='event-doc-content'>Content: 
                <!-- <textarea id='event-doc-content'></textarea> -->
                <?php echo wp_editor("", 'event-doc-content'); ?>
            </label>
            <label for='event-doc-special'>Is this a special event?
                <input type='radio' id='event-doc-special' name='event-doc-special' value='yes'>Yes
                <input type='radio' value='no' checked name='event-doc-special'>No
            </label>
            <label for='event-doc-agenda'>Agenda
                <input type="file" id='event-doc-agenda' class='event-doc-agenda' name="my_file_upload[]">
            </label>
            <label>Other Documents</label>
            <div class='event-doc-container'>
            </div>
            <a href="#" class='event-add-doc-btn'>Add documents</a>
            <br />
            <a id='event-form-save' class='indsha-button' href='#'>Save</a>
        </form>
    </div>
    <?php
    $end = ob_get_clean();
    return $start . $org . $end;
}
add_shortcode( 'ind-add-event', 'ind_add_event');