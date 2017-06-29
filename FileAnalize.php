<?php

class FileAnalize extends FinderAnalize
{
    public function dirStat()
    {
        $this->outWrap($this->path);

        $totalFilesCount = $this->files()->count();
        $filesInRoot = $this->files()->depth('== 0')->count();

        $this->out("Total files: " .   $totalFilesCount);

        $this->out("Not php files: " . $this->files()->notName('*.php')->count());
        foreach ($this->files()->notName('*.php') as $file) {
            $this->out('* ' . $file->getRelativePathname());
        }

        $this->out("Dirs in root: " .  $this->directories()->depth('== 0')->count());
        $this->out("Files in root: " .  $filesInRoot);
        $this->out('');

        $rootDirs = [];
        foreach ($this->directories()->depth('== 0') as $dir) {
            $dirName = $dir->getRelativePathname();
            $filesCount = $this->files()->path('/^' . $dirName . '\//')->count();
            $rootDirs[$dirName] = $filesCount;
        }
        arsort($rootDirs);
        
        $tableData = [];
        foreach ($rootDirs as $dirName => $count) {
            $percent = round($count / ($totalFilesCount/100), 2);
            // print only big dirs
            if($percent > 1) {
                $tableData[] = [$dirName, $count, $percent . '%'];
            }
        }
        $this->out('Big dirs: (>1% file count)');
        $this->outTable(['Dir', 'Files', 'Percent'], $tableData);
        $this->out('');

        // for test:
        // $this->out("Count check: " . (array_sum($rootDirs) + $filesInRoot));
    }

    public function fileStat()
    {
        $bigFiles = [];
        foreach ($this->files() as $file) {
            if ($file->getSize() > 1024 * 32) {
                $bigFiles[] = [(int)($file->getSize()/1024), $file->getRelativePathname()];
            }
            ksort($bigFiles);
        }
        usort(
            $bigFiles, function ($a, $b) {
                return $a[0] < $b[0];
            }
        );

        $this->out('Big files: (>32kb)');
        $this->outTable(['Size, kb', 'Name'], $bigFiles);
        $this->out('');
    }

    public function findBySignature($criteriaFind, $criteriaSelect)
    {
        $finded = [];
        foreach ($this->files()->name('*.php') as $file) {

            $currentMatch = false;
            foreach ($file->openFile() as $string) {
                // если находим хотябы 1 критерий поиска - берем фаил целиком
                if (strpos($string, $criteriaFind) !== false) {
                    $currentMatch = true;
                    break;
                }
            }
            if ($currentMatch) {
                foreach ($file->openFile() as $string) {
                    if (strpos($string, $criteriaFind) !== false) {
                        $currentMatch = true;
                        break;
                    }
                }
            }
        }
    }
}
