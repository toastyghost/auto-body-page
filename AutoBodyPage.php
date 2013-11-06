<?php

class AutoBodyPage extends Page
{
  static $db = array(
    'Hours' => 'Text',
    'Phone' => 'Text',
    'Address1' => 'Text',
    'Address2' => 'Text',
    'Latitude' => 'Text',
    'Longitude' => 'Text',
    'LocationID' => 'Int',
    'Mailto' => 'Varchar(100)',
    'SubmitText' => 'Text',
  );
  
  static $has_one = array(
    'Picture' => 'Image',
    'Map' => 'Image',
  );
  
  public function getCMSFields() {
    $fields = parent::getCMSFields();
    
    $locAry = array();
    $locs = DataObject::get('Location');
    
    foreach($locs as $loc) {
      $locAry[$loc->ID] = $loc->Title;
    }
    
    $fields->addFieldToTab('Root.Content.Location', new DropdownField('LocationID', 'Location', $locAry));
    $fields->addFieldToTab('Root.Content.Location', new TextField('Hours'));
    $fields->addFieldToTab('Root.Content.Location', new TextField('Phone'));
    $fields->addFieldToTab('Root.Content.Location', new TextField('Address1', 'Address Line 1'));
    $fields->addFieldToTab('Root.Content.Location', new TextField('Address2', 'Address Line 2'));
    
    $fields->addFieldToTab('Root.Content.OnSubmission', new TextField('Mailto', 'Email Submissions to'));
    $fields->addFieldToTab('Root.Content.OnSubmission', new TextareaField('SubmitText', 'Text on Submission'));
    
    $fields->addFieldToTab('Root.Content.Map', new TextField('Latitude', 'Latitude'));
    $fields->addFieldToTab('Root.Content.Map', new TextField('Longitude', 'Longitude'));
    $fields->addFieldToTab('Root.Content.Map', new ImageField('Map', 'Map', null, null, null, 'images'));
    
    return $fields;
  }
}

class AutoBodyPage_Controller extends Page_Controller
{
  static $allowed_actions = array(
    'ContactForm',
    'Success'
  );
  
  function ContactForm() {
    $fields = new FieldSet(
      new TextField('Full_Name', 'Full Name:'),
      new TextField('Company_Name', 'Company Name:'),
      new TextField('Phone_Number', 'Best Phone Number to Reach You:'),
      new TextField('Email', 'Email:'),
      new TextareaField('Comments', 'Comments:')
    );
    
    $actions = new FieldSet(
      new FormAction('SendContactForm', 'Send')
    );
    
    $validator = new RequiredFields();
    
    return new Form($this, 'ContactForm', $fields, $actions, $validator);
  }
  
  function SendContactForm($data, $form) {
    $From = $data['Email'];
    $To = $this->Mailto;
    $Subject = 'Collision Center email from BrownAutos.com';
    $email = new Email($From, $To, $Subject);
    
    $email->setTemplate('ContactEmail');
    $email->populateTemplate($data);
    $email->send();
    
    Director::redirect($this->Link('?success=1'));
  }
  
  public function Success() {
    return isset($_REQUEST['success']) && $_REQUEST['success'] == '1';
  }
}

?>
