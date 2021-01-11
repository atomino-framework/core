<?php namespace Atomino\EntityPlugins\Attachment\Module\Img;

interface ImgCreatorInterface{

	public function crop(int $width, int $height, string $source, string $target, int $jpegQuality):bool;
	public function height(int $width, int $height, string $source, string $target, int $jpegQuality):bool;
	public function width(int $width, int $height, string $source, string $target, int $jpegQuality):bool;
	public function box(int $width, int $height, string $source, string $target, int $jpegQuality):bool;
	public function scale(int $width, int $height, string $source, string $target, int $jpegQuality):bool;

}