<?php

/**
 * Controller for word.coffee session saving
 */

class Session extends My_Controller {

    private $blurb = "word.coffee presents writers with daily writing prompts to help them stay unblocked in the writing process.";

    function __construct( $request ) {
        parent::__construct( $request );

    }


    public function index( $sample_text = '', $prompt_id = 0 ) {

        $this->set_mode( MODE_BARE );

        $this->model( "sessions" );
        $this->model( "samples" );

        $error         = 0;
        $error_message = '';
        if ( !empty( $prompt_id ) ) {
            // Get the user's current session;
            $session_id = $this->sessions->get_current( );

            if ( !empty( $session_id ) ) {
                $sample = $this->samples->get_for_session( $session_id, $prompt_id );
                if ( empty( $sample ) ) {
                    $this->samples->add(
                        array(
                            'user_id'           => 0,
                            'session_id'        => $session_id,
                            'prompt_id'         => $prompt_id,
                            'text'              => $sample_text,
                            'public'            => 0

                        )
                    );
                } else {
                    $this->samples->update(
                        $sample['id'],
                        array(
                            'text'  => $sample_text
                        )
                    );
                }
                $error = 0;
                $error_message = "success";
            } else {
                $error = 1;
                $error_message = "No session ID";
            }
        } else {
            $error = 1;
            $error_message = "No prompt ID";
        }

        $this->data( 'json_data', json_encode( array( 'error' => $error, 'messsage' => $error_message ) ) );

        return( $this->view( 'bare_json.php' ) );
    }

}
