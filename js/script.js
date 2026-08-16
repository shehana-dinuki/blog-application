function wrapSelection(textareaId, before, after) {
  const textarea = document.getElementById(textareaId);
  const start = textarea.selectionStart;
  const end = textarea.selectionEnd;
  const selectedText = textarea.value.substring(start, end);
  const replacement = before + selectedText + after;

  textarea.setRangeText(replacement, start, end, 'select');
  textarea.focus();
}