<?php
declare(strict_types=1);

namespace ExpertsAcademy\Studio;

use DOMDocument;
use DOMElement;
use DOMNode;
use DOMXPath;

/**
 * Allowlist HTML sanitizer for rich-text properties.
 *
 * Input is parsed in a fragment context, then re-serialized from an allowlisted
 * DOM. Unknown elements are unwrapped; scripts, styles, frames, event handlers
 * and javascript:/data: URLs are removed. Output is safe to embed as HTML.
 */
final class Sanitizer
{
    private const ALLOWED = [
        'p' => [], 'br' => [], 'strong' => [], 'b' => [], 'em' => [], 'i' => [],
        'ul' => [], 'ol' => [], 'li' => [], 'blockquote' => [],
        'h2' => [], 'h3' => [], 'h4' => [],
        'a' => ['href', 'title', 'rel'],
        'code' => [], 'pre' => [], 'hr' => [],
        'table' => [], 'thead' => [], 'tbody' => [], 'tr' => [], 'th' => [], 'td' => [],
    ];

    private const DROP_WITH_CONTENT = ['script','style','iframe','object','embed','form','input','button','textarea','select','link','meta','svg','math','video','audio','source','template'];

    private const MAX_LENGTH = 20000;

    public static function richText(string $html): string
    {
        $html = mb_substr(trim($html), 0, self::MAX_LENGTH);
        if ($html === '') {
            return '';
        }
        if (!class_exists(DOMDocument::class)) {
            return '<p>' . Html::e(strip_tags($html)) . '</p>';
        }

        $previous = libxml_use_internal_errors(true);
        $document = new DOMDocument('1.0', 'UTF-8');
        $wrapped = '<div>' . $html . '</div>';
        $document->loadHTML('<?xml encoding="UTF-8">' . $wrapped, LIBXML_HTML_NOIMPLIED | LIBXML_NONET);
        libxml_clear_errors();
        libxml_use_internal_errors($previous);

        $root = $document->getElementsByTagName('div')->item(0);
        if (!$root instanceof DOMElement) {
            return '';
        }
        self::clean($root);
        $output = '';
        foreach ($root->childNodes as $child) {
            $output .= $document->saveHTML($child);
        }
        return trim($output);
    }

    private static function clean(DOMNode $node): void
    {
        for ($child = $node->firstChild; $child !== null;) {
            $next = $child->nextSibling;
            if ($child instanceof DOMElement) {
                $name = strtolower($child->nodeName);
                if (in_array($name, self::DROP_WITH_CONTENT, true)) {
                    $node->removeChild($child);
                    $child = $next;
                    continue;
                }
                if (!array_key_exists($name, self::ALLOWED)) {
                    self::clean($child);
                    while ($child->firstChild !== null) {
                        $node->insertBefore($child->firstChild, $child);
                    }
                    $node->removeChild($child);
                    $child = $next;
                    continue;
                }
                $allowedAttributes = self::ALLOWED[$name];
                for ($i = $child->attributes->length - 1; $i >= 0; $i--) {
                    $attribute = $child->attributes->item($i);
                    if ($attribute === null) {
                        continue;
                    }
                    $attributeName = strtolower($attribute->nodeName);
                    if (!in_array($attributeName, $allowedAttributes, true) || str_starts_with($attributeName, 'on')) {
                        $child->removeAttribute($attribute->nodeName);
                    }
                }
                if ($name === 'a') {
                    $href = trim($child->getAttribute('href'));
                    if (!self::isSafeUrl($href)) {
                        $child->removeAttribute('href');
                    } else {
                        $child->setAttribute('href', $href);
                    }
                    if ($child->getAttribute('target') === '_blank' || $child->hasAttribute('href')) {
                        $child->setAttribute('rel', 'noopener noreferrer');
                    }
                }
                self::clean($child);
            } elseif ($child->nodeType === XML_TEXT_NODE) {
                // Text is kept as text; serialization escapes it.
            } elseif ($child->nodeType === XML_COMMENT_NODE || $child->nodeType === XML_PI_NODE) {
                $node->removeChild($child);
            }
            $child = $next;
        }
    }

    public static function isSafeUrl(string $url): bool
    {
        if ($url === '') {
            return false;
        }
        if (preg_match('/\A\/(?!\/)[A-Za-z0-9._~!$&\'()*+,;=:@%\/?#-]*\z/D', $url) === 1) {
            return true;
        }
        if (preg_match('/\A#[A-Za-z0-9_-]{1,80}\z/D', $url) === 1) {
            return true;
        }
        if (preg_match('/\Ahttps?:\/\/[^\s"\'<>]{1,2000}\z/iD', $url) === 1) {
            return true;
        }
        return preg_match('/\A(mailto|tel):[^\s"\'<>]{1,200}\z/iD', $url) === 1;
    }

    /** Plain multi-line text: no markup at all. */
    public static function plainText(string $text): string
    {
        return Html::e(mb_substr(trim($text), 0, 8000));
    }
}
