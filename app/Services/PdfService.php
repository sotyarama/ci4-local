<?php

namespace App\Services;

use Dompdf\Dompdf;
use Dompdf\Options;

class PdfService
{
    protected $options;
    protected $writePath;
    protected $fcPath;
    /**
     * When true, save debug HTML/PDF artifacts to writable/logs
     * @var bool
     */
    protected $saveArtifacts = false;

    public function __construct(array $options = [])
    {
        $this->options = $options;
        // Option: when true, save debug HTML/PDF artifacts to writable/logs
        $this->saveArtifacts = isset($options['saveArtifacts']) ? (bool)$options['saveArtifacts'] : false;
        // Allow caller to inject paths (framework-agnostic). Fall back to CI constants if present.
        $this->writePath = isset($options['writePath']) ? rtrim($options['writePath'], DIRECTORY_SEPARATOR) : (defined('WRITEPATH') ? rtrim(WRITEPATH, DIRECTORY_SEPARATOR) : null);
        $this->fcPath = isset($options['fcPath']) ? rtrim($options['fcPath'], DIRECTORY_SEPARATOR) : (defined('FCPATH') ? rtrim(FCPATH, DIRECTORY_SEPARATOR) : null);
    }

    private function log(string $level, string $message): void
    {
        if (function_exists('log_message')) {
            log_message($level, $message);
            return;
        }
        error_log(sprintf('PdfService.%s: %s', $level, $message));
    }

