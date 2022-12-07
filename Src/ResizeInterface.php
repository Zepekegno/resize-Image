<?php

namespace Zepekegno;

interface ResizeInterface
{

	
	/** Resizing an image.
	 * @param string $target
	 * @param mixed $quality
	 * @param bool $delete
	 * @throws ResizeQualityException
	 * @return string|null
	 */
	public function make(string $target, mixed $quality, bool $delete): ?string;

	/** Convert an image to other format or 
	 * convert and resize to other format.
	 * @throws ResizeQualityException
	 * @return  null|string
	 */
	public function convert(string $type, string $target, ?int $quality, bool $isResizable, ?string $name, bool $old): ?string;

	/**
	 * Match all extensions supported for conversion
	 * @param string $extension
	 * @param string $source
	 * @param string $target
	 * @param int $width
	 * @param int $height
	 * @param int $quality=-1
	 * 
	 * @return void
	 */
	public function matchType(string $extension, string $source,string $target, int $width, int $height, int $quality) :void;

}