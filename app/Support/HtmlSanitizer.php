<?php

namespace App\Support;

final class HtmlSanitizer
{
    /**
     * Allow only safe inline HTML tags commonly used in hero headlines.
     * Strips scripts, event handlers, forms, iframes, and other dangerous elements.
     */
    public static function clean(string $html): string
    {
        $allowed = ['span', 'strong', 'b', 'em', 'i', 'br', 'small', 'sub', 'sup'];

        $doc = new \DOMDocument;
        @$doc->loadHTML(
            '<!DOCTYPE html><html><head><meta charset="utf-8"></head><body>'.$html.'</body></html>',
            LIBXML_HTML_NODEFDTD | LIBXML_NOERROR
        );

        $body = $doc->getElementsByTagName('body')->item(0);
        if (! $body) {
            return htmlspecialchars($html, ENT_QUOTES, 'UTF-8');
        }

        self::sanitizeNode($body, $allowed);

        $output = '';
        foreach ($body->childNodes as $child) {
            $output .= $doc->saveHTML($child);
        }

        return trim($output);
    }

    private static function sanitizeNode(\DOMNode $node, array $allowed): void
    {
        $remove = [];

        foreach ($node->childNodes as $child) {
            if (! $child instanceof \DOMElement) {
                continue;
            }

            $tag = strtolower($child->tagName);

            if (! in_array($tag, $allowed)) {
                $remove[] = $child;

                continue;
            }

            $attrs = [];
            foreach ($child->attributes as $attr) {
                $attrs[] = $attr;
            }
            foreach ($attrs as $attr) {
                $name = strtolower($attr->name);
                if (str_starts_with($name, 'on')) {
                    $child->removeAttribute($attr->name);
                }
                if (in_array($name, ['href', 'src', 'action', 'formaction']) &&
                    preg_match('/^\s*javascript\s*:/i', $attr->value)) {
                    $child->removeAttribute($attr->name);
                }
            }

            self::sanitizeNode($child, $allowed);
        }

        foreach ($remove as $el) {
            $fragment = $el->ownerDocument->createDocumentFragment();
            while ($el->firstChild) {
                $fragment->appendChild($el->firstChild);
            }
            $el->parentNode->replaceChild($fragment, $el);
        }
    }
}
