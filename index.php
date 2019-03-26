<?php include('inc/head.php'); ?>
<?php if(isset($_POST['fileToDelete'])) { ?>
    <?php
        $entryToDelete = $_POST['fileToDelete'];
        if (is_file($entryToDelete)){
            unlink($entryToDelete);
        } 
        if (is_dir($entryToDelete)){
            $dir_iterator = new RecursiveDirectoryIterator($entryToDelete);
            $iterator = new RecursiveIteratorIterator($dir_iterator, RecursiveIteratorIterator::CHILD_FIRST);

            // On supprime chaque dossier et chaque fichier	du dossier cible
            foreach($iterator as $fichier){
                $fichier->isDir() ? rmdir($fichier) : unlink($fichier);
            }

            // On supprime le dossier cible
            rmdir($entryToDelete);
        }
    ?>
<?php } ?>
<?php
function parseDirectory(string $path):array {
    if(!is_dir($path)){
        return false;
    }
    // initialize result with an empty array
    $paths = [];
    // directory list content
    $list = array_diff(scandir($path), ['.', '..']);

    // for each entry
    foreach ($list as $entry) {
        $entryPath = $path.DIRECTORY_SEPARATOR.$entry;
        // if entry is a directory
        if (is_dir($entryPath)) {
            // add directory path to result array
            $paths[] = $entryPath;
            // add the result of parseDirectory(entry) to the result array
            $paths = array_merge($paths, parseDirectory($entryPath));
        }
        // if entry is a file
        if (is_file($entryPath)) {
            // add entry path to the result array
            $paths[] = $entryPath;
        }
    }
    
    return $paths;
}

$dir = "./files";
$result = parseDirectory($dir);
?>
<h2>Liste des documents</h2>
<table>
        <?php foreach($result as $doc) { ?>
        <tr>
            <td><?=$doc?></td>
            <td>
                <form action="index.php" method="POST">
                        <input type="hidden" name="fileToDelete" value="<?= $doc ?>">
                        <button class="btn btn-warning" type="delete">supprimer</button>
                </form> 
            </td>
        </tr>
        <?php } ?>
</table>
<?php if(isset($_POST['content'])) { ?>

    <?php
        $fichier = $_POST['file'];
        $file = fopen($fichier, 'w');
        fwrite($file, $_POST['content']);
        fclose($file);
    ?>
<?php } ?>

<?php if (isset($_GET["f"])) { ?>

    <?php
        $fichier = $_GET['f'];       
        $content = file_get_contents($fichier);
    ?>

        <form action="index.php" method="POST">
            <textarea name="content"><?= $content ?></textarea>
            <input type="hidden" name="file" value="<?= $_GET['f'] ?>">
            <button type="submit">envoyer</button>
        </form>

<?php } else { ?>
    <h3>Documents modifiables</h3>
    <?php

        foreach($result as $file) {
            $fileExtension = new SplFileInfo($file);

            if (in_array($fileExtension->getExtension(), array('html', 'txt'))){
    ?>
                
                <a href="?f=<?= $file ?>"><?= $file ?></a></br>
    <?php
            }
        }
    ?>

<?php } ?>

<?php include('inc/foot.php'); ?>