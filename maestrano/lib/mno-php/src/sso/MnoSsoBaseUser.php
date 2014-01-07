<?php

/**
 * Properly format a User received from Maestrano 
 * SAML IDP
 */
class MnoSsoBaseUser
{
  /**
   * User UID
   * @var string
   */
  public $uid = '';
  
  /**
   * User email
   * @var string
   */
  public $email = '';
  
  /**
   * User name
   * @var string
   */
  public $name = '';
  
  /**
   * User surname
   * @var string
   */
  public $surname = '';
  
  /**
   * Maestrano specific user sso session token
   * @var string
   */
  public $sso_session = '';
  
  /**
   * When to recheck for validity of the sso session
   * @var datetime
   */
  public $sso_session_recheck = null;
  
  /**
   * Is user owner of the app
   * @var boolean
   */
  public $app_owner = false;
  
  /**
   * An associative array containing the Maestrano 
   * organizations using this app and to which the
   * user belongs.
   * Keys are the maestrano organization uid.
   * Values are an associative array containing the
   * name of the organization as well as the role 
   * of the user within that organization.
   * ---
   * e.g:
   * { 'org-876' => {
   *      'name' => 'SomeOrga',
   *      'role' => 'Super Admin'
   *   }
   * }
   * @var array
   */
  public $organizations = array();
  
  /**
   * User Local Id
   * @var string
   */
  public $local_id = null;
  
  
  /**
   * Construct the MnoSsoBaseUser object from a SAML response
   *
   * @param OneLogin_Saml_Response $saml_response
   *   A SamlResponse object from Maestrano containing details
   *   about the user being authenticated
   */
  public function __construct(OneLogin_Saml_Response $saml_response)
  {
      // First get the assertion attributes from the SAML
      // response
      $assert_attrs = $saml_response->getAttributes();
      
      // Populate user attributes from assertions
      $this->uid = $assert_attrs['mno_uid'][0];
      $this->sso_session = $assert_attrs['mno_session'][0];
      $this->sso_session_recheck = new DateTime($assert_attrs['mno_session_recheck'][0]);
      $this->name = $assert_attrs['name'][0];
      $this->surname = $assert_attrs['surname'][0];
      $this->email = $assert_attrs['email'][0];
      $this->app_owner = $assert_attrs['app_owner'][0];
      $this->organizations = json_decode($assert_attrs['organizations'][0],true);
  }
  
  /**
   * Try to find a local application user matching the sso one
   * using uid first, then email address.
   * If a user is found via email address then then setLocalUid
   * is called to update the local user Maestrano UID
   * ---
   * Internally use the interface method:
   *  - getLocalIdByUid
   *  - getLocalIdByEmail
   *  - setLocalUid
   * 
   * @return local_id if a local user matched, null otherwise
   */
  public function matchLocal()
  {
    // Try to get the local id from uid
    $lid = $this->_getLocalIdByUid($this->uid);
    
    // Get local id via email if previous search
    // was unsuccessful
    if (is_null($lid)) {
      $lid = $this->_getLocalIdByEmail($this->email);
      
      // Set Maestrano UID on user
      if ($lid) {
        $this->_setLocalUid($lid,$this->uid);
      }
    }
    
    // Assign local_id (can be null)
    $this->local_id = $lid;
    
    return $lid;
  }
  
  /**
   * Get the ID of a local user via Maestrano UID lookup
   * This method must be re-implemented in MnoSsoUser
   * (raise an error otherwise)
   *
   * @return a user ID if found, null otherwise
   */
  protected function _getLocalIdByUid($_uid)
  {
    throw new Exception('Function '. __FUNCTION__ . ' must be overriden in MnoSsoUser class!');
  }
  
  /**
   * Get the ID of a local user via email lookup
   * This method must be re-implemented in MnoSsoUser
   * (raise an error otherwise)
   *
   * @return a user ID if found, null otherwise
   */
  protected function _getLocalIdByEmail($_email)
  {
    throw new Exception('Function '. __FUNCTION__ . ' must be overriden in MnoSsoUser class!');
  }
  
  /**
   * Set the Maestrano UID on a local user via email lookup
   * This method must be re-implemented in MnoSsoUser
   * (raise an error otherwise)
   *
   * @return a user ID if found, null otherwise
   */
  protected function _setLocalUid($_id,$_uid)
  {
    throw new Exception('Function '. __FUNCTION__ . ' must be overriden in MnoSsoUser class!');
  }
}