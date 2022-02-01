<?php

namespace Oka\InputHandlerBundle\Util;

use Symfony\Component\HttpFoundation\Request;

/**
 * @author Cedrick Oka Baidai <okacedrick@gmail.com>
 */
final class RequestUtil
{
    public static function parseQueryStringToArray(Request $request, string $key, string $delimiter = null, $defaultValue = null): array
    {
        $value = $request->query->get($key, $defaultValue);

        if ($value && null !== $delimiter) {
            $value = array_map(function ($value) {
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

            case 'form':
                return array_merge($request->request->all(), $request->files->all());

            default:
                return null;
        }
    }

    public static function getContent(Request $request)
    {
        return null !== $request->getContentType() ? self::getContentFromFormat($request, $request->getContentType()) : null;
    }
}
