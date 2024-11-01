<?php
/*
Plugin Name: SPToday
Plugin URI: http://sp-today.com
Author: SPToday
Version: 0.1
License: GPL 2.0
*/
?>
<?php
class SPToday extends WP_Widget {

	function __construct() {
		// Instantiate the parent object
		parent::__construct( false, 'أسعار العملات مقابل الليرة السورية' );	}

	function widget( $args, $instance ) {
        $title = apply_filters('widget_title', $instance['title']);
        $city = $instance['city'];
        if($city == 'damascus'){
            $url = 'http://sp-today.com/api/cur_damascus.json';
            $city_ar = "دمشق";
        }
        else{
            $url = 'http://sp-today.com/api/cur_aleppo.json';
            $city_ar = "حلب";
        }
        $file = file_get_contents($url);
        $curs = json_decode($file, true);
        $lastupdate = (file_get_contents('http://sp-today.com/api/lastupdate.json'));
        $names = ['دولار أمريكي', 'يورو', 'ليرة تركية'];
        $flags = ['us.png', 'euro.png', 'tr.png'];
        $plugin_dir = plugin_dir_url( $file )."/sptoday";
        ?>
        <style>
            #sptoday-curs{
                width: 100%;
                border: 1px solid #E1E1E1;
                background-color: #f7f7f7;
                margin-bottom:0;
                border-bottom: 0;
                border-top: 0;
            }
            #sptoday-curs td, #sptoday-curs th {border:none;}
            #sptoday-curs thead td, #sptoday-curs thead th {
                color: #209351;
                border-top-color: #209351;
                background-color: #F0f0f0;
                border-top: 5px solid;
                border-bottom: 1px solid #e1e1e1;
            }
            #sptoday-curs tr {height:50px;}
            #sptoday-curs  th, #sptoday-curs  td {
                border-bottom: 1px solid #e1e1e1;
                width: 20%;
            }
            #sptoday-curs th {
                width: 40%;
            }
            td.sptoday-askcolumn {
                width: 47px;
            }
            td.sptoday-arrowcolumn {
                width: 40px;
            }
            .sptoday-curflag{margin-left:4px;}
            #sptoday-bottomtable, #sptoday-bottomtable th, #sptoday-bottomtable td {border:0;}
            #sptoday-bottomtable{
                font-size:14px;
                border: 1px solid #E1E1E1;
                background-color: #F3F3F3;
            }
            #sptoday-bottomtable{width:100%;}
            #sptoday-bottomtable p {
                margin: 0;
                margin-right: 4%;
            }
        </style>
        <div class=widget-top>
            <h4><?php echo $title; ?></h4>
            
            <table id="sptoday-curs">
                <thead>
                    <tr>
                        <th>الأسعار في <?php echo $city_ar; ?></th>
                        <td class="sptoday-askcolumn">شراء</td>
                        <td class="sptoday-arrowcolumn"></td>
                        <td>مبيع</td>
                    </tr>
                </thead>
            <?php foreach($curs as $key=>$value){ ?>
                <tr>
                    <th>
                        <img src=<?php echo $plugin_dir; ?>/images/flags/<?php echo $flags[$key]; ?> class="sptoday-curflag">
                        <?php echo $names[$key].": "; ?>
                    </th>
                    <td><?php echo $value['ask']; ?></td>
                    <td><img src="<?php echo $plugin_dir; ?>/images/row-<?php if($value['arrow']=="1"){?>up.png<?php }else{ ?>down.png<?php } ?>"></td>
                    <td><?php echo $value['bid']; ?></td>
                </tr>
            <?php } ?>
            </table>
            <table id="sptoday-bottomtable">
                <tr>
                    <td class="sptoday-copyright"><p>حسب موقع <a href="https://sp-today.com" target="_blank"><strong>الليرة اليوم</strong></a></p></td>
                    <td class="sptoday-lastupdate"><p><?php echo $lastupdate; ?></p></td>
                </tr>
            </table>
        </div><br>
    <?php
	}

	function form( $instance ) {
		// Output admin widget options form
        // Check values
        if( $instance) {
             $title = esc_attr($instance['title']);
             $city = esc_attr($instance['city']);
        } else {
             $title = '';
             $city = '';
        }
        ?>

        <p>
        <label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('العنوان', 'wp_widget_plugin'); ?></label>
        <input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo $title; ?>" />
        </p>

        <p>
        <label for="<?php echo $this->get_field_id('city'); ?>"><?php _e('إظهار الأسعار في مدينة:', 'wp_widget_plugin'); ?></label>
        <select class="widefat" id="<?php echo $this->get_field_id('city'); ?>" name="<?php echo $this->get_field_name('city'); ?>" type="text" >
            <option value="damascus" <?php if ($city=='damascus') { ?> selected <?php } ?> >دمشق</option>
            <option value="aleppo" <?php if ($city=='aleppo'){ ?> selected <?php } ?> >حلب</option>
        </select>
        </p>

    <?php
	}
    
    function update( $new_instance, $old_instance ) {
		// Save widget options
        $instance = $old_instance;
        // Fields
        $instance['title'] = strip_tags($new_instance['title']);
        $instance['city'] = strip_tags($new_instance['city']);
        return $instance;
	}
}

function sptoday_register_widgets() {
	register_widget( 'SPToday' );
}

add_action( 'widgets_init', 'sptoday_register_widgets' );

?>