<?php
/**
 * BATCH 8B: hero slideshow can also use photos from assets/images/hero/
 * Gallery photos still come first. The folder is used only when the gallery is empty.
 */
$path = 'index.php';
if (!is_file($path)) {
    exit("Run this from the project root.\n");
}
$index = file_get_contents($path);

if (strpos($index, 'hero-slides') === false) {
    exit("Run build_batch8.php first.\n");
}
if (strpos($index, "\$slide['src']") !== false) {
    exit("Already patched. Nothing to do.\n");
}

$add = <<<'FILE_END'


// Give every slide an image address
foreach ($heroSlides as &$slide) {
    $slide['src'] = UPLOAD_URL . $slide['image'];
}
unset($slide);

// No gallery photos yet: use the placeholder photos in assets/images/hero/
if (!$heroSlides) {
    $files = glob(__DIR__ . '/assets/images/hero/*.{jpg,jpeg,png,webp,JPG,JPEG,PNG,WEBP}', GLOB_BRACE) ?: [];
    sort($files);
    foreach (array_slice($files, 0, 6) as $file) {
        $heroSlides[] = ['title' => '', 'src' => url('assets/images/hero/' . rawurlencode(basename($file)))];
    }
}
FILE_END;

$count = 0;
$index = preg_replace('/(\$heroSlides\s*=.*?->fetchAll\(\);)/s', '$1' . $add, $index, 1, $count);
if ($count !== 1) {
    exit("Could not find the slideshow query in index.php.\n");
}

$index = str_replace("e(UPLOAD_URL . \$slide['image'])", "e(\$slide['src'])", $index);

file_put_contents($path, $index);
echo "Patched: index.php\n";
echo "Put photos in assets/images/hero/ and press Ctrl + F5 on the home page.\n";