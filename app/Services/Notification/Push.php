<?php

namespace Services\Notification;

class Push {

    private $title;
    private $body;
    private $data;
 
 
    /**
     * @param $title
     */
    public function setTitle( $title ) {
       $this->title = $title;
       return $this;
    }
 
    /**
     * @param $message
     */
    public function setBody( $message ) {
       $this->body = $message;
       return $this;
    }

    /**
     * @param $message
     */
    public function setType( $type ) {
      $this->type = $type;
      return $this;
   }    
 
 
    /**
     * @param $data
     */
    public function setPayload( $data ) {
       $this->data = $data;
       return $this;
    }
 
 
    /**
     * @return array
     */
    public function getPush() {
       $response                      = array();
       $response['title']     = $this->title;
       $response['body']   = $this->body;
       $response['type']   = $this->type;
       $response['payload'] = $this->data;
       $response['timestamp'] = date( 'Y-m-d G:i:s' );
 
       return $response;
    }
 
 }