    /**
     * Render HTML to PDF and save debug artifacts.
     * Returns array with keys: pdf (binary), htmlFile, pdfFile, duration
     */
    public function renderHtml(string $html, string $filename = 'output.pdf', string $paper = 'A4', string $orientation = 'portrait'): array
    {
        $opts = new Options();
        // Use a core/safer default font and enable font subsetting to reduce embedded font size
        $opts->set('defaultFont', 'Helvetica');
        $opts->set('isFontSubsettingEnabled', true);
        $opts->setIsRemoteEnabled(true);
        $opts->set('isHtml5ParserEnabled', true);

        $dompdf = new Dompdf($opts);
        $dompdf->setPaper($paper, $orientation);

        // Temporarily raise memory and execution time limits for large PDF jobs
        $prevMemory = ini_get('memory_limit');
        $prevTime = ini_get('max_execution_time');
        try {
            ini_set('memory_limit', isset($this->options['memory_limit']) ? $this->options['memory_limit'] : '512M');
            set_time_limit(isset($this->options['max_execution_time']) ? (int)$this->options['max_execution_time'] : 120);
        } catch (\Throwable $e) {
            // ignore if ini_set not allowed
        }

        $start = microtime(true);

        // Basic diagnostics: HTML size and counts before processing
        $htmlBytesBefore = strlen($html);
        preg_match_all('/<img\b/i', $html, $mImgsBefore);
        $imgCountBefore = count($mImgsBefore[0] ?? []);
        preg_match_all('/<svg\b/i', $html, $mSvgsBefore);
        $svgCountBefore = count($mSvgsBefore[0] ?? []);

        // Ensure images are inlined or file:/// absolute paths for Dompdf
        $t0 = microtime(true);
        $html = $this->inlineImagesAsDataUris($html);
        $inlineDuration = microtime(true) - $t0;

        // Sanitize CSS that Dompdf struggles with: remove aspect-ratio and overflow:hidden
        $t1 = microtime(true);
        $html = $this->sanitizeCssForPdf($html);
        $sanitizeDuration = microtime(true) - $t1;

        // Diagnostics after processing
        $htmlBytesAfter = strlen($html);
        preg_match_all('/<img\b/i', $html, $mImgsAfter);
        $imgCountAfter = count($mImgsAfter[0] ?? []);
        preg_match_all('/<svg\b/i', $html, $mSvgsAfter);
        $svgCountAfter = count($mSvgsAfter[0] ?? []);

        $this->log('info', sprintf(
            'html size before=%dB after=%dB imgs before=%d after=%d svgs before=%d after=%d inlineSec=%.2f sanitizeSec=%.2f',
            $htmlBytesBefore,
            $htmlBytesAfter,
            $imgCountBefore,
            $imgCountAfter,
            $svgCountBefore,
            $svgCountAfter,
            $inlineDuration,
            $sanitizeDuration
        ));

        // Optionally save the HTML used for rendering for inspection
        $htmlFile = null;
        if (! empty($this->saveArtifacts)) {
            $base = $this->writePath ?: rtrim(sys_get_temp_dir(), DIRECTORY_SEPARATOR);
            $logDir = $base . DIRECTORY_SEPARATOR . 'logs';
            if (! is_dir($logDir)) {
                @mkdir($logDir, 0755, true);
            }
            $htmlFile = $logDir . DIRECTORY_SEPARATOR . 'debug_pdf_' . date('Ymd_His') . '_' . bin2hex(random_bytes(4)) . '.html';
            @file_put_contents($htmlFile, $html);
        }

        $dompdf->loadHtml($html);
        $t2 = microtime(true);
        $dompdf->render();
        $renderDuration = microtime(true) - $t2;

        $pdfOutput = $dompdf->output();
        $duration = microtime(true) - $start;

        $this->log('info', sprintf('dompdf renderSec=%.2f totalSec=%.2f pdfBytes=%d', $renderDuration, $duration, strlen($pdfOutput)));

        $pdfFile = null;
        if (! empty($this->saveArtifacts)) {
            $base = $this->writePath ?: rtrim(sys_get_temp_dir(), DIRECTORY_SEPARATOR);
            $logDir = $base . DIRECTORY_SEPARATOR . 'logs';
            if (! is_dir($logDir)) {
                @mkdir($logDir, 0755, true);
            }
            $pdfFile = $logDir . DIRECTORY_SEPARATOR . 'debug_pdf_' . date('Ymd_His') . '_' . bin2hex(random_bytes(4)) . '.pdf';
            @file_put_contents($pdfFile, $pdfOutput);
        }

        $this->log('info', sprintf('rendered in %.2f sec; html=%s pdf=%s len=%d', $duration, $htmlFile ?? 'none', $pdfFile ?? 'none', strlen($pdfOutput)));

        // Restore previous PHP limits
        try {
            if ($prevMemory !== false) {
                ini_set('memory_limit', $prevMemory);
            }
            if ($prevTime !== false) {
                set_time_limit((int)$prevTime);
            }
        } catch (\Throwable $e) {
            // ignore
        }

        return [
            'pdf' => $pdfOutput,
            'htmlFile' => $htmlFile,
            'pdfFile' => $pdfFile,
            'duration' => $duration,
        ];
    }

    /**
     * Prune old files from the public downloads directory.
     * Returns number of files removed.
     */
    public function prunePublicDownloads(?string $publicDir = null, int $maxAgeHours = 48): int
    {
        $publicDir = $publicDir ?: ($this->fcPath ? $this->fcPath . DIRECTORY_SEPARATOR . 'downloads' . DIRECTORY_SEPARATOR : null);
        if ($publicDir === null) {
            return 0;
        }
        if (! is_dir($publicDir)) {
            return 0;
        }

        $now = time();
        $maxAge = $maxAgeHours * 3600;
        $removed = 0;

        $it = new \DirectoryIterator($publicDir);
        foreach ($it as $file) {
            if (! $file->isFile()) continue;
            $pathname = $file->getPathname();
            // Only consider PDF files
            if (strtolower($file->getExtension()) !== 'pdf') continue;
            $age = $now - $file->getMTime();
            if ($age > $maxAge) {
                @unlink($pathname);
                $removed++;
            }
        }

        if ($removed > 0) {
            $this->log('info', 'pruned ' . $removed . ' files from public downloads older than ' . $maxAgeHours . ' hours');
        }

        return $removed;
    }

