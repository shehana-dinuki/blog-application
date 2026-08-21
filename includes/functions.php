<?php
/**
 * Converts user-entered Markdown-style text into safe HTML.
 * Secures the content first, then applies supported formatting.
 * Processes each paragraph separately to prevent formatting errors
 * from affecting other paragraphs.
 */
function formatBlogContent($text) {
    // Split into paragraphs on blank lines
    $paragraphs = preg_split('/\n\s*\n/', trim($text));
    $html = '';

    foreach ($paragraphs as $para) {
        $safe = htmlspecialchars(trim($para));

        // Heading: ## Heading (must be the whole paragraph)
        
        if (preg_match('/^##\s+(.+)$/', $safe, $matches)) {
            $html .= '<h3>' . $matches[1] . '</h3>';
            continue;
        }

        // Bold: **text**
        $safe = preg_replace('/\*\*(.+?)\*\*/s', '<strong>$1</strong>', $safe);
        // Italic: *text*
        $safe = preg_replace('/\*(.+?)\*/s', '<em>$1</em>', $safe);
        // Inline code: `text`
        $safe = preg_replace('/`(.+?)`/s', '<code>$1</code>', $safe);
        // Line breaks within a paragraph
        $safe = nl2br($safe);

        $html .= '<p style="margin-bottom: 18px;">' . $safe . '</p>';
    }

    return $html;
}