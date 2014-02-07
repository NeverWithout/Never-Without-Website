<?php

ini_set('display_errors', 1);

/**
* This is the main class which controls just about everything for the staff management plugin.
*
* Most likely, you will not need to do any modify or editing of this file.
*/
class staff_mgt {
    
    public function __construct()
    {
        // check if the staff option exists (should only happen when we first activate)
        $has_option = get_option('staff_mgt_staff');
        
        if ($has_option === false) { add_option( 'staff_mgt_staff', array(), '', 'no' ); }
    }
    
    public function action() { return (!empty($_REQUEST['action'])) ? ($_REQUEST['action']) : (''); }
    
    public function Main() {
    	
    	ob_start();
    	
    	print h2('EZ Staff List');
        
        print p('Thank you for using the Staff Management Wordpress Plugin. This plugin was designed for those individuals that have a hard time 
        		formatting content in a WYSIWYG. Or perhaps you do know how, but you don\'t like spending the time doing it.
        		Select an option below to begin.');
        
        print p('When you are ready to display your content, simply use this shortcode in your content page: ' . strong('[staff_list]'));
        
		
        foreach(staff_mgt::menu_options() as $option)
        {
        	print anchor($_SERVER['SCRIPT_NAME'] . '?page=' . $option['menu_slug'], $option['label'], array('class'=>'add-new-h2'));
        }
        
        $content = ob_get_clean();
        staff_mgt::render_page($content);
    }
    
    /**
    * Configure The menu
    */
    public function ConfigureMenu() {
        add_menu_page('Staff Management', 'Staff Mgt', 'manage_options', 'ez-staff-list', array('staff_mgt','Main'));
        
        foreach(staff_mgt::menu_options() as $option)
        {
        	add_submenu_page( 'ez-staff-list', $option['page_title'], $option['label'], $option['capability'], $option['menu_slug'], $option['function'] );
        }
    }
    
    
    /**
    * This method is here b/c it's used in more then one place
    * @see staff_mgt::ConfigureMenu()
    * @see staff_mgt::Main()
    *
    * @return array Returns an array of menu options
    */
    private function menu_options() {
    	$options = array(
    		array(
    			'page_title'=>'Add New Staff Member',
    			'label'=>'Add New',
    			'capability'=>'manage_options',
    			'menu_slug'=>'edit_staff_member.php',
    			'function'=>array('staff_mgt','edit_member')
    		),
    		
    		array(
    			'page_title'=>'Staff Members',
    			'label'=>'Staff Members',
    			'capability'=>'manage_options',
    			'menu_slug'=>'staff_members.php',
    			'function'=>array('staff_mgt','manage_staff_members')
    		),
    		
    		array(
    			'page_title'=>'Layout Options',
    			'label'=>'Layout Options',
    			'capability'=>'manage_options',
    			'menu_slug'=>'ez_staff_layout_selection.php',
    			'function'=>array('staff_mgt','layout_selection')
    		)
    	);
    	
    	return $options;
    }
    
    
    /**
    * This method will return a form to add a new user
    *
    * @return string Returns HTML form to add a new user
    */
    public function edit_member()
    {
        switch(staff_mgt::action())
        {
            case 'update-staff-member':
                foreach($_POST as $key => $val) { $_POST[$key] = stripslashes($val); }
                
                $staff_members = staff_mgt::get_staff_members();
                
                // Add a New staff member
                if (empty($_POST['staff_id']))
                {    
                    $next_key = count($staff_members) + 1;
                    $staff_members[$next_key] = array();
                    
                    $staff_members[$next_key]['staff_first_name'] = $_POST['staff_first_name'];
                    $staff_members[$next_key]['staff_last_name'] = $_POST['staff_last_name'];
                    $staff_members[$next_key]['staff_title'] = $_POST['staff_title'];
                    $staff_members[$next_key]['staff_about'] = stripslashes(str_replace('\\', '', $_POST['staff_about']));
                    $staff_members[$next_key]['staff_hours'] = $_POST['staff_hours'];
                    $staff_members[$next_key]['staff_photo'] = $_POST['staff_photo'];
                    $staff_members[$next_key]['staff_id'] = time();
                    
                	$message = 'Staff Member Added!';
                }
                else
                {
                    foreach($staff_members as $key => $staff_member)
                    {
                        if ($staff_member['staff_id'] == $_POST['staff_id'])
                        {
                            $staff_members[$key]['staff_first_name'] = $_POST['staff_first_name'];
                            $staff_members[$key]['staff_last_name'] = $_POST['staff_last_name'];
                            $staff_members[$key]['staff_title'] = $_POST['staff_title'];
                            $staff_members[$key]['staff_about'] = stripslashes(str_replace('\\', '', $_POST['staff_about']));
                            $staff_members[$key]['staff_hours'] = $_POST['staff_hours'];
                            $staff_members[$key]['staff_photo'] = $_POST['staff_photo'];
                        }
                    }
                
                	$message = 'Staff Member Updated!';
                }
                
                $staff_members = serialize($staff_members);
                update_option('staff_mgt_staff', $staff_members);
				
				$content .= gen_content('div', $message, array('class'=>'notice'));
                
                ob_start();
                staff_mgt::manage_staff_members();
                $content .= ob_get_clean();
                break;
               
            case 'update-staff-order':
				$staff_members = staff_mgt::get_staff_members();
				foreach($staff_members as $key => $staff_member)
				{
					foreach($_POST['display_order'] as $staff_id => $display_order)
					{
						if ($staff_member['staff_id'] == $staff_id)
						{
							$staff_members[$key]['display_order'] = $display_order;
						}
					}
				}
                
                // Update the orders...
                $staff_members = serialize($staff_members);
                update_option('staff_mgt_staff', $staff_members);
                
                // define how we want to order by...
                update_option('staff_mgt_order_by', $_POST['staff_mgt_order_by']);
                
                // define the order by direction...
                update_option('staff_mgt_order_by_direction', $_POST['staff_mgt_order_by_direction']);
                
                $content = div('Order Updated!', array('class'=>'notice'));
                ob_start();
                staff_mgt::manage_staff_members();
                $content .= ob_get_clean();
            	break;
                
            default:
                ob_start();
                
                $label = (!empty($_GET['staff_id'])) ? ('Update') : ('Add');
				print h2($label . ' Staff Member');
				
                $staff_first_name = '';
                $staff_last_name = '';
                $staff_title = '';
                $staff_about = '';
                $staff_hours = '';
                $staff_photo = '';
                
                $staff_id = (!empty($_GET['staff_id'])) ? ($_GET['staff_id']) : ('');
                
                
                if (!empty($staff_id) && is_numeric($staff_id))
                {
                    $staff_member = staff_mgt::get_staff_members($staff_id);
                    extract($staff_member);
                }
                
                $form = new form('', $_SERVER['SCRIPT_NAME'] . '?page=edit_staff_member.php');
                $form->set_button($label);
                $form->set_columns(1);
                $form->set_attribute('class', 'staff_form ez_staff_form');
                
                $form->add_hidden(new hidden('staff_id', $staff_id));
                $form->add_hidden(new hidden('action', 'update-staff-member'));
                
                $form->add_label('First Name');
                $form->add_element(new textbox('staff_first_name', $staff_first_name));
                
                $form->add_label('Last Name');
                $form->add_element(new textbox('staff_last_name', $staff_last_name));
                
                $form->add_label('Title');
                $form->add_element(new textbox('staff_title', $staff_title));
                
                $form->add_label('About');
                $form->add_element(new textarea('staff_about', $staff_about, 70, 15));
                
                $form->add_label('Hours');
                $form->add_element('Leave blank to exclude hours', 1, array('class'=>'xsmall'));
                $form->add_element(new textbox('staff_hours', $staff_hours, 75));
                
                $form->add_label('Staff Photo URL');
                $form->add_element(new textbox('staff_photo', $staff_photo, 75));
                
                $form->render();
                $content = ob_get_clean();   
                break;
        }
        
        staff_mgt::render_page($content);
    }
    
    
    /**
    * This method will return a recordset list of current staff members
    *
    * @return string Returns HTML table of staff members
    */
    public function manage_staff_members()
    {
    	ob_start();
    	print h2('Manage Staff Members');
    
        switch(staff_mgt::action())
        {       
            default:
            	$form = new form('', $_SERVER['SCRIPT_NAME'] . '?page=edit_staff_member.php');
            	$form->set_button('Update Order');
            	
            	$form->add_hidden(new hidden('action', 'update-staff-order'));
            	
                $staff_members = staff_mgt::get_staff_members();
                
                staff_mgt::array_sort_by_column($staff_members, get_option('staff_mgt_order_by'), get_option('staff_mgt_order_by_direction'));
                
                if (is_array($staff_members))
                {
                    foreach($staff_members as $key => $staff_member)
                    {
                    	$display_order = (!empty($staff_member['display_order'])) ? ($staff_member['display_order']) : ($key);
                    	$textbox = new textbox('display_order[' . $staff_member['staff_id'] . ']', $display_order, 2);
                    	ob_start();
                    	$textbox->render();
                    	$textbox_html = ob_get_clean();
                    	$staff_members[$key]['display_order'] = $textbox_html;
                    	
                        $staff_members[$key]['staff_photo'] = (!empty($staff_member['staff_photo'])) ? (image($staff_member['staff_photo'])) : ('');
                        $staff_members[$key]['name'] = div($staff_member['staff_first_name'] . ' ' . $staff_member['staff_last_name'], array('class'=>'nowrap'));
                        
                        $staff_members[$key]['staff_title'] = div($staff_member['staff_title'], array('class'=>'nowrap'));
                        
                        $staff_members[$key]['staff_about'] = substr($staff_member['staff_about'], 0, 300) . '... <br/><br/>';
                        
                        $hours = (!empty($staff_member['staff_hours'])) ? (gen_content('strong', 'Hours') . $staff_member['staff_hours']) : ('');
                        $staff_members[$key]['staff_about'] .= $hours;
                        
                        $actions = '';
                        $actions .= anchor($_SERVER['SCRIPT_NAME'] . '?page=edit_staff_member.php&action=edit&staff_id=' . $staff_member['staff_id'], 'Edit') . ' | ';
                        $delete_href = $_SERVER['SCRIPT_NAME'] . '?page=edit_staff_member.php&action=remove_staff_user&staff_id=' . $staff_member['staff_id'];
                        $actions .= gen_content('span', anchor($delete_href, 'Delete', array('class'=>'submitdelete remove_staff', 'staff_id'=>$staff_member['staff_id'])), array('class'=>'trash'));
                        $staff_members[$key]['actions'] = div($actions, array('class'=>'nowrap'));
                    }
                    
                    $data_order = array();
                    $data_order["display_order"] = "Display Order";
                    $data_order["staff_photo"] = "Photo";
                    $data_order["name"] = "Name";
                    $data_order["staff_title"] = "Title";
                    $data_order["staff_about"] = "About";
                    $data_order["actions"] = "Actions";
                        
                    $table = new rs_list($data_order, $staff_members);
                    $table->empty_message('You do not have any staff members at this time');
                    $table->identify("", "standard_rs wp-list-table widefat staff_members", "staff_members");
                    
                    ob_start();
                    $table->render();
                    $table_html = ob_get_clean();
                    
                    $form->add_element($table_html, 2);
                    $form->add_label("Order By:", 1, '', array('style'=>'width: 90px;'));
                    
                    // Order by
                    $order_by_setting = get_option('staff_mgt_order_by');
                    $ssa = new ssa('staff_mgt_order_by', array('display_order'=>'Display Order Value', 'staff_first_name'=>'First Name', 'staff_last_name'=>'Last Name', 'staff_title'=>'Staff Title'));
                    $ssa->selected_value($order_by_setting);

                    // Order by Direction
                    $staff_mgt_order_by_direction = get_option('staff_mgt_order_by_direction');
                    $ssa_direction = new ssa('staff_mgt_order_by_direction', array('SORT_ASC'=>'Ascending', 'SORT_DESC'=>'Descending'));
                    $ssa_direction->selected_value($staff_mgt_order_by_direction);

					$form->add_element(array($ssa, $ssa_direction));
                    
                    $form->render();
                }
                else
                {
                    print p('You do not have any staff members at this time', array('class'=>'notice'));
                }     
                break;
        }
        
        $content = ob_get_clean();
        
        staff_mgt::render_page($content);
   
    }
    
    
    
    /**
    * This method will return a list of departments and ability to add a new department
    *
    * @todo
    * @return string Returns HTML table of departments
    */
    public function manage_staff_departments()
    {
    	staff_mgt::render_page($content);
    }
    
    
    /**
    * This method will return options for template selections
    *
    * @return string Returns HTML table for selecting layout template
    */
    public function layout_selection()
    {
    	ob_start();
    	
    	print h2('Layout Options');
    	print p('Use the form below to update your layout options');
    	

        switch(staff_mgt::action())
        {
            case 'update-staff-layout-options':
            	
            	if (empty($_POST['staff_mgt_display_columns']) || !is_numeric($_POST['staff_mgt_display_columns']))
            		$_POST['staff_mgt_display_columns'] = 1;
            		
            	// update the columns
            	update_option('staff_mgt_display_columns', $_POST['staff_mgt_display_columns']);
            	
            	
            	// update the bg and border colors
            	update_option('staff_mgt_background_color', $_POST['staff_mgt_background_color']);
            	update_option('staff_mgt_border_color', $_POST['staff_mgt_border_color']);
            	update_option('staff_mgt_font_color', $_POST['staff_mgt_font_color']);
            	
            	print div('Layout Options Updated', array('class'=>'notice'));
            	break;
		}
    	
    	$form = new form('', $_SERVER['SCRIPT_NAME'] . '?page=' . $_GET['page']);
		$form->add_hidden(new hidden('action', 'update-staff-layout-options'));
    	$form->set_button('Update Layout Options');
    	
    	$form->set_attribute('class', 'ez_staff_form');
    	
    	$form->add_label('Number of Columns');
    	$columns = get_option('staff_mgt_display_columns');
    	$columns = (empty($columns)) ? (1) : ($columns);
    	$form->add_element(new textbox('staff_mgt_display_columns', $columns, 2));
    	
    	
    	$form->add_label('Staff Background Color');
    	$bg_color = get_option('staff_mgt_background_color');
    	$bg_color = (empty($bg_color)) ? (staff_mgt::default_bg_color()) : ($bg_color);
    	$element = new textbox('staff_mgt_background_color', $bg_color);
    	$element->set_attribute('class', 'Multiple');
    	$form->add_element($element);
    	
    	
    	$form->add_label('Staff Border Color');
    	$border_color = get_option('staff_mgt_border_color');
    	$border_color = (empty($border_color)) ? (staff_mgt::default_border_color()) : ($border_color);
    	$element = new textbox('staff_mgt_border_color', $border_color);
    	$element->set_attribute('class', 'Multiple');
    	$form->add_element($element);
    	
    	
    	$form->add_label('Font Color');
    	$font_color = get_option('staff_mgt_font_color');
    	$font_color = (empty($font_color)) ? (staff_mgt::default_font_color()) : ($font_color);
    	$element = new textbox('staff_mgt_font_color', $font_color);
    	$element->set_attribute('class', 'Multiple');
    	$form->add_element($element);
    	
    	$form->render();
    	
    	
    	$content = ob_get_clean();
    	staff_mgt::render_page($content);
    }
    
    
    private function default_bg_color() { return 'F5F5F5'; }
    private function default_border_color() { return 'CDCDCD'; }
    private function default_font_color() { return '373737'; }
    
    
    
    
    /**
    * This is a private function that will get the serialized array from the database, then unserialize it.
    *
    * @param int The staff ID. Default is empty, which will return all the members
    * 
    * @return array Returns array of staff members
    */
    private function get_staff_members($staff_id='')
    {
        $staff_members = get_option('staff_mgt_staff');
        if (!empty($staff_members) && !is_array($staff_members)) { $staff_members = unserialize($staff_members); }
        
        if (is_numeric($staff_id))
        {
            foreach($staff_members as $key => $staff_member)
            {
                if ($staff_member['staff_id'] != $staff_id)
                {
                    unset($staff_members[$key]);
                }
            }
            $staff_members = array_values($staff_members);
            return (isset($staff_members[0])) ? ($staff_members[0]) : (false);
        }
        else
        {
            return $staff_members;
        }
    }
    
    
    /**
    * This function will remove the given staff member
    * 
    * @param int The Staff ID
    *
    * @return bool Returns true when removed
    */
    private function remove_staff_member($staff_id)
    {
        if (is_numeric($staff_id))
        {
            $staff_members = staff_mgt::get_staff_members();
            foreach($staff_members as $key => $staff_member)
            {
                // remove this user from the array!
                if ($staff_member['staff_id'] == $staff_id) { unset($staff_members[$key]); }
            }
            
            // Now save the array back to the database again
            $staff_members = array_values($staff_members);
            $staff_members = serialize($staff_members);
            update_option('staff_mgt_staff', $staff_members);
            
        }
        else { return false; }
    }


    /**
    * This will render the page for all content and pages.
    *
    * @param string The content
    *
    * @return string The changed content
    */
    private function render_page($content)
    {
        print gen_content('div', $content, array('class'=>'wrap', 'style'=>'margin-top: 40px;'));
    }
    
    
    
    /**
    * This function will list the user on the front end using shortcode [staff_list]
    */
    public function staff_list()
    {
    	ob_start();
    	
    	// custom CSS
    	$bg_color = get_option('staff_mgt_background_color');
    	$bg_color = (empty($bg_color)) ? (staff_mgt::default_bg_color()) : ($bg_color);
    	
    	$border_color = get_option('staff_mgt_border_color');
    	$border_color = (empty($border_color)) ? (staff_mgt::default_border_color()) : ($border_color);

    	$font_color = get_option('staff_mgt_font_color');
    	$font_color = (empty($font_color)) ? (staff_mgt::default_font_color()) : ($font_color);
    	
    	$styles = '
			table.staff_list td div.staff_data {
				background-color: #' . $bg_color . ';
				border: 1px solid #' . $border_color . ';
				color: #' . $font_color . ';
			}
    	';
    	
    	print xhe('style', $styles, array('style'=>'text/css'));
    	
        $table = new table();
        $table->set_attribute('class', 'staff_list');
        
        $columns = get_option('staff_mgt_display_columns');
        $columns = (empty($columns) || !is_numeric($columns)) ? (1) : ($columns);
        $table->set_columns($columns);
        
        $staff_members = staff_mgt::get_staff_members();
        
        // Order the list..
        staff_mgt::array_sort_by_column($staff_members, get_option('staff_mgt_order_by'), get_option('staff_mgt_order_by_direction'));
        
        foreach($staff_members as $key => $staff_member)
        {   
            // Photo
            $image = (!empty($staff_member['staff_photo'])) ? (image($staff_member['staff_photo'])) : ('');
            
            // Name
            $name = $staff_member['staff_first_name'] . ' ' . $staff_member['staff_last_name'];
            $title = $staff_member['staff_title'];
            $name_title = div($name . ' - ' . $title, array('class'=>'staff_name'));
            
            $about = div($staff_member['staff_about'], array('class'=>'staff_about'));
            
            if (!empty($staff_member['staff_hours']))
            {
            	$hours = div(gen_content('strong', 'My Hours: ') . $staff_member['staff_hours'], array('class'=>'my_hours'));
            }
            else { $hours = ''; }
            
            $staff_data = div($name_title . $image . $about . div('&nbsp;', array('style'=>'clear: both;')) . $hours, array('class'=>'staff_data'));
            $table->td($staff_data);
        }
        
        $table->render();
        return ob_get_clean();
    }
    



	public function array_sort_by_column(&$arr, $col, $dir = SORT_ASC) {
		
		switch($dir)
		{
			case 'SORT_ASC':
				$dir = SORT_ASC;
				break;
				
			case 'SORT_DESC':
				$dir = SORT_DESC;
				break;
			
			default:
				$dir = SORT_ASC;
				break;
				
		}
		$sort_col = array();
		foreach ($arr as $key=> $row) {
			$sort_col[$key] = $row[$col];
		}
	
		array_multisort($sort_col, $dir, $arr);
	}





    
    /**
    * This function handles all global ajax calls
    */
    public function ajax_it() {
    ?>
        <script type="text/javascript" >
        jQuery(document).ready(function($) {
            
            //------------------------------------------------
            // Remove a user
            //------------------------------------------------
            $('a.remove_staff').click(function () {
                var this_tr = $(this).closest('tr');
                var this_td = $(this).closest('td');
                if (confirm('Are you sure you want to do this?'))
                {

                    var plugin_url = <?php print "'" . WP_PLUGIN_URL . "'"; ?>;
                    $(this).after('<img src="' + plugin_url + '/ez-staff-list/images/ajax-loader-bar.gif" class="loader" />');
                    $(this).hide();   

                    var data = {
                        action: 'staff_mgt',
                        sub_action: 'remove_staff_user',
                        staff_id: $(this).attr('staff_id')
                    };
                
                    // since 2.8 ajaxurl is always defined in the admin header and points to admin-ajax.php
                    jQuery.post(ajaxurl, data, function(response) {
                        if (response)
                        {
                            $(this_td).html('Removed!')
                            $(this_tr).children('td').attr('style', 'background-color: #ffc2b9;');
                        }
                    });
                }       
                return false;
            });
            
        });
        </script>
    <?php
    }
    
    /**
    * This function will return all the global AJAX results
    */
    function staff_mgt_ajax_callback() {
        global $wpdb; // this is how you get access to the database
        
        switch($_POST['sub_action'])
        {
            case "remove_staff_user":
                staff_mgt::remove_staff_member($_POST['staff_id']);
                print 'removed staff ID: ' . $_POST['staff_id'];
                break;
        }
    
        die(); // this is required to return a proper result
    }
}
?>