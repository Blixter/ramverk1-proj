<?php
namespace Blixter\Filter;

/**
 * Extract frontmatter from text and pass text through a set of filter to
 * format and extract information from the text.
 */
class MdFilter
{

    /**
     * Format text according to Markdown syntax.
     *
     * @param string $text the text that should be formatted.
     *
     * @return string as the formatted html-text.
     * @SuppressWarnings(PHPMD.StaticAccess)
     */
    public function markdown($text)
    {
        $text = \Michelf\MarkdownExtra::defaultTransform($text);
        // Remove comments
        $text = preg_replace('/<!--(.|\s)*?-->/', '', $text);
        $text = \Michelf\SmartyPantsTypographer::defaultTransform(
            $text,
            "2"
        );
        return $text;
    }
}
