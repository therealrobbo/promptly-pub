<?php
/**
 * Model for Partner Codes, which includes stuff like Google analytics scripts, facebook widgets, etc
 */

define( 'PC_EXCLUDE_NONE',             0 );
define( 'PC_EXCLUDE_CBR_FRONT_PAGE',   1 );
define( 'PC_EXCLUDE_CBR_IMAGE_VIEWER', 2 );
define( 'PC_EXCLUDE_PRINTER_FRIENDLY', 3 );


define( 'PC_LOCATION_HEADER',                100 );
define( 'PC_LOCATION_AFTER_BODY_OPEN',       200 );
define( 'PC_LOCATION_BEFORE_CONTENT_BODY',   210 );
define( 'PC_LOCATION_AFTER_CONTENT_BODY',    220 );
define( 'PC_LOCATION_CONTENT_FOOTER_TOP',    225 );
define( 'PC_LOCATION_CONTENT_FOOTER_BOTTOM', 230 );
define( 'PC_LOCATION_AFTER_BLURB_OPEN',      265 );
define( 'PC_LOCATION_AFTER_BLURB_CLOSE',     270 );
define( 'PC_LOCATION_BEFORE_BODY_CLOSE',     300 );
define( 'PC_LOCATION_AFTER_BODY_CLOSE',      400 );



class Partner_codes extends PZ_Model {

    public $partner_code_table = 'partner_codes';
    public $exclusions = array(
        PC_EXCLUDE_NONE             => 'Nowhere'
    );
    public $locations = array(
        PC_LOCATION_HEADER                => 'Site Header',
        PC_LOCATION_AFTER_BODY_OPEN       => 'After Body Open',
        PC_LOCATION_BEFORE_BODY_CLOSE     => 'Before Body Close',
        PC_LOCATION_AFTER_BODY_CLOSE      => 'After Body Close'
    );



    function __construct(  ) {
        parent::__construct(  );
    }

    /**
     * Add a new nav item to the database
     *
     * @param $update_fields
     * @return int|null
     */
    function  add( $update_fields ) {

        $query = "INSERT INTO  " . $this->partner_code_table . " " .
                         "SET pc_name       = '" . $this->gCI->db->escape_string( $update_fields['pc_name'] )  . "', " .
                             "pc_code       = '" . $this->gCI->db->escape_string( $update_fields['pc_code'] )  . "', " .
                             "pc_location   = '" . $update_fields['pc_location']  . "', " .
                             "pc_order      = '" . $update_fields['pc_order']  . "', " .
                             "pc_exclusions = '" . $update_fields['pc_exclusions']  . "'";
        if ( $this->gCI->db->query( $query ) )
            return $this->gCI->db->insert_id();
        else
            return( null );
    }

    /**
     * Update a partner code in the database
     *
     * @param $update_fields
     * @return int|null
     */
    function  update( $update_fields ) {

        $query = "UPDATE  " . $this->partner_code_table . " " .
                    "SET pc_name       = '" . $this->gCI->db->escape_string( $update_fields['pc_name'] )  . "', " .
                        "pc_code       = '" . $this->gCI->db->escape_string( $update_fields['pc_code'] )  . "', " .
                        "pc_location   = '" . $update_fields['pc_location']  . "', " .
                        "pc_order      = '" . $update_fields['pc_order']  . "', " .
                        "pc_exclusions = '" . $update_fields['pc_exclusions']  . "' " .
                  "WHERE pc_id='" . $update_fields['pc_id'] . "'";
        return ( $this->gCI->db->query( $query ) );
    }


    /**
     * Get a single partner code from the database
     *
     * @param $pc_id
     * @return array
     */
    function get_code_item( $pc_id ) {

        $query = "SELECT * FROM " . $this->partner_code_table . " " .
                         "WHERE pc_id = '" . $this->gCI->db->escape_string( $pc_id ) . "' ";
        $result = $this->gCI->db->query( $query );

        $pc_item = null;
        if ( !empty( $result ) ) {
            $pc_item = $this->gCI->db->fetch_assoc( $result );
        }

        return( $pc_item );
    }


