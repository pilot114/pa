<?php

class FsAnalize
{
    private $finder;

    public function __construct(Finder $finder)
    {
        $this->finder = $finder;
    }

    public function stat()
    {
        $this->dirStat();
        $this->fileStat();
        return $this->view();
    }

    public function findBySignature($criteriaFindRegex, $criteriaSelectRegex)
    {
        $finded = [];
        foreach ($this->finder->files()->name('*.php') as $file) {

            $currentMatch = false;
            foreach ($file->openFile() as $string) {
                // если находим хотябы 1 критерий поиска - берем файл целиком
                preg_match($criteriaFindRegex, $string, $matches);
                if ($matches) {
                    $currentMatch = true;
                    break;
                }
            }
            if ($currentMatch) {
                foreach ($file->openFile() as $string2) {
                    preg_match($criteriaSelectRegex, $string2, $matches);
                    if ($matches) {
                        $finded[] = $string2;
                        $currentMatch = true;
                        break;
                    }
                }
            }
        }
        return $finded;
    }

    private function dirStat()
    {
        $this->outWrap($this->finder->path);

        $totalFilesCount = $this->finder->files()->count();
        $filesInRoot = $this->finder->files()->depth('== 0')->count();

        $this->out("Total files: " .   $totalFilesCount);

        $this->out("Not php files: " . $this->finder->files()->notName('*.php')->count());
        foreach ($this->finder->files()->notName('*.php') as $file) {
            $this->out('* ' . $file->getRelativePathname());
        }

        $this->out("Dirs in root: " .  $this->finder->directories()->depth('== 0')->count());
        $this->out("Files in root: " .  $filesInRoot);
        $this->out('');

        $rootDirs = [];
        foreach ($this->finder->directories()->depth('== 0') as $dir) {
            $dirName = $dir->getRelativePathname();
            $filesCount = $this->finder->files()->path('/^' . $dirName . '\//')->count();
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
    }

    private function fileStat()
    {
        $bigFiles = [];
        foreach ($this->finder->files() as $file) {
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
}
