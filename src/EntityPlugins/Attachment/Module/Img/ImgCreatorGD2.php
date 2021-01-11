<?php namespace Atomino\EntityPlugins\Attachment\Module\Img;

class ImgCreatorGD2 implements ImgCreatorInterface{

	public function crop(int $width, int $height, string $source, string $target, int|null $jpegQuality):bool{
		if(is_null($img = $this->loadImage($source))) return false;

		$oAspect = imagesx($img) / imagesy($img);
		$aspect = $width / $height;
		$resizeWidth = ( $aspect < $oAspect ) ? $height * $oAspect : $width;
		$resizeHeight = ( $aspect > $oAspect ) ? $width / $oAspect : $height;
		$img = $this->doResize($img, $resizeWidth, $resizeHeight);
		$img = $this->doCrop($img, $width, $height);

		return $this->saveImage($target, $img, $jpegQuality);
	}
	public function height(int $width, int $height, string $source, string $target, int|null $jpegQuality):bool{
		if(is_null($img = $this->loadImage($source))) return false;

		$oAspect = imagesx($img) / imagesy($img);
		$this->doResize($img, (int)( $height * $oAspect ), $height);
		if ($width != 0 and (int)( $height * $oAspect ) > $width) return $this->doCrop($img, $width, $height);

		return $this->saveImage($target, $img, $jpegQuality);
	}
	public function width(int $width, int $height, string $source, string $target, int|null $jpegQuality):bool{
		if(is_null($img = $this->loadImage($source))) return false;

		$oAspect = imagesx($img) / imagesy($img);
		$this->doResize($img, $width, (int)( $width / $oAspect ));
		if ($height != 0 and (int)( $width / $oAspect ) > $height) return $this->doCrop($img, $width, $height);

		return $this->saveImage($target, $img, $jpegQuality);
	}
	public function box(int $width, int $height, string $source, string $target, int|null $jpegQuality):bool{
		if(is_null($img = $this->loadImage($source))) return false;

		$aspect = $width / $height;
		$oAspect = imagesx($img) / imagesy($img);
		if ($aspect < $oAspect) $height = (int)$width / $oAspect;
		elseif ($aspect > $oAspect) $width = (int)$height * $oAspect;

		return $this->saveImage($target, $img, $jpegQuality);
	}
	public function scale(int $width, int $height, string $source, string $target, int|null $jpegQuality):bool{
		if(is_null($img = $this->loadImage($source))) return false;

		$img = $this->doResize($img, $width, $height);

		return $this->saveImage($target, $img, $jpegQuality);
	}

	private function loadImage(string $source):\GdImage|null{
		return match ( getimagesize($source)['2'] ) {
			1 => imagecreatefromgif($source),
			2 => imagecreatefromjpeg($source),
			3 => imagecreatefrompng($source),
			default => null
		};
	}
	private function saveImage(string $target, \GdImage $img, int|null $jpegQuality):bool{
		$pathInfo = pathinfo($target);
		return match ( $pathInfo['extension'] ) {
			'gif' => imagegif($img, $target),
			'png' => imagepng($img, $target),
			'jpg' => imagejpeg($img, $target, base_convert($jpegQuality, 32, 10) * 4),
			'webp' => imagewebp($img, $target, base_convert($jpegQuality, 32, 10) * 4),
			default => false
		};
	}

	private function doResize($img, int $width, int $height): \GdImage|bool{
		$newImg = imagecreatetruecolor($width, $height);
		$oWidth = imagesx($img);
		$oHeight = imagesy($img);
		imagefill($newImg, 0, 0, imagecolorallocatealpha($newImg, 0, 0, 0, 127));
		imagecopyresampled($newImg, $img, 0, 0, 0, 0, $width, $height, $oWidth, $oHeight);
		imagedestroy($img);
		return $newImg;
	}
	private function doCrop($img, int $width, int $height): \GdImage|bool{
		$newImg = imageCreateTrueColor($width, $height);
		imagefill($newImg, 0, 0, imagecolorallocatealpha($newImg, 0, 0, 0, 127));
		$sx = $sy = 0;

		$oWidth = imagesx($img);
		$oHeight = imagesy($img);
		if ($oWidth == $width) $sy = $oHeight / 2 - $height / 2;
		else $sx = $oWidth / 2 - $width / 2;

		imagecopyresampled($newImg, $img, 0, 0, $sx, $sy, $width, $height, $width, $height);
		imagedestroy($img);
		return $newImg;
	}

}