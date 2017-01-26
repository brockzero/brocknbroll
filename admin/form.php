<?php
/**
 * Form.php
 *
 * The Form class is meant to simplify the task of keeping
 * track of errors in user submitted forms and the form
 * field values that were entered correctly.
 *
 */
 
class Form
{
   private $values = array();  //Holds submitted form field values
   private $errors = array();  //Holds submitted form error messages
   private $num_errors;   //The number of errors in submitted form

   /* Class constructor */
   function __construct(){
      if(isset($_SESSION['value_array']) && isset($_SESSION['error_array'])){
         $this->values = $_SESSION['value_array'];
         $this->errors = $_SESSION['error_array'];
         $this->num_errors = count($this->errors);

         unset($_SESSION['value_array']);
         unset($_SESSION['error_array']);
      } else {
         $this->num_errors = 0;
      }
   }

   function setValue($field, $value){
      $this->values[$field] = $value;
   }
   
   function getValue($field){
      if(array_key_exists($field, $this->values)){
		 return htmlspecialchars($this->values[$field]);
      }else{
         return NULL;
      }
   }
   
   function getError($field){
      if(array_key_exists($field, $this->errors)){
         return '<span style="color:#f00;">'.$this->errors[$field].'</span>';
      }else{
         return NULL;
      }
   }

   function setError($field, $errmsg){
      $this->errors[$field] = $errmsg;
      $this->num_errors = count($this->errors);
   }
   
   function getErrorArray() {
      return $this->errors;
   }
            
   function getNumErrors() {
	  return $this->num_errors;   
   }
}
?>