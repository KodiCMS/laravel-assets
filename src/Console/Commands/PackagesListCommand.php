<?php

namespace KodiCMS\Assets\Console\Commands;

use KodiCMS\Assets\Contracts\PackageInterface;
use KodiCMS\Assets\Contracts\PackageManagerInterface;
use Illuminate\Console\Command;
use KodiCMS\Assets\Contracts\AssetElementInterface;
use Symfony\Component\Console\Helper\TableSeparator;

class PackagesListCommand extends Command
{
    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'assets:packages';

    /**
     * The table headers for the command.
     *
     * @var array
     */
    protected $headers = [
        'Package',
        'Files',
        'Dependency',
    ];

    /**
     * Execute the console command.
     *
     * @param PackageManagerInterface $manager
     *
     * @return mixed
     */
    public function fire(PackageManagerInterface $manager)
    {
        $packages = [];

        $i = 0;

        /** @var PackageInterface[] $packagesList */
        $packagesList = $manager->all();

        foreach ($packagesList as $name => $package) {
            /** @var AssetElementInterface[] $files */
            $files = $package->all();

            foreach ($files as $file) {
                if (isset($packages[$name])) {
                    $packages[$i]['id'] = '';
                    $packages[$i]['files'] = $file->getSrc();
                    $packages[$i]['deps'] = $file->getDependency();

                    $i++;
                } else {
                    $packages[$name]['id'] = $name;
                    $packages[$name]['files'] = $file->getSrc();
                    $packages[$name]['deps'] = $file->getDependency();
                }
            }

            $packages[$i] = new TableSeparator();
            $i++;
        }

        foreach ($packages as $i => $data) {
            foreach ($data as $key => $rows) {
                if (is_array($rows)) {
                    $packages[$i][$key] = implode(', ', $rows);
                }
            }
        }

        $this->table($this->headers, $packages);
    }
}
