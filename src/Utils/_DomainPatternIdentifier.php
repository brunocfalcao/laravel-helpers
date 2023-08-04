<?php

namespace Brunocfalcao\LaravelHelpers\Utils;

class _DomainPatternIdentifier
{
    public static function parts($url = null)
    {
        if (is_null($url)) {
            $url = request()->fullUrl();
        }

        $parsedUrl = parse_url($url);

        $httpType = isset($parsedUrl['scheme']) ? $parsedUrl['scheme'] : 'http';
        $subdomain = null; // Initialize as null
        $domain = null;
        $suffix = null;
        $port = isset($parsedUrl['port']) ? $parsedUrl['port'] : 80;
        $path = null;

        if (isset($parsedUrl['host'])) {
            $hostParts = explode('.', $parsedUrl['host']);
            $numParts = count($hostParts);

            if ($numParts >= 2) {
                $domain = $hostParts[$numParts - 2];
                $suffix = $hostParts[$numParts - 1];

                if ($numParts > 2 && $hostParts[0] !== 'www') {
                    $subdomain = implode('.', array_slice($hostParts, 0, $numParts - 2));
                }
            } else {
                $domain = $parsedUrl['host'];
            }
        }

        if (isset($parsedUrl['path'])) {
            $path = trim($parsedUrl['path'], '/');
            if ($path === '') {
                $path = null;
            }
        }

        return [
            'http_type' => $httpType,
            'subdomain' => $subdomain,
            'domain' => $domain,
            'suffix' => $suffix,
            'port' => $port,
            'path' => $path,
        ];
    }
}
