<?php

declare(strict_types=1);

if (PHP_SAPI !== 'cli') {
    exit(2);
}

$base = null;
$zip = false;
foreach (array_slice($argv, 1) as $arg) {
    if ($arg === '--zip') {
        $zip = true;
    } else {
        $base = rtrim($arg, '/');
    }
}

if (!$base) {
    echo "Usage: php verify_extension.php <path> [--zip]\n";
    exit(2);
}

final class Verifier
{
    private string $base;
    private bool $zip;
    private array $results = [];
    private int $errors = 0;
    private int $warnings = 0;
    private ?SimpleXMLElement $manifest = null;
    private string $slug = '';
    private string $filesPath = '';
    private string $adminPath = '';
    private string $servicesProvider = '';
    private string $entryFile = '';
    private string $namespacePath = '';
    private string $namespacePrefix = '';

    public function __construct(string $base, bool $zip)
    {
        $this->base = realpath($base) ?: $base;
        $this->zip = $zip;
    }

    public function run(): void
    {
        $this->check('Manifest', [$this, 'checkManifest']);
        $this->check('Dateien', [$this, 'checkFiles']);
        $this->check('Namespace', [$this, 'checkNamespace']);
        $this->check('Services', [$this, 'checkServices']);
        $this->check('Sprachen', [$this, 'checkLanguages']);
        $this->check('Syntax', [$this, 'checkSyntax']);
        if ($this->zip) {
            $this->check('ZIP', [$this, 'checkZip']);
        }
        foreach ($this->results as $result) {
            echo $result[0] . ': ' . $result[1] . PHP_EOL;
            foreach ($result[2] as $msg) {
                echo '  - ' . $msg . PHP_EOL;
            }
        }
    }

    public function exitCode(): int
    {
        if ($this->errors) {
            return 2;
        }
        if ($this->warnings) {
            return 1;
        }
        return 0;
    }

    private function check(string $name, callable $fn): void
    {
        $status = 'OK';
        $messages = [];
        try {
            $fn($status, $messages);
        } catch (Throwable $e) {
            $status = 'ERROR';
            $messages[] = $e->getMessage();
        }
        $this->results[] = [$name, $status, $messages];
        if ($status === 'ERROR') {
            $this->errors++;
        }
        if ($status === 'WARN') {
            $this->warnings++;
        }
    }

    private function checkManifest(string &$status, array &$messages): void
    {
        $xmlFiles = glob($this->base . '/*.xml');
        foreach ($xmlFiles as $file) {
            $xml = @simplexml_load_file($file);
            if ($xml && (string) $xml['type'] === 'component') {
                $this->manifest = $xml;
                $name = basename($file, '.xml');
                $this->slug = preg_replace('/^com_/', '', $name);
                break;
            }
        }
        if (!$this->manifest) {
            $status = 'ERROR';
            $messages[] = 'Manifest not found';
            return;
        }
        if (!$this->manifest->xpath('//files') || !$this->manifest->xpath('//languages') || !$this->manifest->xpath('//services') || !$this->manifest->xpath('//namespace')) {
            $status = 'ERROR';
            $messages[] = 'Required nodes missing';
            return;
        }
        $adminFolder = (string) ($this->manifest->administration['folder'] ?? '');
        $this->adminPath = $adminFolder ? $this->base . '/' . $adminFolder : $this->base;
        $filesNode = $this->manifest->administration->files[0];
        $filesFolder = (string) $filesNode['folder'];
        $this->filesPath = $filesFolder ? $this->base . '/' . $filesFolder : $this->adminPath;
        $this->entryFile = $this->filesPath . '/' . $this->slug . '.php';
        $serviceNode = $this->manifest->administration->services->service[0] ?? null;
        if ($serviceNode) {
            $this->servicesProvider = $this->filesPath . '/' . trim((string) $serviceNode);
        }
        $nsNode = $this->manifest->administration->namespace[0];
        $this->namespacePath = $this->base . '/' . (string) $nsNode['path'];
        $this->namespacePrefix = trim((string) $nsNode);
    }

    private function checkFiles(string &$status, array &$messages): void
    {
        if (!$this->manifest) {
            $status = 'ERROR';
            $messages[] = 'Manifest missing';
            return;
        }
        $filesNode = $this->manifest->administration->files[0];
        foreach ($filesNode->filename as $f) {
            $p = $this->filesPath . '/' . (string) $f;
            if (!is_file($p)) {
                $status = 'ERROR';
                $messages[] = $p;
            }
        }
        foreach ($filesNode->folder as $f) {
            $p = $this->filesPath . '/' . (string) $f;
            if (!is_dir($p)) {
                $status = 'ERROR';
                $messages[] = $p;
            }
        }
        $langNodes = $this->manifest->administration->languages[0];
        $langFolder = (string) $langNodes['folder'];
        $langBase = $langFolder ? $this->adminPath . '/' . $langFolder : $this->adminPath;
        foreach ($langNodes->language as $l) {
            $p = $langBase . '/' . (string) $l;
            if (!is_file($p)) {
                $status = 'ERROR';
                $messages[] = $p;
            }
        }
        if ($this->servicesProvider && !is_file($this->servicesProvider)) {
            $status = 'ERROR';
            $messages[] = $this->servicesProvider;
        }
        if (!is_file($this->entryFile)) {
            $status = 'ERROR';
            $messages[] = $this->entryFile;
        }
    }

