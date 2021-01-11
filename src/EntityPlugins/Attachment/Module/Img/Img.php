<?php namespace Atomino\EntityPlugins\Attachment\Module\Img;

use Atomino\Core\Application;
use Atomino\EntityPlugins\Attachment\Module\Attachment;
use Atomino\EntityPlugins\Attachment\Module\Config;
use JetBrains\PhpStorm\Pure;
use Symfony\Component\HttpFoundation\File\File;

/**
 * @property string $png
 * @property string $jpg
 * @property string $webp
 * @property string $gif
 * @property string $url
 */
class Img{
	private string $urlBase;

	private File $file;
	private string $operation;
	private int $jpegQuality = 60;
	private string $pathId;
	private string $secret;

	public function __construct(private Attachment $attachment){
		$this->file = $this->attachment->file;
		$config = Application::DIC()->get(Config::class);
		$this->urlBase = $config->imgUrl;
		$this->secret = $config->imgSecret;
		$this->jpegQuality = $config->imgJpegQuality ?? 80;
		$this->pathId = str_replace('/', '', trim($this->attachment->storage->subPath, "/"));
	}

	#region resize
	public function scale(int $width, int $height): self{
		$padding = $this->convPad($width, $height);
		$this->operation = 's' . $this->conv($width, $padding) . $this->conv($height, $padding);
		return $this;
	}
	public function crop(int $width, int $height): self{
		$padding = $this->convPad($width, $height);
		$this->operation = 'c' . $this->conv($width, $padding) . $this->conv($height, $padding);
		return $this;
	}
	public function box(int $width, int $height): self{
		$padding = $this->convPad($width, $height);
		$this->operation = 'b' . $this->conv($width, $padding) . $this->conv($height, $padding);
		return $this;
	}
	public function width(int $width, int $maxHeight = 0): self{
		$padding = $this->convPad($width, $maxHeight);
		$this->operation = 'w' . $this->conv($width, $padding) . $this->conv($maxHeight, $padding);
		return $this;
	}
	public function height(int $height, int $maxWidth = 0): self{
		$padding = $this->convPad($height, $maxWidth);
		$this->operation = 'h' . $this->conv($maxWidth, $padding) . $this->conv($height, $padding);
		return $this;
	}
	#endregion

	#region export
	public function exportGif(): string{ return $this->img('gif'); }
	public function exportPng(): string{ return $this->img('png'); }
	public function exportWebp(): string{ return $this->img('webp'); }
	public function exportJpg(int $quality = null): string{
		if (!is_null($quality)) $this->jpegQuality = $quality;
		return $this->img('jpg');
	}
	public function export(int $quality = null): string{
		if (!is_null($quality)) $this->jpegQuality = $quality;
		$pathInfo = pathinfo($this->attachment->filename);
		$ext = strtolower($pathInfo['extension']);
		if ($ext == 'jpeg') $ext = 'jpg';
		return $this->img($ext);
	}
	#endregion

	private function img(string $ext): string{
		$op = $this->operation;
		if ($ext === 'jpg' || $ext === 'webp'){
			$this->jpegQuality = min(max($this->jpegQuality, 0), 100);
			$op .= '.' . base_convert(floor($this->jpegQuality / 4), 10, 32);
		}
		$url = $this->file->getFilename() . '.' . $op . '.' . $this->pathId;
		$url = $this->urlBase . '/' . urlencode($url) . '.' . base_convert(crc32($url . '.' . $ext . $this->secret), 10, 32) . '.' . $ext;
		return $url;
	}

	private function conv(int $value, int $padding): string{
		return str_pad(base_convert($value, 10, 36), $padding, '0', STR_PAD_LEFT);
	}
	#[Pure] private function convPad(int ...$values): int{
		return strlen(base_convert(max(...$values), 10, 36));
	}

	#[Pure] public function __isset($name): bool{ return in_array($name, ['png', 'gif', 'jpg', 'jpeg', 'webp', 'url']); }
	public function __get($name): string|null{
		return match ( $name ) {
			'png' => $this->exportPng(),
			'gif' => $this->exportGif(),
			'jpg', 'jpeg' => $this->exportJpg(),
			'webp' => $this->exportWebp(),
			'url' => $this->export(),
			default => null
		};
	}
}