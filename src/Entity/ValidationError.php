<?php namespace Atomino\Entity;


class ValidationError extends \Exception {

	private $errors = [];

	public function __construct($errors){
		$this->errors = $errors;
		parent::__construct("Validation error");
	}

	public function getErrors(){return $this->errors; }
	public function getMessages(){return array_map(function ($error){return ['field'=>$error['field'], 'message'=>$error['message']];}, $this->errors); }

}