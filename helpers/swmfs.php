<?php
class swmfs {
    public static $waste_types = [
        'ts-scrap-metal*' => 'TS Scrap Metal*',
        'ts-tires' => 'TS Tires',
        'ts-carpet' => 'TS Carpet',
        'hhw-e-waste' => 'HHW E-Waste',
        'hhw-mixed-recycling' => 'HHW Mixed Recycling',
        'hhw-diverted' => 'HHW Diverted'
    ];

    public static $landfill_type = [
        'slug' => 'landfill-type',
        'name' => 'Landfill Type',
        'options' => [
            'garbage' => 'Garbage (Class 1)',
            'diverted' => 'C&D (class 3) Diverted'
        ]
    ];

    public static function get_metabox_text_markup( $slug, $name, $meta_val ) {
        ?>
        <div>
            <label for="<?=$slug?>">
                <?=$name;?>
                <input 
                    type='text' 
                    value='<?=$meta_val?>' 
                    id='<?=$slug?>' 
                    name="<?=$slug?>" 
                    />
            </label>
        </div>
        <?php
    }

    //function to check if selected
    public static function selectedChk ($compare, $pluginTip){
        $compare = sanitize_text_field($compare);
        if($pluginTip == $compare){
            return 'value="'.$compare.'" Selected';
        }else{
            return 'value="'.$compare.'"';
        }
    }


    public static function get_metabox_option_markup( $slug, $name, $meta_val ) {
        ?>
        <div>
            <label for="<?=$slug?>">
                <?=$name;?>
                <select id='<?=$slug?>' 
                    name="<?=$slug?>" 
                >
                    <option value=''>Select</option>
                    <?foreach( self::$landfill_type['options'] as $tslug => $tname ):?>
                        <option <?=self::selectedChk($tslug, $meta_val)?>><?=$tname?></option>
                    <?endforeach;?>
                </select>
            </label>
        </div>
        <?php
    }

    public static function save_generic_category_fields_meta( $field_name, $post_id, $post) {   
		
		// Return if the user doesn't have edit permissions.
		if ( ! current_user_can( 'edit_post', $post_id ) ) {
			return $post_id;
		}

		// Verify this came from the our screen and with proper authorization,
		// because save_post can be triggered at other times.
		if ( ! isset( $_POST[$field_name] ) || ! wp_verify_nonce( $_POST[$field_name . '_fields'], basename(__FILE__) ) ) {
			return $post_id;
		}
		// Now that we're authenticated, time to save the data.
		// This sanitizes the data from the field and saves it into an array $events_meta.
		$events_meta[$field_name] = esc_textarea( $_POST[$field_name] );
		// Cycle through the $events_meta array.
		// Note, in this example we just have one item, but this is helpful if you have multiple.
		foreach ( $events_meta as $key => $value ) :
			// Don't store custom data twice
			if ( 'revision' === $post->post_type ) {
				return;
			}
			if ( get_post_meta( $post_id, $key, false ) ) {
				// If the custom field already has a value, update it.
				update_post_meta( $post_id, $key, $value );
			} else {
				// If the custom field doesn't have a value, add it.
				add_post_meta( $post_id, $key, $value);
			}
			if ( ! $value ) {
				// Delete the meta key if there's no value
				delete_post_meta( $post_id, $key );
			}
		endforeach;
	}

    public static function get_metaboxes_for_text_fields($post) {
        foreach( self::$waste_types as $slug => $name ) {
			wp_nonce_field( basename( __FILE__ ), "${slug}_fields" );
            $meta_val = get_post_meta( $post->ID, $slug, true );
            self::get_metabox_text_markup( $slug, $name, $meta_val );
		}
    }

    public static function get_metabox_for_category_field($post) {
        $slug = self::$landfill_type['slug'];
        $name = self::$landfill_type['name'];

        wp_nonce_field( basename( __FILE__ ), "${slug}_fields" );
        $meta_val = get_post_meta( $post->ID, $slug, true );

        self::get_metabox_option_markup( $slug, $name, $meta_val );
    }

    	// landfill classes 
	public static function save_fields_meta( $post_id, $post) { 
		foreach(array_keys( self::$waste_types ) as $key)
		    self::save_generic_category_fields_meta( $key, $post_id, $post );
		
		// save our center type field
		self::save_generic_category_fields_meta( 
				self::$landfill_type['slug'], 
			$post_id, 
			$post 
		);
	}

}