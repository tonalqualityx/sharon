<?php
defined('ABSPATH') or die('No script kiddies please!'); //For security

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
    $choice = ['Edit Department/Committee Page', 'Schedule a meeting or event', 'Add Document', 'Add/edit meeting minutes or docs'];
    if(is_user_logged_in()){    
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
            $return = ob_get_clean();
    }else{
        
        $return = "Sorry, you don't have permission to access this content.";
        $return .= wp_login_form(array('echo' => false));
    }
    return $return;
}
add_shortcode( 'ind-complete-management', 'ind_complete_management' );

function ind_org_management_form(){
    if(isset($_GET['id'])){
        $id = $_GET['id'];
    }
    echo do_shortcode('[ind-complete-management]');

    ?>
    <div class='complete-management-form-container'>
    <h1 class='form-title'>Editing - <?php echo get_the_title($id); ?></h1>
    <?php 
    cred_form(4803,$id);
    ?>
    </div>
    <?php
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
    $terms = get_terms( array(
        'taxonomy' => 'document-category',
        'hide_empty' => false,
    ) );
    ob_start();
    echo do_shortcode('[ind-complete-management]');
    ?>
    <div id='get_current_terms' class='hide'>
    <option value='' dissabled selected>Select a category</option>
    <?php
    foreach($terms as $key => $value){
        ?>
        <option value='<?php echo $value->term_id; ?>'><?php echo $value->name; ?></option>
        <?php
    }
    ?>
    </div>
    <?php
    // var_dump($terms);
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

function ind_document_search($atts){
    $atts = shortcode_atts(
        array(
            'num' => 10,
            'board' => null,
        ), $atts, 'ind-document-search'
    );
    $terms = get_terms(
        array(
            'taxonomy' => 'document-category',
            'hide_empty' => false,
        )
    );
    if(isset($_POST['cat-search'])){
        $cat_search = $_POST['cat-search'];
    }
    if(isset($_POST['org-search'])){
        $org_search = $_POST['org-search'];
    }
    if(isset($_POST['start-date-search'])){
        $start_date = $_POST['start-date-search'];
    }
    if(isset($_POST['end-date-search'])){
        $end_date = $_POST['end-date-search'];
        if($end_date == $start_date){
            $end_date = date("Y-m-d", strtotime($end_date. ' + 1 days'));
        }
    }
    if(isset($_POST['keyword-search'])){
        $keyword = $_POST['keyword-search'];
        if($keyword == ''){
            unset($keyword);
        }
    }
    if(isset($_POST['document-search-validation'])){
        $validation = $_POST['document-search-validation'];
    }
    ob_start();
    ?>
    <div class='ind-doc-search-contianer'>
        <form id='ind-doc-search-form' action="#ind-doc-search-form" method="post">
            <div class='search-form-individual'>
                <label for='cat-search'>All Categories</label>
                <select id="cat-search" class="cat-search" name="cat-search" placeholder="">
                    <option value="" dissabled selected>All Categories</option>
        
    <?php
    // var_dump($terms);
    foreach($terms as $key => $value){
        // var_dump($value);
        $name = $value->name;
        $slug = $value->slug;
        $id = $value->term_id;
        $selected = '';
        if($cat_search == $id){
            $selected = ' selected ';
        }
        ?>
        <option value="<?php echo $id; ?>"<?php echo $selected; ?>><?php echo $name; ?></option>
        <?php
    }
    ?>
                </select>
            </div>
            <div class='search-form-individual'>
            <label for='org-search'>All Boards</label>
            <select id='org-search' class='org-search' name='org-search' placeholder=''>
                <option value="" dissabled selected>All Boards</option>
    <?php
    $args = array(
        'post_type' => 'organization',
        'posts_per_page' => -1
    );
    
    $query = new WP_Query($args);
    if($query->have_posts()){
        while($query->have_posts()){
            $query->the_post();
            $title = get_the_title();
            $id = get_the_id();
            $selected = '';
            if($atts['board'] && $validation != 'yes'){
                if($id == $atts['board']){
                    $selected = ' selected ';
                }
            }else if($org_search == $id){
                $selected = ' selected ';
            }
            ?>
            <option value="<?php echo $id; ?>"<?php echo $selected; ?>><?php echo $title; ?></option>
            <?php
        }
    }
    ?>
                </select>
            </div>
            <div class='search-form-individual'>
            <label for='start-date-search'>Start Date</label>
            <input type='date' id='start-date-search' name='start-date-search' value='<?php echo $start_date; ?>'>
            </div>
            <div class='search-form-individual'>
            <label for='end-date-search'>End Date</label>
            <input type='date' id='end-date-search' name='end-date-search' value='<?php echo $end_date; ?>'>
            </div>
            <div class='search-form-individual'>
            <label for='keyword-search'>Keyword</label>
            <input type='text' id='keyword-search' name='keyword-search' value='<?php echo $keyword; ?>'>
            </div>
            <input type='hidden' name='document-search-validation' value='yes'>
            <input type='submit' value='search' id='ind-search-submit' name='submit'>
        </form>
    </div>
    <div class='ind-doc-results-container'>
        <?php
        // var_dump($_POST);
        if(isset($_POST['document-search-validation'])){

        
            // var_dump(get_post_meta(6073, 'wpcf-document-date', true));
            // var_dump(strtotime($start_date));
            $args = array(
                'post_type' => "document",
                'posts_per_page' => -1,
                'paged' => $paged,        
            );
            if($cat_search){
                $args['tax_query'] = array(
                    array(
                        'taxonomy' => 'document-category',
                        'field' => 'term_id',
                        'terms' => $cat_search,
                    ),
                );
            }
            if($org_search){
                $args['toolset_relationships'] = array(
                    'role' => 'child',
                    'related_to' => $org_search,
                    'relationship' => 'organization-document',
                );
            }
            $start = strtotime($start_date);
            $end = strtotime($end_date);
            if($start_date && $end_date){
                $args['meta_query'] = array(
                    array(
                        'key' => 'wpcf-document-date',
                        'value' => array($start, $end),
                        'compare' => "BETWEEN",
                        'type' => 'NUMERIC',
                    ),
                );
            }else if($start_date){
                $args['meta_query'] = array(
                    array(
                        'key' => 'wpcf-document-date',
                        'value' => $start,
                        'compare' => ">=",
                        'type' => 'NUMERIC',
                    ),
                ); 
            }else if($end_date){
                $args['meta_query'] = array(
                    array(
                        'key' => 'wpcf-document-date',
                        'value' => $end,
                        'compare' => "<=",
                        'type' => 'NUMERIC',
                    ),
                );
            }
            if($keyword){
                $search = new WP_Query($args);
                $array_of_ids = array();
                foreach($search->posts as $key => $value){
                    // $array_of_ids[] = $value->ID;
                    $attatch_args = array('post_parent' => $value->ID,
                        'posts_per_page' => -1,
                        'post_type' => 'attachment',    
                    );
                    $attachments = get_children($attatch_args);
                    if($attachments){
                        foreach($attachments as $data => $pdf){
                            array_push($array_of_ids, $pdf->ID);
                        }
                    }
                }
                wp_reset_postdata();
                if(isset($array_of_ids)){
                    $new_args = array(
                        's' => $keyword,
                        'post_type' => 'attachment',
                        'post__in' => $array_of_ids,
                    );
                    $documents = new WP_Query();
                    $documents->parse_query($new_args);
                    relevanssi_do_query($documents);
                }else{
                    $documents = new WP_Query($args);
                }
                // $search_args = array(
                //     's' => $keyword,
                //     'post_type' => 'attachment',
                //     'post__in' => $array_of_ids,
                // );
                // $attachments = new WP_Query();
                // $attachments->parse_query($search_args);
                // relevanssi_do_query($attachments);
                
                
                // var_dump($search->posts);
                
            } else {
                $documents = new WP_Query($args);

            }
            
            ?>
            <div class='document-search-result-container'>
            <?php
            if($documents && $documents->have_posts()){
                $counting_pages = 1;
                while($documents->have_posts()){
                    $documents->the_post();
                    $id = get_the_id();
                    // var_dump(get_post_meta($id));
                    if($counting_pages > 10){
                        $pagination_class = 'hide';
                    }
                    if($keyword){
                        $link = wp_upload_dir()['baseurl'] . '/' . get_post_meta($id, '_wp_attached_file', true);
                    }else{
                        $link = get_post_meta($id, 'wpcf-document-file', true);
                    }
                    ?>
                    <div class='document-search-result-single <?php echo $pagination_class; ?>'>
                        <a href='<?php echo $link; ?>' target="_blank"><?php echo get_the_title(wp_get_post_parent_id($id)); ?></a>
                    </div>
                    <?php
                    $counting_pages++;
                }
                ?>
                <div class="pagination">
                    <?php 
                        $total_count = count($documents->posts);
                        $pagination = ceil($total_count / 10);
                        $page_count = 1;
                        while($pagination >= $page_count){
                            if($page_count == 1){
                                $page_class = 'doc_page_selected';
                            }else{
                                $page_class = '';
                            }
                            ?>
                            <a href='#' class='doc-pagination <?php echo $page_class; ?>' data-num='<?php echo $page_count; ?>' ><?php echo $page_count; ?></a>
                            <?php
                            $page_count++;
                        }
                    ?>
                </div>
                <?php
            }else{
                ?>
                <p>No search results found</p>
                <?php
            }
            ?>
            </div>
            <?php
        }
        ?>
    </div>
    <?php
    wp_reset_postdata();
    return ob_get_clean();
}
add_shortcode('ind-document-search', 'ind_document_search');

function ind_add_minutes(){

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
    <div class='upload-meeting-form-container'>
        <form id='upload-meeting-form-id'>
            <h2>Upload Document Form</h2>
            <!-- <label for='upload-doc-title'>Title: 
                <input id='upload-doc-title' name='upload-doc-title' type='text' class='upload-required'>
            </label>
            <label for='upload-doc-date'>Date: 
                <input id='upload-doc-date' name='upload-doc-date' type='date'>
            </label> -->
            <label for='upload-meeting-category'>Category: 
                <select id='meeting-category' name='meeting-category' class='meeting-cat-dropdown'>
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
            <label for='upload-meeting-file'>Upload a PDF file
                <input type="file" id='upload-meeting-file' class='upload-required' name="my_file_upload[]">
            </label>
            <label for='upload-meeting-orgs'>Organization: 
                <?php
                $start = ob_get_clean();
                if(is_user_logged_in() && (current_user_can('administrator') || $has_orgs)){
                    ob_start();
                    ?>
                    <select id="meeting-organization" name="meeting-organization" placeholder="" class="meeting-org-dropdown" required="">
                        <option value="" dissabled selected>Select Department/Committee</option>
                        <?php
                            foreach($orgs_array as $key => $value){
                                ?>
                                <option value="<?php echo $value; ?>"><?php echo get_the_title($value); ?></option>
                                <?php
                            }
                        ?>
                    </select>
                    <select id="meeting-meeting" name='meeting-meeting' placeholder="" class='meeting-meeting-dropdown' required="">
                            <option value="" dissabled selected>Select an organization</option>
                    </select>
                    
                    <label id='minutes-override-label' class='hide' for='minutes-override'>Check if replacing the current minutes?
                        <input type='checkbox' value='0' name='minutes-override' id="minutes-override">
                    </label>
                    <?php
                    $org = ob_get_clean();
                }
                ob_start();
                ?>
                <br />
            </label>
            <a id='meeting-form-save' class='indsha-button' href='#'>Save</a>
        </form>
    </div>
    <?php
    $end = ob_get_clean();
    return $start . $org . $end;
}
add_shortcode( 'ind-add-minutes', 'ind_add_minutes');

function indsh_custom_menu(){
    ob_start();
        wp_nav_menu(array(
            'theme_location' => 'primary',
            'menu_class'     => 'primary-menu',
            'walker'         => new ind_Walker(),
        ));
    
    return ob_get_clean();
}

add_shortcode( 'ind-menu', 'indsh_custom_menu');


function fix_post_titles(){
    $args = array(
        'post_type' => 'document',
        'posts_per_page' => -1,
    );
    $query = new WP_Query($args);
    if($query->have_posts()){
        while($query->have_posts()){
            $query->the_post();
            $title = get_the_title();
            
            preg_match('/[a-zA-Z]+[.,-]? \d+[.,-]? \d{4}/', $title, $match);
            // var_dump($match);
            $new_date = str_replace(str_split('\.,-'), "", $match[0]);
            $explode = explode(" ", $new_date);
            $new_date = $explode[0] . " " . $explode[1] . ", " . $explode[2];
            $new_date = strtotime($new_date);
            update_post_meta(get_the_id(), 'wpcf-document-date', $new_date);
            $new_title = str_replace($match[0], "", $title);
            if(count($new_date) > 5){
                break;
            }
        }
    }
}
add_shortcode('ind-fix-post-titles', 'fix_post_titles');

function ind_page_title(){
    ob_start();
    ?>
    <h1 class='ind-page-title'><?php echo get_the_title(); ?></h1>
    <?php
    return ob_get_clean();
}
add_shortcode('ind-page-title', 'ind_page_title');

function ind_notifications(){
    $notification_array = ind_display_notice(true);
    // var_dump($notification_array[0]['title']);
    if(isset($notification_array[0])){
        ob_start();
        foreach($notification_array as $key => $value){
            $title = $value['title'];
            $content = $value['content'];

            ?>
            <a href="#" class='notice-button' data-content='<?php echo htmlspecialchars($content, ENT_QUOTES); ?>'><?php echo $title; ?></a>
            <?php
        }
        $return = ob_get_clean();
        return $return;
    }else{
        $return = "<p>There are currently no notices.</p>";
        return $return;
    }
}
add_shortcode('ind-notifications', 'ind_notifications');

function ind_header_hero(){
    return org_header_hero();
}
add_shortcode('ind-header-hero', 'ind_header_hero');

function ind_upcoming_events(){
    $args = array(
        'post_type' => 'event',
        'posts_per_page' => '5',
        'meta_key' => 'wpcf-event-date',
        'orderby' => 'meta_value_num',
        'order' => 'DESC',
    );
    ob_start();
    $events = new WP_Query($args);
    if($events->have_posts()){
        while($events->have_posts()){
            $events->the_post();
            $link = get_the_permalink();
            ?>
            <p class='upcoming-events-list'><a href='<?php echo $link; ?>'><?php echo get_the_title(); ?></a></p>
            <?php
        }
    }
    wp_reset_postdata();
    $return = ob_get_clean();
    return $return;
}
add_shortcode('ind-upcoming-events', 'ind_upcoming_events');

function ind_bid_opps(){
    $today = strtotime('now');
    $args = array(
        'posts_per_page' => -1,
        'post_type' => 'bid',
        'meta_query' => array(
            array(
                'key' => 'wpcf-date',
                'value' => $today,
                'compare' => '>=',
                'type' => 'NUMERIC',
            ),
        ),
    );
    ob_start();
    $bids = new WP_Query($args);
    if($bids->have_posts()){
        while($bids->have_posts()){
            $bids->the_post();
            $link = get_the_permalink();
            $content = get_the_content();
            $title = get_the_title();
            $page = "<h2 class='margin-top-0'>" . $title . "</h2><p>" . $content . "</p>";
            ?>
            <p class='upcoming-events-list'>
                <div>
                    <div class='ind-bid-content hide'><?php echo $page; ?></div>
                    <a class='ind-make-modal' data-class='ind-bid-content' href='#'><?php echo get_the_title(); ?></a>
                </div>
            </p>
            <?php
        }
    }else{
        ?>
        <p>Nothing Scheduled at the moment please check back soon.</p>
        <?php
    }
    wp_reset_postdata();
    $return = ob_get_clean();
    return $return;
}
add_shortcode('ind-bid-opps', 'ind_bid_opps');

function copy_date(){
    return date('Y');
}
add_shortcode('copy-date', 'copy_date');

function meeting_schedule($atts){
    $atts = shortcode_atts(
        array(
            'days' => 2,
        ), $atts, 'meeting-schedule'
    );
    $days = $atts['days'] * 86400;
    $today = strtotime('now') - $days;
    $args = array(
        'posts_per_page' => -1,
        'post_type' => 'event',
        'meta_query' => array(
            array(
                'key' => 'wpcf-event-date',
                'value' => $today,
                'compare' => '>=',
                'type' => 'NUMERIC',
            ),
        ),
    );
    $query  = new WP_Query($args);
    ob_start();
    ?>
    <div class='ind-meeting-schedule-container'>
    <?php
    if($query->have_posts()){
        while($query->have_posts()){
            $query->the_post();
            $title = get_the_title();
            // $id = get_the_ID();
            // $date = get_post_meta($id, 'wpcf-event-date', true);
            // $time = get_post_meta($id, 'wpcf-time', true);
            $link = get_the_permalink();
            ?>
            <a href='<?php echo $link; ?>'><?php echo $title; ?></a>
            <?php
        }
    }else{
        ?>
        <div class='ind-meeting-schedule-empty'>Nothing scheduled at the moment please check back soon.</div>
        <?php
    }
    ?>
    </div>
    <?php
    return ob_get_clean();
}
add_shortcode('meeting-schedule', 'meeting_schedule');

function ind_local_events(){
    $today = strtotime('now');
    $args = array(
        'posts_per_page' => -1,
        'post_type' => 'local-event',
        'meta_query' => array(
            array(
                'key' => 'wpcf-date',
                'value' => $today,
                'compare' => '>=',
                'type' => 'NUMERIC',
            ),
        ),
    );
    $query  = new WP_Query($args);
    ob_start();
    ?>
    <div class='ind-local-event-container'>
    <?php
    if($query->have_posts()){
        while($query->have_posts()){
            $query->the_post();
            $title = get_the_title();
            $content = get_the_content();
            ?>
            <a class='ind-local-event' data-title='<?php echo $title; ?>' data-content='<?php echo $content; ?>' href='#'><?php echo $title; ?></a>
            <?php
        }
    }else{
        ?>
        <div class='ind-local-event-empty'>Nothing scheduled at the moment please check back soon.</div>
        <?php
    }
    ?>
    </div>
    <?php
    return ob_get_clean();
}
add_shortcode('local-events', 'ind_local_events');
