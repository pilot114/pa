<?php

namespace Component;

class FsAnalize
{
    /**
     * @var Finder
     */
    private $finder;

    public function __construct(Finder $finder)
    {
        $this->finder = $finder;
    }

    /**
     * Выводит общую информацию по файлам и директориям
     */
    public function stat()
    {
        $this->dirStat();
        $this->fileStat('php');
        return $this->finder->view();
    }

    /**
     * Находит кол-во использований в коде по regex
     */
    public function findCountUsage($criteriaFindRegex)
    {
        $finded = [];
        foreach ($this->finder->files()->name('*.php') as $file) {
            $countMatches = 0;
            foreach ($file->openFile() as $string) {
                preg_match($criteriaFindRegex, $string, $matches);
                $countMatches += count($matches);
            }
            if ($countMatches) {
                $finded[$file->getRelativePathname()] = $countMatches;
            }
        }
        return $finded;
    }

    /**
     * Находит что-нибудь в коде по regex
     * @param $criteriaFindRegex   - сигнатура файла, в котором ищем
     * @param $criteriaSelectRegex - сигнатура того, что ищем
     * @return array
     */
    public function findBySignature($criteriaFindRegex, $criteriaSelectRegex)
    {
        $finded = [];
        foreach ($this->finder->files()->name('*.php') as $file) {

            $currentMatch = false;
            foreach ($file->openFile() as $string) {
                // если находим хотябы 1 критерий поиска - берем файл целиком
                preg_match($criteriaFindRegex, $string, $matches);
                if (count($matches) > 0) {
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

    private function dirStat($printNotPhp = false)
    {
        $this->finder->outWrap($this->finder->path);

        $totalFilesCount = $this->finder->files()->count();
        $filesInRoot = $this->finder->files()->depth('== 0')->count();

        $this->finder->out("Total files: " .   $totalFilesCount);
        $this->finder->out("Not php files: " . $this->finder->files()->notName('*.php')->count());
        if ($printNotPhp) {
            foreach ($this->finder->files()->notName('*.php') as $file) {
                $this->finder->out('* ' . $file->getRelativePathname());
            }
        }

        $this->finder->out("Dirs in root: " .  $this->finder->directories()->depth('== 0')->count());
        $this->finder->out("Files in root: " .  $filesInRoot);
        $this->finder->out('');

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
        $this->finder->out('Big dirs: (>1% file count)');
        $this->finder->outTable(['Dir', 'Files', 'Percent'], $tableData);
        $this->finder->out('');
    }

    private function fileStat($ext = null)
    {
        $pattern = '*';
        if ($ext) {
            $pattern .= '.' . $ext;
        }

        $bigFiles = [];
        foreach ($this->finder->files()->name($pattern) as $file) {
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

        $this->finder->out('Big files: (>32kb)');
        $this->finder->outTable(['Size, kb', 'Name'], $bigFiles);
        $this->finder->out('');
    }
}
