<?php

namespace Astrotomic\IgnitionGithubTab;

use Facade\Ignition\Tabs\Tab as BaseTab;

class Tab extends BaseTab
{
    public function name(): string
    {
        return 'GitHub';
    }

    public function component(): string
    {
        return 'ignition-github';
    }

    public function registerAssets(): void
    {
        $this->script($this->component(), __DIR__.'/../dist/js/tab.js');
    }

    public function meta(): array
    {
        return [
            'config' => json_decode(file_get_contents(__DIR__.'/../github.json')),
        ];
    }
}
