<?php

/**
 * Controller for default word.coffee front page
 */

class Home extends My_Controller {

    private $blurb = "word.coffee presents writers with daily writing prompts to help them stay unblocked in the writing process.";
    private $about_text = "";


    function __construct( $request ) {
        parent::__construct( $request );

        $this->model( "prompts" );
        $this->library( "convert_html" );

        $this->about_text =
            "<p>Sometimes, the hardest part of writing is just getting started.</p>" .
            "<p><strong>word.coffee</strong> presents you with daily writing prompts to help you get and stay unblocked in the writing process. Just visit us, read today's prompt and start writing.</p>" .
            "<p>If you're ready to begin, click the <strong>Get Started</strong> button.</p>" .
            "<p class='fine'>We'll store a cookie so we can save your writing between visits, and you don't have to see this welcome page again...</p>";

    }


    public function index( $action = '' ) {

        $this->set_mode( MODE_FRONT );
        $session_id = $this->sessions->get_current( );
        if ( !empty( $session_id ) || ( $action == "wake_up_and_write" ) ) {
            return( $this->write() );
        } else {
            return( $this->about() );
        }
    }


    public function about( ) {
        $this->set_mode( MODE_FRONT );

        $this->data( 'about_text', $this->about_text );

        $this->data( 'template',    'about.php' );

        $this->asset_request( REQ_ASSET_CSS, 'https://fonts.googleapis.com/css?family=Slabo+27px' );
        $this->asset_request( REQ_ASSET_CSS, 'about' );

        $this->asset_request( REQ_ASSET_JS,  'about' );

        return( $this->view(  ) );
    }

    public function write( ) {
        $this->set_mode( MODE_FRONT );

        $prompt_rec = $this->prompts->select_for_today(  );

        // The Prompt
        $this->data( 'prompt_id',    $prompt_rec['id'] );
        $this->data( 'prompt_text',  $prompt_rec['text'] );

        // Does the current user has a session going?
        $session_id = $this->sessions->get_current( );
        if ( empty( $session_id ) ) {
            $this->sessions->start_session( );
        }

        // Get the user's current sample
        $sample = ( !empty( $session_id ) ? $this->samples->get_for_session( $session_id, $prompt_rec['id'] ) : null );
        $this->data( 'sample_text', ( !empty( $sample ) ? $sample['text'] : '' ) );

        // Header stuff
        $page_title    = $this->gen_page_title( $this->gPZ['app_slogan'] );
        $this->data( 'title',  $page_title );

        $this->data( 'show_control_panel', true );

        // SEO Stuff
        $canonical_url = $this->gPZ['base_url'];
        $this->data( 'page_title',    $page_title );
        $this->data( 'canonical_url', $canonical_url );
        $this->data( 'seo_descr',     $this->blurb );

        $this->data( 'coffee_control_dl_types',      array(
            CONVERT_TYPE_RTF  => "Rich Text Format (RTF)",
            CONVERT_TYPE_HTML => "HTML",
            CONVERT_TYPE_TXT  => "Plain Text",
        ) );

        // Open graph meta tags
        $this->add_og_meta( 'url',            $canonical_url );
        $this->add_og_meta( 'title',          $page_title );
        $this->add_og_meta( 'description',    $this->blurb );

        // And some static stuff
        $this->data( 'template',    'home.php' );

        $this->asset_request( REQ_ASSET_CSS, 'https://fonts.googleapis.com/css?family=Slabo+27px' );
        $this->asset_request( REQ_ASSET_CSS, 'trumbowyg' );
        $this->asset_request( REQ_ASSET_CSS, 'home' );

        $this->asset_request( REQ_ASSET_JS, 'trumbowyg' );
        $this->asset_request( REQ_ASSET_JS,  'home' );

        return( $this->view(  ) );
    }

}
