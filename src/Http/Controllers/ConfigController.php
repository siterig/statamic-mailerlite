<?php

namespace SiteRig\MailerLite\Http\Controllers;

use Edalzell\Forma\ConfigController as BaseController;
use Illuminate\Support\Arr;

class ConfigController extends BaseController
{
    protected function postProcess(array $values): array
    {
        $userConfig = Arr::get($values, 'user');

        return array_merge(
            $values,
            ['user' => $userConfig[0]]
        );
    }

    protected function preProcess(string $handle): array
    {
        $config = config($handle);

        return array_merge(
            $config,
            ['user' => [Arr::get($config, 'user', [])]]
        );
    }
}