    private function inlineImagesAsDataUris(string $html): string
    {
        libxml_use_internal_errors(true);
        $doc = new \DOMDocument();
        $doc->loadHTML('<?xml encoding="utf-8"?>' . $html, LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD);

        // Inline simple SVG <style> rules into element attributes for better Dompdf support.
        try {
            $xpath = new \DOMXPath($doc);
            // Find all <style> elements inside SVGs
            $styleNodes = $doc->getElementsByTagName('style');
            for ($si = $styleNodes->length - 1; $si >= 0; $si--) {
                $styleNode = $styleNodes->item($si);
                $css = $styleNode->textContent;
                if (! $css) {
                    continue;
                }

                // Parse simple CSS rules: selector { prop: val; }
                if (preg_match_all('/([^\{]+)\{([^}]+)\}/sU', $css, $matches)) {
                    foreach ($matches[1] as $idx => $selectorRaw) {
                        $selector = trim($selectorRaw);
                        $decls = trim($matches[2][$idx]);
                        // Build XPath for simple selectors: tag, .class, #id
                        $queries = [];
                        foreach (preg_split('/\s*,\s*/', $selector) as $sel) {
                            $sel = trim($sel);
                            if ($sel === '') continue;
                            if (strpos($sel, '.') === 0) {
                                $cls = substr($sel, 1);
                                $queries[] = "//*[contains(concat(' ', normalize-space(@class), ' '), ' " . $cls . " ')]";
                            } elseif (strpos($sel, '#') === 0) {
                                $id = substr($sel, 1);
                                $queries[] = "//*[@id='" . $id . "']";
                            } elseif (preg_match('/^[a-zA-Z][a-zA-Z0-9_-]*$/', $sel)) {
                                $queries[] = '//' . $sel;
                            } else {
                                // unsupported selector - skip
                            }
                        }

                        if (empty($queries)) continue;

                        // Parse declarations into key => value
                        $declPairs = [];
                        foreach (preg_split('/;\s*/', $decls) as $d) {
                            if (trim($d) === '') continue;
                            $parts = explode(':', $d, 2);
                            if (count($parts) !== 2) continue;
                            $name = trim($parts[0]);
                            $value = trim($parts[1]);
                            $declPairs[$name] = $value;
                        }

                        if (empty($declPairs)) continue;

                        foreach ($queries as $q) {
                            $nodes = @$xpath->query($q);
                            if (! $nodes) continue;
                            foreach ($nodes as $node) {
                                // Only apply to element nodes
                                if (! ($node instanceof \DOMElement)) {
                                    continue;
                                }

                                // Apply declarations as attributes when applicable
                                foreach ($declPairs as $prop => $val) {
                                    // If property is a presentation attribute, set it directly
                                    if (in_array($prop, ['fill', 'stroke', 'opacity', 'stroke-width', 'stroke-linecap', 'stroke-linejoin'])) {
                                        $node->setAttribute($prop, $val);
                                        continue;
                                    }

                                    // Otherwise append to style attribute
                                    $existing = $node->getAttribute('style');
                                    if ($existing && substr(trim($existing), -1) !== ';') {
                                        $existing .= ';';
                                    }
                                    $existing .= $prop . ':' . $val . ';';
                                    $node->setAttribute('style', $existing);
                                }
                            }
                        }
                    }
                }

                // Remove the style node after inlining
                $styleNode->parentNode->removeChild($styleNode);
            }
        } catch (\Throwable $e) {
            // non-fatal - continue without SVG style inlining
        }

        $imgs = $doc->getElementsByTagName('img');
        $inlined = 0;

        for ($i = $imgs->length - 1; $i >= 0; $i--) {
            $img = $imgs->item($i);
            $src = $img->getAttribute('src');
            if (! $src) {
                continue;
            }

            $lower = strtolower($src);
            if (strpos($lower, 'data:') === 0 || strpos($lower, 'file:') === 0) {
                continue;
            }

            $resolved = '';

            // Try resolve as local path relative to front-controller path (FCPATH) if available
            $fcBase = $this->fcPath ?: (defined('FCPATH') ? rtrim(FCPATH, DIRECTORY_SEPARATOR) : null);
            if ($fcBase) {
                $cand = realpath($fcBase . DIRECTORY_SEPARATOR . ltrim($src, '/'));
                if ($cand !== false && is_file($cand)) {
                    $resolved = $cand;
                }
            }

            if ($resolved === '') {
                $pos = strpos($src, '/images/');
                if ($pos !== false && isset($fcBase) && $fcBase) {
                    $rel = substr($src, $pos + strlen('/images/'));
                    $cand2 = realpath($fcBase . DIRECTORY_SEPARATOR . 'images' . DIRECTORY_SEPARATOR . ltrim($rel, '/'));
                    if ($cand2 !== false && is_file($cand2)) {
                        $resolved = $cand2;
                    }
                }
            }

            $data = null;
            // Try to optimize large raster images (GD must be available)
            $optPath = null;
            if ($resolved !== '') {
                $optPath = $this->optimizeRasterImage($resolved);
                $readPath = $optPath ?: $resolved;
                $data = @file_get_contents($readPath);
                $filesize = $data === false ? 0 : strlen($data);
            } elseif (preg_match('#^https?://#i', $src) && ini_get('allow_url_fopen')) {
                $data = @file_get_contents($src);
                $filesize = $data === false ? 0 : strlen($data);
            } else {
                $filesize = 0;
            }

            // Inline images up to 1 MB to avoid embedding very large binaries
            $inlineLimit = 1 * 1024 * 1024; // 1 MB

            // If we have image data and it's SVG, attempt to inline the SVG markup
            // regardless of size: Dompdf generally handles inline SVG markup better
            // than file:// or data-uri fallbacks for vector fidelity.
            $isSvg = false;
            $ext = strtolower(pathinfo($resolved ?: $src, PATHINFO_EXTENSION));
            if (($resolved !== '' || ($data !== null && $data !== false)) && ($ext === 'svg' || stripos((string)$data, '<svg') !== false)) {
                $isSvg = true;
            }

            if ($isSvg && $data !== null && $data !== false && $filesize > 0) {
                // Sanitize: remove XML prolog if present
                $svg = preg_replace('#<\?xml.*?\?>#is', '', $data);
                $svg = preg_replace('#<!DOCTYPE.*?>#is', '', $svg);
                $svg = trim($svg);

                // Try to replace the <img> node with actual SVG markup so Dompdf renders vectors correctly
                try {
                    $fragment = $doc->createDocumentFragment();
                    if ($fragment->appendXML($svg)) {
                        $img->parentNode->replaceChild($fragment, $img);
                        $inlined++;
                        $this->log('info', 'inlined SVG as markup (' . ($resolved ?: $src) . ')');
                        continue; // next image
                    }
                } catch (\Throwable $e) {
                    // fall back to other approaches below
                }
            }

            if ($data !== null && $data !== false && $filesize > 0 && $filesize <= $inlineLimit) {
                // Non-SVG inlining path
                $finfoMime = @mime_content_type($resolved ?: $src);
                if ($finfoMime === false || $finfoMime === null) {
                    $finfoMime = 'application/octet-stream';
                }
                $dataUri = 'data:' . $finfoMime . ';base64,' . base64_encode($data);
                $img->setAttribute('src', $dataUri);
                $inlined++;
            } elseif ($resolved !== '') {
                // If file exists but was too large to inline, set file:/// path
                $normalized = str_replace('\\', '/', $resolved);
                $img->setAttribute('src', 'file:///' . $normalized);
                if ($filesize > $inlineLimit) {
                    $this->log('info', 'image too large to inline, left as file:/// - ' . $normalized . ' (bytes=' . $filesize . ')');
                }
            }
        }

        if ($inlined > 0) {
            $this->log('info', 'inlined ' . $inlined . ' images as data URIs');
        }

        // Ensure inline SVG elements have explicit width/height attributes for Dompdf.
        $svgs = $doc->getElementsByTagName('svg');
        $svgAdjusted = 0;
        for ($i = $svgs->length - 1; $i >= 0; $i--) {
            $svg = $svgs->item($i);
            if (! ($svg instanceof \DOMElement)) {
                continue;
            }
            $hasWidth = $svg->hasAttribute('width');
            $hasHeight = $svg->hasAttribute('height');
            if ($hasWidth && $hasHeight) {
                continue;
            }

            $viewBox = $svg->getAttribute('viewBox') ?: $svg->getAttribute('viewbox');
            $w = null;
            $h = null;
            if ($viewBox) {
                // viewBox: minX minY width height
                $parts = preg_split('/[\s,]+/', trim($viewBox));
                if (count($parts) === 4) {
                    $w = (int)ceil((float)$parts[2]);
                    $h = (int)ceil((float)$parts[3]);
                }
            }

            if ($w === null || $h === null) {
                // fallback sensible defaults
                $w = 400;
                $h = 300;
            }

            if (! $hasWidth) {
                $svg->setAttribute('width', (string)$w);
            }
            if (! $hasHeight) {
                $svg->setAttribute('height', (string)$h);
            }
            $svgAdjusted++;
        }

        if ($svgAdjusted > 0) {
            $this->log('info', 'adjusted ' . $svgAdjusted . ' inline SVGs with width/height attributes');
        }

        // Keep inline <svg> markup rather than converting it to <img> data URIs.
        // Converting inline SVGs to image data URIs can strip presentation
        // or styling and negatively affect transparency; Dompdf handles
        // inline SVG markup more reliably for our cases.

        $out = $doc->saveHTML();
        libxml_clear_errors();
        return $out;
    }

