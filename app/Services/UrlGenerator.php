<?php namespace App\Services;

use Illuminate\Routing\UrlGenerator as LaravelUrlGenerator;
use Illuminate\Http\Request;
use InvalidArgumentException;
use Illuminate\Contracts\Routing\UrlRoutable;
use Illuminate\Contracts\Routing\UrlGenerator as UrlGeneratorContract;

class UrlGenerator extends LaravelUrlGenerator {
  
	/**
	 * Generate a absolute URL to the given path.
	 *
	 * @param  string  $path
	 * @param  mixed  $extra
	 * @param  bool|null  $secure
	 * @return string
	 */
	public function to($path, $extra = array(), $secure = null)
	{
		// First we will check if the URL is already a valid URL. If it is we will not
		// try to generate a new one but will simply return the URL as is, which is
		// convenient since developers do not always have to check if it's valid.
		if ($this->isValidUrl($path)) return $path;

		$scheme = $this->getScheme($secure);

		$extra = $this->formatParameters($extra);

		$tail = implode('/', array_map(
			'rawurlencode', (array) $extra)
		);

		// Once we have the scheme we will compile the "tail" by collapsing the values
		// into a single string delimited by slashes. This just makes it convenient
		// for passing the array of parameters to this URL as a list of segments.	
		
		$root = $this->getRootUrl($scheme);		
		//echo '/'.\Config::get('app.locale_prefix');
		if(strpos($path, 'images') === FALSE)	
		$root .= '/'.\Config::get('app.locale_prefix');

		return $this->trimUrl($root, $path, $tail);
	}
}
