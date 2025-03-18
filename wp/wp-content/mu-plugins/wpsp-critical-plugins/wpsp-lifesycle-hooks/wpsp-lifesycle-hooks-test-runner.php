<?php


new wpsp_lifecycl_hooks_test_runner();

class wpsp_lifecycl_hooks_test_runner{
  
  
  
  ###
  #Setup your hooks debugging/testing environment. We preffer debug/test using discord.
  ###
  
  //please set it to: error_log, discord
  private $_logMethod = 'error_log'; 
  
  //set your discord server webhook if the above log method is discord.
  private $_discordWebHook = '';
  
  
  
  
  function __construct(){
    
    #run after site is created
		add_action( 'wpsp_lifecycle_after_site_created', array($this, 'wpsp_lifecycle_after_site_created') );
    
    #run before site is removed
		add_action( 'wpsp_lifecycle_before_site_removed', array($this, 'wpsp_lifecycle_before_site_removed') );
    
    #run before site is cloned
		add_action( 'wpsp_lifecycle_before_site_cloned', array($this, 'wpsp_lifecycle_before_site_cloned') );
    
    #run after site is cloned
    add_action( 'wpsp_lifecycle_after_site_cloned', array($this, 'wpsp_lifecycle_after_site_cloned') );
    
    #run before site is updated
		add_action( 'wpsp_lifecycle_before_site_updated', array($this, 'wpsp_lifecycle_before_site_updated') );
    
    #run after site is updated
    add_action( 'wpsp_lifecycle_after_site_updated', array($this, 'wpsp_lifecycle_after_site_updated') );
    
    
    #run before site is deactivated
		add_action( 'wpsp_lifecycle_before_site_deactivated', array($this, 'wpsp_lifecycle_before_site_deactivated') );
    
    #run after site is activated
    add_action( 'wpsp_lifecycle_after_site_activated', array($this, 'wpsp_lifecycle_after_site_activated') );
    
  }
  
  
  #run after site is created
  function wpsp_lifecycle_after_site_created ( $args ){
    $this->outPutArgsToSelectedMethod( 'wpsp_lifecycle_after_site_created', $args );
  }
  
  
  #run before site is removed
  function wpsp_lifecycle_before_site_removed ( $args ){
    $this->outPutArgsToSelectedMethod( 'wpsp_lifecycle_before_site_removed', $args );
  }
  
  
  #run before site is cloned - Note: This hook will run on the new site. It will not run on the source site.
  function wpsp_lifecycle_before_site_cloned ( $args ){
    $this->outPutArgsToSelectedMethod( 'wpsp_lifecycle_before_site_cloned', $args );
  }
  
  
  #run after site is cloned - Note: This hook will run on the new site. It will not run on the source site.
  function wpsp_lifecycle_after_site_cloned ( $args ){
    $this->outPutArgsToSelectedMethod( 'wpsp_lifecycle_after_site_cloned', $args );
  }
  
  
  #run before site is updated
  function wpsp_lifecycle_before_site_updated ( $args ){
    $this->outPutArgsToSelectedMethod( 'wpsp_lifecycle_before_site_updated', $args );
  }
  
  
  #run after site is udpated
  function wpsp_lifecycle_after_site_updated ( $args ){
    $this->outPutArgsToSelectedMethod( 'wpsp_lifecycle_after_site_updated', $args );
  }
  
  #run before site is deactivated
  function wpsp_lifecycle_before_site_deactivated ( $args ){
    $this->outPutArgsToSelectedMethod( 'wpsp_lifecycle_before_site_deactivated', $args );
  }
  
  
  #run after site is activated
  function wpsp_lifecycle_after_site_activated ( $args ){
    $this->outPutArgsToSelectedMethod( 'wpsp_lifecycle_after_site_activated', $args );
  }
  
  
  
  
  
  
  
  
  
  function outPutArgsToSelectedMethod( $hookName, $args ){
    
    if( $this->_logMethod == 'error_log' ){
      
      if (is_array($args) || is_object($args)) {
          error_log( $hookName . ' is called. ARGS: ' . print_r($args, true) );
      } else {
          error_log( $hookName . ' is called. ARGS: ' . var_export($args, true) );
      }
      
    }else{
      $this->sendMsgUsingDiscord( $hookName, $args );
    }
    
  }
  
  
  
  function sendMsgUsingDiscord( $hookName, $args ){
      // Format the output
      $content = is_array($args) || is_object($args) 
          ? print_r( $args, true )
          : var_export( $args, true );
      
      // Save the content to a temporary file
      $tempFile = tmpfile();
      $metaData = stream_get_meta_data($tempFile);
      $tempFilePath = $metaData['uri'];
      fwrite($tempFile, $content);
      
      // Prepare the payload for a file attachment
      $payload = [
          'payload_json' => json_encode(['content' => $hookName . ' is called. ARGS:']),
          'file'         => new CURLFile($tempFilePath, 'text/plain', 'log.txt')
      ];
      
      $ch = curl_init();
      curl_setopt( $ch, CURLOPT_URL, $this->_discordWebHook );
      curl_setopt( $ch, CURLOPT_POST, true );
      curl_setopt( $ch, CURLOPT_RETURNTRANSFER, true );
      curl_setopt( $ch, CURLOPT_SSL_VERIFYPEER, false );
      // Using multipart/form-data, so don't set a JSON header
      curl_setopt( $ch, CURLOPT_POSTFIELDS, $payload );
      
      $response = curl_exec( $ch );
      curl_close( $ch );
      
      // Clean up the temporary file
      fclose($tempFile);
  }
  
}