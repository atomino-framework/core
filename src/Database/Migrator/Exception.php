<?php namespace Atomino\Database\Migrator;

class Exception extends \Exception{
	const IntegrityCheckError = 1;
	const StatusCheckError = 2;
	const VersionNotFound = 3;

}