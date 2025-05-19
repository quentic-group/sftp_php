<?php

require_once "_vendor/autoload.php";

use League\Flysystem\Config;
use League\Flysystem\DirectoryListing;
use League\Flysystem\Filesystem;
use League\Flysystem\PhpseclibV3\SftpConnectionProvider;
use League\Flysystem\PhpseclibV3\SftpAdapter;

$privateKey = getenv("OPENSSH_PRIVATE_KEY");
$sfpHost = getenv("SFTP_HOST");
$sftpUserName = getenv("SFTP_USER_NAME");

function showDirectoryContent(Filesystem $filesystem, string $path) {
    /**
     * @var DirectoryListing $values
     */
    $values = $filesystem->listContents($path, true);

    echo json_encode($values->toArray(), JSON_PRETTY_PRINT);
}

function copyDirectory(Filesystem $filesystem, string $remoteDirPath) {
    $localFilesystem = new League\Flysystem\Local\LocalFilesystemAdapter(__DIR__ . "/local-data");
    $cfg = new Config();
    foreach ($filesystem->listContents($remoteDirPath, true) as $item) {
        if ($item->isFile()) {
            $remotePath = $item->path();
            $localPath = $remotePath; // Adjust if you want a different structure
            $contents = $filesystem->read($remotePath);
            $localFilesystem->write($localPath, $contents, $cfg);
        } elseif ($item->isDir()) {
            $localFilesystem->createDirectory($item->path(), $cfg);
        }
    }
}

echo "connecting to sftp server\n";
echo "host: $sfpHost, user: $sftpUserName\n";

$filesystem = new Filesystem(new SftpAdapter(
    new SftpConnectionProvider(
        $sfpHost,
        $sftpUserName,
        null,
        $privateKey,
        null,
        22,
        true,
        30,
        10,
        null,
        null
    ),
    '/'
));

$remotePath = "data";
$showRemotePath = $remotePath;
foreach ($argv as $i => $arg) {
    if ($arg === '--copy-dir' && isset($argv[$i + 1])) {
        $remotePath = $argv[$i + 1];
    } else if ($arg === '--show' && isset($argv[$i + 1])) {
        $showRemotePath = $argv[$i + 1];
    }
}

if (in_array('--copy-dir', $argv)) {
    copyDirectory($filesystem, $remotePath);
} else {
    showDirectoryContent($filesystem, $showRemotePath);
}