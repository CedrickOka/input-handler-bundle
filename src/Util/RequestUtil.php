<?php

namespace Oka\InputHandlerBundle\Util;

use Symfony\Component\HttpFoundation\Request;

/**
 * 
 * @author Cedrick Oka Baidai <okacedrick@gmail.com>
 * 
 */
final class RequestUtil
{
	public static function parseQueryStringToArray(Request $request, string $key, string $delimiter = null, $defaultValue = null): array
	{
		$value = $request->query->get($key, $defaultValue);
		
		if ($value && $delimiter !== null) {
			$value = array_map(function($value){
				return self::sanitizeQueryString($value);
			}, explode($delimiter, $value));
		}
		
		return $value;
	}
	
	public static function sanitizeQueryString(string $query): string
	{
		return trim(rawurldecode($query));
	}
	
	public static function getContentFromFormat(Request $request, string $format)
	{
		switch ($format) {
			case 'json':
				return json_decode($request->getContent(), true);
				
			case 'xml':
				return simplexml_load_string($request->getContent(), true);
				break;
				
			case 'form':
				return $request->request->all();
				break;
				
			default;
				return null;
		}
	}
	
	public static function getContent(Request $request):? array
	{
		switch ($request->getContentType()) {
			case 'json':
				return json_decode($request->getContent(), true);
				
			case 'xml':
				return simplexml_load_string($request->getContent(), true);
				break;
				
			case 'form':
				return $request->request->all();
				break;
				
			default;
				return null;
		}
	}
	
	public static function getContentLikeArray(Request $request): array
	{
		switch ($request->getContentType()) {
			case 'json':
				$data = json_decode($request->getContent(), true);
				return $data ?: [];
				
			case 'form':
				return $request->request->all();
				
			default;
				return [];
		}
	}
}