    /**
     * Remove or rewrite CSS rules that commonly break layout in Dompdf.
     */
    private function sanitizeCssForPdf(string $html): string
    {
        // Remove aspect-ratio declarations
        $html = preg_replace('#aspect-ratio\s*:[^;"}]+;#i', '', $html);

        // Remove overflow: hidden which can clip elements in print layout
        $html = preg_replace('#overflow\s*:\s*hidden\s*;#i', '', $html);

        // Remove unsupported CSS functions like aspect-ratio usages in shorthand
        $html = preg_replace('#aspect-ratio\s*:\s*[^;]+;#i', '', $html);

        return $html;
    }

    /**
     * Optimize raster images (JPEG/PNG) for PDF embedding.
     * Returns path to optimized file, or original path on failure / unsupported image.
     */
    private function optimizeRasterImage(string $path): ?string
    {
        if (! is_file($path) || ! function_exists('imagecreatefromstring')) {
            return null;
        }

        $info = @getimagesize($path);
        if (! $info) return null;

        $mime = $info['mime'] ?? '';
        if (stripos($mime, 'image/') !== 0) return null;

        // Only optimize raster images (not SVG)
        if (stripos($mime, 'svg') !== false) return null;

        $maxWidth = 1200;
        $quality = 75;

        $hash = sha1($path . '|' . filemtime($path));
        $cacheDir = $this->getPdfImageCacheDir();
        // We'll decide extension based on whether the image has alpha/transparency
        $outJpg = rtrim($cacheDir, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR . $hash . '.jpg';
        $outPng = rtrim($cacheDir, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR . $hash . '.png';
        if (is_file($outJpg)) {
            return $outJpg;
        }
        if (is_file($outPng)) {
            return $outPng;
        }

        try {
            $contents = @file_get_contents($path);
            if ($contents === false) return null;
            $src = @imagecreatefromstring($contents);
            if (! $src) return null;
            $w = imagesx($src);
            $h = imagesy($src);
            // Detect whether the source image has alpha (transparency) - only relevant for PNG-like sources
            $hasAlpha = false;
            if (stripos($mime, 'png') !== false) {
                // Improved sampling to detect alpha channel without scanning every pixel.
                $maxSamples = 3000;
                $grid = (int)ceil(sqrt($maxSamples));
                $stepX = max(1, (int)floor($w / $grid));
                $stepY = max(1, (int)floor($h / $grid));
                for ($yy = 0; $yy < $h && ! $hasAlpha; $yy += $stepY) {
                    for ($xx = 0; $xx < $w; $xx += $stepX) {
                        $rgba = @imagecolorat($src, $xx, $yy);
                        if ($rgba === false) continue;
                        // Extract 7-bit alpha from GD color int: shift then mask
                        $alpha = ($rgba >> 24) & 0x7F;
                        if ($alpha > 0) {
                            $hasAlpha = true;
                            break;
                        }
                    }
                }
            }

            // Determine target dimensions
            if ($w <= $maxWidth) {
                $newW = $w;
                $newH = $h;
            } else {
                $newW = $maxWidth;
                $newH = (int)floor($h * ($newW / $w));
            }

            // Create destination image depending on whether we need alpha
            if ($hasAlpha) {
                $dst = imagecreatetruecolor($newW, $newH);
                // Preserve alpha channel
                imagealphablending($dst, false);
                imagesavealpha($dst, true);
                $transparent = imagecolorallocatealpha($dst, 0, 0, 0, 127);
                imagefilledrectangle($dst, 0, 0, $newW, $newH, $transparent);
                imagecopyresampled($dst, $src, 0, 0, 0, 0, $newW, $newH, $w, $h);

                // Save as PNG to preserve transparency
                // compression: 0 (no) - 9 (max). Use 6 as balanced default.
                @imagepng($dst, $outPng, 6);
                imagedestroy($src);
                imagedestroy($dst);

                $this->log('info', 'created optimized PNG (preserving transparency) ' . $outPng . ' (cacheDir=' . $cacheDir . ')');
                return $outPng;
            } else {
                // No alpha: generate an RGB JPEG
                $dst = imagecreatetruecolor($newW, $newH);
                $bg = imagecolorallocate($dst, 255, 255, 255);
                imagefill($dst, 0, 0, $bg);
                imagecopyresampled($dst, $src, 0, 0, 0, 0, $newW, $newH, $w, $h);

                @imagejpeg($dst, $outJpg, $quality);
                imagedestroy($src);
                imagedestroy($dst);

                $this->log('info', 'created optimized JPEG ' . $outJpg . ' (cacheDir=' . $cacheDir . ')');
                return $outJpg;
            }
        } catch (\Throwable $e) {
            return null;
        }
    }

    /**
     * Determine a writable cache directory for PDF-optimized images.
     * Priority: options['cacheDir'] -> WRITEPATH/cache/pdf_images -> sys_get_temp_dir()/ci4_pdf_cache
     */
    private function getPdfImageCacheDir(): string
    {
        // 1) explicit option
        if (! empty($this->options['cacheDir'])) {
            $dir = $this->options['cacheDir'];
        } else {
            if ($this->writePath) {
                $dir = rtrim($this->writePath, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR . 'cache' . DIRECTORY_SEPARATOR . 'pdf_images' . DIRECTORY_SEPARATOR;
            } else {
                $dir = (defined('WRITEPATH') ? WRITEPATH : rtrim(sys_get_temp_dir(), DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR) . 'cache' . DIRECTORY_SEPARATOR . 'pdf_images' . DIRECTORY_SEPARATOR;
            }
        }

        // Normalize
        $dir = str_replace(['\\', '/'], DIRECTORY_SEPARATOR, $dir);

        // Try to ensure directory exists and is writable
        if (! is_dir($dir)) {
            try {
                @mkdir($dir, 0755, true);
            } catch (\Throwable $e) {
                // ignore
            }
        }

        if (! is_dir($dir) || ! is_writable($dir)) {
            // Fallback to system temp
            $tmp = rtrim(sys_get_temp_dir(), DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR . 'ci4_pdf_cache' . DIRECTORY_SEPARATOR;
            if (! is_dir($tmp)) {
                @mkdir($tmp, 0755, true);
            }
            if (is_dir($tmp) && is_writable($tmp)) {
                $dir = $tmp;
            }
        }

        return $dir;
    }
}
