<?php
$files = new RecursiveIteratorIterator(new RecursiveDirectoryIterator('resources/views'));
foreach($files as $file) {
    if (!$file->isFile() || $file->getExtension() !== 'php') {
        continue;
    }
    $path = $file->getRealPath();
    $content = file_get_contents($path);
    $original = $content;
    
    // Only touch files that contain "modal-dialog"
    if (strpos($content, 'modal-dialog') !== false) {
        $content = preg_replace_callback('/(<div[^>]*class=")([^"]*modal-dialog[^"]*)("[^>]*>)/', function($m) {
            $classes = explode(' ', $m[2]);
            $classes = array_map('trim', $classes);
            // Remove specific existing classes to avoid duplicates
            $classes = array_filter($classes, function($c) {
                // If there are existing margin classes or centered class, strip them out first
                if (preg_match('/^mx-\d+$|^m-\d+$/', $c)) return false;
                if ($c === 'modal-dialog-centered' || $c === 'mx-sm-auto') return false;
                if ($c === '') return false;
                return true;
            });
            // Add the desired classes
            $classes[] = 'modal-dialog-centered';
            $classes[] = 'mx-3';
            $classes[] = 'mx-sm-auto';
            return $m[1] . implode(' ', array_unique($classes)) . $m[3];
        }, $content);
        
        if ($original !== $content) {
            file_put_contents($path, $content);
            echo "Updated: " . $path . "\n";
        }
    }
}
echo "Modals successfully updated!\n";