    /**
     * Delete an item from the database
     *
     * @param $pc_id
     *
     * @return true if delete succeeded, false otherwise
     */
    function delete( $pc_id ) {

        $query = "DELETE FROM " . $this->partner_code_table  . " " .
                       "WHERE pc_id='" . $pc_id . "'";

        return( $this->gCI->db->query( $query ) );
    }


    /**
     * Update the sort and parent fields on Nav Item records in the database
     *
     * @param $sort_order - an array containing the sort order for each nav item
     * @param $form_locations - an array containing the parent id for each nav item
     */
    function update_sort( $sort_order, $form_locations ) {

        foreach( $sort_order as $key => $value ) {
            $query = "UPDATE " . $this->partner_code_table . " " .
                        "SET pc_order='" . $value . "', " .
                            "pc_location='" . $form_locations[$key] . "' " .
                      "WHERE pc_id='" . $key . "'";
            $this->gCI->db->query( $query );
        }
    }


    /**
     * Get the list of nav items from the database in raw (not prepped for HTML menus) form
     *
     * @param int $location_id
     * @param int $exclusion_page - the current page so we can check exclusion conditions
     *
     * @return array|null
     */
    function get_all( $location_id = 0, $exclusion_page = PC_EXCLUDE_NONE ) {

        $new_pc_items = null;

        // Build a where clause
        $where = "WHERE 1 ";

        // Limit selection to specified locations
        if ( !empty( $location_id ) )
            $where .= "AND pc_location='" .  $location_id . "' ";

        if ( !empty( $exclusion_page ) ) {
            $where .= "AND pc_exclusions NOT LIKE '"   . $exclusion_page . "' " .    // just the exclusion page
                      "AND pc_exclusions NOT LIKE '%," . $exclusion_page . "' " .    // A list ending with the exclusion page
                      "AND pc_exclusions NOT LIKE '%," . $exclusion_page . ",%' " .  // A list the exclusion page in the middle
                      "AND pc_exclusions NOT LIKE '"   . $exclusion_page . ",%' ";   // A list begining with the exclusion page
        }

        // Query the database
        $query = "SELECT * FROM " . $this->partner_code_table . " " .
                           $where .
                      "ORDER BY pc_location ASC, pc_order ASC";

        $result = $this->gCI->db->query( $query );

        // Cache the query results in an array
        if ( !empty( $result ) ) {
            $new_pc_items = array();
            while( $pc_item = $this->gCI->db->fetch_assoc( $result ) ) {
                $new_pc_items[] = $pc_item;
            }
        }

        return( $new_pc_items );
    }



    /**
     * Get all partners codes and return them as a table of $location => $code_info
     *
     * @param bool $is_front_page
     *
     * @return array
     */
    function get_all_codes( $is_front_page = false ) {


        $return_array = array();
        foreach( $this->locations  as $location_id => $location_name  ) {

            // Get the current list of nav items
            $partner_codes = $this->get_all( $location_id, ( $is_front_page ? PC_EXCLUDE_CBR_FRONT_PAGE : PC_EXCLUDE_NONE ) );
            if ( !empty( $partner_codes ) && is_array( $partner_codes ) &&
                ( count( $partner_codes ) > 0 ) ) {
                $return_array[$location_id] = $partner_codes;
            }
        }

        return( $return_array );
    }


    /**
     * Take some pre-loaded partner codes and put them into a template
     *
     * @param $partner_codes
     * @param $template
     */
    function add_to_template( $partner_codes ) {
        // Load up the partner codes into the template
        foreach( $partner_codes as $location_id => $partner_code_info ) {
            $this->gCI->data( 'partner_codes_' .  $location_id, $partner_code_info );
        }
    }


}