    private function checkNamespace(string &$status, array &$messages): void
    {
        if (!$this->namespacePath || !$this->namespacePrefix || !is_dir($this->namespacePath)) {
            $status = 'ERROR';
            $messages[] = 'Namespace path missing';
            return;
        }
        $iterator = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($this->namespacePath));
        foreach ($iterator as $file) {
            if ($file->isFile() && $file->getExtension() === 'php') {
                $handle = fopen($file->getPathname(), 'r');
                if (!$handle) {
                    $status = 'ERROR';
                    $messages[] = $file->getPathname();
                    continue;
                }
                $nsLine = null;
                while (($line = fgets($handle)) !== false) {
                    $line = trim($line);
                    if ($line !== '') {
                        if (str_starts_with($line, 'namespace ')) {
                            $nsLine = $line;
                        }
                        break;
                    }
                }
                fclose($handle);
                if (!$nsLine || !str_starts_with($nsLine, 'namespace ' . $this->namespacePrefix)) {
                    $status = 'ERROR';
                    $messages[] = $file->getPathname();
                }
            }
        }
    }

    private function checkServices(string &$status, array &$messages): void
    {
        if (!$this->servicesProvider || !is_file($this->servicesProvider)) {
            $status = 'ERROR';
            $messages[] = 'Provider missing';
            return;
        }
        $content = file_get_contents($this->servicesProvider) ?: '';
        if (!str_contains($content, 'ComponentDispatcherFactory') || !str_contains($content, 'MVCFactory') || !str_contains($content, 'RouterFactory')) {
            $status = 'ERROR';
            $messages[] = $this->servicesProvider;
        }
        if (!is_file($this->entryFile)) {
            $status = 'ERROR';
            $messages[] = 'Entry file missing';
            return;
        }
        $entry = file_get_contents($this->entryFile) ?: '';
        $warn = false;
        if (!str_contains($entry, 'ComponentDispatcherFactoryInterface')) {
            $warn = true;
            $messages[] = 'ComponentDispatcherFactoryInterface';
        }
        if (!str_contains($entry, 'MVCFactoryInterface')) {
            $warn = true;
            $messages[] = 'MVCFactoryInterface';
        }
        if ($warn && $status === 'OK') {
            $status = 'WARN';
        }
    }

    private function checkLanguages(string &$status, array &$messages): void
    {
        $dir = $this->base . '/administrator/language';
        if (!is_dir($dir)) {
            $status = 'WARN';
            $messages[] = 'administrator/language not found';
            return;
        }
        $ok = true;
        foreach (scandir($dir) as $tag) {
            if ($tag[0] === '.') {
                continue;
            }
            $p = $dir . '/' . $tag;
            if (is_dir($p)) {
                $f1 = $p . '/' . $tag . '.com_' . $this->slug . '.ini';
                $f2 = $p . '/' . $tag . '.com_' . $this->slug . '.sys.ini';
                if (!is_file($f1)) {
                    $ok = false;
                    $messages[] = $f1;
                }
                if (!is_file($f2)) {
                    $ok = false;
                    $messages[] = $f2;
                }
            }
        }
        if (!$ok && $status === 'OK') {
            $status = 'WARN';
        }
    }

    private function checkSyntax(string &$status, array &$messages): void
    {
        $iterator = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($this->base));
        $ok = true;
        foreach ($iterator as $file) {
            if ($file->isFile() && $file->getExtension() === 'php') {
                $cmd = 'php -l ' . escapeshellarg($file->getPathname()) . ' 2>&1';
                exec($cmd, $out, $code);
                if ($code !== 0) {
                    $ok = false;
                    $messages[] = implode(' ', $out);
                }
            }
        }
        if (!$ok && $status === 'OK') {
            $status = 'ERROR';
        }
    }

    private function checkZip(string &$status, array &$messages): void
    {
        $zipFile = $this->base . '/' . $this->slug . '-test.zip';
        $zip = new ZipArchive();
        if ($zip->open($zipFile, ZipArchive::CREATE | ZipArchive::OVERWRITE) !== true) {
            $status = 'WARN';
            $messages[] = 'ZIP not created';
            return;
        }
        $iterator = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($this->base));
        foreach ($iterator as $file) {
            if (!$file->isDir()) {
                $pathName = $file->getPathname();
                $localName = substr($pathName, strlen($this->base) + 1);
                $zip->addFile($pathName, $localName);
            }
        }
        $zip->close();
        $size = filesize($zipFile);
        $messages[] = $zipFile . ' (' . $size . ' bytes)';
    }
}

$verifier = new Verifier($base, $zip);
$verifier->run();
exit($verifier->exitCode());
