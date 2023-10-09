<?php

namespace Brunocfalcao\LaravelHelpers\Utils;

class DomainPatternIdentifier
{
    // Define named constants for readability and maintainability
    const DEFAULT_HTTP_PORT = 80;

    /**
     * Parse the given URL and extract its components.
     *
     * @param  string|null  $url The URL to parse.
     * @return array An associative array containing URL components.
     */
    public static function parseUrl($url = null)
    {
        if (is_null($url)) {
            $url = request()->fullUrl();
        }

        $parsedUrl = parse_url($url);

        $httpScheme = self::extractHttpScheme($parsedUrl);
        $subdomain = self::extractSubdomain($parsedUrl);
        $domain = self::extractDomain($parsedUrl);
        $topLevelDomain = self::extractTopLevelDomain($parsedUrl);
        $port = self::extractPort($parsedUrl);
        $path = self::extractPath($parsedUrl);
        $method = request()->method();
        $querystring = self::extractQuerystring($parsedUrl);

        return [
            'method' => $method,
            'http_scheme' => $httpScheme,
            'subdomain' => $subdomain,
            'domain' => $domain,
            'top_level_domain' => $topLevelDomain,
            'port' => $port,
            'path' => $path,
            'querystring' => $querystring,
        ];
    }

    private static function extractHttpScheme($parsedUrl)
    {
        return $parsedUrl['scheme'] ?? 'http';
    }

    private static function extractSubdomain($parsedUrl)
    {
        $subdomain = null;
        if (isset($parsedUrl['host'])) {
            $hostParts = explode('.', $parsedUrl['host']);
            $numParts = count($hostParts);

            if ($numParts > 2 && $hostParts[0] !== 'www') {
                $subdomain = implode('.', array_slice($hostParts, 0, $numParts - 2));
            }
        }

        return $subdomain;
    }

    private static function extractDomain($parsedUrl)
    {
        $domain = null;
        if (isset($parsedUrl['host'])) {
            $hostParts = explode('.', $parsedUrl['host']);
            $numParts = count($hostParts);

            if ($numParts >= 2) {
                $domain = $hostParts[$numParts - 2];
            } else {
                $domain = $parsedUrl['host'];
            }
        }

        return $domain;
    }

    private static function extractTopLevelDomain($parsedUrl)
    {
        $topLevelDomain = null;
        if (isset($parsedUrl['host'])) {
            $hostParts = explode('.', $parsedUrl['host']);
            $numParts = count($hostParts);

            if ($numParts >= 2) {
                $topLevelDomain = $hostParts[$numParts - 1];
            }
        }

        return $topLevelDomain;
    }

    private static function extractPort($parsedUrl)
    {
        return $parsedUrl['port'] ?? self::DEFAULT_HTTP_PORT;
    }

    private static function extractPath($parsedUrl)
    {
        $path = null;
        if (isset($parsedUrl['path'])) {
            $path = trim($parsedUrl['path'], '/');
            if ($path === '') {
                $path = null;
            }
        }

        return $path;
    }

    private static function extractQuerystring($parsedUrl)
    {
        if (isset($parsedUrl['query'])) {
            $querystring = [];
            parse_str($parsedUrl['query'], $querystring);

            return $querystring;
        }

        return null;
    }
}
