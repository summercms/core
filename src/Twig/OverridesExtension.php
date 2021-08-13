<?php

namespace Bolt\Twig;

use Twig\Environment;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;

/**
 * Prevent Twig from doing well-intended things that could be abused
 *
 * @see: https://github.com/swisskyrepo/PayloadsAllTheThings/tree/master/Server%20Side%20Template%20Injection#twig
 */
class OverridesExtension extends AbstractExtension
{
    private $blacklisted = ['curl_exec', 'curl_multi_exec', 'escapeshellcmd', 'exec', 'parse_ini_file', 'passthru', 'popen', 'proc_get_status', 'proc_nice', 'proc_open', 'shell_exec', 'show_source', 'system'];

    /**
     * {@inheritdoc}
     */
    public function getFilters(): array
    {
        return [
            new TwigFilter('file_excerpt', [$this, 'file_excerpt']),
            new TwigFilter('filter', [$this, 'filter'], ['needs_environment' => true]),
        ];
    }

    function file_excerpt(string $file): string
    {
        return $file;
    }

    function filter(Environment $env, $array, $arrow)
    {
        if (in_array($arrow, $this->blacklisted)) {
            return $array;
        }

        return twig_array_filter($env, $array, $arrow);
    }

}